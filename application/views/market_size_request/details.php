<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK") . ' to Pending List',
    'href' => site_url($CI->controller_url . '/index/list')
);
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK") . ' to All List',
    'href' => site_url($CI->controller_url . '/index/list_all')
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php echo Bi_helper::get_market_size_location($item_id); ?>

    <?php echo Bi_helper::get_market_size_info($item_id, $CI->common_view_location); ?>

</div>
