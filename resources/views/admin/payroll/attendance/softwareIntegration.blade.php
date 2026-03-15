@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Import ZKTeco Attendance') }}</title>
@endsection

@push('css')
<style>
    .import-container {
        max-width: 700px;
        margin: 40px auto;
    }
    .upload-area {
        border: 2px dashed #56d2ff;
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        background: #f8fdff;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .upload-area:hover, .upload-area.dragover {
        background: #eefbff;
        border-color: #007bff;
    }
    .upload-icon {
        font-size: 50px;
        color: #56d2ff;
        margin-bottom: 15px;
    }
    #attendance_file {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }
    .file-info {
        margin-top: 15px;
        display: none;
        font-weight: 600;
        color: #333;
    }
    .progress-wrapper {
        display: none;
        margin-top: 20px;
    }
    .progress {
        height: 10px;
        border-radius: 5px;
    }
    .btn-import {
        background: #56d2ff;
        border: none;
        color: #fff;
        padding: 10px 30px;
        font-weight: 600;
        border-radius: 25px;
        transition: 0.3s;
    }
    .btn-import:hover {
        background: #33b5e5;
        box-shadow: 0 4px 12px rgba(86, 210, 255, 0.4);
    }
    .instruction-card {
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        border-left: 4px solid #56d2ff;
        margin-bottom: 20px;
    }
    .guideline-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(86, 210, 255, 0.10);
        padding: 14px 12px;
        margin-bottom: 12px;
        border-left: 5px solid #56d2ff;
        transition: box-shadow 0.3s, border-color 0.3s;
        position: relative;
    }
    .guideline-section:hover {
        box-shadow: 0 6px 24px rgba(86, 210, 255, 0.18);
        border-left: 5px solid #007bff;
        background: #f8fdff;
    }
    .guideline-section h5 {
        color: #007bff;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
        font-size: 1.1rem;
        text-shadow: 0 2px 8px rgba(86,210,255,0.08);
        padding: 4px 12px 4px 0;
        border-radius: 6px;
        display: inline-block;
    }
    .guideline-section ul, .guideline-section ol {
        margin-bottom: 0;
    }
    .smart-list-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        border-radius: 6px;
        padding: 10px 14px;
        color: #222;
        font-weight: 500;
        box-shadow: 0 1px 4px rgba(86,210,255,0.07);
        line-height: 1.5;
        margin-bottom: 6px;
        transition: background 0.2s, box-shadow 0.2s, color 0.2s;
    }
    .smart-list-item:hover {
        background: #e3f2fd;
        box-shadow: 0 4px 16px rgba(86,210,255,0.13);
        color: #007bff;
    }
    .smart-icon {
        color: #56d2ff;
        font-size: 1.1em;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
    }
