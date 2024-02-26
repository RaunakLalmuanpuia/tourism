<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChartsController extends Controller
{

    // get total number of visitors of portal
    public function visitorsMonthly()
    {
        // Query to get the total number of visitors month-wise for the year 2022
        $visitors = DB::table('ip_check_ins')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(DISTINCT ip_address) as total_visitors'))
            ->whereYear('created_at', 2022)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();


        // Prepare the result as series
        // $series = [];
        // foreach ($visitors as $visitor) {
        //     $series[] = [
        //         'name' => $this->getMonthName($visitor->month), // Convert month number to name
        //         'data' => $visitor->total_visitors
        //     ];
        // }

        // Return the result as series
        // Log the data before returning
        Log::info($visitors);

        // Return the result as series
        return response()->json(['data' => $visitors]);
    }
    public function newUserRegistered()
    {
    }
}
