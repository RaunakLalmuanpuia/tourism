<?php

namespace App\Http\Livewire;

use Throwable;
use App\Models\User;
use App\Models\Lodge;
use Livewire\Component;
use App\Models\LodgeRoomData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Models\UserBooking as ModelsUserBooking;

class UserBooking extends Component
{
  public $bookings;
  public $cancelBooking = false;
  public $bookingId;

  public function onCancelClicked($id)
  {
    $this->cancelBooking = true;
    $this->bookingId = $id;
  }

  public function onCancelled()
  {
    $booking = ModelsUserBooking::findOrFail($this->bookingId);
    if ($booking->status == "Confirmed" && $booking->param1 == "Booked") {
      $booking->cancelled_at = now();
      $booking->status = 'Cancelled';
      $booking->save();
      $this->cancelBooking = false;

      $this->sendSms($booking->users_id);
      $this->sendEmail($this->bookingId);

      session()->flash('flash.banner', 'Your booking has been cancelled.');
      session()->flash('flash.bannerStyle', 'success');

      return redirect(request()->header('Referer'));
    } else {
      session()->flash('flash.banner', 'Cancellation Period is over');
      session()->flash('flash.bannerStyle', 'danger');

      return redirect(request()->header('Referer'));
    }
  }

  public function sendSms($userId)
  {
    $user = User::findOrFail($userId);
    $templateId_user = '1407164844802974092';
    $templateId_lodge = '1407170669727078645';

    //cancellation sent to lodge
    try {
      $booking = ModelsUserBooking::findOrFail($this->bookingId);
      $lodge_room = LodgeRoomData::where('id', $booking->lodge_room_data_id)->first();
      $lodge = Lodge::where('id', $lodge_room->lodges_id)->first();

      Http::withHeaders([
        'Authorization' => "Bearer 11|Bzz867LIgOYkk8hVterz3KSsDY8Cmjg5FV7C2N7d",
      ])->get("https://sms.msegs.in/api/send-sms", [
        'template_id' => $templateId_lodge,
        'message' => "$lodge->name, Booking made by $user->name with order id: $booking->order_id has been cancelled. Please login to https://mizoramtourism.com for details. MZTOUR",
        'recipient' => $lodge->phone,
      ]);
    } catch (Throwable $ex) {
      Log::info("MZERROR:line-71->UserBooking sendSms():::" . $ex);
    }

    //cancellation sms sent to user
    try {
      Http::withHeaders([
        'Authorization' => "Bearer 11|Bzz867LIgOYkk8hVterz3KSsDY8Cmjg5FV7C2N7d",
      ])->get("https://sms.msegs.in/api/send-sms", [
        'template_id' => $templateId_user,
        'message' => "Dear $user->name , your Booking has been cancelled . please login to https://mizoramtourism.com for details",
        'recipient' => $user->phone
      ]);
    } catch (Throwable $ex) {
      Log::info("MZERROR:line-84->UserBooking sendSms():::" . $ex);
    }
  }

  public function sendEmail($bookingId)
  {
    try {
      $booking = ModelsUserBooking::findOrFail($bookingId);
      $user = User::findOrFail($booking->users_id);
      $room = LodgeRoomData::where('id', $booking->lodge_room_data_id)->with('lodgeRoomTypes')->first();
      $lodge = Lodge::where('id', $room->lodges_id)->with('users')->first();
      // Mail::send(
      //   'emails.booking-cancellation',
      //   ['user' => $user, 'booking' => $booking, 'room' => $room],
      //   function ($message) use ($lodge) {
      //       $message->to('mizoram.tourism2@gmail.com')
      //       ->subject('Booking Cancellation - '. $lodge->name);
      //     $message->from('noreply@gmail.com', 'Mizoram Tourism');
      //   }
      // );

      Mail::send(
        'emails.booking-cancellation',
        ['user' => $user, 'booking' => $booking, 'room' => $room],
        function ($message) use ($lodge) {
          $message->to($lodge->users->first()->email)
            ->subject('Booking Cancellation' . $lodge->name);
          $message->from('noreply@gmail.com', 'Mizoram Tourism');
        }
      );


    } catch (Throwable $ex) {
      Log::info("MZERROR:line-117->UserBooking sendEmail():::" . $ex);
    }
  }

  public function dialogCancel()
  {
    $this->cancelBooking = false;
  }

  public function render()
  {
    return view('livewire.user-booking');
  }
}
