<?php
$CI = & get_instance();

$target_varieties = array();
if($outlet_id > 0){
    $results = Query_helper::get_info($CI->config->item('table_bi_target_outlet_wise_details'), '*', array('outlet_id ='.$outlet_id, 'status ="'.$CI->config->item('system_status_active').'"'));
    foreach($results as $result)
    {
        $target_varieties[$result['variety_id']] = $result['amount_target'];
    }
}
?>

<table class="table table-bordered" xmlns="http://www.w3.org/1999/html">
    <tr>
        <th><?php echo $CI->lang->line("LABEL_CROP_NAME"); ?></th>
        <th><?php echo $CI->lang->line("LABEL_CROP_TYPE_NAME"); ?></th>
        <th style="text-align:center"><?php echo $CI->lang->line("LABEL_VARIETY_NAME").' '.$CI->lang->line("LABEL_AMOUNT_TARGET"); ?> (Taka)</th>
    </tr>
    <?php
    $current_crop_id = -1;
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
                    <?php foreach ($type['varieties'] as $variety_id => $variety) { ?>
                        <label style="font-weight:normal; display:block; clear:both">
                            <div style="width:70%; padding:5px 0; text-align:right" class="pull-left"><label><?php echo $variety; ?> :</label></div>
                            <div style="width:30%; padding:5px 0; text-align:right; margin-bottom:5px" class="pull-right">
                                <span><?php echo System_helper::get_string_amount($target_varieties[$variety_id]); ?></span>
                            </div>
                        </label>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
</table>
