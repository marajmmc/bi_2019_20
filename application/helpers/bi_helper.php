<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bi_helper
{
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
