<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$CI->db->from($CI->config->item('table_login_csetup_customer') . ' cus');
$CI->db->select('cus_type.name type_name, COUNT(*) as cus_count');

$CI->db->join($CI->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = cus.id', 'INNER');

$CI->db->join($CI->config->item('table_login_csetup_cus_type') . ' cus_type', 'cus_type.id = cus_info.type', 'INNER');

$CI->db->where('cus_info.revision', 1);
$CI->db->where('cus.status !=', $CI->config->item('system_status_delete'));
$CI->db->group_by('cus_type.id');
$items = $CI->db->get()->result_array();
?>
<div style="padding:15px">
    <table style="width:100%">
        <?php foreach ($items as $item)
        {
            ?>
            <tr>
                <td style="text-align:left">Total <?php echo $item['type_name']; ?></td>
                <td style="text-align:left">: <?php echo $item['cus_count']; ?> (<a href="javascript:void();">view</a>)
                </td>
            </tr>
        <?php
        } ?>
    </table>
</div>
