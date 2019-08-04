<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();


?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
    <!--<input type="hidden" name="id" value="<?php /*echo $id; */?>">-->
    <input type="hidden" name="item[crop_id]" value="<?php echo $item['crop_id'] ?>">
    <?php /* <input type="hidden" name="item[crop_type_id]" value="<?php echo $item['crop_type_id'] ?>"> */ ?>

    <div class="row widget">
        <div class="row show-grid">
            <div class="col-lg-12"  style="padding:0">
                <div class="col-xs-6" style="padding:0 15px 0 0">
                    <div class="widget-header">
                        <div class="title">
                            ARM Variety
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                    /* <div class="checkbox">
                    <label><input type="checkbox" id="select_all_arm">SELECT ALL</label>
                    </div> */
                    ?>
                    <?php
                    foreach ($arm_varieties as $variety)
                    {
                        $checked = ($variety['variety_id'] == $arm_variety_id)? 'checked':'';
                        ?>
                        <div class="checkbox">
                            <label><input type="radio" class="setup_arm" name="item[arm_variety_id]" value="<?php echo $variety['variety_id']; ?>" <?php echo $checked; ?> /> &nbsp;<?php echo $variety['variety_name']; ?>
                            </label>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <div class="col-xs-6" style="padding:0">
                    <div class="widget-header">
                        <div class="title">
                            Competitor Variety
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" id="select_all_competitor" /> SELECT ALL</label>
                    </div>
                    <?php
                    foreach ($competitor_varieties as $variety)
                    {
                        $checked = (in_array($variety['variety_id'], $competitor_variety_ids))? 'checked':'';
                        ?>
                        <div class="checkbox">
                            <label><input type="checkbox" class="setup_competitor" name="item[variety_ids][]" value="<?php echo $variety['variety_id']; ?>" <?php echo $checked; ?> /> <?php echo $variety['variety_name']; ?>
                            </label>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php /*
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <div class="action_button">
                    <button id="button_action_report" type="button" class="btn" data-form="#report_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                </div>
            </div>
            <div class="col-xs-4">

            </div>
        </div> */ ?>


    </div>
    <div class="clearfix"></div>
</form>
