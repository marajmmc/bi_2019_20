<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Root_controller
{
    public $message;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        /*$this->message="";
        $this->permissions=User_helper::get_permission('Setup_print');*/
        $this->controller_url=strtolower(get_class());
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->load->helper('bi');
        //$this->load->helper('barcode');
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_AMOUNT_CREDIT_LIMIT'] = 'Credit Limit';
        $this->lang->language['LABEL_AMOUNT_CREDIT_BALANCE'] = 'Available Credit';
        $this->lang->language['LABEL_AMOUNT_CREDIT_DUE'] = 'Due';
        $this->lang->language['LABEL_AMOUNT_LAST_PAYMENT'] = 'Last payment amount';
        $this->lang->language['LABEL_DATE_LAST_PAYMENT'] = 'Last Payment Date';
        $this->lang->language['LABEL_DAY_LAST_PAYMENT'] = 'Last Payment days';
        $this->lang->language['LABEL_AMOUNT_LAST_SALE'] = 'Last Invoice amount';
        $this->lang->language['LABEL_DATE_LAST_SALE'] = 'Last Invoice Date';
        $this->lang->language['LABEL_DAY_LAST_SALE'] = 'Last Invoice days';
        $this->lang->language['LABEL_DAY_COLOR_PAYMENT_START'] = 'Payment warning color start(days)';
        $this->lang->language['LABEL_DAY_COLOR_PAYMENT_INTERVAL'] = 'Payment warning color interval(days)';

        $this->lang->language['LABEL_DAY_COLOR_SALES_START'] = 'Invoice warning color start(days)';
        $this->lang->language['LABEL_DAY_COLOR_SALES_INTERVAL'] = 'Invoice warning color interval(days)';

        $this->lang->language['LABEL_SALE_DUE_STATUS'] = 'Last Invoice due status';
    }
    public function index($action="",$type="", $value=0)
    {
        if($action=="chart_sales_crop_wise_last_three_years")
        {
            $this->system_chart_sales_crop_wise_last_three_years();
        }
        elseif($action=="get_item_chart_sales_crop_wise_last_three_years")
        {
            $this->system_get_items_chart_sales_crop_wise_last_three_years($type, $value);
        }
        elseif($action=="chart_invoice_payment_wise_cash_credit")
        {
            $this->system_chart_invoice_payment_wise_cash_credit();
        }
        elseif($action=="get_item_chart_invoice_payment_wise_cash_credit")
        {
            $this->system_get_item_chart_invoice_payment_wise_cash_credit($type, $value);
        }
        elseif($action=="chart_amount_sales_vs_target")
        {
            $this->system_chart_amount_sales_vs_target();
        }
        elseif($action=="get_item_chart_amount_sales_vs_target")
        {
            $this->system_get_item_chart_amount_sales_vs_target($type, $value);
        }
        elseif($action=="invoice_amount")
        {
            $this->system_invoice_amount();
        }
        elseif($action=="report_farmer_balance_notification")
        {
            $this->system_report_farmer_balance_notification();
        }
        elseif($action=="get_item_report_farmer_balance_notification")
        {
            $this->system_get_item_report_farmer_balance_notification();
        }
        elseif($action=="focusable_varieties")
        {
            $this->system_focusable_varieties();
        }

    }

    private function system_chart_sales_crop_wise_last_three_years()
    {
        $data=array();
        $html_id = $this->input->post('html_id');
        //$locations_post = $this->input->post('locations');
        $data['type'] = $this->input->post('type');
        $data['value'] = $this->input->post('value');
        $data['unitInterval'] = $this->input->post('unitInterval');
        //$data['locations']=$this->get_locations($locations_post);

        if($data['type']=='today')
        {
            $data['title']="Today (".$data['value'].' '.date('M, Y').") Crop Wise Sales";
            $fiscal_year_number=2;
            $month=0;
            $date=$data['value'];
        }
        elseif($data['type']=='month')
        {
            $data['title']="Monthly (".$this->lang->line('LABEL_MONTH_'.intval($data['value'])).") Crop Wise Sales";
            $fiscal_year_number=2;
            $month=$data['value'];
            $date=0;
        }
        elseif($data['type']=='year')
        {
            $data['title']="Last ".($data['value']+1)." Years Crop Wise Sales";
            $fiscal_year_number=$data['value'];
            $month=0;//date('m', time());
            $date=0;
        }
        else
        {
            $fiscal_year_number=0;
            $month=0;//date('m', time());
            $date=0;
        }
        $data['fiscal_years']=$this->get_fiscal_years($fiscal_year_number, $month, $date);
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_id,"html"=>$this->load->view($this->controller_url."/chart_sales_crop_wise_last_three_years",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    private function system_get_items_chart_sales_crop_wise_last_three_years($type,$value)
    {
        $location_post=array
        (
            'division_id'=>$this->input->get('division_id'),
            'zone_id'=>$this->input->get('zone_id'),
            'territory_id'=>$this->input->get('territory_id'),
            'district_id'=>$this->input->get('district_id'),
            'outlet_id'=>$this->input->get('outlet_id'),
        );
        $locations=$this->get_locations($location_post);

        if($type=='today')
        {
            $fiscal_year_number=2;
            $month=0;
            $date=$value;
        }
        elseif($type=='month')
        {
            $fiscal_year_number=2;
            $month=$value;
            $date=0;
        }
        elseif($type=='year')
        {
            $fiscal_year_number=$value;
            $month=0;
            $date=0;
        }
        else
        {
            $fiscal_year_number=2;
            $month=0;
            $date=0;
        }

        $this->db->from($this->config->item('table_login_csetup_customer').' outlet');
        $this->db->select('outlet.id');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id = outlet.id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');

        $this->db->where('outlet.status',$this->config->item('system_status_active'));
        $this->db->where('outlet_info.revision',1);
        if($locations['division_id']>0)
        {
            $this->db->where('zones.division_id',$locations['division_id']);
            if($locations['zone_id']>0)
            {
                $this->db->where('zones.id',$locations['zone_id']);
                if($locations['territory_id']>0)
                {
                    $this->db->where('territories.id',$locations['territory_id']);
                    if($locations['district_id']>0)
                    {
                        $this->db->where('districts.id',$locations['district_id']);
                        if($locations['outlet_id']>0)
                        {
                            $this->db->where('outlet.id',$locations['outlet_id']);
                        }
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

        $fiscal_years=$this->get_fiscal_years($fiscal_year_number, $month, $date);
        $sales=array();
        foreach($fiscal_years as $fy)
        {
            $this->db->from($this->config->item('table_pos_sale_details').' details');
            $this->db->select('details.variety_id, details.pack_size, details.pack_size_id');
            //$this->db->select('SUM(details.quantity) quantity_sale_pkt');
            $this->db->select('SUM(((details.pack_size*details.quantity)/1000)) quantity_sale_kg');
            //$this->db->select('SUM(details.amount_payable_actual-((details.amount_payable_actual*sale.discount_self_percentage)/100)) amount_total');

            $this->db->join($this->config->item('table_pos_sale').' sale','sale.id = details.sale_id','INNER');

            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=details.variety_id', 'INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
            $this->db->select('crop_type.crop_id');

            $this->db->where('sale.date_sale >=',$fy['date_start']);
            $this->db->where('sale.date_sale <=',$fy['date_end']);
            $this->db->where('sale.status',$this->config->item('system_status_active'));
            $this->db->where_in('sale.outlet_id',$outlet_ids);
            //$this->db->group_by('details.variety_id, details.pack_size_id');
            $this->db->group_by('crop_type.crop_id');
            $results=$this->db->get()->result_array();
            //echo $this->db->last_query();
            foreach($results as $result)
            {
                //$sales[$result['variety_id']][$result['pack_size']][$fy['id']]=$result;
                $sales[$result['crop_id']][$fy['id']]=$result;
            }
        }

        $this->db->from($this->config->item('table_login_setup_classification_crops').' crop');
        $this->db->select('crop.id crop_id,crop.name crop_name');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $crops = $this->db->get()->result_array();
        $items=array();
        foreach($crops as $crop)
        {
            $info=array();
            $info['crop']=$crop['crop_name'];
            $info['target']=0;//rand(1000,5000);
            foreach($fiscal_years as $fiscal_year)
            {
                //$info[str_replace('-','_',$fiscal_year['name'])]=3333;
                if(isset($sales[$crop['crop_id']][$fiscal_year['id']]))
                {
                    /*if($sales[$crop['crop_id']][$fiscal_year['id']]['quantity_sale_kg']>100)
                    {
                        $quantity_sale_kg=($sales[$crop['crop_id']][$fiscal_year['id']]['quantity_sale_kg'])/1000;
                    }
                    else
                    {
                        $quantity_sale_kg=$sales[$crop['crop_id']][$fiscal_year['id']]['quantity_sale_kg'];
                    }*/
                    $quantity_sale_kg=$sales[$crop['crop_id']][$fiscal_year['id']]['quantity_sale_kg'];
                    $info[str_replace('-','_',$fiscal_year['name'])]=$quantity_sale_kg;
                }
            }
            $items[]=$info;
        }

        /*
        $items[]=array('crop'=>'jan', 'max'=>2461, 'min'=>3000, 'ave'=>3000);
        $items[]=array('crop'=>'feb', 'max'=>1224, 'min'=>2500, 'ave'=>2500);
        $items[]=array('crop'=>'mar', 'max'=>4215, 'min'=>1500, 'ave'=>1500);
        $items[]=array('crop'=>'apr', 'max'=>3251, 'min'=>5000, 'ave'=>5000);*/

        $this->json_return($items);
    }
    private function system_chart_invoice_payment_wise_cash_credit()
    {
        $data=array();
        $html_id = $this->input->post('html_id');
        $data['type'] = $this->input->post('type');
        $data['value'] = $this->input->post('value');
        $data['unitInterval'] = $this->input->post('unitInterval');

        if($data['type']=='today')
        {
            $data['title']="Today (".$data['value'].' '.date('M, Y').") Payment (%) Summary";
            $fiscal_year_number=2;
            $month=0;
            $date=$data['value'];
        }
        elseif($data['type']=='month')
        {
            $data['title']="Monthly (".$this->lang->line('LABEL_MONTH_'.intval($data['value'])).") Payment (%) Summary";
            $fiscal_year_number=2;
            $month=$data['value'];
            $date=0;
        }
        elseif($data['type']=='year')
        {
            $data['title']="Current Years Payment (%) Summary";
            $fiscal_year_number=$data['value'];
            $month=0;//date('m', time());
            $date=0;
        }
        else
        {
            $fiscal_year_number=0;
            $month=0;//date('m', time());
            $date=0;
        }
        $data['fiscal_years']=$this->get_fiscal_years($fiscal_year_number, $month, $date);
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_id,"html"=>$this->load->view($this->controller_url."/chart_invoice_payment_wise_cash_credit",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    public function system_get_item_chart_invoice_payment_wise_cash_credit($type,$value)
    {
        $location_post=array
        (
            'division_id'=>$this->input->get('division_id'),
            'zone_id'=>$this->input->get('zone_id'),
            'territory_id'=>$this->input->get('territory_id'),
            'district_id'=>$this->input->get('district_id'),
            'outlet_id'=>$this->input->get('outlet_id'),
        );
        $locations=$this->get_locations($location_post);
        if($type=='today')
        {
            $fiscal_year_number=0;
            $month=0;
            $date=$value;
        }
        elseif($type=='month')
        {
            $fiscal_year_number=0;
            $month=$value;
            $date=0;
        }
        elseif($type=='year')
        {
            $fiscal_year_number=0;
            $month=0;
            $date=0;
        }
        else
        {
            $fiscal_year_number=0;
            $month=0;
            $date=0;
        }
        $fiscal_years=$this->get_fiscal_years($fiscal_year_number, $month, $date);
        foreach($fiscal_years as $fy)
        {
            $this->db->from($this->config->item('table_pos_sale').' sale');
            $this->db->select('SUM(sale.amount_payable) sale_amount, sale.sales_payment_method');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id = sale.outlet_id and outlet_info.revision =1','INNER');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');

            $this->db->select('sale.outlet_id');
            $this->db->select('d.id district_id');
            $this->db->select('t.id territory_id');
            $this->db->select('zone.id zone_id');
            $this->db->select('zone.division_id division_id');
            if($locations['division_id']>0)
            {
                $this->db->where('zone.division_id',$locations['division_id']);
                if($locations['zone_id']>0)
                {
                    $this->db->where('zone.id',$locations['zone_id']);
                    if($locations['territory_id']>0)
                    {
                        $this->db->where('t.id',$locations['territory_id']);
                        if($locations['district_id']>0)
                        {
                            $this->db->where('d.id',$locations['district_id']);
                            if($locations['outlet_id']>0)
                            {
                                $this->db->where('outlet_info.customer_id',$locations['outlet_id']);
                            }
                        }
                    }
                }
            }
            $this->db->where('sale.date_sale >=',$fy['date_start']);
            $this->db->where('sale.date_sale <=',$fy['date_end']);
            $this->db->where('sale.status',$this->config->item('system_status_active'));
            $this->db->group_by('sales_payment_method');
            $results=$this->db->get()->result_array();
            $amount=array();
            foreach($results as $result)
            {
                if(isset($amount[$result['sales_payment_method']]))
                {
                    $amount[$result['sales_payment_method']]+=$result['sale_amount'];
                }
                else
                {
                    $amount[$result['sales_payment_method']]=$result['sale_amount'];
                }
                if(isset($amount['Total']))
                {
                    $amount['Total']+=$result['sale_amount'];
                }
                else
                {
                    $amount['Total']=$result['sale_amount'];
                }
            }
        }
        $total_amount['Total']=0;
        $total_amount['Cash']=0;
        $total_amount['Credit']=0;
        if(isset($amount['Total']))
        {
            $total_amount['Total']=$amount['Total'];
            if(isset($amount['Cash']))
            {
                $total_amount['Cash']=($amount['Cash']*100)/$amount['Total'];
            }
            if(isset($amount['Credit']))
            {
                $total_amount['Credit']=($amount['Credit']*100)/$amount['Total'];
            }
        }
        //$items[]=array('Head'=>'Total', 'Value'=>0);
        $items[]=array('Head'=>'Cash', 'Value'=>System_helper::get_string_amount($total_amount['Cash']));
        $items[]=array('Head'=>'Credit', 'Value'=>System_helper::get_string_amount($total_amount['Credit']));

        $this->json_return($items);
    }
    private function system_chart_amount_sales_vs_target()
    {
        $data=array();
        $html_id = $this->input->post('html_id');
        $data['type'] = $this->input->post('type');
        $data['value'] = $this->input->post('value');
        $data['unitInterval'] = $this->input->post('unitInterval');

        if($data['type']=='today')
        {
            $data['title']="Today (".$data['value'].' '.date('M, Y').") Sales (%) Summary";
            $fiscal_year_number=2;
            $month=0;
            $date=$data['value'];
        }
        elseif($data['type']=='month')
        {
            $data['title']="Monthly (".$this->lang->line('LABEL_MONTH_'.intval($data['value'])).") Sales (%) Summary";
            $fiscal_year_number=2;
            $month=$data['value'];
            $date=0;
        }
        elseif($data['type']=='year')
        {
            $data['title']="Current Years Sales (%) Summary";
            $fiscal_year_number=$data['value'];
            $month=0;//date('m', time());
            $date=0;
        }
        else
        {
            $fiscal_year_number=0;
            $month=0;//date('m', time());
            $date=0;
        }
        $data['fiscal_years']=$this->get_fiscal_years($fiscal_year_number, $month, $date);
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_id,"html"=>$this->load->view($this->controller_url."/amount_wise_sales_vs_target",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    public function system_get_item_chart_amount_sales_vs_target($type,$value)
    {
        $location_post=array
        (
            'division_id'=>$this->input->get('division_id'),
            'zone_id'=>$this->input->get('zone_id'),
            'territory_id'=>$this->input->get('territory_id'),
            'district_id'=>$this->input->get('district_id'),
            'outlet_id'=>$this->input->get('outlet_id'),
        );
        $locations=$this->get_locations($location_post);
        if($type=='today')
        {
            $fiscal_year_number=0;
            $month=0;
            $date=$value;
        }
        elseif($type=='month')
        {
            $fiscal_year_number=0;
            $month=$value;
            $date=0;
        }
        elseif($type=='year')
        {
            $fiscal_year_number=0;
            $month=0;
            $date=0;
        }
        else
        {
            $fiscal_year_number=0;
            $month=0;
            $date=0;
        }

        $fiscal_years=$this->get_fiscal_years($fiscal_year_number, $month, $date);
        /// achivement
        $amount_sales=0;
        foreach($fiscal_years as $fy)
        {
            $this->db->from($this->config->item('table_pos_sale').' sale');
            $this->db->select('SUM(sale.amount_payable) sale_amount, sale.sales_payment_method');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id = sale.outlet_id and outlet_info.revision =1','INNER');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');

            $this->db->select('sale.outlet_id');
            $this->db->select('d.id district_id');
            $this->db->select('t.id territory_id');
            $this->db->select('zone.id zone_id');
            $this->db->select('zone.division_id division_id');
            if($locations['division_id']>0)
            {
                $this->db->where('zone.division_id',$locations['division_id']);
                if($locations['zone_id']>0)
                {
                    $this->db->where('zone.id',$locations['zone_id']);
                    if($locations['territory_id']>0)
                    {
                        $this->db->where('t.id',$locations['territory_id']);
                        if($locations['district_id']>0)
                        {
                            $this->db->where('d.id',$locations['district_id']);
                            if($locations['outlet_id']>0)
                            {
                                $this->db->where('outlet_info.customer_id',$locations['outlet_id']);
                            }
                        }
                    }
                }
            }
            $this->db->where('sale.date_sale >=',$fy['date_start']);
            $this->db->where('sale.date_sale <=',$fy['date_end']);
            $this->db->where('sale.status',$this->config->item('system_status_active'));
            //$this->db->group_by('sales_payment_method');
            $result=$this->db->get()->row_array();
            $amount_sales+=$result['sale_amount'];
        }

        // target
        $amount_target=0;
        foreach($fiscal_years as $fy)
        {
            $this->db->from($this->config->item('table_bi_target_tsme').' item');
            $this->db->select('SUM(item.amount_target) amount_target');
            $this->db->select("CONCAT_WS('-', year, lpad(month,2,'0'), '01') AS date_string");
            $this->db->select("TIMESTAMPDIFF(SECOND, '1970-01-01', CONCAT_WS('-', year, lpad(month,2,'0'), '01')) AS date_target ");

            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = item.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');

            if($locations['division_id']>0)
            {
                $this->db->where('zone.division_id',$locations['division_id']);
                if($locations['zone_id']>0)
                {
                    $this->db->where('zone.id',$locations['zone_id']);
                    if($locations['territory_id']>0)
                    {
                        $this->db->where('t.id',$locations['territory_id']);
                    }
                }
            }
            /*$this->db->where('item.date_sale >=',$fy['date_start']);

            $this->db->where('item.date_sale <=',$fy['date_end']);
            $this->db->where('sale.status',$this->config->item('system_status_active'));*/
            //$this->db->group_by('sales_payment_method');
            $this->db->having(array('date_target >=' => $fy['date_start'], 'date_target <=' => $fy['date_end']));
            $result=$this->db->get()->row_array();
            $amount_target+=$result['amount_target'];
        }
        $items[]=array('Head'=>'Target', 'Value'=>System_helper::get_string_amount($amount_target));
        $items[]=array('Head'=>'Achivement', 'Value'=>System_helper::get_string_amount($amount_sales));
        $this->json_return($items);
    }
    private function system_invoice_amount()
    {
        $location=$this->input->post('locations');
        $location_post=array
        (
            'division_id'=>$location['division_id'],
            'zone_id'=>$location['zone_id'],
            'territory_id'=>$location['territory_id'],
            'district_id'=>$location['district_id'],
            'outlet_id'=>$location['outlet_id'],
        );
        $locations=$this->get_locations($location_post);
        $type=$this->input->post('type');
        $value=$this->input->post('value');
        if($type=='today')
        {
            $fiscal_year_number=0;
            $month=0;
            $date=$value;
        }
        elseif($type=='month')
        {
            $fiscal_year_number=0;
            $month=$value;
            $date=0;
        }
        elseif($type=='year')
        {
            $fiscal_year_number=0;
            $month=0;
            $date=0;
        }
        else
        {
            $fiscal_year_number=0;
            $month=0;
            $date=0;
        }
        $data=array();
        $fiscal_years=$this->get_fiscal_years($fiscal_year_number, $month, $date);
        foreach($fiscal_years as $fy)
        {
            $this->db->from($this->config->item('table_pos_sale').' sale');
            $this->db->select('SUM(sale.amount_payable) sale_amount, sale.sales_payment_method');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id = sale.outlet_id and outlet_info.revision =1','INNER');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');

            $this->db->select('sale.outlet_id');
            $this->db->select('d.id district_id');
            $this->db->select('t.id territory_id');
            $this->db->select('zone.id zone_id');
            $this->db->select('zone.division_id division_id');
            if($locations['division_id']>0)
            {
                $this->db->where('zone.division_id',$locations['division_id']);
                if($locations['zone_id']>0)
                {
                    $this->db->where('zone.id',$locations['zone_id']);
                    if($locations['territory_id']>0)
                    {
                        $this->db->where('t.id',$locations['territory_id']);
                        if($locations['district_id']>0)
                        {
                            $this->db->where('d.id',$locations['district_id']);
                            if($locations['outlet_id']>0)
                            {
                                $this->db->where('outlet_info.customer_id',$locations['outlet_id']);
                            }
                        }
                    }
                }
            }
            $this->db->where('sale.date_sale >=',$fy['date_start']);
            $this->db->where('sale.date_sale <=',$fy['date_end']);
            $this->db->where('sale.status',$this->config->item('system_status_active'));
            $this->db->group_by('sales_payment_method');
            $results=$this->db->get()->result_array();
            $amount=array();
            foreach($results as $result)
            {
                if(isset($amount[$result['sales_payment_method']]))
                {
                    $amount[$result['sales_payment_method']]+=$result['sale_amount'];
                }
                else
                {
                    $amount[$result['sales_payment_method']]=$result['sale_amount'];
                }
                if(isset($amount['Total']))
                {
                    $amount['Total']+=$result['sale_amount'];
                }
                else
                {
                    $amount['Total']=$result['sale_amount'];
                }
            }
        }

        /* Outstading */
        $date_end=time();

        $this->db->from($this->config->item('table_login_csetup_customer').' cus');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = cus.id','INNER');
        $this->db->select('cus.id value, cus_info.name text');
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        if($locations['division_id']>0)
        {
            $this->db->where('division.id',$locations['division_id']);
            if($locations['zone_id']>0)
            {
                $this->db->where('zone.id',$locations['zone_id']);
                if($locations['territory_id']>0)
                {
                    $this->db->where('t.id',$locations['territory_id']);
                    if($locations['district_id']>0)
                    {
                        $this->db->where('cus_info.district_id',$locations['district_id']);
                        if($locations['outlet_id']>0)
                        {
                            $this->db->where('cus_info.customer_id',$locations['outlet_id']);
                        }
                    }
                }
            }
        }
        $this->db->where('cus_info.revision',1);
        $this->db->where('cus.status !=',$this->config->item('system_status_delete'));
        $this->db->where('cus.status',$this->config->item('system_status_active'));
        $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('cus_info.revision',1);
        $this->db->order_by('cus_info.ordering','ASC');
        //$outlets=$this->db->get()->result_array();
        $results=$this->db->get()->result_array();
        $outlet_ids[0]=0;
        foreach($results as $result)
        {
            $outlet_ids[]=$result['value'];
        }
        $this->db->from($this->config->item('table_pos_sale').' sale');
        $this->db->select('sale.outlet_id');
        $this->db->select('SUM(sale.amount_payable_actual) amount_debit_total',false);
        $this->db->select('SUM(CASE WHEN sale.sales_payment_method="Cash" then sale.amount_payable_actual ELSE 0 END) amount_sale_cash',false);
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = sale.farmer_id','INNER');
        $this->db->select('sale.outlet_id');
        $this->db->where_in('sale.status',$this->config->item('system_status_active'));
        $this->db->where('sale.date_sale <=',$date_end);
        $this->db->where('farmer.amount_credit_limit >',0);
        $this->db->where_in('sale.outlet_id',$outlet_ids);
        //$this->db->group_by('sale.outlet_id');
        $result=$this->db->get()->row_array();
        $due_sales=$result['amount_debit_total']-$result['amount_sale_cash'];

        //previous payment
        $this->db->from($this->config->item('table_pos_farmer_credit_payment').' dp');
        $this->db->select('dp.outlet_id');
        $this->db->select('SUM(dp.amount) amount_payment_total',false);
        $this->db->where('dp.status',$this->config->item('system_status_active'));
        $this->db->where('dp.date_payment <=',$date_end);
        $this->db->where_in('dp.outlet_id',$outlet_ids);
        //$this->db->group_by('dp.outlet_id');
        $result=$this->db->get()->row_array();
        $payments=$result['amount_payment_total'];

        $amount['amount_credit_due']=0;
        if($due_sales>$payments)
        {
            $amount['amount_credit_due']=$due_sales-$payments;
        }

        $ajax['status']=true;
        $ajax['invoice_amount_total']=isset($amount['Total'])?System_helper::get_string_amount($amount['Total']):System_helper::get_string_amount(0);
        $ajax['invoice_amount_cash']=isset($amount['Cash'])?System_helper::get_string_amount($amount['Cash']):System_helper::get_string_amount(0);
        $ajax['invoice_amount_credit']=isset($amount['Credit'])?System_helper::get_string_amount($amount['Credit']):System_helper::get_string_amount(0);
        $ajax['invoice_amount_due']=$amount['amount_credit_due'];
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    private function get_fiscal_years($fiscal_year_number, $month, $date)
    {
        $time=time();
        $this->db->from($this->config->item('table_login_basic_setup_fiscal_year').' fy');
        $this->db->select('*');
        $this->db->where('date_start <= '.$time);
        $this->db->order_by('id','DESC');
        $this->db->limit($fiscal_year_number+1,0);
        $results = $this->db->get()->result_array();

        $fiscal_years=array();
        for($i=sizeof($results)-1; $i>=0; $i--)
        {
            $results[$i]['start_date']=date('d-m-Y',$results[$i]['date_start']);
            $results[$i]['end_date']=date('d-m-Y',$results[$i]['date_end']);
            $fiscal_years[$results[$i]['id']]=$results[$i];
        }
        if($month>0)
        {
            $num_of_months=1;//$this->input->post('num_of_months');
            foreach($fiscal_years as &$fy)
            {
                $year_start=date('Y', $fy['date_start']);
                $month_start=date('m', $fy['date_start']);
                $year_end=date('Y', $fy['date_end']);
                $month_end=date('m', $fy['date_end']);
                if($month>$month_end)
                {
                    $fy['date_start']=strtotime('01-'.$month.'-'.$year_start);
                    if(($month+$num_of_months)>12)
                    {
                        $fy['date_end']=strtotime('01-'.($month+$num_of_months-12).'-'.$year_end)-1;
                    }
                    else
                    {
                        $fy['date_end']=strtotime('01-'.($month+$num_of_months).'-'.$year_start)-1;
                    }
                }
                else
                {
                    $fy['date_start']=strtotime('01-'.$month.'-'.$year_end);

                    if(($month+$num_of_months)>12)
                    {
                        $fy['date_end']=strtotime('01-'.($month+$num_of_months-12).'-'.($year_end+1))-1;
                    }
                    else
                    {
                        $fy['date_end']=strtotime('01-'.($month+$num_of_months).'-'.$year_end)-1;
                    }
                }
                $fy['start_date']=date('d-m-Y',$fy['date_start']);
                $fy['end_date']=date('d-m-Y',$fy['date_end']);
            }
        }
        elseif($date>0)
        {
            /*$month=date('m',time());
            foreach($fiscal_years as &$fy)
            {
                $year_start=date('Y', $fy['date_start']);
                $fy['date_start']=strtotime($date.'-'.$month.'-'.$year_start);
                $fy['date_end']=strtotime(($date+1).'-'.$month.'-'.$year_start)-1;
            }*/
            $month=date('m',time());
            $year_start=date('Y', time());
            foreach($fiscal_years as &$fy)
            {
                //$year_start=date('Y', $fy['date_start']);
                $fy['date_start']=strtotime($date.'-'.$month.'-'.$year_start);
                $fy['date_end']=strtotime(($date+1).'-'.$month.'-'.$year_start)-1;

                $fy['start_date']=date('d-m-Y',$fy['date_start']);
                $fy['end_date']=date('d-m-Y',$fy['date_end']);
            }
        }
        else
        {

        }

        return $fiscal_years;
    }
    private function system_report_farmer_balance_notification()
    {
        $data=array();
        $html_id=$this->input->post('html_id');
        $reports['day_color_payment_start']=20;
        $reports['day_color_payment_interval']=10;
        $reports['day_color_sales_start']=20;
        $reports['day_color_sales_interval']=20;
        $data['options'] = $reports;
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_id,"html"=>$this->load->view($this->controller_url."/report_farmer_balanace_notification",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    private function system_get_item_report_farmer_balance_notification()
    {
        $time=time();

        $location_post=array
        (
            'division_id'=>$this->input->get('division_id'),
            'zone_id'=>$this->input->get('zone_id'),
            'territory_id'=>$this->input->get('territory_id'),
            'district_id'=>$this->input->get('district_id'),
            'outlet_id'=>$this->input->get('outlet_id'),
        );
        $locations=$this->get_locations($location_post);

        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');

        $this->db->where('outlet_info.revision',1);
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));
        if($locations['division_id']>0)
        {
            $this->db->where('zones.division_id',$locations['division_id']);
            if($locations['zone_id']>0)
            {
                $this->db->where('zones.id',$locations['zone_id']);
                if($locations['territory_id']>0)
                {
                    $this->db->where('territories.id',$locations['territory_id']);
                    if($locations['district_id']>0)
                    {
                        $this->db->where('districts.id',$locations['district_id']);
                        if($locations['outlet_id']>0)
                        {
                            $this->db->where('outlet_info.customer_id',$locations['outlet_id']);
                        }
                    }
                }
            }
        }
        $this->db->order_by('outlet_info.ordering');
        $results=$this->db->get()->result_array();

        $outlets=array();
        $outlet_ids=array(0);
        foreach($results as $result)
        {
            $outlets[$result['outlet_id']]=$result['outlet_name'];
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
        }
        //payment
        $this->db->from($this->config->item('table_pos_farmer_credit_payment') . ' payment');
        $this->db->select('MAX( payment.date_payment ) AS date_last_payment');
        $this->db->select('payment.farmer_id');
        $this->db->where_in('payment.outlet_id', $outlet_ids);
        $this->db->group_by('payment.farmer_id');
        $sub_query=$this->db->get_compiled_select();

        $this->db->from($this->config->item('table_pos_farmer_credit_payment') . ' payment');
        $this->db->select('payment.farmer_id');
        $this->db->select('payment.amount amount_last_payment');
        $this->db->select('payment.date_payment date_last_payment');
        $this->db->join('('.$sub_query.') payment_max','payment_max.farmer_id = payment.farmer_id AND payment_max.date_last_payment= payment.date_payment','INNER');
        $results=$this->db->get()->result_array();
        $payment=array();
        foreach($results as $result)
        {
            $payment[$result['farmer_id']]=$result;
        }

        $this->db->from($this->config->item('table_pos_sale').' sale');
        $this->db->select('MAX( sale.date_sale ) AS date_last_sale');
        $this->db->select('sale.farmer_id');
        $this->db->where_in('sale.outlet_id', $outlet_ids);
        $this->db->where('sale.status',$this->config->item('system_status_active'));
        $this->db->where('sale.sales_payment_method','Credit');
        $this->db->group_by('sale.farmer_id');
        $sub_query=$this->db->get_compiled_select();

        $this->db->from($this->config->item('table_pos_sale').' sale');
        $this->db->select('sale.farmer_id');
        $this->db->select('sale.amount_payable_actual amount_last_sale');
        $this->db->select('sale.date_sale date_last_sale');
        $this->db->join('('.$sub_query.') sale_max','sale_max.farmer_id = sale.farmer_id AND sale_max.date_last_sale= sale.date_sale','INNER');
        $this->db->where('sale.status',$this->config->item('system_status_active'));
        $this->db->where('sale.sales_payment_method','Credit');
        $results=$this->db->get()->result_array();

        $sales=array();
        foreach($results as $result)
        {
            $sales[$result['farmer_id']]=$result;
        }

        //dealers
        $this->db->from($this->config->item('table_pos_setup_farmer_farmer') . ' farmer');
        $this->db->select('farmer.id, farmer.name, farmer.amount_credit_limit, farmer.amount_credit_balance');

        $this->db->join($this->config->item('table_pos_setup_farmer_outlet') . ' farmer_outlet', 'farmer_outlet.farmer_id = farmer.id AND farmer_outlet.revision =1', 'INNER');
        $this->db->select('farmer_outlet.outlet_id');
        $this->db->where('farmer.amount_credit_limit > ', 0);
        $this->db->where_in('farmer_outlet.outlet_id', $outlet_ids);
        $this->db->order_by('farmer_outlet.id');
        $this->db->order_by('farmer.id DESC');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['outlet_name'] = $outlets[$item['outlet_id']];
            $item['barcode'] = $item['id'];//Barcode_helper::get_barcode_farmer($item['id']);
            $item['amount_credit_due'] = $item['amount_credit_limit'] - $item['amount_credit_balance'];
            if(isset($payment[$item['id']]))
            {
                $item['amount_last_payment'] = $payment[$item['id']]['amount_last_payment'];
                $item['date_last_payment'] = System_helper::display_date($payment[$item['id']]['date_last_payment']);
                $item['day_last_payment'] = intval(($time-$payment[$item['id']]['date_last_payment'])/(3600*24));
            }
            else
            {
                $item['amount_last_payment']=0;
                $item['date_last_payment']=0;
                $item['day_last_payment']=0;
            }

            if(isset($sales[$item['id']]))
            {
                $item['amount_last_sale'] = $sales[$item['id']]['amount_last_sale'];
                $item['date_last_sale'] = System_helper::display_date($sales[$item['id']]['date_last_sale']);
                $item['day_last_sale'] = intval(($time-$sales[$item['id']]['date_last_sale'])/(3600*24));
            }
            else
            {
                $item['amount_last_sale']=0;
                $item['date_last_sale']=0;
                $item['day_last_sale']=0;
            }
            $item['sale_due_status']='--';
            if($item['amount_credit_due']==0)
            {
                $item['sale_due_status']='No Due';
            }
            else if($item['amount_credit_due']==$item['amount_last_sale'])
            {
                $item['sale_due_status']='Due was Cleared';
            }
            else if($item['amount_credit_due']>$item['amount_last_sale'])
            {
                $item['sale_due_status']='Due was not Cleared';
            }
            else
            {
                if($item['date_last_payment']<=$item['date_last_sale'])
                {
                    $item['sale_due_status']='Due was Cleared';
                }
                else
                {
                    $item['sale_due_status']='Partial paid after invoice';
                }
            }

        }

        $this->json_return($items);
    }
    public function get_locations($location_post)
    {
        $location['division_id']=0;
        $location['zone_id']=0;
        $location['territory_id']=0;
        $location['district_id']=0;
        $location['outlet_id']=0;
        if(isset($location_post['division_id']) && $location_post['division_id']>0)
        {
            if($this->locations['division_id']>0)
            {
                $location['division_id']=$this->locations['division_id'];
            }
            else
            {
                $location['division_id']=$location_post['division_id'];
            }
            /*zone*/
            if(isset($location_post['zone_id']) && $location_post['zone_id']>0)
            {
                if($this->locations['zone_id']>0)
                {
                    $location['zone_id']=$this->locations['zone_id'];
                }
                else
                {
                    $location['zone_id']=$location_post['zone_id'];
                }
                /*territory*/
                if(isset($location_post['territory_id']) && $location_post['territory_id']>0)
                {
                    if($this->locations['territory_id']>0)
                    {
                        $location['territory_id']=$this->locations['territory_id'];
                    }
                    else
                    {
                        $location['territory_id']=$location_post['territory_id'];
                    }
                    /*districts*/
                    if(isset($location_post['district_id']) && $location_post['district_id']>0)
                    {
                        if($this->locations['district_id']>0)
                        {
                            $location['district_id']=$this->locations['district_id'];
                        }
                        else
                        {
                            $location['district_id']=$location_post['district_id'];
                        }
                        /*outlet*/
                        if(isset($location_post['outlet_id']) && $location_post['outlet_id']>0)
                        {
                            $location['outlet_id']=$location_post['outlet_id'];
                        }
                    }
                }
            }
        }
        return $location;
    }
    public function system_focusable_varieties()
    {
        //$user_locations = User_helper::get_locations();
        $location=$this->input->post('locations');
        $location_post=array
        (
            'division_id'=>$location['division_id'],
            'zone_id'=>$location['zone_id'],
            'territory_id'=>$location['territory_id'],
            'district_id'=>$location['district_id'],
            'outlet_id'=>$location['outlet_id'],
        );
        $locations=$this->get_locations($location_post);
        $data = array();

        /*------------------------------------- SEASON CODE -------------------------------------*/
        $where = array(
            "status ='". $this->config->item('system_status_active')."'"
        );
        $select = array('id', 'name','date_start','date_end','description');
        $seasons = Query_helper::get_info($this->config->item('table_bi_setup_season'), $select, $where, 0, 0, array('date_start ASC'));

        $time_assumed_today = System_helper::get_time(date("1970-m-d"));
        $current_season=array();
        foreach($seasons as &$season){
            if(($time_assumed_today >= $season['date_start']) && ($time_assumed_today <= $season['date_end']))
            {
                $current_season = $season;
                break;
            }
            else if(date('Y', $season['date_end']) > date('Y', $season['date_start']))
            {
                $season['date_end'] = strtotime('-1 years', $season['date_end']);
                $season['date_start'] = strtotime('-1 years', $season['date_start']);
                if(($time_assumed_today >= $season['date_start']) && ($time_assumed_today <= $season['date_end']))
                {
                    $current_season = $season;
                    break;
                }
            }
        }
        $data['current_season'] = $current_season;

        /*---------------------------------------- FOCUSED VARIETY ----------------------------------------*/
        $this->db->from($this->config->item('table_login_csetup_cus_info') . ' cus_info');
        $this->db->select('cus_info.customer_id outlet_id, cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = cus_info.district_id', 'INNER');
        $this->db->select('districts.id district_id, districts.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territories', 'territories.id = districts.territory_id', 'INNER');
        $this->db->select('territories.id territory_id, territories.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zones', 'zones.id = territories.zone_id', 'INNER');
        $this->db->select('zones.id zone_id, zones.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' divisions', 'divisions.id = zones.division_id', 'INNER');
        $this->db->select('divisions.id division_id, divisions.name division_name');

        $this->db->where('cus_info.revision', 1);
        $this->db->where('cus_info.type', $this->config->item('system_customer_type_outlet_id'));

        if($locations['division_id']>0)
        {
            $this->db->where('zones.division_id',$locations['division_id']);
            if($locations['zone_id']>0)
            {
                $this->db->where('zones.id',$locations['zone_id']);
                if($locations['territory_id']>0)
                {
                    $this->db->where('territories.id',$locations['territory_id']);
                    if($locations['district_id']>0)
                    {
                        $this->db->where('districts.id',$locations['district_id']);
                        if($locations['outlet_id']>0)
                        {
                            $this->db->where('cus_info.customer_id',$locations['outlet_id']);
                        }
                    }
                }
            }
        }
        $this->db->order_by('divisions.id');
        $this->db->order_by('zones.id');
        $this->db->order_by('territories.id');
        $this->db->order_by('districts.id');
        $this->db->order_by('cus_info.customer_id');

        $results_outlet = $this->db->get()->result_array();
        $current_user_outlets = array();
        $current_user_outlet_ids = array();
        foreach($results_outlet as $result_outlet)
        {
            $current_user_outlets[$result_outlet['outlet_id']] = $result_outlet;
            $current_user_outlet_ids[] = $result_outlet['outlet_id'];
        }

        $this->db->from($this->config->item('table_bi_setup_variety_focused_details') . ' details');
        $this->db->select('details.*');

        $this->db->join($this->config->item('table_bi_setup_variety_focused') . ' main', "main.id = details.focus_id AND main.status = '" . $this->config->item('system_status_active') . "'", 'INNER');
        $this->db->select('main.outlet_id, main.variety_focused_count');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', "cus_info.customer_id = main.outlet_id AND cus_info.revision=1", 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' v', 'v.id = details.variety_id', 'INNER');
        $this->db->select('v.name variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
        $this->db->select('type.id crop_type_id, type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');

        $this->db->where('details.revision', 1);
        $this->db->where_in('main.outlet_id', $current_user_outlet_ids);
        $this->db->like('details.season', ",{$current_season['id']},");

        $this->db->order_by('crop.id');
        $this->db->order_by('type.id');
        $this->db->order_by('v.id');
        $focusable_varieties = $this->db->get()->result_array();

        $rowspan =array();
        foreach($focusable_varieties as &$variety)
        {
            $variety['sales_date_start'] = Bi_helper::cultivation_date_display($variety['sales_date_start']);
            $variety['sales_date_end'] = Bi_helper::cultivation_date_display($variety['sales_date_end']);

            if(!isset($rowspan['crop'][$variety['crop_id']])){
                $rowspan['crop'][$variety['crop_id']] = 0;
            }
            if(!isset($rowspan['type'][$variety['crop_type_id']])){
                $rowspan['type'][$variety['crop_type_id']] = 0;
            }
            if(!isset($rowspan['variety'][$variety['variety_id']])){
                $rowspan['variety'][$variety['variety_id']] = 0;
            }
            $rowspan['crop'][$variety['crop_id']]++; // For calculating Crop Rows
            $rowspan['type'][$variety['crop_type_id']]++; // For calculating Crop Type Rows
            $rowspan['variety'][$variety['variety_id']]++; // For calculating Variety Rows
        }

        $data['focusable_varieties'] = $focusable_varieties;
        $data['rowspan'] = $rowspan;

        $html_id=$this->input->post('html_id');
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_id,"html"=>$this->load->view($this->controller_url."/focusable_varieties",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
}
