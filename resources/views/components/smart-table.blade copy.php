@props([
    'headers' => [],
    'rows' => [],
    'id' => 'smartTable'.rand(1000,9999)
])

<style>
#{{ $id }}-wrapper{
    max-height:600px;
    overflow:auto;
    border:1px solid #ddd;
}

#{{ $id }}{
    width:100%;
    border-collapse:collapse;
}

#{{ $id }} th,
#{{ $id }} td{
    border:1px solid #ddd;
    padding:6px 10px;
    white-space:nowrap;
    background:#fff;
}

#{{ $id }} thead th{
    position:sticky;
    top:0;
    background:#5f6d7a;
    color:#fff;
    z-index:30;
}

#{{ $id }} .freeze-col{
    position:sticky;
    left:0;
    background:#fff;
    z-index:20;
}

#{{ $id }} thead .freeze-col{
    background:#45525d;
    z-index:50;
}

#{{ $id }} tbody tr:hover{
    background:#f8f9fa;
}

.smart-action-btn{
    background:none;
    border:none;
    cursor:pointer;
    font-size:16px;
}
.smart-action-btn:hover{
    color:#007bff;
}
</style>

<div id="{{ $id }}-wrapper">
<table id="{{ $id }}">
<thead>
<tr>
<th class="freeze-col text-center" width="80">SL</th>
@foreach($headers as $h)
<th @if(!empty($h['freeze'])) class="freeze-col" @endif>
    {{ $h['label'] }}
</th>
@endforeach
</tr>
</thead>
<tbody>

@forelse($rows as $loopIndex => $row)
<tr>
<td class="freeze-col text-center">
<div style="display:flex;gap:6px;justify-content:center;align-items:center;">

<span>{{ $row['sl'] }}</span> {{-- Only one SL display --}}

@if(!empty($row['actions']))
<button type="button"
        class="smart-action-btn"
        data-toggle="modal"
        data-target="#{{ $id }}-actionModal-{{ $loopIndex }}">
    <i class="fa fa-ellipsis-v"></i>
</button>
@endif

</div>
</td>

@foreach($headers as $h)
<td @if(!empty($h['freeze'])) class="freeze-col" @endif>
    {!! data_get($row, $h['key']) ?? '--' !!}
</td>
@endforeach
</tr>
@empty
<tr>
    <td colspan="100%" class="text-center text-muted">No Data Found</td>
</tr>
@endforelse

</tbody>
</table>
</div>

{{-- Modal Outside Table Wrapper --}}
@foreach($rows as $loopIndex => $row)
@if(!empty($row['actions']))
<div class="modal fade" id="{{ $id }}-actionModal-{{ $loopIndex }}" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actions</h5>
                <button type="button" class="btn-close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                {!! $row['actions'] !!}
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
