<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$user = User_helper::get_user();

$current_time = System_helper::get_time(date("Y-m-d"));
$where = array(
    "date_start <=". $current_time,
    "date_end >=". $current_time,
    "status ='". $CI->config->item('system_status_active')."'"
);
$season = Query_helper::get_info($CI->config->item('table_bi_setup_season'), array('*'), $where, 1); System_helper::display_date($current_time);
?>

<div class="row widget">
    <div class="col-lg-8" style="padding-right: 0px !important; min-height: 200px">
        <div class="panel with-nav-tabs panel-default" style=" min-height: 200px">
            <div class="panel-heading">
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
    <div class="col-lg-1 bg-success" style="padding: 0px !important; ">
        <div style="text-align: center; background-color: #449d44; color:#fff; border: 1px solid green; border-top: 5px solid green; margin-bottom: 20px; min-height: 90px">
            <small>No of Due Invoice.</small>
            <hr style="margin: 5px !important;"/>
            <strong style="font-size: 25px; padding: 5px">3055</strong>
        </div>
        <div style="text-align: center; background-color: #449d44; color:#fff; border: 1px solid green; border-top: 5px solid green; min-height: 90px">
            <small>No of Due Invoice.</small>
            <hr style="margin: 5px !important;"/>
            <strong style="font-size: 25px; padding: 5px">3055</strong>
        </div>
    </div>
    <div class="col-lg-3 col-xs-12 bg-info" style="padding: 5px; min-height: 200px">
        <div style="width: 100%; border-bottom: 1px green solid; margin-bottom: 2px;">
            <small> <strong>Focused Crops [ Season: <?php echo $season['name'].' ('.date('M, d',$season['date_start']).' - '.date('M, d',$season['date_end']).')'; ?> ]</strong> </small>
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
    <div class="col-md-6">
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
    <div class="col-md-3">
        <?php
        if($user->user_group>1)
        {
            $user_group=','.$user->user_group.',';
            $CI->db->where("item.user_group_ids LIKE '%$user_group%'");
        }
        $CI->db->from($CI->config->item('table_pos_setup_notice_request').' item');
        $CI->db->select('item.*');
        $CI->db->join($CI->config->item('table_pos_setup_notice_types').' type','type.id=item.type_id','INNER');
        $CI->db->select('type.name notice_type');
        //$CI->db->where('item.type_id',$id);
        /*$CI->db->where('item.status',$CI->config->item('system_status_active'));
        $CI->db->where('item.status_approve',$CI->config->item('system_status_approved'));
        $CI->db->where('item.expire_time >=',time());*/
        //$CI->db->order_by('item.ordering','ASC');
        $CI->db->order_by('item.id','DESC');
        $results=$CI->db->get()->result_array();
        //echo $CI->db->last_query();
        $notice_type_count=array();
        $notice_type_names=array();
        $notices=array();
        foreach($results as $result)
        {
            if(isset($notice_type_count[$result['type_id']]))
            {
                $notice_type_count[$result['type_id']]+=1;
            }
            else
            {
                $notice_type_count[$result['type_id']]=1;
            }
            $notice_type_names[$result['type_id']]=$result['notice_type'];
            $notices[$result['type_id']][]=$result;
        }
        ?>
        <div class="panel with-nav-tabs panel-default">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab_notice_0" data-toggle="tab" class="dropdown tab_id_notice" id="" >
                            Event (1)
                        </a>
                    </li>
                    <?php
                    $div_active=2;
                    ksort($notice_type_names);
                    foreach($notice_type_names as $key=>$notice_type_name)
                    {
                        ?>
                        <li class="<?php if($div_active==1){echo 'active';}?>">
                            <a href="#tab_notice_<?php echo $key?>" data-toggle="tab" class="dropdown tab_id_notice" id="" >
                                <?php echo $notice_type_name?> (<?php echo isset($notice_type_count[$key])?$notice_type_count[$key]:'';?>)
                            </a>
                        </li>
                    <?php
                        ++$div_active;
                    }
                    ?>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_notice_0">
                        <strong>Best of Sale On This Month (ESME)</strong>
                        <hr/>
                        <img alt="Logo" height="200" class="site_logo pull-left" src="http://localhost/login_2018_19/images/logo.png">
                        &nbsp; Md. Xyz Abcd
                    </div>
                    <?php
                    $div_active=2;
                    ksort($notice_type_names);
                    foreach($notice_type_names as $key=>$notice_type_name)
                    {
                    ?>
                        <div class="tab-pane fade in <?php if($div_active==1){echo 'active';}?>" id="tab_notice_<?php echo $key?>">
                            <?php echo $key?>
                        </div>
                    <?php
                        ++$div_active;
                    }
                    ?>

                </div>
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
    </style>
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
