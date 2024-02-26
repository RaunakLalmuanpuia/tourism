<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Lodge Statement</title>
</head>
<body>
  <table>
    <tr>
      <th class="p-2 text-center border">Order ID</th>
      <th class="p-2 text-center border">Transaction Date</th>
      <th class="p-2 text-center border">Refund Date</th>
      <th class="p-2 text-center border">Check In Date</th>
      <th class="p-2 text-center border">Amount</th>
      <th class="p-2 text-center border">Payment Status</th>
      <th class="p-2 text-center border">Booking Status</th>
    </tr>
    @foreach ($data as $item)
    <tr>
      <td class="p-2 text-center border">{{ $item[0]->orderId }}</td>
        <td class="p-2 text-center border">
          {{ date('d-m-Y', strtotime($item[0]->created_at)) }}
        </td>
        <td class="p-2 text-center border">
          @if ($item[0]->status == 'REFUNDED')
          {{ date('d-m-Y', strtotime($item[0]->updated_at)) }}
          @endif
        </td>
        <td class="p-2 text-center border">{{ $item[0]->userBooking->check_in }}</td>
        <td class="p-2 text-center border">{{ $item[0]->amount }}</td>
        <td class="p-2 text-center border">{{ $item[0]->status }}</td>
        @if ($item[0]->userBooking->status == 1)
          <td class="p-2 text-center border">Unconfirmed</td>
        @else
          <td class="p-2 text-center border">{{ $item[0]->userBooking->status }}{{ $item[0]->userBooking->status == 'Cancelled' ? ' at ' . $item[0]->userBooking->cancelled_at : '' }}</td>
        @endif
    </tr>
    @endforeach
  </table>
</body>
</html>
