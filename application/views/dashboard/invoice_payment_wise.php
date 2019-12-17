<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<div style=" overflow-x: scroll">
    <div id='jqxChartInvoiceYear' style="width:100%; height:500px; position: relative; left: 0px; top: 0px;">

    </div>
</div>
<script type="text/javascript">


    jQuery(document).ready(function()
    {
        var url = "<?php echo site_url('Dashboard/index/get_item_chart_invoice_payment_wise/'.$type.'/'.$value);?>";
        var source =
        {
            datatype: "json",
            datafields:
            [
                { name: 'Head' },
                { name: 'Value' }
            ],
            url:url
        };

        var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });

        // prepare jqxChart settings
        var settings =
        {
            title: "Primary Consumer Analysis",
            description: "(<?php echo $title?> )",
            enableAnimations: true,
            showLegend: true,
            showBorderLine: false,
            /*legendLayout: { left: 50, top: 310, width: 0, height: 350, flow: 'horizontal' },*/
            padding: { left: 5, top: 0, right: 5, bottom: 5 },
            titlePadding: { left: 0, top: 0, right: 0, bottom: 0 },
            source: dataAdapter,
            colorScheme: 'scheme11',
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
    });
</script>
