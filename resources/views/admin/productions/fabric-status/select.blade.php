<div style="position: relative; flex-grow: 1;">
    <input type="text" id="pi_search_input"
           class="form-control"
           placeholder="Type Buyer or PI No..."
           autocomplete="off"
           value="{{ request('pi_text') }}">

    <!-- লুকানো ইনপুট আসল আইডি পাঠানোর জন্য -->
    <input type="hidden" name="pi_id" id="pi_id_hidden" value="{{ request('pi_id') }}">
    <input type="hidden" name="pi_text" id="pi_text_hidden" value="{{ request('pi_text') }}">

    <!-- সার্চ রেজাল্ট ড্রপডাউন -->
    <ul id="pi_search_results" class="list-group" style="position: absolute; width: 100%; z-index: 999; display: none; max-height: 200px; overflow-y: auto; box-shadow: 0px 4px 6px rgba(0,0,0,0.1);">
    </ul>
</div>
