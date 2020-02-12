<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$CI->load->helper('bi_helper');

$user = User_helper::get_user();
$user_locations = User_helper::get_locations();

/*---------------------------- FISCAL YEAR ----------------------------*/
$time_today = System_helper::get_time(date('Y-m-d'));
$where = array(
    "status ='". $CI->config->item('system_status_active')."'",
    "date_start <=". $time_today,
    "date_end >=". $time_today
);
$fiscal_year_current = Query_helper::get_info($CI->config->item('table_login_basic_setup_fiscal_year'), '*', $where, 1);
$fiscal_current = $fiscal_year_current['date_start'];
$fiscal_months =array();
do
{
    $fiscal_months[date( 'm', $fiscal_current )] = date( 'M Y', $fiscal_current );
    $fiscal_current = strtotime( '+1 months', $fiscal_current);
}
while ($fiscal_current <= $fiscal_year_current['date_end']);

/*---------------------------- SEASON CODE ----------------------------*/
$where = array(
    "status ='". $CI->config->item('system_status_active')."'"
);
$select = array('id', 'name','date_start','date_end','description');
$seasons = Query_helper::get_info($CI->config->item('table_bi_setup_season'), $select, $where, 0, 0, array('date_start ASC'));

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
/*-------------------------- FOCUSED VARIETY --------------------------*/

$CI->db->from($CI->config->item('table_login_csetup_cus_info') . ' cus_info');
$CI->db->select('cus_info.customer_id outlet_id, cus_info.name outlet_name');

$CI->db->join($CI->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
$CI->db->select('district.id district_id, district.name district_name');

$CI->db->join($CI->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
$CI->db->select('territory.id territory_id, territory.name territory_name');

$CI->db->join($CI->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
$CI->db->select('zone.id zone_id, zone.name zone_name');

$CI->db->join($CI->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
$CI->db->select('division.id division_id, division.name division_name');

$CI->db->where('cus_info.revision', 1);
$CI->db->where('cus_info.type', $CI->config->item('system_customer_type_outlet_id'));

if ($user_locations['division_id'] > 0) {
    $CI->db->where('division.id', $user_locations['division_id']);
    if ($user_locations['zone_id'] > 0) {
        $CI->db->where('zone.id', $user_locations['zone_id']);
        if ($user_locations['territory_id'] > 0) {
            $CI->db->where('territory.id', $user_locations['territory_id']);
            if ($user_locations['district_id'] > 0) {
                $CI->db->where('district.id', $user_locations['district_id']);
            }
        }
    }
}
$CI->db->order_by('division.id');
$CI->db->order_by('zone.id');
$CI->db->order_by('territory.id');
$CI->db->order_by('district.id');
$CI->db->order_by('cus_info.customer_id');

$results_outlet = $CI->db->get()->result_array();
$current_user_outlets = array();
$current_user_outlet_ids = array();
foreach($results_outlet as $result_outlet)
{
    $current_user_outlets[$result_outlet['outlet_id']] = $result_outlet;
    $current_user_outlet_ids[] = $result_outlet['outlet_id'];
}

$CI->db->from($CI->config->item('table_bi_setup_variety_focused_details') . ' details');
$CI->db->select('details.*');

$CI->db->join($CI->config->item('table_bi_setup_variety_focused') . ' main', "main.id = details.focus_id AND main.status = '" . $CI->config->item('system_status_active') . "'", 'INNER');
$CI->db->select('main.outlet_id, main.variety_focused_count');

$CI->db->join($CI->config->item('table_login_csetup_cus_info') . ' cus_info', "cus_info.customer_id = main.outlet_id AND cus_info.revision=1", 'INNER');
$CI->db->select('cus_info.name outlet_name');

$CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' v', 'v.id = details.variety_id', 'INNER');
$CI->db->select('v.name variety_name');

$CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
$CI->db->select('type.id crop_type_id, type.name crop_type_name');

$CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
$CI->db->select('crop.id crop_id, crop.name crop_name');

$CI->db->where('details.revision', 1);
$CI->db->where_in('main.outlet_id', $current_user_outlet_ids);
$CI->db->like('details.season', ",{$current_season['id']},");

$CI->db->order_by('crop.id');
$CI->db->order_by('type.id');
$CI->db->order_by('v.id');
$focusable_varieties = $CI->db->get()->result_array();

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

$locations = User_helper::get_locations();

?>

<div class="row widget">
    <?php
    if($user->user_group==0)
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_NOT_ASSIGNED_GROUP');?></h3>

        </div>
    <?php
    }
    ?>
    <?php
    if($user->username_password_same)
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_USERNAME_PASSWORD_SAME');?></h3>

        </div>
    <?php
    }
    ?>
    <?php
    if($CI->is_site_offline())
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_SITE_OFFLINE');?></h3>
        </div>
    <?php
    }
    ?>
