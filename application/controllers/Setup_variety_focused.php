<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_variety_focused extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->locations = User_helper::get_locations();
        if (!($this->locations)) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->load->helper('bi_helper');
        $this->language_labels();
    }

    private function language_labels()
    {
        // Label
        $this->lang->language['LABEL_FOCUSED_VARIETY'] = 'Focusable Varieties';
        $this->lang->language['LABEL_VARIETY_FOCUSED_COUNT'] = 'Total Focusable';
        // Messages
        $this->lang->language['MSG_ID_NOT_EXIST'] = 'ID Not Exist.';
        $this->lang->language['MSG_INVALID_TRY'] = 'Invalid Try.';
        $this->lang->language['MSG_LOCATION_ERROR'] = 'Trying to Access Focused Variety of Other Location';
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list") {
            $this->system_list();
        } elseif ($action == "get_items") {
            $this->system_get_items();
        } elseif ($action == "add") {
            $this->system_add();
        } elseif ($action == "edit") {
            $this->system_edit($id);
        } elseif ($action == "save") {
            $this->system_save();
        } elseif ($action == "details") {
            $this->system_details($id);
        } elseif ($action == "get_variety_info") {
            $this->system_get_variety_info();
        } elseif ($action == "set_preference") {
            $this->system_set_preference('list');
        } elseif ($action == "save_preference") {
            System_helper::save_preference();
        } else {
            $this->system_list();
        }
    }

    private function get_preference_headers($method = 'list')
    {
        $data = array();
        $data['id'] = 1;
        $data['outlet_name'] = 1;
        $data['district_name'] = 1;
        $data['territory_name'] = 1;
        $data['zone_name'] = 1;
        $data['division_name'] = 1;
        $data['variety_focused_count'] = 1;
        $data['status'] = 1;
        return $data;
    }

    private function system_set_preference($method = 'list')
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1)) {
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_' . $method);
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            $user = User_helper::get_user();
            $method = 'list';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = $this->lang->line('LABEL_OUTLET_NAME') . " Wise Focused Varieties List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $this->common_query(); // Call Common part of below Query Stack

        $items = $this->db->get()->result_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) {
            $data = array();
            $data['item'] = Array(
                'id' => 0,
                'division_id' => 0,
                'zone_id' => 0,
                'territory_id' => 0,
                'district_id' => 0,
                'outlet_id' => 0,
                'crop_id' => 0,
                'variety_focused' => '',
                'status' => ''
            );

            $data['title'] = "Add Focused Variety ( " . ($this->lang->line('LABEL_OUTLET_NAME')) . " Wise )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/add');
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            } else {
                $item_id = $this->input->post('id');
            }

            $this->common_query(); // Call Common part of below Query Stack

            // Additional Conditions -STARTS
            $this->db->where('vf.id', $item_id);
            // Additional Conditions -ENDS

            $data['item'] = $this->db->get()->row_array();
            $this->db->flush_cache(); // Flush/Clear current Query Stack
            if (!$data['item']) {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item'])) {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_LOCATION_ERROR'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
                $this->json_return($ajax);
            }

            $data['title'] = "Edit Focused Variety ( ID:" . $data['item']['id'] . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $item_id);
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $item_id = $this->input->post('id');
        $item = $this->input->post('item');
        $variety = $this->input->post('variety');
        $item['variety_focused'] = json_encode($variety);
        $item['variety_focused_count'] = sizeof($variety);

        $user = User_helper::get_user();
        $time = time();
        if ($item_id > 0) //EDIT
        { //Permission Checking
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->common_query(); // Call Common part of below Query Stack

            // Additional Conditions -STARTS
            $this->db->where('vf.id', $item_id);
            // Additional Conditions -ENDS

            $result = $this->db->get()->row_array();
            $this->db->flush_cache(); // Flush/Clear current Query Stack
            if (!$result) {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($result)) {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_LOCATION_ERROR'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
                $this->json_return($ajax);
            }

            // Sort the array elements
            $arr1 = json_decode($result['variety_focused'], TRUE);
            sort($arr1);
            $arr2 = $variety;
            sort($arr2);
            if ($arr1 == $arr2) { // If no changes made in EDIT mode.
                $ajax['status'] = false;
                $ajax['system_message'] = 'Please select or, unselect atleast 1 variety';
                $this->json_return($ajax);
            }
        }
        else //ADD
        {
            //Permission Checking
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_bi_setup_variety_focused'));
            $this->db->select('*');
            $this->db->where('outlet_id', $item['outlet_id']);
            $this->db->where('revision', 1);
            $this->db->where('status', $this->config->item('system_status_active'));
            $result = $this->db->get()->row_array();
            if ($result) {
                $ajax['status'] = false;
                $ajax['system_message'] = 'A setup for same Outlet Already Exist.';
                $this->json_return($ajax);
            }
        }
        //Validation Checking
        if (!$this->check_validation()) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        if ($item_id > 0) // Revision Update if EDIT
        {
            //Update
            $update_where = array(
                'outlet_id =' . $item['outlet_id']
            );
            $update_item = array();
            $update_item['date_updated'] = $time;
            $update_item['user_updated'] = $user->user_id;

            $this->db->set('revision', 'revision+1', FALSE);
            Query_helper::update($this->config->item('table_bi_setup_variety_focused'), $update_item, $update_where, FALSE);
        }
        //Insert
        $item['status'] = $this->config->item('system_status_active');
        $item['revision'] = 1;
        $item['date_created'] = $time;
        $item['user_created'] = $user->user_id;
        Query_helper::add($this->config->item('table_bi_setup_variety_focused'), $item, FALSE);
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE) {
            $ajax['status'] = true;
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function system_details($id)
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            } else {
                $item_id = $this->input->post('id');
            }

            $this->common_query(); // Call Common part of below Query Stack

            // Additional Conditions -STARTS
            $this->db->where('vf.id', $item_id);
            // Additional Conditions -ENDS

            $result = $this->db->get()->row_array();
            $this->db->flush_cache(); // Flush/Clear current Query Stack

            if (!$result) {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
                $this->json_return($ajax);
            }

            $focused_varieties = json_decode($result['variety_focused'], TRUE);
            $focused_variety_names = array();
            $varieties = Bi_helper::get_all_varieties();
            $i = 0;
            foreach ($varieties as $variety) {
                if (in_array($variety['variety_id'], $focused_varieties)) {
                    $focused_variety_names[] = (++$i) . '. ' . $variety['variety_name'] . ' <i style="font-size:0.85em">(' . $variety['crop_type_name'] . ', ' . $variety['crop_name'] . ')</i>';
                }
            }

            //--------- System User Info ------------
            $user_ids = array();
            $user_ids[$result['user_created']] = $result['user_created'];
            if ($result['user_updated'] > 0) {
                $user_ids[$result['user_updated']] = $result['user_updated'];
            }
            $user_info = System_helper::get_users_info($user_ids);

            //---------------- Basic Info ----------------
            $data = array();
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_DIVISION_NAME'),
                'value_1' => $result['division_name'],
                'label_2' => $this->lang->line('LABEL_ZONE_NAME'),
                'value_2' => $result['zone_name']
            );
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_TERRITORY_NAME'),
                'value_1' => $result['territory_name'],
                'label_2' => $this->lang->line('LABEL_DISTRICT_NAME'),
                'value_2' => $result['district_name'],
            );
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_OUTLET_NAME'),
                'value_1' => $result['outlet_name']
            );
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_FOCUSED_VARIETY'),
                'value_1' => implode(',<br/>', $focused_variety_names)
            );
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_CREATED_BY'),
                'value_1' => $user_info[$result['user_created']]['name'] . ' ( ' . $user_info[$result['user_created']]['employee_id'] . ' )',
                'label_2' => $this->lang->line('LABEL_DATE_CREATED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_created'])
            );

            $data['title'] = "Focused Variety Details ( ID:" . $item_id . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details/' . $item_id);
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_variety_info()
    {
        $post = $this->input->post();
        $data = array();

        $data['compared_varieties'] = array();
        if ($post['id'] > 0) {
            $result = Query_helper::get_info($this->config->item('table_bi_setup_variety_focused'), array('variety_focused'), array('id =' . $post['id']), 1);
            $data['compared_varieties'] = json_decode($result['variety_focused'], TRUE);
        }

        $results = Bi_helper::get_all_varieties('', 0, 0, $post['crop_id']);

        $data['items'] = array();
        foreach ($results as $result) {
            $data['items'][$result['crop_id']]['crop_name'] = $result['crop_name'];
            $data['items'][$result['crop_id']]['varieties'][$result['variety_id']] = '<b>' . $result['variety_name'] . '</b> <i style="font-size:0.85em" title="Crop Type">(' . $result['crop_type_name'] . ')</i>';
        }

        if ($data['items']) {
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => '#variety_list', "html" => $this->load->view($this->controller_url . "/get_variety_info", $data, true));
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = 'No data found.';
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $variety = $this->input->post('variety');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[outlet_id]', $this->lang->line('LABEL_OUTLET_NAME'), 'required|trim|is_natural_no_zero');
        if ($this->form_validation->run() == FALSE) {
            $this->message
                = validation_errors();
            return false;
        }
        if (!isset($variety)) {
            $this->message = 'At least one ' . $this->lang->line('LABEL_ARM_VARIETY') . ' need to save.';
            return false;
        }
        return true;
    }

    private function check_my_editable($item)
    {
        if (($this->locations['division_id'] > 0) && ($this->locations['division_id'] != $item['division_id'])) {
            return false;
        }
        if (($this->locations['zone_id'] > 0) && ($this->locations['zone_id'] != $item['zone_id'])) {
            return false;
        }
        if (($this->locations['territory_id'] > 0) && ($this->locations['territory_id'] != $item['territory_id'])) {
            return false;
        }
        if (($this->locations['district_id'] > 0) && ($this->locations['district_id'] != $item['district_id'])) {
            return false;
        }
        return true;
    }

    private function common_query()
    {
        $this->db->start_cache();

        $this->db->from($this->config->item('table_bi_setup_variety_focused') . ' vf');
        $this->db->select('vf.*');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = vf.outlet_id', 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id');
        $this->db->select('district.id district_id, district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.id territory_id, territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.id zone_id, zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.id division_id, division.name division_name');
        if ($this->locations['division_id'] > 0) {
            $this->db->where('division.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0) {
                $this->db->where('zone.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0) {
                    $this->db->where('territory.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0) {
                        $this->db->where('district.id', $this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('vf.status', $this->config->item('system_status_active'));
        $this->db->where('vf.revision', 1);
        $this->db->order_by('vf.id', 'DESC');

        $this->db->stop_cache();
    }
}
