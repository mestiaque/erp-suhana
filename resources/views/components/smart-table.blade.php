@props([
    'headers' => [],
    'rows' => [],
    'actions' => true,
    'id' => 'smartTable'.rand(1000,9999)
])

<style>
#{{ $id }}-wrapper{
    max-height:calc(58vh);
    overflow:auto;
    cursor:grab;
    border:1px solid #ddd;
    position:relative;
}
#{{ $id }}-wrapper.active{ cursor:grabbing; }
#{{ $id }}{ width:100%; border-collapse:collapse; }
#{{ $id }} th, #{{ $id }} td{ border:1px solid #ddd; padding:6px 10px; white-space:nowrap; background:#fff; }
#{{ $id }} thead th{ position:sticky; top:0; background:#5f6d7a; color:#fff; z-index:30; }
#{{ $id }} .freeze-col{ position:sticky; left:0; background:#fff; z-index:20; }
#{{ $id }} thead .freeze-col{ background:#45525d; z-index:50; }
#{{ $id }} tbody tr:hover{ background:#f8f9fa; }
.smart-action{ border:none; background:none; cursor:pointer; font-size:16px; }
.smart-action:hover{ color:#007bff; }
</style>

<div id="{{ $id }}-wrapper">
    <table id="{{ $id }}">
        <thead>
            <tr>
                {{-- @if($actions)<th class="freeze-col text-center">SL</th>@endif --}}
                @if($actions)<th class="freeze-col text-center" style="width:30px;" data-left="0">SL</th>@endif
                @foreach($headers as $h)
                    {{-- <th @if(!empty($h['freeze'])) class="freeze-col" @endif>{{ $h['label'] }}</th> --}}
                    <th
                        @if(!empty($h['freeze'])) class="freeze-col" @endif
                        @if(!empty($h['width'])) style="width:{{ $h['width'] }};" @endif
                        @if(!empty($h['left'])) data-left="{{ $h['left'] }}" @endif
                    >
                        {{ $h['label'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                <tr>
                    @if($actions)
                        <td class="freeze-col text-center" style="width:30px;" data-left="0">
                            <div style="display:flex;gap:6px;align-items:center;justify-content:center;">
                                <span>{{ $row['sl'] ?? $i+1 }}</span>
                                @if(!empty($row['actions']))
                                <button class="smart-action" data-actions='@json($row["actions"])'>
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    @endif

                    @foreach($headers as $h)
                    {{-- <td @if(!empty($h['freeze'])) class="freeze-col" @endif>
                        {!! data_get($row,$h['key']) ?? '--' !!}
                    </td> --}}
                        <td
                            @if(!empty($h['freeze'])) class="freeze-col" @endif
                            @if(!empty($h['width'])) style="width:{{ $h['width'] }};" @endif
                            @if(!empty($h['left'])) data-left="{{ $h['left'] }}" @endif
                        >
                            {!! data_get($row,$h['key']) ?? '--' !!}
                        </td>
                    @endforeach

                    @if($actions && empty($row['actions']))<td></td>@endif
                </tr>
            @empty
                <tr>
                    <td colspan="100%" class="text-center text-muted">No Data Found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- =========================
   SINGLE DYNAMIC MODAL
========================= --}}
@if($actions)
<div class="modal fade" id="{{ $id }}-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            {{-- Modal Header --}}
            {{-- <div class="modal-header bg-primary text-white py-2 px-3">
                <h5 class="modal-title mb-0">Actions</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div> --}}

            {{-- Modal Body --}}
            <div class="modal-body p-2 text-center">
                <div class="list-group list-group-flush" id="{{ $id }}-actionList">
                    {{-- Actions will be appended here dynamically --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endif

<script>
(function(){
    /* =========================
       Freeze Column
    ========================= */
    function calcFreeze_{{ $id }}(){
        let table = document.getElementById('{{ $id }}');
        if(!table) return;

        // --- SL column width (if exists) ---
        let slCol = table.querySelector('thead th.freeze-col.text-center');
        let left = 0;
        if(slCol){
            slCol.style.left = '0px';       // always 0
            slCol.style.width = '30px';     // fixed width
            left = slCol.offsetWidth;       // start left after SL
        }

        // --- Calculate thead lefts for other frozen columns ---
        table.querySelectorAll('thead .freeze-col').forEach(th=>{
            if(th === slCol) return; // skip SL

            let customLeft = th.dataset.left;
            if(customLeft){
                th.style.left = customLeft;
                left = parseInt(customLeft) + th.offsetWidth;
            } else {
                th.style.left = left + 'px';
                left += th.offsetWidth;
            }
        });

        // --- Calculate tbody lefts ---
        table.querySelectorAll('tbody tr').forEach(tr=>{
            let l = slCol ? slCol.offsetWidth : 0; // start after SL
            tr.querySelectorAll('.freeze-col').forEach(td=>{
                if(slCol && td === tr.querySelector('td.freeze-col.text-center')) return; // skip SL

                let customLeft = td.dataset.left;
                if(customLeft){
                    td.style.left = customLeft;
                    l = parseInt(customLeft) + td.offsetWidth;
                } else {
                    td.style.left = l + 'px';
                    l += td.offsetWidth;
                }
            });
        });
    }

    function calcFreezeV2_{{ $id }}(){
        let table = document.getElementById('{{ $id }}');
        if(!table) return;

        // ১. প্রথমে হেডার ক্যালকুলেট করা
        let leftOffset = 0;
        let headerCols = table.querySelectorAll('thead th.freeze-col');

        headerCols.forEach(th => {
            th.style.left = leftOffset + 'px';
            leftOffset += th.getBoundingClientRect().width;
        });

        // ২. এবার বডির প্রতিটি রো এর জন্য একই অফসেট সেট করা
        table.querySelectorAll('tbody tr').forEach(tr => {
            let bodyLeftOffset = 0;
            let bodyCols = tr.querySelectorAll('td.freeze-col');

            bodyCols.forEach((td, index) => {
                // হেডারের উইডথ অনুযায়ী বডির লেফট সেট করা যাতে এলাইনমেন্ট ঠিক থাকে
                if(headerCols[index]) {
                    td.style.left = headerCols[index].style.left;
                }
            });
        });
    }

    setTimeout(calcFreeze_{{ $id }},150);
    window.addEventListener('resize',calcFreeze_{{ $id }});

    /* =========================
       Drag Scroll
    ========================= */
    let wrapper=document.getElementById('{{ $id }}-wrapper');
    if(wrapper){
        let isDown=false, startX, scrollLeft;
        wrapper.addEventListener('mousedown', e => { isDown=true; wrapper.classList.add('active'); startX=e.pageX-wrapper.offsetLeft; scrollLeft=wrapper.scrollLeft; });
        wrapper.addEventListener('mouseup',()=>isDown=false);
        wrapper.addEventListener('mouseleave',()=>isDown=false);
        wrapper.addEventListener('mousemove', e => { if(!isDown) return; e.preventDefault(); let x=e.pageX-wrapper.offsetLeft; wrapper.scrollLeft=scrollLeft-(x-startX)*1.2; });
    }

    /* =========================
       Dynamic Actions Modal
    ========================= */
    let modalEl=document.getElementById('{{ $id }}-modal');
    let actionList=document.getElementById('{{ $id }}-actionList');

    document.querySelectorAll('#{{ $id }} .smart-action').forEach(btn=>{
        btn.addEventListener('click', function(){
            let actions=JSON.parse(this.dataset.actions || '[]');
            actionList.innerHTML=''; // clear old

            actions.forEach(a=>{
                // can check server-side before passing a
                const el=document.createElement(a.tag || 'a');
                if(a.tag==='a'){ el.href=a.href || '#'; }
                // if(a.tag==='a'){ el.href=a.href || '#'; el.target='blank'; }
                el.className='list-group-item list-group-item-action';
                el.innerHTML=a.label;
                actionList.appendChild(el);
            });

            // Show modal (Bootstrap 5)
            if(window.bootstrap){
                let modal=new bootstrap.Modal(modalEl); modal.show();
            }
        });
    });
})();
</script>
