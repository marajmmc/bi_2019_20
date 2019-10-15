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

    <input type="hidden" id="id" name="id" value="<?php echo $item_info['id']; ?>"/>

    <div class="row widget">

        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-sm-12" id="items_container">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="6" style="text-align:center"><?php echo $title; ?></th>
                    </tr>
                    <tr class="table_head">
                        <th><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th style="text-align:center"><?php echo $this->lang->line('LABEL_VARIETY_ARM'); ?></th>
                        <th style="text-align:center"><?php echo $this->lang->line('LABEL_VARIETY_COMPETITOR_EXISTING'); ?></th>
                        <th style="text-align:center"><?php echo $this->lang->line('LABEL_VARIETY_COMPETITOR_EDITED'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo $item_info['crop_name']?></td>
                        <td><?php echo $item_info['crop_type_name']?></td>
                        <td>
                            <table style="width:100%">
                                <tr>
                                    <?php
                                    $serial=0;
                                    foreach($variety_arm as $info)
                                    {
                                        ++$serial;
                                        ?>
                                        <td><?php echo $serial.'. '.$info['name']?></td>
                                        <?php
                                        echo ($serial % 2 == 0) ? '</tr><tr>' : '';
                                    }
                                    ?>
                            </table>
                        </td>
                        <td>
                            <table style="width:100%">
                                <tr>
                                    <?php
                                    $serial=0;
                                    foreach($variety_competitor as $info)
                                    {
                                        ++$serial;
                                        ?>
                                        <td><?php echo $serial.'. '.$info['name']?></td>
                                        <?php
                                        echo ($serial % 2 == 0) ? '</tr><tr>' : '';
                                    }
                                    ?>
                            </table>
                        </td>
                        <td>
                            <table style="width:100%">
                                <tr>
                                <?php
                                $serial=0;
                                foreach($variety_competitor as $info)
                                {
                                    ++$serial;
                                    ?>
                                    <td>
                                        <div class="checkbox" style="margin:0">
                                            <label>
                                                <input type="checkbox" name="items[<?php echo $info['crop_id']; ?>][<?php echo $info['crop_type_id']; ?>][]" value="<?php echo $info['id']; ?>" <?php //echo $checked; ?> />
                                                <?php echo $info['name']?>
                                                <small style="font-size: 10px">(<?php echo $info['crop_type_name']?>, <?php echo $info['competitor_name']?>)</small>
                                            </label>
                                        </div>
                                    </td>
                                <?php
                                echo ($serial % 2 == 0) ? '</tr><tr>' : '';
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
    <?php
    if(sizeof($histories)>0)
    {
        ?>
        <div class="row widget">

            <div class="widget-header">
                <div class="title">
                    Cultivation Period Setup Histories
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
            foreach($histories as $history)
            {
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#accordion_basic_<?php echo $history['id']?>" href="#">+ History <?php echo $history['revision']?></a></label>
                    </h4>
                </div>
                <div id="accordion_basic_<?php echo $history['id']?>" class="panel-collapse collapse out">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="8" style="text-align:center">Cultivation Period Setup History (<?php echo $history['revision']?>)</th>
                        </tr>
                        <tr class="table_head">
                            <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
                            <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                            <th colspan="2" style="text-align:center">Before <?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
                            <th colspan="2" style="text-align:center"><?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
                            <th rowspan="2" style="text-align:center"><?php echo $this->lang->line('LABEL_DATE_CREATED'); ?></th>
                            <th rowspan="2" style="text-align:center"><?php echo $this->lang->line('LABEL_USER_CREATED'); ?></th>
                        </tr>
                        <tr>
                            <th style="text-align:center">Start Date</th>
                            <th style="text-align:center">End Date</th>
                            <th style="text-align:center">Start Date</th>
                            <th style="text-align:center">End Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo $history['crop_name']?></td>
                            <td><?php echo $history['crop_type_name']?></td>
                            <td><?php echo $history['date_start_old']?Bi_helper::cultivation_date_display($history['date_start_old']):''; ?></td>
                            <td><?php echo $history['date_end_old']?Bi_helper::cultivation_date_display($history['date_end_old']):''; ?></td>
                            <td><?php echo $history['date_start']?Bi_helper::cultivation_date_display($history['date_start']):''; ?></td>
                            <td><?php echo $history['date_end']?Bi_helper::cultivation_date_display($history['date_end']):''; ?></td>
                            <td><?php echo System_helper::display_date($history['date_created'])?></td>
                            <td><?php echo $history['user_created_full_name']?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            }
            ?>

        </div>
    <?php
    }
    ?>
</form>
<script type="text/javascript">
    jQuery(document).ready(function ($)
    {
        system_off_events(); // Triggers
        $(".datepicker").datepicker({dateFormat : 'dd-MM'});

    });
</script>