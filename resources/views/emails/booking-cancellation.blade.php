<div class="">
  Dear Lodge,
  <br>
  <p>
    <b>{{ $user->name }}</b> has <span style="color:red;">CANCELLED</span> <b>{{ $booking->number_of_room_require }}</b> <b>{{ $room->lodgeRoomTypes->name }}</b> room{{ $booking->number_of_room_require == 1 ? '' : 's'}} in your lodge.
    <br>
    From <b>{{ $booking->check_in }}</b> to <b>{{ $booking->check_out }}</b>
    <br>
    Order ID is <b>{{ $booking->order_id }}</b>
    <br>
  </p>
  <HR>
    Please login to https://mizoramtourism.com for more details.


</div>
