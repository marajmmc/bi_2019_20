<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI =& get_instance();
$user = User_helper::get_user();

$designation_name = 'Designation not set';
if (!empty($user->designation))
{
    $result = Query_helper::get_info($CI->config->item('table_login_setup_designation'), array('id', 'name', 'status', 'ordering'), array('status !="' . $this->config->item('system_status_delete') . '"', 'id =' . $user->designation), 1);
    if ($result)
    {
        $designation_name = $result['name'];
    }
}

if ($user)
{
    ?>
    <div class="collapse navbar-collapse user-dropdown" style="padding:0">
        <ul class="nav navbar-nav">
            <li class="dropdown" style="border-radius:0; border:none; background:none">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="padding:12px; color:#fff; font-size:1.2em">
                    <?php echo $user->name; ?> <span class="caret"></span> </a>

                <ul class="dropdown-menu user-dropdown-menus" style="left:auto; right:-1px !important; padding-bottom:0">
                    <li title="Designation">
                        <span class="glyphicon glyphicon-user one" style="width:25px"></span> <?php echo $designation_name; ?>
                    </li>
                    <li title="Cell Number">
                        <span class="glyphicon glyphicon-earphone one" style="width:25px"></span> <?php echo !empty($user->mobile_no) ? $user->mobile_no : 'Cell no. not set' ?>
                    </li>
                    <li  title="Email Address">
                        <span class="glyphicon glyphicon-envelope one" style="width:25px"></span> <?php echo !empty($user->email) ? $user->email : 'Email not set' ?>
                    </li>

                    <li role="separator" class="divider" style="padding:0; margin-bottom:0"></li>

                    <li style="padding:0"><a href="<?php echo site_url('home/logout'); ?>" style="padding:10px 20px;">
                            <span class="glyphicon glyphicon-off one" style="width:25px"></span> Logout </a></li>
                </ul>
            </li>
        </ul>
    </div>
    <style>
        .user-dropdown-menus > li {
            font-weight: normal;
            display: block;
            padding: 3px 20px;
            clear: both;
            line-height: 1.42857143;
            color: #333;
            white-space: nowrap;
        }

        li.no-link:hover > a,
        .user-dropdown li.dropdown > a.dropdown-toggle,
        .user-dropdown li.dropdown > a.dropdown-toggle:hover {
            background: none !important
        }
    </style>
<?php
}
?>
