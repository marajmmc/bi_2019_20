<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
if ($file_type == $CI->config->item('system_file_type_image'))
{
    $back_page = '/index/list_image/';
    if ($whose == 'ARM')
    {
        $back_page = '/index/list_image_arm/';
    }
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_BACK"),
        'href' => site_url($CI->controller_url . $back_page . $item_head['variety_id'])
    );
}
else
{
    $back_page = '/index/list_video/';
    if ($whose == 'ARM')
    {
        $back_page = '/index/list_video_arm/';
    }
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_BACK"),
        'href' => site_url($CI->controller_url . $back_page . $item_head['variety_id'])
    );
}

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
<form id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_file'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>
    <input type="hidden" id="variety_id" name="item[variety_id]" value="<?php echo $item_head['variety_id']; ?>"/>
    <input type="hidden" name="whose" value="<?php echo $whose; ?>">
    <input type="hidden" name="item[file_type]" value="<?php echo $file_type; ?>">

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php
        $details = Bi_helper::get_variety_info($item_head['variety_id'], $whose);
        foreach ($details as &$detail)
        {
            $detail['collapse'] = 'out';
            echo $CI->load->view("info_basic", array('accordion' => $detail), true);
        }
        ?>

        <?php if ($file_type == $CI->config->item('system_file_type_image'))
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Picture</label>
                </div>
                <div class="col-xs-4">
                    <input type="file" class="browse_button_image" data-preview-container="#image_variety_info" data-preview-width="300" name="file_name">
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"></label>
                </div>
                <div class="col-xs-4" id="image_variety_info">
                    <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture') . $item['file_location']; ?>" alt="<?php echo $item['file_name']; ?>">
                </div>
            </div>
        <?php
        }
        else if ($file_type == $CI->config->item('system_file_type_video'))
        {
            ?>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Video</label>
                </div>
                <div class="col-xs-4">
                    <div id="video_preview_container_id">
                        <video width="300" controls id="video_preview_id">
                            <source src="<?php if (strlen($item['file_location']) > 0)
                            {
                                echo $CI->config->item('system_base_url_picture') . $item['file_location'];
                            } ?>" id="competitor_variety_video">
                        </video>
                    </div>
                    <div>
                        <input type="file" class="browse_button_video file_type_video" name="file_name" accept="video/*">
                    </div>
                </div>
            </div>
        <?php } ?>


        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></label>
            </div>
            <div class="col-xs-4">
                <textarea name="item[remarks]" class="form-control"><?php echo $item['remarks'] ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status" name="item[status]" class="form-control">
                    <option value="<?php echo $CI->config->item('system_status_active'); ?>" <?php if ($item['status'] == $CI->config->item('system_status_active'))
                    {
                        echo "selected='selected'";
                    } ?> ><?php echo $CI->lang->line('ACTIVE') ?></option>
                    <option value="<?php echo $CI->config->item('system_status_inactive'); ?>" <?php if ($item['status'] == $CI->config->item('system_status_inactive'))
                    {
                        echo "selected='selected'";
                    } ?> ><?php echo $CI->lang->line('INACTIVE') ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function () {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        $(".browse_button_image").filestyle({input: false, icon: false, buttonText: "Upload Picture", buttonName: "btn-primary"});
        $(".browse_button_video").filestyle({input: false, icon: false, buttonText: "Upload Video", buttonName: "btn-primary"});

        $(document).on("change", ".file_type_video", function (evt) {
            var $source = $('#competitor_variety_video');
            $source[0].src = URL.createObjectURL(this.files[0]);
            $source.parent()[0].load();
            var video = document.createElement('video');
            video.src = URL.createObjectURL(this.files[0]);
            video.onloadedmetadata = function () {
                window.URL.revokeObjectURL(this.src);
            }
        });

    });
</script>
