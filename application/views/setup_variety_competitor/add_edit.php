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
<style> label {
        margin-top: 5px;
    } </style>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">

    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">

            <div class="col-sm-12">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COMPETITOR_NAME'); ?>
                            <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <select name="item[competitor_id]" id="competitor_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                            <?php
                            if ($competitors)
                            {
                                foreach ($competitors as $competitor)
                                {
                                    ?>
                                    <option value="<?php echo $competitor['value'] ?>" <?php echo ($competitor['value'] == $item['competitor_id']) ? "selected" : ""; ?>><?php echo $competitor['text']; ?></option>
                                <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="crop_id_container">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>
                            <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <select id="crop_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="crop_type_id_container">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>
                            <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <select name="item[crop_type_id]" id="crop_type_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>
                            <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <input type="text" name="item[name]" class="form-control" value="<?php echo $item['variety_name']; ?>"/>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_HYBRID'); ?>
                            <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <select name="item[hybrid]" class="form-control" id="hybrid">
                            <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                            <?php
                            foreach ($hybrids as $hybrid)
                            {
                                ?>
                                <option value="<?php echo $hybrid['value'] ?>" <?php if ($hybrid['value'] == $item['hybrid'])
                                {
                                    echo "selected";
                                } ?>><?php echo $hybrid['text']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER'); ?>
                            <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <input type="text" name="item[ordering]" class="form-control float_type_positive" style="text-align:left" value="<?php echo $item['ordering']; ?>"/>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Status <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <select name="item[status]" class="form-control status-combo">
                            <option value="<?php echo $CI->config->item('system_status_active'); ?>"><?php echo $CI->config->item('system_status_active'); ?></option>
                            <option value="<?php echo $CI->config->item('system_status_inactive'); ?>"><?php echo $CI->config->item('system_status_inactive'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>

            <div class="clearfix"></div>
        </div>

</form>

<script type="text/javascript">

jQuery(document).ready(function ($) {
    system_off_events(); // Triggers
    $(document).off("change", "#competitor_id");

    <?php if(($item['id'] > 0) && ($item['crop_id'] > 0)){ ?>
        $("#crop_id").html(get_dropdown_with_select(system_crops, <?php echo $item['crop_id']; ?>));
        $("#crop_type_id").html(get_dropdown_with_select(system_types[<?php echo $item['crop_id']; ?>], <?php echo $item['crop_type_id']; ?>));
    <?php } else { ?>
        $("#crop_id").html(get_dropdown_with_select(system_crops));
    <?php } ?>

    $(document).on("change", "#crop_id", function () {
        $("#crop_type_id").val('');
        var crop_id = $('#crop_id').val();
        if ((crop_id !== undefined) && (crop_id > 0)) {
            $("#crop_type_id").html(get_dropdown_with_select(system_types[crop_id]));
            $('#crop_type_id_container').show();
        }
        else {
            $('#crop_type_id_container').hide();
        }
    });

    /*var system_competitor_varieties = JSON.parse('
    <?php //echo json_encode($competitor_varieties);?>');
    <?php// if($item['id'] > 0){ ?>
     $("#variety_id").html(get_dropdown_with_select(system_competitor_varieties[
    <?php //echo $item['competitor_id']; ?>],
    <?php// echo $item['variety_id']; ?>));
     $('#variety_id_container').show();
    <?php// } ?>

     $(document).on("change", "#competitor_id", function () {
     $("#variety_id").val('');
     var competitor_id = $('#competitor_id').val();
     if ((competitor_id !== undefined) && (competitor_id > 0)) {
     $("#variety_id").html(get_dropdown_with_select(system_competitor_varieties[competitor_id]));
     $('#variety_id_container').show();
     }
     else {
     $('#variety_id_container').hide();
     }
     });*/

    /*$(document).on("change", "#competitor_id", function () {
     $("#variety_id").val('');
     var competitor_id = $('#competitor_id').val();
     if (competitor_id > 0) {
     $.ajax({
     url: "
    <?php //echo site_url($CI->controller_url.'/index/get_competitor_varieties/') ?>",
     type: 'POST',
     datatype: "JSON",
     data: { competitor_id: competitor_id },
     success: function (data, status) {

     },
     error: function (xhr, desc, err) {
     console.log("error");
     }
     });
     $('#variety_id_container').show();
     }
     else {
     $('#variety_id_container').hide();
     }
     });*/

    /* $(document).on("click", ".system_button_add_more", function (event) {
     var current_id = parseInt($(this).attr('data-current-id'));
     current_id = current_id + 1;
     $(this).attr('data-current-id', current_id);
     var content_id = '#system_content_add_more table tbody';
     $(content_id + ' .arm-variety').attr('name', 'items[' + current_id + '][arm_variety_id]');
     $(content_id + ' .arm-characteristic').attr('name', 'items[' + current_id + '][arm_characteristic]');
     var html = $(content_id).html();
     $("#variety_container tbody tr.addMore").before(html);
     });

     $(document).on("click", ".system_addMore_delete", function (event) {
     $(this).closest('tr').remove();
     });*/
});

/*jQuery(document).ready(function ($) {
 system_off_events(); // Triggers
 $(".datepicker").datepicker({dateFormat: display_date_format});

 $(document).off("input", ".expense_budget");
 $(document).off("input", ".participant_budget");

 *//*--------------------- CROP RELATED DROPDOWN ---------------------*//*
 $(document).on("change", "#crop_id", function () {
 $("#crop_type_id").val('');
 $("#variety1_id").val('');
 $("#variety2_id").val('');

 var crop_id = $('#crop_id').val();
 $('#crop_type_id_container').hide();
 $('#variety1_id_container').hide();
 $('#variety2_id_container').hide();
 if (crop_id > 0) {
 $('#crop_type_id_container').show();
 if (system_types[crop_id] !== undefined) {
 $("#crop_type_id").html(get_dropdown_with_select(system_types[crop_id]));
 }
 }
 });

 $(document).on("change", "#crop_type_id", function () {
 $("#variety1_id").val('');
 $("#variety2_id").val('');
 var crop_type_id = $('#crop_type_id').val();
 if (crop_type_id > 0) {
 $.ajax({
 url: "<?php //echo site_url($CI->controller_url.'/index/get_fd_budget_varieties/') ?>",
 type: 'POST',
 datatype: "JSON",
 data: { id: crop_type_id },
 success: function (data, status) {

 },
 error: function (xhr, desc, err) {
 console.log("error");
 }
 });
 $('#variety1_id_container').show();
 $('#variety2_id_container').show();
 }
 else {
 $('#variety1_id_container').hide();
 $('#variety2_id_container').hide();
 }
 });
 *//*--------------------- CROP RELATED DROPDOWN ( END )-------------*//*


 *//*--------------------- LOCATION RELATED DROPDOWN -----------------------------*//*
 $(document).on("change", "#division_id", function () {
 $("#zone_id").val('');
 $("#territory_id").val('');
 $("#district_id").val('');
 $("#outlet_id").val('');
 $('#growing_area_id').val('');

 var division_id = $('#division_id').val();
 $('#zone_id_container').hide();
 $('#territory_id_container').hide();
 $('#district_id_container').hide();
 $('#outlet_id_container').hide();
 $('#growing_area_id_container').hide();
 $('#dealer_container').hide();
 $('#leading_farmer_container').hide();
 if (division_id > 0) {
 $('#zone_id_container').show();
 if (system_zones[division_id] !== undefined) {
 $("#zone_id").html(get_dropdown_with_select(system_zones[division_id]));
 }
 }
 calculate_total_participants('reset');
 });
 $(document).on("change", "#zone_id", function () {
 $("#territory_id").val('');
 $("#district_id").val('');
 $("#outlet_id").val('');
 $('#growing_area_id').val('');

 var zone_id = $('#zone_id').val();
 $('#territory_id_container').hide();
 $('#district_id_container').hide();
 $('#outlet_id_container').hide();
 $('#growing_area_id_container').hide();
 $('#dealer_container').hide();
 $('#leading_farmer_container').hide();
 if (zone_id > 0) {
 $('#territory_id_container').show();
 if (system_territories[zone_id] !== undefined) {
 $("#territory_id").html(get_dropdown_with_select(system_territories[zone_id]));
 }
 }
 calculate_total_participants('reset');
 });
 $(document).on("change", "#territory_id", function () {
 $("#district_id").val('');
 $("#outlet_id").val('');
 $('#growing_area_id').val('');

 var territory_id = $('#territory_id').val();
 $('#district_id_container').hide();
 $('#outlet_id_container').hide();
 $('#growing_area_id_container').hide();
 $('#dealer_container').hide();
 $('#leading_farmer_container').hide();
 if (territory_id > 0) {
 $('#district_id_container').show();
 if (system_districts[territory_id] !== undefined) {
 $("#district_id").html(get_dropdown_with_select(system_districts[territory_id]));
 }
 }
 calculate_total_participants('reset');
 });
 $(document).on("change", "#district_id", function () {
 $('#outlet_id').val('');
 $('#growing_area_id').val('');

 var district_id = $('#district_id').val();
 $('#outlet_id_container').hide();
 $('#growing_area_id_container').hide();
 $('#dealer_container').hide();
 $('#leading_farmer_container').hide();
 if (district_id > 0) {
 if (system_outlets[district_id] !== undefined) {
 $('#outlet_id_container').show();
 $('#outlet_id').html(get_dropdown_with_select(system_outlets[district_id]));
 }
 }
 calculate_total_participants('reset');
 });
 $(document).on("change", "#outlet_id", function () {
 $('#growing_area_id').val('');

 var outlet_id = parseInt($(this).val());
 $('#growing_area_id_container').hide();
 if (outlet_id > 0) {
 $.ajax({
 url: "<?php //echo site_url($CI->controller_url.'/index/get_growing_area/') ?>",
 type: 'POST',
 datatype: "JSON",
 data: {
 html_container_id: '#growing_area_id',
 id: outlet_id
 },
 success: function (data, status) {
 if (data.status) {
 $('#growing_area_id_container').show();
 }
 },
 error: function (xhr, desc, err) {
 console.log("error");
 }
 });

 $.ajax({
 url: "<?php //echo site_url($CI->controller_url.'/index/get_dealers/') ?>",
 type: 'POST',
 datatype: "JSON",
 data: {
 html_container_id: '#dealer_id',
 id: outlet_id
 },
 success: function (data, status) {
 if (data.status) {
 $('#dealer_container').show();
 }
 },
 error: function (xhr, desc, err) {
 console.log("error");
 }
 });

 $.ajax({
 url: "<?php //echo site_url($CI->controller_url.'/index/get_lead_farmers/') ?>",
 type: 'POST',
 datatype: "JSON",
 data: {
 html_container_id: '#leading_farmer_id',
 id: outlet_id
 },
 success: function (data, status) {
 if (data.status) {
 $('#leading_farmer_container').show();
 }
 },
 error: function (xhr, desc, err) {
 console.log("error");
 }
 });
 } else {
 $('#dealer_container').hide();
 $('#leading_farmer_container').hide();
 }

 calculate_total_participants('reset');
 });
 *//*--------------------- LOCATION RELATED DROPDOWN ( END ) ---------------------*//*

 *//* Calculate Total Participant *//*
 $(document).on("input", ".participant_budget", function () {
 calculate_total_participants('');
 });

 function calculate_total_participants(action) {
 if (action == 'reset') {
 $(".participant_budget").val(0);
 }
 var total = parseInt(0);
 var item = parseInt(0);
 $(".participant_budget").each(function (index, element) {
 item = parseInt($(this).val());
 if (!isNaN(item) && (item > 0)) {
 total += item;
 }
 });
 $('#no_of_participant').text(total);
 }

 *//* Calculate Total Budget Expense *//*
 $(document).on("input", ".expense_budget", function () {
 var total = parseFloat(0);
 var item = parseFloat(0);
 $(".expense_budget").each(function (index, element) {
 item = parseFloat($(this).val());
 if (!isNaN(item) && (item > 0)) {
 total += item;
 }
 });
 $('#total_budget').text(get_string_amount(total));
 });

 $(document).on("blur", ".integer_type_positive, .float_type_positive", function () {
 var value = $(this).val();
 if (value == "") {
 $(this).val(0)
 }
 });
 });*/
</script>
