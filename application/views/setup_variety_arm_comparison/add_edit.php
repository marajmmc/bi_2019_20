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
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row show-grid">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php
            $basic = array(
                'header' => '+ ARM Variety Information',
                'collapse' => 'in',
                'data' => array(
                    array(
                        'label_1' => $CI->lang->line('LABEL_ARM_VARIETY_NAME'),
                        'value_1' => $item['arm_variety_name'] . ' ( ID: ' . $item['arm_variety_id'] . ' )'
                    ),
                    array(
                        'label_1' => $CI->lang->line('LABEL_CROP_NAME'),
                        'value_1' => $item['crop_name']
                    ),
                    array(
                        'label_1' => $CI->lang->line('LABEL_CROP_TYPE_NAME'),
                        'value_1' => $item['crop_type_name']
                    )
                )
            );
            $CI->load->view('info_basic', array('accordion' => $basic));
            ?>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

            <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
                <input type="hidden" name="item[arm_variety_id]" value="<?php echo $item['arm_variety_id']; ?>">
                <input type="hidden" name="item[crop_id]" value="<?php echo $item['crop_id']; ?>">

                <div class="row widget" style="margin:0">
                    <div class="widget-header" style="margin:0">
                        <div class="title">
                            Competitors Varieties
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-lg-12">
                            <div class="col-lg-12" style="margin-bottom:20px;overflow:hidden">
                                <div class="checkbox pull-left">
                                    <label><input type="checkbox" id="select_all_competitor"> SELECT ALL</label>
                                </div>
                                <div class="pull-right" style="margin:10px 0">
                                    Total Selected: <b id="total_count">0</b>
                                </div>
                            </div>
                            <?php
                            foreach ($item['competitor_varieties'] as $competitor_id => $competitor)
                            {
                                ?>
                                <div class="col-lg-12">
                                    <div class="panel panel-default" style="margin:0; border-radius:0">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <label class="">
                                                    <a style="font-weight:normal" class="external text-danger" data-toggle="collapse" data-target="#accordion_<?php echo $competitor_id; ?>" href="#">+ <?php echo $competitor['competitor_name']; ?></a>
                                                </label>
                                                <?php
                                                $expand = '';
                                                if ($competitor['compared_varieties'] > 0)
                                                {
                                                    /* ?><label class="pull-right" style="font-weight:normal; font-size:0.8em; margin-top:5px">Compared Varieties: <?php echo $competitor['compared_varieties']; ?></label><?php */
                                                    $expand = 'in';
                                                }
                                                ?>
                                            </h4>
                                        </div>

                                        <div id="accordion_<?php echo $competitor_id; ?>" class="panel-collapse collapse <?php echo $expand; ?>">
                                            <div class="row show-grid" style="margin:10px 0">
                                                <div class="col-xs-12" style="padding-left:30px">
                                                    <?php
                                                    foreach ($competitor['varieties'] as $variety)
                                                    {
                                                        $checked = (in_array($variety['variety_id'], $item['competitor_variety_ids'])) ? 'checked' : '';
                                                        ?>
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" class="setup_competitor" name="item[competitor_variety_ids][<?php echo $competitor_id; ?>][]" value="<?php echo $variety['variety_id']; ?>" <?php echo $checked; ?> />
                                                                <b><?php echo $variety['variety_name']; ?></b>
                                                                <i style="font-size:0.9em" title="Crop Type">(<?php echo $variety['crop_type_name']; ?>)</i>
                                                            </label>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </form>

        </div>
    </div>
</div>


<script type="text/javascript">

    jQuery(document).ready(function () {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        $(document).off("change", "#select_all_competitor");
        $(document).off("change", ".setup_competitor");

        total_selected();

        $(document).on("change", "#select_all_competitor", function () {
            if ($(this).is(':checked')) {
                $('.setup_competitor').prop('checked', true);
                $('.panel-collapse').addClass('in');
                $('.panel-collapse').removeAttr('style');
            }
            else {
                $('.setup_competitor').prop('checked', false);
            }
            total_selected();
        });

        $(document).on("change", ".setup_competitor", function () {
            total_selected();
        });
    });

    function total_selected() {
        var counter = 0;
        $('.setup_competitor').each(function (index, value) {
            if ($(this).prop("checked") == true) {
                counter++;
            }
            console.log('Checkbox ' + index + ' :' + $(this).attr('checked'));
        });
        $('#total_count').text(counter);
    }
</script>