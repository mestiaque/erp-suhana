<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Media;
use App\Models\Order;
use App\Models\Sample;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\OrderItem;
use App\Models\SampleItem;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ProductionController extends Controller
{
    public function productionPlanning(Request $r)
    {
        $totals = Sample::whereNotIn('status', ['trash', 'temp'])
            ->selectRaw("COUNT(*) AS total")
            ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending")
            ->selectRaw("COUNT(CASE WHEN status = 'confirmed' THEN 1 END) AS confirmed")
            ->selectRaw("COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed")
            ->selectRaw("COUNT(CASE WHEN status = 'cancel' THEN 1 END) AS cancel")
            ->first();


        return view(adminTheme().'productions.planning.index',compact('totals'));
    }




}
