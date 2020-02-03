<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list')
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
    $CI->load->view('info_basic', array('accordion' => array('collapse' => 'in', 'data' => $item)));
    ?>

    <div style="margin-bottom:20px" class="row show-grid" id="variety_container">
        <div class="col-xs-12" style="padding:0">
            <?php echo $variety_list; ?>
        </div>
    </div>

</div>
