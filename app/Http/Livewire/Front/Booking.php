<?php

namespace App\Http\Livewire\Front;

use DateTime;
use Throwable;
use App\Models\User;
use App\Models\Lodge;
use App\Models\Amenity;
use App\Models\UserOtp;
use Livewire\Component;
use Carbon\CarbonPeriod;
use App\Models\BookedDates;
use App\Models\UserBooking;
use App\Models\LodgeAmenity;
use App\Models\LodgeRoomData;
use App\Models\LodgeRoomType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

// use function PHPSTORM_META\type;

class Booking extends Component
{
  public $lodges;
  public $selectedLodge;
  public $selectedLodgeId;
  public $checkIn;
  public $checkOut;
  public $roomTypes;
  public $selectedRoomType;
  public $selectedInDay;
  public $selectedOutDay;
  public $noOfRooms = 1;
  public $roomsAvailable;
  public $price;
  public $noOfRoomsAvailable;
  public $noOfDays = 1;
  public $noLodgeAvailable = false;
  public $bookingNotAvailable = false; //flag to check if booking can be made
  public $amenities;

  // login for mobile users;
  public $signupDialog = false;
  public $emailPhone = '';
  public $type = '';
  public $mailSent = false;
  public $existing = false;
  public $user;
  public $enteredOtp;
  public $invalid = false;

  public function mount()
  {

    if (session()->exists('bookingData')) {
      $this->initializeBookingData();
    } else {
      $this->checkIn = date_format(today(), 'Y-m-d');
      $this->checkOut = date_format(today()->addDay(), 'Y-m-d');

      $this->selectedInDay = date('l', strtotime($this->checkIn));
      $this->selectedOutDay = date('l', strtotime($this->checkOut));

      $roomTypes = LodgeRoomType::pluck('id');
      $rooms = LodgeRoomData::whereIn('lodge_room_types_id', $roomTypes)->where('param1', 'Enable')->pluck('lodges_id');

      $this->selectedLodge = Lodge::whereIn('id', $rooms)->with('districtMetaData')->orderBy('name', 'ASC')->first()->toArray();
      $this->selectedLodgeId = $this->selectedLodge['id'];

      $this->lodges = Lodge::whereIn('id', $rooms)->with('districtMetaData')->orderBy('name', 'ASC')->get() ?? [];
      $roomType = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->where('param1', 'Enable')->get()->pluck('lodge_room_types_id');

      $this->selectedRoomType = LodgeRoomType::whereIn('id', $roomType)->first()->toArray();

      $this->roomsAvailable = LodgeRoomData::where('lodges_id', $this->selectedLodge)->where('lodge_room_types_id', $this->selectedRoomType)->first();
      $this->price = $this->roomsAvailable->price * $this->noOfRooms * $this->noOfDays;
      $this->roomTypes = LodgeRoomType::whereIn('id', $roomType)->get();
      $this->getNoOfRooms();
      $this->getRoomAmenities();
    }
  }

  public function getRoomAmenities()
  {
    $roomType = LodgeRoomData::with(['amenities'])->where('lodges_id', $this->selectedLodge['id'])->where('lodge_room_types_id', $this->selectedRoomType['id'])->first();
    $amenities = [];
    // dd($roomType);
    foreach ($roomType->amenities as $amenity) {
      array_push($amenities, $amenity->name);
    }
    $this->amenities = $amenities;
  }

  public function updatedSelectedLodgeId()
  {
    $this->emit('refreshSelect2');

    $lodge = Lodge::where('id', $this->selectedLodgeId)->with('districtMetaData')->first()->toArray();
    $this->selectedLodge = $lodge;
    $roomType = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->where('param1', 'Enable')->get()->pluck('lodge_room_types_id');
    $this->selectedRoomType = LodgeRoomType::whereIn('id', $roomType)->first()->toArray();
    $this->roomTypes = LodgeRoomType::whereIn('id', $roomType)->get();
    $this->roomsAvailable = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->where('lodge_room_types_id', $this->selectedRoomType)->first();

    $this->checkIn = date_format(today(), 'Y-m-d');
    $this->checkOut = date_format(today()->addDay(), 'Y-m-d');
    $this->selectedInDay = date('l', strtotime($this->checkIn));
    $this->selectedOutDay = date('l', strtotime($this->checkOut));

    $date1 = new DateTime($this->checkIn);
    $date2 = new DateTime($this->checkOut);
    $interval = $date1->diff($date2);
    $this->noOfDays = (int) $interval->format('%a');

    $this->noOfRooms = 1;

    $this->price = $this->roomsAvailable->price * $this->noOfRooms * $this->noOfDays;

    $this->getNoOfRooms();
    $this->getRoomAmenities();
  }

