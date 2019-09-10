<table class="table table-bordered">
    <thead>
    <tr>
        <th colspan="6" style="text-align:center"><?php echo $title; ?></th>
    </tr>
    <tr class="table_head">
        <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
        <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
        <th colspan="2" style="text-align:center">Before <?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
        <th colspan="2" style="text-align:center"><?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
    </tr>
    <tr>
        <th style="text-align:center">Date Start</th>
        <th style="text-align:center">Date End</th>
        <th style="text-align:center">Date Start</th>
        <th style="text-align:center">Date End</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($crops)
    {
        $init_crop_id = -1;
        $rowspan=0;
        foreach ($crops as $crop)
        {
            $date_start_old="";
            $date_end_old="";
            if(isset($cultivation_period_old[$crop['crop_type_id']]))
            {
                $date_start_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_start']);
                $date_end_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_end']);
            }

            $date_start=Bi_helper::cultivation_date_display(0);
            $date_end=Bi_helper::cultivation_date_display(0);
            $rowspan = $crop_type_count[$crop['crop_id']];
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
                <td><?php echo $crop['crop_type_name']?></td>
                <td>
                    <?php
                    if($date_start_old)
                    {
                        echo $date_start_old;
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if($date_end_old)
                    {
                        echo $date_end_old;
                    }
                    ?>
                </td>
                <td>
                    <div class='input-group date' id='datetimepicker1'>
                        <input type="text" name="items[<?php echo $crop['crop_type_id']; ?>][date_start]" class="form-control date_large" value="<?php echo $date_start?$date_start:''; ?>" readonly="true" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </td>
                <td>
                    <div class='input-group date' id='datetimepicker1'>
                        <input type="text" name="items[<?php echo $crop['crop_type_id']; ?>][date_end]" class="form-control date_large" value="<?php echo $date_end?$date_end:''; ?>" readonly="true" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </td>
            </tr>
        <?php
        }
    }
    ?>
    </tbody>
</table>
<script>
    jQuery(document).ready(function()
    {

    });
</script>
