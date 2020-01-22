<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Variety_cultivation_period_request extends Root_Controller
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
        elseif ($action == "forward")
        {
            $this->system_forward($id);
        }
        elseif ($action == "save_forward")
        {
            $this->system_save_forward();
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
            $data['outlet_name'] = 1;
            $data['district_name'] = 1;
            $data['territory_name'] = 1;
            $data['zone_name'] = 1;
            $data['division_name'] = 1;
            $data['number_of_edit'] = 1;
        }
        else if ($method == 'list_all')
        {
            $data['id'] = 1;
            $data['outlet_name'] = 1;
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
            $data['title'] = $this->lang->line('LABEL_OUTLET_NAME') . " Wise Cultivation Period List";
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

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = item.outlet_id AND cus_info.revision=1', 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = cus_info.district_id');
        $this->db->select('districts.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territories', 'territories.id = districts.territory_id', 'INNER');
        $this->db->select('territories.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zones', 'zones.id = territories.zone_id', 'INNER');
        $this->db->select('zones.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' divisions', 'divisions.id = zones.division_id', 'INNER');
        $this->db->select('divisions.name division_name');

        $this->db->where('item.status', $this->config->item('system_status_active'));
        $this->db->where('item.status_forward !=', $this->config->item('system_status_forwarded'));
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
                    }
                }
            }
        }
        $items = $this->db->get()->result_array();
        //echo $this->db->last_query();
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
            $data['title'] = $this->lang->line('LABEL_OUTLET_NAME') . " Wise Cultivation Period All List";
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

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = item.outlet_id AND cus_info.revision=1', 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = cus_info.district_id');
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
                    }
                }
            }
        }
        $items = $this->db->get()->result_array();
        $this->json_return($items);
    }
    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data = array();
            $data['item'] = Array
            (
                'id' => 0,
                'division_id' => 0,
                'zone_id' => 0,
                'territory_id' => 0,
                'district_id' => 0,
                //'upazilla_id' => 0,
                'outlet_id' => 0,
                'market_size' => '',
                'ordering' => 99,
                'status' => ''
            );

            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets'] = array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id'],'status ="'.$this->config->item('system_status_active').'"'));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id'],'status ="'.$this->config->item('system_status_active').'"'));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id'],'status ="'.$this->config->item('system_status_active').'"'));
                        if ($this->locations['district_id'] > 0)
                        {
                            $data['outlets'] = Query_helper::get_info($this->config->item('table_login_csetup_cus_info'), array('customer_id value', 'name text'), array('district_id =' . $this->locations['district_id'], 'revision=1', 'type =' . $this->config->item('system_customer_type_outlet_id')));
                        }
                    }
                }
            }

            $data['title'] = "New Cultivation Period For  " . ($this->lang->line('LABEL_OUTLET_NAME')) . " Area ";
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
            $this->db->from($this->config->item('table_bi_variety_cultivation_period_request') . ' item');
            $this->db->select('item.*');

            $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = item.outlet_id AND cus_info.revision=1', 'INNER');
            $this->db->select('cus_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = cus_info.district_id', 'INNER');
            $this->db->select('districts.id district_id, districts.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = districts.territory_id', 'INNER');
            $this->db->select('territory.id territory_id, territory.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.id division_id, division.name division_name');

            $this->db->where('item.id', $item_id);
            $this->db->where('item.status', $this->config->item('system_status_active'));
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['zones']=true;
            $data['territories']=true;
            $data['districts']=true;
            $data['outlets'] = true;

            $this->db->from($this->config->item('table_bi_variety_cultivation_period'));
            $this->db->select('*');
            $this->db->where('outlet_id', $data['item']['outlet_id']);
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


            $data['title'] = "Edit Variety Cultivation Period ( Outlet Wise )";
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
        $id = $this->input->post('id');
        $user = User_helper::get_user();
        $time = time();
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        $outlet_id = $this->input->post('outlet_id');

        if ($id > 0) //EDIT
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
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
            if($result['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already Forwarded.';
                $this->json_return($ajax);
            }
            $outlet_id=$result['outlet_id'];
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

        // Old item
        $this->db->from($this->config->item('table_bi_variety_cultivation_period'));
        $this->db->select('*');
        $this->db->where('outlet_id', $outlet_id);
        $results = $this->db->get()->result_array();
        $cultivation_period_old=array();
        $cultivation_period_array=array();
        foreach ($results as $result)
        {
            $cultivation_period_old[$result['type_id']] = $result;
            $cultivation_period_array[$result['type_id']]['old'] = $result['date_start'].'~'.$result['date_end'];
            $cultivation_period_array[$result['type_id']]['new'] = "";
        }

        $invalid_date=false;
        foreach($items as $type_id=>$info)
        {
            if($info['date_start'])
            {
                if(!isset($cultivation_period_array[$type_id]['old']))
                {
                    $cultivation_period_array[$type_id]['old']="";
                }
                $date_start=System_helper::get_time($info['date_start'].'-1970');
                $date_end=System_helper::get_time($info['date_end'].'-1970');

                if($date_end<$date_start)
                {
                    $date_end=System_helper::get_time($info['date_end'].'-1971');
                }
                if($date_end!=0)
                {
                    $date_end+=24*3600-1;
                }

                if(isset($cultivation_period_old[$type_id]))
                {
                    if(!(($cultivation_period_old[$type_id]['date_start']==$date_start) && ($cultivation_period_old[$type_id]['date_end']==$date_end)))
                    {
                        $cultivation_period_array[$type_id]['new']=$date_start.'~'.$date_end;
                    }
                }
                else
                {
                    $cultivation_period_array[$type_id]['new']=$date_start.'~'.$date_end;
                }
            }
        }

        $item_head['cultivation_period']=json_encode($cultivation_period_array);

        $this->db->trans_start(); //DB Transaction Handle START
        if ($id > 0) // Revision Update if EDIT
        {
            $item_head['date_updated'] = $time;
            $item_head['user_updated'] = $user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_bi_variety_cultivation_period_request'), $item_head, array("id =" . $id), FALSE);
        }
        else
        {
            $item_head['outlet_id'] = $outlet_id;
            $item_head['revision_count'] = 1;
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_bi_variety_cultivation_period_request'), $item_head, FALSE);
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
            $this->db->from($this->config->item('table_bi_variety_cultivation_period_request') . ' item');
            $this->db->select('item.*');

            $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = item.outlet_id AND cus_info.revision=1', 'INNER');
            $this->db->select('cus_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = cus_info.district_id', 'INNER');
            $this->db->select('districts.id district_id, districts.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = districts.territory_id', 'INNER');
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

            $data['title'] = "Cultivation Period Details ( Outlet Wise )";
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
    private function system_forward($id)
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

            $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = item.outlet_id AND cus_info.revision=1', 'INNER');
            $this->db->select('cus_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = cus_info.district_id', 'INNER');
            $this->db->select('districts.id district_id, districts.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = districts.territory_id', 'INNER');
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
            if ($data['item']['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Forwarded.';
                $this->json_return($ajax);
            }

            $data['info_basic']=Bi_helper::get_basic_info($data['item']);

            $this->db->from($this->config->item('table_bi_variety_cultivation_period'));
            $this->db->select('*');
            $this->db->where('outlet_id', $data['item']['outlet_id']);
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

            $data['title'] = "Forward Cultivation Period (Outlet Wise)";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/forward", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/forward/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
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
            if($result['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already Forwarded.';
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
        $data['remarks_forward']=$item_head['remarks_forward'];
        $data['status_forward']=$item_head['status_forward'];
        Query_helper::update($this->config->item('table_bi_variety_cultivation_period_request'),$data,array('id='.$id));

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
    private function check_validation()
    {
        $id = $this->input->post('id');
        $outlet_id = $this->input->post('outlet_id');
        $items = $this->input->post('items');

        if( !($id > 0) && (!isset($outlet_id) || !($outlet_id > 0)) ){
            $this->message = $this->lang->line('LABEL_OUTLET_NAME') . ' field is required.';
            return false;
        }

        $entry_not_found = TRUE;
        foreach($items as $item){
            if(trim($item['date_start'])!='' && trim($item['date_end'])!=''){
                $entry_not_found = FALSE;
            }elseif(trim($item['date_start'])=='' && trim($item['date_end'])==''){
                // Do nothing
            }else{
                $this->message = 'Both '.$this->lang->line('LABEL_DATE_START').' and '.$this->lang->line('LABEL_DATE_END').' fields are required';
                return false;
            }
        }

        if($entry_not_found){
            $this->message = 'Atleast One '.$this->lang->line('LABEL_DATE_START').' and '.$this->lang->line('LABEL_DATE_END').' is required.';
            return false;
        }

        return true;
    }
    private function system_get_cultivation_period_info()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $post=$this->input->post();
            $data = array();
            $data['outlet_id'] = $post['outlet_id'];

            $item_old=Query_helper::get_info($this->config->item('table_bi_variety_cultivation_period_request'),array('*'),array('outlet_id ="'.$data['outlet_id'].'"','status_approve ="'.$this->config->item('system_status_pending').'"'));
            if($item_old)
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'This upazilla cultivation period information exist. Not yet forwarded or approval';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_bi_variety_cultivation_period'));
            $this->db->select('*');
            $this->db->where('outlet_id', $data['outlet_id']);
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

            $data['title'] = "Cultivation Period";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#items_container", "html" => $this->load->view($this->controller_url . "/get_cultivation_period_info", $data, true));
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
}
