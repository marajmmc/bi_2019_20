<table class="table table-bordered">
    <thead>
    <tr>
        <th colspan="4" style="text-align:center"><?php echo $title; ?></th>
    </tr>
    <tr class="table_head">
        <th style="text-align:center"><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
        <th style="text-align:center"><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
        <th style="text-align:center">Exist Varieties</th>
        <th style="text-align:center">Editable Varieties</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($varieties as $crop)
    {
        ?>
        <tr>
            <td rowspan="<?php echo sizeof($crop['crop_type'])+1?>"><?php echo $crop['crop_name']?></td>
        </tr>
        <?php
        foreach($crop['crop_type'] as $type)
        {
            ?>
            <tr>
                <td><?php echo $type['crop_type_name']?></td>
                <td>
                    <?php
                    if(isset($item_varieties[$type['crop_type_id']]) && sizeof($item_varieties[$type['crop_type_id']])>0)
                    {
                        foreach($item_varieties[$type['crop_type_id']] as $variety_id)
                        {
                            echo isset($variety_info[$variety_id])?$variety_info[$variety_id]['variety_name'].'<br />':'';
                            ?>
                            <input type="hidden" name="items[<?php echo $type['crop_type_id']; ?>][old][]" value="<?php echo $variety_id?>"/>
                        <?php
                        }
                    }
                    else
                    {
                        echo '';
                    }
                    ?>
                </td>
                <td>
                <?php
                $item_type=isset($item_varieties[$type['crop_type_id']])?array_flip($item_varieties[$type['crop_type_id']]):array();
                foreach($type['varieties'] as $variety)
                {
                    $text_color='';
                    if(isset($item_type[$variety['variety_id']]))
                    {
                        $text_color = 'text-danger';
                    }
                    ?>
                    <div class="checkbox" style="margin:0">
                        <label class="<?php echo $text_color?>">
                            <?php
                            $checked = '';
                            ?>
                            <input type="checkbox" name="items[<?php echo $type['crop_type_id']; ?>][new][]" value="<?php echo $variety['variety_id']; ?>" <?php echo $checked; ?> />
                            <?php echo $variety['variety_name']; ?>
                        </label>
                    </div>
                    <?php
                }
                ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>

