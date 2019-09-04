<?php
/*echo '<pre>';
print_r($crop_old);
print_r($crop_edit);
echo '</pre>';*/
?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="4" style="text-align:center"><?php echo $table_title; ?></th>
        </tr>
        <tr class="table_head">
            <th><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
            <th><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
            <th>Old <?php echo $this->lang->line('LABEL_MARKET_SIZE'); ?></th>
            <th><?php echo $this->lang->line('LABEL_MARKET_SIZE'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($crops)
        {
            $init_crop_id = -1;
            foreach ($crops as $crop)
            {
                $size_old = (isset($market_size_old[$crop['crop_id']][$crop['crop_type_id']]))? $market_size_old[$crop['crop_id']][$crop['crop_type_id']]: '-';
                $size_edit = (isset($market_size_edit[$crop['crop_id']][$crop['crop_type_id']]))? $market_size_edit[$crop['crop_id']][$crop['crop_type_id']]: '';
                ?>
                <tr>
                    <?php
                    $rowspan = 1;
                    if ($init_crop_id != $crop['crop_id'])
                    {
                        $rowspan = $crop_type_count[$crop['crop_id']];
                        ?>
                        <td rowspan="<?php echo $rowspan; ?>"><?php echo $crop['crop_name']; ?></td>
                        <?php
                        $init_crop_id = $crop['crop_id'];
                    }
                    ?>
                    <td><?php echo $crop['crop_type_name']; ?></td>
                    <td><?php echo $size_old; ?></td>
                    <td>
                        <input type="text" name="crop_wise_market_size[<?php echo $crop['crop_id']; ?>][<?php echo $crop['crop_type_id']; ?>]" class="form-control float_type_positive crop_wise_market_size" value="<?php echo $size_edit; ?>" style="text-align:left"/>
                    </td>
                </tr>
            <?php
            }
        }
        ?>
    </tbody>
</table>

<style>.table_head th{white-space: nowrap; text-align: center}</style>
