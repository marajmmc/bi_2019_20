<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Variety_arm_upazilla_wise_approve extends Root_Controller
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
        $this->common_view_location = 'variety_arm_upazilla_wise_request';
        $this->locations = User_helper::get_locations();
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->language_labels();
        $this->load->helper('bi_helper');
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_CREATED_TIME']='Created Time';
        $this->lang->language['LABEL_FORWARDED_TIME']='Forward Time';
        $this->lang->language['LABEL_APPROVED_TIME']='Approve Time';
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
        elseif ($action == "details")
        {
            $this->system_details($id);
        }
        elseif ($action == "approve")
        {
            $this->system_approve($id);
        }
        elseif ($action == "save_approve")
        {
            $this->system_save_approve();
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
        if ($method == 'list')
        {
            $data['created_by'] = 1;
            $data['created_time'] = 1;
            $data['forwarded_by'] = 1;
            $data['forwarded_time'] = 1;
        }
        elseif ($method == 'list_all')
        {
            $data['status'] = 1;
            $data['created_by'] = 1;
            $data['created_time'] = 1;
            $data['status_forward'] = 1;
            $data['forwarded_by'] = 1;
            $data['forwarded_time'] = 1;
            $data['status_approve'] = 1;
            $data['approved_by'] = 1;
            $data['approved_time'] = 1;
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
            $data['title'] = $this->lang->line('LABEL_UPAZILLA_NAME') . " Wise Market Size List for Approval";
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
        $this->db->from($this->config->item('table_bi_variety_arm_upazilla_request') . ' item');
        $this->db->select('item.*, revision_count number_of_edit');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = item.upazilla_id');
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

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info_created', 'user_info_created.id = item.user_created');
        $this->db->select('user_info_created.name created_by');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info_forward', 'user_info_forward.id = item.user_forwarded');
        $this->db->select('user_info_forward.name forwarded_by');

        $this->db->where('item.status', $this->config->item('system_status_active'));
        $this->db->where('item.status_forward', $this->config->item('system_status_forwarded'));
        $this->db->where('item.status_approve', $this->config->item('system_status_pending'));
        $this->db->order_by('division.name');
        $this->db->order_by('zone.name');
        $this->db->order_by('territory.name');
        $this->db->order_by('district.name');
        $this->db->order_by('item.id');
        $items = $this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['created_time'] = System_helper::display_date_time($item['date_created']);
            $item['forwarded_time'] = System_helper::display_date_time($item['date_forwarded']);
        }
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
            $data['title'] = $this->lang->line('LABEL_UPAZILLA_NAME') . " Wise Market Size All List for Approval";
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
        $this->db->from($this->config->item('table_bi_variety_arm_upazilla_request') . ' item');
        $this->db->select('item.*, revision_count number_of_edit');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = item.upazilla_id');
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

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info_created', 'user_info_created.id = item.user_created');
        $this->db->select('user_info_created.name created_by');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info_forwarded', 'user_info_forwarded.id = item.user_forwarded','LEFT');
        $this->db->select('user_info_forwarded.name forwarded_by');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info_approved', 'user_info_approved.id = item.user_approved','LEFT');
        $this->db->select('user_info_approved.name approved_by');

        $this->db->where('item.status', $this->config->item('system_status_active'));
        $this->db->where('item.status_forward', $this->config->item('system_status_forwarded'));
        $this->db->order_by('division.name');
        $this->db->order_by('zone.name');
        $this->db->order_by('territory.name');
        $this->db->order_by('district.name');
        $this->db->order_by('item.id');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['created_time'] = System_helper::display_date_time($item['date_created']);
            $item['forwarded_time'] = System_helper::display_date_time($item['date_forwarded']);
            $item['approved_time'] = System_helper::display_date_time($item['date_approved']);
        }
        $this->json_return($items);
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
            $this->db->from($this->config->item('table_bi_variety_arm_upazilla_request') . ' item');
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

            $data['info_basic'] = Bi_helper::get_basic_info($data['item']);

            /// discussion :: all crop should be show or entry crop show?
            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' variety');
            $this->db->select('variety.id variety_id, variety.name variety_name, variety.crop_type_id');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = variety.crop_type_id', 'INNER');
            $this->db->select('type.id crop_type_id, type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');

            $this->db->where('type.status', $this->config->item('system_status_active'));
            $this->db->where('crop.status', $this->config->item('system_status_active'));
            $this->db->where('variety.whose', 'ARM');

            $this->db->order_by('crop.ordering', 'ASC');
            $this->db->order_by('type.ordering', 'ASC');
            $this->db->order_by('variety.ordering', 'ASC');
            $results = $this->db->get()->result_array();
            $data['varieties'] = array();
            $data['variety_info'] = array();
            foreach($results as $result)
            {
                $data['varieties'][$result['crop_id']]['crop_id']=$result['crop_id'];
                $data['varieties'][$result['crop_id']]['crop_name']=$result['crop_name'];
                $data['varieties'][$result['crop_id']]['crop_type'][$result['crop_type_id']]['crop_type_id']=$result['crop_type_id'];
                $data['varieties'][$result['crop_id']]['crop_type'][$result['crop_type_id']]['crop_type_name']=$result['crop_type_name'];
                $data['varieties'][$result['crop_id']]['crop_type'][$result['crop_type_id']]['varieties'][$result['variety_id']]['variety_id']=$result['variety_id'];
                $data['varieties'][$result['crop_id']]['crop_type'][$result['crop_type_id']]['varieties'][$result['variety_id']]['variety_name']=$result['variety_name'];
                $data['variety_info'][$result['variety_id']]=$result;

            }

            $data['title'] = "Details (".$data['item']['upazilla_name'].") Upazilla Wise Assign ARM Varieties";
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
    private function system_approve($id)
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
            $this->db->from($this->config->item('table_bi_variety_arm_upazilla_request') . ' item');
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
            $this->db->where('item.status', $this->config->item('system_status_active'));
            $this->db->where('item.status_forward', $this->config->item('system_status_forwarded'));
            $this->db->where('item.status_approve !=', $this->config->item('system_status_approved'));
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
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Approve Market Size of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Approve Market Size of other Location';
                $this->json_return($ajax);
            }

            if ($data['item']['status_approve'] == $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'This market size request is already approved.';
                $this->json_return($ajax);
            }
            $data['info_basic'] = Bi_helper::get_basic_info($data['item']);

            $variety_ids[0]=0;
            $item_varieties=json_decode($data['item']['variety_ids'],true);
            foreach($item_varieties as $type)
            {
                foreach($type['new'] as $variety_id)
                {
                    $variety_ids[$variety_id]=$variety_id;
                }
            }

            /*$type_varieties=array();
            foreach($item_varieties as $type_id=>$type)
            {
                foreach($type['new'] as $variety_id)
                {
                    $type_varieties[$type_id][]=$variety_id;
                }
            }
            foreach($type_varieties as $type_id=>$type_variety)
            {
                echo '<pre>';
                print_r($type_id);
                echo '</pre>';
            }*/

            /// discussion :: all crop should be show or entry crop show?
            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' variety');
            $this->db->select('variety.id variety_id, variety.name variety_name, variety.crop_type_id');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = variety.crop_type_id', 'INNER');
            $this->db->select('type.id crop_type_id, type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');

            $this->db->where('type.status', $this->config->item('system_status_active'));
            $this->db->where('crop.status', $this->config->item('system_status_active'));
            $this->db->where('variety.whose', 'ARM');
            $this->db->where_in('variety.id', $variety_ids);

            $this->db->order_by('crop.ordering', 'ASC');
            $this->db->order_by('type.ordering', 'ASC');
            $this->db->order_by('variety.ordering', 'ASC');
            $results = $this->db->get()->result_array();
            $data['varieties'] = array();
            $data['variety_info'] = array();
            foreach($results as $result)
            {
                $data['varieties'][$result['crop_id']]['crop_id']=$result['crop_id'];
                $data['varieties'][$result['crop_id']]['crop_name']=$result['crop_name'];
                $data['varieties'][$result['crop_id']]['crop_type'][$result['crop_type_id']]['crop_type_id']=$result['crop_type_id'];
                $data['varieties'][$result['crop_id']]['crop_type'][$result['crop_type_id']]['crop_type_name']=$result['crop_type_name'];
                $data['varieties'][$result['crop_id']]['crop_type'][$result['crop_type_id']]['varieties'][$result['variety_id']]['variety_id']=$result['variety_id'];
                $data['varieties'][$result['crop_id']]['crop_type'][$result['crop_type_id']]['varieties'][$result['variety_id']]['variety_name']=$result['variety_name'];
                $data['variety_info'][$result['variety_id']]=$result;

            }

            $data['title'] = "Approve Assign (".$data['item']['upazilla_name'].") Upazilla Wise ARM Variety";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/approve", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/approve/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_approve()
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
        $this->db->from($this->config->item('table_bi_variety_arm_upazilla_request') . ' item');
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
        $this->db->where('item.status', $this->config->item('system_status_active'));
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
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Approve Major Competitor Varieties of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Approve Major Competitor Varieties of other Location';
            $this->json_return($ajax);
        }
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $item_varieties=json_decode($result['variety_ids'],true);
        $type_varieties=array();
        foreach($item_varieties as $type_id=>$type)
        {
            foreach($type['new'] as $variety_id)
            {
                $type_varieties[$type_id][]=$variety_id;
            }
        }

        $this->db->trans_start(); //DB Transaction Handle START

        if ($item['status_approve'] == $this->config->item('system_status_rollback'))
        {
            $item['status_forward'] = $this->config->item('system_status_pending');
            $item['remarks_rollback'] = $item['remarks_approve'];
            $item['date_rollback'] = $time;
            $item['user_rollback'] = $user->user_id;
            unset($item['status_approve']);
            unset($item['remarks_approve']);
        }
        else if ($item['status_approve'] == $this->config->item('system_status_rejected'))
        {
            $item['date_approved'] = $time;
            $item['user_approved'] = $user->user_id;
        }
        else
        {
            foreach($type_varieties as $type_id=>$type_variety)
            {
                $result_item = Query_helper::get_info($this->config->item('table_bi_variety_arm_upazilla'), '*', array("type_id=".$type_id,"upazilla_id =" . $result['upazilla_id']), 1);
                if($result_item)
                {
                    $data=array();
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    $this->db->set('revision_count', 'revision_count+1', false);
                    Query_helper::update($this->config->item('table_bi_variety_arm_upazilla'), $data, array("id=".$result_item['id']), false);
                }
                else
                {
                    $data=array();
                    $data['date_created'] = $time;
                    $data['user_created'] = $user->user_id;
                    $data['upazilla_id'] = $result['upazilla_id'];
                    $data['type_id'] = $type_id;
                    $data['variety_ids'] = json_encode($type_variety);
                    $data['revision_count'] = 1;
                    Query_helper::add($this->config->item('table_bi_variety_arm_upazilla'), $data, false);
                }
            }

            $item['remarks_approve'] = $item['remarks_approve'];
            $item['date_approved'] = $time;
            $item['user_approved'] = $user->user_id;
        }
        // Request Table UPDATE
        Query_helper::update($this->config->item('table_bi_variety_arm_upazilla_request'), $item, array("id =" . $item_id), false);

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
    private function check_validation()
    {
        $item = $this->input->post('item');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('item[status_approve]', $this->lang->line('LABEL_STATUS_APPROVE'), 'trim|required');
        // `Remarks` is mandatory for Rollback & Reject.
        if (($item['status_approve'] == $this->config->item('system_status_rollback')) || ($item['status_approve'] == $this->config->item('system_status_rejected')))
        {
            $this->form_validation->set_rules('item[remarks_approve]', $this->lang->line('LABEL_REMARKS'), 'trim|required');
        }
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
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
