<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_approve'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
        $data['accordion']['collapse']='in';
        echo $CI->load->view("info_basic", $data, true);
        ?>

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
                        <th style="text-align:center">Date Start</th>
                        <th style="text-align:center">Date End</th>
                        <th style="text-align:center">Date Start</th>
                        <th style="text-align:center">Date End</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($crops)
                    {
                        $cultivation_period=json_decode($item['cultivation_period'],TRUE);
                        $init_crop_id = -1;
                        $rowspan=0;
                        foreach ($crops as $crop)
                        {
                            $date_start_old="";
                            $date_end_old="";
                            if(isset($cultivation_period_old[$crop['crop_type_id']]))
                            {
                                $date_start_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_start']);
                                $date_end_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_end']);
                            }

                            $date_start=Bi_helper::cultivation_date_display(0);
                            $date_end=Bi_helper::cultivation_date_display(0);
                            if(isset($cultivation_period[$crop['crop_type_id']]['new']))
                            {
                                $date=explode('~',$cultivation_period[$crop['crop_type_id']]['new']);
                                $date_start=isset($date[0])?Bi_helper::cultivation_date_display($date[0]):'';
                                $date_end=isset($date[1])?Bi_helper::cultivation_date_display($date[1]):'';
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
                                    <?php
                                    if($date_start_old)
                                    {
                                        echo $date_start_old;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($date_end_old)
                                    {
                                        echo $date_end_old;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $date_start?$date_start:''; ?>
                                </td>
                                <td>
                                    <?php echo $date_end?$date_end:''; ?>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_APPROVE'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select name="item[status_approve]" class="form-control status-combo">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_approved'); ?>"><?php echo $CI->config->item('system_status_approved'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_rejected'); ?>"><?php echo $CI->config->item('system_status_rejected'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_rollback'); ?>"><?php echo $CI->config->item('system_status_rollback'); ?></option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <textarea id="remarks_approve" name="item[remarks_approve]" class="form-control"></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4"> &nbsp; </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Sure Approve Variety Cultivation Period Changes?">Save</button>
                </div>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>

</form>

<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers


    });
</script>
