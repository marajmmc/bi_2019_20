<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>

<div style="width:100%; border-bottom:1px green solid; margin-bottom:2px; padding:5px; box-sizing:border-box">
    <small>
        <strong>Focused Crops
            <?php if ($current_season) { ?>
                [ Season: <?php echo $current_season['name'] . ' (' . date('M, d', $current_season['date_start']) . ' - ' . date('M, d', $current_season['date_end']) . ')'; ?> ]
            <?php } else { ?>
                [ Season Not Set. ]
            <?php } ?>
        </strong>
    </small>
</div>

<table class="table-bordered">
    <tr>
        <th><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
        <th><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
        <th><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
        <th><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?></th>
        <th><?php echo $CI->lang->line('LABEL_DATE_START'); ?>
        <th><?php echo $CI->lang->line('LABEL_DATE_END'); ?>
    </tr>
    <?php
    $current_crop_id = $current_type_id = $current_variety_id = -1;
    foreach ($focusable_varieties as $focusable_variety) {
        ?>
        <tr>
            <?php
            if ($current_crop_id != $focusable_variety['crop_id']) {
                $current_crop_id = $focusable_variety['crop_id'];
                ?>
                <td rowspan="<?php echo $rowspan['crop'][$current_crop_id]; ?>"><?php echo $focusable_variety['crop_name']; ?></td>
            <?php
            }
            if ($current_type_id != $focusable_variety['crop_type_id']) {
                $current_type_id = $focusable_variety['crop_type_id'];
                ?>
                <td rowspan="<?php echo $rowspan['type'][$current_type_id]; ?>"><?php echo $focusable_variety['crop_type_name']; ?></td>
            <?php
            }
            if ($current_variety_id != $focusable_variety['variety_id']) {
                $current_variety_id = $focusable_variety['variety_id'];
                ?>
                <td rowspan="<?php echo $rowspan['variety'][$current_variety_id]; ?>"><?php echo $focusable_variety['variety_name']; ?></td>
            <?php
            }
            ?>
            <td><?php echo $focusable_variety['outlet_name']; ?></td>
            <td><?php echo $focusable_variety['sales_date_start']; ?></td>
            <td><?php echo $focusable_variety['sales_date_end']; ?></td>
        </tr>
    <?php
    }
    ?>
</table>
