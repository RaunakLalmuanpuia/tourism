<?php

namespace App\Http\Livewire\Front;

use App\Models\Guest;
use GuzzleHttp\Client;
use Livewire\Component;
use App\Models\UserBooking;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class BookingInformation extends Component
{
  public $addGuestDialog = false;
  public $below12Years = false;
  public $newGuestName;
  public $bookingData;
  public $specialRequest;
  public $suggestion;
  public $errorPopupMessage = false;

  public $guests = [];

  public function mount()
  {
    $guest = [
      'name' => Auth::user()->name,
      'email' => Auth::user()->email,
      'phone' => Auth::user()->phone,
      'below12' => false,
    ];
    array_push($this->guests, $guest);
  }

  public function addNewGuest($name, bool $below12)
  {
    if ($name != '') {
      $guest = [
        'name' => $name,
        'below12' => $below12 == 1 ? true : false
      ];
      array_push($this->guests, $guest);
      $this->addGuestDialog = false;
      $this->newGuestName = '';
      $this->below12Years = false;
      $this->emit('guestAdded', ['guests' => $this->guests]);
    }
  }

  public function below12()
  {
    $this->below12Years = !$this->below12Years;
  }

  public function addGuest()
  {
    $this->addGuestDialog = true;
  }

  public function removeGuest($index)
  {
    unset($this->guests[$index]);
    $this->guests = array_values($this->guests);
    $this->emit('guestRemoved', ['guests' => $this->guests]);
  }

  public function makePayment()
  {
    //callback url when paytm is done

    $callbackUrl = URL::to('/') . '/response-handler';
    if (Auth::user()->id == 304) {
      $amount = 1;
    } else {
      $amount = $this->bookingData['amount'];
    }

    //proceed for paymentF

    try {
      $client = new Client();
      $url = env('APP_ENV') == 'local' ? 'https://paymentgw.mizoram.gov.in/api/initiate-test-payment' : 'https://paymentgw.mizoram.gov.in/api/initiate-payment';
      // $url = 'https://paymentgw.mizoram.gov.in/api/initiate-test-payment';
      $response = $client->request('POST', $url, [
        'form_params' => [
          'callback_url' => $callbackUrl,
          'order_id' => $this->bookingData['orderId'],
          'amount' => $amount,
          'department_id' => 3,
          'customer' => json_encode('MZTOUR: ' . $this->bookingData['lodge']['name']),
          'shouldSplit' => true,
          'splitMethod' => 'PERCENTAGE',
          'splitMid' => 'ZUnFVOlw678076036222',
          'splitPercentage' => 100,
        ]
      ]);

      // save booking
      $booking = UserBooking::where('order_id', $this->bookingData['orderId'])->first();
      $booking->status = '1';
      $booking->save();

      //first guest is set as the username if not otherwise changed guest details
      for ($i = 0; $i < count($this->guests); $i++) {
        $guest = new Guest;
        $guest->users_id = Auth::user()->id;
        $guest->booking_id = $booking->id;
        $guest->first_name = $this->guests[$i]['name'];
        $guest->email = $this->guests[0]['email'];
        $guest->phone = $this->guests[0]['phone'];
        $guest->suggestion = $this->suggestion;
        $guest->param1 = $this->guests[$i]['below12'] == 1 ? true : false;
        $guest->save();
      }
    } catch (\Throwable $th) {
      $this->errorPopupMessage = true;
      // dd($th);
      return redirect()->route('booking');
    }

    // dd($response);

    $response = json_decode($response->getBody());
    // session()->forget('bookingData');

    //redirec to response handler
    return redirect($response);
  }

  public function render()
  {
    return view('livewire.front.booking-information');
  }
}
