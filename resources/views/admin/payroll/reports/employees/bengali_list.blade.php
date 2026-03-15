@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Bengali Employee List') }}</title>
@endsection

@push('css')
<style>
    .bengali-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .bengali-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
    table.bengali-table { font-family: 'SutonnyMJ', 'Nikosh', 'SolaimanLipi', Arial, sans-serif; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Bengali Employee List</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Reports</li>
        <li class="item">Bengali List</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="bengali-header">
        <h2>কর্মচারী তালিকা</h2>
        <p>Employee List in Bengali Format</p>
    </div>

    <div class="bengali-card">
        <form action="{{ route('admin.reports.employees.bengaliList') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label>Department / বিভাগ</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments / সকল বিভাগ</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Status / অবস্থা</label>
                <select name="status" class="form-control">
                    <option value="">All / সকল</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active / সক্রিয়</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive / নিষ্ক্রিয়</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover bengali-table">
                <thead class="table-success">
                    <tr>
                        <th>ক্রমিক</th>
                        <th>কর্মচারী আইডি</th>
                        <th>নাম (বাংলা)</th>
                        <th>বিভাগ</th>
                        <th>পদবী</th>
                        <th>যোগদানের তারিখ</th>
                        <th>মোবাইল</th>
                        <th>অবস্থা</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees ?? [] as $i => $emp)
                    <tr>
                        <td>{{ $this->banglaNumber($i + 1) }}</td>
                        <td>{{ $emp->employee_id ?? 'N/A' }}</td>
                        <td>{{ $emp->bangla_name ?? $emp->name }}</td>
                        <td>{{ $emp->department->name ?? 'N/A' }}</td>
                        <td>{{ $emp->designation ?? 'N/A' }}</td>
                        <td>
                            @if($emp->joining_date)
                            {{ $this->banglDate($emp->joining_date) }}
                            @else
                            N/A
                            @endif
                        </td>
                        <td>{{ $this->banglaNumber($emp->phone) }}</td>
                        <td>
                            @if($emp->employee_status == 'active')
                            <span class="badge bg-success">সক্রিয়</span>
                            @else
                            <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">কোন কর্মচারী পাওয়া যায়নি</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="text-end">
                            <strong>মোট: {{ $this->banglaNumber(count($employees ?? [])) }} জন কর্মচারী</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-end mt-3">
            <button onclick="window.print()" class="btn btn-secondary"><i class="bx bx-printer"></i> Print / মুদ্রণ</button>
        </div>
    </div>

</div>

@push('js')
<script>
    // Helper functions for Blade (these would be in controller or helper)
    // banglaNumber conversion
    function banglaNumber(num) {
        const banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return String(num).split('').map(digit => banglaDigits[digit] || digit).join('');
    }

    // banglDate conversion (simplified)
    function banglDate(date) {
        // This would be handled in controller with proper Bengali date formatting
        return date;
    }
</script>
@endpush

@endsection
