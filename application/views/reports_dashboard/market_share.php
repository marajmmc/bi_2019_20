<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

?>

<div style="width:100%; height:100%; overflow-x:scroll">
    <div id='jqxMarketShareChart' style="width: 100%; height: 330px; position:relative; left:0px; top:0px;">  </div>
</div>

<script type="text/javascript" src="<?php echo str_replace('bi_2019_20', 'login_2018_19', base_url('js/jqx/jqxchart.js')); ?>"></script>

<script type="text/javascript">
    $(document).ready(function () {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        // prepare chart data as an array
        var dataSource = [
              {company:"ARM", percentage: 35},
              {company:"Syngenta", percentage: 20},
              {company:"ACI", percentage: 15},
              {company:"Supreme Seed", percentage: 18},
              {company:"United Seed", percentage: 4},
              {company:"Metal Seed", percentage: 8}
        ];

        // prepare jqxChart settings
        var settings = {
            title: "Market Share (Seed)",
            description: "(ARM Versus Competitor)",
            enableAnimations: true,
            showLegend: true,
            showBorderLine: true,
            padding: { left: 5, top: 5, right: 5, bottom: 5 },
            titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
            source: dataSource,

            colorScheme: 'scheme03',
            seriesGroups:
                [
                    {
                        type: 'pie',
                        showLabels: true,
                        series:
                            [
                                {
                                    dataField: 'percentage',
                                    displayText: 'company',
                                    labelRadius: 100,
                                    initialAngle: 15,
                                    radius: 85,
                                    centerOffset: 0,
                                    formatFunction: function (value) {
                                        if (isNaN(value))
                                            return value;
                                        return parseFloat(value) + '%';
                                    }
                                }
                            ]
                    }
                ]
        };

        // setup the chart
        $('#jqxMarketShareChart').jqxChart(settings);

    });
</script>

