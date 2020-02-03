<?php
$CI = & get_instance();
?>

<table class="table table-bordered">
    <tr>
        <th colspan="6" style="text-align:center">
            <span style="font-size:1.3em"><?php echo $CI->lang->line('LABEL_FOCUSED_VARIETY'); ?></span>
            <p style="font-weight:normal; font-size:0.85em; margin:10px 0 0; display:none">
                [ &nbsp;
                <?php
                $season_list = array();
                foreach($seasons as $season){
                    $season_list[] = '<u>'.$season['name'].'</u> : '.date('M, d',$season['date_start']).'&nbsp; <i class="glyphicon glyphicon-resize-horizontal"/> &nbsp;'.date('M, d',$season['date_end']);
                }
                echo implode('&nbsp;  |  &nbsp;', $season_list);
                ?>
                &nbsp; ]
            </p>
        </th>
    </tr>
    <tr>
        <th style="text-align:center" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
        <th style="text-align:center" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
        <th style="text-align:center" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>  <i style="color:#FF0000">*</i></th>
        <th style="text-align:center" rowspan="2"><?php echo $CI->lang->line('LABEL_SEASON_NAME'); ?>  <i style="color:#FF0000">*</i></th>
        <th style="text-align:center" colspan="2"><?php echo $CI->lang->line('LABEL_DATE_SALES'); ?></th>
    </tr>
    <tr>
        <th style="text-align:center"><?php echo $CI->lang->line('LABEL_DATE_START'); ?>  <i style="color:#FF0000">*</i></th>
        <th style="text-align:center"><?php echo $CI->lang->line('LABEL_DATE_END'); ?>  <i style="color:#FF0000">*</i></th>
    </tr>
    <?php
    $current_crop_id = $current_type_id = -1;

    $i=1;
    foreach ($crops as $crop_id => $crop) {
        foreach ($crop['types'] as $type_id => $type) {
            foreach ($type['varieties'] as $variety_id => $variety) {
                $disabled = 'disabled';
                $checked = '';
                $style= '';
                $sales_date_start = '';
                $sales_date_end = '';
                if(isset($item['focusable_varieties'][$variety_id])){
                    $disabled = '';
                    $checked = 'checked';
                    $style= 'background:lightgreen;';
                    $sales_date_start = $item['focusable_varieties'][$variety_id]['sales_date_start'];
                    $sales_date_end = $item['focusable_varieties'][$variety_id]['sales_date_end'];
                }
            ?>
                <tr>
                    <?php
                    if($current_crop_id != $crop_id )
                    {
                        $current_crop_id = $crop_id;
                        ?>
                        <td rowspan="<?php echo $rowspans['crop'][$crop_id]; ?>"><?php echo $crop['name']; ?></td>
                    <?php
                    }
                    ?>


                    <?php
                    if($current_type_id != $type_id )
                    {
                        $current_type_id = $type_id;
                        ?>
                        <td rowspan="<?php echo $rowspans['type'][$type_id]; ?>"><?php echo $type['name']; ?></td>
                    <?php
                    }
                    ?>

                    <td style="padding:0 30px; box-sizing:border-box" data-row="<?php echo $i; ?>">
                        <div class="checkbox" style="<?php echo $style; ?>">
                            <input class="variety_checkbox" type="checkbox" name="variety[<?php echo $variety_id; ?>]" value="<?php echo $variety_id; ?>" <?php echo $checked; ?>><span><?php echo $variety; ?></span>
                        </div>
                    </td>
                    <td style="padding:0 30px; box-sizing:border-box" id="season_wrap_<?php echo $i; ?>">
                        <?php
                        foreach($seasons as $season_id => $season)
                        {
                            $season_checked = '';
                            if(isset($item['focusable_varieties'][$variety_id]) && in_array($season_id, $item['focusable_varieties'][$variety_id]['season'])){
                                $season_checked = 'checked';
                            }
                        ?>
                            <div class="checkbox" style="white-space:nowrap">
                                <input type="checkbox" name="variety[<?php echo $variety_id; ?>][season][]" value="<?php echo $season_id; ?>" <?php echo $season_checked; ?> <?php echo $disabled; ?> /> <?php echo $season['name']; ?> &nbsp;
                            </div>
                        <?php
                        }
                        ?>
                    </td>
                    <td id="sales_start_<?php echo $i; ?>">
                        <input type="text" name="variety[<?php echo $variety_id; ?>][date_start]" class="form-control datepicker" <?php echo $disabled; ?> readonly value="<?php echo $sales_date_start; ?>"/>
                    </td>
                    <td id="sales_end_<?php echo $i; ?>">
                        <input type="text" name="variety[<?php echo $variety_id; ?>][date_end]" class="form-control datepicker" <?php echo $disabled; ?> readonly value="<?php echo $sales_date_end; ?>"/>
                    </td>
                </tr>
            <?php
                $i++;
            }
        }
    }
    ?>
</table>

<style type="text/css"> input[disabled]{ cursor: not-allowed !important; }</style>

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $('.variety_checkbox').click(function(){
            var row_id = $(this).closest('td').attr('data-row');
            var selector = '#season_wrap_'+row_id+' input, #sales_start_'+row_id+' input, #sales_end_'+row_id+' input';
            if($(this).prop("checked") == true)
            {
                $(selector).removeAttr('disabled');
            }
            else
            {
                $(selector).val('').prop("checked", false).attr('disabled','');
            }
        });

        $( ".datepicker" ).datepicker({
            dateFormat : 'dd-MM',
            changeMonth: true,
            changeYear: true,
            yearRange: "c-2:c+2"
        });
    });
</script>
