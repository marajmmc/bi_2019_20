<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list')
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php
    $CI->load->view('info_basic', array('accordion' => array('collapse' => 'in', 'data' => $item)));

    $results = Bi_helper::get_all_varieties('', $variety_focused);
    $crops = array();
    foreach ($results as $result)
    {
        $crops[$result['crop_id']]['name'] = $result['crop_name'];
        $crops[$result['crop_id']]['types'][$result['crop_type_id']]['name'] = $result['crop_type_name'];
        $crops[$result['crop_id']]['types'][$result['crop_type_id']]['varieties'][$result['variety_id']] = $result['variety_name'];
    }
    ?>
    <!--------------------- Current Record --------------------->
    <table class="table table-bordered">
        <tr>
            <th class="text-center">Crop</th>
            <th class="text-center">Crop Type</th>
            <th class="text-center">Variety</th>
        </tr>
        <?php
        $current_crop_id = -1;
        foreach ($crops as $crop_id => $crop)
        {
            foreach ($crop['types'] as $type)
            {
                ?>
                <tr>
                    <?php
                    if ($current_crop_id != $crop_id) {
                        $current_crop_id = $crop_id; ?>
                        <td rowspan="<?php echo sizeof($crop['types']); ?>"><?php echo $crop['name']; ?></td>
                    <?php } ?>
                    <td><?php echo $type['name']; ?></td>
                    <td style="box-sizing:border-box">
                        <ol>
                            <?php foreach ($type['varieties'] as $variety_id => $variety) { ?>
                                <li><b><?php echo $variety; ?></b></li>
                            <?php } ?>
                        </ol>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>

    <?php
    //--------------------- History ---------------------
    $this->db->from($this->config->item('table_bi_setup_variety_focused'));
    $this->db->select('variety_focused, variety_focused_count, revision, date_created, user_created, date_updated, user_updated');
    $this->db->where('status', $this->config->item('system_status_active'));
    $this->db->where('revision >', 1);
    $this->db->where('outlet_id', $outlet_id);
    $this->db->order_by('revision');
    $results_history = $this->db->get()->result_array();
    if($results_history)
    {
        foreach($results_history as $result_history)
        {
            $results = Bi_helper::get_all_varieties('', json_decode($result_history['variety_focused'], TRUE));
            $crops = array();
            foreach ($results as $result)
            {
                $crops[$result['crop_id']]['name'] = $result['crop_name'];
                $crops[$result['crop_id']]['types'][$result['crop_type_id']]['name'] = $result['crop_type_name'];
                $crops[$result['crop_id']]['types'][$result['crop_type_id']]['varieties'][$result['variety_id']] = $result['variety_name'];
            }
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#accordion<?php echo $result_history['revision'];?>" href="#">History <?php echo ($result_history['revision'] - 1); ?></a></label>
                    </h4>
                </div>
                <div id="accordion<?php echo $result_history['revision']; ?>" class="panel-collapse collapse">
                    <table class="table table-bordered">
                        <tr>
                            <th class="text-center">Crop</th>
                            <th class="text-center">Crop Type</th>
                            <th class="text-center">Variety</th>
                        </tr>
                        <?php
                        $current_crop_id = -1;
                        foreach ($crops as $crop_id => $crop)
                        {
                            foreach ($crop['types'] as $type)
                            {
                                ?>
                                <tr>
                                    <?php
                                    if ($current_crop_id != $crop_id) {
                                        $current_crop_id = $crop_id; ?>
                                        <td rowspan="<?php echo sizeof($crop['types']); ?>"><?php echo $crop['name']; ?></td>
                                    <?php } ?>
                                    <td><?php echo $type['name']; ?></td>
                                    <td style="box-sizing:border-box">
                                        <ol>
                                            <?php foreach ($type['varieties'] as $variety_id => $variety) { ?>
                                                <li><b><?php echo $variety; ?></b></li>
                                            <?php } ?>
                                        </ol>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </table>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>
