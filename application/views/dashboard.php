<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$user = User_helper::get_user();

$CI = & get_instance();
?>
<div class="row widget">
    <?php
    if ($user->user_group == 0)
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_NOT_ASSIGNED_GROUP'); ?></h3>

        </div>
    <?php
    }
    ?>
    <?php
    if ($user->username_password_same)
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_USERNAME_PASSWORD_SAME'); ?></h3>

        </div>
    <?php
    }
    ?>
    <?php
    if ($CI->is_site_offline())
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_SITE_OFFLINE'); ?></h3>
        </div>
    <?php
    }
    ?>

    <div class="col-sm-12 text-center">

        <?php /* <div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
            <h1><?php echo $user->name; ?></h1>
            <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_profile_picture') . $user->image_location; ?>" alt="<?php echo $user->name; ?>">
        </div> */ ?>

        <!--<div class="col-lg-9 col-md-8 col-sm-7 col-xs-6" style="padding-top:15px">-->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:15px">

            <?php $CI->load->view('dashboard_items'); ?>

        </div>
    </div>
</div>
<div class="clearfix"></div>
