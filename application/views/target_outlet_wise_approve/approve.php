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

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_approve'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php
        $data = array();
        $data['accordion']['data'] = $item;
        $data['accordion']['collapse'] = 'in';

        $CI->load->view('info_basic', $data);
        ?>

        <div class="row show-grid">
            <div class="col-sm-3 col-xs-1"> &nbsp; </div>
            <div class="col-sm-6 col-xs-11">
                <?php echo $target_variety_list; ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_APPROVE'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select name="item[status_approve]" class="form-control status-combo">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_approved'); ?>"><?php echo $CI->lang->line('LABEL_APPROVED'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_rollback') ?>"><?php echo $CI->lang->line('LABEL_ROLLBACK'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_rejected') ?>"><?php echo $CI->lang->line('LABEL_REJECTED'); ?></option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <textarea id="remarks" name="item[remarks_approve]" class="form-control"></textarea>
            </div>
            <div class="col-xs-4">
                <label class="control-label normal remarks-req"> </label>
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
<style type="text/css">
    .remarks-req {
        color: #FF0000;
        display: none;
        font-style:italic;
        font-weight:normal !important
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

        $(".status-combo").on('change', function (event) {
            $(".remarks-req").css('display','none');
            var options = $(this).val();
            if (options == '<?php echo $CI->config->item('system_status_approved'); ?>')
            {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $CI->lang->line('MSG_CONFIRM_APPROVE'); ?>');
            }
            else if (options == '<?php echo $CI->config->item('system_status_rollback'); ?>')
            {
                $("label.remarks-req").text('This field is required for Rollback');
                $(".remarks-req").css('display','inline-block');
                $("#button_action_save").attr('data-message-confirm', '<?php echo $CI->lang->line('MSG_CONFIRM_ROLLBACK'); ?>');
            }
            else if (options == '<?php echo $CI->config->item('system_status_rejected'); ?>')
            {
                $("label.remarks-req").text('This field is required for Reject');
                $(".remarks-req").css('display','inline-block');
                $("#button_action_save").attr('data-message-confirm', '<?php echo $CI->lang->line('MSG_CONFIRM_REJECT'); ?>');
            }
            else
            {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>
