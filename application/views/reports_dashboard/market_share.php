<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

?>

<div style="padding:15px; width:100%; height:100%; overflow-x:scroll">
    <div id='jqxMarketShareChart' style="width: 100%; height: 100%; position:relative; left:0px; top:0px;">  </div>
</div>

<script type="text/javascript" src="<?php echo str_replace('bi_2019_20', 'login_2018_19', base_url('js/jqx/jqxchart.js')); ?>"></script>

<script type="text/javascript">
    $(document).ready(function () {
        // prepare chart data as an array
        var source =
        {
            datatype: "csv",
            datafields: [
                { name: 'Company' },
                { name: 'Share' }
            ],
            url: '<?php echo site_url('/images/text_data/market_share_data.txt') ?>'
        };
        var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
        // prepare jqxChart settings
        var settings = {
            title: "Market Share (Seed)",
            description: "(ARM Versus Competitor)",
            enableAnimations: true,
            showLegend: false,
            showBorderLine: false,
            legendPosition: { left: 5, top: 5, width: 5, height: 5 },
            padding: { left: 5, top: 5, right: 5, bottom: 5 },
            titlePadding: { left: 0, top: 0, right: 0, bottom: 0 },
            source: dataAdapter,
            colorScheme: 'scheme02',
            seriesGroups:
                [
                    {
                        type: 'pie',
                        showLabels: true,
                        series:
                        [
                            {
                                displayText: 'Company',
                                dataField: 'Share',
                                labelRadius: 80,
                                initialAngle: 15,
                                radius: 60,
                                centerOffset: 0,
                                formatSettings: { sufix: '%', decimalPlaces: 1 }
                            }
                        ]
                    }
                ]
        };

        // setup the chart
        $('#jqxMarketShareChart').jqxChart(settings);

    });
</script>