</style>
@endpush

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="row">
        <div class="col-md-6" style="background: rgb(254, 240, 255)">
            <div class=" border-0 mt-4">
                <div class="guideline-section bg-white shadow-sm mb-3">
                    <h4 class="card-title text-center text-primary mb-0">ZKTimeSync Guideline</h4>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-info-circle"></i> ভূমিকা</h5>
                            <p class="mb-0">Python ভিত্তিক টাইম অ্যান্ড অ্যাটেনডেন্স ডিভাইস ম্যানেজমেন্ট টুল।</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-download"></i> ইনস্টলেশন</h5>
                            <div class="smart-list">
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-download"></i></span> App ডাউনলোড <a href="{{ url('admin/download/zk-installer') }}" download class="btn btn-import btn-sm py-1 px-2"><i class="fa fa-download"></i></a></div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-exclamation-triangle"></i></span> "Don't run" দেখলে "More info" → "Run anyway"</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-cogs"></i> GUI বৈশিষ্ট্য</h5>
                            <div class="smart-list row g-2">
                                <div class="smart-list-item col-4"><span class="smart-icon"><i class="fa fa-plus-circle"></i></span> Add</div>
                                <div class="smart-list-item col-4"><span class="smart-icon"><i class="fa fa-edit"></i></span> Edit</div>
                                <div class="smart-list-item col-4"><span class="smart-icon"><i class="fa fa-trash"></i></span> Remove</div>
                                <div class="smart-list-item col-4"><span class="smart-icon"><i class="fa fa-calendar"></i></span> Date</div>
                                <div class="smart-list-item col-4"><span class="smart-icon"><i class="fa fa-refresh"></i></span> Auto</div>
                                <div class="smart-list-item col-4"><span class="smart-icon"><i class="fa fa-bolt"></i></span> Sync</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-server"></i> ডিভাইস পরিচালনা</h5>
                            <div class="smart-list">
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-plus"></i></span> Add: নাম, IP, Port, Password</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-edit"></i></span> Edit: তথ্য পরিবর্তন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-trash"></i></span> Remove: মুছে ফেলা</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-calendar"></i> তারিখ নির্বাচন</h5>
                            <div class="smart-list row g-2">
                                <div class="smart-list-item col-3"><span class="smart-icon"><i class="fa fa-calendar"></i></span> Today</div>
                                <div class="smart-list-item col-3"><span class="smart-icon"><i class="fa fa-calendar"></i></span> Yesterday</div>
                                <div class="smart-list-item col-3"><span class="smart-icon"><i class="fa fa-calendar"></i></span> 7 Days</div>
                                <div class="smart-list-item col-3"><span class="smart-icon"><i class="fa fa-calendar"></i></span> Custom</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-clock"></i> অটো সিঙ্ক</h5>
                            <div class="smart-list row g-2">
                                <div class="smart-list-item col-2"><span class="smart-icon"><i class="fa fa-clock"></i></span> 1h</div>
                                <div class="smart-list-item col-2"><span class="smart-icon"><i class="fa fa-clock"></i></span> 4h</div>
                                <div class="smart-list-item col-2"><span class="smart-icon"><i class="fa fa-clock"></i></span> 6h</div>
                                <div class="smart-list-item col-2"><span class="smart-icon"><i class="fa fa-clock"></i></span> 8h</div>
                                <div class="smart-list-item col-2"><span class="smart-icon"><i class="fa fa-clock"></i></span> 12h</div>
                                <div class="smart-list-item col-2"><span class="smart-icon"><i class="fa fa-clock"></i></span> 24h</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-exclamation-triangle"></i> সমস্যা হলে করণীয়</h5>
                            <div class="smart-list">
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-wifi"></i></span> ডিভাইস এবং কম্পিউটার একই নেটওয়ার্ক (LAN/WiFi) এ সংযুক্ত আছে কিনা নিশ্চিত করুন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-globe"></i></span> নেটওয়ার্ক বা ইন্টারনেট সংযোগ সক্রিয় আছে কিনা যাচাই করুন </div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-key"></i></span> ডিভাইসের IP Address, Port এবং Communication Password সঠিকভাবে সেট করা আছে কিনা দেখুন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-shield"></i></span> সফটওয়্যারটি Administrator হিসেবে (Run as Administrator) চালু করুন</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section mb-0">
                            <h5><i class="fa fa-desktop"></i> সমর্থিত প্ল্যাটফর্ম</h5>
                            <p class="mb-0"><i class="fab fa-windows"></i> Windows 8 অথবা তার পরবর্তী ভার্সন</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-2">
                    <small class="text-muted">&copy; 2026 ZKTimeSync Tutorial</small>
                </div>
            </div>
        </div>
        <div class="col-md-6" style="background: rgb(240, 255, 242)">
            <div class=" border-0 mt-4">
                <div class="guideline-section bg-white shadow-sm mb-3">
                    <h4 class="card-title text-center text-primary mb-0">ZKTimeADMS Guideline</h4>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-info-circle"></i> ভূমিকা</h5>
                            <p class="mb-0"><b>ADMS</b> হলো একটি প্রযুক্তি যার মাধ্যমে বায়োমেট্রিক ডিভাইস ইন্টারনেট ব্যবহার করে সরাসরি সার্ভারে উপস্থিতির (Attendance) ডেটা পাঠাতে পারে</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-download"></i> ইনস্টলেশন</h5>
                            <div class="smart-list">
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-download"></i></span> App ডাউনলোড <a href="{{ url('admin/download/zk-installer-v2') }}" download class="btn btn-import btn-sm py-1 px-2"><i class="fa fa-download"></i></a></div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-exclamation-triangle"></i></span> "Don't run" দেখলে "More info" → "Run anyway"</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-cogs"></i> GUI বৈশিষ্ট্য</h5>
                            <div class="smart-list row g-2">
                                <div class="smart-list-item col-12"><span class="smart-icon"><i class="fa fa-bolt"></i></span>On Punch Sync (পাঞ্চ করার সাথে সাথে ডেটা সার্ভারে সিঙ্ক হবে)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-server"></i> ডিভাইস পরিচালনা</h5>
                            <div class="smart-list">
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-server"></i></span> সফটওয়্যার ইনস্টল করার পর সেখানে একটি Server IP এবং Port দেখাবে। এই তথ্যগুলো Attendance Machine-এ সেট করতে হবে।</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-step-forward"></i> মেশিন সেটআপ </h5>
                            <div class="smart-list">
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-wifi"></i></span> <b>COMM.</b> অপশনে প্রবেশ করুন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-cloud"></i></span> <b>Cloud Server / ADMS</b> অপশনটি নির্বাচন করুন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-toggle-on"></i></span> <b>Enable Domain:</b> OFF রাখুন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-globe"></i></span> <b>Server Address:</b> সফটওয়্যারে প্রদর্শিত IP দিন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-plug"></i></span> <b>Server Port:</b> সফটওয়্যারে প্রদর্শিত Port দিন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-lock"></i></span> <b>HTTPS:</b> Disable রাখুন</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section">
                            <h5><i class="fa fa-exclamation-triangle"></i> সমস্যা হলে করণীয়</h5>
                            <div class="smart-list">
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-wifi"></i></span> ডিভাইস এবং কম্পিউটার একই নেটওয়ার্ক (LAN/WiFi) এ সংযুক্ত আছে কিনা নিশ্চিত করুন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-globe"></i></span> নেটওয়ার্ক বা ইন্টারনেট সংযোগ সক্রিয় আছে কিনা যাচাই করুন </div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-key"></i></span> ডিভাইসের IP Address এবং Port সঠিকভাবে সেট করা আছে কিনা দেখুন</div>
                                <div class="smart-list-item"><span class="smart-icon"><i class="fa fa-shield"></i></span> সফটওয়্যারটি Administrator হিসেবে (Run as Administrator) চালু করুন</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="guideline-section mb-0">
                            <h5><i class="fa fa-desktop"></i> সমর্থিত প্ল্যাটফর্ম</h5>
                            <p class="mb-0"><i class="fab fa-windows"></i> Windows 8 অথবা তার পরবর্তী ভার্সন</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-2">
                    <small class="text-muted">&copy; 2026 ZKTeco ADMS Guideline</small>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
<script>
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('attendance_file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const uploadForm = document.getElementById('uploadForm');
    const progressWrapper = document.getElementById('progressWrapper');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');

    // highlight drop area
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropArea.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropArea.classList.remove('dragover');
        }, false);
    });

    // handle file select
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            fileInfo.style.display = 'block';
        }
    });

    // Form Submit with Fake Progress for UX
    uploadForm.onsubmit = function() {
        document.getElementById('submitBtn').disabled = true;
        progressWrapper.style.display = 'block';
        let width = 0;
        let interval = setInterval(() => {
            if (width >= 90) {
                clearInterval(interval);
            } else {
                width += 5;
                progressBar.style.width = width + '%';
                progressPercent.textContent = width + '%';
            }
        }, 100);
    };
</script>
@endpush





