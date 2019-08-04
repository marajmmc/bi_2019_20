<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_helper
{
    public static function performance_management()
    {
        $CI =& get_instance();
        $data = array();

        $user_id = 1;

        //$data['user']=Query_helper::get_info($CI->config->item('table_login_setup_user'),array('id','employee_id','user_name','status'),array('id ='.$user_id),1);


        //$data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);

    }
}