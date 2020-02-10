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
    <!--<input type="hidden" id="id" name="id" value="<?php /*echo $id; */?>"/>-->

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
    </div>

    <div class="clearfix"></div>
</form>

<style>
    #target_distribution table{width:500px; margin:0 auto}
    th{text-align:center}
    #target_distribution td:last-child{text-align:right}
</style>

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
