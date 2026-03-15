<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Cards - {{ $user->name }}</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;700&display=swap');

        body { font-family: 'Arial', sans-serif; margin: 20px; background-color: #f0f0f0; }
        .cards-container { display: flex; flex-direction: column; gap: 60px; }

        .card-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: bold;gap: 20px;justify-content: center;flex-wrap: wrap;}
        .header-title{padding:1rem;}
        .card-wrapper { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
        .card { width: 53.98mm; height: 85.6mm; background: #fff; border: 1px solid #ddd; padding: 10px; box-sizing: border-box; box-shadow: 0 4px 8px rgba(0,0,0,0.1); position: relative;}

        .header { font-size: 14px; font-weight: bold; margin-bottom: 8px; text-align:center; color:red; }
        .header2 { font-size: 12px; font-weight: bold; margin-bottom: 6px; margin-top: 10px; text-align:center; color:green; }
        
        .id-title { font-size: 12px; text-align: center; margin: 5px 45px; border: 1px solid #ddd; padding: 2px; }
        .divider { margin-top: 5px; border-bottom: 1px solid #eee; }
        .details-row { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 3px; }
        .label { font-weight: 500; color: #555; }
        .value { text-align: right; font-weight: bold; }
        .profile-pic { width: 90px; height: 100px; margin: 0 auto; display: block; border: 1px solid #ddd; object-fit: cover; }
        .footer-logo { font-size: 9px; text-align: center; position: absolute; bottom: 1mm; left: 50%; transform: translateX(-50%); white-space: nowrap; }
        .address-text { font-size: 11px; line-height: 1.2; margin-top: 5px; text-align:center }
        .contact-row {  font-size: 13px; margin-bottom: 3px; color:red; text-align:center;font-weight: bold;}
        .contact-row2 {  font-size: 12px; margin-bottom: 3px; margin-top: 3px; color:black; text-align:center;}
        .contact-icon { margin-right: 5px; }
        .warning-text { font-size: 10px; font-style: italic; margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px; text-align:center; }
        .website { font-size: 9px; text-align: center; margin-top: 39px; }
        .print-btn { padding: 8px 12px; margin-bottom: 10px; cursor: pointer; }
        .details-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 0px; }
        .details-table td { padding: 3px 0px; }
        .details-label { font-weight: 500; color: #555; }
        .details-value { text-align: right; }
        .details-table td { padding: 3px 0px; }
        .ban-cw .details-table td { padding: 1px 0px; }
        .signature-row { text-align: center; position: absolute; bottom: 4mm; left: 50%; transform: translateX(-50%); white-space: nowrap; gap: 50px; display: flex; justify-content: space-between;  }
        .signature-box { width: 45%; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-bottom: 2px; }
        .signature-text { font-size: 8px; }


        @media print {
            body { background: #fff; }
            .print-btn { display: none; }
            .cards-container { gap: 0; }
            .card { box-shadow: none; margin: 0 auto; }
        }
    </style>
</head>
<body>

<div class="cards-container">

    {{-- English Card --}}
    <div>
        <div class="card-header">
            <div>
                <button class="print-btn" onclick="printCard('english-front')">Print EN Front</button>
                <span class="header-title header-title-1">ENGLISH ID CARD</span>
                <button class="print-btn" onclick="printCard('english-back')">Print EN Back</button>
            </div>
        </div>

        <div class="card-wrapper en-cw">
            {{-- Front --}}
            <div class="card" id="english-front">
                <div class="header">Assist IT Solution</div>
                <div class="id-title">ID CARD</div>
            
                <img src="{{ asset($user->image()) }}" alt="Profile Picture" class="profile-pic">
            
                <div class="divider"></div>
            
                <table class="details-table">
                    <tr>
                        <td class="details-label">Name</td><td>:</td>
                        <td class="details-value">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Designation</td><td>:</td>
                        <td class="details-value">{{ optional($user->designation)->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">ID No</td><td>:</td>
                        <td class="details-value">{{ $user->employee_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Department</td><td>:</td>
                        <td class="details-value">{{ optional($user->department)->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Joined</td><td>:</td>
                        <td class="details-value">{{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Issued</td><td>:</td>
                        <td class="details-value">{{ now()->format('d/m/Y') }}</td>
                    </tr>
                </table>
                
                
                <div class="signature-row">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-text">Holder Signature</div>
                    </div>
                
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-text">Authorized Signature</div>
                    </div>
                </div>
            
                <div class="footer-logo">
                    Managed by Assist IT Solution
                </div>
            </div>


            {{-- Back --}}
            <div class="card" id="english-back">

                <table class="details-table">
                    <tr>
                        <td class="details-label">Validity</td><td>:</td>
                        <td class="details-value">{{ $user->employment_status ?? 'Until Employment Ends' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Blood Group</td><td>:</td>
                        <td class="details-value">{{ $user->blood_group ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Emergency</td><td>:</td>
                        <td class="details-value">{{ $user->emergency_mobile ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">NID / Birth</td><td>:</td>
                        <td class="details-value">{{ $user->nid_number ?? '-' }}</td>
                    </tr>
                </table>
            
                <div class="divider" style="margin-bottom:5px;"></div>
            
                <div class="header2">Company Address:</div>
                <div class="contact-row">Assist IT Solution</div>
            
                <div class="address-text">
                    {{ config('company.address', 'Sector 3, Uttara, Dhaka-1230, Bangladesh.') }}
                </div>
            
                <div class="contact-row2">
                    <span class="contact-icon">📞</span>{{ config('company.phone', '0101100110011') }}
                </div>
            
                <div class="warning-text">
                    If lost, inform management immediately.
                    This card is issued under Bangladesh Labor Act 2015, Form 6.
                </div>
            
                <div class="footer-logo">
                    {{ config('company.website', 'www.assistitsolution.com') }}
                </div>
            </div>

        </div>
    </div>

    {{-- Bangla Card --}}
    <div>
        <div class="card-header">
            <div>
                <button class="print-btn" onclick="printCard('bangla-front')">Print BN Front</button>
                <span class="header-title header-title-2">BANGLA ID CARD</span>
                <button class="print-btn" onclick="printCard('bangla-back')">Print BN Back</button>
            </div>
        </div>

        <div class="card-wrapper ban-cw">
            {{-- Front --}}
            <div class="card" id="bangla-front">
                <div class="header"> অ্যাসিস্ট আইটি সলিউশন </div>
                <div class="id-title">আইডি কার্ড</div>
                <img src="{{ asset($user->image()) }}" 
                     alt="Profile Picture" class="profile-pic">
                <div class="divider"></div>
                <table class="details-table">
                    <tr>
                        <td class="details-label">       নাম         </td> <td>:</td>
                        <td class="details-value">  {{ $user->name }}  </td>
                    </tr>
                    <tr>
                        <td class="details-label">        পদবী         </td> <td>:</td>
                        <td class="details-value">  {{ optional($user->designation)->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">            আইডি নং                         </td>      <td>:</td>
                        <td class="details-value">{{ $user->employee_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">         বিভাগ             </td> <td>:</td>
                        <td class="details-value"> {{ optional($user->department)->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">       যোগদান     :</td>    <td>:</td>
                        <td class="details-value"> {{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">        ইস্যুর                    </td> <td>:</td>
                        <td class="details-value"> {{ now()->format('d/m/Y') }}</td>
                    </tr>
                </table>
                
                <div class="signature-row">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-text">Holder Signature</div>
                    </div>
                
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-text">Authorized Signature</div>
                    </div>
                </div>

                <div class="footer-logo">   পরিচালনায় অ্যাসিস্ট আইটি সলিউশন     </div>
            </div>

            {{-- Back --}}
            <div class="card" id="bangla-back">
                <table class="details-table">
                    <tr>
                        <td class="details-label">মেয়াদ       </td>                            <td>:</td>
                        <td class="details-value">  {{ $user->employment_status ?? 'চাকুরি নিষ্পত্তি পর্যন্ত' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">রক্তের গ্রুপ                    </td>  <td>:</td>
                        <td class="details-value">  {{ $user->blood_group ?? 'AB+' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">জরুরি ফোন               </td>               <td>:</td>
                        <td class="details-value">  {{ $user->emergency_mobile ?? '01000000000' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">এনআইডি  
                        <!--/জন্ম নিবন্ধন                -->
                         </td>           <td>:</td>
                        <td class="details-value">{{ $user->nid_number ?? '12345678901234567' }}</td>
                    </tr>
                </table>
                <div class="divider" style="    margin-bottom: 5px;"></div>
                <div class="header2"> প্রতিষ্ঠানের ঠিকানা:             </div>
                <div class="contact-row">  অ্যাসিস্ট আইটি সলিউশন       </div>
                <div class="address-text">{{ config('company.address', 'সেক্টর ৩, উত্তরা, ঢাকা-১২৩০, বাংলাদেশ।') }}</div>
                <div class="contact-row2"><span class="contact-icon">📞</span>{{ config('company.phone', '০১৩০৬৬৭৬৬৪৯৮') }}</div>
                
                <div class="warning-text">  উক্ত পরিচয়পত্র হারিয়ে গেলে ব্যবস্থাপনা কর্তৃপক্ষকে জানাইতে হবে। উক্ত আইডি কার্ডটি বাংলাদেশের শ্রম ২০১৫ এর ফর্ম ৬ অনুযায় গঠিত।                   </div>
                <div class="footer-logo">{{ config('company.website', 'www.assistitsolution.com') }}</div>
            </div>
        </div>
    </div>

</div>

<script>
function printCard(cardId) {
    var card = document.getElementById(cardId);
    if (!card) {
        alert("Card not found!");
        return;
    }

    var printWindow = window.open('', '_blank', 'width=1200,height=800');

    printWindow.document.write('<html><head><title>Print ID Card</title>');
    printWindow.document.write('<style>');
    /* BODY */
    printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background:#fff; }');
    
    /* CARD LAYOUT */
    printWindow.document.write('.card { width: 53.98mm; height: 85.6mm; background: #fff; border: 1px solid #ddd; padding: 10px; box-sizing: border-box; box-shadow: none; margin: 0 auto; position: relative;}');
    
    /* HEADERS */
    printWindow.document.write('.header { font-size: 14px; font-weight: bold; margin-bottom: 8px; text-align:center; color:red; }');
    printWindow.document.write('.header2 { font-size: 12px; font-weight: bold; margin-bottom: 6px; margin-top: 10px; text-align:center; color:green; }');
    
    /* TITLE */
    printWindow.document.write('.id-title { font-size: 12px; text-align: center; margin: 5px 45px; border: 1px solid #ddd; padding: 2px; }');
    
    /* DIVIDER */
    printWindow.document.write('.divider { margin-top: 5px; border-bottom: 1px solid #eee; }');
    
    /* PROFILE IMAGE */
    printWindow.document.write('.profile-pic { width: 90px; height: 100px; margin: 0 auto; display: block; border: 1px solid #ddd; object-fit: cover; }');
    
    /* DETAILS TABLE */
    printWindow.document.write('.details-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 0; }');
    printWindow.document.write('.details-table td { padding: 3px 0px; }');
    printWindow.document.write('.ban-cw .details-table td { padding: 1px 0px; }');
    printWindow.document.write('.details-label { font-weight: 500; color: #555; }');
    printWindow.document.write('.details-value { text-align: right; }');
    
    /* FOOTER */
    printWindow.document.write('.footer-logo { font-size: 9px; text-align: center; position: absolute; bottom: 1mm; left: 50%; transform: translateX(-50%); white-space: nowrap; }');
    
    /* ADDRESS & CONTACT */
    printWindow.document.write('.address-text { font-size: 11px; line-height: 1.2; margin-top: 5px; text-align:center; }');
    printWindow.document.write('.contact-row { font-size: 13px; margin-bottom: 3px; color:red; text-align:center; font-weight:bold; }');
    printWindow.document.write('.contact-row2 { font-size: 12px; margin-bottom: 3px; margin-top: 3px; color:black; text-align:center; }');
    
    /* WARNING */
    printWindow.document.write('.warning-text { font-size: 10px; font-style: italic; margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px; text-align:center; }');
    
    printWindow.document.write('.signature-box { width: 45%; text-align: center; }');
    printWindow.document.write('.signature-line { border-top: 1px solid #000; margin-bottom: 2px; }');
    printWindow.document.write('.signature-text { font-size: 8px; }');
    printWindow.document.write('.signature-row { text-align: center; position: absolute; bottom: 4mm; left: 50%; transform: translateX(-50%); white-space: nowrap; gap: 50px; display: flex; justify-content: space-between;  }');
    
    /* WEBSITE */
    // printWindow.document.write('.website { font-size: 9px; text-align: center; margin-top: 39px; }');
    // ট্যাগ ক্লোজ করার সময় সতর্কতা
    printWindow.document.write('<\/style><\/head><body>'); 

    printWindow.document.write(card.outerHTML);

    printWindow.document.write('<\/body><\/html>');
    printWindow.document.close();

    // ইমেজ লোড হওয়ার জন্য সামান্য সময় দেওয়া ভালো
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };
}


</script>

</body>
</html>
