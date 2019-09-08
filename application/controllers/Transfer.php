<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller
{
    public function index()
    {
        //$this->produce_data();
    }

    public function produce_data()
    {

        $this->db->from('arm_login_2018_19.login_setup_classification_crop_types');
        $this->db->select('*');
        $results2 = $this->db->get()->result_array();

        $types=array();
        foreach($results2 as $result2){
            $types[$result2['id']] = $result2;
        }

        $type_acres=array();

        $this->db->from('arm_login_2018_19.login_setup_classification_type_acres');
        $this->db->select('*');
        $this->db->where('revision', 1);
        $results = $this->db->get()->result_array();


        $final=array();
        foreach($results as $result){
            $type_acres[$result['type_id']][$result['upazilla_id']]=$result['quantity_acres'];

            $final[]=array(
                'type_id'=> $result['type_id'],
                'upazilla_id'=> $result['upazilla_id'],
                /*'quantity_acres' => $result['quantity_acres'],
                'quantity_kg_acre' => $types[$result['type_id']]['quantity_kg_acre'],*/
                'market_size_kg'=> $result['quantity_acres'] * ($types[$result['type_id']]['quantity_kg_acre']),
                'revision_count'=> 1
            );
        }
        $this->db->insert_batch($this->config->item('table_bi_market_size_main'), $final);

        echo '<pre> <h4>Total '.count($final).'</h4>';
        print_r($final);
        echo '</pre>';
    }

}
