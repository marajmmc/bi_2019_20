<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$configs_path=str_replace('bi_2019_20','login_2018_19',APPPATH).'config/';

require_once($configs_path.'user_group.php');
require_once($configs_path.'table_bi.php');
require_once($configs_path.'table_login.php');
