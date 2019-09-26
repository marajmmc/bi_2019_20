<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK") . ' to Pending List',
    'href' => site_url($CI->controller_url . '/index/list')
);
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK") . ' to All List',
    'href' => site_url($CI->controller_url . '/index/list_all')
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
                    <th colspan="2" style="text-align:center">Existing <?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
                    <th colspan="2" style="text-align:center">Edited <?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
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
                        /*$date_start_old="";
                        $date_end_old="";
                        if(isset($cultivation_period_old[$crop['crop_type_id']]))
                        {
                            $date_start_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_start']);
                            $date_end_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_end']);
                        }*/

                        $date_start_old="";
                        $date_end_old="";
                        if(isset($cultivation_period[$crop['crop_type_id']]['old']))
                        {
                            $date_old=explode('~',$cultivation_period[$crop['crop_type_id']]['old']);
                            $date_start_old=isset($date_old[0])?Bi_helper::cultivation_date_display($date_old[0]):'';
                            $date_end_old=isset($date_old[1])?Bi_helper::cultivation_date_display($date_old[1]):'';
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

</div>