</div>

<div class="row widget">
    <div class="col-sm-2 col-xs-12">
        <?php
        if ($locations['division_id'] > 0)
        {
            ?>
            <div class="form-group">
                <label for="usr"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
                <span class="form-control"> <?php echo $locations['division_name']; ?></span>
                <input type="hidden" id="division_id" value="<?php echo $locations['division_id']; ?>" />
            </div>
        <?php
        }
        else
        {
            ?>
            <div class="form-group">
                <label for="usr"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
                <select id="division_id" class="form-control" >
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="col-sm-2 col-xs-12" id="zone_id_container" style="display: <?php if ($locations['zone_id'] > 0){echo 'block';}else{echo 'none';}?>" >
        <?php
        if ($locations['zone_id'] > 0)
        {
            ?>
            <div class="form-group">
                <label for="usr"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
                <span class="form-control"> <?php echo $locations['zone_name']; ?></span>
                <input type="hidden" id="zone_id" value="<?php echo $locations['zone_id']; ?>" />
            </div>
        <?php
        }
        else
        {
            ?>
            <div class="form-group">
                <label for="usr"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
                <select id="zone_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="col-sm-2 col-xs-12" id="territory_id_container" style="display: <?php if ($locations['territory_id'] > 0){echo 'block';}else{echo 'none';}?>" >
        <?php
        if ($locations['territory_id'] > 0)
        {
            ?>
            <div class="form-group">
                <label for="usr"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
                <span class="form-control"> <?php echo $locations['territory_name']; ?></span>
                <input type="hidden" id="territory_id" value="<?php echo $locations['territory_id']; ?>" />
            </div>
        <?php
        }
        else
        {
            ?>
            <div class="form-group">
                <label for="usr"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
                <select id="territory_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="col-sm-2 col-xs-12" id="district_id_container" style="display: <?php if ($locations['district_id'] > 0){echo 'block';}else{echo 'none';}?>" >
        <?php
        if ($locations['district_id'] > 0)
        {
            ?>
            <div class="form-group">
                <label for="usr"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
                <span class="form-control"> <?php echo $locations['district_name']; ?></span>
                <input type="hidden" id="district_id" value="<?php echo $locations['district_id']; ?>" />
            </div>
        <?php
        }
        else
        {
            ?>
            <div class="form-group">
                <label for="usr"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
                <select id="district_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="col-sm-2 col-xs-12" id="outlet_id_container" style="display: <?php if ($locations['district_id'] > 0){echo 'block';}else{echo 'none';}?>;" >
        <div class="form-group">
            <label for="usr"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?></label>
            <select id="outlet_id" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
            </select>
        </div>
    </div>
    <div class="col-sm-2 col-xs-12 pull-right" >
        <div class="form-group">
            <label for="usr">&nbsp;</label>
            <button type="button" class="btn btn-success form-control" id="btn_dashboard_view_all"> View </button>
        </div>
    </div>
</div>
<div class="row widget">
    <div class="col-lg-6" style="padding: 0px !important; min-height: 150px">
        <div class="panel with-nav-tabs panel-default panel-tab" style=" min-height: 150px; margin-bottom: 0px !important;">
            <div class="panel-heading ">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_sales_amount" data-toggle="tab" class="dropdown tab_id_sales_amount" id="tab_id_sales_amount_today" data-type="today" data-value="<?php echo date('d', time())?>">Today</a></li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown">Month <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php
                            foreach($fiscal_months as $key => $fiscal_month)
                            {
                                ?>
                                <li>
                                    <a href="#tab_sales_amount" class="dropdown tab_id_sales_amount" data-toggle="tab" data-type="month" data-unit-interval="500" data-value="<?php echo $key?>"><?php echo $fiscal_month?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="#tab_sales_amount" data-toggle="tab" class="dropdown tab_id_sales_amount" id="tab_id_sales_amount_today" data-type="year" data-value="2">Current Years</a></li>
                </ul>
            </div>
            <div class="panel-body  bg-warning">
                <div class="tab-content">
                    <div class=" fade in active">
                        <div class="col-lg-6 col-xs-12 dashboard_div_invoice_amount">
                            <a href="#" class="btn btn-success btn-lg" role="button">
                                BDT. <span id="invoice_amount_total"> 125441.05 </span>
                                <br/>
                                <p>Total Amount From Invoice's</p>
                            </a>
                        </div>
                        <div class="col-lg-6 col-xs-12 dashboard_div_invoice_amount">
                            <a href="#" class="btn btn-warning btn-lg" role="button">
                                BDT. <span id="invoice_amount_due"> 125441.05 </span>
                                <br/>
                                <p>Total Credit Amount From Invoice's</p>
                            </a>
                        </div>
                        <div class="col-lg-6 col-xs-12 dashboard_div_invoice_amount">
                            <a href="#" class="btn btn-primary btn-lg" role="button">
                                BDT. <span id="invoice_amount_cash"> 125441.05 </span>
                                <br/>
                                <p>Total Cash Amount From Invoice's</p>
                            </a>
                        </div>
                        <div class="col-lg-6 col-xs-12 dashboard_div_invoice_amount">
                            <a href="#" class="btn btn-warning btn-lg" role="button">
                                BDT. <span id="invoice_amount_credit"> 125441.05 </span>
                                <br/>
                                <p>Total Credit Amount From Invoice's</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--<div class="col-lg-1 bg-success" style="padding: 0px !important; ">
        <div style="text-align: center; background-color: #449d44; color:#fff; border: 1px solid green; border-top: 5px solid green; margin-bottom: 10px; min-height: 70px">
            <small>No of Due Invoice.</small>
            <hr style="margin: 5px !important;"/>
            <strong style="font-size: 25px; padding: 5px">3055</strong>
        </div>
        <div style="text-align: center; background-color: #449d44; color:#fff; border: 1px solid green; border-top: 5px solid green; min-height: 70px">
            <small>No of Due Invoice.</small>
            <hr style="margin: 5px !important;"/>
            <strong style="font-size: 25px; padding: 5px">3055</strong>
        </div>
    </div>-->
    <div class="col-lg-1 col-xs-12 bg-warning" style="padding: 5px; min-height: 150px">
        sdf
    </div>
    <div class="col-lg-5 col-xs-12 bg-warning" style="padding: 5px; min-height: 150px; height: 150px">
        <div style="width: 100%; border-bottom: 1px green solid; margin-bottom: 3px;">
            <small>
                <strong>Focused Crops
                    <?php if($current_season) { ?>
                    [ Season: <?php echo $current_season['name'].' ('.date('M, d',$current_season['date_start']).' - '.date('M, d',$current_season['date_end']).')'; ?> ]
                    <?php } else { ?>
                    [ Season Not Set. ]
                    <?php } ?>
                </strong>
            </small>
        </div>

        <div id="focusable_variety_container">
            <table class="table-bordered">
                <tr>
                    <th><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_DATE_START'); ?>
                    <th><?php echo $CI->lang->line('LABEL_DATE_END'); ?>
                </tr>
                <?php
                $current_crop_id = $current_type_id = $current_variety_id = -1;
                foreach($focusable_varieties as $focusable_variety)
                {
                ?>
                    <tr>
                        <?php
                        if ($current_crop_id != $focusable_variety['crop_id'])
                        {
                            $current_crop_id = $focusable_variety['crop_id'];
                            ?>
                            <td rowspan="<?php echo $rowspan['crop'][$current_crop_id]; ?>"><?php echo $focusable_variety['crop_name']; ?></td>
                        <?php
                        }
                        if ($current_type_id != $focusable_variety['crop_type_id'])
                        {
                            $current_type_id = $focusable_variety['crop_type_id'];
                        ?>
                            <td rowspan="<?php echo $rowspan['type'][$current_type_id]; ?>"><?php echo $focusable_variety['crop_type_name']; ?></td>
                        <?php
                        }
                        if ($current_variety_id != $focusable_variety['variety_id'])
                        {
                            $current_variety_id = $focusable_variety['variety_id'];
                            ?>
                            <td rowspan="<?php echo $rowspan['variety'][$current_variety_id]; ?>"><?php echo $focusable_variety['variety_name']; ?></td>
                        <?php
                        }
                        ?>
                        <td><?php echo $focusable_variety['outlet_name']; ?></td>
                        <td><?php echo $focusable_variety['sales_date_start']; ?></td>
                        <td><?php echo $focusable_variety['sales_date_end']; ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
        <!--<span class="app-label-bg-none">White Love</span>
        <span class="app-label-bg-none">White Love</span>
        <span class="app-label-bg-none">Snow Star</span>-->
    </div>
</div>
<div class="row widget tab-section-2">
    <!-- crop wise sales  -->
    <div class="col-md-6" style="padding: 0px !important; ">
        <div class="panel with-nav-tabs panel-default  panel-tab">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_sales_year" data-toggle="tab" class="dropdown tab_id_sales_years" id="tab_id_sales_today" data-type="today" data-unit-interval="10" data-value="<?php echo date('d', time())?>">Today</a></li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown">Month <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php
                            foreach($fiscal_months as $key => $fiscal_month)
                            {
                                ?>
                                <li>
                                    <a href="#tab_sales_year" class="dropdown tab_id_sales_years" data-toggle="tab" data-type="month" data-unit-interval="500" data-value="<?php echo $key?>"><?php echo $fiscal_month?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="#tab_sales_year" data-toggle="tab" class="dropdown tab_id_sales_years" id="tab_id_sales_years" data-type="year" data-unit-interval="10000" data-value="2">Last 3 Years</a></li>
                    <!--<li class="dropdown">
                        <a href="#" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#tab4primary" data-toggle="tab">Primary 4</a></li>
                            <li><a href="#tab5primary" data-toggle="tab">Primary 5</a></li>
                        </ul>
                    </li>-->
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_sales_year" style=" ">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Invoice wise sales -->
    <div class="col-md-3" style="padding: 0px !important; ">
        <div class="panel with-nav-tabs panel-default panel-tab">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_invoices_year" data-toggle="tab" class="dropdown tab_id_invoices_years" id="tab_id_invoice_today" data-type="today" data-unit-interval="10" data-value="<?php echo date('d', time())?>">Today</a></li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown">Month <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php
                            foreach($fiscal_months as $key => $fiscal_month)
                            {
                                ?>
                                <li>
                                    <a href="#tab_invoices_year" class="dropdown tab_id_invoices_years" data-toggle="tab" data-type="month" data-unit-interval="500" data-value="<?php echo $key?>"><?php echo $fiscal_month?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="#tab_invoices_year" data-toggle="tab" class="dropdown tab_id_invoices_years" id="tab_id_invoice_years" data-type="year" data-unit-interval="10000" data-value="0">Year</a></li>
                    <!--<li class="dropdown">
                        <a href="#" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#tab4primary" data-toggle="tab">Primary 4</a></li>
                            <li><a href="#tab5primary" data-toggle="tab">Primary 5</a></li>
                        </ul>
                    </li>-->
                </ul>
            </div>
            <div class="panel-body" style="min-height: 220px; height: 220px;">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_invoices_year" style=" ">&nbsp;</div>
                </div>
            </div>
        </div>
        <div class="panel with-nav-tabs panel-default panel-tab">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_amount_sale_vs_target" data-toggle="tab" class="dropdown tab_id_amount_sale_vs_target" id="tab_id_amount_sale_vs_target_today" data-type="today" data-unit-interval="10" data-value="<?php echo date('d', time())?>">Today</a></li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown">Month <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php
                            foreach($fiscal_months as $key => $fiscal_month)
                            {
                                ?>
                                <li>
                                    <a href="#tab_amount_sale_vs_target" class="dropdown tab_id_amount_sale_vs_target" data-toggle="tab" data-type="month" data-unit-interval="500" data-value="<?php echo $key?>"><?php echo $fiscal_month?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="#tab_amount_sale_vs_target" data-toggle="tab" class="dropdown tab_id_amount_sale_vs_target" id="tab_id_invoice_years" data-type="year" data-unit-interval="10000" data-value="0">Year</a></li>
                </ul>
            </div>
            <div class="panel-body" style="min-height: 220px; height: 220px;">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_amount_sale_vs_target" style=" ">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3" style="padding: 0px !important; ">
        <div id="div_report_farmer_balance_notification">

        </div>
    </div>

</div>
<div class="clearfix"></div>

<style type="text/css">
    .tab_id_notice
    {
        padding: 5px !important;
        font-size: 10px;
        font-weight: bold;
    }
    .tab-section-2 .panel-body
    {
        min-height: 505px;;
    }
    .tab-pane
    {
        min-height: 350px;
    }
    #focusable_variety_container{width:100%; overflow:scroll}
    #focusable_variety_container table{width:100%}
    #focusable_variety_container th{text-align:center}
    #focusable_variety_container td{vertical-align:top; padding:2px;}
</style>

<script type="text/javascript">

    $(document).ready(function ()
    {
        <?php
        if (!($locations['division_id'] > 0))
        {
        ?>
        $('#division_id').html(get_dropdown_with_select(system_divisions));
        <?php
        }
        if($locations['division_id'] > 0)
        {
            ?>
        $('#zone_id_container').show();
        $('#zone_id').html(get_dropdown_with_select(system_zones[<?php echo $locations['division_id']; ?>]));
        <?php
        }
        if($locations['zone_id'] > 0)
        {
            ?>
        $('#territory_id_container').show();
        $('#territory_id').html(get_dropdown_with_select(system_territories[<?php echo $locations['zone_id']; ?>]));
        <?php
        }
        if($locations['territory_id'] > 0)
        {
            ?>
        $('#district_id_container').show();
        $('#district_id').html(get_dropdown_with_select(system_districts[<?php echo $locations['territory_id']; ?>])); <?php
        }
        if($locations['district_id'] > 0)
        {
            ?>
        $('#outlet_id_container').show();
        $('#outlet_id').html(get_dropdown_with_select(system_outlets[<?php echo $locations['district_id']; ?>])); <?php
        }
        ?>
        $(document).on('change', '#division_id', function () {
            $('#zone_id').val('');
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            var division_id = $('#division_id').val();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $("#system_report_container").html('');
            if (division_id > 0) {
                if (system_zones[division_id] !== undefined) {
                    $('#zone_id_container').show();
                    $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
                }
            }
        });
        $(document).on('change', '#zone_id', function () {
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            var zone_id = $('#zone_id').val();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $("#system_report_container").html('');
            if (zone_id > 0) {
                if (system_territories[zone_id] !== undefined) {
                    $('#territory_id_container').show();
                    $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).on('change', '#territory_id', function () {
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#outlet_id_container').hide();
            $('#district_id_container').hide();
            $("#system_report_container").html('');
            var territory_id = $('#territory_id').val();
            if (territory_id > 0) {
                if (system_districts[territory_id] !== undefined) {
                    $('#district_id_container').show();
                    $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
                }
            }
        });
        $(document).on('change', '#district_id', function () {
            $('#outlet_id').val('');
            $("#system_report_container").html('');
            var district_id = $('#district_id').val();
            $('#outlet_id_container').hide();
            if (district_id > 0) {
                if (system_outlets[district_id] !== undefined) {
                    $('#outlet_id_container').show();
                    $('#outlet_id').html(get_dropdown_with_select(system_outlets[district_id]));
                }
            }
        });

        $(document).on('click', '#btn_dashboard_view_all', function () {
            load_invoice_amount();
            load_report_farmer_balance_notification();
            /*load_chart_crop_wise_sales();*/
            load_chart_invoice_payment();
            load_chart_amount_sales_vs_target();
        });
        load_invoice_amount();
        load_report_farmer_balance_notification();
        /*load_chart_crop_wise_sales();*/
        load_chart_invoice_payment();
        load_chart_amount_sales_vs_target();

        var locations =
        {
            division_id:$('#division_id').val(),
            zone_id:$('#zone_id').val(),
            territory_id:$('#territory_id').val(),
            district_id:$('#district_id').val(),
            outlet_id:$('#outlet_id').val()
        }
        var division_id = 0;
        var zone_id =0;
        var territory_id = 0;
        var district_id = 0;
        var outlet_id = 0;
        $('.tab_id_sales_amount').on('click',function()
        {
            $("#invoice_amount_total").html('')
            $("#invoice_amount_due").html('')
            $("#invoice_amount_cash").html('')
            $("#invoice_amount_credit").html('')
            var report_type = 'month'
            var url = "<?php echo site_url('Dashboard/index/invoice_amount');?>";
            var data_post =
            {

                type:$(this).attr('data-type'),
                value:$(this).attr('data-value')
            }
            $.ajax({
                url: url,
                type: 'post',
                dataType: "JSON",
                data: data_post,
                success: function (data, status)
                {
                    $("#invoice_amount_total").html(data.invoice_amount_total)
                    $("#invoice_amount_due").html(data.invoice_amount_due)
                    $("#invoice_amount_cash").html(data.invoice_amount_cash)
                    $("#invoice_amount_credit").html(data.invoice_amount_credit)
                },
                error: function (xhr, desc, err)
                {


                }
            });
        })
        $('.tab_id_sales_years').on('click',function()
        {
            $($(this).attr('href')).html('')
             var url = "<?php echo site_url('Dashboard/index/chart_sales_crop_wise');?>";
             var data=
             {
                 locations:locations,
                 html_id:$(this).attr('href'),
                 type:$(this).attr('data-type'),
                 value:$(this).attr('data-value'),
                 unitInterval:$(this).attr('data-unit-interval')
             }
             $.ajax({
                 url: url,
                 type: 'post',
                 dataType: "JSON",
                 data: data,
                 success: function (data, status)
                 {

                 },
                 error: function (xhr, desc, err)
                 {


                 }
            });

        })
        $('.tab_id_invoices_years').on('click',function()
        {
            $($(this).attr('href')).html('')
             var url = "<?php echo site_url('Dashboard/index/chart_invoice_payment_wise');?>";
             var data=
             {
                 division_id:division_id,
                 zone_id:zone_id,
                 territory_id:territory_id,
                 district_id:district_id,
                 outlet_id:outlet_id,
                 html_id:$(this).attr('href'),
                 type:$(this).attr('data-type'),
                 value:$(this).attr('data-value'),
                 unitInterval:$(this).attr('data-unit-interval')
             }
             $.ajax({
                 url: url,
                 type: 'post',
                 dataType: "JSON",
                 data: data,
                 success: function (data, status)
                 {

                 },
                 error: function (xhr, desc, err)
                 {


                 }
            });

        })
        $('.tab_id_amount_sale_vs_target').on('click',function()
        {
            $($(this).attr('href')).html('')
             var url = "<?php echo site_url('Dashboard/index/chart_amount_sales_vs_target');?>";
             var data=
             {
                 division_id:division_id,
                 zone_id:zone_id,
                 territory_id:territory_id,
                 district_id:district_id,
                 outlet_id:outlet_id,
                 html_id:$(this).attr('href'),
                 type:$(this).attr('data-type'),
                 value:$(this).attr('data-value'),
                 unitInterval:$(this).attr('data-unit-interval')
             }
             $.ajax({
                 url: url,
                 type: 'post',
                 dataType: "JSON",
                 data: data,
                 success: function (data, status)
                 {

                 },
                 error: function (xhr, desc, err)
                 {


                 }
            });

        })

    });
    function load_invoice_amount()
    {
        $("#invoice_amount_total").html('')
        $("#invoice_amount_due").html('')
        $("#invoice_amount_cash").html('')
        $("#invoice_amount_credit").html('')
        var report_type = 'month'
        var url = "<?php echo site_url('Dashboard/index/invoice_amount');?>";
        var data_post =
        {
            //html_id:$(elm_id).attr('href'),
            type:'month',
            value:'<?php echo date('m', time())?>'
        }
        $.ajax({
            url: url,
            type: 'post',
            dataType: "JSON",
            data: data_post,
            success: function (data, status)
            {
                $("#invoice_amount_total").html(data.invoice_amount_total)
                $("#invoice_amount_due").html(data.invoice_amount_due)
                $("#invoice_amount_cash").html(data.invoice_amount_cash)
                $("#invoice_amount_credit").html(data.invoice_amount_credit)
            },
            error: function (xhr, desc, err)
            {.0000


            }
        });
    }
    function load_chart_crop_wise_sales()
    {
        var elm_id="#tab_id_sales_today";
        $($(elm_id).attr('href')).html('')
        var url = "<?php echo site_url('Dashboard/index/chart_sales_crop_wise');?>";
        var data_post =
        {
            html_id:$(elm_id).attr('href'),
            //type:$(elm_id).attr('data-type'),
            //value:$(elm_id).attr('data-value'),
            type:'month',
            value:'<?php echo date('m', time())?>',
            unitInterval:$(elm_id).attr('data-unit-interval')
        }
        $.ajax({
            url: url,
            type: 'post',
            dataType: "JSON",
            data: data_post,
            success: function (data, status)
            {

            },
            error: function (xhr, desc, err)
            {

            }
        });
    }
    function load_chart_invoice_payment()
    {
        var elm_id="#tab_id_invoice_today";
        $($(elm_id).attr('href')).html('')
        var url = "<?php echo site_url('Dashboard/index/chart_invoice_payment_wise');?>";
        var data_post =
        {
            html_id:$(elm_id).attr('href'),
            //type:$(elm_id).attr('data-type'),
            //value:$(elm_id).attr('data-value'),
            type:'month',
            value:'<?php echo date('m', time())?>',
            unitInterval:$(elm_id).attr('data-unit-interval')
        }
        $.ajax({
            url: url,
            type: 'post',
            dataType: "JSON",
            data: data_post,
            success: function (data, status)
            {

            },
            error: function (xhr, desc, err)
            {

            }
        });
    }
    function load_chart_amount_sales_vs_target()
    {
        var elm_id="#tab_id_amount_sale_vs_target_today";
        $($(elm_id).attr('href')).html('')
        var url = "<?php echo site_url('Dashboard/index/chart_amount_sales_vs_target');?>";
        var data_post =
        {
            html_id:$(elm_id).attr('href'),
            //type:$(elm_id).attr('data-type'),
            //value:$(elm_id).attr('data-value'),
            type:'month',
            value:'<?php echo date('m', time())?>',
            unitInterval:$(elm_id).attr('data-unit-interval')
        }
        $.ajax({
            url: url,
            type: 'post',
            dataType: "JSON",
            data: data_post,
            success: function (data, status)
            {

            },
            error: function (xhr, desc, err)
            {

            }
        });
    }
    function load_report_farmer_balance_notification()
    {
        var elm_id="#div_report_farmer_balance_notification";
        $($(elm_id).attr('href')).html('')
        var url = "<?php echo site_url('Dashboard/index/report_farmer_balance_notification');?>";
        var data_post =
        {
            html_id:elm_id
        }
        $.ajax({
            url: url,
            type: 'post',
            dataType: "JSON",
            data: data_post,
            success: function (data, status)
            {

            },
            error: function (xhr, desc, err)
            {

            }
        });
    }
</script>
