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

            <div class="row show-grid col-sm-12">
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
                    elseif($item['id'] > 0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['division_name']; ?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="division_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?> </option>
                        </select>
                    <?php
                    }
                    ?>
                </div>
            </div>

            <div class="row show-grid col-sm-12" id="zone_id_container" style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>">
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
                    elseif($item['id'] > 0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['zone_name']; ?></label>
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

            <div class="row show-grid col-sm-12" id="territory_id_container" style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>">
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
                    elseif($item['id'] > 0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['territory_name']; ?></label>
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

            <div class="row show-grid col-sm-12" id="district_id_container" style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>">
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
                    elseif($item['id'] > 0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['district_name']; ?></label>
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

            <div class="row show-grid col-sm-12" id="upazilla_id_container" style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>">
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
                    elseif($item['id'] > 0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['upazilla_name']; ?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select name="item[upazilla_id]" id="upazilla_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?> </option>
                        </select>
                    <?php
                    }
                    ?>
                </div>
            </div>

            <div class="row show-grid col-sm-12" id="market_size_container" style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MARKET_SIZE'); ?>
                        <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-6 col-xs-8 display">

                    <!---AJAX Data Loads Here--->

                </div>
            </div>

        </div>

    </div>

</form>

<style> label {
        margin-top: 5px
    } </style>

<script type="text/javascript">

    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers
        $(document).off("change", "#district_id");

        var upazillas = JSON.parse('<?php echo json_encode($upazillas);?>');

        <?php if($item['id'] > 0){ ?>
            var selected_division_id = <?php echo $item['division_id']; ?>;
            var selected_zone_id = <?php echo $item['zone_id']; ?>;
            var selected_territory_id = <?php echo $item['territory_id']; ?>;
            var selected_district_id = <?php echo $item['district_id']; ?>;
            var selected_upazilla_id = <?php echo $item['upazilla_id']; ?>;

            $("#division_id").html(get_dropdown_with_select(system_divisions, selected_division_id));
            $("#zone_id").html(get_dropdown_with_select(system_zones[selected_division_id], selected_zone_id));
            $("#territory_id").html(get_dropdown_with_select(system_territories[selected_zone_id], selected_territory_id));
            $("#district_id").html(get_dropdown_with_select(system_districts[selected_territory_id], selected_district_id));
            $("#upazilla_id").html(get_dropdown_with_select(upazillas[selected_district_id], selected_upazilla_id));

            get_market_size(<?php echo $item['upazilla_id']; ?>, '<?php echo $item['upazilla_name']; ?>', '<?php echo $item['market_size']; ?>');
        <?php } else { ?>
            $("#division_id").html(get_dropdown_with_select(system_divisions));
        <?php } ?>

        $(document).on("change", "#division_id", function () {
            clear_child(true, true, true, true)

            var division_id = $(this).val();
            if (division_id > 0) {
                $('#zone_id_container').show();
                if (system_zones[division_id] !== undefined) {
                    $("#zone_id").html(get_dropdown_with_select(system_zones[division_id]));
                }
            }
        });
        $(document).on("change", "#zone_id", function () {
            clear_child(true, true, true)

            var zone_id = $(this).val();
            if (zone_id > 0) {
                $('#territory_id_container').show();
                if (system_territories[zone_id] !== undefined) {
                    $("#territory_id").html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).on("change", "#territory_id", function () {
            clear_child(true, true)

            var territory_id = $(this).val();
            if (territory_id > 0) {
                $('#district_id_container').show();
                if (system_districts[territory_id] !== undefined) {
                    $("#district_id").html(get_dropdown_with_select(system_districts[territory_id]));
                }
            }
        });
        $(document).on("change", "#district_id", function () {
            clear_child(true)

            var district_id = $(this).val();
            if (district_id > 0) {
                $('#upazilla_id_container').show();
                if (upazillas[district_id] !== undefined) {
                    $("#upazilla_id").html(get_dropdown_with_select(upazillas[district_id]));
                }
            }
        });
        $(document).on("change", "#upazilla_id", function () {
            clear_child();

            var upazilla_id = $(this).val();
            var upazilla_name = $(this).children('option:selected').text();

            if ((upazilla_id != undefined) && (upazilla_id.trim() != "" )) {
                get_market_size(upazilla_id, upazilla_name);
            }
        });
    });

    function get_market_size(upazilla_id, upazilla_name, market_size_edit='')
    {
        $.ajax({
            url: "<?php echo site_url($CI->controller_url.'/index/get_market_size/') ?>",
            type: 'POST',
            datatype: "JSON",
            data: {
                html_container_id: '#market_size_container .display',
                upazilla_id: upazilla_id,
                upazilla_name: upazilla_name,
                market_size_edit: market_size_edit
            },
            success: function (data, status) {
                if (data.status) {
                    $('#market_size_container').show();
                }
            },
            error: function (xhr, desc, err) {
                console.log("error");
            }
        });
    }

    function clear_child(upazilla=false, district=false, territory=false, zone=false, division=false)
    {
        $("#market_size_container").hide(); // hides entire market-size table
        $(".crop_wise_market_size").val(''); // clears entire market-size table
        if(upazilla)
        {
            $("#upazilla_id").val('');
            $('#upazilla_id_container').hide();
        }
        if(district)
        {
            $("#district_id").val('');
            $('#district_id_container').hide();
        }
        if(territory)
        {
            $("#territory_id").val('');
            $('#territory_id_container').hide();
        }
        if(zone)
        {
            $("#zone_id").val('');
            $('#zone_id_container').hide();
        }
        if(division)
        {
            $("#division_id").val('');
            $('#division_id_container').hide();
        }
    }
</script>
