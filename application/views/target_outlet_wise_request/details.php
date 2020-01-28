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

</div>
