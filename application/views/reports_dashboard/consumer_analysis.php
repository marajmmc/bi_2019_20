<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$this->db->from($this->config->item('table_pos_setup_farmer_farmer') . ' f');
$CI->db->select('f.farmer_type_id');
$CI->db->select('COUNT(*) farmer_count');

$this->db->join($this->config->item('table_pos_setup_farmer_type') . ' ft', 'ft.id = f.farmer_type_id', 'INNER');
$this->db->select('ft.name farmer_type_name');

$CI->db->where('f.status', $CI->config->item('system_status_active'));
$CI->db->group_by('ft.id');
$items = $CI->db->get()->result_array();

?>
<div style="padding:15px">
    <table style="width:100%">
        <?php foreach ($items as $item)
        {
            ?>
            <tr>
                <td style="text-align:left"><?php echo $item['farmer_type_name']; ?></td>
                <td style="text-align:left">: <?php echo $item['farmer_count']; ?> (<a>view</a>)</td>
            </tr>
        <?php
        } ?>
    </table>
</div>