  public function updatedCheckIn($date)
  {
    $this->selectedInDay = date('l', strtotime($date));
    $this->checkOut = date('Y-m-d', strtotime("+1 day", strtotime($date)));
    $this->selectedOutDay = date('l', strtotime($this->checkOut));
    $this->noOfDays = 1;
    $this->getNoOfRooms();
    $this->getRoomAmenities();
  }

  public function updatedCheckOut($date)
  {
    $this->selectedOutDay = date('l', strtotime($date));
    $date1 = new DateTime($this->checkIn);
    $date2 = new DateTime($this->checkOut);
    $interval = $date1->diff($date2);
    $this->noOfDays = (int) $interval->format('%a');
    $this->price = $this->roomsAvailable->price * $this->noOfRooms * $this->noOfDays;
    $this->getNoOfRooms();
    $this->getRoomAmenities();
  }

  public function selectLodge($lodge)
  {
    $this->selectedLodge = $lodge;
    $roomType = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->where('param1', 'Enable')->get()->pluck('lodge_room_types_id');
    $this->selectedRoomType = LodgeRoomType::whereIn('id', $roomType)->first()->toArray();
    $this->roomTypes = LodgeRoomType::whereIn('id', $roomType)->get();
    $this->roomsAvailable = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->where('lodge_room_types_id', $this->selectedRoomType)->first();

    $this->checkIn = date_format(today(), 'Y-m-d');
    $this->checkOut = date_format(today()->addDay(), 'Y-m-d');
    $this->selectedInDay = date('l', strtotime($this->checkIn));
    $this->selectedOutDay = date('l', strtotime($this->checkOut));

    $date1 = new DateTime($this->checkIn);
    $date2 = new DateTime($this->checkOut);
    $interval = $date1->diff($date2);
    $this->noOfDays = (int) $interval->format('%a');

    $this->noOfRooms = 1;

    $this->price = $this->roomsAvailable->price * $this->noOfRooms * $this->noOfDays;

    $this->getNoOfRooms();
    $this->getRoomAmenities();
  }

  public function selectRoomType($roomType)
  {
    $this->selectedRoomType = $roomType;
    $this->roomsAvailable = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->where('lodge_room_types_id', $this->selectedRoomType)->first();
    $this->price = $this->roomsAvailable->price * $this->noOfRooms * $this->noOfDays;
    $this->getNoOfRooms();
    $this->getRoomAmenities();
  }

  public function selectNoOfRooms($no)
  {
    $this->noOfRooms = $no;
    $this->price = $this->roomsAvailable->price * $this->noOfDays * $this->noOfRooms;
  }

  public function getNoOfRooms()
  {
    $roomsAvailable = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->where('lodge_room_types_id', $this->selectedRoomType['id'])->first();

    $period = CarbonPeriod::create($this->checkIn, date('Y-m-d', strtotime('-1 day', strtotime($this->checkOut))));
    $dates = [];

    foreach ($period as $date) {
      array_push($dates, $date->format('Y-m-d'));
    }

    $bookedDates = BookedDates::whereIn('booked', $dates)
      ->get()
      ->pluck('user_booking_id');

    // dd($this->roomsAvailable->id);

    // using param1 as booking status
    $bookedRooms = UserBooking::whereIn('id', $bookedDates->unique())->where('status', 'Confirmed')->where('param1', '!=', 'Out')->where('lodge_room_data_id', $this->roomsAvailable->id)->sum('number_of_room_require');

    $this->noOfRoomsAvailable = $roomsAvailable->room_available - $bookedRooms;

    $reservedRooms = UserBooking::whereIn('id', $bookedDates->unique())->where('status', '1')->where('lodge_room_data_id', $this->roomsAvailable->id)->sum('number_of_room_require');

    $this->noOfRoomsAvailable = $this->noOfRoomsAvailable - $reservedRooms;

    if ($this->noOfRoomsAvailable <= 0) {
      $this->noOfRooms = 0;
      $this->price = $this->roomsAvailable->price * $this->noOfDays * $this->noOfRooms;
    } else {
      $this->noOfRooms = 1;
      $this->price = $this->roomsAvailable->price * $this->noOfDays * $this->noOfRooms;
    }
  }

