@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{ websiteTitle('Shifts List') }}</title>
@endsection 

@push('css')
<style type="text/css"></style>
@endpush 

@section('contents')
<div class="flex-grow-1">
    <!-- Start -->
    <div class="card mb-30">
        <!--<div class="card-header d-flex justify-content-between align-items-center">-->
        <!--    <h3>Shifts List</h3>-->
        <!--    <div class="dropdown">-->
        <!--        <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddShift" style="padding:5px 15px;">-->
        <!--            <i class="bx bx-plus"></i> Shift-->
        <!--        </a>-->
        <!--        <a href="{{ route('admin.shifts') }}" class="btn-custom yellow">-->
        <!--            <i class="bx bx-rotate-left"></i>-->
        <!--        </a>-->
        <!--    </div>-->
        <!--</div>-->

        <div class="card-body">
            @include(adminTheme().'alerts')

            <div class=" mt-5">
                <form action="{{ isset($shift) ? route('admin.shiftsAction', ['update', $shift->id]) : route('admin.shiftsAction', 'store') }}" method="POST">
                    @csrf
                    @if(isset($shift))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <!-- Left Panel: Form Fields -->
                        <div class="col-md-9">
                            <div class="card p-4">

                                <!-- Shift Names -->
                                <div class="row mb-3">
                                    <label for="name_of_shift" class="col-sm-3 col-form-label">Name of Shift</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="name_of_shift" name="name_of_shift" value="{{ old('name_of_shift', $shift->name_of_shift ?? '') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="name_of_shift_bn" class="col-sm-3 col-form-label">Name of Shift (বাংলা)</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="name_of_shift_bn" name="name_of_shift_bn" value="{{ old('name_of_shift_bn', $shift->name_of_shift_bn ?? '') }}">
                                    </div>
                                </div>

                                <!-- Shift Timing Fields -->
                                <div class="row mb-3 align-items-center">
                                    <label for="shift_starting_time" class="col-sm-3 col-form-label">Shift Starting Time</label>
                                    <div class="col-sm-4">
                                        <input type="time" class="form-control" id="shift_starting_time" name="shift_starting_time" value="{{ old('shift_starting_time', $shift->shift_starting_time ?? '') }}">
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <label for="red_marking_on" class="col-sm-3 col-form-label">Red Marking On</label>
                                    <div class="col-sm-4">
                                        <input type="time" class="form-control" id="red_marking_on" name="red_marking_on" value="{{ old('red_marking_on', $shift->red_marking_on ?? '') }}">
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <label for="shift_closing_time" class="col-sm-3 col-form-label">Shift Closing Time</label>
                                    <div class="col-sm-4">
                                        <input type="time" class="form-control" id="shift_closing_time" name="shift_closing_time" value="{{ old('shift_closing_time', $shift->shift_closing_time ?? '') }}">
                                    </div>
                                    <div class="col-sm-auto form-check">
                                        <input type="checkbox" class="form-check-input" value="1" id="shift_closing_time_next_day" name="shift_closing_time_next_day" {{ old('shift_closing_time_next_day', $shift->shift_closing_time_next_day ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shift_closing_time_next_day">Next Day?</label>
                                    </div>
                                </div>

                                <!-- Over Time Fields -->
                                <div class="row mb-3 align-items-center">
                                    <label for="over_time_allowed_up_to" class="col-sm-3 col-form-label">Over Time Allowed Up-to</label>
                                    <div class="col-sm-4">
                                        <input type="time" class="form-control" id="over_time_allowed_up_to" name="over_time_allowed_up_to" value="{{ old('over_time_allowed_up_to', $shift->over_time_allowed_up_to ?? '') }}">
                                    </div>
                                    <div class="col-sm-auto form-check">
                                        <input type="checkbox" class="form-check-input" value="1" id="over_time_allowed_up_to_next_day" name="over_time_allowed_up_to_next_day" {{ old('over_time_allowed_up_to_next_day', $shift->over_time_allowed_up_to_next_day ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="over_time_allowed_up_to_next_day">Next Day?</label>
                                    </div>
                                </div>
                                <div class="row mb-3 align-items-center">
                                    <label for="over_time_1_allowed_up_to" class="col-sm-3 col-form-label">Over Time 1 Allowed Up-to</label>
                                    <div class="col-sm-4">
                                        <input type="time" class="form-control" id="over_time_1_allowed_up_to" name="over_time_1_allowed_up_to" value="{{ old('over_time_1_allowed_up_to', $shift->over_time_1_allowed_up_to ?? '') }}">
                                    </div>
                                    <div class="col-sm-auto form-check">
                                        <input type="checkbox" class="form-check-input" value="1" id="over_time_1_allowed_up_to_next_day" name="over_time_1_allowed_up_to_next_day" {{ old('over_time_1_allowed_up_to_next_day', $shift->over_time_1_allowed_up_to_next_day ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="over_time_1_allowed_up_to_next_day">Next Day?</label>
                                    </div>
                                </div>

                                <!-- Card Accept Fields -->
                                <div class="row mb-3 align-items-center">
                                    <label for="card_accept_from" class="col-sm-3 col-form-label">Card Accept From</label>
                                    <div class="col-sm-4">
                                        <input type="time" class="form-control" id="card_accept_from" name="card_accept_from" value="{{ old('card_accept_from', $shift->card_accept_from ?? '') }}">
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <label for="card_accept_to" class="col-sm-3 col-form-label">Card Accept To</label>
                                    <div class="col-sm-4">
                                        <input type="time" class="form-control" id="card_accept_to" name="card_accept_to" value="{{ old('card_accept_to', $shift->card_accept_to ?? '') }}">
                                    </div>
                                    <div class="col-sm-auto form-check">
                                        <input type="checkbox" class="form-check-input" value="1" id="card_accept_to_next_day" name="card_accept_to_next_day" {{ old('card_accept_to_next_day', $shift->card_accept_to_next_day ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="card_accept_to_next_day">Next Day?</label>
                                    </div>
                                </div>

                                <!-- Meal Option -->
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Meal Option</label>
                                    <div class="col-sm-9">
                                        @foreach(['NA','Lunch','30 Min Lunch','1 Hr Lunch & 30 Min Iftar','30 Min Lunch & 1 Hr Dinner'] as $option)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="meal_option" id="meal_option_{{ str_replace(' ','_',$option) }}" value="{{ $option }}" {{ old('meal_option', $shift->meal_option ?? '') == $option ? 'checked' : '' }}>
                                                <label class="form-check-label" for="meal_option_{{ str_replace(' ','_',$option) }}">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Allowances & Checkboxes -->
                                <div class="row mb-3 align-items-center">
                                    <label for="tiffin_allowance" class="col-sm-3 col-form-label">Tiffin Allowance</label>
                                    <div class="col-sm-4">
                                        <input type="number" step="0.01" class="form-control" id="tiffin_allowance" name="tiffin_allowance" value="{{ old('tiffin_allowance', $shift->tiffin_allowance ?? '0.00') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-9 offset-sm-3">
                                        <div class="form-check">
                                            <input class="form-check-input" value="1" type="checkbox" id="no_lunch_hour_holiday" name="no_lunch_hour_holiday" {{ old('no_lunch_hour_holiday', $shift->no_lunch_hour_holiday ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="no_lunch_hour_holiday">No Lunch Hour for Holiday?</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="dinner_allowance" name="dinner_allowance" {{ old('dinner_allowance', $shift->dinner_allowance ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="dinner_allowance">Dinner Allowance?</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" value="1" type="checkbox" id="double_shift" name="double_shift" {{ old('double_shift', $shift->double_shift ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="double_shift">Double Shift?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Weekly OT & Actions -->
                        <div class="col-md-3">
                            <div class="card p-3">

                                <label for="weekly_overtime_allowed" class="form-label">Weekly Overtime Allowed</label>
                                <input type="time" class="form-control mb-2" id="weekly_overtime_allowed" name="weekly_overtime_allowed" value="{{ old('weekly_overtime_allowed', $shift->weekly_overtime_allowed ?? '') }}">

                                @foreach(['Sat','Sun','Mon','Tue','Wed','Thu'] as $day)
                                    <label for="weekly_ot_{{ strtolower($day) }}" class="form-label mt-2">{{ $day }}</label>
                                    <input type="time" class="form-control" id="weekly_ot_{{ strtolower($day) }}" name="weekly_ot_{{ strtolower($day) }}" value="{{ old('weekly_ot_' . strtolower($day), $shift->{'weekly_ot_' . strtolower($day)} ?? '') }}">
                                @endforeach

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('admin.shifts') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-success">{{ isset($shift) ? 'Save Changes' : 'Create Shift' }}</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
