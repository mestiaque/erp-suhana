<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterPlanning extends Model
{
    use HasFactory;

    protected $table = 'master_planning';

    protected $guarded = [];

    // relation to production planning rows
    public function productions()
    {
        return $this->hasMany(ProductionPlanning::class, 'master_plan_id', 'id');
    }


    // creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // updater
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // approver
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
