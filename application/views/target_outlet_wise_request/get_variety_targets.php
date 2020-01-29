<?php
$CI = & get_instance();

$target_varieties = array();
if($target_id > 0){
    $results = Query_helper::get_info($CI->config->item('table_bi_target_outlet_wise_details'), '*', array('target_id ='.$target_id, 'amount_target > 0'));
    foreach($results as $result)
    {
        $target_varieties[$result['variety_id']] = $result['amount_target'];
    }
}
?>

<table class="table table-bordered">
    <tr>
        <th style="white-space:nowrap; width:1%"><?php echo $CI->lang->line("LABEL_CROP_NAME"); ?></th>
        <th style="white-space:nowrap; width:1%"><?php echo $CI->lang->line("LABEL_CROP_TYPE_NAME"); ?></th>
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
                    <?php
                    foreach ($type['varieties'] as $variety_id => $variety) {
                        $value = $style = '';
                        if (array_key_exists($variety_id, $target_varieties)) {
                            $value = $target_varieties[$variety_id];
                            $style = 'font-weight:bold; background:lightgreen';
                        }
                        ?>
                        <label style="font-weight:normal; display:block; clear:both">
                            <div style="width:58%; padding:5px 0; text-align:right;<?php echo $style; ?>" class="pull-left"><span><?php echo $variety; ?> :</span></div>
                            <div style="width:40%; margin-bottom:5px" class="pull-right">
                                <input type="text" class="form-control float_type_positive price_unit_tk target_input" value="<?php echo $value; ?>" name="varieties[<?php echo $variety_id; ?>]" />
                            </div>
                        </label>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
</table>
