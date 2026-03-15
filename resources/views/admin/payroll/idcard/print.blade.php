<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Print</title>
    <style>
        /* A4 Page Setup - Portrait */
        @page {
            size: A4 portrait;
            margin: 3mm;
        }

        @media print {
            html, body {
                height: auto;
            }

            .a4-page {
                page-break-after: always;
                break-after: page;
            }

            .card-container {
                page-break-inside: avoid;
                break-inside: avoid;
                display: contents;
            }

            .no-print { display: none !important; }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* ID Card Size: Portrait - 53.98mm x 85.6mm (Credit Card Size) */
        .id-card {
            width: 53.98mm;
            height: 85.6mm;
            background: #fff;
            border: 1px solid #ddd;
            padding: 4mm;
            box-sizing: border-box;
            display: inline-block;
            margin: 0.5mm;
            page-break-inside: avoid;
            vertical-align: top;
            position: relative;
        }

        .card-header-text {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2mm;
            text-align: center;
            color: #1e3c72;
        }

        .card-id-title {
            font-size: 7px;
            text-align: center;
            margin: 2mm 10mm;
            border: 1px solid #ddd;
            padding: 1px;
        }

        .card-divider {
            margin-top: 2px;
            border-bottom: 1px solid #eee;
        }

        .card-profile-pic {
            width: 28mm;
            height: 32mm;
            margin: 0 auto;
            display: block;
            border: 1px solid #ddd;
            object-fit: cover;
            margin-bottom: 2mm;
        }

        .card-details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6px;
        }

        .card-details-table td {
            padding: 0.5px 0px;
        }

        .card-details-label {
            font-weight: 500;
            color: #555;
        }

        .card-details-value {
            text-align: right;
            font-weight: bold;
        }

        .card-signature-row {
            text-align: center;
            position: absolute;
            bottom: 2mm;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            gap: 8px;
            display: flex;
            justify-content: space-between;
            width: 90%;
        }

        .card-signature-box {
            width: 40%;
            text-align: center;
        }

        .card-signature-line {
            border-top: 1px solid #000;
            margin-bottom: 0px;
        }

        .card-signature-text {
            font-size: 4px;
        }

        .card-footer-logo {
            font-size: 5px;
            text-align: center;
            position: absolute;
            bottom: 1mm;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .card-contact-row { font-size: 6px; margin-bottom: 1px; color: #1e3c72; text-align:center;font-weight: bold;}
        .card-contact-row2 { font-size: 5px; margin-bottom: 1px; margin-top: 1px; color:black; text-align:center;}
        .card-warning-text { font-size: 5px; font-style: italic; margin-top: 2mm; border-top: 1px solid #eee; padding-top: 2px; text-align:center; }

        /* A4 Grid: 4 columns x 4 rows = 16 cards per page (portrait), each card has front + back */
        .a4-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            /* grid-auto-rows: 87mm; */
            width: 100%;
        }

        .a4-page {
            width: 100%;
            height: 291mm;
            padding: 1mm;
            box-sizing: border-box;
            page-break-after: always;
        }

        .print-btn {
            position: fixed;
            top: 48vh;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            z-index: 1000;
            animation: pulseBtn 1s infinite;

        }
        .print-btn:hover {
            background: #0056b3;
        }
        @keyframes pulseBtn {
            0% {
                /* transform: scale(1); */
                box-shadow: 0 0 0 0 rgba(0,123,255, 0.6);
            }
            70% {
                /* transform: scale(1.08); */
                box-shadow: 0 0 0 12px rgba(0,123,255, 0);
            }
            100% {
                /* transform: scale(1); */
                box-shadow: 0 0 0 0 rgba(0,123,255, 0);
            }
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Print ID Cards</button>

    @if(count($selectedUsers) > 0)
    @php
    $cardsPerPage = 12; // 4 columns x 3 rows (portrait)
    $totalCards = count($selectedUsers);
    $totalPages = ceil($totalCards / $cardsPerPage);
    $companyName = general()->title ?? 'Company';
    $companyAddress = general()->address_one ?? 'Address';
    @endphp

    @for($page = 0; $page < $totalPages; $page++)
    <div class="a4-page">
        <div class="a4-grid">
            @php
            $start = $page * $cardsPerPage;
            $end = min($start + $cardsPerPage, $totalCards);
            @endphp

            @for($i = $start; $i < $end; $i++)
            @php $user = $selectedUsers[$i]; @endphp
            <div class="card-container">
                <!-- Front Side -->
                <div class="id-card">
                    <div class="card-header-text">{{ $companyName }}</div>
                    <div class="card-id-title">ID CARD</div>

                    <div class="card-profile-pic">
                        @php $userImage = $user->image(); @endphp
                        @if($userImage)
                        <img src="{{ asset($userImage) }}" alt="Profile Picture" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            @if($user->gender == 'female')
                                <img src="https://lh6.googleusercontent.com/proxy/2iXRjCpkVKg-LCg9fo6zQWBf4tyBUuKb5fSOlWwsNsHfyRnhN3M6pJXQBE831R-OqTLPjFM2YS4Wu0yXl8zIDf0AjbWuxMWgRJAWxG0rVuuCNxgOynlN" alt="" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <img src="https://colgate.com.pk/wp-content/uploads/2020/01/no_image-male.jpg" alt="" style="width:100%;height:100%;object-fit:cover;">
                            @endif
                        @endif
                    </div>

                    <div class="card-divider"></div>

                    <table class="card-details-table">
                        <tr>
                            <td class="card-details-label">Name</td><td>:</td>
                            <td class="card-details-value">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td class="card-details-label">Designation</td><td>:</td>
                            <td class="card-details-value">{{ optional($user->designation)->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="card-details-label">ID No</td><td>:</td>
                            <td class="card-details-value">{{ $user->employee_id ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="card-details-label">Department</td><td>:</td>
                            <td class="card-details-value">{{ optional($user->department)->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="card-details-label">Joined</td><td>:</td>
                            <td class="card-details-value">{{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="card-details-label">Issued</td><td>:</td>
                            <td class="card-details-value">{{ now()->format('d/m/Y') }}</td>
                        </tr>
                    </table>

                    <div class="card-signature-row">
                        <div class="card-signature-box">
                            <div class="card-signature-line"></div>
                            <div class="card-signature-text">Holder Signature</div>
                        </div>
                        <div class="card-signature-box">
                            <div class="card-signature-line"></div>
                            <div class="card-signature-text">Authorized</div>
                        </div>
                    </div>
                </div>

                <!-- Back Side -->
                <div class="id-card">
                    <table class="card-details-table">
                        <tr>
                            <td class="card-details-label">Validity</td><td>:</td>
                            <td class="card-details-value">{{ $user->employment_status ?? 'Until Employment Ends' }}</td>
                        </tr>
                        <tr>
                            <td class="card-details-label">Blood Group</td><td>:</td>
                            <td class="card-details-value">{{ $user->blood_group ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="card-details-label">Emergency</td><td>:</td>
                            <td class="card-details-value">{{ $user->emergency_mobile ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="card-details-label">NID / Birth</td><td>:</td>
                            <td class="card-details-value">{{ $user->nid_number ?? '-' }}</td>
                        </tr>
                    </table>

                    <div class="card-divider" style="margin-bottom:2px;"></div>

                    <div class="card-contact-row">{{ $companyName }}</div>
                    <div class="card-contact-row2">{{ $companyAddress }}</div>
                    <div class="card-contact-row2">{{ general()->phone ?? '' }}</div>

                    <div class="card-warning-text">
                        If lost, inform management immediately.<br>
                        This card is issued under Bangladesh Labor Act 2015, Form 6.
                    </div>

                    <div class="card-footer-logo">
                        {{ $companyName }}
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
    @endfor

    @endif
</body>
</html>
