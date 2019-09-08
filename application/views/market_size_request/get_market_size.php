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
                $size_old = (isset($market_size_old[$crop['crop_type_id']])) ? $market_size_old[$crop['crop_type_id']] : '';
                if (isset($market_size_edit[$crop['crop_type_id']]))
                {
                    $size_edit = $market_size_edit[$crop['crop_type_id']];
                    $diff_class = 'bg-danger';
                }
                else
                {
                    $size_edit = $size_old;
                    $diff_class = '';
                }
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
                    <td class="<?php echo $diff_class; ?>">
                        <input type="hidden" name="crop_wise_market_size[<?php echo $crop['crop_type_id']; ?>][old]" value="<?php echo $size_old; ?>"/>
                        <?php echo $size_old; ?>
                    </td>
                    <td style="padding:0">
                        <input type="text" name="crop_wise_market_size[<?php echo $crop['crop_type_id']; ?>][new]" class="form-control float_type_positive crop_wise_market_size" value="<?php echo $size_edit; ?>" style="text-align:left"/>
                    </td>
                </tr>
            <?php
            }
        }
        ?>
    </tbody>
</table>

<style>
    .table_head th{white-space: nowrap; text-align: center}
    .mismatch{background:lightcoral}
</style>
