<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$final = array();
$CI->db->from($CI->config->item('table_login_setup_classification_varieties'));
$CI->db->select("*, DATE_FORMAT(FROM_UNIXTIME(date_created), '%b') mt, DATE_FORMAT(FROM_UNIXTIME(date_created), '%y') yr");

$CI->db->where('whose', 'ARM');
$CI->db->where('status', $CI->config->item('system_status_active'));
$row = $CI->db->get()->result_array();

foreach ($row as &$r)
{
    $r['date_created2'] = System_helper::display_date($r['date_created']);
    $count_key = $r['mt'] . '_' . $r['yr'];
    if (!isset($final[$count_key]))
    {
        $final[$count_key] = 0;
    }
    $final[$count_key] += 1;
}

$max = 0;
foreach ($final as $date => $count)
{
    $max = ($count > $max) ? $max = $count : $max;
}
?>

<div style="padding:15px; width:100%; height:100%; overflow-x:scroll">
    <div id='jqxVarietyChart' style="width:500px; height:100%"/>
</div>

<script type="text/javascript" src="<?php echo str_replace('bi_2019_20', 'login_2018_19', base_url('js/jqx/jqxchart.js')); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // prepare chart data
        var sampleData = [
            <?php
             if($final){
                 foreach($final as $key => $val){
                    echo "{ Month: '".str_replace("_",", ", $key)."', Added: ".$val."},";
                 }
             }
             ?>
        ];
        // prepare jqxChart settings
        var settings = {
            borderLineWidth: 0,
            title: "ARM Varieties",
            description: '(Month-Wise)',
            showLegend: false,
            enableAnimations: false,
            source: sampleData,
            categoryAxis: {
                dataField: 'Month',
                showGridLines: true,
                flip: false,
                textRotationAngle: 270
            },
            colorScheme: 'scheme04', // Changes color of Bars
            seriesGroups: [
                {
                    columnsGapPercent: 50,
                    type: 'column',
                    orientation: 'vertical',
                    toolTipFormatSettings: { thousandsSeparator: ',' },
                    valueAxis: {
                        flip: false,
                        maxValue: <?php echo $max; ?>,
                        displayValueAxis: true,
                        description: 'No. of ARM Varieties'
                    },
                    series: [
                        { dataField: 'Added', displayText: 'New ARM Varieties' }
                    ]
                }
            ]
        };
        // setup the chart
        $('#jqxVarietyChart').jqxChart(settings);
    });
</script>
