<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Variety_cultivation_period_approve extends Root_Controller
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
        $this->common_view_location = 'variety_cultivation_period_request';
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
        elseif ($action == "get_cultivation_period_info")
        {
            $this->system_get_cultivation_period_info();
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
            $data['upazilla_name'] = 1;
            $data['district_name'] = 1;
            $data['territory_name'] = 1;
            $data['zone_name'] = 1;
            $data['division_name'] = 1;
            $data['number_of_edit'] = 1;
        }
        else if ($method == 'list_all')
        {
            $data['id'] = 1;
            $data['upazilla_name'] = 1;
            $data['district_name'] = 1;
            $data['territory_name'] = 1;
            $data['zone_name'] = 1;
            $data['division_name'] = 1;
            $data['number_of_edit'] = 1;
            $data['status'] = 1;
            $data['status_forward'] = 1;
            $data['status_approve'] = 1;
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
            $data['title'] = $this->lang->line('LABEL_UPAZILLA_NAME') . " Wise Cultivation Period List";
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
        $this->db->from($this->config->item('table_bi_variety_cultivation_period_request') . ' item');
        $this->db->select('item.*, revision_count number_of_edit');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazillas', 'upazillas.id = item.upazilla_id');
        $this->db->select('upazillas.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = upazillas.district_id');
        $this->db->select('districts.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territories', 'territories.id = districts.territory_id', 'INNER');
        $this->db->select('territories.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zones', 'zones.id = territories.zone_id', 'INNER');
        $this->db->select('zones.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' divisions', 'divisions.id = zones.division_id', 'INNER');
        $this->db->select('divisions.name division_name');

        $this->db->where('item.status', $this->config->item('system_status_active'));
        $this->db->where('item.status_forward', $this->config->item('system_status_forwarded'));
        $this->db->where('item.status_approve', $this->config->item('system_status_pending'));

        $this->db->order_by('item.id','DESC');
        if($this->locations['division_id']>0)
        {
            $this->db->where('divisions.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zones.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('territories.id',$this->locations['territory_id']);
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('districts.id',$this->locations['district_id']);
                        if($this->locations['upazilla_id']>0)
                        {
                            $this->db->where('upazillas.id',$this->locations['upazilla_id']);
                        }
                    }
                }
            }
        }
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
            $data['title'] = $this->lang->line('LABEL_UPAZILLA_NAME') . " Wise Cultivation Period All List";
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
        $this->db->from($this->config->item('table_bi_variety_cultivation_period_request') . ' item');
        $this->db->select('item.*, revision_count number_of_edit');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazillas', 'upazillas.id = item.upazilla_id');
        $this->db->select('upazillas.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = upazillas.district_id');
        $this->db->select('districts.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territories', 'territories.id = districts.territory_id', 'INNER');
        $this->db->select('territories.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zones', 'zones.id = territories.zone_id', 'INNER');
        $this->db->select('zones.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' divisions', 'divisions.id = zones.division_id', 'INNER');
        $this->db->select('divisions.name division_name');

        $this->db->order_by('item.id','DESC');
        if($this->locations['division_id']>0)
        {
            $this->db->where('divisions.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zones.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('territories.id',$this->locations['territory_id']);
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('districts.id',$this->locations['district_id']);
                        if($this->locations['upazilla_id']>0)
                        {
                            $this->db->where('upazillas.id',$this->locations['upazilla_id']);
                        }
                    }
                }
            }
        }
        $items = $this->db->get()->result_array();
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
            if ($data['item']['status']==$this->config->item('system_status_rejected'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Rejected.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_forward']==$this->config->item('system_status_pending'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Not Yet Forwarded.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Approved.';
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

            $data['title'] = "Approve Cultivation Period (Upazilla Wise)";
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
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $cultivation_period="";
        $upazilla_id="";

        if($id>0)
        {
            if(!((isset($this->permissions['action7']) && ($this->permissions['action7']==1))))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(! ($item_head['status_approve']))
            {

                $ajax['status']=false;
                $ajax['system_message']='Approve Field is required.';
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_bi_variety_cultivation_period_request'),'*',array('id ='.$id),1);
            if(!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'ID Not Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if ($result['status']==$this->config->item('system_status_rejected'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Rejected.';
                $this->json_return($ajax);
            }
            if ($result['status_forward']==$this->config->item('system_status_pending'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Not Yet Forwarded.';
                $this->json_return($ajax);
            }
            if ($result['status_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Approved.';
                $this->json_return($ajax);
            }
            $upazilla_id=$result['upazilla_id'];
            $cultivation_period=json_decode($result['cultivation_period'],TRUE);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        if($item_head['status_approve']!=$this->config->item('system_status_approved'))
        {
            if(!($item_head['remarks_approve']))
            {
                $ajax['status']=false;
                $ajax['system_message']="Remarks field is required";
                $this->json_return($ajax);
            }
        }

        // old item
        $this->db->from($this->config->item('table_bi_variety_cultivation_period'));
        $this->db->select('*');
        $this->db->where('upazilla_id', $upazilla_id);
        $results = $this->db->get()->result_array();
        foreach ($results as $result)
        {
            $cultivation_period_old[$result['type_id']] = $result;
        }

        // crop type info
        $this->db->from($this->config->item('table_login_setup_classification_crop_types') . ' type');
        $this->db->select('type.id type_id');
        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $this->db->select('crop.id crop_id');
        $this->db->where('type.status', $this->config->item('system_status_active'));
        $this->db->where('crop.status', $this->config->item('system_status_active'));
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('type.ordering','ASC');
        $crop_types = $this->db->get()->result_array();

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['date_approved']=$time;
        $data['user_approved']=$user->user_id;
        $data['remarks_approve']=$item_head['remarks_approve'];
        if($item_head['status_approve']==$this->config->item('system_status_rollback'))
        {
            $data['status_forward']=$this->config->item('system_status_pending');
            $data['status_approve']=$this->config->item('system_status_pending');
            $this->db->set('revision_count_rollback', 'revision_count_rollback+1', FALSE);
        }
        else
        {
            $data['remarks_approve']=$item_head['remarks_approve'];
            $data['status_approve']=$item_head['status_approve'];
        }
        Query_helper::update($this->config->item('table_bi_variety_cultivation_period_request'),$data,array('id='.$id));

        if($item_head['status_approve']==$this->config->item('system_status_approved'))
        {
            foreach($crop_types as $type)
            {
                if(isset($cultivation_period[$type['type_id']]))
                {
                    $date=explode('~',$cultivation_period[$type['type_id']]);
                    $data=array();
                    $data['type_id'] = $type['type_id'];
                    $data['upazilla_id'] = $upazilla_id;
                    $data['date_start'] = $date[0];
                    $data['date_end'] = $date[1];
                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    if(isset($cultivation_period_old[$type['type_id']]))
                    {
                        $this->db->set('revision_count', 'revision_count+1', FALSE);
                        Query_helper::update($this->config->item('table_bi_variety_cultivation_period'), $data, array("id =" . $id), FALSE);
                    }
                    else
                    {
                        $data['revision_count'] = 1;
                        Query_helper::add($this->config->item('table_bi_variety_cultivation_period'), $data, FALSE);
                    }
                }
            }
        }

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
}
