<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Models\User;
use App\Models\Lodge;
use Carbon\CarbonPeriod;
use App\Models\BookedDates;
use App\Models\UserBooking;
use Illuminate\Http\Request;
use App\Models\LodgeRoomData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\UserBooking as ModelsUserBooking;

class BookingsController extends Controller
{
  public function getMyBookings(Request $request)
  {
    $bookings = UserBooking::where('users_id', $request->user_id)->whereIn('status', ['Confirmed', 'Cancelled'])->with(['lodgeRoomData'])->get();
    return response()->json(['bookings' => $bookings]);
  }

  public function checkDate(Request $request)
  {
    $roomsAvailable = LodgeRoomData::where('lodges_id', $request->lodge_id)->where('lodge_room_types_id', $request->room_type_id)->first();

    $period = CarbonPeriod::create($request->checkIn, date('Y-m-d', strtotime('-1 day', strtotime($request->checkOut))));
    $dates = [];

    foreach ($period as $date) {
      array_push($dates, $date->format('Y-m-d'));
    }

    $bookedDates = BookedDates::whereIn('booked', $dates)
      ->get()
      ->pluck('user_booking_id');

    $bookedRooms = UserBooking::whereIn('id', $bookedDates->unique())->where('status', 'Confirmed')->where('param1', '!=', 'Out')->where('lodge_room_data_id', $request->lodge_room_data_id)->sum('number_of_room_require');

    $noOfRoomsAvailable = ($roomsAvailable->room_available - $bookedRooms <= 0 ? 0 : $roomsAvailable->room_available - $bookedRooms);

    return response()->json(['no_of_rooms_available' => $noOfRoomsAvailable]);
  }

  public function cancelBooking(Request $request)
  {
    $templateId = '1407164844802974092';
    $booking = UserBooking::findOrFail($request->booking_id);
    $booking->cancelled_at = now();
    $booking->status = 'Cancelled';
    $booking->save();

    $user = User::findOrFail($booking->users_id);

    try {
      Http::withHeaders([
        'Authorization' => "Bearer 11|Bzz867LIgOYkk8hVterz3KSsDY8Cmjg5FV7C2N7d",
      ])->get("https://sms.msegs.in/api/send-sms", [
        'template_id' => $templateId,
        'message' => "Dear $user->name , your Booking has been cancelled . please login to https://mizoramtourism.com for details",
        'recipient' => $user->phone
      ]);
    } catch (Throwable $ex) {
      Log::info("MZERROR:line-88->BookingsController cancelBooking():::" . $ex);
    }

    return response()->json(['success' => 'Booking has been cancelled']);
  }
}
