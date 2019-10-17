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
    <div class="widget-header" style="margin:0">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php
    if (sizeof($histories) > 0)
    {
        ?>
        <div class="row widget" style="margin:0">
            <?php
            $serial = 0;
            foreach ($histories as $history)
            {
                $serial++;
                ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#accordion_basic_<?php echo $history['id'] ?>" href="#">+ History <?php echo $history['revision'] ?></a></label>
                        </h4>
                    </div>
                    <div id="accordion_basic_<?php echo $history['id'] ?>" class="panel-collapse collapse <?php if ($serial == 1)
                    {
                        echo 'in';
                    }
                    else
                    {
                        echo 'out';
                    } ?>">

                        <table class="table table-bordered">
                            <thead>
                            <tr class="table_head">
                                <th><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
                                <th><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                                <th style="text-align:center">ARM <?php echo $this->lang->line('LABEL_VARIETY_NAME'); ?></th>
                                <th style="text-align:center">Compared Competitor <?php echo $this->lang->line('LABEL_VARIETY_NAME'); ?></th>
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
                                    <ol>
                                        <?php
                                        $variety_competitor_type_ids = json_decode($history['competitor_varieties'], TRUE);
                                        foreach ($variety_competitor_type_ids as $type_id => $variety_ids)
                                        {
                                            foreach ($variety_ids as $variety_id)
                                            {
                                                ?>
                                                <li>
                                                    <?php echo ($variety_competitor[$type_id][$variety_id]['name']) ? $variety_competitor[$type_id][$variety_id]['name'] : '- Not Found -'; ?>
                                                    <small style="font-size: 10px">(<?php echo $variety_competitor[$type_id][$variety_id]['crop_type_name'] ?>, <?php echo $variety_competitor[$type_id][$variety_id]['competitor_name'] ?>)</small>
                                                </li>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </ol>
                                </td>
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
                    Crop: <?php echo $item_info['crop_name'] ?>
                    <br/>Type: <?php echo $item_info['crop_type_name'] ?>
                    <br/><br/>There is NO history for Major Competitor Varieties.
                </div>
            </div>
        </div>
    <?php
    }
    ?>

</div>
