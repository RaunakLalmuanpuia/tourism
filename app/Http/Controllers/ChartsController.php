<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// get total number of visitors of portal
class ChartController extends Controller
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
 
         Log::info($visitors);
 
         // Return the result as series
         return response()->json(['data' => $visitors]);
     }

     public function bookingsMonthly(Request $request)
     {
         $year = $request->query('year', date('Y')); // Get the year from the query parameters or use the current year if not provided
         
         // Query to get the total bookings month-wise for the specified year
         $bookings = DB::table('payments')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(DISTINCT orderId) as total_bookings'))
            ->where('status', 'TXN_SUCCESS')
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

            $bookings2 = DB::table('payments')
            ->select(DB::raw('COUNT(DISTINCT orderId) as total_bookings'))
            ->where('status', 'TXN_SUCCESS')
            ->whereYear('created_at', $year)
            ->get();
     
         Log::info($bookings);
     
         // Return the result as JSON
         return response()->json(['data' => $bookings]);
     }
     
     public function getUsersMonthly(Request $request)
     {
         $year = $request->query('year', date('Y')); // Get the year from the query parameters or use the current year if not provided
         
         // Query to get the total number of users month-wise for the specified year
         $users = DB::table('users')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(DISTINCT id) as total_users'))
            ->whereYear('created_at', $year)
            ->where('role', 'citizen')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

     
         // Return the result as JSON
         return response()->json(['data' => $users]);
     }

}
