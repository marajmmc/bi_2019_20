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
        $this->lang->language['LABEL_REVISION']='Revision';
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
        if($method == 'list')
        {
            $data['id'] = 1;
            $data['crop_name'] = 1;
            $data['crop_type_name'] = 1;
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
        $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.user_id = item.user_created AND ui.revision=1','INNER');
        $this->db->select('ui.name user_created_full_name');
        $this->db->where('item.status', $this->config->item('system_status_active'));
        $this->db->where('item.revision', 1);
        $this->db->order_by('item.id','DESC');
        $results = $this->db->get()->result_array();
        $info=array();
        foreach($results as $result)
        {
            $info[$result['crop_type_id']]=$result;
        }

        $this->db->from($this->config->item('table_login_setup_classification_crop_types').' ct');
        $this->db->select('ct.id,ct.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = ct.crop_id','INNER');
        $this->db->select('crop.name crop_name');
        $this->db->where('ct.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('ct.ordering','ASC');
        $this->db->order_by('ct.id','ASC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_created']="";
            $item['user_created']="";
            if(isset($info[$item['id']]))
            {
                $item['date_created']=System_helper::display_date($info[$item['id']]['date_created']);
                $item['user_created']=$info[$item['id']]['user_created_full_name'];
            }
        }
        $this->json_return($items);
    }
    private function system_edit($id)
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $data = array();
            $this->db->from($this->config->item('table_login_setup_classification_crop_types').' ct');
            $this->db->select('ct.id,ct.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = ct.crop_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->where('ct.status !=',$this->config->item('system_status_delete'));
            $this->db->where('ct.id',$item_id);
            $this->db->order_by('crop.ordering','ASC');
            $this->db->order_by('crop.id','ASC');
            $this->db->order_by('ct.ordering','ASC');
            $this->db->order_by('ct.id','ASC');
            $data['item_info']=$this->db->get()->row_array();

            $data['item']=Query_helper::get_info($this->config->item('table_bi_setup_variety_cultivation_period'),'*',array('crop_type_id ='.$item_id,'revision = 1'),1);
            $data['date_start_old']=Bi_helper::cultivation_date_display($data['item']['date_start']);
            $data['date_end_old']=Bi_helper::cultivation_date_display($data['item']['date_end']);

            $this->db->from($this->config->item('table_bi_setup_variety_cultivation_period').' cp');
            $this->db->select('cp.*');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = cp.crop_type_id','INNER');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.user_id = cp.user_created AND ui.revision=1','INNER');
            $this->db->select('ui.name user_created_full_name');
            $this->db->where('cp.crop_type_id',$item_id);
            $this->db->where('cp.revision > ',1);
            $this->db->order_by('cp.revision','ASC');
            $data['histories']=$this->db->get()->result_array();


            $data['title'] = "New Cultivation Period Setup ";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/'.$item_id);
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
        $type_id=$id;
        $date_start_old='';
        $date_end_old='';

        if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if ($id > 0) //EDIT
        {
            $result=Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'),'*',array('id ='.$id),1);
            if(!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'ID Not Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_bi_setup_variety_cultivation_period'),'*',array('crop_type_id ='.$id,'revision = 1'),1);
            $date_start_old=isset($result['date_start'])?$result['date_start']:'';
            $date_end_old=isset($result['date_end'])?$result['date_end']:'';
        }

        //Validation Checking
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        // Old item
        $this->db->from($this->config->item('table_bi_variety_cultivation_period'));
        $this->db->select('*');
        $results = $this->db->get()->result_array();
        $cultivation_period_old=array();
        foreach ($results as $result)
        {
            $cultivation_period_old[$result['upazilla_id']][$result['type_id']] = $result;
        }

        $date_start=System_helper::get_time($item_head['date_start'].'-1970');
        $date_end=System_helper::get_time($item_head['date_end'].'-1970');
        if($date_end<$date_start)
        {
            $date_end=System_helper::get_time($item_head['date_end'].'-1971');
        }
        if($date_end!=0)
        {
            $date_end+=24*3600-1;
        }

        $upazillas=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id','name'),array('status ="'.$this->config->item('system_status_active').'"'));
        $cultivation_period_upazilla_insert=array();
        $cultivation_period_upazilla_update=array();
        foreach($upazillas as $upazilla)
        {
            if(isset($cultivation_period_old[$upazilla['id']][$type_id]))
            {
                if($cultivation_period_old[$upazilla['id']][$type_id]['status_change']==0)
                {
                    $cultivation_period_upazilla_update[$upazilla['id']][$type_id]['cultivation_period_start_date']=$date_start;
                    $cultivation_period_upazilla_update[$upazilla['id']][$type_id]['cultivation_period_end_date']=$date_end;
                }
            }
            else
            {
                $cultivation_period_upazilla_insert[]=array(
                    'type_id'=> $type_id,
                    'upazilla_id'=> $upazilla['id'],
                    'date_start'=> $date_start,
                    'date_end'=> $date_end,
                    'revision_count'=> 1,
                    'date_updated'=> $time,
                    'user_updated'=> $user->user_id
                );
            }
        }

        $this->db->trans_start(); //DB Transaction Handle START

        // revision increase
        $item=array();
        $this->db->set('revision', 'revision+1', FALSE);
        Query_helper::update($this->config->item('table_bi_setup_variety_cultivation_period'), $item, array('crop_type_id='.$type_id), FALSE);

        //update setup table
        $item=array();
        $item['crop_type_id']=$type_id;
        $item['date_start_old']=$date_start_old;
        $item['date_end_old']=$date_end_old;
        $item['date_start']=$date_start;
        $item['date_end']=$date_end;
        $item['date_created'] = $time;
        $item['user_created'] = $user->user_id;
        $item['revision'] = 1;
        Query_helper::add($this->config->item('table_bi_setup_variety_cultivation_period'), $item, FALSE);

        // update upazilla wise cultivation period
        if($cultivation_period_upazilla_update)
        {
            foreach($cultivation_period_upazilla_update as $upazilla_id=>$type_info)
            {
                foreach($type_info as $type_id=>$info)
                {
                    $data=array();
                    $data['type_id'] = $type_id;
                    $data['upazilla_id'] = $upazilla_id;
                    $data['date_start'] = $info['cultivation_period_start_date'];
                    $data['date_end'] = $info['cultivation_period_end_date'];
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    $this->db->set('revision_count', 'revision_count+1', FALSE);
                    Query_helper::update($this->config->item('table_bi_variety_cultivation_period'), $data, array('type_id='.$type_id,'upazilla_id='.$upazilla_id), FALSE);
                }
            }
        }
        // batch insert cultivation period table
        if(sizeof($cultivation_period_upazilla_insert)>0)
        {
            $this->db->insert_batch($this->config->item('table_bi_variety_cultivation_period'), $cultivation_period_upazilla_insert);
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
            $this->db->from($this->config->item('table_login_setup_classification_crop_types').' ct');
            $this->db->select('ct.id,ct.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = ct.crop_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->where('ct.status !=',$this->config->item('system_status_delete'));
            $this->db->where('ct.id',$item_id);
            $this->db->order_by('crop.ordering','ASC');
            $this->db->order_by('crop.id','ASC');
            $this->db->order_by('ct.ordering','ASC');
            $this->db->order_by('ct.id','ASC');
            $data['item_info']=$this->db->get()->row_array();

            $this->db->from($this->config->item('table_bi_setup_variety_cultivation_period').' cp');
            $this->db->select('cp.*');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = cp.crop_type_id','INNER');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.user_id = cp.user_created AND ui.revision=1','INNER');
            $this->db->select('ui.name user_created_full_name');
            $this->db->where('cp.crop_type_id',$item_id);
            $this->db->order_by('cp.revision','ASC');
            $data['histories']=$this->db->get()->result_array();

            $data['title'] = "Cultivation Period Details";
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
        $item = $this->input->post('item');
        if(!($item['date_start'] || $item['date_end']))
        {
            $this->message = 'Start & End date field is required.';
            return false;
        }
        return true;
    }

}
