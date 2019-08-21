<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

// competitors upto CURRENT month
$current_month_LastDate = System_helper::get_time(date('Y-m-t'));

$CI->db->from($CI->config->item('table_login_basic_setup_competitor'));
$CI->db->select('COUNT(*) as competitor_count');

$CI->db->where('status', $CI->config->item('system_status_active'));
$CI->db->where('date_created <=', $current_month_LastDate);
$current_month_competitors = $CI->db->get()->row_array();


// competitors upto PREVIOUS month
$previous_Month_LastDate = System_helper::get_time(date('Y-m-t', strtotime('-1 months')));

$CI->db->from($CI->config->item('table_login_basic_setup_competitor'));
$CI->db->select('COUNT(*) as competitor_count');

/*$CI->db->where('status', $CI->config->item('system_status_active'));*/
$CI->db->where('date_created <=', $previous_Month_LastDate);
$previous_month_competitors = $CI->db->get()->row_array();
?>
<div style="padding:15px">
    <table style="width:100%">
        <tr>
            <th colspan="2" style="text-decoration:underline; padding-bottom:5px">Competitors:</th>
        </tr>
        <tr>
            <td style="text-align:left">Current Month (<b><?php echo date('M, y'); ?></b>)</td>
            <td style="text-align:left">: <?php echo $current_month_competitors['competitor_count']; ?> (<a href="javascript:void();">view</a>)
            </td>
        </tr>
        <tr>
            <td style="text-align:left">Last Month (<b><?php echo date('M, y', strtotime('-1 months')); ?></b>)</td>
            <td style="text-align:left">: <?php echo $previous_month_competitors['competitor_count']; ?> (<a href="javascript:void();">view</a>)
            </td>
        </tr>
    </table>
</div>
