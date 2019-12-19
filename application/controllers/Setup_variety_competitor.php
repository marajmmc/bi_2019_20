<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_variety_competitor extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    // public $locations;
    // public $common_view_location;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        // $this->common_view_location = 'setup_competitor_variety';
        /*$this->locations = User_helper::get_locations();
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }*/
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list") // Competitor Variety List
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "list_arm") // ARM Variety List
        {
            $this->system_list_arm();
        }
        elseif ($action == "get_items_arm")
        {
            $this->system_get_items_arm();
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
        elseif ($action == "details_arm")
        {
            $this->system_details_arm($id);
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_arm")
        {
            $this->system_set_preference('list_arm');
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
        $data['variety_name'] = 1;
        if ($method != 'list_arm')
        {
            $data['competitor_name'] = 1;
        }
        $data['crop_name'] = 1;
        $data['crop_type_name'] = 1;
        $data['hybrid'] = 1;
        $data['status'] = 1;
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
            $data['title'] = "Competitor Variety List";
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
        $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' cv');
        $this->db->select('cv.*, cv.name variety_name');

        $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = cv.competitor_id');
        $this->db->select('competitor.name competitor_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = cv.crop_type_id');
        $this->db->select('crop_types.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'LEFT');
        $this->db->select('crops.id crop_id, crops.name crop_name');

        $this->db->join($this->config->item('table_login_setup_classification_hybrid') . ' hybrid', 'hybrid.id = cv.hybrid');
        $this->db->select('hybrid.name hybrid');

        $this->db->where('cv.whose', 'Competitor');
        $this->db->order_by('cv.id');
        $items = $this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_list_arm()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list_arm';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "ARM Variety List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_arm", $data, true));
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

    private function system_get_items_arm()
    {
        $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' v');
        $this->db->select('v.*, v.name variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = v.crop_type_id');
        $this->db->select('crop_types.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'LEFT');
        $this->db->select('crops.id crop_id, crops.name crop_name');

        $this->db->join($this->config->item('table_login_setup_classification_hybrid') . ' hybrid', 'hybrid.id = v.hybrid');
        $this->db->select('hybrid.name hybrid');

        $this->db->where('v.whose', 'ARM');
        $this->db->order_by('v.id');
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
                'competitor_id' => 0,
                'crop_type_id' => 0,
                'variety_name' => '',
                'hybrid' => '',
                'ordering' => 99,
                'status' => ''
            );

            $data['competitors'] = Query_helper::get_info($this->config->item('table_login_basic_setup_competitor'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('name ASC'));
            $data['hybrids'] = Query_helper::get_info($this->config->item('table_login_setup_classification_hybrid'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'));

            $data['title'] = "Add New Competitor Variety";
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
            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' cv');
            $this->db->select('cv.*, cv.name variety_name');

            $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = cv.competitor_id');
            $this->db->select('competitor.name competitor_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = cv.crop_type_id');
            $this->db->select('crop_types.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'LEFT');
            $this->db->select('crops.id crop_id, crops.name crop_name');

            $this->db->where('cv.id', $item_id);
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['competitors'] = Query_helper::get_info($this->config->item('table_login_basic_setup_competitor'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('name ASC'));
            $data['hybrids'] = Query_helper::get_info($this->config->item('table_login_setup_classification_hybrid'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'));

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

            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' cv');
            $this->db->select('cv.*, cv.name variety_name');

            $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = cv.competitor_id');
            $this->db->select('competitor.name competitor_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = cv.crop_type_id');
            $this->db->select('crop_types.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'LEFT');
            $this->db->select('crops.id crop_id, crops.name crop_name');

            $this->db->where('cv.id', $item_id);
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

        $this->db->trans_start(); //DB Transaction Handle START
        if ($item_id > 0) // Revision Update if EDIT
        { //Update
            $item['date_updated'] = $time;
            $item['user_updated'] = $user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_bi_setup_competitor_variety'), $item, array("id =" . $item_id), FALSE);
        }
        else
        { //Insert
            $item['whose'] = 'Competitor';
            $item['status'] = $this->config->item('system_status_active');
            $item['revision_count'] = 1;
            $item['date_created'] = $time;
            $item['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_bi_setup_competitor_variety'), $item, FALSE);
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
            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' cv');
            $this->db->select('cv.*, cv.name variety_name');

            $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = cv.competitor_id');
            $this->db->select('competitor.name competitor_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = cv.crop_type_id');
            $this->db->select('crop_types.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'LEFT');
            $this->db->select('crops.id crop_id, crops.name crop_name');

            $this->db->join($this->config->item('table_login_setup_classification_hybrid') . ' hybrid', 'hybrid.id = cv.hybrid');
            $this->db->select('hybrid.name hybrid');

            $this->db->where('cv.id', $item_id);
            $this->db->where('cv.whose', 'Competitor');
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

    private function system_details_arm($id) // ARM Variety Details
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
            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' v');
            $this->db->select('v.*, v.name variety_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = v.crop_type_id');
            $this->db->select('crop_types.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'LEFT');
            $this->db->select('crops.id crop_id, crops.name crop_name');

            $this->db->join($this->config->item('table_login_setup_classification_hybrid') . ' hybrid', 'hybrid.id = v.hybrid');
            $this->db->select('hybrid.name hybrid');

            $this->db->where('v.id', $item_id);
            $this->db->where('v.whose', 'ARM');
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
                'header' => 'ARM Variety Information',
                'div_id' => 'basic_info',
                'collapse' => 'in',
                'data' => array(
                    array(
                        'label_1' => $this->lang->line('LABEL_VARIETY_NAME'),
                        'value_1' => $result['variety_name']
                    ),
                    array(
                        'label_1' => $this->lang->line('LABEL_CROP_NAME'),
                        'value_1' => $result['crop_name'],
                        'label_2' => $this->lang->line('LABEL_CROP_TYPE_NAME'),
                        'value_2' => $result['crop_type_name'],
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
            $data['title'] = "ARM Variety Details ( ID:" . $item_id . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details_arm/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('item[competitor_id]', $this->lang->line('LABEL_COMPETITOR_NAME'), 'required');
        $this->form_validation->set_rules('item[crop_type_id]', $this->lang->line('LABEL_CROP_TYPE_NAME'), 'required');
        $this->form_validation->set_rules('item[name]', $this->lang->line('LABEL_VARIETY_NAME'), 'required');
        $this->form_validation->set_rules('item[hybrid]', $this->lang->line('LABEL_HYBRID'), 'required');
        $this->form_validation->set_rules('item[ordering]', $this->lang->line('LABEL_ORDER'), 'required');
        $this->form_validation->set_rules('item[status]', $this->lang->line('LABEL_STATUS'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
}
