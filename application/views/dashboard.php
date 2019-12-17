<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$user = User_helper::get_user();
$CI = & get_instance();
?>
<div class="row widget">
    <div class="col-lg-3 col-xs-12" style="padding: 5px;">
        <a href="#" class="btn btn-success btn-lg" role="button" style="border-left: 5px #297D29 solid; width: 100%; text-align: right">
            BDT. <span style="font-size: 35px; font-weight: bold;" id="invoice_amount_total"> 125441.05 </span>
            <br/>
            <span style="font-size: 12px;">Total Amount From Invoice's</span>
        </a>
    </div>
    <div class="col-lg-3 col-xs-12" style="padding: 5px;">
        <a href="#" class="btn btn-primary btn-lg" role="button" style="border-left: 5px #336795 solid; width: 100%; text-align: right">
            BDT. <span style="font-size: 35px; font-weight: bold;" id="invoice_amount_cash"> 125441.05 </span>
            <br/>
            <span style="font-size: 12px;">Total Cash Amount From Invoice's</span>
        </a>
    </div>
    <div class="col-lg-3 col-xs-12" style="padding: 5px;">
        <a href="#" class="btn btn-warning btn-lg" role="button" style="border-left: 5px #AC7A3D solid; width: 100%; text-align: right">
            BDT. <span style="font-size: 35px; font-weight: bold;" id="invoice_amount_credit"> 125441.05 </span>
            <br/>
            <span style="font-size: 12px;">Total Credit Amount From Invoice's</span>
        </a>
    </div>
    <div class="col-lg-3 col-xs-12" style="padding: 5px;">
        <div style="width: 100%; border-bottom: 1px green solid; margin-bottom: 2px;">
            <small> <strong>Focused Crops [Season: 1st Jan - 31 Mar]</strong> </small>
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
<div class="row widget">
    <!-- crop wise sales  -->
    <div class="col-md-9">
        <div class="panel with-nav-tabs panel-default">
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
                    <div class="tab-pane fade in active" id="tab_sales_year">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Invoice wise sales -->
    <div class="col-md-3">
        <div class="panel with-nav-tabs panel-default">
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
                    <div class="tab-pane fade in active" id="tab_invoices_year">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>

</div>

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
        var url = "<?php echo site_url('Dashboard/index/invoice_amount');?>";

        $.ajax({
            url: url,
            type: 'post',
            dataType: "JSON",
            //data: data_post,
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
    $(document).ready(function ()
    {
        load_invoice_amount();
        //load_chart();

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

        /*$('#tab_id_invoice_years').on('click',function()
        {
            var url = "<?php //echo site_url('Dashboard/system_get_items_invoice_payment');?>";
            var source =
            {
                datatype: "json",
                datafields:
                [
                    { name: 'Browser' },
                    { name: 'Share' }
                ],
                url:url
            };

            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });

            // prepare jqxChart settings
            var settings = {
                title: "Primary Consumer Analysis",
                description: "(Cash & Credit Payment (%) Summary )",
                enableAnimations: true,
                showLegend: true,
                showBorderLine: false,
                *//*legendLayout: { left: 50, top: 310, width: 0, height: 350, flow: 'horizontal' },*//*
                padding: { left: 5, top: 0, right: 5, bottom: 5 },
                titlePadding: { left: 0, top: 0, right: 0, bottom: 0 },
                source: dataAdapter,
                colorScheme: 'scheme08',
                seriesGroups:
                    [
                        {
                            type: 'pie',
                            showLabels: true,
                            series:
                                [
                                    {
                                        dataField: 'Share',
                                        displayText: 'Browser',
                                        labelRadius: 120,
                                        initialAngle: 15,
                                        radius: 95,
                                        centerOffset: 0,
                                        formatFunction: function (value)
                                        {
                                            if (isNaN(value))
                                                return value;
                                            return parseFloat(value) + '%';
                                        },
                                    }
                                ]
                        }
                    ]
            };

            // setup the chart
            $('#jqxChartInvoiceYear').jqxChart(settings);
        })*/
    });

</script>
