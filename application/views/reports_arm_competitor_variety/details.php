<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
?>
<style>
    .item_panel
    {
        padding-top: 10px;
    }
</style>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_variety_info" href="#">+ Variety Info</a></label>
            </h4>
        </div>
        <div id="collapse_variety_info" class="panel-collapse collapse in item_panel">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item_head['crop_name'];?></label>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item_head['crop_type_name'];?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item_head['name'];?></label>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_characteristics_info" href="#">+ Characteristics Info</a></label>
            </h4>
        </div>
        <div id="collapse_characteristics_info" class="panel-collapse collapse in item_panel">

            <?php if($item_characteristics){?>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CHARACTERISTICS');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <?php echo nl2br($item_characteristics['characteristics']);?>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Cultivation Period 1</label>
                    </div>
                    <div class="col-xs-2">
                        <b>From: </b><?php if($item_characteristics['date_start1']!=0){echo date('d-F',$item_characteristics['date_start1']);}?>
                    </div>
                    <div class="col-xs-2">
                        <b>To: </b><?php if($item_characteristics['date_end1']!=0){echo date('d-F',$item_characteristics['date_end1']);}?>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Cultivation Period 2</label>
                    </div>
                    <div class="col-xs-2">
                        <b>From: </b><?php if($item_characteristics['date_start2']!=0){echo date('d-F',$item_characteristics['date_start2']);}?>
                    </div>
                    <div class="col-xs-2">
                        <b>To: </b><?php if($item_characteristics['date_end2']!=0){echo date('d-F',$item_characteristics['date_end2']);}?>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Compare With Other Variety</label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <?php echo nl2br($item_characteristics['comparison']);?>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <?php echo nl2br($item_characteristics['remarks']);?>
                    </div>
                </div>
            <?php } else{?>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Characteristics setup not done yet.</label>
                    </div>
                    <div class="col-sm-4 col-xs-8">

                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#images_info" href="#">+ Images Info</a></label>
            </h4>
        </div>

        <div id="images_info" class="panel-collapse collapse in item_panel">
            <div class="row show-grid">
            <?php if($item_image){?>
                <?php foreach($item_image as $image){?>
                    <div class="col-sm-3 col-xs-4">
                        <img class="img img-thumbnail img-responsive" style="height: 150px" src="<?php echo $CI->config->item('system_base_url_picture').$image['file_location']; ?>" alt="<?php echo $image['file_name']; ?>">
                    </div>
                <?php } ?>
            <?php } else{?>

                    <div class="col-xs-4">
                        <label class="control-label pull-right">Image setup not done yet.</label>
                    </div>
                    <div class="col-sm-4 col-xs-8">

                    </div>
            <?php } ?>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="panel panel-default">

        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#videos_info" href="#">+ Videos Info</a></label>
            </h4>
        </div>

        <div id="videos_info" class="panel-collapse collapse in item_panel">

            <div class="row show-grid">
                <?php if($item_video){?>
                    <?php foreach($item_video as $video){?>
                        <div class="col-sm-3 col-xs-4">
                            <video class="img img-thumbnail img-responsive" style="height: 150px" controls>
                                <source src="<?php if(isset($video['file_location'])){ echo $CI->config->item('system_base_url_picture').$video['file_location'];}?>">
                            </video>
                        </div>
                    <?php } ?>
                <?php } else{?>
                        <div class="col-xs-4">
                            <label class="control-label pull-right">Video setup not done yet.</label>
                        </div>
                        <div class="col-sm-4 col-xs-8">

                        </div>
                <?php } ?>
            </div>
        </div>
    </div>

</div>

<div class="clearfix"></div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

    });
</script>
