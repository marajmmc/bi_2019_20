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
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR'); ?>
                <span style="color:#FF0000">*</span></label>
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
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MONTH'); ?>
                <span style="color:#FF0000">*</span></label>
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
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?> <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width:1%;white-space:nowrap"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                        <th><?php echo $item['label_location']; ?></th>
                        <th><?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?> <span style="color:#FF0000">*</span></th>
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
                        <td><label class="control-label pull-right" id="amount_target_total">
                            <?php echo System_helper::get_string_amount($sum); ?>
                        </label></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

</form>

<style type="text/css"> label { margin-top: 5px;} th{text-align:center} </style>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        system_off_events(); // Triggers

        $(document).on("input", ".amount_target", function (event) {
            var sum = parseFloat(0);
            var item_amount = parseFloat(0);
            $(".amount_target").each(function (e) {
                item_amount = parseFloat($(this).val());
                if (!isNaN(item_amount) && (item_amount > 0)) {
                    sum += item_amount;
                }
            });
            $("#amount_target_total").text(get_string_amount(sum));
        });
    });
</script>
