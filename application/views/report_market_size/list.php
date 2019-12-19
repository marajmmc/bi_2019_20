<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons = array();
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

if (isset($CI->permissions['action5']) && ($CI->permissions['action5'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => 'Export CSV',
        'id' => 'csvExport',
        'data-title' => "Download"
    );
}

if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
{
    $action_buttons[] = array
    (
        'label' => 'Preference',
        'href' => site_url($CI->controller_url . '/index/set_preference')
    );
}
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if($key=='crop_name' || $key=='crop_type_name' || $key=='date_start' || $key=='date_end')
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'string' },
                        <?php
                    }
                    else
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'number' },
                        <?php
                    }
            }
            ?>
            ],
            url: url,
            type: 'POST',
            data: JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function (row, column, value, defaultHtml, columnSettings, record) {
            var element = $(defaultHtml);
            // console.log(defaultHtml);
            if (record.crop_type_name == "Total Crop") {
                if (column != 'crop_name') {
                    element.css({ 'background-color': system_report_color_crop, 'margin': '0px', 'width': '100%', 'height': '100%', padding: '5px', 'line-height': '25px'});
                }
            }
            else if (record.crop_name == "Grand Total") {
                element.css({ 'background-color': system_report_color_grand, 'margin': '0px', 'width': '100%', 'height': '100%', padding: '5px', 'line-height': '25px'});
            }
            else {
                element.css({'margin': '0px', 'width': '100%', 'height': '100%', padding: '5px', 'line-height': '25px'});
            }
            if ((column != 'crop_name') && (column !== 'crop_type_name') && (column !== 'date_start') && (column !== 'date_end') && (column !== 'variety_arm') && (column !== 'variety_competitor')) {
                if (value == 0) {
                    element.html('');
                }
                else {
                    element.html(number_format(value, 2));
                }
            }
            /*if(column=='details_button' && record.crop_type_name!="Total Crop" && record.crop_name!="Grand Total")
             {
             element.html('<div><button class="btn btn-primary pop_up" data-item-no="'+row+'">Details</button></div>');
             }*/
            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var aggregates = function (total, column, element, record) {
            if (record.outlet == "Grand Total") {
                //console.log(element);
                return record[element];

            }
            return total;
            //return grand_starting_stock;
        };
        var aggregatesrenderer = function (aggregates) {
            var text = aggregates['total'];
            if (text != "Grand Total") {
                if ((aggregates['total'] == '0.00') || (aggregates['total'] == '')) {
                    text = '';
                }
                else {
                    text = number_format(aggregates['total'], 2);
                }
            }
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:' + system_report_color_grand + ';">' + text + '</div>';

        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                source: dataAdapter,
                width: '100%',
                height: '350px',
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                columnsreorder: true,
                enablebrowserselection: true,
                selectionmode: 'singlerow',
                showaggregates: true,
                showstatusbar: true,
                altrows: true,
                //rowsheight: 180,
                rowsheight: 40,
                columnsheight: 40,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', pinned: true, width: '200', filtertype: 'list', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['crop_name']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer},

                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name', width: '100', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer},

                    { text: '<?php echo $CI->lang->line('LABEL_MARKET_SIZE_KG_TOTAL'); ?>', dataField: 'market_size_kg_total', width: '120', cellsalign: 'right', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['market_size_kg_total']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer},

                    { text: '<?php echo $CI->lang->line('LABEL_MARKET_SIZE_KG_ARM'); ?>', dataField: 'market_size_kg_arm', width: '120', cellsalign: 'right', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['market_size_kg_arm']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer},

                    { text: '<?php echo $CI->lang->line('LABEL_MARKET_SIZE_KG_OTHER'); ?>', dataField: 'market_size_kg_other', width: '120', cellsalign: 'right', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['market_size_kg_other']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer},

                    { columngroup: 'cultivation_period', text: '<?php echo $CI->lang->line('LABEL_DATE_START'); ?>', dataField: 'date_start', width: '120', cellsalign: 'right', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['date_start']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer},

                    { columngroup: 'cultivation_period', text: '<?php echo $CI->lang->line('LABEL_DATE_END'); ?>', dataField: 'date_end', width: '120', cellsalign: 'right', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['date_end']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer},

                    { text: '<?php echo $CI->lang->line('LABEL_TYPE_OF_PREFERENCE'); ?>', dataField: 'type_of_preference', width: '200', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['type_of_preference']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer},

                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_COMPETITOR'); ?>', dataField: 'variety_competitor', width: '200', cellsalign: 'right', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['variety_competitor']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer},

                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_ARM'); ?>', dataField: 'variety_arm', width: '200', cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['variety_arm']?0:1;?>, aggregates: [
                        { 'total': aggregates}], aggregatesrenderer: aggregatesrenderer}

                    //{ text: 'Variety Details', dataField: 'details_button',width: '85',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ],
                columngroups: [
                    { text: 'Cultivation Period', align: 'center', name: 'cultivation_period' }
                ]
            });


        $("#csvExport").click(function () {
            //$("#dataTable").jqxDataTable('exportData', 'csv');
            $("#system_jqx_container").jqxGrid('exportdata', 'csv', 'jqxGrid');
        });

    });
</script>