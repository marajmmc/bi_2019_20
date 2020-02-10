<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Target_mde extends Root_Controller
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
        // Labels
        $this->lang->language['LABEL_AMOUNT_TARGET'] = 'Target Amount';
        $this->lang->language['LABEL_LOCATION'] = 'Location';
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list") {
            $this->system_list();
        } elseif ($action == "get_items") {
            $this->system_get_items();
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
        $data['year'] = 1;
        $data['month'] = 1;
        $data['amount_target'] = 1;
        if (!($this->locations['territory_id'] > 0)) {
            $data['location'] = 1;
        }
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
            $data['title'] = "Monthly MDE Target List";
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
        // Additional Conditions -STARTS
        $this->db->where('target.status', $this->config->item('system_status_active'));
        /*$this->db->where('target.status_forward', $this->config->item('system_status_pending'));*/
        // Additional Conditions -ENDS
        $items = $this->db->get()->result_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        foreach ($items as &$item) {
            $item['amount_target'] = System_helper::get_string_amount($item['amount_target']);
            $item['month'] = DateTime::createFromFormat('!m', $item['month'])->format('F');
            if(!($item['amount_target'] > 0)){
                $item['amount_target'] = '<b>No Target Assigned</b>';
            }
        }
        $this->json_return($items);
    }

    private function system_details($id)
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            } else {
                $item_id = $this->input->post('id');
            }

            $data = $this->get_item_info($item_id);

            $data['title'] = ($this->lang->line('LABEL_TERRITORY_NAME')) . "-wise Variety Target Details (ID: " . $item_id . ")";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/view_target", $data, true));
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

    private function get_item_info($item_id) // Common Item Details Info
    {

        $this->common_query(); // Call Common part of below Query Stack
        // Additional Conditions -STARTS
        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->where('target.id', $item_id);
        // Additional Conditions -ENDS
        $result = $this->db->get()->row_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        if (!$result) {
            System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
            $this->json_return($ajax);
        }

        //--------- System User Info ------------
        $user_ids = array();
        $user_ids[$result['user_created']] = $result['user_created'];
        if ($result['user_updated'] > 0) {
            $user_ids[$result['user_updated']] = $result['user_updated'];
        }
        if ($result['user_forwarded'] > 0) {
            $user_ids[$result['user_forwarded']] = $result['user_forwarded'];
        }
        $user_info = System_helper::get_users_info($user_ids);

        //---------------- Basic Info ----------------
        $data = array();
        $data['item_head'] = $result;

        if(!($result['amount_target'] > 0)){
            $amount_inword = $amount = '<span style="color:#FF0000">- No Target Assigned -</span>';
            $show_amount_inword = FALSE;
        }else{
            $amount = System_helper::get_string_amount($result['amount_target']);
            $amount_inword = Bi_helper::get_string_amount_inword($result['amount_target']);
            $show_amount_inword = TRUE;
        }

        $data['item'][] = array
        (
            'label_1' => 'Target ' . $this->lang->line('LABEL_MONTH'),
            'value_1' => (DateTime::createFromFormat('!m', $result['month'])->format('F')) . ', ' . $result['year'],
            'label_2' => $this->lang->line('LABEL_AMOUNT_TARGET'),
            'value_2' => $amount
        );
        if($show_amount_inword){
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_AMOUNT_TARGET') . ' ( In words )',
                'value_1' => $amount_inword
            );
        }
        $data['item'][] = array
        (
            'label_1' => $this->lang->line('LABEL_CREATED_BY'),
            'value_1' => $user_info[$result['user_created']]['name'] . ' ( ' . $user_info[$result['user_created']]['employee_id'] . ' )',
            'label_2' => $this->lang->line('LABEL_DATE_CREATED_TIME'),
            'value_2' => System_helper::display_date_time($result['date_created'])
        );
        if ($result['status_forward'] == $this->config->item('system_status_forwarded')) {
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_FORWARDED_BY'),
                'value_1' => $user_info[$result['user_forwarded']]['name'] . ' ( ' . $user_info[$result['user_forwarded']]['employee_id'] . ' )',
                'label_2' => $this->lang->line('LABEL_DATE_FORWARDED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_forwarded'])
            );
            if (trim($result['remarks_forward']) != '') {
                $data['item'][] = array
                (
                    'label_1' => 'Forward ' . $this->lang->line('LABEL_REMARKS'),
                    'value_1' => nl2br($result['remarks_forward'])
                );
            }
        }
        return $data;
    }

    private function common_query()
    {
        $user = User_helper::get_user();

        $this->db->start_cache();

        $this->db->from($this->config->item('table_bi_target_tsme') . ' target');
        $this->db->select('target.*, target.revision_count AS no_of_edit');

        $this->db->join($this->config->item('table_bi_target_ams') . ' parent', 'parent.id = target.ams_id AND parent.status_forward="' . $this->config->item('system_status_forwarded') . '"', 'INNER');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = target.territory_id', 'INNER');
        $this->db->select('territory.name location');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.id zone_id');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.id division_id');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = target.user_created');
        $this->db->select('user_info.name created_by');

        if ($user->user_group != $this->config->item('USER_GROUP_SUPER')) // If not SuperAdmin, Then user can only access own Item.
        {
            if ($this->locations['territory_id'] > 0) {
                $this->db->where('target.territory_id', $this->locations['territory_id']);
            }
        }
        $this->db->where('user_info.revision', 1);
        $this->db->order_by('target.id', 'DESC');
        $this->db->order_by('target.year');
        $this->db->order_by('target.month');

        $this->db->stop_cache();
    }
}
