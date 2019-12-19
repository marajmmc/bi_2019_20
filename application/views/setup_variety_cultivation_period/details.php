<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK") . ' to List',
    'href' => site_url($CI->controller_url . '/index/list')
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php
    if(sizeof($histories)>0)
    {
        ?>
        <div class="row widget">
            <?php
            $serial=0;
            foreach($histories as $history)
            {
                $serial++;
                ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#accordion_basic_<?php echo $history['id']?>" href="#">+ History <?php echo $history['revision']?></a></label>
                        </h4>
                    </div>
                    <div id="accordion_basic_<?php echo $history['id']?>" class="panel-collapse collapse <?php if($serial==1){echo 'in';}else{echo 'out';}?>">

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th colspan="8" style="text-align:center">Cultivation Period Setup History (<?php echo $history['revision']?>)</th>
                            </tr>
                            <tr class="table_head">
                                <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
                                <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                                <th colspan="2" style="text-align:center">Existing <?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
                                <th colspan="2" style="text-align:center">Edited <?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
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
    else
    {
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning text-center h3">
                    Crop: <?php echo $item_info['crop_name']?>
                    <br/>
                    Type: <?php echo $item_info['crop_type_name']?>
                    <br/>
                    There are is no history
                </div>
            </div>
        </div>
    <?php
    }
    ?>

</div>
