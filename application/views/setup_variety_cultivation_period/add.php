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

    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

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
                        <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th colspan="2" style="text-align:center">Before <?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
                        <th colspan="2" style="text-align:center"><?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
                    </tr>
                    <tr>
                        <th style="text-align:center">Start Date</th>
                        <th style="text-align:center">End Date</th>
                        <th style="text-align:center">Start Date</th>
                        <th style="text-align:center">End Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (sizeof($crops)>0)
                    {
                        $cultivation_period=json_decode($item['cultivation_period'],TRUE);
                        $init_crop_id = -1;
                        $rowspan=0;
                        foreach ($crops as $crop)
                        {
                            /*$date_start_old="";
                            $date_end_old="";
                            if(isset($cultivation_period_old[$crop['crop_type_id']]))
                            {
                                $date_start_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_start']);
                                $date_end_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_end']);
                            }*/

                            $date_start=Bi_helper::cultivation_date_display(0);
                            $date_end=Bi_helper::cultivation_date_display(0);
                            if(isset($cultivation_period[$crop['crop_type_id']]))
                            {
                                $date=explode('~',$cultivation_period[$crop['crop_type_id']]);
                                $date_start=Bi_helper::cultivation_date_display($date[0]);
                                $date_end=Bi_helper::cultivation_date_display($date[1]);
                            }

                            $rowspan = $crop_type_count[$crop['crop_id']];

                            ?>
                            <tr>
                                <?php
                                $rowspan = 1;
                                if ($init_crop_id != $crop['crop_id'])
                                {
                                    $rowspan = $crop_type_count[$crop['crop_id']];
                                    ?>
                                    <td rowspan="<?php echo $rowspan; ?>"><?php echo $crop['crop_name']; ?></td>
                                    <?php
                                    $init_crop_id = $crop['crop_id'];
                                }
                                ?>
                                <td><?php echo $crop['crop_type_name']?></td>
                                <td>
                                    <?php echo $date_start?$date_start:''; ?>
                                </td>
                                <td>
                                    <?php echo $date_end?$date_end:''; ?>
                                </td>
                                <td>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type="text" name="items[<?php echo $crop['crop_type_id']; ?>][date_start]" class="form-control date_large" value="<?php echo $date_start?$date_start:''; ?>" readonly="true" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                </td>
                                <td>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type="text" name="items[<?php echo $crop['crop_type_id']; ?>][date_end]" class="form-control date_large" value="<?php echo $date_end?$date_end:''; ?>" readonly="true" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    else
                    {
                        ?>
                        <tr>
                            <th colspan="21"> Data not found</th>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function ($)
    {
        system_off_events(); // Triggers
        $(".date_large").datepicker({dateFormat : "dd-M",changeMonth: true,changeYear: true,yearRange: "c-2:c+2"});

    });
</script>
