<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_approve');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
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
        <table class="table table-bordered table-responsive system_table_details_view">
            <tbody>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DESCRIPTION');?></label></td>
                <td class="warning header_value" colspan="3"><label class="control-label"><?php echo $item['description'];?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">File</label></td>
                <td class="warning header_value" colspan="3">
                    <a href="<?php echo $CI->config->item('system_base_url_picture') . $item['file_location']; ?>" target="_blank" class="external blob">
                        <img src="<?php echo $CI->config->item('system_base_url_picture') . $item['file_location']; ?>" style="height:200px" alt="<?php echo $item['file_name']?>"/>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
        <hr/>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Approved/Rollback/Reject<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_approve" class="form-control" name="item[status_approve]">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $this->config->item('system_status_approved')?>"><?php echo $this->config->item('system_status_approved')?></option>
                    <option value="<?php echo $this->config->item('system_status_rollback')?>"><?php echo $this->config->item('system_status_rollback')?></option>
                    <option value="<?php echo $this->config->item('system_status_rejected')?>"><?php echo $this->config->item('system_status_rejected')?></option>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><span id="label_remarks"><?php echo $CI->lang->line('LABEL_REMARKS');?></span> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_approve]" id="remarks_approve" class="form-control" ></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Sure Approved Event?">Save</button>
                </div>
            </div>
            <div class="col-sm-4 col-xs-4">

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(".datepicker").datepicker({dateFormat : display_date_format});

        $("#status_approve").on('change', function(){
            $('#label_remarks').html($(this).val()+' Remarks');
        })
    });
</script>

