<?php
$CI = & get_instance();

$results = Query_helper::get_info($CI->config->item('table_bi_target_outlet_wise_details'), '*', array('target_id =' . $target_id, 'amount_target > 0'));
$target_varieties = array();
foreach ($results as $result) {
    $target_varieties[$result['variety_id']] = $result['amount_target'];
}

// Total Variety Count
$total_amount = sizeof(Bi_helper::get_all_varieties());

// Details Table
$this->db->from($this->config->item('table_bi_target_outlet_wise_details'));
$this->db->select('target_id, COUNT(*) AS total_varieties, SUM(amount_target) AS total_amount');
$this->db->where('target_id', $target_id);
$this->db->where('amount_target >', 0);
$this->db->group_by('target_id');
$result_varieties = $this->db->get()->row_array();
?>

<table class="table table-bordered">
    <tr>
        <td colspan="3" style="text-align:center">
            Total Varieties = <label><?php echo $total_amount; ?></label>
            &nbsp; &nbsp; | &nbsp; &nbsp;
            Total Target Varieties = <label><?php echo $result_varieties['total_varieties']; ?></label>
        </td>
    </tr>
    <tr>
        <th><?php echo $CI->lang->line("LABEL_CROP_NAME"); ?></th>
        <th><?php echo $CI->lang->line("LABEL_CROP_TYPE_NAME"); ?></th>
        <th style="text-align:center"><?php echo $CI->lang->line("LABEL_VARIETY_NAME").' '.$CI->lang->line("LABEL_AMOUNT_TARGET"); ?> (Taka)</th>
    </tr>
    <?php
    $current_crop_id = -1;
    $total_target_amount = 0;
    foreach ($crops as $crop_id => $crop) {
        foreach ($crop['types'] as $type_id => $type) {
            ?>
            <tr>
                <?php
                if ($current_crop_id != $crop_id) {
                    $current_crop_id = $crop_id; ?>
                    <td rowspan="<?php echo sizeof($crop['types']); ?>"><?php echo $crop['name']; ?></td>
                <?php
                }
                ?>
                <td><?php echo $type['name']; ?></td>
                <td style="padding:5px 10px; box-sizing:border-box">
                    <?php
                    foreach ($type['varieties'] as $variety_id => $variety) {
                        $total_target_amount += $target_varieties[$variety_id];
                        ?>
                        <label style="font-weight:normal; display:block; clear:both">
                            <div style="width:70%; padding:5px 0; text-align:right" class="pull-left"><span><?php echo $variety; ?> :</span></div>
                            <div style="width:30%; padding:5px 0; text-align:right; margin-bottom:5px" class="pull-right">
                                <span><?php echo System_helper::get_string_amount($target_varieties[$variety_id]); ?></span>
                            </div>
                        </label>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
    <tr>
        <th style="text-align:right" colspan="2"> Grand Total (Taka):</th>
        <th style="text-align:right"><?php echo System_helper::get_string_amount($total_target_amount); ?></th>
    </tr>
</table>
