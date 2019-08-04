<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();

if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['action6'])&&($CI->permissions['action6']==1))
    {
        ?>
        <div class="col-xs-3"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="crop_info" checked><span class="">Crop Info</span></label></div></div>
        <div class="col-xs-3"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="characteristics" checked><span class="">Characteristics</span></label></div></div>
        <div class="col-xs-3"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="cultivation_period" checked><span class="">Cultivation Period</span></label></div></div>
        <div class="col-xs-3"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="picture" checked><span class="">Picture</span></label></div></div>
        <div class="col-xs-3"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="comparison" checked><span class="">Compare With Other Variety</span></label></div></div>
        <div class="col-xs-3"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="remarks" checked><span class="">Remarks</span></label></div></div>
        <div class="col-xs-3"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="number_of_images" checked><span class="">Number Of Images</span></label></div></div>
        <div class="col-xs-3"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="number_of_videos" checked><span class="">Number Of Videos</span></label></div></div>
        <div class="col-xs-3"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="details_button" checked><span class="">Details</span></label></div></div>
    <?php
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        $(document).off("click", ".pop_up");
        $(document).on("click", ".pop_up", function(event)
        {
            var left=((($(window).width()-550)/2)+$(window).scrollLeft());
            var top=((($(window).height()-550)/2)+$(window).scrollTop());
            $("#popup_window").jqxWindow({width: 1200,height:550,position:{x:left,y:top}}); //to change position always
            //$("#popup_window").jqxWindow({position:{x:left,y:top}});
            var row=$(this).attr('data-item-no');
            var id=$("#system_jqx_container").jqxGrid('getrowdata',row).id;
            $.ajax(
                {
                    url: "<?php echo site_url($CI->controller_url.'/index/details') ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data:
                    {
                        html_container_id:'#popup_content',
                        id:id
                    },
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");
                    }
                });
            $("#popup_window").jqxWindow('open');
        });

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'crop_info', type: 'string' },
                { name: 'characteristics', type: 'string' },
                { name: 'cultivation_period', type: 'string' },
                { name: 'picture', type: 'string' },
                { name: 'comparison', type: 'string' },
                { name: 'remarks', type: 'string' },
                { name: 'number_of_images', type: 'string' },
                { name: 'number_of_videos', type: 'string' },
                { name: 'details_button', type: 'string' }
            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            if(column=='details_button')
            {
                element.html('<div><button class="btn btn-primary pop_up" data-item-no="'+row+'">Details</button></div>');
            }
            if(column=='picture')
            {
                element.html('<div><img style="height: 126px" class="img img-responsive" src="'+value+'"></div>');
            }
            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                rowsheight: 133,
                columns: [
                    { text: 'Crop Info', dataField: 'crop_info',width: '150',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'characteristics', dataField: 'characteristics',width: '250',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Cultivation Period', dataField: 'cultivation_period',width: '250',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Picture', dataField: 'picture',width: '250',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Compare With Other Variety', dataField: 'comparison',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Remarks', dataField: 'remarks',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Number Of Images', dataField: 'number_of_images',width: '30',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Number Of Videos', dataField: 'number_of_videos',width: '30',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Details', dataField: 'details_button',width: '100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ]
            });
    });
</script>