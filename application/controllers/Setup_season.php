<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_season extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->load->helper('bi');
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list") {
            $this->system_list($id);
        } elseif ($action == 'get_items') {
            $this->system_get_items();
        } elseif ($action == "add") {
            $this->system_add();
        } elseif ($action == "edit") {
            $this->system_edit($id);
        } elseif ($action == "save") {
            $this->system_save();
        } else {
            $this->system_list($id);
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            $data['title'] = "Season List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url);
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $items = Query_helper::get_info($this->config->item('table_bi_setup_season'), array('id', 'name', 'description', 'date_start', 'date_end'), array('status !="' . $this->config->item('system_status_delete') . '"'));
        foreach ($items as &$item) {
            $item['date_start'] = Bi_helper::cultivation_date_display($item['date_start']);
            $item['date_end'] = Bi_helper::cultivation_date_display($item['date_end']);
        }
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) {
            $time = time();
            $data['title'] = "Add New Season";
            $data["season"] = Array(
                'id' => 0,
                'name' => '',
                'description' => '',
                'date_start' => $time,
                'date_end' => '',
                'ordering' => 99,
                'status' => $this->config->item('system_status_active')
            );
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/add");

            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)) {
            if (($this->input->post('id'))) {
                $season_id = $this->input->post('id');
            } else {
                $season_id = $id;
            }

            $data['season'] = Query_helper::get_info($this->config->item('table_bi_setup_season'), '*', array('id =' . $season_id), 1);

            $data['title'] = "Edit Season (" . $data['season']['name'] . ')';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $season_id);
            $this->json_return($ajax);
        } else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $id = $this->input->post("id");
        $item_head = $this->input->post('season');
        $user = User_helper::get_user();
        if ($id > 0) {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        } else {
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();

            }
        }
        if (!$this->check_validation()) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        } else {
            $date_start = System_helper::get_time($item_head['date_start'] . '-1970');
            $date_end = System_helper::get_time($item_head['date_end'] . '-1970');
            if ($date_end < $date_start) {
                $date_end = System_helper::get_time($item_head['date_end'] . '-1971');
            }
            if ($date_end != 0) {
                $date_end += 24 * 3600 - 1;
            }
            $item_head['date_start'] = $date_start;
            $item_head['date_end'] = $date_end;

            $this->db->trans_start(); //DB Transaction Handle START
            if ($id > 0) {
                $item_head['user_updated'] = $user->user_id;
                $item_head['date_updated'] = time();
                Query_helper::update($this->config->item('table_bi_setup_season'), $item_head, array("id = " . $id));
            } else {
                $item_head['user_created'] = $user->user_id;
                $item_head['date_created'] = time();
                Query_helper::add($this->config->item('table_bi_setup_season'), $item_head);
            }
            $this->db->trans_complete(); //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE) {
                $save_and_new = $this->input->post('system_save_new_status');
                $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
                if ($save_and_new == 1) {
                    $this->system_add();
                } else {
                    $this->system_list();
                }
            } else {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }

    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('season[name]', $this->lang->line('LABEL_NAME'), 'required');
        $this->form_validation->set_rules('season[date_start]', $this->lang->line('LABEL_DATE_START'), 'required');
        $this->form_validation->set_rules('season[date_end]', $this->lang->line('LABEL_DATE_END'), 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
}
