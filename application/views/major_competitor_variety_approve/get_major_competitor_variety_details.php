<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#accordion_mkt_size" href="#"><?php echo $table_title; ?></a></label>
        </h4>
    </div>
    <div id="accordion_mkt_size" class="panel-collapse collapse <?php echo $collapse; ?>">
        <table class="table table-bordered">
            <thead>
            <tr class="table_head">
                <th><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
                <th><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                <th>Old Major Competitor Varieties</th>
                <th>Requested Major Competitor Varieties</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($crops)
            {
                $init_crop_id = -1;
                foreach ($crops as $crop)
                {
                    $size_old = (isset($market_size_old[$crop['crop_type_id']])) ? $market_size_old[$crop['crop_type_id']] : '-';
                    $size_edit = (isset($market_size_edit[$crop['crop_type_id']])) ? $market_size_edit[$crop['crop_type_id']] : '-';
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
                        <td><?php echo $size_edit; ?></td>
                    </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
        <style>.table_head th {
                white-space: nowrap;
                text-align: center
            }</style>
    </div>
</div>