  public function proceedBooking()
  {
    if (Auth::check()) {

      $orderId = 'MZTOUR' . now()->timestamp;
      // $lodgeAmenities = LodgeAmenity::where('lodges_id', $this->selectedLodge['id'])->get()->pluck('amenities_id');
      // $amenities = Amenity::whereIn('id', $lodgeAmenities)->get()->pluck('name')->toArray();
      // $lodgeRooms = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->with('amenities')->get();

      // dd($lodgeRooms);


      $data = [
        'lodge' => $this->selectedLodge,
        'checkIn' => $this->checkIn,
        'checkOut' => $this->checkOut,
        'roomType' => $this->selectedRoomType,
        'noOfRooms' => $this->noOfRooms,
        'amount' => $this->price,
        'orderId' => $orderId,
        'amenities' => $this->amenities,
        'status' => 'Processing', //Booking kal lai mek payment la tih loh
      ];


      $lodgeRoomData = LodgeRoomData::where('lodges_id', $data['lodge']['id'])
        ->where('lodge_room_types_id', $data['roomType']['id'])->first();

      $roomsAvailable = $lodgeRoomData->room_available;

      $period = CarbonPeriod::create($data['checkIn'], date('Y-m-d', strtotime('-1 day', strtotime($data['checkOut']))));
      $dates = [];

      foreach ($period as $date) {
        array_push($dates, $date->format('Y-m-d'));
      }

      $bookedDates = BookedDates::whereIn('booked', $dates)
        ->get()
        ->pluck('user_booking_id');

      // using param1 as booking status
      // check for all bookings made that have been paid and confirmed
      $bookedRooms = UserBooking::whereIn('id', $bookedDates->unique())->where('status', 'Confirmed')->where('param1', '!=', 'Out')->where('lodge_room_data_id', $lodgeRoomData->id)->sum('number_of_room_require');

      //check if there is currently any pending booking/reservation currently made
      $reservedRooms = UserBooking::whereIn('id', $bookedDates->unique())->where('status', '1')->where('lodge_room_data_id', $lodgeRoomData->id)->sum('number_of_room_require');

      $noOfRoomsAvailable = $roomsAvailable - ($bookedRooms + $reservedRooms);

      if ($noOfRoomsAvailable >= $data['noOfRooms']) {

        $booking = new UserBooking;
        $booking->lodge_room_data_id = $lodgeRoomData->id;
        $booking->order_id = $data['orderId'];
        $booking->users_id = Auth::user()->id;
        $booking->number_of_room_require = $data['noOfRooms'];
        $booking->check_in = $data['checkIn'];
        $booking->check_out = $data['checkOut'];
        $booking->status = 1;
        $booking->save();

        $period = CarbonPeriod::create($booking->check_in, date('Y-m-d', strtotime('-1 day', strtotime($booking->check_out))));

        foreach ($period as $date) {
          $bookedDate = new BookedDates;
          $bookedDate->user_booking_id = $booking->id;
          $bookedDate->lodge_room_data_id = $booking->lodge_room_data_id;
          $bookedDate->booked = $date->format('Y-m-d');
          $bookedDate->save();
        }

        $this->bookingNotAvailable = false;

        // When a customer book lodge/hotel store the current data in session
        // this will help to modify booking data by going to booking page
        session(['bookingData' => $data]);

        return redirect()->route('bookingInformation', $data);
      } else {
        $this->bookingNotAvailable = true;
      }
    } else {
      $this->signupDialog = true;
    }
  }

  public function createUser()
  {
    $otp = rand(1000, 9999);
    $templateId = '1407164844795741418';

    $userOtp = UserOtp::firstOrNew(['type' => $this->type, 'contact' => $this->emailPhone]);
    $userOtp->otp = $otp;
    $userOtp->save();

    $this->user = User::where($this->type, $this->emailPhone)->first();
    if ($this->user != null) {
      $this->existing = true;
    }

    if ($this->type == 'email') {
      $this->mailSent = true;
      try {
        $res = Mail::send(
          'emails.registration-mail',
          ['otp' => $otp],
          function ($message) {
            $message->to($this->emailPhone)
              ->subject('OTP for registration');
            $message->from('noreply@gmail.com', 'OTP For Registration');
          }
        );
        $this->mailSent = true;
      } catch (Throwable $ex) {
        Log::info("MZERROR:line-339->Booking createUser():::" . $ex);
        $this->mailSent = false;
      }
    } else {
      try {
        Http::withHeaders([
          'Authorization' => "Bearer 11|Bzz867LIgOYkk8hVterz3KSsDY8Cmjg5FV7C2N7d",
        ])->get("https://sms.msegs.in/api/send-otp", [
          'template_id' => $templateId,
          'message' => "Your OTP for Login/Registration to MZtour is $otp ",
          'recipient' => $this->emailPhone
        ]);
        $this->mailSent = true;
      } catch (Throwable $ex) {
        Log::info("MZERROR:line-353->UserBooking createUser():::" . $ex);
        $this->mailSent = false;
      }
    }
  }

