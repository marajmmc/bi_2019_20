<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_variety_major_competitor extends Root_Controller
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
        $this->common_view_location = 'setup_variety_major_competitor';
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
        $this->lang->language['LABEL_VARIETY_ARM'] = 'ARM Variety';
        $this->lang->language['LABEL_VARIETY_COMPETITOR_EXISTING'] = 'Existing Major Competitor Variety';
        $this->lang->language['LABEL_VARIETY_COMPETITOR_EDITED'] = 'Edited Major Competitor Variety';
        $this->lang->language['LABEL_USER_CREATED'] = 'Created By';
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
        $data['crop_name'] = 1;
        $data['crop_type_name'] = 1;
        $data['date_created'] = 1;
        $data['user_created'] = 1;
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
            $data['title'] = "Major Competitor Variety Setup List";
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
        $this->db->from($this->config->item('table_bi_setup_variety_major_competitor') . ' mc');
        $this->db->select('mc.*');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user', 'user.user_id = mc.user_created');
        $this->db->select('user.name user_full_name');

        $this->db->where('mc.revision', 1);
        $results = $this->db->get()->result_array();
        $major_competitors = array();
        foreach ($results as $result)
        {
            $major_competitors[$result['crop_type_id']] = $result;
        }

        $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' ct');
        $this->db->select('ct.id, ct.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = ct.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->where('ct.status !=', $this->config->item('system_status_delete'));

        $this->db->order_by('crop.ordering', 'ASC');
        $this->db->order_by('crop.id', 'ASC');
        $this->db->order_by('ct.ordering', 'ASC');
        $this->db->order_by('ct.id', 'ASC');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['date_created'] = '';
            $item['user_created'] = '';
            if (isset($major_competitors[$item['id']]))
            {
                $item['date_created'] = System_helper::display_date($major_competitors[$item['id']]['date_created']);
                $item['user_created'] = $major_competitors[$item['id']]['user_full_name'];
            }
        }

        $this->json_return($items);
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
        {
            if (($this->input->post('id')))
            {
                $item_id = $this->input->post('id');
            }
            else
            {
                $item_id = $id;
            }
            $data = array();
            $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' ct');
            $this->db->select('ct.id crop_type_id, ct.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = ct.crop_id', 'INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('ct.id', $item_id);
            $this->db->where('ct.status', $this->config->item('system_status_active'));
            $data['item_info'] = $this->db->get()->row_array();

            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' v');
            $this->db->select('v.id, v.name, v.whose, v.crop_type_id');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
            $this->db->select('type.id crop_type_id, type.name crop_type_name, type.crop_id');
            $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = v.competitor_id', 'LEFT');
            $this->db->select('competitor.name competitor_name');
            $this->db->where('type.crop_id', $data['item_info']['crop_id']);
            $this->db->where('v.whose !=', 'Upcoming');
            $this->db->where('v.status', $this->config->item('system_status_active'));
            $this->db->order_by('type.ordering', 'ASC');
            $this->db->order_by('v.ordering', 'ASC');
            $results = $this->db->get()->result_array();

            $data['variety_arm'] = array();
            $data['variety_competitor'] = array();
            foreach ($results as $result)
            {
                if ($result['whose'] == 'ARM')
                {
                    if ($result['crop_type_id'] == $item_id)
                    {
                        $data['variety_arm'][] = $result;
                    }
                }
                elseif ($result['whose'] == 'Competitor')
                {
                    $data['variety_competitor'][$result['crop_type_id']][$result['id']] = $result;
                }
            }

            $this->db->from($this->config->item('table_bi_setup_variety_major_competitor') . ' mc');
            $this->db->select('mc.*');
            $this->db->where('mc.crop_type_id', $data['item_info']['crop_type_id']);
            $this->db->where('mc.revision', 1);
            $data['variety_competitor_old'] = $this->db->get()->row_array();
            if (!$data['variety_competitor_old'])
            {
                $data['item_info']['id'] = 0;
            }
            else
            {
                $data['item_info']['id'] = $data['variety_competitor_old']['id'];
            }

            $data['histories'] = array();

            $data['title'] = "New Major Competitor Variety Setup";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/edit", $data, true));
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
        $user = User_helper::get_user();
        $time = time();
        $id = $this->input->post('id');
        $type_id = $this->input->post('crop_type_id');
        $items = $this->input->post('items');

        if (!((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || ((isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if ($id > 0) //EDIT
        {
            $result = Query_helper::get_info($this->config->item('table_bi_setup_variety_major_competitor'), '*', array('crop_type_id =' . $type_id, 'revision = 1'), 1);
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
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
        // revision increase
        $item = array();
        $this->db->set('revision', 'revision+1', FALSE);
        Query_helper::update($this->config->item('table_bi_setup_variety_major_competitor'), $item, array('crop_type_id=' . $type_id), FALSE);
        // update setup table
        $item = array();
        $item['crop_type_id'] = $type_id;
        $item['competitor_varieties'] = json_encode($items);
        $item['revision'] = 1;
        $item['date_created'] = $time;
        $item['user_created'] = $user->user_id;
        Query_helper::add($this->config->item('table_bi_setup_variety_major_competitor'), $item, FALSE);
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
            $this->db->from($this->config->item('table_bi_setup_variety_major_competitor') . ' mc');
            $this->db->select('mc.*');
            $this->db->where('mc.crop_type_id', $item_id);
            $this->db->order_by('mc.revision');
            $data['histories'] = $this->db->get()->result_array();

            $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' ct');
            $this->db->select('ct.id crop_type_id, ct.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = ct.crop_id', 'INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('ct.id', $item_id);
            $this->db->where('ct.status', $this->config->item('system_status_active'));
            $data['item_info'] = $this->db->get()->row_array();

            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' v');
            $this->db->select('v.id, v.name, v.whose, v.crop_type_id');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
            $this->db->select('type.id crop_type_id, type.name crop_type_name, type.crop_id');
            $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = v.competitor_id', 'LEFT');
            $this->db->select('competitor.name competitor_name');
            $this->db->where('type.crop_id', $data['item_info']['crop_id']);
            $this->db->where('v.whose !=', 'Upcoming');
            $this->db->where('v.status', $this->config->item('system_status_active'));
            $this->db->order_by('type.ordering', 'ASC');
            $this->db->order_by('v.ordering', 'ASC');
            $results = $this->db->get()->result_array();

            $data['variety_arm'] = array();
            $data['variety_competitor'] = array();
            foreach ($results as $result)
            {
                if ($result['whose'] == 'ARM')
                {
                    if ($result['crop_type_id'] == $item_id)
                    {
                        $data['variety_arm'][] = $result;
                    }
                }
                elseif ($result['whose'] == 'Competitor')
                {
                    $data['variety_competitor'][$result['crop_type_id']][$result['id']] = $result;
                }
            }

            $data['title'] = "Major Competitor Variety Details";
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

    private function check_validation()
    {
        $items = $this->input->post('items');
        if (!$items)
        {
            $this->message = 'Atleast 1 Competitor variety need to save.';
            return false;
        }
        return true;
    }
}
