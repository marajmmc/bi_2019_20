<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Target_outlet_wise_approve extends Root_Controller
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
        $this->locations = User_helper::get_locations();
        if (!($this->locations)) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->common_view_location = 'target_outlet_wise_request';
        $this->load->helper('bi_helper');
        $this->language_labels();
    }

    private function language_labels()
    {
        // Labels
        $this->lang->language['LABEL_TOTAL_TARGET_VARIETIES'] = 'Total Target Varieties';
        $this->lang->language['LABEL_TOTAL_TARGET_AMOUNT'] = 'Total Target Amount';
        $this->lang->language['LABEL_NO_OF_EDIT'] = 'No. of Edit';
        $this->lang->language['LABEL_AMOUNT_TARGET'] = 'Target Amount';
        $this->lang->language['LABEL_REQUESTED_BY'] = 'Requested By';
        $this->lang->language['LABEL_REQUESTED_TIME'] = 'Requested Time';
        // Messages
        $this->lang->language['MSG_ID_NOT_EXIST'] = 'ID Not Exist.';
        $this->lang->language['MSG_INVALID_TRY'] = 'Invalid Try.';
        $this->lang->language['MSG_LOCATION_ERROR'] = 'Trying to Access Other Location';
        $this->lang->language['MSG_APPROVED_ALREADY'] = 'This Variety Target has been Approved Already';
        $this->lang->language['MSG_REJECTED'] = 'This Variety Target has been Rejected';
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list") {
            $this->system_list();
        } elseif ($action == "get_items") {
            $this->system_get_items();
        } elseif ($action == "list_all") {
            $this->system_list_all();
        } elseif ($action == "get_items_all") {
            $this->system_get_items_all();
        } elseif ($action == "details") {
            $this->system_details($id);
        } elseif ($action == "approve") {
            $this->system_approve($id);
        } elseif ($action == "save_approve") {
            $this->system_save_approve();
        } elseif ($action == "set_preference") {
            $this->system_set_preference('list');
        } elseif ($action == "set_preference_all") {
            $this->system_set_preference('list_all');
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
        $data['year'] = 1;
        $data['month'] = 1;
        $data['amount_target'] = 1;
        $data['district_name'] = 1;
        $data['territory_name'] = 1;
        $data['zone_name'] = 1;
        $data['division_name'] = 1;
        $data['no_of_edit'] = 1;
        $data['requested_time'] = 1;
        $data['requested_by'] = 1;
        if ($method == 'list_all') {
            $data['status_approve'] = 1;
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
            $data['title'] = $this->lang->line('LABEL_OUTLET_NAME') . " wise Target Approval Pending List";
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
        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->where('target.status_approve', $this->config->item('system_status_pending'));
        // Additional Conditions -ENDS

        $items = $this->db->get()->result_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        foreach ($items as &$item) {
            $item['month'] = DateTime::createFromFormat('!m', $item['month'])->format('F');
            $item['amount_target'] = System_helper::get_string_amount($item['amount_target']);
            $item['requested_time'] = System_helper::display_date_time($item['date_created']);
        }

        $this->json_return($items);
    }

    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            $user = User_helper::get_user();
            $method = 'list_all';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = $this->lang->line('LABEL_OUTLET_NAME') . " wise Target Request All List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_all", $data, true));
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

    private function system_get_items_all()
    {
        $current_records = $this->input->post('total_records');
        if (!$current_records) {
            $current_records = 0;
        }
        $pagesize = $this->input->post('pagesize');
        if (!$pagesize) {
            $pagesize = 100;
        } else {
            $pagesize = $pagesize * 2;
        }

        $this->common_query(); // Call Common part of below Query Stack

        // Additional Conditions -STARTS
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
        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->limit($pagesize, $current_records);
        // Additional Conditions -ENDS

        $items = $this->db->get()->result_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        foreach ($items as &$item) {
            $item['month'] = DateTime::createFromFormat('!m', $item['month'])->format('F');
            $item['amount_target'] = System_helper::get_string_amount($item['amount_target']);
            $item['requested_time'] = System_helper::display_date_time($item['date_created']);
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

            $data['title'] = ($this->lang->line('LABEL_OUTLET_NAME')) . "-wise Variety Target Details (ID: " . $item_id . ")";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->common_view_location . "/details", $data, true));
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

    private function system_approve($id)
    {
        if (isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            } else {
                $item_id = $this->input->post('id');
            }

            $data = $this->get_item_info($item_id);
            $data['id'] = $item_id;

            // Validation
            if ($data['item_head']['status_approve'] == $this->config->item('system_status_approved')) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_APPROVED_ALREADY');
                $this->json_return($ajax);
            }
            if ($data['item_head']['status_approve'] == $this->config->item('system_status_rejected')) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_REJECTED');
                $this->json_return($ajax);
            }

            $data['title'] = "Approve " . ($this->lang->line('LABEL_OUTLET_NAME')) . "-wise Target (ID: " . $item_id . ")";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/approve", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/approve/' . $item_id);
            $this->json_return($ajax);
        } else {
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
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1))) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

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
        if (!$this->check_my_editable($result)) {
            System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_LOCATION_ERROR'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
            $this->json_return($ajax);
        }
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        if ($result['status_approve'] == $this->config->item('system_status_approved')) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_APPROVED_ALREADY');
            $this->json_return($ajax);
        }
        if ($result['status_approve'] == $this->config->item('system_status_rejected')) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_REJECTED');
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        if($item['status_approve'] == $this->config->item('system_status_rollback'))
        {
            $item['status_forward'] = $this->config->item('system_status_pending');
            $item['remarks_rollback'] = $item['remarks_approve'];
            $item['date_rollback'] = $time;
            $item['user_rollback'] = $user->user_id;

            unset($item['status_approve']);
            unset($item['remarks_approve']);
        }
        else{
            $item['date_approved'] = $time;
            $item['user_approved'] = $user->user_id;
        }

        // Main Table UPDATE
        Query_helper::update($this->config->item('table_bi_target_outlet_wise'), $item, array("id =" . $item_id), FALSE);

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
        if ($result['user_rollback'] > 0) {
            $user_ids[$result['user_rollback']] = $result['user_rollback'];
        }
        if ($result['user_approved'] > 0) {
            $user_ids[$result['user_approved']] = $result['user_approved'];
        }
        $user_info = System_helper::get_users_info($user_ids);

        //---------------- Basic Info ----------------
        $data = array();
        $data['item_head'] = $result;
        $data['item'][] = array
        (
            'label_1' => 'Target '.$this->lang->line('LABEL_MONTH'),
            'value_1' => (DateTime::createFromFormat('!m', $result['month'])->format('F')). ', '. $result['year'],
            'label_2' => $this->lang->line('LABEL_AMOUNT_TARGET'),
            'value_2' => System_helper::get_string_amount($result['amount_target'])
        );
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
            'label_1' => $this->lang->line('LABEL_REQUESTED_BY'),
            'value_1' => $user_info[$result['user_created']]['name'] . ' ( ' . $user_info[$result['user_created']]['employee_id'] . ' )',
            'label_2' => $this->lang->line('LABEL_REQUESTED_TIME'),
            'value_2' => System_helper::display_date_time($result['date_created'])
        );
        if ($result['status_forward'] == $this->config->item('system_status_forwarded') || ($result['user_rollback'] > 0)) {
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_FORWARDED_BY'),
                'value_1' => $user_info[$result['user_forwarded']]['name'] . ' ( ' . $user_info[$result['user_forwarded']]['employee_id'] . ' )',
                'label_2' => $this->lang->line('LABEL_DATE_FORWARDED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_forwarded'])
            );
            if(trim($result['remarks_forward']) != ''){
                $data['item'][] = array
                (
                    'label_1' => 'Forward '. $this->lang->line('LABEL_REMARKS'),
                    'value_1' => nl2br($result['remarks_forward'])
                );
            }
        }
        if($result['user_rollback'] > 0){
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_ROLLBACK_BY'),
                'value_1' => $user_info[$result['user_rollback']]['name'] . ' ( ' . $user_info[$result['user_rollback']]['employee_id'] . ' )',
                'label_2' => $this->lang->line('LABEL_DATE_ROLLBACK_TIME'),
                'value_2' => System_helper::display_date_time($result['date_rollback'])
            );
            $data['item'][] = array
            (
                'label_1' => 'Rollback '. $this->lang->line('LABEL_REMARKS'),
                'value_1' => nl2br($result['remarks_rollback'])
            );
        }

        if ($result['status_approve'] == $this->config->item('system_status_approved')) {
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_APPROVED_BY'),
                'value_1' => $user_info[$result['user_approved']]['name'] . ' ( ' . $user_info[$result['user_approved']]['employee_id'] . ' )',
                'label_2' => $this->lang->line('LABEL_DATE_APPROVED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_approved'])
            );
            if(trim($result['remarks_approve']) != ''){
                $data['item'][] = array
                (
                    'label_1' => 'Approve '. $this->lang->line('LABEL_REMARKS'),
                    'value_1' => nl2br($result['remarks_approve'])
                );
            }
        }elseif ($result['status_approve'] == $this->config->item('system_status_rejected')) {
            $data['item'][] = array
            (
                'label_1' => $this->lang->line('LABEL_REJECTED_BY'),
                'value_1' => $user_info[$result['user_approved']]['name'] . ' ( ' . $user_info[$result['user_approved']]['employee_id'] . ' )',
                'label_2' => $this->lang->line('LABEL_DATE_REJECTED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_approved'])
            );
            $data['item'][] = array
            (
                'label_1' => 'Reject '. $this->lang->line('LABEL_REMARKS'),
                'value_1' => nl2br($result['remarks_approve'])
            );
        }

        return $data;
    }

    private function check_validation()
    {
        $item = $this->input->post('item');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[status_approve]', $this->lang->line('LABEL_STATUS_APPROVE'), 'required|trim');
        if(($item['status_approve'] == $this->config->item('system_status_rollback')) || ($item['status_approve'] == $this->config->item('system_status_rejected')))
        {
            $this->form_validation->set_rules('item[remarks_approve]', $this->lang->line('LABEL_REMARKS'), 'required|trim');
        }
        if ($this->form_validation->run() == FALSE) {
            $this->message = validation_errors();
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
        $user = User_helper::get_user();

        $this->db->start_cache();

        $this->db->from($this->config->item('table_bi_target_outlet_wise') . ' target');
        $this->db->select('target.*, target.revision_count AS no_of_edit');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = target.outlet_id', 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $this->db->select('district.id district_id, district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.id territory_id, territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.id zone_id, zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.id division_id, division.name division_name');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = target.user_created');
        $this->db->select('user_info.name requested_by');

        if ($user->user_group != $this->config->item('USER_GROUP_SUPER')) // If not SuperAdmin, Then user can only access own Item.
        {
            $this->db->where('target.user_created', $user->user_id);
        }
        $this->db->where('target.status_forward', $this->config->item('system_status_forwarded'));
        $this->db->where('cus_info.revision', 1);
        $this->db->where('user_info.revision', 1);
        $this->db->order_by('target.id', 'DESC');

        $this->db->stop_cache();
    }
}
