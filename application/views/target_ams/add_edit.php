<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_SAVE"),
        'id' => 'button_action_save',
        'data-form' => '#save_form'
    );
}
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">

<input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

<div class="row widget">

    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR'); ?> <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
            if ($item['id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['year']; ?></label>
                <input type="hidden" name="item[year]" value="<?php echo $item['year']; ?>" />
            <?php
            }
            else
            {
                ?>
                <select id="year" name="item[year]" class="form-control">
                    <?php
                    for($i = ($item['year']-2); $i <= ($item['year']+2); $i++){
                        ?>
                        <option value="<?php echo $i; ?>" <?php echo ($item['year']==$i)? 'selected':''; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php } ?>
                </select>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MONTH'); ?> <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
            if ($item['id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo DateTime::createFromFormat('!m', $item['month'])->format('F'); ?></label>
                <input type="hidden" name="item[month]" value="<?php echo $item['month']; ?>" />
            <?php
            }
            else
            {
                ?>
                <select id="month" name="item[month]" class="form-control">
                    <?php
                    for($i=1; $i<=12; $i++){
                    ?>
                        <option value="<?php echo $i; ?>" <?php echo ($item['month']==$i)? 'selected':''; ?>>
                            <?php echo DateTime::createFromFormat('!m', $i)->format('F'); ?>
                        </option>
                    <?php } ?>
                </select>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ASSIGNED_TARGET'); ?> &nbsp;</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label text-success bg-success" style="padding:5px;margin:0"><?php echo System_helper::get_string_amount($item['amount_target']); ?></label>
            <span style="font-size:0.85em">( <b>In-words:</b> <?php echo Bi_helper::get_string_amount_inword($item['amount_target']); ?> )</span>
            <input type="hidden" value="<?php echo $item['amount_target']; ?>" id="target_assigned" />
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMAINING_TARGET'); ?> &nbsp;</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label text-warning bg-warning" style="padding:5px;margin:0" id="target_remaining"> </label>&nbsp;
            <span class="hide" style="color:#FF0000;font-size:0.85em" id="target_remaining_msg"> </span>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?> Allocation <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width:1%;white-space:nowrap"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                        <th><?php echo $item['label_location']; ?></th>
                        <th><?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i=1;
                $sum=0;
                foreach($item['target_locations'] as $location){
                    $target_amount = '';
                    if(isset($item['targets'][$location['id']])){
                        $target_amount = $item['targets'][$location['id']];
                        $sum += $target_amount;
                    }
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $location['name']; ?></td>
                        <td>
                            <input type="text" class="form-control float_type_positive price_unit_tk amount_target" value="<?php echo $target_amount; ?>" name="amount_target[<?php echo $location['id']; ?>]" />
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><label class="control-label pull-right">
                            <?php echo $CI->lang->line('LABEL_AMOUNT_TARGET_TOTAL'); ?> :
                        </td>
                        <td>
                            <label class="control-label pull-right" id="amount_target_total">
                                <?php echo System_helper::get_string_amount($sum); ?>
                            </label>
                            <input type="hidden" value="<?php echo $sum; ?>" id="target_allocated"/>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

</form>

<style type="text/css"> label { margin-top: 5px;} th{text-align:center} table{margin:0 !important}</style>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        system_off_events(); // Triggers

        calculate_remaining_target();

        $(document).on("input", ".amount_target", function (event) {
            var item_amount = parseFloat(0);
            var sum_allocation = parseFloat(0);
            $(".amount_target").each(function (e) {
                item_amount = parseFloat($(this).val());
                if (!isNaN(item_amount) && (item_amount > 0)) {
                    sum_allocation += item_amount;
                }
            });
            $('#target_allocated').val(sum_allocation);
            $("#amount_target_total").text(get_string_amount(sum_allocation));
            calculate_remaining_target();
        });
    });

    function calculate_remaining_target() {
        var target_assigned = parseFloat($('#target_assigned').val());
        var target_allocated = parseFloat($('#target_allocated').val());
        var target_remaining = target_assigned-target_allocated;

        if(target_remaining == 0){
            $("#target_remaining").removeAttr('class').attr('class', 'control-label text-success bg-success');
            $("#target_remaining_msg").addClass('hide').text('');
        } else if(target_remaining < 0){
            $("#target_remaining").removeAttr('class').attr('class', 'control-label text-danger bg-danger');
            $("#target_remaining_msg").removeClass('hide').text('Total Allocation has exceeded the Assigned Target.');
        } else {
            $("#target_remaining").removeAttr('class').attr('class', 'control-label text-warning bg-warning');
            $("#target_remaining_msg").addClass('hide').text('');
        }
        $("#target_remaining").text(get_string_amount(target_remaining));
    }
</script>
