<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$CI->load->helper('bi_helper');

$user = User_helper::get_user();
$locations = User_helper::get_locations();

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
                    <li><a href="#tab_sales_amount" data-toggle="tab" class="dropdown tab_id_sales_amount" id="tab_id_sales_amount_today" data-type="today" data-value="<?php echo date('d', time())?>" onclick="load_invoice_amount('today', '<?php echo date('d', time())?>')">Today</a></li>
                    <li class="dropdown active">
                        <a href="#" data-toggle="dropdown" class="dropdown">Month <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php
                            foreach($fiscal_months as $key => $fiscal_month)
                            {
                                ?>
                                <li class="<?php if($key==date('m',time())){echo "active";}?>">
                                    <a href="#tab_sales_amount" class="dropdown tab_id_sales_amount" data-toggle="tab" data-type="month" data-unit-interval="500" data-value="<?php echo $key?>" onclick="load_invoice_amount('month', '<?php echo $key?>')"><?php echo $fiscal_month?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="#tab_sales_amount" data-toggle="tab" class="dropdown tab_id_sales_amount" id="tab_id_sales_amount_today" data-type="year" data-value="2" onclick="load_invoice_amount('year', 2)">Current Years</a></li>
                </ul>
            </div>
            <div class="panel-body  bg-warning">
                <div class="tab-content">
                    <div class=" fade in active">
                        <div class="col-lg-6 col-xs-12 dashboard_div_invoice_amount">
                            <a href="#" class="btn btn-success btn-lg" role="button">
                                BDT. <span id="invoice_amount_total"> 0.00 </span>
                                <br/>
                                <p>Total Amount From Invoice's</p>
                            </a>
                        </div>
                        <div class="col-lg-6 col-xs-12 dashboard_div_invoice_amount" id="dashboard_div_invoice_amount_due">
                            <a href="#" class="btn btn-danger btn-lg" role="button">
                                BDT. <span id="invoice_amount_due"> 0.00 </span>
                                <br/>
                                <p>Amount of Total Outstanding Balance</p>
                            </a>
                        </div>
                        <div class="col-lg-6 col-xs-12 dashboard_div_invoice_amount">
                            <a href="#" class="btn btn-primary btn-lg" role="button">
                                BDT. <span id="invoice_amount_cash"> 0.00 </span>
                                <br/>
                                <p>Total Cash Amount From Invoice's</p>
                            </a>
                        </div>
                        <div class="col-lg-6 col-xs-12 dashboard_div_invoice_amount">
                            <a href="#" class="btn btn-warning btn-lg" role="button">
                                BDT. <span id="invoice_amount_credit"> 0.00 </span>
                                <br/>
                                <p>Total Credit Amount From Invoice's</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Invoice wise sales -->
    <div class="col-md-3" style="padding: 0px !important; ">
        <div class="panel with-nav-tabs panel-default panel-tab">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li><a href="#tab_chart_invoice_payment_cash_credit" data-toggle="tab" class="dropdown" data-toggle="tab" onclick="load_chart_invoice_payment_cash_credit('today', <?php echo date('d', time())?>)">Today</a></li>
                    <li class="dropdown active">
                        <a href="#" data-toggle="dropdown" class="dropdown">Month <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php
                            foreach($fiscal_months as $key => $fiscal_month)
                            {
                                ?>
                                <li class="<?php if($key==date('m',time())){echo "active";}?>">
                                    <a href="#tab_chart_invoice_payment_cash_credit" class="dropdown" data-toggle="tab" onclick="load_chart_invoice_payment_cash_credit('month', <?php echo $key?>)"><?php echo $fiscal_month?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="#tab_chart_invoice_payment_cash_credit" data-toggle="tab" class="dropdown " onclick="load_chart_invoice_payment_cash_credit('year', 0)">Year</a></li>
                </ul>
            </div>
            <div class="panel-body" style="min-height: 206px; height: 206px;">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_chart_invoice_payment_cash_credit" style=" ">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3" style="padding: 0px !important; ">
        <div class="panel with-nav-tabs panel-default panel-tab">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li><a href="#tab_chart_amount_sales_vs_target" data-toggle="tab" class="dropdown" onclick="load_chart_amount_sales_vs_target('today', <?php echo date('d', time())?>)">Today</a></li>
                    <li class="dropdown active">
                        <a href="#" data-toggle="dropdown" class="dropdown">Month <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php
                            foreach($fiscal_months as $key => $fiscal_month)
                            {
                                ?>
                                <li class="<?php if($key==date('m',time())){echo "active";}?>">
                                    <a href="#tab_chart_amount_sales_vs_target" class="dropdown" data-toggle="tab" onclick="load_chart_amount_sales_vs_target('month', <?php echo $key?>)"><?php echo $fiscal_month?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="#tab_chart_amount_sales_vs_target" data-toggle="tab" class="dropdown"  onclick="load_chart_amount_sales_vs_target('year', 0)">Year</a></li>
                </ul>
            </div>
            <div class="panel-body" style="min-height: 206px; height: 206px;">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_chart_amount_sales_vs_target" style=" ">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="row widget tab-section-2">
    <!-- Focusable variety -->
    <div class="col-lg-6 col-xs-12 bg-warning" style="padding:5px; min-height: 150px; height: 250px">
        <div id="div_focusable_varieties">

        </div>
    </div>
    <!-- Farmer balance Notification -->
    <div class="col-md-6" style="padding: 0px !important; ">
        <div id="div_report_farmer_balance_notification">

        </div>
    </div>
</div>

<div class="row widget tab-section-2">
    <!-- Crop wise three years sales -->
    <div class="col-md-12" style="padding: 0px !important; ">
        <div class="panel with-nav-tabs panel-default  panel-tab">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li><a href="#tab_chart_sales_crop_wise_last_three_years" data-toggle="tab" class="dropdown" onclick="load_chart_crop_wise_sales_last_three_years('today', <?php echo date('d', time())?>, 10)">Today</a></li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown">Month <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php
                            foreach($fiscal_months as $key => $fiscal_month)
                            {
                                ?>
                                <li>
                                    <a href="#tab_chart_sales_crop_wise_last_three_years" data-toggle="tab" class="dropdown" onclick="load_chart_crop_wise_sales_last_three_years('month', <?php echo $key?>, 500)"><?php echo $fiscal_month?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="#tab_chart_sales_crop_wise_last_three_years" data-toggle="tab" class="dropdown" onclick="load_chart_crop_wise_sales_last_three_years('year', 2, 10000)">Last 3 Years</a></li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_chart_sales_crop_wise_last_three_years" style=" ">&nbsp;</div>
                </div>
            </div>
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
    #div_focusable_varieties {height:100%; overflow: scroll;}
    #div_focusable_varieties table {width:100%}
    #div_focusable_varieties th {text-align:center}
    #div_focusable_varieties td {vertical-align:top; padding:3px; white-space:nowrap}
</style>

<script type="text/javascript">

    $(document).ready(function ()
    {
        $(document).on('click', '#btn_dashboard_view_all', function () {
            load_all_function('search')
        });
        load_all_function('on_load')

        setInterval(function()
        {
            load_all_function('interval')
        }, 10000);

    });
    function load_all_function(channel)
    {
        if(channel=="search")
        {
            load_invoice_amount('month','<?php echo date('m', time())?>');
            load_focusable_varieties();
            /*load_chart_crop_wise_sales_last_three_years('month', '<?php //echo date('m', time())?>', 500)*/
            load_chart_invoice_payment_cash_credit('month','<?php echo date('m', time())?>');
            load_chart_amount_sales_vs_target('month','<?php echo date('m', time())?>');
            load_report_farmer_balance_notification();
        }
        else if(channel=="on_load")
        {
            load_invoice_amount('month','<?php echo date('m', time())?>');
            load_focusable_varieties();
            //load_chart_crop_wise_sales_last_three_years('month', '<?php //echo date('m', time())?>', 500)
            load_chart_invoice_payment_cash_credit('month','<?php echo date('m', time())?>');
            load_chart_amount_sales_vs_target('month','<?php echo date('m', time())?>');
            load_report_farmer_balance_notification();
        }
        else if(channel=="interval1")
        {
            load_invoice_amount('month','<?php echo date('m', time())?>');
            //load_focusable_varieties();
            /*load_chart_crop_wise_sales_last_three_years('month', '<?php //echo date('m', time())?>', 500)*/
            load_chart_invoice_payment_cash_credit('month','<?php echo date('m', time())?>');
            load_chart_amount_sales_vs_target('month','<?php echo date('m', time())?>');
            load_report_farmer_balance_notification();
        }
        else
        {

        }
    }
    function load_invoice_amount(type, value)
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
            type:type,
            value:value,
            locations:locations()
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
        setTimeout(function()
        {
            //$( "#dashboard_div_invoice_amount_due" ).effect( "fade" );
            $( "#invoice_amount_due" ).effect( "pulsate" );
        }, 5000);

    }
    function load_focusable_varieties()
    {
        var elm_id="#div_focusable_varieties";
        $(elm_id).html('');
        var url = "<?php echo site_url('Dashboard/index/focusable_varieties');?>";
        var data_post =
        {
            html_id:elm_id,
            locations:locations()
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
    function load_chart_crop_wise_sales_last_three_years(type, value, unitInterval)
    {
        $("#tab_chart_sales_crop_wise_last_three_years").html('')
        var url = "<?php echo site_url('Dashboard/index/chart_sales_crop_wise_last_three_years');?>";
        var data_post =
        {
            html_id:"#tab_chart_sales_crop_wise_last_three_years",
            type:type,
            value:value,
            unitInterval:unitInterval,
            locations:locations()
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
    function load_chart_invoice_payment_cash_credit(type, value)
    {
        var elm_id="#tab_chart_invoice_payment_cash_credit";
        $(elm_id).html('')
        var url = "<?php echo site_url('Dashboard/index/chart_invoice_payment_wise_cash_credit');?>";
        var data_post =
        {
            html_id:elm_id,
            //type:$(elm_id).attr('data-type'),
            //value:$(elm_id).attr('data-value'),
            type:type,
            value:value,
            //unitInterval:$(elm_id).attr('data-unit-interval')
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
    function load_chart_amount_sales_vs_target(type, value)
    {
        var elm_id="#tab_chart_amount_sales_vs_target";
        $(elm_id).html('')
        var url = "<?php echo site_url('Dashboard/index/chart_amount_sales_vs_target');?>";
        var data_post =
        {
            html_id:elm_id,
            //type:$(elm_id).attr('data-type'),
            //value:$(elm_id).attr('data-value'),
            type:type,
            value:value,
            //unitInterval:$(elm_id).attr('data-unit-interval')
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
        $(elm_id).html('')
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
    function locations()
    {
        var locations =
        {
            division_id:$('#division_id').val(),
            zone_id:$('#zone_id').val(),
            territory_id:$('#territory_id').val(),
            district_id:$('#district_id').val(),
            outlet_id:$('#outlet_id').val()
        }
        /*var division_id = 0;
        var zone_id =0;
        var territory_id = 0;
        var district_id = 0;
        var outlet_id = 0;*/
        return locations;
    }
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
    });
</script>
