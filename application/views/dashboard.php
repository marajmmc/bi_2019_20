<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$user = User_helper::get_user();
$user_locations = User_helper::get_locations();

/*--------SEASON CODE--------*/
$where = array(
    "status ='". $CI->config->item('system_status_active')."'"
);
$select = array('name','date_start','date_end','description');
$seasons = Query_helper::get_info($CI->config->item('table_bi_setup_season'), $select, $where, 0, 0, array('date_start ASC'));

$time_today = System_helper::get_time(date("1970-m-d"));
$current_season=array();
foreach($seasons as &$season){
    if(($time_today >= $season['date_start']) && ($time_today <= $season['date_end']))
    {
        $current_season = $season;
        break;
    }
    else if(date('Y', $season['date_end']) > date('Y', $season['date_start']))
    {
        $season['date_end'] = strtotime('-1 years', $season['date_end']);
        $season['date_start'] = strtotime('-1 years', $season['date_start']);
        if(($time_today >= $season['date_start']) && ($time_today <= $season['date_end']))
        {
            $current_season = $season;
            break;
        }
    }
}

/*--------FOCUSED VARIETY CODE--------*/

$this->db->from($this->config->item('table_login_csetup_cus_info') . ' cus_info');
$this->db->select('cus_info.id outlet_id, cus_info.name outlet_name');

$this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
$this->db->select('district.id district_id, district.name district_name');

$this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
$this->db->select('territory.id territory_id, territory.name territory_name');

$this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
$this->db->select('zone.id zone_id, zone.name zone_name');

$this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
$this->db->select('division.id division_id, division.name division_name');

$this->db->where('cus_info.revision', 1);
$this->db->where('cus_info.type', $this->config->item('system_customer_type_outlet_id'));

if ($user_locations['division_id'] > 0) {
    $this->db->where('division.id', $user_locations['division_id']);
    if ($user_locations['zone_id'] > 0) {
        $this->db->where('zone.id', $user_locations['zone_id']);
        if ($user_locations['territory_id'] > 0) {
            $this->db->where('territory.id', $user_locations['territory_id']);
            if ($user_locations['district_id'] > 0) {
                $this->db->where('district.id', $user_locations['district_id']);
            }
        }
    }
}
$this->db->order_by('division.id');
$this->db->order_by('zone.id');
$this->db->order_by('territory.id');
$this->db->order_by('district.id');
$this->db->order_by('cus_info.customer_id');

$results_outlet = $this->db->get()->result_array();
$current_user_outlets = array();
$counter=1;
foreach($results_outlet as $result_outlet)
{
    $current_user_outlets[$result_outlet['outlet_id']] = $result_outlet;
    $current_user_outlets[$result_outlet['outlet_id']]['serial'] = $counter++;
}

//echo '<pre>';
//print_r($current_user_outlets);
////print_r($user_locations);
////print_r($user);
//echo '</pre>';
//die('===============');

?>

