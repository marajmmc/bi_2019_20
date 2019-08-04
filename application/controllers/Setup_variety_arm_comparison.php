<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_variety_arm_comparison extends Root_Controller
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

    public function index($action = "list", $id = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "edit")
        {
            $this->system_edit($id);
        }
        elseif ($action == "save")
        {
            $this->system_save();
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }

    private function get_preference_headers($method)
    {
        $data = array();
        if ($method == 'list')
        {
            $data['id'] = 1;
            $data['arm_variety_name'] = 1;
            $data['crop_type_name'] = 1;
            $data['crop_name'] = 1;
            $data['comparison_setup'] = 1;
            $data['competitor_variety_name'] = 1;
        }
        return $data;
    }

    private function system_set_preference($method = 'list')
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_' . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Competitor Variety List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $items = array(); // Final Array
        $arm_varieties = array(); // Auxiliary Array

        $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' arm_v');
        $this->db->select('arm_v.id arm_variety_id, arm_v.name arm_variety_name, arm_v.ordering');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = arm_v.crop_type_id', 'INNER');
        $this->db->select('crop_types.id crop_type_id, crop_types.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'INNER');
        $this->db->select('crops.id crop_id, crops.name crop_name');

        $this->db->where('arm_v.whose', 'ARM');
        $this->db->where('arm_v.status', $this->config->item('system_status_active'));
        $this->db->where('crop_types.status', $this->config->item('system_status_active'));
        $this->db->where('crops.status', $this->config->item('system_status_active'));
        $this->db->order_by('crops.ordering', 'ASC');
        $this->db->order_by('arm_v.ordering', 'ASC');
        $results = $this->db->get()->result_array();
        foreach ($results as $result)
        {
            $arm_varieties[$result['arm_variety_id']] = array(
                'id' => $result['arm_variety_id'],
                'arm_variety_name' => $result['arm_variety_name'],
                'crop_type_name' => $result['crop_type_name'],
                'crop_name' => $result['crop_name'],
                'competitor_variety_name' => '',
                'comparison_setup' => 'Not Done'
            );
        }

        $this->db->from($this->config->item('table_bi_setup_arm_variety_comparison') . ' vc');
        $this->db->select('vc.arm_variety_id, vc.competitor_variety_id');

        $this->db->join($this->config->item('table_bi_setup_competitor_variety') . ' com_var', "com_var.id = vc.competitor_variety_id AND com_var.whose='Competitor'", 'INNER');
        $this->db->select('com_var.name competitor_variety_name, com_var.competitor_id');

        $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', "competitor.id = com_var.competitor_id", 'INNER');
        $this->db->select("GROUP_CONCAT( com_var.name, ' (', competitor.name, ')' SEPARATOR ', <br>') as competitor_names");

        $this->db->group_by('vc.arm_variety_id');
        $this->db->where('vc.status', $this->config->item('system_status_active'));
        $com_results = $this->db->get()->result_array();
        foreach ($com_results as $com_result)
        {
            $arm_varieties[$com_result['arm_variety_id']]['competitor_variety_name'] = $com_result['competitor_names'];
            $arm_varieties[$com_result['arm_variety_id']]['comparison_setup'] = 'Done';
        }
        foreach ($arm_varieties as $arm_variety)
        {
            $items[] = $arm_variety;
        }
        $this->json_return($items);
    }

    private function system_edit($id)
    {
        if ((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }
            $data = array();

            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' arm_v');
            $this->db->select('arm_v.id arm_variety_id, arm_v.name arm_variety_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = arm_v.crop_type_id', 'INNER');
            $this->db->select('crop_types.id crop_type_id, crop_types.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'INNER');
            $this->db->select('crops.id crop_id, crops.name crop_name');

            $this->db->where('arm_v.id', $item_id);
            $this->db->where('arm_v.whose', 'ARM');
            $this->db->where('arm_v.status', $this->config->item('system_status_active'));
            $this->db->where('crop_types.status', $this->config->item('system_status_active'));
            $this->db->where('crops.status', $this->config->item('system_status_active'));
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Variety ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['item']['competitor_variety_ids'] = array();

            // Fetching Data from Comparison table
            $this->db->from($this->config->item('table_bi_setup_arm_variety_comparison'));
            $this->db->select('competitor_variety_id');

            $this->db->where('arm_variety_id', $item_id);
            $this->db->where('status', $this->config->item('system_status_active'));
            $comparison_results = $this->db->get()->result_array();
            if ($comparison_results)
            {
                foreach ($comparison_results as $comparison_result)
                {
                    $data['item']['competitor_variety_ids'][] = $comparison_result['competitor_variety_id'];
                }
            }
            $data_variety_list = $this->get_variety_list_data($data['item']);
            $data['item'] = array_merge($data['item'], $data_variety_list);

            $data['title'] = "ARM Variety Comparison Setup";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $item = $this->input->post('item');
        $item_id = $item['arm_variety_id'];
        $user = User_helper::get_user();
        $time = time();

        if ($item_id > 0) //EDIT
        {
            //Permission Checking
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' arm_v');
            $this->db->select('arm_v.id arm_variety_id, arm_v.name arm_variety_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = arm_v.crop_type_id', 'INNER');
            $this->db->select('crop_types.id crop_type_id, crop_types.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'INNER');
            $this->db->select('crops.id crop_id, crops.name crop_name');

            $this->db->where('arm_v.id', $item['arm_variety_id']);
            $this->db->where('arm_v.whose', 'ARM');
            $this->db->where('arm_v.status', $this->config->item('system_status_active'));
            $this->db->where('crop_types.status', $this->config->item('system_status_active'));
            $this->db->where('crops.status', $this->config->item('system_status_active'));
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ARM Variety ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
        }
        else //ADD
        {
            //Permission Checking
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        //Validation Checking
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_bi_setup_arm_variety_comparison'));
        $this->db->select('competitor_variety_id');

        $this->db->where('crop_id', $item['crop_id']);
        $this->db->where('status', $this->config->item('system_status_active'));
        $this->db->where('arm_variety_id', $item['arm_variety_id']);
        $old_varieties = $this->db->get()->result_array();

        $variety_ids_delete = array();
        $variety_ids_update = array();
        $varieties_insert = array();

        if ($old_varieties)
        {
            $variety_ids_delete = array_column($old_varieties, 'competitor_variety_id');
            foreach ($item['competitor_variety_ids'] as $competitor_id => $competitor_variety_ids)
            {
                foreach ($competitor_variety_ids as $competitor_variety_id)
                {
                    if (($delete_key = array_search($competitor_variety_id, $variety_ids_delete)) !== false)
                    {
                        $variety_ids_update[] = $competitor_variety_id;
                        unset($variety_ids_delete[$delete_key]);
                    }
                    else
                    {
                        $varieties_insert[] = array(
                            'crop_id' => $item['crop_id'],
                            'arm_variety_id' => $item_id,
                            'competitor_variety_id' => $competitor_variety_id,
                            'status' => $this->config->item('system_status_active'),
                            'revision_count' => 1,
                            'date_created' => $time,
                            'user_created' => $user->user_id
                        );
                    }
                }
            }
        }
        else
        {
            $varieties_insert = array();
            foreach ($item['competitor_variety_ids'] as $competitor_id => $competitor_variety_ids)
            {
                foreach ($competitor_variety_ids as $competitor_variety_id)
                {
                    $varieties_insert[] = array(
                        'crop_id' => $item['crop_id'],
                        'arm_variety_id' => $item_id,
                        'competitor_variety_id' => $competitor_variety_id,
                        'status' => $this->config->item('system_status_active'),
                        'revision_count' => 1,
                        'date_created' => $time,
                        'user_created' => $user->user_id
                    );
                }
            }
        }

        $this->db->trans_start(); //DB Transaction Handle START
        if ($variety_ids_delete) // Deleting
        {
            $delete_item = array(
                'status' => $this->config->item('system_status_inactive'),
                'date_deleted' => $time,
                'user_deleted' => $user->user_id
            );
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            $this->db->where_in('competitor_variety_id', $variety_ids_delete);
            $this->db->update($this->config->item('table_bi_setup_arm_variety_comparison'), $delete_item);
        }
        if ($variety_ids_update) // Deleting
        {
            $update_item = array(
                'status' => $this->config->item('system_status_active'),
                'date_updated' => $time,
                'user_updated' => $user->user_id
            );
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            $this->db->where_in('competitor_variety_id', $variety_ids_update);
            $this->db->update($this->config->item('table_bi_setup_arm_variety_comparison'), $update_item);
        }
        if ($varieties_insert) // Deleting
        {
            foreach ($varieties_insert as $insert)
            {
                Query_helper::add($this->config->item('table_bi_setup_arm_variety_comparison'), $insert, FALSE);
            }
        }
        $this->db->trans_complete(); //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $ajax['status'] = true;
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function get_variety_list_data($items)
    {
        // HERE: $items = array('crop_id'=>'', 'crop_type_id'=>'');
        $data = array();

        // Competitor Varieties from BI
        $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' v');
        $this->db->select('v.id variety_id,v.name variety_name');

        $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = v.competitor_id', 'INNER');
        $this->db->select('competitor.id competitor_id, competitor.name competitor_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = v.crop_type_id', 'INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        if ($items['crop_id'] > 0)
        {
            $this->db->where('crop.id', $items['crop_id']);
        }
        $this->db->where('v.whose', 'Competitor');
        $this->db->where('v.status', $this->config->item('system_status_active'));
        $this->db->order_by('competitor.name', 'ASC');
        $this->db->order_by('v.name', 'ASC');
        $results = $this->db->get()->result_array();
        foreach ($results as $result)
        {
            $data['competitor_varieties'][$result['competitor_id']]['competitor_name'] = $result['competitor_name'];
            if (!isset($data['competitor_varieties'][$result['competitor_id']]['compared_varieties']))
            {
                $data['competitor_varieties'][$result['competitor_id']]['compared_varieties'] = 0;
            }
            if (in_array($result['variety_id'], $items['competitor_variety_ids']))
            {
                $data['competitor_varieties'][$result['competitor_id']]['compared_varieties'] += 1;
            }
            $data['competitor_varieties'][$result['competitor_id']]['varieties'][] = array(
                'variety_id' => $result['variety_id'],
                'variety_name' => $result['variety_name'],
                'crop_type_name' => $result['crop_type_name']
            );
        }

        return $data;
    }

    private function check_validation()
    {
        $item = $this->input->post('item');
        if (!isset($item['competitor_variety_ids']) || !$item['competitor_variety_ids'])
        {
            $this->message = 'Atleast 1 competitor variety need to select';
            return false;
        }
        return true;
    }
}
