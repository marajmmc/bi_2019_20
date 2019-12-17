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
    }
    public function index($action="",$type="", $value=0)
    {
        if($action=="chart_sales_crop_wise")
        {
            $this->system_chart_sales_crop_wise();
        }
        elseif($action=="get_item_chart_sales_crop_wise")
        {
            $this->system_get_items_sales_crop_wise($type, $value);
        }
        elseif($action=="chart_invoice_payment_wise")
        {
            $this->system_chart_invoice_payment_wise();
        }
        elseif($action=="get_item_chart_invoice_payment_wise")
        {
            $this->system_get_item_chart_invoice_payment_wise($type, $value);
        }
        elseif($action=="invoice_amount")
        {
            $this->system_invoice_amount();
        }

    }

    private function system_chart_sales_crop_wise()
    {
        $data=array();
        $html_id = $this->input->post('html_id');
        $data['type'] = $this->input->post('type');
        $data['value'] = $this->input->post('value');
        $data['unitInterval'] = $this->input->post('unitInterval');

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
        $ajax['system_content'][]=array("id"=>$html_id,"html"=>$this->load->view($this->controller_url."/sales_crop_wise",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    private function system_get_items_sales_crop_wise($type,$value)
    {
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
    private function system_chart_invoice_payment_wise()
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
        $ajax['system_content'][]=array("id"=>$html_id,"html"=>$this->load->view($this->controller_url."/invoice_payment_wise",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    public function system_get_item_chart_invoice_payment_wise($type,$value)
    {
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
            if($this->locations['division_id']>0)
            {
                $this->db->where('zone.division_id',$this->locations['division_id']);
                if($this->locations['zone_id']>0)
                {
                    $this->db->where('zone.id',$this->locations['zone_id']);
                    if($this->locations['territory_id']>0)
                    {
                        $this->db->where('t.id',$this->locations['territory_id']);
                        if($this->locations['district_id']>0)
                        {
                            $this->db->where('d.id',$this->locations['district_id']);
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
    private function system_invoice_amount()
    {
        $data=array();
        $fiscal_year_number=0;
        $month=0;//date('m', time());
        $date=0;
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
            if($this->locations['division_id']>0)
            {
                $this->db->where('zone.division_id',$this->locations['division_id']);
                if($this->locations['zone_id']>0)
                {
                    $this->db->where('zone.id',$this->locations['zone_id']);
                    if($this->locations['territory_id']>0)
                    {
                        $this->db->where('t.id',$this->locations['territory_id']);
                        if($this->locations['district_id']>0)
                        {
                            $this->db->where('d.id',$this->locations['district_id']);
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
        $ajax['status']=true;
        $ajax['invoice_amount_total']=isset($amount['Total'])?System_helper::get_string_amount($amount['Total']):0;
        $ajax['invoice_amount_cash']=isset($amount['Cash'])?System_helper::get_string_amount($amount['Cash']):0;
        $ajax['invoice_amount_credit']=isset($amount['Credit'])?System_helper::get_string_amount($amount['Credit']):0;
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
            }
        }
        elseif($date>0)
        {
            $month=date('m',time());
            foreach($fiscal_years as &$fy)
            {
                $year_start=date('Y', $fy['date_start']);
                $fy['date_start']=strtotime($date.'-'.$month.'-'.$year_start);
                $fy['date_end']=strtotime(($date+1).'-'.$month.'-'.$year_start)-1;
            }
        }
        else
        {

        }
        return $fiscal_years;
    }
}
