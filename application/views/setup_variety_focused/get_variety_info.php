<table class="table table-bordered">
    <?php
    $current_crop_id = -1;
    foreach ($crops as $crop_id => $crop) {
        foreach ($crop['types'] as $type) {
            ?>
            <tr>
                <?php
                if ($current_crop_id != $crop_id) {
                    $current_crop_id = $crop_id; ?>
                    <td rowspan="<?php echo sizeof($crop['types']); ?>"><?php echo $crop['name']; ?></td>
                <?php } ?>
                <td><?php echo $type['name']; ?></td>
                <td style="padding:0 30px; box-sizing:border-box">
                    <?php
                    foreach ($type['varieties'] as $variety_id => $variety) {
                        $checked = $style = '';
                        if (in_array($variety_id, $focusable_varieties)) {
                            $checked = 'checked';
                            $style = 'font-weight:bold; background:lightgreen';
                        }
                        ?>
                        <label style="font-weight:normal; display:block; clear:both">
                            <div class="checkbox" style="<?php echo $style; ?>">
                                <input type="checkbox" name="variety[]"
                                       value="<?php echo $variety_id; ?>" <?php echo $checked; ?>><span><?php echo $variety; ?></span>
                            </div>
                        </label>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
</table>
