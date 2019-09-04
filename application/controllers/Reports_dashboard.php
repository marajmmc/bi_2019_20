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

    public function get_market_share()
    {
        /*$data='[
                  {
                    "ARM": 35
                  },
                  {
                    "Syngenta": "20"
                  },
                  {
                    "ACI": "15"
                  },
                  {
                    "Supreme Seed": "18"
                  },
                  {
                    "United Seed": "4"
                  },
                  {
                    "Metal Seed": "8"
                  },
               ]';*/

        $items = array(
            'ARM' => 35,
            'Syngenta' => 65
        );

        $this->json_return($items);
    }
}
