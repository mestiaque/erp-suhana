<?php

namespace App\Services;

class HrOptionsService
{
    public static function getOptions(): array
    {
        // Use Eloquent models directly for now; can be optimized/cached later
        $classifications = \App\Models\Attribute::where('type', 16)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name', 'bn_name']);
        $departments     = \App\Models\Attribute::where('type', 3)->where('status', '<>' ,'temp')->orderBy('name')->get(['id', 'name', 'bn_name']);
        $sections        = \App\Models\Attribute::where('type', 29)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name', 'bn_name']);
        $subSections     = \ME\Hr\Models\SubSection::orderBy('name')->get(['id', 'name', 'department_id', 'section_id', 'salary_type', 'approve_man_power', 'bn_name']);
        $designations    = \ME\Hr\Models\Designation::orderBy('name')->get(['id', 'name', 'bn_name']);
        $shifts          = \ME\Hr\Models\Shift::orderBy('name_of_shift')->get(['id', 'name_of_shift']);
        $workingPlaces   = \ME\Hr\Models\WorkingPlace::orderBy('name')->get(['id', 'name', 'bn_name']);
        $lines           = \App\Models\Attribute::where('type', 4)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name', 'bn_name', 'slug']);
        return [
            'classifications' => $classifications,
            'departments'     => $departments,
            'sections'        => $sections,
            'subSections'     => $subSections,
            'designations'    => $designations,
            'shifts'          => $shifts,
            'workingPlaces'   => $workingPlaces,
            'lines'           => $lines,
        ];
    }
}
