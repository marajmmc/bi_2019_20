<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Major_competitor_variety_request extends Root_Controller
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
        $this->common_view_location = 'major_competitor_variety_request';
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
        elseif ($action == "get_competitor_variety_info")
        {
            $this->system_get_competitor_variety_info();
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
            $data['status_approve'] = 1;
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
            $data['title'] = $this->lang->line('LABEL_UPAZILLA_NAME') . " Major Competitor List";
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
        $this->db->from($this->config->item('table_bi_major_competitor_variety_request') . ' item');
        $this->db->select('item.*, revision_count number_of_edit');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazillas', 'upazillas.id = item.upazilla_id');
        $this->db->select('upazillas.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = upazillas.district_id');
        $this->db->select('districts.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territories', 'territories.id = districts.territory_id', 'INNER');
        $this->db->select('territories.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zones', 'zones.id = territories.zone_id', 'INNER');
        $this->db->select('zones.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' divisions', 'divisions.id = zones.division_id', 'INNER');
        $this->db->select('divisions.name division_name');

        $this->db->where('item.status', $this->config->item('system_status_active'));
        $this->db->where('item.status_forward !=', $this->config->item('system_status_forwarded'));
        $this->db->order_by('item.id', 'DESC');
        if ($this->locations['division_id'] > 0)
        {
            $this->db->where('divisions.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0)
            {
                $this->db->where('zones.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0)
                {
                    $this->db->where('territories.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0)
                    {
                        $this->db->where('districts.id', $this->locations['district_id']);
                        if ($this->locations['upazilla_id'] > 0)
                        {
                            $this->db->where('upazillas.id', $this->locations['upazilla_id']);
                        }
                    }
                }
            }
        }
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.id = item.user_created');
        $this->db->select('user_info.name requested_by');

        $this->db->where('item.status', $this->config->item('system_status_active'));
        $this->db->where('item.status_forward !=', $this->config->item('system_status_forwarded'));
        $this->db->order_by('divisions.name');
        $this->db->order_by('zones.name');
        $this->db->order_by('territories.name');
        $this->db->order_by('districts.name');
        $this->db->order_by('item.id');
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
            $data['title'] = $this->lang->line('LABEL_UPAZILLA_NAME') . " Wise Major Competitor All List";
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
        $this->db->from($this->config->item('table_bi_major_competitor_variety_request') . ' item');
        $this->db->select('item.*, revision_count number_of_edit');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazillas', 'upazillas.id = item.upazilla_id');
        $this->db->select('upazillas.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = upazillas.district_id');
        $this->db->select('districts.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territories', 'territories.id = districts.territory_id', 'INNER');
        $this->db->select('territories.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zones', 'zones.id = territories.zone_id', 'INNER');
        $this->db->select('zones.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' divisions', 'divisions.id = zones.division_id', 'INNER');
        $this->db->select('divisions.name division_name');

        $this->db->order_by('item.id', 'DESC');
        if ($this->locations['division_id'] > 0)
        {
            $this->db->where('divisions.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0)
            {
                $this->db->where('zones.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0)
                {
                    $this->db->where('territories.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0)
                    {
                        $this->db->where('districts.id', $this->locations['district_id']);
                        if ($this->locations['upazilla_id'] > 0)
                        {
                            $this->db->where('upazillas.id', $this->locations['upazilla_id']);
                        }
                    }
                }
            }
        }

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.id = item.user_created');
        $this->db->select('user_info.name requested_by');

        $this->db->order_by('divisions.name');
        $this->db->order_by('zones.name');
        $this->db->order_by('territories.name');
        $this->db->order_by('districts.name');
        $this->db->order_by('item.id');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data = array();
            $data['item'] = Array
            (
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

            $data['divisions'] = Query_helper::get_info($this->config->item('table_login_setup_location_divisions'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'));
            $data['zones'] = array();
            $data['territories'] = array();
            $data['districts'] = array();
            $data['upazillas'] = array();
            if ($this->locations['division_id'] > 0)
            {
                $data['zones'] = Query_helper::get_info($this->config->item('table_login_setup_location_zones'), array('id value', 'name text'), array('division_id =' . $this->locations['division_id'], 'status ="' . $this->config->item('system_status_active') . '"'));
                if ($this->locations['zone_id'] > 0)
                {
                    $data['territories'] = Query_helper::get_info($this->config->item('table_login_setup_location_territories'), array('id value', 'name text'), array('zone_id =' . $this->locations['zone_id'], 'status ="' . $this->config->item('system_status_active') . '"'));
                    if ($this->locations['territory_id'] > 0)
                    {
                        $data['districts'] = Query_helper::get_info($this->config->item('table_login_setup_location_districts'), array('id value', 'name text'), array('territory_id =' . $this->locations['territory_id'], 'status ="' . $this->config->item('system_status_active') . '"'));
                        if ($this->locations['district_id'] > 0)
                        {
                            if ($this->locations['upazilla_id'] > 0)
                            {
                                $upazillas = Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'), array('id value', 'name text'), array('id =' . $this->locations['upazilla_id'], 'status ="' . $this->config->item('system_status_active') . '"'));
                            }
                            else
                            {
                                $upazillas = Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'), array('id value', 'name text'), array('district_id =' . $this->locations['district_id'], 'status ="' . $this->config->item('system_status_active') . '"'));
                            }
                            foreach ($upazillas as $upazilla)
                            {
                                $data['upazillas'][$this->locations['upazilla_id']][] = $upazilla;
                            }
                        }
                    }
                }
            }
            /*$data['upazillas'] = array();
            $results = Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'), array('id value', 'name text', 'district_id'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            foreach ($results as $result)
            {
                $data['upazillas'][$result['district_id']][] = $result;
            }*/

            $data['title'] = "Add Major Competitor Variety for  " . ($this->lang->line('LABEL_UPAZILLA_NAME')) . " Area";
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
            $this->db->from($this->config->item('table_bi_major_competitor_variety_request') . ' item');
            $this->db->select('item.*');

            $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = item.upazilla_id');
            $this->db->select('upazilla.id upazilla_id, upazilla.name upazilla_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id', 'INNER');
            $this->db->select('district.id district_id, district.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
            $this->db->select('territory.id territory_id, territory.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.id division_id, division.name division_name');

            $this->db->where('item.id', $item_id);
            $this->db->where('item.status', $this->config->item('system_status_active'));
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

            $data['zones'] = true;
            $data['territories'] = true;
            $data['districts'] = true;
            $data['upazillas'] = true;

            $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' type');
            $this->db->select('type.id crop_type_id, type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');

            $this->db->where('type.status', $this->config->item('system_status_active'));
            $this->db->where('crop.status', $this->config->item('system_status_active'));

            $this->db->order_by('crop.ordering', 'ASC');
            $this->db->order_by('type.ordering', 'ASC');
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

            $data['title'] = "Edit Major Competitor Variety ( " . $data['item']['upazilla_name'] . " )";
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
        $id = $this->input->post('id');
        $user = User_helper::get_user();
        $time = time();
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');

        if ($id > 0) //EDIT
        {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $result = Query_helper::get_info($this->config->item('table_bi_major_competitor_variety_request'), '*', array('id =' . $id), 1);
            if (!$result)
            {
                System_helper::invalid_try('Update Non Exists', $id);
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Notice.';
                $this->json_return($ajax);
            }
            if ($result['status_forward'] == $this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Forwarded.';
                $this->json_return($ajax);
            }

        }
        else
        {
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


        $result_main = Query_helper::get_info($this->config->item('table_bi_major_competitor_variety'), '*', array('upazilla_id =' . $item_head['upazilla_id']), 1);
        if ($result_main)
        {

        }
        /*else
        {
            foreach ($items as &$item_crop)
            {
                foreach ($item_crop as &$item_variety)
                {
                    $item_variety['old'] = array();
                }
            }
        }*/

        $item_head['competitor_varieties'] = json_encode($items, TRUE);

        /*echo '<pre>';
        print_r($this->input->post());
        print_r($items);
        print_r($item_head);
        echo '</pre>';
        die('77777777777777');*/

        $this->db->trans_start(); //DB Transaction Handle START
        if ($id > 0) // Revision Update if EDIT
        {
            $item_head['date_updated'] = $time;
            $item_head['user_updated'] = $user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_bi_major_competitor_variety_request'), $item_head, array("id =" . $id), FALSE);
        }
        else
        {
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;
            $item_head['revision_count'] = 1;
            Query_helper::add($this->config->item('table_bi_major_competitor_variety_request'), $item_head, FALSE);
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

    private function system_details($id)
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
            $this->db->from($this->config->item('table_bi_major_competitor_variety_request') . ' item');
            $this->db->select('item.*');

            $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = item.upazilla_id');
            $this->db->select('upazilla.name upazilla_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id', 'INNER');
            $this->db->select('district.id district_id, district.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
            $this->db->select('territory.id territory_id, territory.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.id division_id, division.name division_name');

            $this->db->where('item.id', $item_id);
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status'] == $this->config->item('system_status_rejected'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Rejected.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_forward'] == $this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Forwarded.';
                $this->json_return($ajax);
            }

            $data['info_basic'] = Bi_helper::get_basic_info($data['item']);

            $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' type');
            $this->db->select('type.id crop_type_id, type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');

            $this->db->where('type.status', $this->config->item('system_status_active'));
            $this->db->where('crop.status', $this->config->item('system_status_active'));

            $this->db->order_by('crop.ordering', 'ASC');
            $this->db->order_by('type.ordering', 'ASC');
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

            $data['title'] = "Forward Major Competitor Varieties {$data['item']['upazilla_name']}";
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
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time = time();
        $item_head = $this->input->post('item');

        if ($id > 0)
        {
            if (!((isset($this->permissions['action7']) && ($this->permissions['action7'] == 1))))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if ($item_head['status_forward'] != $this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Forward Field is required.';
                $this->json_return($ajax);
            }
            $result = Query_helper::get_info($this->config->item('table_bi_major_competitor_variety_request'), '*', array('id =' . $id), 1);
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, 'Forward Not Exists', $id);
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($result['status'] == $this->config->item('system_status_rejected'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Rejected.';
                $this->json_return($ajax);
            }
            if ($result['status_forward'] == $this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Forwarded.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        $data = array();

        $data['date_forwarded'] = $time;
        $data['user_forwarded'] = $user->user_id;
        $data['remarks_forward'] = $item_head['remarks_forward'];
        $data['status_forward'] = $item_head['status_forward'];
        Query_helper::update($this->config->item('table_bi_major_competitor_variety_request'), $data, array('id=' . $id));

        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
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

    private function system_get_competitor_variety_info()
    {
        $post = $this->input->post();
        $data = array();
        $data['upazilla_id'] = $post['upazilla_id'];
        if ($post['competitor_variety_edit'])
        {
            $data['competitor_variety_edit'] = json_decode($post['competitor_variety_edit'], TRUE);
        }
        else
        {
            $data['competitor_variety_edit'] = array();

            $result_request = Query_helper::get_info($this->config->item('table_bi_major_competitor_variety_request'), array('*'), array('upazilla_id =' . $data['upazilla_id']), 1, 0, array('id DESC'));
            if ($result_request)
            {
                if ($result_request['status_approve'] == $this->config->item('system_status_pending'))
                {
                    $ajax['status'] = false;
                    $ajax['system_message'] = 'A request for ' . $post['upazilla_name'] . ' upazilla is already pending.';
                    $this->json_return($ajax);
                }
            }
        }

        if ($post['mode'] == 'ADD')
        {
            $result_main = Query_helper::get_info($this->config->item('table_bi_major_competitor_variety'), array('*'), array('upazilla_id =' . $data['upazilla_id']), 1);
            if ($result_main)
            {
                $competitor_varieties = json_decode($result_main['competitor_varieties'], TRUE);
                foreach ($competitor_varieties as $crop_id => $crop_types)
                {
                    foreach ($crop_types as $type_id => $comp_varieties)
                    {
                        $data['competitor_variety_edit'][$crop_id][$type_id]['old'] = $comp_varieties;
                    }
                }
            }
        }

        $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' variety');
        $this->db->select('variety.id variety_id, variety.name variety_name, variety.crop_type_id');

        $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = variety.competitor_id', 'INNER');
        $this->db->select('competitor.name competitor_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = variety.crop_type_id', 'INNER');
        $this->db->select('type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');

        $this->db->where('competitor.status', $this->config->item('system_status_active'));
        $this->db->where('type.status', $this->config->item('system_status_active'));
        $this->db->where('crop.status', $this->config->item('system_status_active'));

        $this->db->order_by('crop.ordering', 'ASC');
        $this->db->order_by('type.ordering', 'ASC');
        $competitor_variety_results = $this->db->get()->result_array();
        foreach ($competitor_variety_results as $variety_result)
        {
            $data['competitor_varieties'][$variety_result['crop_id']][$variety_result['variety_id']] = array(
                'crop_name' => $variety_result['crop_name'],
                'crop_type_id' => $variety_result['crop_type_id'],
                'crop_type_name' => $variety_result['crop_type_name'],
                'variety_name' => $variety_result['variety_name'],
                'competitor_name' => $variety_result['competitor_name']
            );
        }

        $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' type');
        $this->db->select('type.id crop_type_id, type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');

        $this->db->where('type.status', $this->config->item('system_status_active'));
        $this->db->where('crop.status', $this->config->item('system_status_active'));

        $this->db->order_by('crop.ordering', 'ASC');
        $this->db->order_by('type.ordering', 'ASC');
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

        $data['title'] = "Major Competitors in {$post['upazilla_name']} Area";
        $ajax['status'] = true;
        $ajax['system_content'][] = array("id" => "#items_container", "html" => $this->load->view($this->controller_url . "/get_competitor_variety_info", $data, true));
        if ($this->message)
        {
            $ajax['system_message'] = $this->message;
        }
        $this->json_return($ajax);
    }

    private function check_validation()
    {
        $id = $this->input->post('id');
        $item = $this->input->post('item');
        $items = $this->input->post('items');
        if (!($id > 0) && !($item['upazilla_id'] > 0))
        {
            $this->message = $this->lang->line('LABEL_UPAZILLA_NAME') . ' field is required.';
            return false;
        }
        if ($items)
        {
            $ENTRY_NOT_FOUND = TRUE;
            foreach ($items as $each_items)
            {
                foreach ($each_items as $each)
                {
                    if (isset($each['new']) && $each['new'])
                    {
                        $ENTRY_NOT_FOUND = FALSE;
                    }
                }
            }
            if ($ENTRY_NOT_FOUND)
            {
                $this->message = 'At least 1 Competitor Variety has to Save.';
                return false;
            }
        }
        return true;
    }
}
