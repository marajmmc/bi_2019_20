<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1))) {
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

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>"
      method="post">
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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['division_id'] > 0) {
                    ?>
                    <label class="control-label"><?php echo $item['division_name']; ?></label>
                <?php
                } else if ($CI->locations['division_id'] > 0) {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['division_name']; ?></label>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['zone_id'] > 0) {
                    ?>
                    <label class="control-label"><?php echo $item['zone_name']; ?></label>
                <?php
                } else if ($CI->locations['zone_id'] > 0) {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['zone_name']; ?></label>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['territory_id'] > 0) {
                    ?>
                    <label class="control-label"><?php echo $item['territory_name']; ?></label>
                <?php
                } else if ($CI->locations['territory_id'] > 0) {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['territory_name']; ?></label>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['district_id'] > 0) {
                    ?>
                    <label class="control-label"><?php echo $item['district_name']; ?></label>
                <?php
                } else if ($CI->locations['district_id'] > 0) {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['district_name']; ?></label>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row show-grid" id="outlet_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['outlet_name']; ?></label>
                <input type="hidden" name="item[outlet_id]" value="<?php echo $item['outlet_id']; ?>"/>
            </div>
        </div>

        <div style="margin-bottom:20px" class="row show-grid" id="variety_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FOCUSED_VARIETY'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-8 col-xs-8" style="padding:0">
                <?php echo $variety_list; ?>
            </div>
        </div>

    </div>
</form>

<style type="text/css">label{ margin-top:5px; }</style>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        system_off_events(); // Triggers
    });
</script>