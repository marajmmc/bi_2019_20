<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$bar_width = 23;
$from_date = date('2018-01-01'); // Y-m-01

$CI->db->from($CI->config->item('table_pos_sale') . ' s');
$CI->db->select("s.outlet_id, SUM(s.amount_payable_actual) amount_sold");

$CI->db->join($CI->config->item('table_login_csetup_cus_info') . ' ci', 'ci.customer_id = s.outlet_id');
$CI->db->select("ci.name outlet_name");

$CI->db->where('s.date_sale >=', System_helper::get_time($from_date));
$CI->db->group_by('s.outlet_id');
$CI->db->order_by('ci.name', 'ASC');
$results = $CI->db->get()->result_array();
?>

<div style="padding:15px; width:100%; height:100%; overflow-x:scroll">

    <div id='jqxShowroomSalesChart' style="width:<?php echo $bar_width * (count($results)) ?>px; height:450px"/>

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
                    $title = 'ID: '.$result['outlet_id'];
                    echo "{ outletName: '".$result['outlet_name']."', soldQuantity: ".$result['amount_sold']."},";
                    $max = ($result['amount_sold'] > $max)? $result['amount_sold']:$max;
                 }
             }
             ?>
        ];
        // prepare jqxChart settings
        var settings = {
            borderLineWidth: 0,
            title: "Showroom-wise Sales",
            description: '( Sales from <?php echo date('M d, Y', strtotime($from_date)); ?> )',
            showLegend: false,
            enableAnimations: false,
            source: sampleData,
            categoryAxis: {
                dataField: 'outletName',
                showGridLines: true,
                flip: false,
                textRotationAngle: -90
            },
            colorScheme: 'scheme06', // Changes color of Bars
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
                        description: 'Amount Sold (BDT)'
                    },
                    series: [
                        { dataField: 'soldQuantity', displayText: 'Outlet' }
                    ]
                }
            ]
        };
        // setup the chart
        $('#jqxShowroomSalesChart').jqxChart(settings);
    });
</script>
