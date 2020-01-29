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

$zone_display = $territory_display = $district_display = $outlet_display = 'display:none';

if($item['division_id'] > 0 || $CI->locations['division_id'] > 0)
{
    $zone_display='';
}
if($item['zone_id'] > 0 || $CI->locations['zone_id'] > 0)
{
    $territory_display='';
}
if($item['territory_id'] > 0 || $CI->locations['territory_id'] > 0)
{
    $district_display='';
}
if($item['district_id'] > 0 || $CI->locations['district_id'] > 0)
{
    $outlet_display='';
}
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
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>
                <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
            if ($item['division_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['division_name']; ?></label>
            <?php
            }
            else if ($CI->locations['division_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $CI->locations['division_name']; ?></label>
            <?php
            }
            else
            {
                ?>
                <select id="division_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            <?php
            }
            ?>
        </div>
    </div>

    <div style="<?php echo $zone_display; ?>" class="row show-grid" id="zone_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>
                <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
            if ($item['zone_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['zone_name']; ?></label>
            <?php
            }
            else if ($CI->locations['zone_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $CI->locations['zone_name']; ?></label>
            <?php
            }
            else
            {
                ?>
                <select id="zone_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            <?php
            }
            ?>
        </div>
    </div>

    <div style="<?php echo $territory_display; ?>" class="row show-grid" id="territory_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>
                <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
            if ($item['territory_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['territory_name']; ?></label>
            <?php
            }
            else if ($CI->locations['territory_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $CI->locations['territory_name']; ?></label>
            <?php
            }
            else
            {
                ?>
                <select id="territory_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            <?php
            }
            ?>
        </div>
    </div>

    <div style="<?php echo $district_display; ?>" class="row show-grid" id="district_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>
                <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
            if ($item['district_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['district_name']; ?></label>
            <?php
            }
            else if ($CI->locations['district_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $CI->locations['district_name']; ?></label>
            <?php
            }
            else
            {
                ?>
                <select id="district_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            <?php
            }
            ?>
        </div>
    </div>

    <div style="<?php echo $outlet_display; ?>" class="row show-grid" id="outlet_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>
                <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
            if ($item['outlet_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['outlet_name']; ?></label>
                <input type="hidden" name="item[outlet_id]" value="<?php echo $item['outlet_id']; ?>" />
            <?php
            }
            else
            {
                ?>
                <select id="outlet_id" name="item[outlet_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            <?php
            }
            ?>
        </div>
    </div>

    <?php /* <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?> &nbsp;</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <textarea class="form-control" name="item[remarks]" rows="5"><?php echo $item['remarks']; ?></textarea>
        </div>
    </div>

    <div style="" class="row show-grid" id="crop_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>
                <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
            if ($item['crop_id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['crop_name']; ?></label>
                <input type="hidden" name="item[crop_id]" value="<?php echo $item['crop_id']; ?>" />
            <?php
            }
            else
            {
                ?>
                <select id="crop_id" name="item[crop_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                </select>
            <?php } ?>
        </div>
    </div> */ ?>

    <div class="row show-grid" id="target_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?>
                <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <input type="text" class="form-control float_type_positive price_unit_tk" value="<?php echo $item['amount_target']; ?>" name="item[amount_target]" />
        </div>
    </div>

</div>

</form>

<style type="text/css"> label { margin-top: 5px;} </style>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        system_off_events(); // Triggers

        $('#division_id').html(get_dropdown_with_select(system_divisions));

        <?php
        if($CI->locations['division_id'] > 0)
        {
            ?> $('#zone_id').html(get_dropdown_with_select(system_zones[<?php echo $CI->locations['division_id']; ?>])); <?php
        }
        if($CI->locations['zone_id'] > 0)
        {
            ?> $('#territory_id').html(get_dropdown_with_select(system_territories[<?php echo $CI->locations['zone_id']; ?>])); <?php
        }
        if($CI->locations['territory_id'] > 0)
        {
            ?> $('#district_id').html(get_dropdown_with_select(system_districts[<?php echo $CI->locations['territory_id']; ?>])); <?php
        }
        if($CI->locations['district_id'] > 0)
        {
            ?> $('#upazilla_id').html(get_dropdown_with_select($system_outlets[<?php echo $CI->locations['district_id']; ?>])); <?php
        }
        ?>

        $(document).on('change', '#division_id', function () {
            $('#zone_id').val('');
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            var division_id = $('#division_id').val();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $("#system_report_container").html('');
            if (division_id > 0) {
                if (system_zones[division_id] !== undefined) {
                    $('#zone_id_container').show();
                    $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
                }
            }
        });
        $(document).on('change', '#zone_id', function () {
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            var zone_id = $('#zone_id').val();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $("#system_report_container").html('');
            if (zone_id > 0) {
                if (system_territories[zone_id] !== undefined) {
                    $('#territory_id_container').show();
                    $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).on('change', '#territory_id', function () {
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#outlet_id_container').hide();
            $('#district_id_container').hide();
            $("#system_report_container").html('');
            var territory_id = $('#territory_id').val();
            if (territory_id > 0) {
                if (system_districts[territory_id] !== undefined) {
                    $('#district_id_container').show();
                    $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
                }
            }
        });
        $(document).on('change', '#district_id', function () {
            $('#outlet_id').val('');
            $("#system_report_container").html('');
            var district_id = $('#district_id').val();
            $('#outlet_id_container').hide();
            if (district_id > 0) {
                if (system_outlets[district_id] !== undefined) {
                    $('#outlet_id_container').show();
                    $('#outlet_id').html(get_dropdown_with_select(system_outlets[district_id]));
                }
            }
        });
    });
</script>
