<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Market_size_request extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public $common_view_location;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->common_view_location = 'market_size_request';
        $this->locations = User_helper::get_locations();
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->load->helper('bi_helper');
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "list_all")
        {
            $this->system_list_all();
        }
        elseif ($action == "get_items_all")
        {
            $this->system_get_items_all();
        }
        elseif ($action == "add")
        {
            $this->system_add();
        }
        elseif ($action == "edit")
        {
            $this->system_edit($id);
        }
        elseif ($action == "save")
        {
            $this->system_save();
        }
        elseif ($action == "details")
        {
            $this->system_details($id);
        }
        elseif ($action == "forward")
        {
            $this->system_forward($id);
        }
        elseif ($action == "save_forward")
        {
            $this->system_save_forward();
        }
        elseif ($action == "get_market_size")
        {
            $this->system_get_market_size($id);
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_all")
        {
            $this->system_set_preference('list_all');
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }

    private function get_preference_headers($method = 'list')
    {
        $data = array();
        $data['id'] = 1;
        $data['upazilla_name'] = 1;
        $data['district_name'] = 1;
        $data['territory_name'] = 1;
        $data['zone_name'] = 1;
        $data['division_name'] = 1;
        $data['number_of_edit'] = 1;
        $data['requested_by'] = 1;
        if ($method == 'list_all')
        {
            $data['status'] = 1;
            $data['status_forward'] = 1;
            $data['status_approved'] = 1;
        }
        return $data;
    }

    private function system_set_preference($method = 'list')
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_' . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = $this->lang->line('LABEL_UPAZILLA_NAME') . " Wise Market Size List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $this->db->from($this->config->item('table_bi_market_size_request') . ' ms');
        $this->db->select('ms.*, revision_count number_of_edit');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = ms.upazilla_id');
        $this->db->select('upazilla.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id');
        $this->db->select('district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.name division_name');
        if ($this->locations['division_id'] > 0)
        {
            $this->db->where('division.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0)
            {
                $this->db->where('zone.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0)
                {
                    $this->db->where('territory.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0)
                    {
                        $this->db->where('district.id', $this->locations['district_id']);
                    }
                }
            }
        }

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.id = ms.user_created');
        $this->db->select('user_info.name requested_by');

        $this->db->where('ms.status', $this->config->item('system_status_active'));
        $this->db->where('ms.status_forward !=', $this->config->item('system_status_forwarded'));
        $this->db->order_by('division.name');
        $this->db->order_by('zone.name');
        $this->db->order_by('territory.name');
        $this->db->order_by('district.name');
        $this->db->order_by('ms.id');
        $items = $this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list_all';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = $this->lang->line('LABEL_UPAZILLA_NAME') . " Wise Market Size All List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_all", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_all()
    {
        $current_records = $this->input->post('total_records');
        if (!$current_records)
        {
            $current_records = 0;
        }
        $pagesize = $this->input->post('pagesize');
        if (!$pagesize)
        {
            $pagesize = 100;
        }
        else
        {
            $pagesize = $pagesize * 2;
        }
        $this->db->from($this->config->item('table_bi_market_size_request') . ' ms');
        $this->db->select('ms.*, revision_count number_of_edit');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = ms.upazilla_id');
        $this->db->select('upazilla.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id');
        $this->db->select('district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.name division_name');
        if ($this->locations['division_id'] > 0)
        {
            $this->db->where('division.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0)
            {
                $this->db->where('zone.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0)
                {
                    $this->db->where('territory.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0)
                    {
                        $this->db->where('district.id', $this->locations['district_id']);
                    }
                }
            }
        }

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.id = ms.user_created');
        $this->db->select('user_info.name requested_by');

        $this->db->order_by('division.name');
        $this->db->order_by('zone.name');
        $this->db->order_by('territory.name');
        $this->db->order_by('district.name');
        $this->db->order_by('ms.id');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data = array();
            $data['item'] = Array(
                'id' => 0,
                'division_id' => 0,
                'zone_id' => 0,
                'territory_id' => 0,
                'district_id' => 0,
                'upazilla_id' => 0,
                'market_size' => '',
                'ordering' => 99,
                'status' => ''
            );

            $data['divisions'] = Query_helper::get_info($this->config->item('table_login_setup_location_divisions'), array('id value', 'name text'), array('status !="' . $this->config->item('system_status_delete') . '"'));
            if ($this->locations['division_id'] > 0)
            {
                $data['zones'] = Query_helper::get_info($this->config->item('table_login_setup_location_zones'), array('id value', 'name text'), array('division_id =' . $this->locations['division_id']));
                if ($this->locations['zone_id'] > 0)
                {
                    $data['territories'] = Query_helper::get_info($this->config->item('table_login_setup_location_territories'), array('id value', 'name text'), array('zone_id =' . $this->locations['zone_id']));
                    if ($this->locations['territory_id'] > 0)
                    {
                        $data['districts'] = Query_helper::get_info($this->config->item('table_login_setup_location_districts'), array('id value', 'name text'), array('territory_id =' . $this->locations['territory_id']));
                        if ($this->locations['district_id'] > 0)
                        {
                            $data['upazillas'] = Query_helper::get_info($this->config->item('table_login_setup_location_districts'), array('id value', 'name text'), array('district_id =' . $this->locations['district_id']));
                        }
                    }
                }
            }

            $results = Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'), array('id value', 'name text', 'district_id'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            foreach ($results as $result)
            {
                $data['system_upazillas'][$result['district_id']][] = $result;
            }

            $data['title'] = "New Market Size ( " . ($this->lang->line('LABEL_UPAZILLA_NAME')) . " Wise )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/add');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }
            $data = array();
            $this->db->from($this->config->item('table_bi_market_size_request') . ' ms');
            $this->db->select('ms.*');

            $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = ms.upazilla_id');
            $this->db->select('upazilla.name upazilla_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id', 'INNER');
            $this->db->select('district.id district_id, district.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
            $this->db->select('territory.id territory_id, territory.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.id division_id, division.name division_name');

            $this->db->where('ms.id', $item_id);
            $this->db->where('ms.status', $this->config->item('system_status_active'));
            $this->db->order_by('district.name');
            $this->db->order_by('upazilla.name');
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit Market Size of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Edit Market Size of other Location';
                $this->json_return($ajax);
            }

            $data['system_upazillas'] = array();
            $results = Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'), array('id value', 'name text', 'district_id'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            foreach ($results as $result)
            {
                $data['system_upazillas'][$result['district_id']][] = $result;
            }

            $data['title'] = "Edit Competitor Variety ( ID:" . $data['item']['id'] . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $item_id = $this->input->post('id');
        $item = $this->input->post('item');
        $crop_wise_market_size = $this->input->post('crop_wise_market_size');
        $user = User_helper::get_user();
        $time = time();

        if ($item_id > 0) //EDIT
        {
            //Permission Checking
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_bi_market_size_request') . ' ms');
            $this->db->select('ms.*');

            $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = ms.upazilla_id');
            $this->db->select('upazilla.name upazilla_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id', 'INNER');
            $this->db->select('district.id district_id, district.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
            $this->db->select('territory.id territory_id, territory.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.id division_id, division.name division_name');

            $this->db->where('ms.id', $item_id);
            $this->db->where('ms.status', $this->config->item('system_status_active'));
            $this->db->order_by('district.name');
            $this->db->order_by('upazilla.name');
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($result))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Save Market Size of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Save Market Size of other Location';
                $this->json_return($ajax);
            }
        }
        else //ADD
        {
            //Permission Checking
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        //Validation Checking
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $market_size_array = array();
        foreach ($crop_wise_market_size as $crop_type_id => $market_size)
        {
            if ((trim($market_size['new']) != '') && ($market_size['new'] != $market_size['old']))
            {
                $market_size_array[$crop_type_id] = $market_size['new'];
            }
        }

        $item['market_size'] = json_encode($market_size_array);

        $this->db->trans_start(); //DB Transaction Handle START
        if ($item_id > 0) // Revision Update if EDIT
        { //Update
            $item['date_updated'] = $time;
            $item['user_updated'] = $user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_bi_market_size_request'), $item, array("id =" . $item_id), FALSE);
        }
        else
        { //Insert
            $item['status'] = $this->config->item('system_status_active');
            $item['revision_count'] = 1;
            $item['date_created'] = $time;
            $item['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_bi_market_size_request'), $item, FALSE);
        }
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $ajax['status'] = true;
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function system_details($id) // Competitor Variety Details
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }
            $data = array();
            $data['item_id'] = $item_id;

            $data['title'] = "Market Size Details ( ID:" . $item_id . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->common_view_location . "/details", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_forward($id)
    {
        if (isset($this->permissions['action7']) && ($this->permissions['action7'] == 1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }

            $data = array();
            $this->db->from($this->config->item('table_bi_market_size_request') . ' ms');
            $this->db->select('ms.*');

            $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = ms.upazilla_id');
            $this->db->select('upazilla.name upazilla_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id', 'INNER');
            $this->db->select('district.id district_id, district.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
            $this->db->select('territory.id territory_id, territory.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.id division_id, division.name division_name');

            $this->db->where('ms.id', $item_id);
            $this->db->where('ms.status', $this->config->item('system_status_active'));
            $this->db->order_by('district.name');
            $this->db->order_by('upazilla.name');
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Forward Market Size of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Forward Market Size of other Location';
                $this->json_return($ajax);
            }

            $data['title'] = "Forward Market Size ( ID:" . $item_id . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/forward", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/forward/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_forward()
    {
        $item_id = $this->input->post('id');
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();

        //Permission Checking
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_bi_market_size_request') . ' ms');
        $this->db->select('ms.*');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = ms.upazilla_id');
        $this->db->select('upazilla.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id', 'INNER');
        $this->db->select('district.id district_id, district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.id territory_id, territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.id zone_id, zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.id division_id, division.name division_name');

        $this->db->where('ms.id', $item_id);
        $this->db->where('ms.status', $this->config->item('system_status_active'));
        $this->db->order_by('district.name');
        $this->db->order_by('upazilla.name');
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if ($item['status_forward'] != $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('LABEL_STATUS_FORWARD') . ' field is required.';
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Forward Market Size of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Forward Market Size of other Location';
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        $item['date_forwarded'] = $time;
        $item['user_forwarded'] = $user->user_id;
        // Main Table UPDATE
        Query_helper::update($this->config->item('table_bi_market_size_request'), $item, array("id =" . $item_id), FALSE);

        $this->db->trans_complete(); //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $ajax['status'] = true;
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function system_get_market_size()
    {
        $post = $this->input->post();
        $data = array();

        $data['html_container_id'] = $post['html_container_id'];
        $data['upazilla_id'] = $post['upazilla_id'];
        $data['market_size_edit'] = json_decode($post['market_size_edit'], TRUE);
        // Table Title
        $data['table_title'] = $post['upazilla_name'] . " - Market Size";

        if (trim($post['market_size_edit']) == '')
        {
            $request_found = Query_helper::get_info($this->config->item('table_bi_market_size_request'), array('*'), array('upazilla_id =' . $data['upazilla_id'], 'status_approved !="' . $this->config->item('system_status_approved') . '"'), 1);
            if ($request_found)
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'A request for ' . $post['upazilla_name'] . ' already pending.';
                $this->json_return($ajax);
            }
        }

        // From Main table (Previously Approved Market Size for this Upazilla)
        $this->db->from($this->config->item('table_bi_market_size_main'));
        $this->db->select('*');
        $this->db->where('upazilla_id', $data['upazilla_id']);
        $results = $this->db->get()->result_array();
        foreach ($results as $result)
        {
            $data['market_size_old'][$result['type_id']] = $result['market_size_kg'];
        }

        // -------------------- For crop count -------------------------------
        $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' crop_types');
        $this->db->select('crop_types.id crop_type_id, crop_types.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'INNER');
        $this->db->select('crops.id crop_id, crops.name crop_name');

        $this->db->where('crop_types.status', $this->config->item('system_status_active'));
        $this->db->where('crops.status', $this->config->item('system_status_active'));

        $this->db->order_by('crops.id', 'ASC');
        $this->db->order_by('crop_types.ordering', 'ASC');
        $results = $this->db->get()->result_array();
        $data['crops'] = $results;
        foreach ($results as $result)
        {
            if (isset($data['crop_type_count'][$result['crop_id']]))
            {
                $data['crop_type_count'][$result['crop_id']] += 1;
            }
            else
            {
                $data['crop_type_count'][$result['crop_id']] = 1;
            }
        }
        //-------------------------------------------------------------------
        if ($data)
        {
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => $data['html_container_id'], "html" => $this->load->view($this->controller_url . "/get_market_size", $data, true));
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'No market size found.';
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $post = $this->input->post();
        if (!($post['id'] > 0) && !($post['item']['upazilla_id'] > 0))
        {
            $this->message = $this->lang->line('LABEL_UPAZILLA_NAME') . ' field is required.';
            return false;
        }

        $market_size_not_found = TRUE;
        $market_size_new_not_entry = TRUE;
        foreach ($post['crop_wise_market_size'] as $type_id => $market_size)
        {
            if (trim($market_size['new']) != '')
            {
                $market_size_not_found = FALSE;
                if (($market_size['new'] != $market_size['old']))
                {
                    $market_size_new_not_entry = FALSE;
                }
            }
        }
        if ($market_size_not_found)
        {
            $this->message = 'At least one ' . $this->lang->line('LABEL_MARKET_SIZE') . ' need to save.';
            return false;
        }
        if ($market_size_new_not_entry)
        {
            $this->message = 'No change found in given ' . $this->lang->line('LABEL_MARKET_SIZE') . '.';
            return false;
        }
        return true;
    }

    private function check_my_editable($item)
    {
        if (($this->locations['division_id'] > 0) && ($this->locations['division_id'] != $item['division_id']))
        {
            return false;
        }
        if (($this->locations['zone_id'] > 0) && ($this->locations['zone_id'] != $item['zone_id']))
        {
            return false;
        }
        if (($this->locations['territory_id'] > 0) && ($this->locations['territory_id'] != $item['territory_id']))
        {
            return false;
        }
        if (($this->locations['district_id'] > 0) && ($this->locations['district_id'] != $item['district_id']))
        {
            return false;
        }
        return true;
    }
}
