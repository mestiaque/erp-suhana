@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Offday Settings') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Weekly Offday Settings</h5>
        </div>
        <div class="card-body">
            <form action="{{route('admin.offday.update')}}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="offday">Select Weekly Off Day</label>
                    <select id="offday" name="offday" class="form-control" required>
                        <option value="0" {{ $offday && $offday->name == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                        <option value="1" {{ $offday && $offday->name == 'Monday' ? 'selected' : '' }}>Monday</option>
                        <option value="2" {{ $offday && $offday->name == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                        <option value="3" {{ $offday && $offday->name == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                        <option value="4" {{ $offday && $offday->name == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                        <option value="5" {{ (!$offday || $offday->name == 'Friday') ? 'selected' : '' }}>Friday</option>
                        <option value="6" {{ $offday && $offday->name == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                    </select>
                    <small class="text-muted">This day will be automatically marked as weekly off in attendance</small>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" {{ $offday && $offday->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $offday && $offday->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Settings
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
