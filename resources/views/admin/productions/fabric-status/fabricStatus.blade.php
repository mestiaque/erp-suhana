@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('PI Wise Fabric Status') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>PI Wise Fabric Status</h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- Search / Filter Form --}}
            <div class="row mb-3">
                <div class="col-md-6 mb-1">
                    <form method="GET" action="{{ route('admin.piWiseFabricStatus') }}" id="fabricStatusForm">
                        <div class="input-group">
                            {{-- Searchable Select Partial --}}
                            @include (adminTheme() . 'productions.fabric-status.select')

                            {{-- Load --}}
                            <button type="submit" class="btn btn-success btn-sm rounded-0 mr-2">
                                <i class="fa-solid fa-magnifying-glass"></i> Load
                            </button>

                            {{-- Reset --}}
                            <a href="{{ route('admin.piWiseFabricStatus') }}" class="btn btn-warning rounded-0 text-white"><i class="fa-solid fa-rotate-left"></i> RESET</a>
                            {{-- Print --}}

                        </div>
                    </form>
                </div>
                <div class="col-md-2 offset-4 text-right">
                    @if(isset($pi))
                        <a href="{{ route('admin.piWiseFabricStatus', [
                                'pi_id'   => $pi->id,
                                'pi_text' => request('pi_text'),
                                'print'   => true
                            ]) }}"
                            class="btn btn-primary rounded-0">
                            <i class="fa-solid fa-print"></i> PRINT
                        </a>
                    @endif
                </div>
            </div>


            <hr>

            {{-- ডাটা লোড হওয়ার পর এখানে দেখাবে --}}
            @if(isset($pi))
                <div class="mt-4">
                    {{-- যদি আলাদা ব্লেড ফাইল থাকে তবে সেটি include করতে পারেন --}}
                    {{-- অথবা এখানে সরাসরি স্ট্যাটাস কার্ড/টেবিল দেখাতে পারেন --}}
                    <div class="table-responsive" id="dataTable">
                        @include(adminTheme().'productions.fabric-status.table')
                    </div>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <p>Please search and select a PI to view fabric status.</p>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    let searchInput = $('#pi_search_input');
    let resultsList = $('#pi_search_results');
    let hiddenId = $('#pi_id_hidden');
    let hiddenText = $('#pi_text_hidden');

    // ডাটা নিয়ে আসার কমন ফাংশন
    function loadPIs(query = '') {
        $.ajax({
            url: "{{ route('admin.piWiseFabricStatus') }}",
            method: "GET",
            data: { search: query },
            success: function(data) {
                resultsList.empty().show();
                if(data.length > 0) {
                    data.forEach(function(pi) {
                        resultsList.append(`<li class="list-group-item list-group-item-action pi-item"
                            data-id="${pi.id}"
                            data-text="${pi.pi_no} (${pi.buyer_name})">
                            <strong>${pi.pi_no}</strong> - <small>${pi.buyer_name}</small>
                        </li>`);
                    });
                } else {
                    resultsList.append('<li class="list-group-item text-danger">No PI Found</li>');
                }
            }
        });
    }

    // ইনপুটে ক্লিক বা ফোকাস করলে (ডিফল্ট ১০টি দেখাবে)
    searchInput.on('focus click', function() {
        let query = $(this).val();
        // যদি ইনপুট খালি থাকে অথবা টেক্সট থাকে, দুই ক্ষেত্রেই ডাটা লোড হবে
        loadPIs(query);
    });

    // টাইপ করলে সার্চ হবে
    searchInput.on('keyup', function() {
        let query = $(this).val();
        // সার্চ কিউরি খালি হলেও loadPIs কল হবে যাতে ডিফল্ট ১০টি ফিরে আসে
        loadPIs(query);
    });

    // লিস্ট থেকে আইটেম সিলেক্ট করলে
    $(document).on('click', '.pi-item', function() {
        let id = $(this).data('id');
        let text = $(this).data('text');

        hiddenId.val(id);
        hiddenText.val(text);
        searchInput.val(text);
        resultsList.hide();
    });

    // ড্রপডাউনের বাইরে ক্লিক করলে সেটি বন্ধ হয়ে যাবে
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#pi_search_input, #pi_search_results').length) {
            resultsList.hide();
        }
    });
});
</script>
@endpush


