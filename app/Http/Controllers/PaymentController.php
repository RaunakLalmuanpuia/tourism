<?php

namespace App\Http\Controllers;

use PDF;
use DateTime;
use Throwable;
use App\Models\User;
use App\Models\Guest;
use App\Models\Lodge;
use GuzzleHttp\Client;
use App\Models\Payment;
use App\Models\UserBooking;
use Illuminate\Http\Request;
use App\Models\LodgeRoomData;
use App\Models\LodgeRoomType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
  public function responseHandler(Request $request)
  {
    if (session()->exists('bookingData')) {
      session()->forget('bookingData');
    }

    $booking = UserBooking::where('order_id', $request->orderId)->first();

    if ($request->status == 'TXN_SUCCESS') {
      $this->onTransactionSuccess($request);
    } else if ($request->status == 'TXN_FAILURE') {
      $booking->status = 'Payment Failed';
      $booking->save();
    } else {
      $booking->status = '1';
      $booking->save();
    }

    return redirect()->route('booking.completed', ['status' => $request->status]);

    // if ($request->status == 'TXN_SUCCESS') {
    //   $this->onTransactionSuccess($request);
    //   session()->forget('bookingData');
    //   return redirect()->route('booking.completed', ['status' => $request->status]);
    // } else{
    //   $response = $this->recheckTransactionStatus($request->orderId)->response;

    //   // recheck transaction status whether it actually fails in case of pending
    //   $failureStatus = $response->body->resultInfo->resultStatus;

    //   if ($failureStatus == 'TXN_SUCCESS') {
    //     $this->onTransactionSuccess($request);
    //     session()->forget('bookingData');
    //     return redirect()->route('booking.completed', ['status' => $failureStatus]);
    //   } else {
    //     $booking = UserBooking::where('order_id', $request->orderId)->first();
    //     $booking->status = 'Payment Failed';
    //     $booking->save();
    //   }

    //   return redirect()->route('booking.completed', ['status' => $failureStatus]);
    // }
  }

  public function sendEmail($orderId)
  {
    try {
      $id = $orderId;
      $booking = UserBooking::where('order_id', $id)->first();
      $user = User::findOrFail($booking->users_id);
      $room = LodgeRoomData::where('id', $booking->lodge_room_data_id)->with('lodgeRoomTypes')->first();
      $lodge = Lodge::where('id', $room->lodges_id)->with('users')->first();
      Mail::send(
        'emails.new-booking',
        ['user' => $user, 'booking' => $booking, 'room' => $room],
        function ($message) use ($lodge) {
          $message->to($lodge->users->first()->email)
            ->subject('Booking Information');
          $message->from('noreply@gmail.com', 'Booking confirmation');
        }
      );
    } catch (Throwable $ex) {
      Log::info("MZERROR:line-emailConfirmationLodge->PaymentController sendEmail():::" . $ex);
    }
  }

  public function attachPdf($data, $lodgeRoom, $lodge, $lodgeRoomType, $guests, $noOfNights, $username)
  {
    $pdf = PDF::loadView('pdf.user-invoice', compact('data', 'lodgeRoom', 'lodge', 'lodgeRoomType', 'guests', 'noOfNights', 'username'));
    return $pdf;
  }

  public function getBookingCompleted(Request $request)
  {
    $status = $request->status;

    return view('front.payment-confirmation', compact('status'));
  }

  public function recheckTransactionStatus($orderId)
  {
    $client = new Client();
    $response = $client->request('POST', 'https://paymentgw.mizoram.gov.in/api/status', [
      'form_params' => [
        'order_id' => $orderId,
      ]
    ]);
    return json_decode($response->getBody());
  }

  public function onTransactionSuccess($request)
  {
    $templateId_user = '1407164844802974092';
    $templateId_lodge = '1407170833222683069';

    $payment = new Payment();
    $payment->transactionId = $request->transactionId;
    $payment->orderId = $request->orderId;
    $payment->currency = $request->currency;
    $payment->status = $request->status;
    $payment->amount = $request->amount;
    $payment->save();

    $booking = UserBooking::where('order_id', $request->orderId)->first();
    $booking->status = 'Confirmed';
    $booking->param1 = 'Booked';
    $booking->save();

    $user = User::findOrFail($booking->users_id);

    //sms booking confirmation alert for lodges
    try {
      $booking = UserBooking::findOrFail($booking->id);
      $lodge_room = LodgeRoomData::where('id', $booking->lodge_room_data_id)->first();
      $lodge = Lodge::where('id', $lodge_room->lodges_id)->first();

      Http::withHeaders([
        'Authorization' => "Bearer 11|Bzz867LIgOYkk8hVterz3KSsDY8Cmjg5FV7C2N7d",
      ])->get("https://sms.msegs.in/api/send-sms", [
        'template_id' => $templateId_lodge,
        'message' => "$lodge->name, Booking made by $user->name with order id: $booking->order_id has been confirmed. Please login to https://mizoramtourism.com for details. -MZTOUR",
        'recipient' => $lodge->phone,
      ]);
    } catch (Throwable $ex) {
      Log::info("MZERROR:line-smsConfirmationLodge->PaymentController onTransactionSuccess():::" . $ex);
    }

    //sms booking confirmation alert for users
    try {
      Http::withHeaders([
        'Authorization' => "Bearer 11|Bzz867LIgOYkk8hVterz3KSsDY8Cmjg5FV7C2N7d",
      ])->get("https://sms.msegs.in/api/send-sms", [
        'template_id' => $templateId_user,
        'message' => "Dear $user->name , your Booking has been confirmed . please login to https://mizoramtourism.com for details",
        'recipient' => $user->phone
      ]);
    } catch (Throwable $ex) {
      Log::info("MZERROR:line-smsConfirmationUsers->PaymentController onTransactionSucccess()::: " . $ex);
    }

    $room = LodgeRoomData::where('id', $booking->lodge_room_data_id)->with('lodgeRoomTypes')->first();
    $lodge = Lodge::where('id', $room->lodges_id)->first();

    try {
      $userBooking = UserBooking::where('order_id', $request->orderId)->first()->toArray();
      $lodgeRoomData = LodgeRoomData::where('id', $userBooking['lodge_room_data_id'])->first()->toArray();
      $lodgeRoomType = LodgeRoomType::where('id', $lodgeRoomData['lodge_room_types_id'])->first()->toArray();
      $lodgeBooked = Lodge::where('id', $lodgeRoomData['lodges_id'])->with('districtMetaData')->first()->toArray();
      $guests = Guest::where('booking_id', $userBooking['id'])->get();
      $date1 = new DateTime($userBooking['check_in']);
      $date2 = new DateTime($userBooking['check_out']);
      $interval = $date1->diff($date2);
      $noOfNights = (int) $interval->format('%a');
      $pdf = $this->attachPdf($userBooking, $lodgeRoomData, $lodgeBooked, $lodgeRoomType, $guests, $noOfNights, $user->name);
      Mail::send(
        'emails.confirmation-mail',
        ['name' => $user->name, 'booking' => $booking, 'room' => $room, 'lodge' => $lodge],
        function ($message) use ($user, $pdf, $booking) {
          $message->to($user->email)
            ->subject('Booking Confirmation')
            ->attachData($pdf->output(), $booking->order_id . '.pdf');
          $message->from('noreply@gmail.com', 'Booking confirmation');
        }
      );
    } catch (Throwable $ex) {
      Log::info("MZERROR:line-mailConfirmationUser->PaymentController onTransactionSucccess()::: " . $ex);
    }

    $this->sendEmail($booking->order_id); //send email to lodge
  }
}
