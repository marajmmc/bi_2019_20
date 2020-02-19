<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<div >
    <div id='jqxChartSalesYears' style="width:100%; height:500px; position: relative; left: 0px; top: 0px;">

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
        var url = "<?php echo site_url('Dashboard/index/get_items_chart_sales_crop_wise_last_three_years/'.$type.'/'.$value);?>";
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'crop' },
                { name: 'target' },
                <?php
                foreach($fiscal_years as $fy)
                {
                    ?>
                { name: '<?php echo str_replace('-','_',$fy['name'])?>' },
                <?php
            }
            ?>
            ],
            url:url,
            data:JSON.parse(JSON.stringify(locations))
        };
        var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error);} });
        // prepare jqxChart settings
        var settings = {
            title: "Crop Wise Sales",
            description: "Comparison <?php echo $title?> ",
            enableAnimations: true,
            showLegend: true,
            padding: { left: 5, top: 5, right: 5, bottom: 5 },
            titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
            source: dataAdapter,
            categoryAxis:
            {
                text: 'Category Axis',
                textRotationAngle: -60,
                dataField: 'crop',
                showTickMarks: true,
                tickMarksInterval: 1,
                tickMarksColor: '#888888',
                unitInterval: 1,
                showGridLines: true,
                gridLinesInterval: 1,
                gridLinesColor: '#888888',
                axisSize: 'auto'
            },
            colorScheme: 'scheme01',
            seriesGroups:
                [
                    {
                        type: 'column',
                        //useGradient: false,
                        valueAxis:
                        {
                            unitInterval: <?php echo $unitInterval;?>,
                            displayValueAxis: true,
                            description: 'Quantity (kg)',
                            //descriptionClass: 'css-class-name',
                            axisSize: 'auto',
                            tickMarksColor: '#888888'
                        },
                        series: [
                            {dataField: 'target', displayText:'Target'},
                            <?php
                            foreach($fiscal_years as $fy)
                            {
                                ?>
                            { dataField: '<?php echo str_replace('-','_',$fy['name'])?>', displayText: '<?php echo $fy['name']?>' },
                            <?php
                        }
                        ?>
                        ]
                    }
                ]
        };
        $('#jqxChartSalesYears').jqxChart(settings);
    });
</script>

