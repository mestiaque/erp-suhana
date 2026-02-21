@push('js')

<script>
$(document).ready(function(){

    /* ============================
    ADD ROW FUNCTION
    ============================ */
    function addRow(tableId, rowClass){
        let $tbody = $('#' + tableId + ' tbody');
        let $newRow = $tbody.find('tr.' + rowClass).first().clone();

        let company_name = '';
        let payment_value = '';

        // Check last row first
        let $lastRow = $tbody.find('tr.' + rowClass).last();
        let lastCompVal = $lastRow.find('input[name*="[company_name]"]').val();
        let lastPayVal = $lastRow.find('input[name*="[payment_value]"]').val();

        if(lastCompVal){
            company_name = lastCompVal;
        }
        if(lastPayVal){
            payment_value = lastPayVal;
        }

        // If last row value empty, find first non-empty in all rows
        if(!company_name || !payment_value){
            $tbody.find('tr.' + rowClass).each(function(){
                let compval = $(this).find('input[name*="[company_name]"]').val();
                let payval = $(this).find('input[name*="[payment_value]"]').val();

                if(!company_name && compval){
                    company_name = compval;
                }

                if(!payment_value && payval){
                    payment_value = payval;
                }

                // stop if both found
                if(company_name && payment_value) return false;
            });
        }

        // Clear all inputs except percent / company_name / payment_value
        $newRow.find('input').not(
            'input[name*="[percent]"], input[name*="[company_name]"], input[name*="[payment_value]"]'
        ).val('');

        $newRow.find('input[name*="[company_name]"]').val(company_name);
        $newRow.find('input[name*="[payment_value]"]').val(payment_value);

        // Hide % / Company / Payment in new row (except testTable)
        if(tableId !== 'testTable'){
            $newRow.find('input[name*="[percent]"], input[name*="[company_name]"], input[name*="[payment_value]"]').hide();
        }

        $tbody.append($newRow);
        calculateAllTables();
    }



    /* ============================
    REMOVE ROW FUNCTION
    ============================ */
    $(document).on('click', '.removeRow, .removeRows', function(){
        let $tbody = $(this).closest('tbody');
        if($tbody.find('tr').length > 1){
            $(this).closest('tr').remove();
            calculateAllTables();
        }
    });

    /* ============================
    CALCULATION FOR SINGLE TABLE
    ============================ */
    function calculateTable(tableId){
        let table = $(tableId);
        let piValue = parseFloat($('#pi-value').val()) || 0;
        let total = 0;

        let $rows = table.find('tbody tr');

        $rows.each(function(){
            let $row = $(this);
            let qty = parseFloat($row.find('input[name*="[qty]"], input[name*="_qty]"]').val()) || 0;
            let unit = parseFloat($row.find('input[name*="[unit_price]"], input[name*="_unit_price]"]').val()) || 0;
            let ttlInput = $row.find('input.ttl_usd');
            let itemTotalInput = $row.find('input.item_total');
            let percentInput = $row.find('input[name*="[percent]"]');

            let ttl = qty * unit;
            if(ttlInput.length) ttlInput.val(ttl.toFixed(2));

            // If this is testTable, calculate per row
            if(tableId === '#testTable'){
                if(itemTotalInput.length) itemTotalInput.val(ttl.toFixed(2));
                if(percentInput.length){
                    let percent = piValue ? (ttl / piValue * 100) : 0;
                }
                let percentInputx = $row.find('.testPer');
                if(percentInputx.length){
                    let percent = piValue ? (ttl / piValue * 100) : 0;
                    percentInputx.val(percent.toFixed(2));
                }

            } else {
                total += ttl;
            }
        });

        // Normal tables: show last row item_total & percent
        if(tableId !== '#testTable'){
            $rows.find('input.item_total, input[name*="[percent]"], input[name*="[company_name]"], input[name*="[payment_value]"]').hide();
            let $lastRow = $rows.last();
            $lastRow.find('input.item_total').show().val(total.toFixed(2));
            let percentInput = $lastRow.find('input[name*="[percent]"]');
            if(percentInput.length){
                let percent = piValue ? (total / piValue * 100) : 0;
                percentInput.show().val(percent.toFixed(2));
            }
            $lastRow.find('input[name*="[company_name]"], input[name*="[payment_value]"]').show();
        } else {
            // For testTable: sum all item_total for summary
            $rows.find('input.item_total').each(function(){
                total += parseFloat($(this).val()) || 0;
            });
        }

        return total;
    }

    function calculateTestTable(){
        let piValue = parseFloat($('#pi-value').val()) || 0;
        let $rows = $('#testTable tbody tr.testRow');

        $rows.each(function(){
            let $row = $(this);

            // Only process rows with actual inputs (skip header/label rows)
            let $qtyInput = $row.find('input[name*="[qty]"]');
            let $unitInput = $row.find('input[name*="[unit_price]"]');
            let $ttlInput = $row.find('input.ttl_usd');
            let $itemTotalInput = $row.find('input.item_total');
            let $percentInput = $row.find('.testPer');

            if($qtyInput.length && $unitInput.length){
                let qty = parseFloat($qtyInput.val()) || 0;
                let unit = parseFloat($unitInput.val()) || 0;
                let ttl = qty * unit;

                // Set TTL and Item Total
                if($ttlInput.length) $ttlInput.val(ttl.toFixed(2));
                if($itemTotalInput.length) $itemTotalInput.val(ttl.toFixed(2));

                // Set %
                if($percentInput.length){
                    let percent = piValue ? (ttl / piValue * 100) : 0;
                    $percentInput.val(percent.toFixed(2));
                }
            }
        });
    }

    /* ============================
    CALCULATE ALL TABLES + SUMMARY
    ============================ */
    function calculateAllTables(){
        let piValue = parseFloat($('#pi-value').val()) || 0;
        let grandTotal = 0;

        let tableIds = ['#yarnTable', '#knittingTable', '#dyeingTable', '#accessoriesTable', '#printEmbroideryTable', '#cmTable', '#testTable'];

        tableIds.forEach(function(id){
            if($(id).length){
                let total = calculateTable(id);
                grandTotal += total;
            }
        });

        // ==============================
        // Update Summary Table
        // ==============================
        $('.summary_total_expenditure').val(grandTotal.toFixed(2));
        $('.summary_expenditure_percent').val(piValue ? ((grandTotal/piValue)*100).toFixed(2) : '0.00');
        $('.summary_reservation').val((piValue - grandTotal).toFixed(2));

        // BTB & CASH
        let btbPercent = parseFloat($('.btb_percent_input').val()) || 0;
        let cashPercent = parseFloat($('.cash_percent_input').val()) || 0;

        let btbValue = piValue * btbPercent / 100;
        let cashValue = piValue * cashPercent / 100;

        $('.btb_value').val(btbValue.toFixed(2));
        $('.cash_value').val(cashValue.toFixed(2));

        // BBLC details
        let bbclYarnDyeingPrint = 0;
        let bbclKnitting = 0;

        bbclYarnDyeingPrint += parseFloat($('#yarnTable tbody tr:last input.item_total').val()) || 0;
        bbclYarnDyeingPrint += parseFloat($('#dyeingTable tbody tr:last input.item_total').val()) || 0;
        bbclYarnDyeingPrint += parseFloat($('#accessoriesTable tbody tr:last input.item_total').val()) || 0;
        bbclYarnDyeingPrint += parseFloat($('#printEmbroideryTable tbody tr:last input.item_total').val()) || 0;

        bbclKnitting += parseFloat($('#knittingTable tbody tr:last input.item_total').val()) || 0;

        $('.bbcl_yarn_dyeing_print_access').val(bbclYarnDyeingPrint.toFixed(2) + ' Yarn, Dyeing, Print & Acces');
        $('.bbcl_yarn_dyeing_print_access_val').val(bbclYarnDyeingPrint.toFixed(2));

        $('.bbcl_knitting').val(bbclKnitting.toFixed(2) + ' Knitting');
        $('.bbcl_knitting_val').val(bbclKnitting.toFixed(2));
    }

    // Trigger calculation on input
    $(document).on('keyup input change paste cut drop blur', '#pi-value, #pi_id, input.calc, .btb_percent_input, .cash_percent_input', function(){
        calculateAllTables();
        calculateTestTable();
    });

    // Initial calculation
    calculateAllTables();

    /* ============================
    BIND ADD ROW BUTTONS
    ============================ */
    $('#addYarnRow').click(()=>addRow('yarnTable','yarnRow'));
    $('#addKnitRow').click(()=>addRow('knittingTable','knitRow'));
    $('#addDyeingRow').click(()=>addRow('dyeingTable','dyeingRow'));
    $('#addAccessoriesRow').click(()=>addRow('accessoriesTable','accessoryRow'));
    $('#addPrintEmbRow').click(()=>addRow('printEmbroideryTable','printEmbRow'));
    $('#addCMRow').click(()=>addRow('cmTable','cmRow'));
    $('#addTestRow').click(()=>addRow('testTable','testRow'));

});
</script>


