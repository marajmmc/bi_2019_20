<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK") . ' to Competitor Variety List',
    'href' => site_url($CI->controller_url . '/index/list')
);
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK") . ' to ARM Variety List',
    'href' => site_url($CI->controller_url . '/index/list_arm')
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

    <?php
    foreach ($items as $item)
    {
        echo $CI->load->view("info_basic", array('accordion' => $item), true);
    }
    ?>

</div>
