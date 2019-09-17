<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_variety_cultivation_period extends Root_Controller
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
        $this->common_view_location = 'setup_variety_cultivation_period';
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
        $this->lang->language['LABEL_CULTIVATION_PERIOD']='Cultivation Period';
        $this->lang->language['LABEL_USER_CREATED']='Creation By';
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
        if($method == 'list')
        {
            $data['id'] = 1;
            $data['date_created'] = 1;
            $data['user_created'] = 1;
        }
        else if ($method == 'list_all')
        {
            $data['id'] = 1;
            $data['date_created'] = 1;
            $data['user_created'] = 1;
            $data['revision'] = 1;
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
            $data['title'] = "Cultivation Period Setup List";
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
        $this->db->from($this->config->item('table_bi_setup_variety_cultivation_period') . ' item');
        $this->db->select('item.*');
        $this->db->where('item.status', $this->config->item('system_status_active'));
        $this->db->where('item.revision', 1);
        $this->db->order_by('item.id','DESC');
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
            $data['title'] = "Cultivation Period Setup All List";
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
        $this->db->from($this->config->item('table_bi_setup_variety_cultivation_period') . ' item');
        $this->db->select('item.*');
        $this->db->where('item.status', $this->config->item('system_status_active'));
        $this->db->order_by('item.id','DESC');
        $items = $this->db->get()->result_array();
        $this->json_return($items);
    }
    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data = array();

            $data['item']=Query_helper::get_info($this->config->item('table_bi_setup_variety_cultivation_period'),'*',array('revision = 1'),1);

            $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' type');
            $this->db->select('type.id crop_type_id, type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');

            $this->db->where('type.status', $this->config->item('system_status_active'));
            $this->db->where('crop.status', $this->config->item('system_status_active'));

            $this->db->order_by('crop.ordering','ASC');
            $this->db->order_by('type.ordering','ASC');
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

            $data['title'] = "New Cultivation Period Setup ";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add", $data, true));
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
    private function system_save()
    {
        $id = $this->input->post('id');
        $user = User_helper::get_user();
        $time = time();
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');

        if ($id > 0) //EDIT
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            //$result=Query_helper::get_info($this->config->item('table_bi_variety_cultivation_period_request'),'*',array('id ='.$id),1);
            $result=Query_helper::get_info($this->config->item('table_bi_setup_variety_cultivation_period'),'*',array('id ='.$id),1);
            if(!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'ID Not Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($result['revision']!=1)
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Revision not match.';
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

        /*$result=Query_helper::get_info($this->config->item('table_bi_variety_cultivation_period_request'),'*',array('id ='.$id),1);
        $cultivation_period_setup_old=array();
        if(isset($result['cultivation_period']) && $result['cultivation_period'])
        {
            $cultivation_period_setup_old=json_decode($result['cultivation_period'],TRUE);
        }*/

        // Old item
        /*$this->db->from($this->config->item('table_bi_variety_cultivation_period'));
        $this->db->select('*');
        $results = $this->db->get()->result_array();
        $cultivation_period_old=array();
        foreach ($results as $result)
        {
            $cultivation_period_old[$result['upazilla_id']][$result['type_id']] = $result;
        }*/

        $cultivation_period_array=array();
        foreach($items as $type_id=>$info)
        {
            if($info['date_start'])
            {
                $date_start=Bi_helper::cultivation_date_sql($info['date_start']);
                $date_end=Bi_helper::cultivation_date_sql($info['date_end']);
                if(isset($cultivation_period_old[$type_id]))
                {
                    if(!(($cultivation_period_old[$type_id]['date_start']==$date_start) && ($cultivation_period_old[$type_id]['date_end']==$date_end)))
                    {
                        $cultivation_period_array[$type_id]=Bi_helper::cultivation_date_sql($info['date_start']).'~'.Bi_helper::cultivation_date_sql($info['date_end']);
                    }
                }
                else
                {
                    $cultivation_period_array[$type_id]=Bi_helper::cultivation_date_sql($info['date_start']).'~'.Bi_helper::cultivation_date_sql($info['date_end']);
                }
            }
        }

        $item_head['cultivation_period']=json_encode($cultivation_period_array);

        //need to add date validation
        /*if($invalid_date)
        {
            $ajax['status'] = false;
            $ajax['system_message'] = "End date must greater than start date.";
            $this->json_return($ajax);
        }*/

        $this->db->trans_start(); //DB Transaction Handle START

        $this->db->set('revision', 'revision+1', FALSE);
        Query_helper::update($this->config->item('table_bi_setup_variety_cultivation_period'), $item_head, array(), FALSE);

        $item_head['date_created'] = $time;
        $item_head['user_created'] = $user->user_id;
        $item_head['revision'] = 1;
        Query_helper::add($this->config->item('table_bi_setup_variety_cultivation_period'), $item_head, FALSE);

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
            $this->db->from($this->config->item('table_bi_variety_cultivation_period_request') . ' item');
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

            $data['info_basic']=Bi_helper::get_basic_info($data['item']);

            $this->db->from($this->config->item('table_bi_variety_cultivation_period'));
            $this->db->select('*');
            $this->db->where('upazilla_id', $data['item']['upazilla_id']);
            $results = $this->db->get()->result_array();
            foreach ($results as $result)
            {
                $data['cultivation_period_old'][$result['type_id']] = $result;
            }

            $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' type');
            $this->db->select('type.id crop_type_id, type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');

            $this->db->where('type.status', $this->config->item('system_status_active'));
            $this->db->where('crop.status', $this->config->item('system_status_active'));

            $this->db->order_by('crop.ordering','ASC');
            $this->db->order_by('type.ordering','ASC');
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

            $data['title'] = "Cultivation Period Details ( Upazilla Wise )";
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
        /*$id=$this->input->post('id');
        $item = $this->input->post('item');
        if (!($id > 0) && !($item['upazilla_id'] > 0))
        {
            $this->message = $this->lang->line('LABEL_UPAZILLA_NAME') . ' field is required.';
            return false;
        }*/
        return true;
    }

}
