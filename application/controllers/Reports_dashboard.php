<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports_dashboard extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
    }

    public function index()
    {
        $item = $this->input->post();
        $view_name = $item['report_type'];

        $data = $this->load->view($this->controller_url . "/" . $view_name, $item, true);
        echo json_encode($data, true);
        exit;
    }
}
