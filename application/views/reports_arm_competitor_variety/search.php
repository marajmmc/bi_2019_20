<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

?>
<form class="form_valid" id="search_form" action="<?php echo site_url($CI->controller_url.'/index/list_variety');?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid" id="crop_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_id" name="report[crop_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($crops as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <?php /*
        <div style="display: none;" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_type_id" name="report[crop_type_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                </select>
            </div>
        </div>
        */ ?>

        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button type="submit" class="btn" data-form="#search_form"><?php echo $CI->lang->line("LABEL_LOAD_VARIETY"); ?></button>
                </div>

            </div>
            <div class="col-xs-4">

            </div>
        </div>

    </div>
    <div class="clearfix"></div>
</form>
<div id="variety_list_container">

</div>

<div id="system_report_container">

</div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(document).off("change", "#select_all_arm");
        $(document).off("change", "#select_all_competitor");
        $(document).off("change", "#crop_id");
        $(document).off("change", "#crop_type_id");

        $(document).on("change","#select_all_arm",function()
        {
            if($(this).is(':checked'))
            {
                $('.setup_arm').prop('checked', true);
            }
            else
            {
                $('.setup_arm').prop('checked', false);
            }

        });

        $(document).on("change","#select_all_competitor",function()
        {
            if($(this).is(':checked'))
            {
                $('.setup_competitor').prop('checked', true);
            }
            else
            {
                $('.setup_competitor').prop('checked', false);
            }

        });

        /*
        $(document).on("change","#crop_id",function()
        {
            $('#variety_list_container').html('');
            $('#system_report_container').html('');
            $("#crop_type_id").val("");
            $("#variety_id").val("");

            var crop_id=$('#crop_id').val();
            if(crop_id>0)
            {
                if(system_types[crop_id]!==undefined)
                {
                    $('#crop_type_id_container').show();
                    $('#variety_id_container').hide();
                    $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
                }
                else
                {
                    $('#crop_type_id_container').hide();
                }
            }
            else
            {
                $('#crop_type_id_container').hide();
                $('#variety_id_container').hide();
            }
        });
        */
    });
</script>