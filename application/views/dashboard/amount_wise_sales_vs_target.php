<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<div>
    <div id='jqxChartAmountWiseSalesVsTarget' style="width:100%; height:200px; position: relative; left: 0px; top: 0px;">

    </div>
</div>
<script type="text/javascript">


    jQuery(document).ready(function()
    {
        var locations={
            division_id: $('#division_id').val(),
            zone_id: $('#zone_id').val(),
            territory_id: $('#territory_id').val(),
            district_id: $('#district_id').val(),
            outlet_id: $('#outlet_id').val(),
        }

        var url = "<?php echo site_url('Dashboard/index/get_item_chart_amount_sales_vs_target/'.$type.'/'.$value);?>";
        var source =
        {
            datatype: "json",
            datafields:
            [
                { name: 'Head' },
                { name: 'Value' }
            ],
            url:url,
            data:JSON.parse(JSON.stringify(locations))
        };

        var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });

        // prepare jqxChart settings
        var settings =
        {
            title: "Target Vs Achivement Analysis",
            description: "(<?php echo $title?> )",
            enableAnimations: true,
            showLegend: true,
            showBorderLine: false,
            /*legendLayout: { left: 50, top: 310, width: 0, height: 350, flow: 'horizontal' },*/
            padding: { left: 5, top: 0, right: 5, bottom: 5 },
            titlePadding: { left: 0, top: 0, right: 0, bottom: 0 },
            source: dataAdapter,
            colorScheme: 'scheme05',
            seriesGroups:
            [
                {
                    type: 'pie',
                    showLabels: true,
                    series:
                        [
                            {
                                dataField: 'Value',
                                displayText: 'Head',
                                labelRadius: 60,
                                initialAngle: 15,
                                radius: 50,
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
        $('#jqxChartAmountWiseSalesVsTarget').jqxChart(settings);
    });
</script>