<div class="row widget">
    <div class="col-lg-9" style="padding: 0px !important; min-height: 150px">
        <div class="panel with-nav-tabs panel-default panel-tab" style=" min-height: 150px; margin-bottom: 0px !important;">
            <div class="panel-heading ">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_sales_amount" data-toggle="tab" class="dropdown tab_id_sales_amount" id="tab_id_sales_today" data-type="today" data-value="<?php echo date('d', time())?>">Today</a></li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown">Month <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php
                            for ($i = 1; $i <= 12; $i++)
                            {
                                $month=date("m", strtotime( date( 'Y-'.$i.'-01' )));
                                $month_name=date("M", strtotime( date( 'Y-'.$i.'-01' )));
                                ?>
                                <li>
                                    <a href="#tab_sales_amount" class="dropdown tab_id_sales_amount" data-toggle="tab" data-type="month" data-value="<?php echo $month?>"><?php echo $month_name?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="#tab_sales_amount" data-toggle="tab" class="dropdown tab_id_sales_amount" id="tab_id_sales_amount" data-type="year" data-value="2">Current Years</a></li>
                </ul>
            </div>
            <div class="panel-body  bg-warning">
                <div class="tab-content">
                    <div class=" fade in active" id="tab_sales_amount">
                        <div class="col-lg-4 col-xs-12 dashboard_div_invoice_amount">
                            <a href="#" class="btn btn-success btn-lg" role="button">
                                BDT. <span id="invoice_amount_total"> 125441.05 </span>
                                <br/>
                                <p>Total Amount From Invoice's</p>
                            </a>
                        </div>
                        <div class="col-lg-4 col-xs-12 dashboard_div_invoice_amount">
                            <a href="#" class="btn btn-primary btn-lg" role="button">
                                BDT. <span id="invoice_amount_cash"> 125441.05 </span>
                                <br/>
                                <p>Total Cash Amount From Invoice's</p>
                            </a>
                        </div>
                        <div class="col-lg-4 col-xs-12 dashboard_div_invoice_amount">
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
    <div class="col-lg-3 col-xs-12 bg-warning" style="padding: 5px; min-height: 150px">
        <div style="width: 100%; border-bottom: 1px green solid; margin-bottom: 2px;">
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
        <span class="app-label-bg-none">White Love</span>
        <span class="app-label-bg-none">White Love</span>
        <span class="app-label-bg-none">Snow Star</span>
        <span class="app-label-bg-none">No. 777</span>
        <span class="app-label-bg-none">Milkyway</span>
        <span class="app-label-bg-none">Quick Set (S)</span>
        <span class="app-label-bg-none">Super Early (S)</span>
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
                            for ($i = 1; $i <= 12; $i++)
                            {
                                $month=date("m", strtotime( date( 'Y-'.$i.'-01' )));
                                $month_name=date("M", strtotime( date( 'Y-'.$i.'-01' )));
                                ?>
                                <li>
                                    <a href="#tab_sales_year" class="dropdown tab_id_sales_years" data-toggle="tab" data-type="month" data-unit-interval="500" data-value="<?php echo $month?>"><?php echo $month_name?></a>
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
                            for ($i = 1; $i <= 12; $i++)
                            {
                                $month=date("m", strtotime( date( 'Y-'.$i.'-01' )));
                                $month_name=date("M", strtotime( date( 'Y-'.$i.'-01' )));
                                ?>
                                <li>
                                    <a href="#tab_invoices_year" class="dropdown tab_id_invoices_years" data-toggle="tab" data-type="month" data-unit-interval="500" data-value="<?php echo $month?>"><?php echo $month_name?></a>
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
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_invoices_year" style=" ">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3" style="padding: 0px !important; ">
        <div id="div_report_farmer_balance_notification">

        </div>
    </div>

</div>
<style>
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
</style>
<div class="clearfix"></div>
<style>
    .tab-pane
    {
        min-height: 350px;
    }
</style>

<script type="text/javascript">
    function load_invoice_amount()
    {
        $("#invoice_amount_total").html('')
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
                $("#invoice_amount_cash").html(data.invoice_amount_cash)
                $("#invoice_amount_credit").html(data.invoice_amount_credit)
            },
            error: function (xhr, desc, err)
            {.0000


            }
        });
    }
    function load_chart()
    {
        var elm_id="#tab_id_sales_today";
        $($(elm_id).attr('href')).html('')
        var url = "<?php echo site_url('Dashboard/index/chart_sales_crop_wise');?>";
        var data_post =
        {
            html_id:$(elm_id).attr('href'),
            type:$(elm_id).attr('data-type'),
            value:$(elm_id).attr('data-value'),
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
    $(document).ready(function ()
    {
        //load_invoice_amount();
        //load_chart();
        load_report_farmer_balance_notification();

        $('.tab_id_sales_amount').on('click',function()
        {
            $("#invoice_amount_total").html('')
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

</script>
