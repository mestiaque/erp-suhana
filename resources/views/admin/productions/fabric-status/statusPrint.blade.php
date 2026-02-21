<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{websiteTitle('PI Fabric Status')}}</title>
    <link rel="apple-touch-icon" href="{{asset(general()->favicon())}}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{asset(general()->favicon())}}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f2f2f2;
            font-size: 10px;

        }
        p{
            margin: 2px;
        }

        /* -------- A4 Layout -------- */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            padding: 4mm;
            margin: 0px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .no-print-container {
            width: 210mm;
            padding: 1mm;
            margin: 10px auto;
        }

        /* -------- Table Fix -------- */
        table {
            width: 100%;
            border-collapse: collapse !important;
        }

        table th, table td {
            border: 1px solid #dee2e6 !important;
            padding: 4px 6px;
        }

        thead th {
            background: #e9ecef !important;
        }

        tr, td, th {
            page-break-inside: avoid !important;
        }


        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 20px;
        }

        .signature-box {
            text-align: center;
            flex: 1;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 40px 20px 5px 20px;
            position: relative;
        }

        .signature-text {
            font-family: 'Brush Script MT', cursive;
            font-size: 24px;
            margin-top: -35px;
            color: #1a3d0a;
        }
        #fabStatTabl{
            font-size: 0.2rem !important;
        }


        /* -------- Print Mode -------- */
        @media print {
            body {
                background: none;
                font-size: 10px;
            }
            .print-container {
                margin: 0;
                width: 100%;
                min-height: auto;
                box-shadow: none;
                padding: 0;
            }
            @page {
                size: A4;
                margin: 4mm;
            }
            .no-print-container{
                display: none !important;
            }

        }
    </style>
</head>

<body>
<div class="no-print-container"
     style="
        position:sticky;
        top:0;
        z-index:999;
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:10px 0;

     ">

    <!-- Back Button (Left) -->
<a href="{{ route('admin.piWiseFabricStatus', [
        'pi_id'   => request('pi_id'),
        'pi_text' => request('pi_text')
    ]) }}"
   style="
        padding:6px 18px;
        background:#6c757d;
        color:#fff;
        border-radius:4px;
        text-decoration:none;
        font-size:14px;
        border:1px solid #6c757d;
   ">
    ← Back
</a>

    <!-- Print Button (Right) -->
    <button id="PrintAction"
        style="
            padding:6px 18px;
            background:#0d6efd;
            color:#fff;
            border-radius:4px;
            border:1px solid #0d6efd;
            font-size:14px;
            cursor:pointer;
        ">
        🖨️ Print
    </button>

</div>
<div class="print-container">
        <div class="textarea" style="">
            <div class="text-center mb-2" style="">
                <div class="row text-left">
                    <div class="col-1 psss-0">
                        <img src="{{asset(general()->logo())}}" alt="logo" style="max-height: 44px;">
                    </div>
                    <div class="col-8 p-0" style="text-align: left; font-size:16px">
                        <p style="text-align: center; font-size: 40px; font-family: serif; line-height: 39px;">
                            {{general()->title}}
                        </p>
                    </div>
                    <div class="col-3 p-0" style="text-align: left">

                        {!!general()->address_one!!}<br>
                        <b>Phone:</b> {{general()->mobile}}
                        <br>
                        <b>Email:</b> {{general()->email}}<br>
                    </div>
                </div>

                <span style="display: inline-block;padding: 2px 25px;border: 1px solid #ddd;border-radius: 4px;background: #fbfbfb;">
                    PI WISE FABRIC STATUS
                </span>
            </div>
            @include(adminTheme().'productions.fabric-status.table')
            <div class="signature-section d-none">
                <div class="signature-box">
                    <div class="signature-line">
                        <div class="signature-text" style="height: 1px;"></div>
                    </div>
                    <small>Accounts Officer</small>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        <div class="signature-text" style="height: 1px;"></div>
                    </div>
                    <small>Accounts Manager</small>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        <div class="signature-text" style="height: 1px;" ></div>
                    </div>
                    <small>Managing Director</small>
                </div>
            </div>
        </div>







</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{asset('admin/assets/js/inword.js')}}"></script>
<script>
    // window.print();
    document.getElementById('PrintAction').addEventListener('click', function () {
        window.print();
    });

    var amount = Number($('#total_amount_input').val());
    console.log(amount);
    var words = toWords(amount);
    $('#total_amount_word').html(words + ' Taka Only');

</script>

</body>
</html>