  public function goBack()
  {
    $this->mailSent = false;
  }

  public function verifyOtp()
  {
    $userOtp = UserOtp::where('contact', $this->emailPhone)->where('otp', $this->enteredOtp)->first();
    if ($userOtp != null) {
      $this->invalid = false;
      $userOtp->delete();
      if ($this->existing) {
        Auth::login($this->user);
        return redirect(request()->header('Referer'));
      }
      $data = [
        'type' => $this->type,
        'emailPhone' => $this->emailPhone,
      ];

      $this->signupDialog = false;
      $this->emit('otpVerified', $data);
    } else {
      $this->invalid = true;
    }
  }

  public function updatedEmailPhone()
  {
    if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $this->emailPhone)) {
      $this->type = 'email';
    } elseif (preg_match("/^\d+$/", $this->emailPhone)) {
      $this->type = 'phone';
    } else {
      $this->type = '';
    }
  }

  public function initializeBookingData()
  {
    //check if session for current booking exist
    //this is done to retrieve data when users want to modify their bookings in booking page
    $bookingData = session()->pull('bookingData');

    if ($bookingData['status'] == 'Processing') {
      $reservedRooms = UserBooking::where('order_id', $bookingData['orderId'])->first();
      $reservedRooms->status = "Booking Retracted"; //modify bookin an click emaw back button an click chuan booking in process lai kha delete lovin retracted tiin a in save ang
      $reservedRooms->save();
      $bookingData['status'] = "Booking Retracted";
    }

    $this->checkIn = $bookingData["checkIn"];
    $this->checkOut = $bookingData["checkOut"];

    $this->selectedInDay = date('l', strtotime($bookingData["checkIn"]));
    $this->selectedOutDay = date('l', strtotime($bookingData["checkOut"]));

    //calculate no of days to use as calculation for price
    $date1 = new DateTime($this->checkIn);
    $date2 = new DateTime($this->checkOut);
    $interval = $date1->diff($date2);
    $this->noOfDays = (int) $interval->format('%a');

    $roomTypes = LodgeRoomType::pluck('id');
    $rooms = LodgeRoomData::whereIn('lodge_room_types_id', $roomTypes)->where('param1', 'Enable')->pluck('lodges_id');

    $this->lodges = Lodge::whereIn('id', $rooms)->with('districtMetaData')->orderBy('name', 'ASC')->get() ?? [];
    $this->selectedLodge = $bookingData['lodge'];
    $this->selectedLodgeId = $this->selectedLodge['id'];
    $roomType = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->where('param1', 'Enable')->get()->pluck('lodge_room_types_id');

    $this->selectedRoomType = $bookingData['roomType'];
    $this->noOfRooms = $bookingData['noOfRooms'];

    $this->roomsAvailable = LodgeRoomData::where('lodges_id', $this->selectedLodge)->where('lodge_room_types_id', $this->selectedRoomType)->first();
    $this->roomTypes = LodgeRoomType::whereIn('id', $roomType)->get();

    $roomsAvailable = LodgeRoomData::where('lodges_id', $this->selectedLodge['id'])->where('lodge_room_types_id', $this->selectedRoomType['id'])->first();

    $period = CarbonPeriod::create($this->checkIn, date('Y-m-d', strtotime('-1 day', strtotime($this->checkOut))));
    $dates = [];

    $this->price = $this->roomsAvailable->price * $this->noOfRooms * $this->noOfDays;

    $this->getRoomAmenities();

    foreach ($period as $date) {
      array_push($dates, $date->format('Y-m-d'));
    }

    $bookedDates = BookedDates::whereIn('booked', $dates)
      ->get()
      ->pluck('user_booking_id');

    // using param1 as booking status
    $bookedRooms = UserBooking::whereIn('id', $bookedDates->unique())->where('status', 'Confirmed')->where('param1', '!=', 'Out')->where('lodge_room_data_id', $this->roomsAvailable->id)->sum('number_of_room_require');
    $reservedRooms = UserBooking::whereIn('id', $bookedDates->unique())->where('status', '1')->where('lodge_room_data_id', $this->roomsAvailable->id)->sum('number_of_room_require');

    $this->noOfRoomsAvailable = $roomsAvailable->room_available - ($reservedRooms + $bookedRooms);
  }

  public function render()
  {
    return view('livewire.front.booking');
  }
}
