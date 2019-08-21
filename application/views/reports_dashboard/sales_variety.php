<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$bar_width = 20;
$from_date = date('2018-01-01'); // Y-m-01

$CI->db->from($CI->config->item('table_pos_sale_details') . ' sd');
$CI->db->select("sd.variety_id, (SUM(sd.pack_size)/1000) AS quantity_sold");

$CI->db->join($CI->config->item('table_pos_sale') . ' s', 's.id = sd.sale_id');

$CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' v', 'v.id = sd.variety_id');
$CI->db->select("v.name variety_name");

$CI->db->where('s.date_sale >=', System_helper::get_time($from_date));
$CI->db->group_by('sd.variety_id');
$results = $CI->db->get()->result_array();
?>

<div style="padding:15px; width:100%; height:100%; overflow-x:scroll">

    <div id='jqxVarietySalesChart' style="width:<?php echo $bar_width * (count($results)) ?>px; height:600px"/>

</div>

<script type="text/javascript" src="<?php echo str_replace('bi_2019_20', 'login_2018_19', base_url('js/jqx/jqxchart.js')); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // prepare chart data
        var sampleData = [
            <?php
            $max=0;
             if($results){
                 foreach($results as $result){
                    $title = 'ID: '.$result['variety_id'];
                    echo "{ varietyName: '".$result['variety_name']."', soldQuantity: ".$result['quantity_sold']."},";
                    $max = ($result['quantity_sold'] > $max)? $result['quantity_sold']:$max;
                 }
             }
             ?>
        ];
        // prepare jqxChart settings
        var settings = {
            borderLineWidth: 0,
            title: "Variety-wise Sales",
            description: '( Sales from <?php echo date('M d, Y', strtotime($from_date)); ?> )',
            showLegend: false,
            enableAnimations: false,
            source: sampleData,
            categoryAxis: {
                dataField: 'varietyName',
                showGridLines: true,
                flip: false,
                textRotationAngle: -90
            },
            colorScheme: 'scheme03', // Changes color of Bars
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
                        description: 'Quantity Sold (Kg)'
                    },
                    series: [
                        { dataField: 'soldQuantity', displayText: 'Variety' }
                    ]
                }
            ]
        };
        // setup the chart
        $('#jqxVarietySalesChart').jqxChart(settings);
    });
</script>
