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
            <th class="p-2 text-center border">Booking ID</th>
            <th class="p-2 text-center border">Name</th>
            <th class="p-2 text-center border">Contact</th>
            <th class="p-2 text-center border">Email</th>
            <th class="p-2 text-center border">Lodge/Room</th>
            <th class="p-2 text-center border">No. of Rooms</th>
            <th class="p-2 text-center border">Check In</th>
            <th class="p-2 text-center border">Check Out</th>
            <th class="p-2 text-center border">Refund Date</th>
            <th class="p-2 text-center border">Amount</th>
            <th class="p-2 text-center border">Cancelled At</th>
        </tr>
        @foreach ($data as $item)
            <tr>
                <td class="p-2 text-center border">{{ $item->order_id }}</td>
                <td class="p-2 text-center border">{{ $item->users->name }}</td>
                <td class="p-2 text-center border">{{ $item->users->phone }}</td>
                <td class="p-2 text-center border">{{ $item->users->email }}</td>
                <td class="p-2 text-center border">{{ $item->lodgeRoomData->lodges->name }} /
                    {{ $item->lodgeRoomData->lodgeRoomTypes->name }}</td>
                <td class="p-2 text-center border">{{ $item->number_of_room_require }}</td>
                <td class="p-2 text-center border">{{ date('d-m-Y', strtotime($item->check_in)) }}</td>
                <td class="p-2 text-center border">{{ date('d-m-Y', strtotime($item->check_out)) }}</td>
                <td class="p-2 text-center border">
                    @if ($item->payment != null)
                        @if ($item->payment->status == 'REFUNDED')
                            {{ $item->payment->updated_at }}
                        @endif
                    @endif
                </td>
                <td class="p-2 text-center border">
                    @if ($item->payment != null)
                        {{ $item->payment->amount }}
                        @php
                            $total += $item->payment->amount;
                        @endphp
                    @endif
                </td>
                <td class="p-2 text-center border">{{ date('d-m-Y H:i a', strtotime($item->cancelled_at)) }}</td>
            </tr>
        @endforeach
        <tr>
            <th class="p-2 text-center border"></th>
            <th class="p-2 text-center border"></th>
            <th class="p-2 text-center border"></th>
            <th class="p-2 text-center border"></th>
            <th class="p-2 text-center border"></th>
            <th class="p-2 text-center border"></th>
            <th class="p-2 text-center border"></th>
            <th class="p-2 text-center border">Total</th>
            <th class="p-2 text-center border">{{ $total }}</th>
            <th class="p-2 text-center border"></th>
        </tr>
    </table>
</body>

</html>
