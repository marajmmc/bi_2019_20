<table class="table table-bordered">
    <thead>
    <tr>
        <th colspan="4" style="text-align:center"><?php echo $title; ?></th>
    </tr>
    <tr class="table_head">
        <th style="width:1%;white-space:nowrap;text-align:center"><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
        <th style="width:1%;white-space:nowrap;text-align:center"><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
        <th style="width:1%;white-space:nowrap;text-align:center">Old competitor varieties</th>
        <th style="white-space:nowrap;text-align:center">New competitor varieties</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($crops)
    {
        $init_crop_id = -1;
        $rowspan = 0;
        foreach ($crops as $crop)
        {
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
                <td><?php echo $crop['crop_type_name'] ?></td>
                <td>
                    -----
                </td>
                <td>
                    <table style="width:100%">
                        <tr>
                            <?php
                            if (isset($competitor_varieties[$crop['crop_id']]))
                            {
                                $i = 1;
                                foreach ($competitor_varieties[$crop['crop_id']] as $variety_id => $variety)
                                {
                                    echo '<td>';
                                    ?>
                                    <div class="checkbox" style="margin:0">
                                        <label>
                                            <?php
                                            $checked = '';
                                            if(isset($competitor_variety_edit[$crop['crop_id']][$crop['crop_type_id']]) && in_array($variety_id, $competitor_variety_edit[$crop['crop_id']][$crop['crop_type_id']]))
                                            {
                                                $checked = 'checked';
                                            }
                                            ?>
                                            <input type="checkbox" name="items[<?php echo $crop['crop_id']; ?>][<?php echo $crop['crop_type_id']; ?>][]" value="<?php echo $variety_id; ?>" <?php echo $checked; ?> />
                                            <?php echo $variety['variety_name'] . ' (' . $variety['crop_type_name'] . ', ' . $variety['competitor_name'] . ')' ?>
                                        </label>
                                    </div>
                                    <?php
                                    echo '</td>';
                                    echo ($i % 2 == 0) ? '</tr><tr>' : '';
                                    $i++;
                                }
                            }
                            else
                            {
                                echo '<i>- No competitor available right now -<i>';
                            }
                            ?>
                        </tr>
                    </table>
                </td>
            </tr>
        <?php
        }
    }
    ?>
    </tbody>
</table>
<script>
    jQuery(document).ready(function () {
        //$(".date_large").datepicker({dateFormat : "dd-M",changeMonth: true,changeYear: true,yearRange: "c-2:c+2"});
    });
</script>
