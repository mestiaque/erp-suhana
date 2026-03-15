@foreach($invoice->items()->whereHas('piOrder')->limit(2)->get() as $itm)
{{$itm->piOrder->invoice}},
@endforeach
<br><span class="badge badge-info">{{$invoice->items()->count()}} PI No</span>