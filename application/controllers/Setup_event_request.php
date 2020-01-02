<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_event_request extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $common_view_location;
    public $file_type;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->common_view_location='setup_event_request';
        $this->file_type='';
        $this->language_labels();
        $this->load->helper('event');
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_REVISION_COUNT']='Number of edit';
        $this->lang->language['LABEL_EVENT_ID']='Event ID';
        $this->lang->language['LABEL_TITLE']='Event Title';
        $this->lang->language['LABEL_EVENT_TYPE']='Event Type';
        $this->lang->language['LABEL_DATE_PUBLISH']='Event Publish Date';
        $this->lang->language['LABEL_EXPIRE_DAY']='Number Of Day As New';
        $this->lang->language['LABEL_REMAINING_DAY']='Number Of Remaining Day';
        $this->lang->language['LABEL_FILE_IMAGE']='File';
        $this->lang->language['LABEL_FILE_VIDEO']='Video';
        $this->lang->language['LABEL_LINK_URL']='External Link (Url)';
    }
    public function index($action="list",$id=0,$id1=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="list_all")
        {
            $this->system_list_all();
        }
        elseif($action=="get_items_all")
        {
            $this->system_get_items_all();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="forward")
        {
            $this->system_forward($id);
        }
        elseif ($action == "save_forward")
        {
            $this->system_save_forward();
        }
        elseif($action=="delete")
        {
            $this->system_delete($id);
        }
        elseif ($action == "save_delete")
        {
            $this->system_save_delete();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="add_image")
        {
            $this->system_add_file($id);
        }
        elseif($action=="edit_image")
        {
            $this->system_edit_file($id,$id1);
        }
        elseif($action=="save_file")
        {
            $this->system_save_file();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif($action=="set_preference_all")
        {
            $this->system_set_preference('list_all');
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='list')
        {
            $data['id']= 1;
            $data['date_publish']= 1;
            $data['expire_day']= 1;
            $data['remaining_day']= 1;
            $data['title']= 1;
            $data['description']= 1;
            $data['revision_count']= 1;
            $data['ordering']= 1;
            $data['status']= 1;
        }
        elseif($method=='list_all')
        {
            $data['id']= 1;
            $data['date_publish']= 1;
            $data['expire_day']= 1;
            $data['remaining_day']= 1;
            $data['title']= 1;
            $data['description']= 1;
            $data['revision_count']= 1;
            $data['ordering']= 1;
            $data['status']= 1;
            $data['status_forward']= 1;
            $data['status_approve']= 1;
        }
        elseif($method=='list_file')
        {
            $data['id']= 1;
            $data['file_image_video']= 1;
            $data['remarks']= 1;
            $data['ordering']= 1;
            $data['revision_count']= 1;
            $data['link_url']= 1;
            $data['status']= 1;
        }
        else
        {

        }

        return $data;
    }
    private function system_set_preference($method)
    {
        $user = User_helper::get_user();
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['preference_method_name']=$method;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_'.$method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Event Publish Request Pending List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
    {
        $time=time();
        $this->db->from($this->config->item('table_bi_setup_events').' item');
        $this->db->select('item.*');
       /* $this->db->join($this->config->item('table_pos_setup_event_types').' type','type.id=item.type_id','INNER');
        $this->db->select('type.name notice_type');*/
        $this->db->where('item.status !=',$this->config->item('system_status_delete'));
        $this->db->where('item.status_forward',$this->config->item('system_status_pending'));
        //$this->db->order_by('item.ordering','ASC');
        $this->db->order_by('item.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['expire_day']=Event_helper::get_expire_day($item['date_publish'],$item['expire_time']);
            $item['remaining_day']=Event_helper::get_expire_day_by_current_time($item['expire_time']);
            $item['date_publish']=$item['date_publish']?System_helper::display_date($item['date_publish']):'';
        }
        $this->json_return($items);
    }
    private function system_list_all()
    {
        $user = User_helper::get_user();
        $method='list_all';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Event Publish Request All List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_all",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_all/');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_all()
    {
        $current_records = $this->input->post('total_records');
        if(!$current_records)
        {
            $current_records=0;
        }
        $pagesize = $this->input->post('pagesize');
        if(!$pagesize)
        {
            $pagesize=100;
        }
        else
        {
            $pagesize=$pagesize*2;
        }
        $time=time();
        $this->db->from($this->config->item('table_bi_setup_events').' item');
        $this->db->select('item.*');
        /*$this->db->join($this->config->item('table_pos_setup_notice_types').' type','type.id=item.type_id','INNER');
        $this->db->select('type.name notice_type');*/
        $this->db->where('item.status !=',$this->config->item('system_status_delete'));
        //$this->db->order_by('item.ordering','ASC');
        $this->db->order_by('item.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['expire_day']=Event_helper::get_expire_day($item['date_publish'],$item['expire_time']);
            $item['remaining_day']=Event_helper::get_expire_day_by_current_time($item['expire_time']);
            $item['date_publish']=$item['date_publish']?System_helper::display_date($item['date_publish']):'';
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create New Event For Request";
            $data['item']['id']=0;
            //$data['item']['type_id']='';
            $data['item']['date_publish']=time();
            $data['item']['expire_day']=0;
            $data['item']['expire_day_remaining']=0;
            $data['item']['title']='';
            $data['item']['description']='';
            //$data['item']['user_group_ids'][]=array();
            //$data['item']['url_links']='';
            $data['item']['status']=$this->config->item('system_status_active');
            $data['item']['ordering']=99;
            //$data['notice_types']=Query_helper::get_info($this->config->item('table_pos_setup_notice_types'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            /*$user=User_helper::get_user();
            if($user->user_group==1)
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            }
            else
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('id !=1','status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            }*/

            //$data['user_group_ids']=array();
            //$data['urls']=array();
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            $time=time();
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $data['item']=Query_helper::get_info($this->config->item('table_bi_setup_events'),array('*'),array('id ='.$item_id),1,0,array('id ASC'));
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Event.';
                $this->json_return($ajax);
            }
            if($data['item']['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Event Already Forwarded.';
                $this->json_return($ajax);
            }
            //$item['expire_day']=Event_helper::get_expire_day($data['item']['date_publish'],$data['item']['expire_time']);
            $data['item']['expire_day_remaining']=Event_helper::get_expire_day_by_current_time($data['item']['expire_time']);

            //$data['user_group_ids']=explode(',',trim($data['item']['user_group_ids'],','));
            //$data['urls']=json_decode($data['item']['url_links'],true);

            //$data['notice_types']=Query_helper::get_info($this->config->item('table_pos_setup_notice_types'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            /*$user=User_helper::get_user();
            if($user->user_group==1)
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            }
            else
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('id !=1','status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            }*/

            $data['title']="Edit Event Publish :: ". $data['item']['id'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $user_groups=$this->input->post('user_groups');
        $urls=$this->input->post('urls');
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_bi_setup_events'),'*',array('id ='.$id),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Event.';
                $this->json_return($ajax);
            }
            if($result['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Event Already Forwarded.';
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        /*if(sizeof($user_groups)>0)
        {
            $item_head['user_group_ids']=','.implode(',',$user_groups).',';
        }
        else
        {
            $item_head['user_group_ids']='';
        }
        if(sizeof($urls)>0)
        {
            $item_head['url_links']=json_encode($urls);
        }
        else
        {
            $item_head['url_links']='';
        }*/
        if($item_head['date_publish'])
        {
            $item_head['date_publish']=System_helper::get_time($item_head['date_publish']);
            $item_head['expire_time']=$item_head['date_publish']+$item_head['expire_day']*3600*24;
        }

        //$item_head['expire_time']=$time+$item_head['expire_day']*3600*24;
        if($id>0)
        {
            //$data=array();
            //$item_head['expire_time']=System_helper::get_time($item_head['date_publish'])+$item_head['expire_day']*3600*24; // discussion with abid vi & shaiful vi.
            $item_head['date_updated'] = $time;
            $item_head['user_updated'] = $user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_bi_setup_events'),$item_head, array('id='.$id), false);
        }
        else
        {
            //$item_head['expire_time']=System_helper::get_time($item_head['date_publish'])+$item_head['expire_day']*3600*24;
            $item_head['date_created']=$time;
            $item_head['user_created']=$user->user_id;
            $item_head['revision_count']=1;
            Query_helper::add($this->config->item('table_bi_setup_events'),$item_head, false);
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add();
            }
            else
            {
                $this->system_list();
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function system_forward($id)
    {
        if(isset($this->permissions['action7'])&&($this->permissions['action7']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_bi_setup_events').' item');
            $this->db->select('item.*');
            $this->db->where('item.id',$item_id);
            $this->db->where('item.status !=',$this->config->item('system_status_delete'));
            $this->db->order_by('item.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Forward Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Event.';
                $this->json_return($ajax);
            }
            if($data['item']['status']==$this->config->item('system_status_inactive'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Event In-Active.';
                $this->json_return($ajax);
            }
            if($data['item']['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Event Already Forwarded.';
                $this->json_return($ajax);
            }
            $data['info_basic']=Event_helper::get_basic_info($data['item']);
            $data['title']="Event Forward :: ". $data['item']['id'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_forward()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        if($id>0)
        {
            if(!((isset($this->permissions['action7']) && ($this->permissions['action7']==1))))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if($item_head['status_forward']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Forward Field is required.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();

        $data['date_forwarded']=$time;
        $data['user_forwarded']=$user->user_id;
        $data['status_forward']=$item_head['status_forward'];
        $this->db->set('revision_count_forwarded', 'revision_count_forwarded+1', FALSE);
        Query_helper::update($this->config->item('table_bi_setup_events'),$data,array('id='.$id));

        $this->db->trans_complete();   //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function system_delete($id)
    {
        if(isset($this->permissions['action3'])&&($this->permissions['action3']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_bi_setup_events').' item');
            $this->db->select('item.*');
            /*$this->db->join($this->config->item('table_pos_setup_notice_types').' type','type.id=item.type_id','INNER');
            $this->db->select('type.name notice_type');*/
            $this->db->where('item.id',$item_id);
            $this->db->where('item.status !=',$this->config->item('system_status_delete'));
            $this->db->order_by('item.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Forward Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Event.';
                $this->json_return($ajax);
            }
            /*if($data['item']['status']==$this->config->item('system_status_inactive'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Notice In-Active.';
                $this->json_return($ajax);
            }
            if($data['item']['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Notice Already Forwarded.';
                $this->json_return($ajax);
            }*/

            $data['info_basic']=Event_helper::get_basic_info($data['item']);
            //$data['files']=Query_helper::get_info($this->config->item('table_pos_setup_notice_file_videos'),'*',array('notice_id='.$item_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));

            /*$data['user_group_ids']=explode(',',trim($data['item']['user_group_ids'],','));
            $data['urls']=json_decode($data['item']['url_links'],true);*/

            //$data['notice_types']=Query_helper::get_info($this->config->item('table_pos_setup_notice_types'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            /*$user=User_helper::get_user();
            if($user->user_group==1)
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            }
            else
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('id !=1','status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            }*/

            $data['title']="Event Delete :: ". $data['item']['id'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/delete",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/delete/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_delete()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        if($id>0)
        {
            if(!((isset($this->permissions['action3']) && ($this->permissions['action3']==1))))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            //if($item_head['status']!=$this->config->item('system_status_active') || $item_head['status']!=$this->config->item('system_status_inactive') || $item_head['status']!=$this->config->item('system_status_delete'))
            if(!($item_head['status']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Status Change Field is required.';
                $this->json_return($ajax);
            }
            if(!$item_head['reason_delete'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Status Change Reason Field is required.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();

        $data['date_updated']=$time;
        $data['user_updated']=$user->user_id;
        $data['reason_delete']=$item_head['reason_delete'];
        $data['status']=$item_head['status'];
        Query_helper::update($this->config->item('table_bi_setup_events'),$data,array('id='.$id));

        $this->db->trans_complete();   //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_bi_setup_events').' item');
            $this->db->select('item.*');
            /*$this->db->join($this->config->item('table_pos_setup_notice_types').' type','type.id=item.type_id','INNER');
            $this->db->select('type.name notice_type');*/
            $this->db->where('item.id',$item_id);
            $this->db->where('item.status !=',$this->config->item('system_status_delete'));
            $this->db->order_by('item.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Detail Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Event.';
                $this->json_return($ajax);
            }
            $data['info_basic']=Event_helper::get_basic_info($data['item']);
            //$data['files']=Query_helper::get_info($this->config->item('table_pos_setup_notice_file_videos'),'*',array('notice_id='.$item_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));

            $data['user_group_ids']=explode(',',trim($data['item']['user_group_ids'],','));
            $data['urls']=json_decode($data['item']['url_links'],true);

            //$data['notice_types']=Query_helper::get_info($this->config->item('table_pos_setup_notice_types'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $user=User_helper::get_user();
            if($user->user_group==1)
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            }
            else
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('id !=1','status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            }

            /* data show hide parameter*/
            $data['user_groups_show']=true;
            /*list action button */
            //$data['action_buttons'][]=array();
            $data['action_buttons'][]=array(
                'label'=>'All List',
                'href'=>site_url($this->controller_url.'/index/list_all')
            );
            $data['action_buttons'][]=array(
                'label'=>'Pending List',
                'href'=>site_url($this->controller_url)
            );
            $data['title']="Event Details :: ". $data['item']['id'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->common_view_location."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
            $this->json_return($ajax);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_add_file($id)
    {
        if($id>0)
        {
            $notice_id=$id;
        }
        else
        {
            $notice_id=$this->input->post('id');
        }
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            $data['title']="Create New Event File Upload";
            $data['item']['id']=$notice_id;
            /*$data['item']['file_name']='';
            $data['item']['file_location']='images/no_image.jpg';
            $data['item']['remarks_photo']='';*/

            $this->db->from($this->config->item('table_bi_setup_events').' item');
            $this->db->select('item.*');
            $this->db->where('item.id',$notice_id);
            $this->db->where('item.status !=',$this->config->item('system_status_delete'));
            $this->db->where('item.status_forward',$this->config->item('system_status_pending'));
            $this->db->order_by('item.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if($data['item']['status']==$this->config->item('system_status_inactive'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Event In-Active.';
                $this->json_return($ajax);
            }
            if($data['item']['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Event Already Forwarded.';
                $this->json_return($ajax);
            }
            $data['info_basic']=Event_helper::get_basic_info($data['item']);

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_file",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_image/'.$notice_id.'/');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit_file($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_bi_setup_events').' item');
            $this->db->select('item.*');
            $this->db->where('item.id',$item_id);
            $this->db->where('item.status !=',$this->config->item('system_status_delete'));
            $this->db->where('item.status_forward',$this->config->item('system_status_pending'));
            $this->db->order_by('item.id','DESC');
            $item=$this->db->get()->row_array();
            if($item['status']==$this->config->item('system_status_inactive'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Notice In-Active.';
                $this->json_return($ajax);
            }
            if($item['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Notice Already Forwarded.';
                $this->json_return($ajax);
            }
            $data['info_basic']=Event_helper::get_basic_info($item);
            $data['item']=Query_helper::get_info($this->config->item('table_pos_setup_notice_file_videos'),array('*'),array('id ='.$item_id),1,0,array('id ASC'));
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $data['title']="Edit Notice File :: ". $data['item']['id'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_file",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_image/'.$item_id.'/');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_file()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!($id>0))
        {
            if(!$this->check_validation_file())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->json_return($ajax);
            }
        }
        $result=Query_helper::get_info($this->config->item('table_bi_setup_events'),'*',array('id ='.$id),1);
        if(!$result)
        {
            System_helper::invalid_try('Update File Non Exists',$id);
            $ajax['status']=false;
            $ajax['system_message']='Invalid Notice.';
            $this->json_return($ajax);
        }
        if($result['status']==$this->config->item('system_status_inactive'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Notice In-Active.';
            $this->json_return($ajax);
        }
        if($result['status_forward']==$this->config->item('system_status_forwarded'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Notice Already Forwarded.';
            $this->json_return($ajax);
        }

        $data=array();
        $uploaded_files = array();
        $path = 'images/event/' . $id;
        $uploaded_files = System_helper::upload_file($path,'jpg|jpeg|png|bmp',10240);

        if(array_key_exists('file_name',$uploaded_files))
        {
            if($uploaded_files['file_name']['status'])
            {
                $data['file_name']=$uploaded_files['file_name']['info']['file_name'];
                $data['file_location']=$path.'/'.$uploaded_files['file_name']['info']['file_name'];
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$uploaded_files['file_name']['message'];
                $this->json_return($ajax);
                die();
            }
        }


        $this->db->trans_start();  //DB Transaction Handle START

        $data['remarks_photo']=$item_head['remarks_photo'];
        $data['date_file_uploaded'] = $time;
        $data['user_file_uploaded'] = $user->user_id;
        Query_helper::update($this->config->item('table_bi_setup_events'),$data, array('id='.$id), false);

        $this->db->trans_complete();   //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add_file($id);
            }
            else
            {
                $this->system_list();
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function check_validation()
    {
        $id = $this->input->post('id');
        $item_head = $this->input->post('item');
        $time_today=System_helper::get_time(System_helper::display_date(time()));
        $time_publish = System_helper::get_time($item_head['date_publish']);
        if (!$time_publish)
        {
            $this->message = 'The ' . $this->lang->line('LABEL_DATE_PUBLISH') . ' From field is required.';
            return false;
        }
        if(!($id>0))
        {
            if($time_publish<$time_today)
            {
                $this->message = 'Publish date will be greater than current.';
                return false;
            }
        }

        $this->load->library('form_validation');
        //$this->form_validation->set_rules('item[type_id]',$this->lang->line('LABEL_NOTICE_TYPE'),'required');
        $this->form_validation->set_rules('item[title]',$this->lang->line('LABEL_TITLE'),'required');
        $this->form_validation->set_rules('item[ordering]',$this->lang->line('LABEL_ORDER'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_file()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id','ID','required');
        if (!($_FILES['file_name']['name']))
        {
            $this->form_validation->set_rules('file_name',$this->lang->line('LABEL_FILE_IMAGE'),'required');
        }
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
