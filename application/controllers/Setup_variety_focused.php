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
        $this->lang->language['LABEL_SEASON_NAME'] = 'Season';
        $this->lang->language['LABEL_DATE_SALES'] = 'Sales Date';
        $this->lang->language['LABEL_VARIETY_FOCUSED_COUNT'] = 'Total Focusable';
        $this->lang->language['LABEL_FOCUSED_VARIETY'] = 'Focusable Varieties';
        // Messages
        $this->lang->language['MSG_ID_NOT_EXIST'] = 'ID Not Exist.';
        $this->lang->language['MSG_INVALID_TRY'] = 'Invalid Try.';
        $this->lang->language['MSG_LOCATION_ERROR'] = 'Trying to Access Focused Variety of Other Location';
        $this->lang->language['MSG_SEASON_ERROR'] = 'No season is Set. Setup season first.';
        $this->lang->language['MSG_VARIETY_ERROR'] = 'No focused variety is Set.';
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list") {
            $this->system_list();
        } elseif ($action == "get_items") {
            $this->system_get_items();
        } elseif ($action == "edit") {
            $this->system_edit($id);
        } elseif ($action == "save") {
            $this->system_save();
        } elseif ($action == "details") {
            $this->system_details($id);
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
        $data['variety_focused_count'] = 1;
        $data['focused_variety'] = 1;
        $data['outlet_name'] = 1;
        $data['district_name'] = 1;
        $data['territory_name'] = 1;
        $data['zone_name'] = 1;
        $data['division_name'] = 1;
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
            $data['title'] = ($this->lang->line('LABEL_OUTLET_NAME'))."-wise Focusable variety List";
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

        /*$this->db->join($this->config->item('table_bi_setup_variety_focused_details') . ' details', "details.focus_id = vf.id AND details.revision=1", 'INNER');
        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' v', 'v.id = details.variety_id', 'INNER');
        $this->db->select("GROUP_CONCAT(v.name SEPARATOR ', ') AS focused_variety");
        $this->db->group_by('details.focus_id');*/
        $items = $this->db->get()->result_array();

        $this->db->flush_cache(); // Flush/Clear current Query Stack

        $this->db->from($this->config->item('table_bi_setup_variety_focused_details') . ' details');
        $this->db->select("details.focus_id, GROUP_CONCAT( TRIM(v.name) SEPARATOR ' | ') AS focused_variety");
        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' v', 'v.id = details.variety_id', 'INNER');
        $this->db->where('details.revision', 1);
        $this->db->group_by('details.focus_id');
        $results = $this->db->get()->result_array();

        $detail_items = array();
        foreach($results as $result){
            $detail_items[$result['focus_id']] = $result['focused_variety'];
        }

        foreach($items as &$item)
        {
            if($item['focus_id'] > 0){
                $item['focused_variety'] = $detail_items[$item['focus_id']];
            }
        }

        $this->json_return($items);
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)) {
            if ($id > 0) {
                $outlet_id = $id;
            } else {
                $outlet_id = $this->input->post('id');
            }
            $data = array();

            $this->common_query(); // Call Common part of below Query Stack
            // Additional Conditions -STARTS
            $this->db->where('cus_info.customer_id', $outlet_id);
            // Additional Conditions -ENDS
            $data['item'] = $this->db->get()->row_array();

            $this->db->flush_cache(); // Flush/Clear current Query Stack

            if (!$data['item']) {
                System_helper::invalid_try(__FUNCTION__, $outlet_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item'])) {
                System_helper::invalid_try(__FUNCTION__, $outlet_id, $this->lang->line('MSG_LOCATION_ERROR'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
                $this->json_return($ajax);
            }

            $data['item']['focusable_variety'] = Query_helper::get_info($this->config->item('table_bi_setup_variety_focused'), 'id, variety_focused_count', array('outlet_id =' . $outlet_id, 'status ="'.$this->config->item('system_status_active').'"'), 1);
            if ($data['item']['focusable_variety']) {
                $details = Query_helper::get_info($this->config->item('table_bi_setup_variety_focused_details'), '*', array('revision=1', 'focus_id =' . $data['item']['focusable_variety']['id']));
                foreach($details as $detail)
                {
                    $data['item']['focusable_varieties'][$detail['variety_id']]['season'] = explode(',',  trim($detail['season'], ","));
                    $data['item']['focusable_varieties'][$detail['variety_id']]['sales_date_start'] = Bi_helper::cultivation_date_display($detail['sales_date_start']);
                    $data['item']['focusable_varieties'][$detail['variety_id']]['sales_date_end'] = Bi_helper::cultivation_date_display($detail['sales_date_end']);
                }
            }else{
                $data['item']['focusable_varieties'] = array();
            }

            $result_seasons = Query_helper::get_info($this->config->item('table_bi_setup_season'), '*', array('status ="' . $this->config->item('system_status_active') . '"'));
            if (!$result_seasons) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_SEASON_ERROR');
                $this->json_return($ajax);
            }
            $data['seasons'] = array();
            foreach ($result_seasons as $result_season) {
                $data['seasons'][$result_season['id']] = array(
                    'name' => $result_season['name'],
                    'date_start' => $result_season['date_start'],
                    'date_end' => $result_season['date_end']
                );
            }

            $results = Bi_helper::get_all_varieties();

            $data['crops'] = array();
            $data['rowspans'] = array();
            foreach ($results as $result) {
                $data['crops'][$result['crop_id']]['name'] = $result['crop_name'];
                $data['crops'][$result['crop_id']]['types'][$result['crop_type_id']]['name'] = $result['crop_type_name'];
                $data['crops'][$result['crop_id']]['types'][$result['crop_type_id']]['varieties'][$result['variety_id']] = $result['variety_name'];

                if(!isset($data['rowspans']['crop'][$result['crop_id']])){
                    $data['rowspans']['crop'][$result['crop_id']] = 0;
                }
                if(!isset($data['rowspans']['type'][$result['crop_type_id']])){
                    $data['rowspans']['type'][$result['crop_type_id']] = 0;
                }

                $data['rowspans']['crop'][$result['crop_id']]++; // For calculating Crop Rows
                $data['rowspans']['type'][$result['crop_type_id']]++; // For calculating Crop Type Rows
            }

            $data['variety_list'] = $this->load->view($this->controller_url . "/get_variety_info", $data, true);

            $data['title'] = "Edit ".($this->lang->line('LABEL_OUTLET_NAME'))."-wise Focusable variety ( ".($this->lang->line('LABEL_OUTLET_NAME'))." ID: " . $data['item']['id'] . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $outlet_id);
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        //Permission Checking
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $outlet_id = $this->input->post('id'); // $item_id Outlet ID
        $item_head = $this->input->post('item');
        $varieties = $this->input->post('variety');

        $user = User_helper::get_user();
        $time = time();

        $this->common_query(); // Call Common part of below Query Stack
        // Additional Conditions -STARTS
        $this->db->where('cus_info.customer_id', $outlet_id);
        // Additional Conditions -ENDS
        $result = $this->db->get()->row_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack
        if (!$result) {
            System_helper::invalid_try(__FUNCTION__, $outlet_id, $this->lang->line('MSG_ID_NOT_EXIST'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result)) {
            System_helper::invalid_try(__FUNCTION__, $outlet_id, $this->lang->line('MSG_LOCATION_ERROR'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
            $this->json_return($ajax);
        }
        if (!$this->check_validation()) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $result_seasons = Query_helper::get_info($this->config->item('table_bi_setup_season'), '*', array('status ="' . $this->config->item('system_status_active') . '"'));
        if (!$result_seasons) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_SEASON_ERROR');
            $this->json_return($ajax);
        }

        $variety_focused = Query_helper::get_info($this->config->item('table_bi_setup_variety_focused'), 'id, outlet_id', array('outlet_id =' . $outlet_id, 'status ="'.$this->config->item('system_status_active').'"'), 1);

        $this->db->trans_start(); //DB Transaction Handle START

        $item_head['variety_focused_count'] = sizeof($varieties); // From Post Data
        $item_head['outlet_id'] = $outlet_id;

        if ($variety_focused['id'] > 0) {
            // EDIT Update
            $item_head['date_updated'] = $time;
            $item_head['user_updated'] = $user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_bi_setup_variety_focused'), $item_head, array('id =' . $variety_focused['id']), FALSE);

            // Update Revision for Details Table
            $this->db->set('revision', 'revision+1', FALSE);
            Query_helper::update($this->config->item('table_bi_setup_variety_focused_details'), array(), array('focus_id =' . $variety_focused['id']), FALSE);

            $item_id = $variety_focused['id'];
        } else {
            // Prepare Main Table Data
            $item_head['status'] = $this->config->item('system_status_active');
            $item_head['revision_count'] = 1;
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;
            $item_id = Query_helper::add($this->config->item('table_bi_setup_variety_focused'), $item_head, FALSE);
        }

        //Insert Details Data
        foreach ($varieties as $variety_id => $variety) {
            $date_start = System_helper::get_time($variety['date_start'] . '-1970');
            $date_end = System_helper::get_time($variety['date_end'] . '-1970');
            if ($date_end < $date_start) {
                $date_end = System_helper::get_time($variety['date_end'] . '-1971');
            }
            if ($date_end != 0) {
                $date_end += 24 * 3600 - 1;
            }
            $items = array(
                'focus_id' => $item_id,
                'variety_id' => $variety_id,
                'season' => ',' . (implode(',', $variety['season'])) . ',',
                'season_count' => sizeof($variety['season']),
                'sales_date_start' => $date_start,
                'sales_date_end' => $date_end,
                'revision' => 1
            );
            Query_helper::add($this->config->item('table_bi_setup_variety_focused_details'), $items, FALSE);
        }

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
                $outlet_id = $id;
            } else {
                $outlet_id = $this->input->post('id');
            }

            $this->common_query(); // Call Common part of below Query Stack
            // Additional Conditions -STARTS
            $this->db->where('cus_info.customer_id', $outlet_id);
            // Additional Conditions -ENDS
            $result = $this->db->get()->row_array();
            $this->db->flush_cache(); // Flush/Clear current Query Stack

            if (!$result) {
                System_helper::invalid_try(__FUNCTION__, $outlet_id, $this->lang->line('MSG_ID_NOT_EXIST'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
                $this->json_return($ajax);
            }
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

            $focused_varieties = array();
            $data['item']['focusable_variety'] = Query_helper::get_info($this->config->item('table_bi_setup_variety_focused'), 'id, variety_focused_count', array('outlet_id =' . $outlet_id, 'status ="'.$this->config->item('system_status_active').'"'), 1);
            if ($data['item']['focusable_variety']) {
                $details = Query_helper::get_info($this->config->item('table_bi_setup_variety_focused_details'), '*', array('revision=1', 'focus_id =' . $data['item']['focusable_variety']['id']));
                foreach($details as $detail)
                {
                    $data['item']['focusable_varieties'][$detail['variety_id']]['season'] = explode(',',  trim($detail['season'], ","));
                    $data['item']['focusable_varieties'][$detail['variety_id']]['sales_date_start'] = Bi_helper::cultivation_date_display($detail['sales_date_start']);
                    $data['item']['focusable_varieties'][$detail['variety_id']]['sales_date_end'] = Bi_helper::cultivation_date_display($detail['sales_date_end']);
                    $focused_varieties[] = $detail['variety_id'];
                }
            }else{
                $data['item']['focusable_varieties'] = array();
            }

            $result_seasons = Query_helper::get_info($this->config->item('table_bi_setup_season'), '*', array('status ="' . $this->config->item('system_status_active') . '"'));
            // Validation
            if (!$result_seasons) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_SEASON_ERROR');
                $this->json_return($ajax);
            }
            // Validation
            if (!$focused_varieties) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_VARIETY_ERROR');
                $this->json_return($ajax);
            }

            $data['seasons'] = array();
            foreach ($result_seasons as $result_season) {
                $data['seasons'][$result_season['id']] = array(
                    'name' => $result_season['name'],
                    'date_start' => $result_season['date_start'],
                    'date_end' => $result_season['date_end']
                );
            }

            $results = Bi_helper::get_all_varieties('', $focused_varieties);

            $data['crops'] = array();
            $data['rowspans'] = array();
            foreach ($results as $result) {
                $data['crops'][$result['crop_id']]['name'] = $result['crop_name'];
                $data['crops'][$result['crop_id']]['types'][$result['crop_type_id']]['name'] = $result['crop_type_name'];
                $data['crops'][$result['crop_id']]['types'][$result['crop_type_id']]['varieties'][$result['variety_id']] = $result['variety_name'];

                if(!isset($data['rowspans']['crop'][$result['crop_id']])){
                    $data['rowspans']['crop'][$result['crop_id']] = 0;
                }
                if(!isset($data['rowspans']['type'][$result['crop_type_id']])){
                    $data['rowspans']['type'][$result['crop_type_id']] = 0;
                }

                $data['rowspans']['crop'][$result['crop_id']]++; // For calculating Crop Rows
                $data['rowspans']['type'][$result['crop_type_id']]++; // For calculating Crop Type Rows
            }

            $data['variety_list'] = $this->load->view($this->controller_url . "/get_variety_info_view", $data, true);

            $data['title'] = "Focused Variety Details ( ID:" . $outlet_id . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details/' . $outlet_id);
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
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

    private function check_validation()
    {
        $varieties = $this->input->post('variety');
        if (!$varieties) {
            $this->message = 'At least One ' . $this->lang->line('LABEL_VARIETY_NAME') . ' need to Save.';
            return false;
        } else {
            foreach ($varieties as $variety) {
                if (!isset($variety['season']) || (sizeof($variety['season']) == 0)) {
                    $this->message = 'At least One ' . $this->lang->line('LABEL_SEASON_NAME') . ' has to Select.';
                    return false;
                }
                if (!isset($variety['date_start']) || (trim($variety['date_start']) == '')) {
                    $this->message = $this->lang->line('LABEL_DATE_START') . ' field is Required.';
                    return false;
                }
                if (!isset($variety['date_end']) || (trim($variety['date_end']) == '')) {
                    $this->message = $this->lang->line('LABEL_DATE_END') . ' field is Required.';
                    return false;
                }
            }
        }
        return true;
    }

    private function common_query()
    {
        $this->db->start_cache();

        $this->db->from($this->config->item('table_login_csetup_cus_info') . ' cus_info');
        $this->db->select('cus_info.customer_id id, cus_info.name outlet_name');

        $this->db->join($this->config->item('table_bi_setup_variety_focused') . ' vf', "vf.outlet_id = cus_info.customer_id AND vf.status = '" . $this->config->item('system_status_active') . "'", 'LEFT');
        $this->db->select('vf.id focus_id, vf.outlet_id, vf.variety_focused_count');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
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
        $this->db->where('cus_info.type', $this->config->item('system_customer_type_outlet_id'));
        $this->db->where('cus_info.revision', 1);
        $this->db->order_by('division.id');
        $this->db->order_by('zone.id');
        $this->db->order_by('district.id');
        $this->db->order_by('cus_info.customer_id');

        $this->db->stop_cache();
    }
}
