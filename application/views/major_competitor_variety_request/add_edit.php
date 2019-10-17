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

$results = Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'), array('id value', 'name text', 'competitor_id'), array('whose ="Competitor"', 'status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('name'));
foreach ($results as $result)
{
    $competitor_varieties[$result['competitor_id']][] = $result;
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
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>
            <span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <?php
        if ($CI->locations['division_id'] > 0)
        {
            ?>
            <label class="control-label"><?php echo $CI->locations['division_name']; ?></label>
        <?php
        }
        else
        {
            if ($item['id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['division_name']; ?></label>
            <?php
            }
            else
            {
                ?>
                <select id="division_id" name="division_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    foreach ($divisions as $division)
                    {
                        ?>
                        <option value="<?php echo $division['value'] ?>"><?php echo $division['text']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
            ?>
        <?php
        }
        ?>
    </div>
</div>
<div style="<?php echo (!(sizeof($zones) > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="zone_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>
            <span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <?php
        if ($CI->locations['zone_id'] > 0)
        {
            ?>
            <label class="control-label"><?php echo $CI->locations['zone_name']; ?></label>
        <?php
        }
        else
        {
            if ($item['id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['zone_name']; ?></label>
            <?php
            }
            else
            {
                ?>
                <select id="zone_id" name="zone_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    foreach ($zones as $zone)
                    {
                        ?>
                        <option value="<?php echo $zone['value'] ?>" <?php echo ($zone['value'] == $item['zone_id']) ? "selected" : ""; ?>><?php echo $zone['text']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
        }
        ?>
    </div>
</div>
<div style="<?php echo (!(sizeof($territories) > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="territory_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>
            <span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <?php
        if ($CI->locations['territory_id'] > 0)
        {
            ?>
            <label class="control-label"><?php echo $CI->locations['territory_name']; ?></label>
        <?php
        }
        else
        {
            if ($item['id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['territory_name']; ?></label>
            <?php
            }
            else
            {
                ?>
                <select id="territory_id" name="territory_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    foreach ($territories as $territory)
                    {
                        ?>
                        <option value="<?php echo $territory['value'] ?>" <?php echo ($territory['value'] == $item['territory_id']) ? "selected" : ""; ?>><?php echo $territory['text']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
        }
        ?>
    </div>
</div>
<div style="<?php echo (!(sizeof($districts) > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="district_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>
            <span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <?php
        if ($CI->locations['district_id'] > 0)
        {
            ?>
            <label class="control-label"><?php echo $CI->locations['district_name']; ?></label>
        <?php
        }
        else
        {
            if ($item['id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['district_name']; ?></label>
            <?php
            }
            else
            {
                ?>
                <select id="district_id" name="district_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    foreach ($districts as $district)
                    {
                        ?>
                        <option value="<?php echo $district['value'] ?>" <?php echo ($district['value'] == $item['district_id']) ? "selected" : ""; ?>><?php echo $district['text']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
        }
        ?>
    </div>
</div>
<div style="<?php echo (!(sizeof($upazillas) > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="upazilla_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?>
            <span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <?php
        if ($CI->locations['upazilla_id'] > 0)
        {
            ?>
            <label class="control-label"><?php echo $CI->locations['upazilla_name']; ?></label>
        <?php
        }
        else
        {
            if ($item['id'] > 0)
            {
                ?>
                <label class="control-label"><?php echo $item['upazilla_name']; ?></label>
            <?php
            }
            else
            {
                ?>
                <select id="upazilla_id" name="item[upazilla_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    foreach ($upazillas[$item['district_id']] as $upazilla)
                    {
                        ?>
                        <option value="<?php echo $upazilla['value'] ?>" <?php echo ($upazilla['value'] == $item['upazilla_id']) ? "selected" : ""; ?>><?php echo $upazilla['text']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
        }
        ?>
    </div>
</div>
<div class="row show-grid">
    <div class="col-sm-12" id="items_container">

        <!---- AJAX Response Here ---->

    </div>
</div>
</div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers
        $(".date_large").datepicker({dateFormat: "dd-M", changeMonth: true, changeYear: true, yearRange: "c-2:c+2"});

        <?php
        if(isset($item['upazilla_id']) && ($item['upazilla_id']>0))
        {
        ?>
        get_competitor_variety_info('EDIT', <?php echo $item['upazilla_id']; ?>, '<?php echo $item['upazilla_name']; ?>', '<?php echo $item['competitor_varieties']; ?>');
        <?php
        }
        ?>

        /*location*/
        $(document).off('change', '#division_id');
        $(document).on('change', '#division_id', function () {
            $('#zone_id').val('');
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            var division_id = $('#division_id').val();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#upazilla_id_container').hide();
            $("#items_container").html('');
            if (division_id > 0) {
                if (system_zones[division_id] !== undefined) {
                    $('#zone_id_container').show();
                    $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
                }
            }

        });
        $(document).off('change', '#zone_id');
        $(document).on('change', '#zone_id', function () {
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            var zone_id = $('#zone_id').val();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#upazilla_id_container').hide();
            $("#items_container").html('');
            if (zone_id > 0) {
                if (system_territories[zone_id] !== undefined) {
                    $('#territory_id_container').show();
                    $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).off('change', '#territory_id');
        $(document).on('change', '#territory_id', function () {
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            $('#upazilla_id_container').hide();
            $('#district_id_container').hide();
            $("#items_container").html('');
            var territory_id = $('#territory_id').val();
            if (territory_id > 0) {
                if (system_districts[territory_id] !== undefined) {
                    $('#district_id_container').show();
                    $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
                }

            }
        });
        $(document).off('change', '#district_id');
        $(document).on('change', '#district_id', function () {
            $('#upazilla_id').val('');
            $("#items_container").html('');
            var district_id = $('#district_id').val();
            $('#upazilla_id_container').hide();
            //console.log(system_upazillas);
            if (district_id > 0) {
                if (system_upazillas[district_id] !== undefined) {
                    $('#upazilla_id_container').show();
                    $('#upazilla_id').html(get_dropdown_with_select(system_upazillas[district_id]));
                }
            }
        });
        $(document).off('change', '#upazilla_id');
        $(document).on('change', '#upazilla_id', function () {
            $("#items_container").html('');
            var upazilla_id = $('#upazilla_id').val();
            var upazilla_name = $('#upazilla_id option:selected').text();
            if (upazilla_id > 0) {
                get_competitor_variety_info('ADD', upazilla_id, upazilla_name);
            }
        });

    });

    function get_competitor_variety_info(mode='ADD', upazilla_id, upazilla_name='', competitor_variety_edit='') {
        $.ajax({
            url: "<?php echo site_url($CI->controller_url.'/index/get_competitor_variety_info/') ?>",
            type: 'POST',
            datatype: "JSON",
            data: {
                mode: mode,
                upazilla_id: upazilla_id,
                upazilla_name: upazilla_name,
                competitor_variety_edit: competitor_variety_edit
            },
            success: function (data, status) {
                //$("#items_container").html('');
            },
            error: function (xhr, desc, err) {
                console.log("error");
            }
        });
    }
</script>
