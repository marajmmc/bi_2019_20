<table class="table table-bordered">
    <thead>
    <tr>
        <th colspan="4" style="text-align:center"><?php echo $title; ?></th>
    </tr>
    <tr class="table_head">
        <th><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
        <th><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
        <th style="text-align:center">Old Competitor Varieties</th>
        <th style="text-align:center">New Competitor Varieties</th>
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
                    <div class='input-group date' id='datetimepicker1'>
                        <label>Old Competitor Variety 1</label><br/>
                        <label>Old Competitor Variety 2</label>
                        <?php /*<input type="text" name="items[<?php echo $crop['crop_type_id']; ?>][date_start]" class="form-control date_large" value="<?php echo $date_start?$date_start:''; ?>" readonly="true" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>*/?>
                    </div>
                </td>
                <td>
                    <div class='input-group date' id='datetimepicker1'>
                        <input type="checkbox" name="" value="" /> Competitor Variety 1 <br/>
                        <input type="checkbox" name="" value="" /> Competitor Variety 2 <br/>
                        <input type="checkbox" name="" value="" /> Competitor Variety 3
                        <?php /* <input type="text" name="items[<?php echo $crop['crop_type_id']; ?>][date_end]" class="form-control date_large" value="<?php echo $date_end?$date_end:''; ?>" readonly="true" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>*/ ?>
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
        //$(".date_large").datepicker({dateFormat : "dd-M",changeMonth: true,changeYear: true,yearRange: "c-2:c+2"});
    });
</script>
