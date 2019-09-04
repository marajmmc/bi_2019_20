<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Market_size_request extends Root_Controller
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
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
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
        if ($method == 'list_all')
        {
            $data['status'] = 1;
            $data['status_forward'] = 1;
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
            $data['title'] = "Upazilla Wise Market Size List";
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
        $this->db->select('ms.*');

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

        $this->db->where('ms.status', $this->config->item('system_status_active'));
        $this->db->order_by('division.name');
        $this->db->order_by('zone.name');
        $this->db->order_by('territory.name');
        $this->db->order_by('district.name');
        $this->db->order_by('ms.id');
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

            $data['upazillas'] = array();
            $results = Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'), array('id value', 'name text', 'district_id'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            foreach ($results as $result)
            {
                $data['upazillas'][$result['district_id']][] = $result;
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

            $data['upazillas'] = array();
            $results = Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'), array('id value', 'name text', 'district_id'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            foreach ($results as $result)
            {
                $data['upazillas'][$result['district_id']][] = $result;
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
        if ($item['id'] > 0)
        {
            foreach ($crop_wise_market_size as $crop_id => $market_size)
            {
                foreach ($market_size as $crop_type_id => $size)
                {
                    if (is_numeric($size) && ($size > 0))
                    {
                        $market_size_array[$crop_id][$crop_type_id] = $size;
                    }
                }
            }
        }
        else
        {
            foreach ($crop_wise_market_size as $crop_id => $market_size)
            {
                foreach ($market_size as $crop_type_id => $size)
                {
                    if (is_numeric($size) && ($size > 0))
                    {
                        $market_size_array[$crop_id][$crop_type_id] = $size;
                    }
                }
            }
            $item['market_size'] = json_encode($market_size_array, TRUE);
        }

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
            $this->db->from($this->config->item('table_bi_market_size_request') . ' ms');
            $this->db->select('ms.*');

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

            $this->db->where('ms.id', $item_id);
            $this->db->where('ms.status', $this->config->item('system_status_active'));
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids = array(
                $result['user_created'] => $result['user_created'],
                $result['user_updated'] => $result['user_updated']
            );
            $user_info = System_helper::get_users_info($user_ids);

            $items = array();
            // Competitor Basic Information
            $items[0] = array(
                'header' => 'Competitor\'s Variety Information',
                'div_id' => 'basic_info',
                'collapse' => 'in',
                'data' => array(
                    array(
                        'label_1' => $this->lang->line('LABEL_COMPETITOR_NAME'),
                        'value_1' => $result['competitor_name'],
                        'label_2' => $this->lang->line('LABEL_CROP_NAME'),
                        'value_2' => $result['crop_name']
                    ),
                    array(
                        'label_1' => $this->lang->line('LABEL_CROP_TYPE_NAME'),
                        'value_1' => $result['crop_type_name'],
                        'label_2' => $this->lang->line('LABEL_VARIETY_NAME'),
                        'value_2' => $result['variety_name']
                    ),
                    array(
                        'label_1' => $this->lang->line('LABEL_HYBRID'),
                        'value_1' => $result['hybrid'],
                        'label_2' => $this->lang->line('LABEL_STATUS'),
                        'value_2' => $result['status']
                    ),
                    array(
                        'label_1' => $this->lang->line('LABEL_CREATED_BY'),
                        'value_1' => $user_info[$result['user_created']]['name'],
                        'label_2' => $this->lang->line('LABEL_DATE_CREATED_TIME'),
                        'value_2' => System_helper::display_date_time($result['date_created'])
                    )
                )
            );

            if ($result['user_updated'] > 0)
            {
                $items[0]['data'][] = array(
                    'label_1' => $this->lang->line('LABEL_UPDATED_BY'),
                    'value_1' => $user_info[$result['user_updated']]['name'],
                    'label_2' => $this->lang->line('LABEL_DATE_UPDATED_TIME'),
                    'value_2' => System_helper::display_date_time($result['date_updated'])
                );
            }

            $data['items'] = $items;
            $data['title'] = "Competitor Variety Details ( ID:" . $item_id . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
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

    private function system_get_market_size()
    {
        $post = $this->input->post();
        $data = array();

        $data['html_container_id'] = $post['html_container_id'];
        $data['upazilla_id'] = $post['upazilla_id'];

        // From Request table (Current Requesting Market Size for this Upazilla)
        $data['market_size_edit'] = json_decode($post['market_size_edit'], TRUE);

        // From Main table (Previously Approved Market Size for this Upazilla)
        $this->db->from($this->config->item('table_bi_market_size_main'));
        $this->db->select('market_size');
        $this->db->where('upazilla_id', $data['upazilla_id']);
        $row = $this->db->get()->row_array();
        $data['market_size_old'] = json_decode($row['market_size'], TRUE);

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

        // Table Title
        $data['table_title'] = $post['upazilla_name'] . " - Market Size";

        if ($data)
        {
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => $data['html_container_id'], "html" => $this->load->view($this->controller_url . "/get_market_size", $data, true));
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("SET_LEADING_FARMER_AND_DEALER");
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
        foreach ($post['crop_wise_market_size'] as $market_size)
        {
            foreach ($market_size as $size)
            {
                if (is_numeric($size) && ($size > 0))
                {
                    $market_size_not_found = FALSE;
                }
            }
        }
        if ($market_size_not_found)
        {
            $this->message = 'At least one ' . $this->lang->line('LABEL_MARKET_SIZE') . ' need to save.';
            return false;
        }
        return true;
    }
}
