<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Market_size_approve extends Root_Controller
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
        elseif ($action == "set_preference")
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
        if ($method == 'list_all')
        {
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

        $this->db->where('ms.status', $this->config->item('system_status_active'));
        $this->db->where('ms.status_forward', $this->config->item('system_status_forwarded'));
        $this->db->where('ms.status_approved !=', $this->config->item('system_status_approved'));
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

        $this->db->where('ms.status', $this->config->item('system_status_active'));
        $this->db->where('ms.status_forward', $this->config->item('system_status_forwarded'));
        $this->db->order_by('division.name');
        $this->db->order_by('zone.name');
        $this->db->order_by('territory.name');
        $this->db->order_by('district.name');
        $this->db->order_by('ms.id');
        $items = $this->db->get()->result_array();
        $this->json_return($items);
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
            $this->db->where('ms.status_forward', $this->config->item('system_status_forwarded'));
            $this->db->where('ms.status_approved !=', $this->config->item('system_status_approved'));
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

            if ($data['item']['status_approved'] == $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'This market size request is already approved.';
                $this->json_return($ajax);
            }

            $data['title'] = "Approve Market Size ( ID:" . $item_id . " )";
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
        /*echo '<pre>';
        print_r($this->input->post());
        echo '</pre>';*/


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
        if ($item['status_approved'] != $this->config->item('system_status_approved'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('LABEL_STATUS_APPROVE') . ' field is required.';
            $this->json_return($ajax);
        }

        $market_sizes = json_decode($result['market_size'], TRUE);
        /*echo '<pre>';
        print_r($market_sizes);
        print_r($result);
        echo '</pre>';
        die('=====================');*/

        $this->db->trans_start(); //DB Transaction Handle START

        if($item['status_approved'] == $this->config->item('system_status_approved'))
        {
            $market_sizes = json_decode($result['market_size'], TRUE);
            foreach($market_sizes as $type_id => $market_size)
            {
                Query_helper::update($this->config->item('table_bi_market_size_main'), array('market_size_kg'=>$market_size), array("type_id =" . $type_id, "upazilla_id =" . $result['upazilla_id']), FALSE);
            }

            $item['date_approved'] = $time;
            $item['user_approved'] = $user->user_id;
            // Main Table UPDATE
            Query_helper::update($this->config->item('table_bi_market_size_request'), $item, array("id =" . $item_id), FALSE);
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
}
