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
                    $size_old = $size_edit = '';

                    if(isset($major_competitor_varieties[$crop['crop_id']][$crop['crop_type_id']]['old']))
                    {
                        $size_old='<ol>';
                        foreach($major_competitor_varieties[$crop['crop_id']][$crop['crop_type_id']]['old'] as $variety_id){
                            $size_old.='<li>'.($competitor_varieties[$crop['crop_id']][$variety_id]['variety_name']).
                                ' ('.($competitor_varieties[$crop['crop_id']][$variety_id]['competitor_name']).')'.'</li>';
                        }
                        $size_old.='</ol>';
                    }
                    if(isset($major_competitor_varieties[$crop['crop_id']][$crop['crop_type_id']]['new']))
                    {
                        $size_edit='<ol>';
                        foreach($major_competitor_varieties[$crop['crop_id']][$crop['crop_type_id']]['new'] as $variety_id){
                            $size_edit.='<li>'.($competitor_varieties[$crop['crop_id']][$variety_id]['variety_name']).
                                ' ('.($competitor_varieties[$crop['crop_id']][$variety_id]['competitor_name']).')'.'</li>';
                        }
                        $size_edit.='</ol>';
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
                        <td><?php echo $size_old; ?></td>
                        <td><?php echo $size_edit; ?></td>
                    </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
        <style>.table_head th {white-space: nowrap;text-align: center}</style>
    </div>
</div>
