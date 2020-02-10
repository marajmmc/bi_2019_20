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
                foreach ($seasons as $season) {
                    $season_list[] = '<u>' . $season['name'] . '</u> : ' . date('M, d', $season['date_start']) . '&nbsp; <i class="glyphicon glyphicon-resize-horizontal"/> &nbsp;' . date('M, d', $season['date_end']);
                }
                echo implode('&nbsp;  |  &nbsp;', $season_list);
                ?>
                &nbsp; ]
            </p>
        </th>
    </tr>
    <tr class="table-header">
        <th rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
        <th rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
        <th rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
        <th rowspan="2"><?php echo $CI->lang->line('LABEL_SEASON_NAME'); ?></th>
        <th colspan="2"><?php echo $CI->lang->line('LABEL_DATE_SALES'); ?></th>
    </tr>
    <tr>
        <th style="text-align:center"><?php echo $CI->lang->line('LABEL_DATE_START'); ?></th>
        <th style="text-align:center"><?php echo $CI->lang->line('LABEL_DATE_END'); ?></th>
    </tr>
    <?php
    $current_crop_id = $current_type_id = -1;
    $i = 1;
    foreach ($crops as $crop_id => $crop) {
        foreach ($crop['types'] as $type_id => $type) {
            foreach ($type['varieties'] as $variety_id => $variety) {
                $sales_date_start = $item['focusable_varieties'][$variety_id]['sales_date_start'];
                $sales_date_end = $item['focusable_varieties'][$variety_id]['sales_date_end'];
                ?>
                <tr>
                    <?php
                    if ($current_crop_id != $crop_id) {
                        $current_crop_id = $crop_id;
                        ?>
                        <td rowspan="<?php echo $rowspans['crop'][$crop_id]; ?>"><?php echo $crop['name']; ?></td>
                    <?php
                    }
                    if ($current_type_id != $type_id) {
                        $current_type_id = $type_id;
                        ?>
                        <td rowspan="<?php echo $rowspans['type'][$type_id]; ?>"><?php echo $type['name']; ?></td>
                    <?php
                    }
                    ?>
                    <td><span><?php echo $variety; ?></span></td>
                    <td style="white-space:nowrap">
                        <?php
                        $index=1;
                        foreach ($seasons as $season_id => $season) {
                            if (isset($item['focusable_varieties'][$variety_id]) && in_array($season_id, $item['focusable_varieties'][$variety_id]['season'])) {
                               ?>
                                <span><?php echo '('.($index++).') <b>'.$season['name']; ?></b></span> &nbsp; &nbsp;
                            <?php
                            }
                            ?>
                        <?php
                        }
                        ?>
                    </td>
                    <td><span><?php echo $sales_date_start; ?></span></td>
                    <td><span><?php echo $sales_date_end; ?></span></td>
                </tr>
                <?php
                $i++;
            }
        }
    }
    ?>
</table>

<style type="text/css">
    input[disabled] {
        cursor: not-allowed !important;
    }

    .table-header th {
        text-align: center !important;
        width: 1%;
        white-space: nowrap;
    }
</style>

<script type="text/javascript">
    jQuery(document).ready(function () {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});

        $('.variety_checkbox').click(function () {
            var row_id = $(this).closest('td').attr('data-row');
            var selector = '#season_wrap_' + row_id + ' input, #sales_start_' + row_id + ' input, #sales_end_' + row_id + ' input';
            if ($(this).prop("checked") == true) {
                $(selector).removeAttr('disabled');
            }
            else {
                $(selector).val('').prop("checked", false).attr('disabled', '');
            }
        });

        $(".datepicker").datepicker({
            dateFormat: 'dd-MM',
            changeMonth: true,
            changeYear: true,
            yearRange: "c-2:c+2"
        });
    });
</script>
