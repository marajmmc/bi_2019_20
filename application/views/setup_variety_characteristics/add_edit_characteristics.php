<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$back_page = '';
if ($whose == 'ARM')
{
    $back_page = '/index/list_arm';
}

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . $back_page)
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_SAVE"),
    'id' => 'button_action_save',
    'data-form' => '#save_form'
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_characteristics'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
    <input type="hidden" id="whose" name="whose" value="<?php echo $whose; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php
        foreach ($details as $detail)
        {
            echo $CI->load->view("info_basic", array('accordion' => $detail), true);
        }
        ?>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CHARACTERISTICS'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="item[characteristics]"><?php echo $item['characteristics']; ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Cultivation Period 1</label>
            </div>
            <div class="col-xs-2">
                <input type="text" name="item[date_start1]" class="form-control datepicker" value="<?php if ($item['date_start1'] != 0)
                {
                    echo date('d-F', $item['date_start1']);
                } ?>" readonly/>
            </div>
            <div class="col-xs-2">
                <input type="text" name="item[date_end1]" class="form-control datepicker" value="<?php if ($item['date_end1'] != 0)
                {
                    echo date('d-F', $item['date_end1']);
                } ?>" readonly/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Cultivation Period 2</label>
            </div>
            <div class="col-xs-2">
                <input type="text" name="item[date_start2]" class="form-control datepicker" value="<?php if ($item['date_start2'] != 0)
                {
                    echo date('d-F', $item['date_start2']);
                } ?>" readonly/>
            </div>
            <div class="col-xs-2">
                <input type="text" name="item[date_end2]" class="form-control datepicker" value="<?php if ($item['date_end2'] != 0)
                {
                    echo date('d-F', $item['date_end2']);
                } ?>" readonly/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRICE'); ?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($whose == 'ARM' && is_array($item['price']))
                {
                    ?>
                    <table class="table table-bordered">
                        <tr>
                            <th class="text-center"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                            <th class="text-center"><?php echo $CI->lang->line('LABEL_PRICE'); ?></th>
                        </tr>
                        <?php
                        foreach ($item['price'] as $price)
                        {
                            ?>
                            <tr>
                                <td><?php echo $price['pack_size_name']; ?></td>
                                <td class="text-right"><?php echo System_helper::get_string_amount($price['price']); ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                <?php
                }
                else
                {
                    ?>
                    <input type="text" name="item[price]" class="form-control float_type_positive" value="<?php echo $item['price']; ?>" style="text-align:left"/>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_USP'); ?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="item[comparison]"><?php echo $item['comparison']; ?></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="item[remarks]"><?php echo $item['remarks']; ?></textarea>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<style>label {
        margin-top: 5px
    }</style>
<script type="text/javascript">
    jQuery(document).ready(function () {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        $(".datepicker").datepicker({dateFormat: 'dd-MM'});
    });
</script>
