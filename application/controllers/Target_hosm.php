<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Target_hosm extends Root_Controller
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
        $this->common_view_location = 'target_hosm';
        $this->load->helper('bi_helper');
        $this->language_labels();
    }

    private function language_labels()
    {
        // Labels
        $this->lang->language['LABEL_AMOUNT_TARGET'] = 'Target Amount';
        $this->lang->language['LABEL_AMOUNT_TARGET_TOTAL'] = 'Total Target Amount';
        $this->lang->language['LABEL_NO_OF_EDIT'] = 'No. of Edit';
        // Messages
        $this->lang->language['MSG_FORWARDED_ALREADY'] = 'This Target has been Forwarded Already';
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
        } elseif ($action == "add") {
            $this->system_add();
        } elseif ($action == "edit") {
            $this->system_edit($id);
        } elseif ($action == "save") {
            $this->system_save();
        } elseif ($action == "details") {
            $this->system_details($id);
        } elseif ($action == "forward") {
            $this->system_forward($id);
        } elseif ($action == "save_forward") {
            $this->system_save_forward();
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
        $data['year'] = 1;
        $data['month'] = 1;
        $data['amount_target_total'] = 1;
        $data['no_of_edit'] = 1;
        if ($method == 'list_all') {
            $data['status_forward'] = 1;
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
            $data['title'] = "Monthly HOSM Target List";
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
        $this->db->where('target.status_forward', $this->config->item('system_status_pending'));
        // Additional Conditions -ENDS
        $items = $this->db->get()->result_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        foreach ($items as &$item) {
            $item['month'] = DateTime::createFromFormat('!m', $item['month'])->format('F');
            $item['amount_target_total'] = System_helper::get_string_amount($item['amount_target_total']);
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
            $data['title'] = "Monthly HOSM Target All List";
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
        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->limit($pagesize, $current_records);
        // Additional Conditions -ENDS
        $items = $this->db->get()->result_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        foreach ($items as &$item) {
            $item['month'] = DateTime::createFromFormat('!m', $item['month'])->format('F');
            $item['amount_target_total'] = System_helper::get_string_amount($item['amount_target_total']);
        }
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) {
            $data = array();
            $data['item'] = Array(
                'id' => 0,
                'month' => (intval(date('m')) + 1),
                'year' => intval(date('Y')),
                'targets' => array()
            );
            $data['item']['target_locations'] = Query_helper::get_info($this->config->item('table_login_setup_location_divisions'), array('id', 'name'), array('status ="' . $this->config->item('system_status_active') . '"'));
            $data['item']['label_location'] = $this->lang->line('LABEL_DIVISION_NAME');

            $data['title'] = "Add " . ($data['item']['label_location']) . "-wise Target";
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
            $data = array();

            $this->common_query(); // Call Common part of below Query Stack
            // Additional Conditions -STARTS
            $this->db->where('target.status', $this->config->item('system_status_active'));
            $this->db->where('target.id', $item_id);
            // Additional Conditions -ENDS
            $data['item'] = $this->db->get()->row_array();
            $this->db->flush_cache(); // Flush/Clear current Query Stack

            if (!$data['item']) {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
                $this->json_return($ajax);
            }
            if ($data['item']['status_forward'] == $this->config->item('system_status_forwarded')) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_ALREADY');
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item'])) {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_LOCATION_ERROR'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
                $this->json_return($ajax);
            }

            $results = Query_helper::get_info($this->config->item('table_bi_target_dsm'), array('division_id', 'amount_target'), array('hosm_id =' . $item_id));
            foreach ($results as $result) {
                $data['item']['targets'][$result['division_id']] = $result['amount_target'];
            }
            $data['item']['target_locations'] = Query_helper::get_info($this->config->item('table_login_setup_location_divisions'), array('id', 'name'), array('status ="' . $this->config->item('system_status_active') . '"'));
            $data['item']['label_location'] = $this->lang->line('LABEL_DIVISION_NAME');

            $data['title'] = "Edit " . ($data['item']['label_location']) . "-wise Target (ID: ".$item_id.")";
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
        $user = User_helper::get_user();
        $time = time();

        $item_id = $this->input->post('id');
        $item_head = $this->input->post('item');
        $amount_target = $this->input->post('amount_target');

        //Validation Checking
        if (!$this->check_validation()) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        if ($item_id > 0) //EDIT
        {
            //Permission Checking
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))) {
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
            if ($result['status_forward'] == $this->config->item('system_status_forwarded')) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_ALREADY');
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($result)) {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_LOCATION_ERROR'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
                $this->json_return($ajax);
            }
        } else //ADD
        {
            //Permission Checking
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_bi_target_hosm'));
            $this->db->select('*');
            $this->db->where('month', $item_head['month']);
            $this->db->where('year', $item_head['year']);
            $this->db->where('status', $this->config->item('system_status_active'));
            $result = $this->db->get()->row_array();
            if ($result) {
                $ajax['status'] = false;
                $ajax['system_message'] = 'A Target for same Month is Already Exist';
                $this->json_return($ajax);
            }
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $item_head['amount_target_total'] = 0;
        foreach ($amount_target as $amount) {
            $item_head['amount_target_total'] += $amount;
        }
        if ($item_id > 0) // Revision Update if EDIT
        {
            $item_head['user_updated'] = $user->user_id;
            $item_head['date_updated'] = $time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_bi_target_hosm'), $item_head, array("id = " . $item_id)); // UPDATE into Main Table

            foreach ($amount_target as $location_id => $amount) {
                $items = array(
                    'amount_target' => $amount,
                    'status' => $this->config->item('system_status_active')
                );
                $item_head['user_updated'] = $user->user_id;
                $item_head['date_updated'] = $time;
                $this->db->set('revision_count', 'revision_count+1', FALSE);
                Query_helper::update($this->config->item('table_bi_target_dsm'), $items, array('hosm_id = ' . $item_id, 'division_id = ' . $location_id)); // UPDATE into Details Table
            }

        } else {
            $item_head['revision_count'] = 1;
            $item_head['status'] = $this->config->item('system_status_active');
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;
            $item_id = Query_helper::add($this->config->item('table_bi_target_hosm'), $item_head, FALSE); // INSERT into Main Table

            // Prepare & Insert Data for Next Layer Table
            foreach ($amount_target as $location_id => $amount) {
                $items = array(
                    'hosm_id' => $item_id,
                    'year' => $item_head['year'],
                    'month' => $item_head['month'],
                    'division_id' => $location_id,
                    'amount_target' => $amount,
                    'revision_count' => 1,
                    'status' => $this->config->item('system_status_active'),
                    'date_created' => $time,
                    'user_created' => $user->user_id
                );
                Query_helper::add($this->config->item('table_bi_target_dsm'), $items, FALSE); // INSERT into Details Table
            }
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
                $item_id = $id;
            } else {
                $item_id = $this->input->post('id');
            }

            $data = $this->get_item_info($item_id);
            $data['details_title'] = 'HOSM Target Distribution';

            $location_id_field = 'division_id';
            $foreign_key = 'hosm_id';

            $this->db->from($this->config->item('table_bi_target_dsm') . ' target');
            $this->db->select("target.{$location_id_field}, target.amount_target");
            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' location', "location.id = target.{$location_id_field}", 'INNER');
            $this->db->select('location.name');
            $this->db->where("target.{$foreign_key}", $item_id);
            $data['details'] = $this->db->get()->result_array();

            $data['title'] = ($this->lang->line('LABEL_DIVISION_NAME')) . "-wise Variety Target Details (ID: " . $item_id . ")";
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

    private function system_forward($id)
    {
        if (isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            } else {
                $item_id = $this->input->post('id');
            }

            $data = $this->get_item_info($item_id);
            // Validation
            if ($data['item_head']['status_forward'] == $this->config->item('system_status_forwarded')) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_ALREADY');
                $this->json_return($ajax);
            }
            $data['id'] = $item_id;
            $data['details_title'] = 'HOSM Target Distribution';

            $location_id_field = 'division_id';
            $foreign_key = 'hosm_id';

            $this->db->from($this->config->item('table_bi_target_dsm') . ' target');
            $this->db->select("target.{$location_id_field}, target.amount_target");
            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' location', "location.id = target.{$location_id_field}", 'INNER');
            $this->db->select('location.name');
            $this->db->where("target.{$foreign_key}", $item_id);
            $data['details'] = $this->db->get()->result_array();

            $data['title'] = "Forward " . ($this->lang->line('LABEL_DIVISION_NAME')) . "-wise Target (ID: ".$item_id.")";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/forward", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/forward/' . $item_id);
            $this->json_return($ajax);
        } else {
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
        if ($item['status_forward'] != $this->config->item('system_status_forwarded')) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('LABEL_STATUS_FORWARD') . ' field is required.';
            $this->json_return($ajax);
        }
        if ($result['status_forward'] == $this->config->item('system_status_forwarded')) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_ALREADY');
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        $item['date_forwarded'] = $time;
        $item['user_forwarded'] = $user->user_id;
        // Main Table UPDATE
        Query_helper::update($this->config->item('table_bi_target_hosm'), $item, array("id =" . $item_id), FALSE);

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
        $user_info = System_helper::get_users_info($user_ids);

        //---------------- Basic Info ----------------
        $data = array();
        $data['item_head'] = $result;
        $data['item'][] = array
        (
            'label_1' => 'Target ' . $this->lang->line('LABEL_MONTH'),
            'value_1' => (DateTime::createFromFormat('!m', $result['month'])->format('F')) . ', ' . $result['year'],
            'label_2' => $this->lang->line('LABEL_AMOUNT_TARGET_TOTAL'),
            'value_2' => System_helper::get_string_amount($result['amount_target_total'])
        );
        $data['item'][] = array
        (
            'label_1' => $this->lang->line('LABEL_AMOUNT_TARGET_TOTAL') . ' ( In words )',
            'value_1' => Bi_helper::get_string_amount_inword($result['amount_target_total']),
        );
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

    private function check_validation()
    {
        $amount_target = $this->input->post('amount_target');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[month]', $this->lang->line('LABEL_MONTH'), 'required|trim|is_natural_no_zero');
        $this->form_validation->set_rules('item[year]', $this->lang->line('LABEL_YEAR'), 'required|trim|is_natural_no_zero');
        if ($this->form_validation->run() == FALSE) {
            $this->message = validation_errors();
            return false;
        }
        foreach ($amount_target as $amount) {
            if (!(trim($amount) > 0)) {
                $this->message = 'All ' . $this->lang->line('LABEL_AMOUNT_TARGET') . ' fields are required.';
                return false;
            }
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

        $this->db->from($this->config->item('table_bi_target_hosm') . ' target');
        $this->db->select('target.*, target.revision_count AS no_of_edit');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = target.user_created');
        $this->db->select('user_info.name requested_by');

        if ($user->user_group != $this->config->item('USER_GROUP_SUPER')) // If not SuperAdmin, Then user can only access own Item.
        {
            $this->db->where('target.user_created', $user->user_id);
        }
        $this->db->where('user_info.revision', 1);
        $this->db->order_by('target.id', 'DESC');
        $this->db->order_by('target.year');
        $this->db->order_by('target.month');

        $this->db->stop_cache();
    }
}
