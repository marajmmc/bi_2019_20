<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bi_helper
{
    public static $warning_color = '#ff6a6a';

    public static function get_basic_info($result)
    {
        $CI = & get_instance();
        //--------- System User Info ------------
        $user_ids = array();
        $user_ids[$result['user_created']] = $result['user_created'];
        if ($result['user_updated'] > 0)
        {
            $user_ids[$result['user_updated']] = $result['user_updated'];
        }
        if ($result['user_forwarded'] > 0)
        {
            $user_ids[$result['user_forwarded']] = $result['user_forwarded'];
        }
        if ($result['user_approved'] > 0)
        {
            $user_ids[$result['user_approved']] = $result['user_approved'];
        }
        $user_info = System_helper::get_users_info($user_ids);

        //---------------- Basic Info ----------------
        $data = array();
        $data[] = array
        (
            'label_1' => $CI->lang->line('LABEL_DIVISION_NAME'),
            'value_1' => $result['division_name'],
            'label_2' => $CI->lang->line('LABEL_ZONE_NAME'),
            'value_2' => $result['zone_name']
        );

        $data[] = array
        (
            'label_1' => $CI->lang->line('LABEL_TERRITORY_NAME'),
            'value_1' => $result['territory_name'],
            'label_2' => $CI->lang->line('LABEL_DISTRICT_NAME'),
            'value_2' => $result['district_name'],
        );
        $data[] = array
        (
            'label_1' => $CI->lang->line('LABEL_OUTLET_NAME'),
            'value_1' => $result['outlet_name'],
            'label_2' => 'Revision (Edit)',
            'value_2' => $result['revision_count']
        );
        $data[] = array
        (
            'label_1' => 'Created By',
            'value_1' => $user_info[$result['user_created']]['name'] . ' ( ' . $user_info[$result['user_created']]['employee_id'] . ' )',
            'label_2' => 'Created Time',
            'value_2' => System_helper::display_date_time($result['date_created'])
        );
        if ($result['user_updated'] > 0)
        {
            $inactive_update_by = 'Updated By';
            $inactive_update_time = 'Updated Time';
            if ($result['status'] == $CI->config->item('system_status_inactive'))
            {
                $inactive_update_by = 'In-Active By';
                $inactive_update_time = 'In-Active Time';
            }
            $data[] = array(
                'label_1' => $inactive_update_by,
                'value_1' => $user_info[$result['user_updated']]['name'] . ' ( ' . $user_info[$result['user_updated']]['employee_id'] . ' )',
                'label_2' => $inactive_update_time,
                'value_2' => System_helper::display_date_time($result['date_updated'])
            );
        }
        $data[] = array
        (
            'label_1' => $CI->lang->line('LABEL_STATUS_FORWARD'),
            'value_1' => $result['status_forward'],
            'label_2' => 'Revision (Forward)',
            'value_2' => $result['revision_count'],
        );
        if ($result['status_forward'] == $CI->config->item('system_status_forwarded'))
        {
            $data[] = array
            (
                'label_1' => 'Forwarded By',
                'value_1' => $user_info[$result['user_forwarded']]['name'] . ' ( ' . $user_info[$result['user_forwarded']]['employee_id'] . ' )',
                'label_2' => 'Forwarded Time',
                'value_2' => System_helper::display_date_time($result['date_forwarded'])
            );
            $data[] = array
            (
                'label_1' => 'Remarks (Forward)',
                'value_1' => $result['remarks_forward']
            );
        }
        if ($result['status_approve'] == $CI->config->item('system_status_approved'))
        {
            $label_approve = $CI->config->item('system_status_approved');
        }
        else if ($result['status_approve'] == $CI->config->item('system_status_rejected'))
        {
            $label_approve = 'Reject';
        }
        else
        {
            $label_approve = $CI->config->item('system_status_approved');
        }
        $data[] = array
        (
            'label_1' => $label_approve . ' Status',
            'value_1' => $result['status_approve'],

        );
        if ($result['status_approve'] != $CI->config->item('system_status_pending'))
        {
            $data[] = array
            (
                'label_1' => $label_approve . ' By',
                'value_1' => $user_info[$result['user_approved']]['name'] . ' ( ' . $user_info[$result['user_approved']]['employee_id'] . ' )',
                'label_2' => $label_approve . ' Time',
                'value_2' => System_helper::display_date_time($result['date_approved'])
            );
            $data[] = array
            (
                'label_1' => $label_approve . ' Remarks',
                'value_1' => $result['remarks_approve']
            );
        }
        if ($result['revision_count_rollback'] > 0)
        {
            if ($result['status_approve'] == $CI->config->item('system_status_pending'))
            {
                $data[] = array
                (
                    'label_1' => 'Revision (Rollback)',
                    'value_1' => $result['revision_count_rollback'],
                    'label_2' => 'Rollback Reason',
                    'value_2' => $result['remarks_approve']
                );
            }
            else
            {
                $data[] = array
                (
                    'label_1' => 'Revision (Rollback)',
                    'value_1' => $result['revision_count_rollback']
                );
            }

        }
        return $data;
    }

    public static function get_market_size_info($item_id, $controller_url, $collapse = 'in')
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
        $CI->db->from($CI->config->item('table_bi_market_size'));
        $CI->db->select('upazilla_id, type_id, market_size_kg');
        $CI->db->where('upazilla_id', $row_request['upazilla_id']);
        $results = $CI->db->get()->result_array();

        foreach ($results as $result)
        {
            $data['market_size_old'][$result['type_id']] = $result['market_size_kg'];
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

    public static function get_market_size_location($item_id, $collapse = 'in')
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
            $result['user_rollback'] => $result['user_rollback'],
            $result['user_rejected'] => $result['user_rejected'],
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
        if ($result['user_rollback'] > 0)
        {
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_ROLLBACK') . ' ' . $CI->lang->line('LABEL_REMARKS'),
                'value_1' => '<span style="color:' . Bi_helper::$warning_color . '">' . nl2br($result['remarks_rollback']) . '</span>'
            );
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_ROLLBACK_BY'),
                'value_1' => '<span style="color:' . Bi_helper::$warning_color . '">' . $user_info[$result['user_rollback']]['name'] . '</span>',
                'label_2' => $CI->lang->line('LABEL_DATE_ROLLBACK_TIME'),
                'value_2' => '<span style="color:' . Bi_helper::$warning_color . '">' . System_helper::display_date_time($result['date_rollback']) . '</span>'
            );
        }
        if ($result['user_rejected'] > 0)
        {
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_REJECTED') . ' ' . $CI->lang->line('LABEL_REMARKS'),
                'value_1' => '<span style="color:' . Bi_helper::$warning_color . '">' . nl2br($result['remarks_rejected']) . '</span>'
            );
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_REJECTED_BY'),
                'value_1' => '<span style="color:' . Bi_helper::$warning_color . '">' . $user_info[$result['user_rejected']]['name'] . '</span>',
                'label_2' => $CI->lang->line('LABEL_DATE_REJECTED_TIME'),
                'value_2' => '<span style="color:' . Bi_helper::$warning_color . '">' . System_helper::display_date_time($result['date_rejected']) . '</span>'
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

    public static function get_all_varieties($status = '', $variety_id = 0, $crop_type_id = 0, $crop_id = 0, $whose = 'ARM')
    {
        $CI =& get_instance();
        if ($whose == 'ARM') {
            $CI->db->from($CI->config->item('table_login_setup_classification_varieties') . ' v');
            $CI->db->select('v.id variety_id, v.name variety_name, v.whose, v.status');
        } else {
            $CI->db->from($CI->config->item('table_bi_setup_competitor_variety') . ' v');
            $CI->db->select('v.id variety_id, v.name variety_name, v.whose, v.status');

            $CI->db->join($CI->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = v.competitor_id');
            $CI->db->select('competitor.id competitor_id, competitor.name competitor_name');
        }

        $CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
        $CI->db->select('type.id crop_type_id, type.name crop_type_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $CI->db->select('crop.id crop_id, crop.name crop_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_hybrid') . ' hybrid', 'hybrid.id = v.hybrid');
        $CI->db->select('hybrid.name hybrid');

        if(is_array($variety_id) && (sizeof($variety_id) > 0)){
            $CI->db->where_in('v.id', $variety_id);
        }
        elseif ($variety_id > 0) {
            $CI->db->where('v.id', $variety_id);
        }

        if ($crop_type_id > 0) {
            $CI->db->where('type.id', $crop_type_id);
        }
        if ($crop_id > 0) {
            $CI->db->where('crop.id', $crop_id);
        }

        if ($status != '') {
            $CI->db->where('v.status', $status);
        } else {
            $CI->db->where('v.status', $CI->config->item('system_status_active'));
        }
        $CI->db->where('v.whose', $whose);

        if ($whose == 'Competitor') {
            $CI->db->order_by('competitor.id');
        }
        $CI->db->order_by('crop.id');
        $CI->db->order_by('type.id');

        if(is_array($variety_id) && (sizeof($variety_id) > 0)){
            return $CI->db->get()->result_array(); // Results
        }elseif ($variety_id > 0) {
            return $CI->db->get()->row_array(); // Result
        } else {
            return $CI->db->get()->result_array(); // Results
        }
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

    public static function cultivation_date_display($date_int)
    {
        $return_value = 0;
        if ($date_int && strtotime(date('d-M-Y', $date_int)))
        {
            $return_value = date('d-F', $date_int);
        }
        return $return_value;
    }

    public static function cultivation_date_sql($date_string)
    {
        $return_value = 0;
        if (strtotime($date_string))
        {
            $return_value = strtotime('1970-' . date('m-d', strtotime($date_string)));
        }
        return $return_value;
    }

    public static function get_major_competitor_variety_location($item_id, $collapse = 'in')
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_bi_major_competitor_variety_request') . ' item');
        $CI->db->select('item.*');

        $CI->db->join($CI->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = item.upazilla_id');
        $CI->db->select('upazilla.name upazilla_name');

        $CI->db->join($CI->config->item('table_login_setup_location_districts') . ' district', 'district.id = upazilla.district_id');
        $CI->db->select('district.name district_name');

        $CI->db->join($CI->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $CI->db->select('territory.name territory_name');

        $CI->db->join($CI->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $CI->db->select('zone.name zone_name');

        $CI->db->join($CI->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $CI->db->select('division.name division_name');

        $CI->db->where('item.id', $item_id);
        $CI->db->where('item.status', $CI->config->item('system_status_active'));
        $result = $CI->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $CI->json_return($ajax);
        }

        $user_ids = array(
            $result['user_created'] => $result['user_created'],
            $result['user_updated'] => $result['user_updated'],
            $result['user_forwarded'] => $result['user_forwarded'],
            $result['user_rollback'] => $result['user_rollback'],
            $result['user_rejected'] => $result['user_rejected'],
            $result['user_approved'] => $result['user_approved']
        );
        $user_info = System_helper::get_users_info($user_ids);

        $item = array(
            'header' => 'Major Competitor Variety Information',
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
        if ($result['user_rollback'] > 0)
        {
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_ROLLBACK') . ' ' . $CI->lang->line('LABEL_REMARKS'),
                'value_1' => '<span style="color:' . Bi_helper::$warning_color . '">' . nl2br($result['remarks_rollback']) . '</span>'
            );
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_ROLLBACK_BY'),
                'value_1' => '<span style="color:' . Bi_helper::$warning_color . '">' . $user_info[$result['user_rollback']]['name'] . '</span>',
                'label_2' => $CI->lang->line('LABEL_DATE_ROLLBACK_TIME'),
                'value_2' => '<span style="color:' . Bi_helper::$warning_color . '">' . System_helper::display_date_time($result['date_rollback']) . '</span>'
            );
        }
        if ($result['user_rejected'] > 0)
        {
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_REJECTED') . ' ' . $CI->lang->line('LABEL_REMARKS'),
                'value_1' => '<span style="color:' . Bi_helper::$warning_color . '">' . nl2br($result['remarks_rejected']) . '</span>'
            );
            $item['data'][] = array(
                'label_1' => $CI->lang->line('LABEL_REJECTED_BY'),
                'value_1' => '<span style="color:' . Bi_helper::$warning_color . '">' . $user_info[$result['user_rejected']]['name'] . '</span>',
                'label_2' => $CI->lang->line('LABEL_DATE_REJECTED_TIME'),
                'value_2' => '<span style="color:' . Bi_helper::$warning_color . '">' . System_helper::display_date_time($result['date_rejected']) . '</span>'
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

    public static function get_major_competitor_variety_info($item_id, $controller_url, $collapse = 'in')
    {
        $CI =& get_instance();
        $data = array();
        $data['collapse'] = $collapse;

        // From Request table (Current Requesting Major Competitor Variety for this Upazilla)
        $CI->db->from($CI->config->item('table_bi_major_competitor_variety_request') . ' item');
        $CI->db->select('*');
        $CI->db->join($CI->config->item('table_login_setup_location_upazillas') . ' upazilla', 'upazilla.id = item.upazilla_id');
        $CI->db->select('upazilla.name upazilla_name');
        $CI->db->where('item.id', $item_id);
        $row_request = $CI->db->get()->row_array();

        $data['major_competitor_varieties'] = json_decode($row_request['competitor_varieties'], TRUE);


        $CI->db->from($CI->config->item('table_bi_setup_competitor_variety') . ' variety');
        $CI->db->select('variety.id variety_id, variety.name variety_name, variety.crop_type_id');

        $CI->db->join($CI->config->item('table_login_basic_setup_competitor') . ' competitor', 'competitor.id = variety.competitor_id', 'INNER');
        $CI->db->select('competitor.name competitor_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = variety.crop_type_id', 'INNER');
        $CI->db->select('type.name crop_type_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $CI->db->select('crop.id crop_id, crop.name crop_name');

        $CI->db->where('competitor.status', $CI->config->item('system_status_active'));
        $CI->db->where('type.status', $CI->config->item('system_status_active'));
        $CI->db->where('crop.status', $CI->config->item('system_status_active'));

        $CI->db->order_by('crop.ordering', 'ASC');
        $CI->db->order_by('type.ordering', 'ASC');
        $competitor_variety_results = $CI->db->get()->result_array();
        foreach ($competitor_variety_results as $variety_result)
        {
            $data['competitor_varieties'][$variety_result['crop_id']][$variety_result['variety_id']] = array(
                'crop_name' => $variety_result['crop_name'],
                'crop_type_id' => $variety_result['crop_type_id'],
                'crop_type_name' => $variety_result['crop_type_name'],
                'variety_name' => $variety_result['variety_name'],
                'competitor_name' => $variety_result['competitor_name']
            );
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
        $data['crops'] = $results = $CI->db->get()->result_array();
        foreach ($results as $result)
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
        $data['table_title'] = 'Major Competitor Varieties ( ' . $row_request['upazilla_name'] . ' ' . $CI->lang->line('LABEL_UPAZILLA_NAME') . ' )';

        return $CI->load->view($controller_url . "/get_major_competitor_variety_details", $data, true);
    }

    /*------------------Convert Numeric Amount INTO In-Word------------------*/
    public static function get_string_amount_inword($number)
    {
        $number = (float)$number;
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_length)
        {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number)
            {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
            }
            else $str[] = null;
        }
        $Taka = implode('', array_reverse($str));

        $words[0] = 'Zero';
        $Paisa = ($decimal) ? ", " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paisa' : '';

        return ($Taka ? $Taka . 'Taka' : '') . $Paisa;
    }
}
