<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_SAVE"),
        'id' => 'button_action_save',
        'data-form' => '#save_form'
    );
}
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));

$results = Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'), array('id value', 'name text', 'competitor_id'), array('whose ="Competitor"', 'status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('name'));
foreach ($results as $result)
{
    $competitor_varieties[$result['competitor_id']][] = $result;
}

?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">

    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

    <div class="row widget">

        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['division_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                <?php
                }
                else
                {
                    if($item['id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['division_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="division_id" name="division_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($divisions as $division)
                            {?>
                                <option value="<?php echo $division['value']?>"><?php echo $division['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                    ?>
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['zone_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
                <?php
                }
                else
                {
                    if($item['id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['zone_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="zone_id" name="zone_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($zones as $zone)
                            {?>
                                <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$item['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['territory_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['territory_name'];?></label>
                <?php
                }
                else
                {
                    if($item['id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['territory_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="territory_id" name="territory_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($territories as $territory)
                            {?>
                                <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$item['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['district_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
                <?php
                }
                else
                {
                    if($item['id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['district_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="district_id" name="district_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($districts as $district)
                            {?>
                                <option value="<?php echo $district['value']?>" <?php if($district['value']==$item['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($upazillas)>0)){echo 'display:none';} ?>" class="row show-grid" id="upazilla_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['upazilla_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['upazilla_name'];?></label>
                <?php
                }
                else
                {
                    if($item['id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['upazilla_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="upazilla_id" name="item[upazilla_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($upazillas[$item['district_id']] as $upazilla)
                            {?>
                                <option value="<?php echo $upazilla['value']?>" <?php if($upazilla['value']==$item['upazilla_id']){ echo "selected";}?>><?php echo $upazilla['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-sm-12" id="items_container">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="6" style="text-align:center"><?php echo $title; ?></th>
                    </tr>
                    <tr class="table_head">
                        <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th colspan="2" style="text-align:center">Before <?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
                        <th colspan="2" style="text-align:center"><?php echo $this->lang->line('LABEL_CULTIVATION_PERIOD'); ?></th>
                    </tr>
                    <tr>
                        <th style="text-align:center">Date Start</th>
                        <th style="text-align:center">Date End</th>
                        <th style="text-align:center">Date Start</th>
                        <th style="text-align:center">Date End</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($crops)
                    {
                        $cultivation_period=json_decode($item['cultivation_period'],TRUE);
                        $init_crop_id = -1;
                        $rowspan=0;
                        foreach ($crops as $crop)
                        {
                            $date_start_old="";
                            $date_end_old="";
                            if(isset($cultivation_period_old[$crop['crop_type_id']]))
                            {
                                $date_start_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_start']);
                                $date_end_old=Bi_helper::cultivation_date_display($cultivation_period_old[$crop['crop_type_id']]['date_end']);
                            }

                            $date_start=Bi_helper::cultivation_date_display(0);
                            $date_end=Bi_helper::cultivation_date_display(0);
                            if(isset($cultivation_period[$crop['crop_type_id']]))
                            {
                                $date=explode('~',$cultivation_period[$crop['crop_type_id']]);
                                $date_start=Bi_helper::cultivation_date_display($date[0]);
                                $date_end=Bi_helper::cultivation_date_display($date[1]);
                            }

                            $rowspan = $crop_type_count[$crop['crop_id']];

                            ?>
                            <tr>
                                <?php
                                $rowspan = 1;
                                if ($init_crop_id != $crop['crop_id'])
                                {
                                    $rowspan = $crop_type_count[$crop['crop_id']];
                                    ?>
                                    <td rowspan="<?php echo $rowspan; ?>"><?php echo $crop['crop_name']; ?></td>
                                    <?php
                                    $init_crop_id = $crop['crop_id'];
                                }
                                ?>
                                <td><?php echo $crop['crop_type_name']?></td>
                                <td>
                                    <?php
                                    if($date_start_old)
                                    {
                                        echo $date_start_old;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($date_end_old)
                                    {
                                        echo $date_end_old;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type="text" name="items[<?php echo $crop['crop_type_id']; ?>][date_start]" class="form-control date_large" value="<?php echo $date_start?$date_start:''; ?>" readonly="true" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type="text" name="items[<?php echo $crop['crop_type_id']; ?>][date_end]" class="form-control date_large" value="<?php echo $date_end?$date_end:''; ?>" readonly="true" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function ($)
    {
        system_off_events(); // Triggers
        $(".date_large").datepicker({dateFormat : "dd-M",changeMonth: true,changeYear: true,yearRange: "c-2:c+2"});

        /*location*/
        $(document).off('change', '#division_id');
        $(document).on('change','#division_id',function()
        {
            $('#zone_id').val('');
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            var division_id=$('#division_id').val();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#upazilla_id_container').hide();
            $("#items_container").html('');
            if(division_id>0)
            {
                if(system_zones[division_id]!==undefined)
                {
                    $('#zone_id_container').show();
                    $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
                }
            }

        });
        $(document).off('change', '#zone_id');
        $(document).on('change','#zone_id',function()
        {
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            var zone_id=$('#zone_id').val();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#upazilla_id_container').hide();
            $("#items_container").html('');
            if(zone_id>0)
            {
                if(system_territories[zone_id]!==undefined)
                {
                    $('#territory_id_container').show();
                    $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).off('change', '#territory_id');
        $(document).on('change','#territory_id',function()
        {
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            $('#upazilla_id_container').hide();
            $('#district_id_container').hide();
            $("#items_container").html('');
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                if(system_districts[territory_id]!==undefined)
                {
                    $('#district_id_container').show();
                    $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
                }

            }
        });
        $(document).off('change', '#district_id');
        $(document).on('change','#district_id',function()
        {
            $('#upazilla_id').val('');
            $("#items_container").html('');
            var district_id=$('#district_id').val();
            $('#upazilla_id_container').hide();
            //console.log(system_upazillas);
            if(district_id>0)
            {
                if(system_upazillas[district_id]!==undefined)
                {
                    $('#upazilla_id_container').show();
                    $('#upazilla_id').html(get_dropdown_with_select(system_upazillas[district_id]));
                }
            }
        });
        $(document).off('change', '#upazilla_id');
        $(document).on('change','#upazilla_id',function()
        {
            $("#items_container").html('');
            var upazilla_id=$('#upazilla_id').val();
            if(upazilla_id>0)
            {
                $.ajax({
                    url: '<?php echo site_url($CI->controller_url.'/index/get_cultivation_period_info'); ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{upazilla_id:upazilla_id},
                    success: function (data, status)
                    {
                        //$("#items_container").html('');
                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");
                    }
                });
            }
        });

    });

    function get_market_size(upazilla_id, upazilla_name, market_size_edit='')
    {
        $.ajax({
            url: "<?php echo site_url($CI->controller_url.'/index/get_market_size/') ?>",
            type: 'POST',
            datatype: "JSON",
            data: {
                html_container_id: '#item_container .display',
                upazilla_id: upazilla_id,
                upazilla_name: upazilla_name,
                market_size_edit: market_size_edit
            },
            success: function (data, status) {
                if (data.status) {
                    $('#market_size_container').show();
                }
            },
            error: function (xhr, desc, err) {
                console.log("error");
            }
        });
    }
</script>