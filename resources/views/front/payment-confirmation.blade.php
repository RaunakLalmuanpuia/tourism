@extends('layouts.layout')

@section('contents')
    <div class="container p-4 mx-auto md:p-32">
        {{-- <div class="text-center w-full md:w-1/2 mx-auto p-4 rounded-xl {{ $status == 'TXN_FAILURE' ? 'bg-red-500':'bg-green-500' }} text-white">
    <div class="mb-4 text-xl font-extrabold">Booking {{ $status == 'TXN_SUCCESS' ? 'Confirmation':'Failure' }}</div>
    <div class="">
      Your booking {{ $status == 'TXN_SUCCESS' ? 'has been confirmed': 'could not be completed. Please try again later' }}.
      <br>
      {!! $status == 'TXN_SUCCESS' ? 'Please wait for your confirmation email.' : 'If the issue persists or amount debitted,<br>feel free to contact us at the given phone number below' !!}
    </div>
  </div>
  <div class="w-full p-4 mx-auto text-center md:w-1/2">
    <div class="text-sm italic font-extrabold">
      Note: If you do not receive confirmation mail within 4 hours,
      <br>
      or for any booking related issues please contact:
      <a href="tel: +917642809581">7642809581</a>
    </div>
  </div> --}}
        @if ($status == 'TXN_SUCCESS')
            <div class="w-full p-4 mx-auto text-center text-white bg-green-500 md:w-1/2 rounded-xl">
                <div class="mb-4 text-xl font-extrabold">Booking Confirmation </div>
                <div class="">
                    Your booking has been confirmed.
                </div>
            </div>
            <div class="w-full p-4 mx-auto text-center md:w-1/2">
                <div class="text-sm italic font-extrabold">
                    Note: If you do not receive confirmation mail within 4 hours,
                    <br>
                    or for any booking related issues please contact:
                    <a href="tel: +917642809581">7642809581</a>
                </div>
            </div>
        @elseif ($status == 'TXN_FAILURE')
            <div class="w-full p-4 mx-auto text-center text-white bg-red-500 md:w-1/2 rounded-xl">
                <div class="mb-4 text-xl font-extrabold"> Booking Failure </div>
                <div class="">
                    Your booking could not be completed. Please try again later.
                </div>
            </div>
            <div class="w-full p-4 mx-auto text-center md:w-1/2">
                <div class="text-sm italic font-extrabold">
                    Note: For any booking related issues please contact:
                    <a href="tel: +917642809581">7642809581</a>
                </div>
            </div>
        @else
            <div class="w-full p-4 mx-auto text-center text-black bg-yellow-300 md:w-1/2 rounded-xl">
                <div class="mb-4 text-xl font-extrabold"> Booking Pending </div>
                <div class="">
                    Your booking is under process. Kindly wait for the CONFIRMATION on your registered Email/ Phone Number.
                </div>
            </div>
            <div class="w-full p-4 mx-auto text-center md:w-1/2">
                <div class="text-sm italic font-extrabold">
                    For issues not resolved within an hour. Please Contact the Helpline :
                    <a href="tel: +917642809581">7642809581</a>
                </div>
            </div>
        @endif
    </div>
    <script>
      //redirect to booking page to prevent back button from being pressed too often
        setTimeout(function() {
            let link = "{{ config('app.env') }}";
            if (link == "local") {
                window.location.href = "http://localhost:8000/booking"
            } else {
                window.location.href = "https://mizoramtourism.com/booking"
            }
        }, 2500); // 2 second
    </script>
@endsection