<script>
    function getSelectedPiJson() {
        let option = $('#pi_id option:selected');
        let jsonStr = option.attr('data-pi-json'); // get raw string
        let jsonObj = JSON.parse(jsonStr); // parse to JS object
        return jsonObj;
    }

    // On change
    $(document).on('change', '#pi_id', function () {
        let selectedJson = getSelectedPiJson();
        console.log(selectedJson);
        $('.buyer_name').val(selectedJson.buyer_name)
        $('.pi_value').val(parseFloat(selectedJson.total_bill).toFixed(2));
        $('.summary_pi_value').val(parseFloat(selectedJson.total_bill).toFixed(2));
        $('.total_qty').val(selectedJson.total_qty)
        $('.pi_total_qty').val(selectedJson.total_qty)
        $('.order_count').val(selectedJson.order_count)
        $('.style_count').val(selectedJson.style_count)
        $('.item_name').val(selectedJson.item_name)
        $('.shipment_date').val(selectedJson.shipment_date)
        console.log(selectedJson.shipment_date);
    });
</script>

<script>
    $(document).on('keyup input change paste cut drop blur', '.o_cost, .machine_use, .production_total_cost, .production_day, .cm_doz', function() {
        let $tr = $(this).closest('tr'); // jQuery wrapper
        let ocost = parseFloat($tr.find('.o_cost').val()) || 0;
        let machine_use = parseFloat($tr.find('.machine_use').val()) || 0;
        let total_cost = ocost * machine_use;
        $tr.find('.production_total_cost').val(total_cost.toFixed(2));
        // let product_day = parseFloat($tr.find('.production_day').val()) || 0;
        // let cm_doz = product_day ? (total_cost / product_day) : 0;
        // $tr.find('.cm_doz').val(cm_doz.toFixed(2));
    });
</script>

@endpush
