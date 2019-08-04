<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$action_buttons = array();
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'label' => 'Competitor Variety List',
        'href' => site_url($CI->controller_url)
    );
}
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => 'Characteristics Setup',
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/add_edit_characteristics_arm')
    );
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => 'Image Setup',
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/list_image_arm')
    );
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => 'Video Setup',
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/list_video_arm')
    );
}
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line('ACTION_DETAILS'),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/details_arm')
    );
}
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'class' => 'button_action_download',
        'data-title' => "Print",
        'data-print' => true
    );
}
if (isset($CI->permissions['action5']) && ($CI->permissions['action5'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DOWNLOAD"),
        'class' => 'button_action_download',
        'data-title' => "Download"
    );
}
if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
{
    $action_buttons[] = array
    (
        'label' => 'Preference',
        'href' => site_url($CI->controller_url . '/index/set_preference_arm')
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list_arm')
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
    if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
    {
        $CI->load->view('preference', array('system_preference_items' => $system_preference_items));
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers
        system_preset({controller: '<?php echo $CI->router->class; ?>'});

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_arm/'); ?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key => $value)
                {
                    if($key=='id')
                    {
                    ?> { name: '<?php echo $key; ?>', type: 'integer' },
                <?php
                    }
                    else
                    {
                    ?> { name: '<?php echo $key; ?>', type: 'string' },
                <?php
                    }
                }
                ?>
            ],
            id: 'id',
            type: 'POST',
            url: url
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize: 50,
                pagesizeoptions: ['20', '50', '100', '200', '300', '500'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection: true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', pinned: true, dataField: 'id', width: '50', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', pinned: true, dataField: 'name', width: '200', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', width: '150', filtertype: 'list', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name', width: '150', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CHARACTERISTICS'); ?>', dataField: 'characteristics', width: '130', filtertype: 'list', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['characteristics']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_IMAGES'); ?>', dataField: 'number_of_images', width: '100', filtertype: 'none', cellsalign: 'right', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['number_of_images']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_VIDEOS'); ?>', dataField: 'number_of_videos', width: '100', filtertype: 'none', cellsalign: 'right', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['number_of_videos']?0:1;?>}
                ]
            });
    });
</script>
