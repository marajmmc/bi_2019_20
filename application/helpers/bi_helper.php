<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bi_helper
{

    public static function get_market_size_info($item_id, $controller_url, $collapse='in')
    {
        $CI =& get_instance();
        $data = array();
        $data['collapse'] = $collapse;

        // From Request table (Current Requesting Market Size for this Upazilla)
        $CI->db->from($CI->config->item('table_bi_market_size_request') . ' ms');
        $CI->db->select('upazilla_id, market_size');
        $CI->db->join($CI->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = ms.upazilla_id');
        $CI->db->select('upazilla.name upazilla_name');
        $CI->db->where('ms.id', $item_id);
        $row_request = $CI->db->get()->row_array();

        $data['market_size_edit'] = json_decode($row_request['market_size'], TRUE);

        // From Main table (Previously Approved Market Size for this Upazilla)
        $CI->db->from($CI->config->item('table_bi_market_size_main'));
        $CI->db->select('upazilla_id, type_id, market_size_kg');
        $CI->db->where('upazilla_id', $row_request['upazilla_id']);
        $results = $CI->db->get()->result_array();

        foreach($results as $result){
            $data['market_size_old'][$result['type_id']]=$result['market_size_kg'];
        }

        // -------------------- For crop count -------------------------------
        $CI->db->from($CI->config->item('table_login_setup_classification_crop_types') . ' crop_types');
        $CI->db->select('crop_types.id crop_type_id, crop_types.name crop_type_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crops', 'crops.id = crop_types.crop_id', 'INNER');
        $CI->db->select('crops.id crop_id, crops.name crop_name');

        $CI->db->where('crop_types.status', $CI->config->item('system_status_active'));
        $CI->db->where('crops.status', $CI->config->item('system_status_active'));

        $CI->db->order_by('crops.id', 'ASC');
        $CI->db->order_by('crop_types.ordering', 'ASC');
        $data['crops'] = $CI->db->get()->result_array();
        foreach ($data['crops'] as $result)
        {
            if (isset($data['crop_type_count'][$result['crop_id']]))
            {
                $data['crop_type_count'][$result['crop_id']] += 1;
            }
            else
            {
                $data['crop_type_count'][$result['crop_id']] = 1;
            }
        }
        //-------------------------------------------------------------------
        $data['table_title'] = 'Market Sizes ( ' . $row_request['upazilla_name'] . ' ' . $CI->lang->line('LABEL_UPAZILLA_NAME') . ' )';

        return $CI->load->view($controller_url . "/get_market_size_details", $data, true);
    }

    public static function get_market_size_location($item_id, $collapse='in')
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_bi_market_size_request') . ' ms');
        $CI->db->select('ms.*');

        $CI->db->join($CI->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = ms.upazilla_id');
        $CI->db->select('upazilla.name upazilla_name');

        $CI->db->join($CI->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id');
        $CI->db->select('district.name district_name');

        $CI->db->join($CI->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $CI->db->select('territory.name territory_name');

        $CI->db->join($CI->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $CI->db->select('zone.name zone_name');

        $CI->db->join($CI->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $CI->db->select('division.name division_name');

        $CI->db->where('ms.id', $item_id);
        $CI->db->where('ms.status', $CI->config->item('system_status_active'));
        $result = $CI->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try('Details', $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $CI->json_return($ajax);
        }

        $user_ids = array(
            $result['user_created'] => $result['user_created'],
            $result['user_updated'] => $result['user_updated'],
            $result['user_forwarded'] => $result['user_forwarded'],
            $result['user_approved'] => $result['user_approved']
        );
        $user_info = System_helper::get_users_info($user_ids);

        $item = array(
            'header' => 'Market Size Information',
            'div_id' => 'basic_info',
            'collapse' => $collapse,
            'data' => array(
                array(
                    'label_1' => $CI->lang->line('LABEL_UPAZILLA_NAME'),
                    'value_1' => $result['upazilla_name']
                ),
                array(
                    'label_1' => $CI->lang->line('LABEL_DISTRICT_NAME'),
                    'value_1' => $result['district_name'],
                    'label_2' => $CI->lang->line('LABEL_TERRITORY_NAME'),
                    'value_2' => $result['territory_name']
                ),
                array(
                    'label_1' => $CI->lang->line('LABEL_ZONE_NAME'),
                    'value_1' => $result['zone_name'],
                    'label_2' => $CI->lang->line('LABEL_DIVISION_NAME'),
                    'value_2' => $result['division_name']
                ),
                array(
                    'label_1' => $CI->lang->line('LABEL_CREATED_BY'),
                    'value_1' => $user_info[$result['user_created']]['name'],
                    'label_2' => $CI->lang->line('LABEL_DATE_CREATED_TIME'),
                    'value_2' => System_helper::display_date_time($result['date_created'])
                )
            )
        );
        if ($result['user_updated'] > 0)
        {
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_UPDATED_BY'),
                'value_1' => $user_info[$result['user_updated']]['name'],
                'label_2' => $CI->lang->line('LABEL_DATE_UPDATED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_updated'])
            );
        }
        if ($result['user_forwarded'] > 0)
        {
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_FORWARDED_BY'),
                'value_1' => $user_info[$result['user_forwarded']]['name'],
                'label_2' => $CI->lang->line('LABEL_DATE_FORWARDED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_forwarded'])
            );
        }
        if ($result['user_approved'] > 0)
        {
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_APPROVED_BY'),
                'value_1' => $user_info[$result['user_approved']]['name'],
                'label_2' => $CI->lang->line('LABEL_DATE_APPROVED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_approved'])
            );
        }

        return $CI->load->view("info_basic", array('accordion' => $item), true);
    }

    public static function get_variety_info($item_id, $whose = 'Competitor', $show_basic = true, $show_characteristics = false, $show_images = false, $show_videos = false)
    {
        $CI =& get_instance();
        $items = array();
        $i = 0;

        if ($show_basic)
        {
            if ($whose == 'ARM')
            {
                $CI->db->from($CI->config->item('table_login_setup_classification_varieties') . ' v');
                $CI->db->select('v.id, v.name, v.status, v.date_created, v.user_created, v.date_updated, v.user_updated');
            }
            else
            {
                $CI->db->from($CI->config->item('table_bi_setup_competitor_variety') . ' v');
                $CI->db->select('v.id, v.name, v.status, v.date_created, v.user_created, v.date_updated, v.user_updated');

                $CI->db->join($CI->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = v.competitor_id');
                $CI->db->select('competitor.name competitor_name');
            }

            $CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
            $CI->db->select('type.name crop_type_name');

            $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $CI->db->select('crop.name crop_name');

            $CI->db->join($CI->config->item('table_login_setup_classification_hybrid') . ' hybrid', 'hybrid.id = v.hybrid');
            $CI->db->select('hybrid.name hybrid');

            $CI->db->where('v.id', $item_id);
            $CI->db->where('v.whose', $whose);
            $result = $CI->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try('Details', $item_id, 'Id Non-Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $CI->json_return($ajax);
            }
            else
            {
                $user_ids = array(
                    $result['user_created'] => $result['user_created'],
                    $result['user_updated'] => $result['user_updated']
                );
                $user_info = System_helper::get_users_info($user_ids);

                // Checks ARM / Competitor & assign Competitor name if `Competitor`
                $variety_label = array(
                    'label_1' => $CI->lang->line('LABEL_VARIETY_NAME'),
                    'value_1' => $result['name'] . ' ( ID: ' . $item_id . ' )'
                );
                if ($whose == 'Competitor')
                {
                    $variety_label['label_2'] = $CI->lang->line('LABEL_COMPETITOR_NAME');
                    $variety_label['value_2'] = $result['competitor_name'];
                }
                //-----------------------------------------------------------------

                $items[$i] = array(
                    'header' => '+ Basic Information',
                    'div_id' => 'info_' . $i,
                    'collapse' => 'in',
                    'data' => array(
                        $variety_label,
                        array(
                            'label_1' => $CI->lang->line('LABEL_CROP_NAME'),
                            'value_1' => $result['crop_name'],
                            'label_2' => $CI->lang->line('LABEL_CROP_TYPE_NAME'),
                            'value_2' => $result['crop_type_name']
                        ),
                        array(
                            'label_1' => $CI->lang->line('LABEL_HYBRID'),
                            'value_1' => $result['hybrid'],
                            'label_2' => $CI->lang->line('LABEL_STATUS'),
                            'value_2' => $result['status']
                        ),
                        array(
                            'label_1' => $CI->lang->line('LABEL_CREATED_BY'),
                            'value_1' => $user_info[$result['user_created']]['name'],
                            'label_2' => $CI->lang->line('LABEL_DATE_CREATED_TIME'),
                            'value_2' => System_helper::display_date_time($result['date_created'])
                        )
                    )
                );
                if ($result['user_updated'] > 0)
                {
                    $items[$i]['data'][] = array(
                        'label_1' => $CI->lang->line('LABEL_UPDATED_BY'),
                        'value_1' => $user_info[$result['user_updated']]['name'],
                        'label_2' => $CI->lang->line('LABEL_DATE_UPDATED_TIME'),
                        'value_2' => System_helper::display_date_time($result['date_updated'])
                    );
                }
                $i++;
            }
        }

        if ($show_characteristics)
        {
            $result = Query_helper::get_info($CI->config->item('table_bi_setup_competitor_variety_characteristics'), '*', array('variety_id =' . $item_id), 1);
            if ($result)
            {
                $user_ids = array(
                    $result['user_created'] => $result['user_created'],
                    $result['user_updated'] => $result['user_updated']
                );
                $user_info = System_helper::get_users_info($user_ids);
                $items[$i] = array(
                    'header' => '+ Characteristics Information',
                    'div_id' => 'info_' . $i,
                    'collapse' => 'in',
                    'data' => array(
                        array(
                            'label_1' => $CI->lang->line('LABEL_CHARACTERISTICS'),
                            'value_1' => '<span style="font-weight:normal">' . nl2br($result['characteristics']) . '</span>'
                        ),
                        array(
                            'label_1' => $CI->lang->line('LABEL_CULTIVATION_PERIOD_1') . ' &nbsp;&nbsp;' . $CI->lang->line('LABEL_FROM'),
                            'value_1' => date('d-F', $result['date_start1']), //System_helper::display_date($result['date_start1']),
                            'label_2' => $CI->lang->line('LABEL_TO'),
                            'value_2' => date('d-F', $result['date_end1'])
                        ),
                        array(
                            'label_1' => $CI->lang->line('LABEL_CULTIVATION_PERIOD_2') . ' &nbsp;&nbsp;' . $CI->lang->line('LABEL_FROM'),
                            'value_1' => date('d-F', $result['date_start2']),
                            'label_2' => $CI->lang->line('LABEL_TO'),
                            'value_2' => date('d-F', $result['date_end2'])
                        ),
                        array(
                            'label_1' => $CI->lang->line('LABEL_COMPARE_WITH_OTHER_VARIETY'),
                            'value_1' => '<span style="font-weight:normal">' . nl2br($result['comparison']) . '</span>'
                        ), array(
                            'label_1' => $CI->lang->line('LABEL_REMARKS'),
                            'value_1' => '<span style="font-weight:normal">' . nl2br($result['remarks']) . '</span>'
                        ),
                        array(
                            'label_1' => $CI->lang->line('LABEL_CREATED_BY'),
                            'value_1' => $user_info[$result['user_created']]['name'],
                            'label_2' => $CI->lang->line('LABEL_DATE_CREATED_TIME'),
                            'value_2' => System_helper::display_date_time($result['date_created'])
                        )
                    )
                );
                if ($result['user_updated'] > 0)
                {
                    $items[$i]['data'][] = array(
                        'label_1' => $CI->lang->line('LABEL_UPDATED_BY'),
                        'value_1' => $user_info[$result['user_updated']]['name'],
                        'label_2' => $CI->lang->line('LABEL_DATE_UPDATED_TIME'),
                        'value_2' => System_helper::display_date_time($result['date_updated'])
                    );
                }
            }
            else
            {
                $items[$i] = array(
                    'header' => '+ Characteristics Information',
                    'div_id' => 'info_' . $i,
                    'collapse' => 'in',
                    'data' => array(
                        array(
                            'label_1' => '<p style="font-weight:normal;text-align:center">No ' . $CI->lang->line('LABEL_CHARACTERISTICS') . ' Done Yet</p>'
                        ))
                );
            }
            $i++;
        }

        if ($show_images)
        {
            $items[$i] = array(
                'header' => '+ Image Information',
                'div_id' => 'info_' . $i,
                'collapse' => 'in'
            );

            $results = Query_helper::get_info($CI->config->item('table_bi_setup_competitor_variety_files'), '*', array('variety_id =' . $item_id, 'file_type ="' . $CI->config->item('system_file_type_image') . '"', 'status ="' . $CI->config->item('system_status_active') . '"'));
            if ($results)
            {

                foreach ($results as $result)
                {
                    $image = '<a href="' . $CI->config->item('system_base_url_picture') . $result['file_location'] . '" target="_blank" class="external blob">
                                <img class="img img-thumbnail img-responsive" style="width:300px; height:200px" src="' . $CI->config->item('system_base_url_picture') . $result['file_location'] . '" alt="' . $result['file_name'] . '">
                             </a>';

                    $items[$i]['data'][] = array(
                        'label_1' => $image,
                        'value_1' => '<span style="font-weight:normal"><b style="text-decoration:underline">Remarks:</b><br/>' . nl2br($result['remarks']) . '</span>'
                    );
                }
            }
            else
            {
                $items[$i]['data'][] = array(
                    'label_1' => '<p style="text-align:center">No ' . $CI->lang->line('LABEL_IMAGE') . ' has been Uploaded Yet</p>'
                );
            }

            $i++;
        }

        if ($show_videos)
        {
            $items[$i] = array(
                'header' => '+ Video Information',
                'div_id' => 'info_' . $i,
                'collapse' => 'in'
            );

            $results = Query_helper::get_info($CI->config->item('table_bi_setup_competitor_variety_files'), '*', array('variety_id =' . $item_id, 'file_type ="' . $CI->config->item('system_file_type_video') . '"', 'status ="' . $CI->config->item('system_status_active') . '"'));
            if ($results)
            {

                foreach ($results as $result)
                {
                    $video = '<video class="img img-thumbnail img-responsive" style="width:350px; max-height:350px" controls>
                                 <source src="' . $CI->config->item('system_base_url_picture') . $result['file_location'] . '"/>
                              </video>';

                    $items[$i]['data'][] = array(
                        'label_1' => $video,
                        'value_1' => '<span style="font-weight:normal"><b style="text-decoration:underline">Remarks:</b><br/>' . nl2br($result['remarks']) . '</span>'
                    );
                }
            }
            else
            {
                $items[$i]['data'][] = array(
                    'label_1' => '<p style="text-align:center">No ' . $CI->lang->line('LABEL_VIDEO') . ' has been Uploaded Yet</p>'
                );
            }

            $i++;
        }

        return $items;
    }
}
