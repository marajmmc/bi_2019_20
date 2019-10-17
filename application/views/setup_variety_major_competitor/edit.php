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
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $item_info['id']; ?>"/>
    <input type="hidden" name="crop_type_id" value="<?php echo $item_info['crop_type_id']; ?>"/>

    <div class="row widget">

        <div class="widget-header" style="margin:0">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid" style="padding:0; margin:0">
            <div class="col-sm-12" id="items_container" style="padding:0; margin:0">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="3" style="text-align:center">ARM</th>
                        <th colspan="2" style="text-align:center">Competitor</th>
                    </tr>
                    <tr class="table_head">
                        <th><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th style="text-align:center"><?php echo $this->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th style="text-align:center">Current Compared <?php echo $this->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th style="text-align:center">New <?php echo $this->lang->line('LABEL_VARIETY_NAME'); ?> for Comparison</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo $item_info['crop_name'] ?></td>
                        <td><?php echo $item_info['crop_type_name'] ?></td>
                        <td>
                            <ol>
                                <?php
                                if ($variety_arm)
                                {
                                    foreach ($variety_arm as $info)
                                    {
                                        ?>
                                        <li><?php echo $info['name'] ?></li>
                                    <?php
                                    }
                                }
                                ?>
                            </ol>
                        </td>
                        <td>
                            <table style="width:100%">
                                <tr>
                                    <?php
                                    $serial = 0;
                                    if ($variety_competitor_old)
                                    {
                                        $variety_competitor_type_ids = json_decode($variety_competitor_old['competitor_varieties'], TRUE);
                                        foreach ($variety_competitor_type_ids as $type_id => $variety_ids)
                                        {
                                            foreach ($variety_ids as $variety_id)
                                            {
                                                ++$serial;
                                                ?>
                                                <td>
                                                    <?php echo $serial . '. ' . $variety_competitor[$type_id][$variety_id]['name'] ?>
                                                    <small style="font-size: 10px">(<?php echo $variety_competitor[$type_id][$variety_id]['crop_type_name'] ?>, <?php echo $variety_competitor[$type_id][$variety_id]['competitor_name'] ?>)</small>
                                                </td>
                                                <?php
                                                echo ($serial % 2 == 0) ? '</tr><tr>' : '';
                                            }
                                        }
                                    }
                                    ?>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table style="width:100%">
                                <tr>
                                    <?php
                                    $serial = 0;
                                    if ($variety_competitor)
                                    {
                                        foreach ($variety_competitor as $competitor_crop_types)
                                        {
                                            foreach ($competitor_crop_types as $competitor_crop_type)
                                            {
                                                ++$serial;
                                                ?>
                                                <td>
                                                    <div class="checkbox" style="margin:0">
                                                        <label>
                                                            <input type="checkbox" name="items[<?php echo $competitor_crop_type['crop_type_id']; ?>][]" value="<?php echo $competitor_crop_type['id']; ?>"/>
                                                            <?php echo $competitor_crop_type['name'] ?>
                                                            <small style="font-size: 10px">(<?php echo $competitor_crop_type['crop_type_name'] ?>, <?php echo $competitor_crop_type['competitor_name'] ?>)</small>
                                                        </label>
                                                    </div>
                                                </td>
                                                <?php
                                                echo ($serial % 2 == 0) ? '</tr><tr>' : '';
                                            }
                                        }
                                    }
                                    ?>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers
        $(".datepicker").datepicker({dateFormat: 'dd-MM'});
    });
</script>
