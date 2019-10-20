<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_market_size extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->locations=User_helper::get_locations();
        $this->user=User_helper::get_user();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url = strtolower(get_class($this));
        $this->language_labels();
        $this->load->helper('bi_helper');
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_CROP_TYPE']='Crop Type';
        $this->lang->language['LABEL_MARKET_SIZE_KG_TOTAL']='Total Market Size kg';
        $this->lang->language['LABEL_MARKET_SIZE_KG_ARM']='ARM Market Size kg';
        $this->lang->language['LABEL_MARKET_SIZE_KG_OTHER']='Other Market Size kg';
        $this->lang->language['LABEL_VARIETY_ARM']='ARM Variety';
        $this->lang->language['LABEL_VARIETY_COMPETITOR']='Competitor Major Variety';
    }

    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_search();
        }
    }
    private function get_preference_headers()
    {
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['market_size_kg_total']= 1;
        $data['market_size_kg_arm']= 1;
        $data['market_size_kg_other']= 1;
        $data['date_start']= 1;
        $data['date_end']= 1;
        $data['date_end']= 1;
        $data['variety_arm']= 1;
        $data['variety_competitor']= 1;
        return $data;
    }
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search_outlets_payment"'),1);
        $data=$this->get_preference_headers();
        if($result)
        {
            if($result['preferences']!=null)
            {
                $preferences=json_decode($result['preferences'],true);
                foreach($data as $key=>$value)
                {
                    if(isset($preferences[$key]))
                    {
                        $data[$key]=$value;
                    }
                    else
                    {
                        $data[$key]=0;
                    }
                }
            }
        }
        return $data;
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['preference_method_name']='search_outlets_payment';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_outlets_payment');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="ARM & Competitor Market Size Report";
            $ajax['status']=true;
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['upazillas']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id'],'status ="'.$this->config->item('system_status_active').'"'));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id'],'status ="'.$this->config->item('system_status_active').'"'));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id'],'status ="'.$this->config->item('system_status_active').'"'));
                        if($this->locations['district_id']>0)
                        {
                            if($this->locations['upazilla_id']>0)
                            {
                                $upazillas=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id value','name text'),array('id ='.$this->locations['upazilla_id'],'status ="'.$this->config->item('system_status_active').'"'));
                            }
                            else
                            {
                                $upazillas=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"'));
                            }
                            foreach($upazillas as $upazilla)
                            {
                                $data['upazillas'][$this->locations['upazilla_id']][] = $upazilla;
                            }
                        }
                    }

                }
            }

            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            /*$data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }*/
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url);

            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }

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
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $data['options']=$reports;
            $ajax['status']=true;
            $data['title']="Market Size Report";
            $data['system_preference_items']= $this->get_preference();
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));

            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }

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
        $items=array();
        // post
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $upazilla_id=$this->input->post('upazilla_id');

        // get total market size
        //$results=Query_helper::get_info($this->config->item('table_bi_market_size'),array('*'),array());
        $this->db->from($this->config->item('table_bi_market_size') . ' market_size');
        $this->db->select('market_size.type_id, SUM(market_size.market_size_kg) AS market_size_kg');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = market_size.type_id','INNER');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = market_size.upazilla_id');
        //$this->db->select('upazilla.id upazilla_id, upazilla.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id');
        //$this->db->select('district.id district_id, district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        //$this->db->select('territory.id territory_id, territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        //$this->db->select('zone.id zone_id, zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        //$this->db->select('division.id division_id, division.name division_name');
        if($crop_id>0)
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
            }
        }
        if ($division_id > 0)
        {
            $this->db->where('division.id', $division_id);
            if ($zone_id > 0)
            {
                $this->db->where('zone.id', $zone_id);
                if ($territory_id > 0)
                {
                    $this->db->where('territory.id', $territory_id);
                    if ($district_id > 0)
                    {
                        $this->db->where('district.id', $district_id);
                        if ($upazilla_id > 0)
                        {
                            $this->db->where('upazilla.id', $upazilla_id);
                        }
                    }
                }
            }
        }
        $this->db->group_by('market_size.type_id');
        $results = $this->db->get()->result_array();
        $market_size_kg_total=array();
        foreach($results as $result)
        {
            $market_size_kg_total[$result['type_id']]=$result['market_size_kg'];
        }

        // get fiscal year
        $fiscal_year=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);

        // get location for sales
        $this->db->from($this->config->item('table_login_csetup_customer').' outlet');
        $this->db->select('outlet.id');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id = outlet.id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        // need to upazilla table join

        $this->db->where('outlet.status',$this->config->item('system_status_active'));
        $this->db->where('outlet_info.revision',1);
        if($division_id>0)
        {
            $this->db->where('zones.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zones.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('territories.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('districts.id',$district_id);
                        // need to upzilla
                    }
                }
            }
        }
        $results=$this->db->get()->result_array();
        $outlet_ids=array();
        $outlet_ids[0]=0;
        foreach($results as $result)
        {
            $outlet_ids[$result['id']]=$result['id'];
        }
        // get calculate total market size
        $this->db->from($this->config->item('table_pos_sale_details').' details');
        $this->db->select('details.variety_id, details.pack_size, details.pack_size_id, crop_type.id crop_type_id');
        $this->db->select('SUM(details.quantity) quantity_sale_pkt');
        $this->db->select('SUM((details.pack_size*details.quantity)/1000) quantity_sale_kg');

        $this->db->join($this->config->item('table_pos_sale').' sale','sale.id = details.sale_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=details.variety_id', 'INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');

        $this->db->where('sale.date_sale >=',$fiscal_year['date_start']);
        $this->db->where('sale.date_sale <=',$fiscal_year['date_end']);
        $this->db->where('sale.status',$this->config->item('system_status_active'));
        if($crop_id>0)
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
            }
        }
        $this->db->where_in('sale.outlet_id',$outlet_ids);
        //$this->db->group_by('details.variety_id, details.pack_size_id');
        $this->db->group_by('crop_type.id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            //$sales[$result['variety_id']][$result['pack_size']][$fiscal_year['id']]=$result;
            $market_size_kg_arm[$result['crop_type_id']]=$result['quantity_sale_kg'];
        }

        // get type wise cultivation period :: minimum start date
        $this->db->from($this->config->item('table_bi_variety_cultivation_period').' cultivation_period');
        $this->db->select('cultivation_period.type_id, MIN(cultivation_period.date_start) as date_start_min');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = cultivation_period.type_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = cultivation_period.upazilla_id');
        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id');
        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->where('cultivation_period.status',$this->config->item('system_status_active'));
        if($crop_id>0)
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
            }
        }
        if ($division_id > 0)
        {
            $this->db->where('division.id', $division_id);
            if ($zone_id > 0)
            {
                $this->db->where('zone.id', $zone_id);
                if ($territory_id > 0)
                {
                    $this->db->where('territory.id', $territory_id);
                    if ($district_id > 0)
                    {
                        $this->db->where('district.id', $district_id);
                        if ($upazilla_id > 0)
                        {
                            $this->db->where('upazilla.id', $upazilla_id);
                        }
                    }
                }
            }
        }
        $this->db->group_by('cultivation_period.type_id');
        $results=$this->db->get()->result_array();
        $date_start_min=array();
        foreach($results as $result)
        {
            $date_start_min[$result['type_id']] = $result['date_start_min'];
        }
        // get type wise cultivation period :: maximum end date
        $this->db->from($this->config->item('table_bi_variety_cultivation_period').' cultivation_period');
        $this->db->select('cultivation_period.type_id,MAX(cultivation_period.date_end) as date_end_max');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = cultivation_period.type_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = cultivation_period.upazilla_id');
        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id');
        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->where('cultivation_period.status',$this->config->item('system_status_active'));
        if($crop_id>0)
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
            }
        }
        if ($division_id > 0)
        {
            $this->db->where('division.id', $division_id);
            if ($zone_id > 0)
            {
                $this->db->where('zone.id', $zone_id);
                if ($territory_id > 0)
                {
                    $this->db->where('territory.id', $territory_id);
                    if ($district_id > 0)
                    {
                        $this->db->where('district.id', $district_id);
                        if ($upazilla_id > 0)
                        {
                            $this->db->where('upazilla.id', $upazilla_id);
                        }
                    }
                }
            }
        }
        $this->db->group_by('cultivation_period.type_id');
        $results=$this->db->get()->result_array();
        $date_end_max=array();
        foreach($results as $result)
        {
            $date_end_max[$result['type_id']] = $result['date_end_max'];
        }



        // get arm variety
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id,v.name,v.whose,v.crop_type_id');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->select('type.id crop_type_id, type.name crop_type_name, type.crop_id');
        if($crop_id>0)
        {
            $this->db->where('type.crop_id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('v.crop_type_id',$crop_type_id);
            }
        }
        //$this->db->where('v.whose','ARM');
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->order_by('type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $results=$this->db->get()->result_array();
        $varieties_arm=array();
        $variety_info=array();
        foreach($results as $result)
        {
            /*if($result['whose']=='ARM')
            {
                if(isset($varieties_arm[$result['crop_type_id']]))
                {
                    $varieties_arm[$result['crop_type_id']] = $varieties_arm[$result['crop_type_id']].", ".$result['name'];
                }
                else
                {
                    $varieties_arm[$result['crop_type_id']] = $result['name'];
                }
            }*/
            $variety_info[$result['id']]=$result;
        }

        // get arm variety type & upazilla wise
        $this->db->from($this->config->item('table_bi_variety_arm_upazilla') . ' arm_variety');
        $this->db->select('arm_variety.*');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = arm_variety.upazilla_id');
        $this->db->select('upazilla.id upazilla_id, upazilla.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id');
        $this->db->select('district.id district_id, district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.id territory_id, territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.id zone_id, zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.id division_id, division.name division_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = arm_variety.type_id','INNER');
        if($crop_id>0)
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
            }
        }
        if ($division_id > 0)
        {
            $this->db->where('division.id', $division_id);
            if ($zone_id > 0)
            {
                $this->db->where('zone.id', $zone_id);
                if ($territory_id > 0)
                {
                    $this->db->where('territory.id', $territory_id);
                    if ($district_id > 0)
                    {
                        $this->db->where('district.id', $district_id);
                        if ($upazilla_id > 0)
                        {
                            $this->db->where('upazilla.id', $upazilla_id);
                        }
                    }
                }
            }
        }
        $results = $this->db->get()->result_array();
        $varieties_arm=array();
        foreach($results as $result)
        {
            $item_varieties=json_decode($result['variety_ids'],true);
            foreach($item_varieties as $variety_id)
            {
                $variety_name=isset($variety_info[$variety_id])?$variety_info[$variety_id]['name']:'';
                if(isset($varieties_arm[$result['type_id']]))
                {
                    $varieties_arm[$result['type_id']] = $varieties_arm[$result['type_id']].", ".$variety_name;
                }
                else
                {
                    $varieties_arm[$result['type_id']] = $variety_name;
                }
            }
        }

        // get major variety type wise
        $this->db->from($this->config->item('table_bi_major_competitor_variety') . ' competitor_variety');
        $this->db->select('competitor_variety.*');

        $this->db->join($this->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = competitor_variety.upazilla_id');
        $this->db->select('upazilla.id upazilla_id, upazilla.name upazilla_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id');
        $this->db->select('district.id district_id, district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.id territory_id, territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.id zone_id, zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.id division_id, division.name division_name');
        if ($division_id > 0)
        {
            $this->db->where('division.id', $division_id);
            if ($zone_id > 0)
            {
                $this->db->where('zone.id', $zone_id);
                if ($territory_id > 0)
                {
                    $this->db->where('territory.id', $territory_id);
                    if ($district_id > 0)
                    {
                        $this->db->where('district.id', $district_id);
                        if ($upazilla_id > 0)
                        {
                            $this->db->where('upazilla.id', $upazilla_id);
                        }
                    }
                }
            }
        }
        $results = $this->db->get()->result_array();
        $competitor_major_varieties=array();
        $varieties_competitor=array();
        foreach($results as $result)
        {
            $competitor_varieties=json_decode($result['competitor_varieties'],true);
            foreach($competitor_varieties as $types)
            {
                foreach($types as $type_id=>$varieties)
                {
                    foreach($varieties as $variety_id)
                    {
                        if(isset($variety_info[$variety_id]))
                        {
                            $variety_name=$variety_info[$variety_id]['name'];
                            if(!isset($competitor_major_varieties[$type_id][$variety_id]))
                            {
                                if(isset($varieties_competitor[$type_id]))
                                {
                                    $varieties_competitor[$type_id]=$varieties_competitor[$type_id].", ".$variety_name;
                                }
                                else
                                {
                                    $varieties_competitor[$type_id]=$variety_name;
                                }
                            }
                            $competitor_major_varieties[$type_id][$variety_id]=isset($variety_info[$variety_id])?$variety_info[$variety_id]['name']:'';
                        }
                    }
                }
            }
        }

        $this->db->from($this->config->item('table_login_setup_classification_crops').' crop');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.crop_id=crop.id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
            }
        }
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');

        $results=$this->db->get()->result_array();

        $prev_crop_name='';
        $first_row=true;
        $crop_total=$this->initialize_row(array('crop_type_name'=>'Total Crop'));
        $grand_total=$this->initialize_row(array('crop_name'=>'Grand Total'));
        foreach($results as $result)
        {
            $info=$this->initialize_row($result);
            if(!$first_row)
            {
                if($prev_crop_name!=$info['crop_name'])
                {
                    $crop_total['crop_name']=$prev_crop_name;
                    $items[]=$crop_total;
                    $crop_total=$this->reset_row($crop_total);
                    $prev_crop_name=$info['crop_name'];
                }
                else
                {
                    //$info['crop_name']='';
                    //info['crop_type_name']='';
                }
            }
            else
            {
                $prev_crop_name=$info['crop_name'];
                $first_row=false;
            }
            $info['market_size_kg_total']=isset($market_size_kg_total[$result['crop_type_id']])?$market_size_kg_total[$result['crop_type_id']]:0;
            $info['market_size_kg_arm']=isset($market_size_kg_arm[$result['crop_type_id']])?$market_size_kg_arm[$result['crop_type_id']]:0;
            $info['market_size_kg_other']=($info['market_size_kg_total']-$info['market_size_kg_arm']); // discussion with abid & shaiful vi.
            $info['date_start']=isset($date_start_min[$result['crop_type_id']])?Bi_helper::cultivation_date_display($date_start_min[$result['crop_type_id']]):'';
            $info['date_end']=isset($date_end_max[$result['crop_type_id']])?Bi_helper::cultivation_date_display($date_end_max[$result['crop_type_id']]):'';
            $info['variety_arm']=isset($varieties_arm[$result['crop_type_id']])?$varieties_arm[$result['crop_type_id']]:'';
            $info['variety_competitor']=isset($varieties_competitor[$result['crop_type_id']])?$varieties_competitor[$result['crop_type_id']]:'';
            $items[]=$info;
            foreach($info  as $key=>$r)
            {
                if(!(($key=='crop_name')||($key=='crop_type_name') || ($key=='date_start') || ($key=='date_end') || ($key=='variety_arm') || ($key=='variety_competitor') ))
                {
                    $crop_total[$key]+=$info[$key];
                    $grand_total[$key]+=$info[$key];
                }
            }
        }
        $items[]=$crop_total;
        $items[]=$grand_total;

        $this->json_return($items);

    }
    private function initialize_row($info)
    {
        $row=$this->get_preference_headers();
        foreach($row  as $key=>$r)
        {
            if($key=='crop_name')
            {
                $row[$key]=isset($info['crop_name'])?$info['crop_name']:'';
            }
            elseif($key=='crop_type_name')
            {
                $row[$key]=isset($info['crop_type_name'])?$info['crop_type_name']:'';
            }
            elseif($key=='date_start' || $key=='date_end' || $key=='variety_arm' || $key=='variety_competitor')
            {
                $row[$key]='';
            }
            else
            {
                $row[$key]=0;
            }
        }

        return $row;
    }
    private function reset_row($info)
    {
        foreach($info  as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')))
            {
                $info[$key]='';
            }
        }
        return $info;
    }
}
