<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends CI_Controller
{
    public function index()
    {
        //$this->produce_data();
    }

    /*public function produce_data()
    {
        $this->db->from('arm_login_2018_19.login_setup_location_upazillas');
        $this->db->select('*');
        $this->db->where('status', 'Active');
        $results_upazilla = $this->db->get()->result_array();
        $upazillas = array();
        foreach ($results_upazilla as $result_upazilla)
        {
            $upazillas[$result_upazilla['id']] = $result_upazilla;
        }
        $upazillas_remaining = $upazillas;




        $this->db->from('arm_login_2018_19.login_setup_classification_crop_types');
        $this->db->select('*');
        $results_crop_type = $this->db->get()->result_array();
        $types = array();
        foreach ($results_crop_type as $result_crop_type)
        {
            $types[$result_crop_type['id']] = $result_crop_type;
            $types[$result_crop_type['id']]['crop_type_id'] = $result_crop_type['id'];
        }



        $this->db->from('arm_login_2018_19.login_setup_classification_type_acres a');
        $this->db->select('*');
        $this->db->join('arm_login_2018_19.login_setup_classification_crop_types t', 't.id = a.type_id');
        $this->db->select('t.crop_id');
        //$this->db->where('revision', 1);
        $type_acres = $this->db->get()->result_array();


        $final = array();
        foreach ($type_acres as $type_acre)
        {
            $final[] = array(
                'type_id' => $type_acre['type_id'],
                'upazilla_id' => $type_acre['upazilla_id'],
                'market_size_kg' => $type_acre['quantity_acres'] * ($types[$type_acre['type_id']]['quantity_kg_acre']),
                'revision_count' => 1
            );
            unset($upazillas_remaining[$type_acre['upazilla_id']]);
        }

        foreach($upazillas_remaining as $upazilla_remaining){
            $final[] = array(
                'type_id' => 0,
                'upazilla_id' => $upazilla_remaining['id'],
                'market_size_kg' => 0,
                'revision_count' => 1
            );
        }

        // Main Query
        $this->db->insert_batch($this->config->item('table_bi_market_size'), $final);

        echo '<pre>';
        echo '<h4>Total types ' . count($types) . '</h4>';
        echo '<h4>Total final ' . count($final) . '</h4>';

        print_r($upazillas_remaining);

        echo '</pre>';
    }*/

}
