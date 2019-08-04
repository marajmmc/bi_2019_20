<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<form class="form_valid" id="report_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
    <input type="hidden" name="report[crop_id]" value="<?php echo $report['crop_id'] ?>">
    <input type="hidden" name="report[crop_type_id]" value="<?php echo $report['crop_type_id'] ?>">
    <div class="row widget">
        <div class="row show-grid">
            <div class="col-xs-6">
                <div class="widget-header">
                    <div class="title">
                        ARM Variety
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" id="select_all_arm">SELECT ALL</label>
                </div>
                <?php
                foreach($arm_varieties as $variety)
                {
                    ?>
                    <div class="checkbox">
                        <label><input type="checkbox" class="setup_arm" name="variety_ids[]" value="<?php echo $variety['variety_id']; ?>"><?php echo $variety['variety_name']; ?></label>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="col-xs-6">
                <div class="widget-header">
                    <div class="title">
                        Competitor Variety
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" id="select_all_competitor">SELECT ALL</label>
                </div>
                <?php
                foreach($competitor_varieties as $variety)
                {
                    ?>
                    <div class="checkbox">
                        <label><input type="checkbox" class="setup_competitor" name="variety_ids[]" value="<?php echo $variety['variety_id']; ?>"><?php echo $variety['variety_name']; ?></label>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

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
        </div>
    </div>
    <div class="clearfix"></div>
</form>
