<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_variety_characteristics extends Root_Controller
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
        $this->load->helper('bi_helper');
    }

    public function index($action = "list", $id = 0, $id1 = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        if ($action == "list_arm")
        {
            $this->system_list('ARM');
        }
        elseif ($action == "get_items_arm")
        {
            $this->system_get_items('ARM');
        }
        elseif ($action == 'add_edit_characteristics')
        {
            $this->system_add_edit_characteristics($id);
        }
        elseif ($action == 'add_edit_characteristics_arm')
        {
            $this->system_add_edit_characteristics($id, 'ARM');
        }
        elseif ($action == "save_characteristics")
        {
            $this->system_save_characteristics();
        }
        //-------------- Image & Video Competitor ----------------
        elseif ($action == "list_image")
        {
            $this->system_list_file($id, $this->config->item('system_file_type_image'));
        }
        elseif ($action == "list_video")
        {
            $this->system_list_file($id, $this->config->item('system_file_type_video'));
        }
        elseif ($action == "get_items_files")
        {
            $this->system_get_items_files($id);
        }
        elseif ($action == 'add_edit_image')
        {
            $this->system_add_edit_file($id, $this->config->item('system_file_type_image'), $id1);
        }
        elseif ($action == 'add_edit_video')
        {
            $this->system_add_edit_file($id, $this->config->item('system_file_type_video'), $id1);
        }
        elseif ($action == "save_file")
        {
            $this->system_save_file();
        }
        //------------------ Image & Video ARM ------------------
        elseif ($action == "list_image_arm")
        {
            $this->system_list_file($id, $this->config->item('system_file_type_image'), 'ARM');
        }
        elseif ($action == "list_video_arm")
        {
            $this->system_list_file($id, $this->config->item('system_file_type_video'), 'ARM');
        }
        elseif ($action == 'add_edit_image_arm')
        {
            $this->system_add_edit_file($id, $this->config->item('system_file_type_image'), $id1, 'ARM');
        }
        elseif ($action == 'add_edit_video_arm')
        {
            $this->system_add_edit_file($id, $this->config->item('system_file_type_video'), $id1, 'ARM');
        }
        //------------------------------------------------------
        elseif ($action == "details")
        {
            $this->system_details($id);
        }
        elseif ($action == "details_arm")
        {
            $this->system_details($id, 'ARM');
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_arm")
        {
            $this->system_set_preference('list_arm');
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
        if ($method == 'list' || $method == 'list_arm')
        {
            $data['id'] = 1;
            $data['name'] = 1;
            $data['crop_name'] = 1;
            $data['crop_type_name'] = 1;
            $data['characteristics'] = 1;
            $data['number_of_images'] = 1;
            $data['number_of_videos'] = 1;
        }
        else if ($method == 'list_file')
        {
            $data['id'] = 1;
            $data['file_name'] = 1;
            $data['remarks'] = 1;
            $data['status'] = 1;
        }
        else
        {
            $data = array();
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

    private function system_list($whose = 'Competitor')
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $method = 'list';
            $view = '/list';
            if ($whose == 'ARM')
            {
                $method = 'list_arm';
                $view = '/list_arm';
            }

            $user = User_helper::get_user();
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = $whose . " Variety List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . $view, $data, true));
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

    private function system_get_items($whose = 'Competitor')
    {
        if ($whose == 'ARM')
        {
            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' v');
            $this->db->select('v.id,v.name');
        }
        else
        {
            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' v');
            $this->db->select('v.id,v.name');
        }

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
        $this->db->select('type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_bi_setup_competitor_variety_characteristics') . ' characteristics', 'characteristics.variety_id = v.id', 'LEFT');
        $this->db->select('characteristics.characteristics');

        $this->db->join($this->config->item('table_bi_setup_competitor_variety_files') . ' files_images', 'files_images.variety_id =v.id AND files_images.file_type="' . $this->config->item('system_file_type_image') . '"  AND files_images.status="' . $this->config->item('system_status_active') . '"', 'LEFT');
        $this->db->select('count(DISTINCT files_images.id) number_of_images', true);

        $this->db->join($this->config->item('table_bi_setup_competitor_variety_files') . ' files_videos', 'files_videos.variety_id =v.id AND files_videos.file_type="' . $this->config->item('system_file_type_video') . '" AND files_videos.status="' . $this->config->item('system_status_active') . '"', 'LEFT');
        $this->db->select('count(DISTINCT files_videos.id) number_of_videos', true);

        $this->db->order_by('crop.ordering', 'ASC');
        $this->db->order_by('type.ordering', 'ASC');
        $this->db->order_by('v.ordering', 'ASC');
        $this->db->where('v.status !=', $this->config->item('system_status_delete'));
        $this->db->where('v.whose', $whose);
        $this->db->group_by('v.id');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            if ($item['characteristics'])
            {
                $item['characteristics'] = "Done";
            }
            else
            {
                $item['characteristics'] = "Not Done";
            }
        }

        /*echo '<pre>';
        print_r($whose);
        print_r($items);
        echo '</pre>'; die('44455555555555');*/

        $this->json_return($items);
    }

    /*
    private function system_list_characteristics_arm()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list_characteristics_arm';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "ARM Variety List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_characteristics_arm", $data, true));
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

    private function system_get_items_characteristics_arm()
    {
        $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' v');
        $this->db->select('v.id,v.name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
        $this->db->select('type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_bi_setup_competitor_variety_characteristics') . ' characteristics', 'characteristics.variety_id = v.id', 'LEFT');
        $this->db->select('characteristics.characteristics');

        $this->db->join($this->config->item('table_bi_setup_competitor_variety_files') . ' files_images', 'files_images.variety_id =v.id AND files_images.file_type="' . $this->config->item('system_file_type_image') . '"  AND files_images.status="' . $this->config->item('system_status_active') . '"', 'LEFT');
        $this->db->select('count(DISTINCT files_images.id) number_of_images', true);

        $this->db->join($this->config->item('table_bi_setup_competitor_variety_files') . ' files_videos', 'files_videos.variety_id =v.id AND files_videos.file_type="' . $this->config->item('system_file_type_video') . '" AND files_videos.status="' . $this->config->item('system_status_active') . '"', 'LEFT');
        $this->db->select('count(DISTINCT files_videos.id) number_of_videos', true);

        $this->db->order_by('crop.ordering', 'ASC');
        $this->db->order_by('type.ordering', 'ASC');
        $this->db->order_by('v.ordering', 'ASC');
        $this->db->where('v.status !=', $this->config->item('system_status_delete'));
        $this->db->where('v.whose', 'Competitor');
        $this->db->group_by('v.id');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            if ($item['characteristics'])
            {
                $item['characteristics'] = "Done";
            }
            else
            {
                $item['characteristics'] = "Not Done";
            }
        }
        $this->json_return($items);
    }
    */


    private function system_add_edit_characteristics($id, $whose = 'Competitor')
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
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
            $data['details'] = Bi_helper::get_variety_info($item_id, $whose, true);
            if (!$data['details'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Variety ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $item = Query_helper::get_info($this->config->item('table_bi_setup_competitor_variety_characteristics'), '*', array('variety_id =' . $item_id), 1);
            if ($item)
            {
                $data['item'] = $item;
                if (!($data['item']['price'] > 0))
                {
                    $data['item']['price'] = '';
                }
            }
            else
            {
                $data['item']['characteristics'] = '';
                $data['item']['price'] = '';
                $data['item']['comparison'] = '';
                $data['item']['remarks'] = '';
                $data['item']['date_start1'] = '';
                $data['item']['date_end1'] = '';
                $data['item']['date_start2'] = '';
                $data['item']['date_end2'] = '';
            }

            $page_url = 'add_edit_characteristics/';
            if ($whose == 'ARM')
            {
                $page_url = 'add_edit_characteristics_arm/';


                $this->db->from($this->config->item('table_login_setup_classification_variety_price') . ' vp');
                $this->db->select('vp.variety_id, vp.pack_size_id, vp.price, vp.price_net');

                $this->db->join($this->config->item('table_login_setup_classification_pack_size') . ' ps', 'ps.id = vp.pack_size_id', 'INNER');
                $this->db->select('ps.name pack_size_name');

                $this->db->where('vp.variety_id', $item_id);
                $result = $this->db->get()->result_array();
                if ($result)
                {
                    $data['item']['price'] = $result;
                }
            }

            $data['whose'] = $whose;
            $data['id'] = $item_id;
            $data['title'] = "Edit {$whose} Variety Characteristics";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit_characteristics", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $page_url . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_characteristics()
    {
        /*echo '<pre>';
        print_r($this->input->post());
        echo '</pre>';
        die('UUUUUUUUUUUUUUU');*/

        $variety_id = $this->input->post("id");
        $whose = $this->input->post("whose");
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();
        if (!((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if (!$this->check_validation_characteristics())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        else
        {
            if ($whose == 'ARM')
            {
                $variety = Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'), '*', array('id =' . $variety_id, 'whose ="ARM"'), 1);
            }
            else
            {
                $variety = Query_helper::get_info($this->config->item('table_bi_setup_competitor_variety'), '*', array('id =' . $variety_id, 'whose ="Competitor"'), 1);
            }

            if (!$variety)
            {
                System_helper::invalid_try(__FUNCTION__, $variety_id, 'Variety Id Non-Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $old_item = Query_helper::get_info($this->config->item('table_bi_setup_competitor_variety_characteristics'), '*', array('variety_id =' . $variety_id), 1);

            $item['date_start1'] = System_helper::get_time($item['date_start1'] . '-1970');
            $item['date_end1'] = System_helper::get_time($item['date_end1'] . '-1970');
            if ($item['date_end1'] < $item['date_start1'])
            {
                $item['date_end1'] = System_helper::get_time($this->input->post('date_end1') . '-1971');
            }
            if ($item['date_end1'] != 0)
            {
                $item['date_end1'] += 24 * 3600 - 1;
            }
            $item['date_start2'] = System_helper::get_time($item['date_start2'] . '-1970');
            $item['date_end2'] = System_helper::get_time($item['date_end2'] . '-1970');
            if ($item['date_end2'] < $item['date_start2'])
            {
                $item['date_end2'] = System_helper::get_time($this->input->post('date_end2') . '-1971');
            }
            if ($item['date_end2'] != 0)
            {
                $item['date_end2'] += 24 * 3600 - 1;
            }

            $this->db->trans_start(); //DB Transaction Handle START

            if ($old_item)
            {
                $item['user_updated'] = $user->user_id;
                $item['date_updated'] = $time;
                $this->db->set('revision_count', 'revision_count+1', FALSE);
                Query_helper::update($this->config->item('table_bi_setup_competitor_variety_characteristics'), $item, array("id = " . $old_item['id']));
            }
            else
            {
                $item['variety_id'] = $variety_id;
                $item['revision_count'] = 1;
                $item['user_created'] = $user->user_id;
                $item['date_created'] = $time;
                Query_helper::add($this->config->item('table_bi_setup_competitor_variety_characteristics'), $item);
            }
            $this->db->trans_complete(); //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list($whose);
            }
            else
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }

    private function system_list_file($variety_id, $file_type, $whose = 'Competitor')
    {
        $user = User_helper::get_user();
        $method = 'list_file';
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
        {
            if ($variety_id > 0)
            {
                $item_id = $variety_id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }

            if ($whose == 'ARM')
            {
                $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' v');
                $this->db->select('v.id,v.name');
            }
            else
            {
                $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' v');
                $this->db->select('v.id,v.name');
            }
            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
            $this->db->select('type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $this->db->select('crop.name crop_name');

            $this->db->where('v.id', $item_id);
            $this->db->where('v.whose', $whose);
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try('list_file ' . $file_type, $item_id, 'Variety Id Non-Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            if ($file_type == $this->config->item('system_file_type_image'))
            {
                $data['file_type'] = $this->config->item('system_file_type_image');
                $data['title'] = "Image List ( {$whose} Variety : " . $data['item']['name'] . ' )';
            }
            else
            {
                $data['file_type'] = $this->config->item('system_file_type_video');
                $data['title'] = "Video List ( {$whose} Variety : " . $data['item']['name'] . ' )';
            }

            $data['whose'] = $whose;
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_file", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }

            if ($file_type == $this->config->item('system_file_type_image'))
            {
                $page_url = 'list_image';
                if ($whose == 'ARM')
                {
                    $page_url = 'list_image_arm';
                }
            }
            else
            {
                $page_url = 'list_video';
                if ($whose == 'ARM')
                {
                    $page_url = 'list_video_arm';
                }
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $page_url . '/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_files()
    {
        $variety_id = $this->input->post('variety_id');
        $file_type = $this->input->post('file_type');

        $this->db->from($this->config->item('table_bi_setup_competitor_variety_files') . ' files');
        $this->db->select('files.*');
        $this->db->where('files.variety_id', $variety_id);
        if ($file_type == $this->config->item('system_file_type_image'))
        {
            $this->db->where('files.file_type', $this->config->item('system_file_type_image'));
        }
        else
        {
            $this->db->where('files.file_type', $this->config->item('system_file_type_video'));
        }
        $this->db->where('files.status !=', $this->config->item('system_status_delete'));
        $items = $this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_add_edit_file($variety_id, $file_type, $file_id = 0, $whose = 'Competitor')
    {
        if ($file_id > 0)
        {
            $item_id = $file_id; //edit by refresh
        }
        elseif (($this->input->post('id')) > 0)
        {
            $item_id = $this->input->post('id'); //edit from list
        }
        else
        {
            $item_id = 0; //new
        }
        if ($item_id > 0)
        {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        if ($whose == 'ARM')
        {
            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' v');
            $this->db->select('v.id variety_id, v.name');
        }
        else
        {
            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' v');
            $this->db->select('v.id variety_id, v.name');
        }
        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
        $this->db->select('type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->where('v.id', $variety_id);
        $this->db->where('v.whose', $whose);
        $data['item_head'] = $this->db->get()->row_array();
        if (!$data['item_head'])
        {
            System_helper::invalid_try(__FUNCTION__, $variety_id, 'Variety Id Non-Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

        if ($item_id > 0) // EDIT
        {
            $this->db->from($this->config->item('table_bi_setup_competitor_variety_files') . ' files');
            $this->db->select('files.*');
            $this->db->where('files.id', $item_id);
            if ($file_type == $this->config->item('system_file_type_image'))
            {
                $this->db->where('files.file_type', $this->config->item('system_file_type_image'));
            }
            else if ($file_type == $this->config->item('system_file_type_video'))
            {
                $this->db->where('files.file_type', $this->config->item('system_file_type_video'));
            }

            $this->db->where('files.status !=', $this->config->item('system_status_delete'));
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                if ($file_type == $this->config->item('system_file_type_image'))
                {
                    System_helper::invalid_try('Edit_file(image)', $item_id, 'File Id Non-Exists');
                }
                else if ($file_type == $this->config->item('system_file_type_video'))
                {
                    System_helper::invalid_try('Edit_file(video)', $item_id, 'File Id Non-Exists');
                }

                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($file_type == $this->config->item('system_file_type_image'))
            {
                $data['title'] = 'Edit Image Of ' . $whose . ' Variety (' . $data['item_head']['name'] . ')';
            }
            else if ($file_type == $this->config->item('system_file_type_video'))
            {
                $data['title'] = 'Edit Video Of ' . $whose . ' Variety (' . $data['item_head']['name'] . ')';
            }
        }
        else // ADD
        {
            $data['item'] = array(
                'id' => 0,
                'remarks' => '',
                'status' => $this->config->item('system_status_active')
            );
            if ($file_type == $this->config->item('system_file_type_image'))
            {
                $data['item']['file_name'] = 'no_image.jpg';
                $data['item']['file_location'] = 'images/no_image.jpg';
                $data['title'] = "Add Image ( {$whose} Variety : " . $data['item_head']['name'] . " )";
            }
            else if ($file_type == $this->config->item('system_file_type_video'))
            {
                $data['item']['file_name'] = '';
                $data['item']['file_location'] = '';
                $data['title'] = "Add Video ( {$whose} Variety : " . $data['item_head']['name'] . " )";
            }

        }
        $data['whose'] = $whose;
        $data['file_type'] = $file_type;

        $ajax['status'] = true;
        $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit_file", $data, true));
        if ($this->message)
        {
            $ajax['system_message'] = $this->message;
        }

        if ($file_type == $this->config->item('system_file_type_image'))
        {
            $page_url = 'add_edit_image';
            if ($whose == 'ARM')
            {
                $page_url = 'add_edit_image_arm';
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $page_url . '/' . $variety_id . '/' . $item_id);
        }
        else if ($file_type == $this->config->item('system_file_type_video'))
        {
            $page_url = 'add_edit_video';
            if ($whose == 'ARM')
            {
                $page_url = 'add_edit_video_arm';
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $page_url . '/' . $variety_id . '/' . $item_id);
        }

        $this->json_return($ajax);
    }

    private function system_save_file()
    {
        $id = $this->input->post("id");
        $whose = $this->input->post("whose");
        $item = $this->input->post('item');
        $file_type = $item['file_type'];
        $user = User_helper::get_user();
        $time = time();

        if ($id > 0)
        {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_bi_setup_competitor_variety_files') . ' files');
            $this->db->select('files.*');
            $this->db->where('files.id', $id);
            if ($file_type == $this->config->item('system_file_type_image'))
            {
                $this->db->where('files.file_type', $this->config->item('system_file_type_image'));
            }
            else if ($file_type == $this->config->item('system_file_type_video'))
            {
                $this->db->where('files.file_type', $this->config->item('system_file_type_video'));
            }

            $this->db->where('files.status !=', $this->config->item('system_status_delete'));
            $file_info = $this->db->get()->row_array();
            if (!$file_info)
            {
                if ($file_type == $this->config->item('system_file_type_image'))
                {
                    System_helper::invalid_try('Save_file(image)', $id, 'File Id Non-Exists');
                }
                else if ($file_type == $this->config->item('system_file_type_video'))
                {
                    System_helper::invalid_try('Save_file(video)', $id, 'File Id Non-Exists');
                }

                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
        }
        else
        {
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        if (!$this->check_validation_file_info())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        if ($whose == 'ARM')
        {
            $this->db->from($this->config->item('table_login_setup_classification_varieties') . ' v');
            $this->db->select('v.id variety_id, v.name');
        }
        else
        {
            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' v');
            $this->db->select('v.id variety_id, v.name');
        }
        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
        $this->db->select('type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->where('v.id', $item['variety_id']);
        $this->db->where('v.whose', $whose);
        $variety_info = $this->db->get()->row_array();
        if (!$variety_info)
        {
            if ($file_type == $this->config->item('system_file_type_image'))
            {
                System_helper::invalid_try(__FUNCTION__ . ('(image)'), $item['variety_id'], 'Variety Id Non-Exists');
            }
            else if ($file_type == $this->config->item('system_file_type_video'))
            {
                System_helper::invalid_try(__FUNCTION__ . ('(video)'), $item['variety_id'], 'Variety Id Non-Exists');
            }


            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

        $path = 'images/competitor_variety/' . $item['variety_id'];
        $uploaded_files = array();
        if ($file_type == $this->config->item('system_file_type_video'))
        {
            $uploaded_files = System_helper::upload_file($path, $this->config->item('system_file_type_video_ext'), $this->config->item('system_file_type_video_max_size'));
        }
        else if ($file_type == $this->config->item('system_file_type_image'))
        {
            $uploaded_files = System_helper::upload_file($path);
        }

        if (!($id > 0))
        {
            if (!$uploaded_files)
            {
                $ajax['status'] = false;
                if ($file_type == $this->config->item('system_file_type_image'))
                {
                    $ajax['system_message'] = 'The Picture field is required';
                }
                else if ($file_type == $this->config->item('system_file_type_video'))
                {
                    $ajax['system_message'] = 'The Video field is required';
                }
                $this->json_return($ajax);
            }
        }

        if (array_key_exists('file_name', $uploaded_files))
        {
            if ($uploaded_files['file_name']['status'])
            {
                $item['file_name'] = $uploaded_files['file_name']['info']['file_name'];
                $item['file_location'] = $path . '/' . $uploaded_files['file_name']['info']['file_name'];
            }
            else
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $uploaded_files['file_name']['message'];
                $this->json_return($ajax);
                die();
            }
        }
        $this->db->trans_start(); //DB Transaction Handle START
        if ($id > 0)
        {
            $item['user_updated'] = $user->user_id;
            $item['date_updated'] = $time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_bi_setup_competitor_variety_files'), $item, array("id = " . $id));
        }
        else
        {
            $item['user_created'] = $user->user_id;
            $item['date_created'] = $time;
            $item['revision_count'] = 1;
            Query_helper::add($this->config->item('table_bi_setup_competitor_variety_files'), $item);
        }
        $this->db->trans_complete(); //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            if ($file_type == $this->config->item('system_file_type_image'))
            {
                $this->system_list_file($item['variety_id'], $this->config->item('system_file_type_image'), $whose);
            }
            else if ($file_type == $this->config->item('system_file_type_video'))
            {
                $this->system_list_file($item['variety_id'], $this->config->item('system_file_type_video'), $whose);
            }
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    /*
    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data = array();
            $data['item'] = Array(
                'id' => 0,
                'competitor_id' => 0,
                'crop_type_id' => 0,
                'variety_name' => '',
                'hybrid' => '',
                'ordering' => 99,
                'status' => ''
            );

            $data['competitors'] = Query_helper::get_info($this->config->item('table_login_basic_setup_competitor'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('name ASC'));
            $data['hybrids'] = Query_helper::get_info($this->config->item('table_login_setup_classification_hybrid'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'));

            $data['title'] = "Add New Competitor Variety";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/add');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
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
            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' cv');
            $this->db->select('cv.*, cv.name variety_name');

            $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = cv.competitor_id');
            $this->db->select('competitor.name competitor_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = cv.crop_type_id');
            $this->db->select('crop_types.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'LEFT');
            $this->db->select('crops.id crop_id, crops.name crop_name');

            $this->db->where('cv.id', $item_id);
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['competitors'] = Query_helper::get_info($this->config->item('table_login_basic_setup_competitor'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('name ASC'));
            $data['hybrids'] = Query_helper::get_info($this->config->item('table_login_setup_classification_hybrid'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'));

            $data['title'] = "Edit Competitor Variety ( ID:" . $data['item']['id'] . " )";
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
        $item_id = $this->input->post('id');
        $item = $this->input->post('item');
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

            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' cv');
            $this->db->select('cv.*, cv.name variety_name');

            $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = cv.competitor_id');
            $this->db->select('competitor.name competitor_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = cv.crop_type_id');
            $this->db->select('crop_types.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'LEFT');
            $this->db->select('crops.id crop_id, crops.name crop_name');

            $this->db->where('cv.id', $item_id);
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
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

        $this->db->trans_start(); //DB Transaction Handle START
        if ($item_id > 0) // Revision Update if EDIT
        { //Update
            $item['date_updated'] = $time;
            $item['user_updated'] = $user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_bi_setup_competitor_variety'), $item, array("id =" . $item_id), FALSE);
        }
        else
        { //Insert
            $item['whose'] = 'Competitor';
            $item['status'] = $this->config->item('system_status_active');
            $item['revision_count'] = 1;
            $item['date_created'] = $time;
            $item['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_bi_setup_competitor_variety'), $item, FALSE);
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


    private function system_details($id)
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
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
            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' cv');
            $this->db->select('cv.*, cv.name variety_name');

            $this->db->join($this->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = cv.competitor_id');
            $this->db->select('competitor.name competitor_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_types', 'crop_types.id = cv.crop_type_id');
            $this->db->select('crop_types.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'LEFT');
            $this->db->select('crops.id crop_id, crops.name crop_name');

            $this->db->join($this->config->item('table_login_setup_classification_hybrid') . ' hybrid', 'hybrid.id = cv.hybrid');
            $this->db->select('hybrid.name hybrid');

            $this->db->where('cv.id', $item_id);
            $this->db->where('cv.whose', 'Competitor');
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids = array(
                $result['user_created'] => $result['user_created'],
                $result['user_updated'] => $result['user_updated']
            );
            $user_info = System_helper::get_users_info($user_ids);

            $items = array();
            // Competitor Basic Information
            $items[0] = array(
                'header' => 'Competitor\'s Variety Information',
                'div_id' => 'basic_info',
                'collapse' => 'in',
                'data' => array(
                    array(
                        'label_1' => $this->lang->line('LABEL_COMPETITOR_NAME'),
                        'value_1' => $result['competitor_name'],
                        'label_2' => $this->lang->line('LABEL_CROP_NAME'),
                        'value_2' => $result['crop_name']
                    ),
                    array(
                        'label_1' => $this->lang->line('LABEL_CROP_TYPE_NAME'),
                        'value_1' => $result['crop_type_name'],
                        'label_2' => $this->lang->line('LABEL_VARIETY_NAME'),
                        'value_2' => $result['variety_name']
                    ),
                    array(
                        'label_1' => $this->lang->line('LABEL_HYBRID'),
                        'value_1' => $result['hybrid'],
                        'label_2' => $this->lang->line('LABEL_STATUS'),
                        'value_2' => $result['status']
                    ),
                    array(
                        'label_1' => $this->lang->line('LABEL_CREATED_BY'),
                        'value_1' => $user_info[$result['user_created']]['name'],
                        'label_2' => $this->lang->line('LABEL_DATE_CREATED_TIME'),
                        'value_2' => System_helper::display_date_time($result['date_created'])
                    )
                )
            );

            if ($result['user_updated'] > 0)
            {
                $items[0]['data'][] = array(
                    'label_1' => $this->lang->line('LABEL_UPDATED_BY'),
                    'value_1' => $user_info[$result['user_updated']]['name'],
                    'label_2' => $this->lang->line('LABEL_DATE_UPDATED_TIME'),
                    'value_2' => System_helper::display_date_time($result['date_updated'])
                );
            }

            $data['items'] = $items;
            $data['title'] = "Competitor Variety Details ( ID:" . $item_id . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('item[competitor_id]', $this->lang->line('LABEL_COMPETITOR_NAME'), 'required');
        $this->form_validation->set_rules('item[crop_type_id]', $this->lang->line('LABEL_CROP_TYPE_NAME'), 'required');
        $this->form_validation->set_rules('item[name]', $this->lang->line('LABEL_VARIETY_NAME'), 'required');
        $this->form_validation->set_rules('item[hybrid]', $this->lang->line('LABEL_HYBRID'), 'required');
        $this->form_validation->set_rules('item[ordering]', $this->lang->line('LABEL_ORDER'), 'required');
        $this->form_validation->set_rules('item[status]', $this->lang->line('LABEL_STATUS'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }x

    private function system_details($id)
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }

            $this->db->from($this->config->item('table_bi_setup_competitor_variety') . ' v');
            $this->db->select('v.id,v.name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
            $this->db->select('type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $this->db->select('crop.name crop_name');

            $this->db->where('v.id', $item_id);
            $this->db->where('v.whose', 'Competitor');
            $data['item_head'] = $this->db->get()->row_array();
            if (!$data['item_head'])
            {
                System_helper::invalid_try('Details', $item_id, 'Id Non-Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['item_characteristics'] = Query_helper::get_info($this->config->item('table_bi_setup_competitor_variety_characteristics'), '*', array('variety_id =' . $item_id), 1);
            $item_files = Query_helper::get_info($this->config->item('table_bi_setup_competitor_variety_files'), '*', array('variety_id =' . $item_id, 'status ="' . $this->config->item('system_status_active') . '"'));
            $data['item_image'] = array();
            $data['item_video'] = array();
            foreach ($item_files as $item_file)
            {
                if ($item_file['file_type'] == $this->config->item('system_file_type_image'))
                {
                    $data['item_image'][$item_file['id']] = $item_file;
                }
                else if ($item_file['file_type'] == $this->config->item('system_file_type_video'))
                {
                    $data['item_video'][$item_file['id']] = $item_file;
                }

            }
            $data['title'] = "Details Competitor Variety Info Of (" . $data['item_head']['name'] . ')';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details/' . $item_id);
            $this->json_return($ajax);

        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
*/

    private function system_details($id, $whose = 'Competitor')
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }

            $page_url = 'details/';
            if ($whose == 'ARM')
            {
                $page_url = 'details_arm/';
            }

            $data['items'] = Bi_helper::get_variety_info($item_id, $whose, true, true, true, true);

            $data['title'] = "Details of {$whose} Variety";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $page_url . $item_id);
            $this->json_return($ajax);

        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function check_validation_characteristics()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[characteristics]', $this->lang->line('LABEL_CHARACTERISTICS'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }

    private function check_validation_file_info()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[status]', $this->lang->line('LABEL_STATUS'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
}
