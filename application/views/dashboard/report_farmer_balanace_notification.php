<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<div >
    <div class="col-xs-12" id="system_jqx_container_report_farmer_balance_notification">

    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_item_report_farmer_balance_notification');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'outlet_name', type: 'string' },
                { name: 'barcode', type: 'string' },
                { name: 'name', type: 'string' },
                { name: 'amount_credit_limit', type: 'string' },
                { name: 'amount_credit_balance', type: 'string' },
                { name: 'amount_credit_due', type: 'string' },
                { name: 'amount_last_payment', type: 'string' },
                { name: 'date_last_payment', type: 'string' },
                { name: 'day_last_payment', type: 'string' },
                { name: 'amount_last_sale', type: 'string' },
                { name: 'date_last_sale', type: 'string' },
                { name: 'day_last_sale', type: 'string' },
                { name: 'sale_due_status', type: 'string' }
            ],
            id: 'id',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            if(((column=='date_last_payment')&& (record['date_last_payment']==0))||((column=='day_last_payment')&& (record['day_last_payment']==0))||((column=='date_last_sale')&& (record['date_last_sale']==0))||((column=='day_last_sale')&& (record['day_last_sale']==0)))
            {
                element.html('');
            }
            if(column.substr(0,6)=='amount')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_amount(value));
                }
            }

            return element[0].outerHTML;

        };
        var aggregatesrenderer_amount=function (aggregates)
        {
            var text='';
            if(!((aggregates['sum']=='0.00')||(aggregates['sum']=='')))
            {
                text=get_string_amount(aggregates['sum']);
            }
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';
        };
        // create jqxgrid.
        $("#system_jqx_container_report_farmer_balance_notification").jqxGrid(
            {
                width: '100%',
                height: '545px',
                source: dataAdapter,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                selectionmode: 'singlerow',
                showaggregates: true,
                showstatusbar: true,
                altrows: true,
                rowsheight: 35,
                columnsreorder: true,
                enablebrowserselection: true,
                pageable: true,
                pagesize: 1000,
                pagesizeoptions: ['10', '20', '50', '100', '200', '300', '500','1000'],
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id', width:60, cellsrenderer: cellsrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', dataField: 'outlet_name', width:180, filtertype: 'list', cellsrenderer: cellsrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode', width:80, cellsrenderer: cellsrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name', width:220, cellsrenderer: cellsrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_CREDIT_LIMIT'); ?>', dataField: 'amount_credit_limit', width:120, cellsrenderer: cellsrenderer, cellsalign: 'right'},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_CREDIT_BALANCE'); ?>', dataField: 'amount_credit_balance', width:120, cellsrenderer: cellsrenderer, cellsalign: 'right'},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_CREDIT_DUE'); ?>', dataField: 'amount_credit_due', width:120, cellsrenderer: cellsrenderer, cellsalign: 'right'},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_LAST_PAYMENT'); ?>', dataField: 'amount_last_payment', width:120, cellsrenderer: cellsrenderer, cellsalign: 'right'},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_LAST_PAYMENT'); ?>', dataField: 'date_last_payment', width:140, cellsrenderer: cellsrenderer, cellsalign: 'center'},
                        { text: '<?php echo $CI->lang->line('LABEL_DAY_LAST_PAYMENT'); ?>', dataField: 'day_last_payment', width:140, cellsrenderer: cellsrenderer, cellsalign: 'center'},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_LAST_SALE'); ?>', dataField: 'amount_last_sale', width:120, cellsrenderer: cellsrenderer, cellsalign: 'right'},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_LAST_SALE'); ?>', dataField: 'date_last_sale', width:140, cellsrenderer: cellsrenderer, cellsalign: 'center'},
                        { text: '<?php echo $CI->lang->line('LABEL_DAY_LAST_SALE'); ?>', dataField: 'day_last_sale', width:140, cellsrenderer: cellsrenderer, cellsalign: 'center'},
                        { text: '<?php echo $CI->lang->line('LABEL_SALE_DUE_STATUS'); ?>', dataField: 'sale_due_status', width:200, filtertype: 'list', cellsrenderer: cellsrenderer}
                    ]
            });
    });
</script>
