<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'label' => 'Pending List',
        'href' => site_url($CI->controller_url . '/index/list')
    );
}
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DETAILS"),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/details')
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
        'href' => site_url($CI->controller_url . '/index/set_preference_all')
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list_all')

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

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_all'); ?>";
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
                    ?>
                        { name: '<?php echo $key; ?>', type: 'integer' },
                    <?php
                    }
                    else
                    {
                    ?>
                        { name: '<?php echo $key; ?>', type: 'string' },
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
                pagesizeoptions: ['50', '100', '200', '300', '500', '1000', '5000'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection: true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', pinned: true, dataField: 'id', width: '50', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?>', pinned: true, dataField: 'upazilla_name', width: '150', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['upazilla_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['district_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name', width: '120', filtertype: 'list', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['territory_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name', width: '120', filtertype: 'list', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['zone_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name', width: '120', filtertype: 'list', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['division_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_EDIT'); ?>', dataField: 'number_of_edit', width: '60', filtertype: 'none', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['number_of_edit']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_FORWARDED_BY'); ?>', dataField: 'forwarded_by', width: '180', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['forwarded_by']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS_APPROVE'); ?>', dataField: 'status_approved', width: '120', filtertype: 'list', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['status_approved']?0:1;?>}
                ]
            });
    });
</script>