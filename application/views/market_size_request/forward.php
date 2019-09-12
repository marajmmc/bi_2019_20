<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_forward'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php echo Bi_helper::get_market_size_location($item['id']); ?>

        <?php echo Bi_helper::get_market_size_info($item['id'], $CI->controller_url); ?>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_FORWARD'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select name="item[status_forward]" class="form-control status-combo">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_forwarded'); ?>"><?php echo $CI->lang->line('LABEL_FORWARDED'); ?></option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <textarea id="remarks" name="item[remarks_forward]" class="form-control"></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4"> &nbsp; </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
                </div>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>

</form>

<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

        $(".status-combo").on('change', function (event) {
            var options = $(this).val();
            if (options == '<?php echo $CI->config->item('system_status_forwarded'); ?>') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $CI->lang->line('MSG_CONFIRM_FORWARD'); ?>');
            } else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>
