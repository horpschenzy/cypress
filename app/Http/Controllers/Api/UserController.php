<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function userActivities()
    {
        $dateRange =  request()->query('daterange');
        $explodeDateRange = explode("-", $dateRange);
        $startRange = isset($explodeDateRange[0]) ?? '';
        if ($startRange) {
            $startRange = $explodeDateRange[0];
            $startRange = Carbon::createFromFormat('d/m/Y', $startRange)->format('Y-m-d');
        }

        $endRange = isset($explodeDateRange[1]) ?? '';
        if ($endRange) {
            $endRange = $explodeDateRange[1];
            $endRange = Carbon::createFromFormat('d/m/Y', $endRange)->format('Y-m-d');
        }
        $user = User::withWhereHas('activities', function ($query) use ($startRange, $endRange) {
                            if ($startRange && $endRange) {
                                $query->whereDate('start_date', '>=', $startRange)
                                ->whereDate('start_date',   '<=', $endRange);
                            } elseif ($startRange) {
                                $query->whereDate('start_date', '=', $startRange);
                            } elseif ($endRange) {
                                $query->whereDate('start_date', '>=', $endRange);
                            }
                            $query->orderBy('start_date');
                    })->where('id', auth()->id())->first();
        if ($user->activities) {
            return response()->json(['status' => true, 'data' => $user->activities], 200);
        } 
        return response()->json(['status' => false, 'message' => 'No activities found'], 200);
        
    }
}
