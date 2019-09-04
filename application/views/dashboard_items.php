<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$main_grid_class_large = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
$main_grid_class = 'col-lg-3 col-md-6 col-sm-6 col-xs-12';
?>

<div class="row">

<div class="<?php echo $main_grid_class_large; ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_10" data-report-view="sales_variety" href="#">+ Variety-wise Sales</a></label>
            </h4>
        </div>


        <div id="accordion_10" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="inner-container">

                </div>
            </div>
        </div>
    </div>
</div>

<div class="<?php echo $main_grid_class_large; ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_11" data-report-view="sales_showroom" href="#">+ Showroom-wise Sales</a></label>
            </h4>
        </div>

        <div id="accordion_11" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="inner-container" style="padding:0">

                </div>
            </div>
        </div>
    </div>
</div>

<div class="<?php echo $main_grid_class; ?>">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_1" data-report-view="performance_management" href="#">+ Performance Management</a></label>
            </h4>
        </div>

        <div id="accordion_1" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="col-xs-12" style="padding:0">
                    <div class="inner-container">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_2" data-report-view="market_insight" href="#">+ Market Insight</a></label>
            </h4>
        </div>

        <div id="accordion_2" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="col-xs-12" style="padding:0">
                    <div class="inner-container">

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="<?php echo $main_grid_class; ?>">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_4" data-report-view="consumer_analysis" href="#">+ Primary Consumer Analysis</a></label>
            </h4>
        </div>

        <div id="accordion_4" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="col-xs-12" style="padding:0">
                    <div class="inner-container">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_5" data-report-view="market_share" href="#">+ Market Share</a></label>
            </h4>
        </div>

        <div id="accordion_5" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="col-xs-12" style="padding:0">
                    <div class="inner-container">
                        <img src="<?php echo site_url('/images/mkt-share.jpg') ?>" style="width:100%;max-height:230px" alt="Image not Found"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="<?php echo $main_grid_class; ?>">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_6" href="#">+ Sales &amp; Target Achievement</a></label>
            </h4>
        </div>

        <div id="accordion_6" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="col-xs-12" style="padding:0">
                    <div class="inner-container">
                        <img src="<?php echo site_url('/images/sales-target.jpg') ?>" style="width:100%;max-height:230px" alt="Image not Found"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_7" href="#">+ Weather Status</a></label>
            </h4>
        </div>

        <div id="accordion_7" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="col-xs-12" style="padding:0">
                    <div class="inner-container">
                        <img src="<?php echo site_url('/images/weather-forecast.jpg') ?>" style="width:100%;max-height:230px" alt="Image not Found"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="<?php echo $main_grid_class; ?>">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_9" data-report-view="showroom" href="#">+ Showroom</a></label>
            </h4>
        </div>

        <div id="accordion_9" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="col-xs-12" style="padding:0">
                    <div class="inner-container">

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger report-header collapsed" data-toggle="collapse" data-target="#accordion_3" data-report-view="business_partners" href="#">+ Business Partners</a></label>
            </h4>
        </div>

        <div id="accordion_3" class="panel-collapse collapse">
            <div class="row show-grid" style="padding:0; margin:0">
                <div class="col-xs-12" style="padding:0">
                    <div class="inner-container">

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<div id="loading" style="display:none">
    <div style="height:100%; position:relative">
        <span style="position:absolute; top:40%; left:35%; font-size:1.5em">Loading...</span>
    </div>
</div>

<style>.inner-container { height: 300px                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  ......+000000000000000000000000000000000000000000+0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000....++++00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000
    }</style>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        $(document).off("click", ".report-header");

        $(document).on("click", ".report-header", function () {
            var reportType = $(this).attr("data-report-view");
            var isExpanded = !($(this).hasClass('collapsed'));

            if ((reportType != undefined) && (reportType.trim() != "" ) && isExpanded) {
                var panel_id = $(this).attr("data-target");

                var loadingText = $('div#loading').html();
                $(panel_id + ' .inner-container').html(loadingText);

                $.ajax({
                    url: "<?php echo site_url('Reports_dashboard/index/') ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data: { report_type: reportType },
                    success: function (data, status) {
                        var response = JSON.parse(data);
                        $(panel_id + ' .inner-container').html(response);
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
        });
    });
</script>
