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
            <?php
            $item_varieties=json_decode($item['variety_ids'],true);
            ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th colspan="4" style="text-align:center"><?php echo $title; ?></th>
                </tr>
                <tr class="table_head">
                    <th style="text-align:center"><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th style="text-align:center"><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th style="text-align:center">Exist Varieties</th>
                    <th style="text-align:center">Editable Varieties</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($varieties as $crop)
                {
                    ?>
                    <tr>
                        <td rowspan="<?php echo sizeof($crop['crop_type'])+1?>"><?php echo $crop['crop_name']?></td>
                    </tr>
                    <?php
                    foreach($crop['crop_type'] as $type)
                    {
                        ?>
                        <tr>
                            <td><?php echo $type['crop_type_name']?></td>
                            <td>
                                <?php
                                if(isset($item_varieties[$type['crop_type_id']]['old']) && sizeof($item_varieties[$type['crop_type_id']]['old'])>0)
                                {
                                    foreach($item_varieties[$type['crop_type_id']]['old'] as $variety_id)
                                    {
                                        echo isset($variety_info[$variety_id])?$variety_info[$variety_id]['variety_name'].'<br />':'---';
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if(isset($item_varieties[$type['crop_type_id']]['new']) && sizeof($item_varieties[$type['crop_type_id']]['new'])>0)
                                {
                                    foreach($item_varieties[$type['crop_type_id']]['new'] as $variety_id)
                                    {
                                        echo isset($variety_info[$variety_id])?$variety_info[$variety_id]['variety_name'].'<br />':'---';
                                    }
                                }
                                ?>
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
