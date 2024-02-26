<div class="mb-10">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 md:grid-cols-3">
        <div>
            <div class="p-10 border border-booking-card-500">
                <div class="text-4xl font-kushan">Review your Booking</div>
                <div class="mt-10">
                    <div class="flex flex-row justify-between">
                        <div class="flex-col">
                            <div class="text-xl font-extrabold">{{ $bookingData['lodge']['name'] }}</div>
                            <div class="text-s">
                                {{ $bookingData['lodge']['district_meta_data']['district_name'] }} District
                            </div>
                        </div>
                        <div class="flex-col">&#8377; {{ $bookingData['amount'] }}</div>
                    </div>
                </div>
                <div class="mt-10">
                    <div class="flex flex-row justify-between">
                        <div class="flex-col">
                            <div class="text-xl font-extrabold">{{ $bookingData['roomType']['name'] }}</div>
                        </div>
                        <div class="flex-col">{{ $bookingData['noOfRooms'] }} Room</div>
                    </div>
                </div>
                {{-- <div class="mt-10">
                    <div class="flex flex-row justify-between">
                        <div class="flex-col">
                            <div class="text-xl font-extrabold">Amenities</div>
                        </div>
                        <div class="flex-col">
                            @foreach ($bookingData['amenities'] as $amenity)
                                {{ $amenity }} <br>
                            @endforeach
                        </div>
                    </div>
                </div> --}}
                <div class="mt-10">
                    <div class="p-4 border border-dashed border-booking-card-500 bg-booking-background-solid">
                        <div class="flex flex-row justify-between">
                            <div class="flex-col">
                                CHECK IN
                                <div class="mt-2">
                                    {{ date('jS F Y', strtotime($bookingData['checkIn'])) }}
                                </div>
                                <div class="text-xs">
                                    {{ date('l', strtotime($bookingData['checkIn'])) }}
                                </div>
                            </div>
                            <div class="flex-col">
                                CHECK OUT
                                <div class="mt-2">
                                    {{ date('jS F Y', strtotime($bookingData['checkOut'])) }}
                                </div>
                                <div class="text-xs">
                                    {{ date('l', strtotime($bookingData['checkOut'])) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div class="p-4">
                <div class="text-3xl font-kushan">Visitor Information</div>
                <div class="">You're almost there, please fill all the necessary information</div>
            </div>
            <div class="w-full p-4 border border-booking-card-500">
                <div class="text-2xl font-extrabold">Guest Details</div>
                <div class="text-xs italic">Contact information of one of the guest is required</div>
                <div class="mt-10">
                    <input wire:model="guests.0.name" class="w-full" type="text" label="name"
                        placeholder="Full Name">
                </div>
                <div class="grid grid-cols-2 gap-4 mt-10">
                    <div>
                        <input wire:model="guests.0.email" class="w-full" type="email" placeholder="Email ID">
                    </div>
                    <div>
                        <input wire:model="guests.0.phone" class="w-full" type="number" placeholder="Phone Number">
                    </div>
                </div>
                <div class="mt-10">
                    <button wire:click="addGuest" class="text-colorx hover:text-tourism-green">+ Add Guest</button>
                </div>
                <div class="w-full mt-10 mb-10 border-b border-booking-card-500">
                    Your Guests
                </div>
                @for ($i = 0; $i < count($guests); $i++)
                    <div class="grid grid-cols-5 gap-2 mb-1 border-b border-dashed border-opacity-40">
                        <div class="col-span-4 p-1">
                            <div class="font-extrabold">{{ $i + 1 . '. ' . strtoupper($guests[$i]['name']) }}</div>
                            <div class="text-xs">{{ $guests[$i]['below12'] ? 'Below 12 Years' : '' }}</div>
                        </div>
                        <div class="">
                            @if ($i > 0)
                                <button wire:click="removeGuest({{ $i }})"
                                    class="rounded-full bg-red-600 text-white font-extrabold text-xs pl-[5px] pr-[5px]">
                                    &times;
                                </button>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
            <div class="w-full p-4 mt-10 border border-booking-card-500">
                <div class="text-2xl font-extrabold">Special Request (Optional)</div>
                <textarea wire:model.lazy="suggestion" name="" id="" cols="30" rows="4" class="w-full"
                    placeholder="If you have any request, please mention here"></textarea>
            </div>
            <div class="grid items-center grid-cols-1 gap-4 mt-5 md:grid-cols-4">
                <div class="">
                    <img src="/image/icons/paytm-pg-blue.png" alt="paytm" class="mx-auto my-2">
                </div>
                <div
                    class="w-full bg-gradient-to-r from-[#042e6f] to-[#00baf2] text-white font-bold text-center col-span-3 p-[1.45rem] rounded-full">
                    Get ₹30-₹500 Cashback with minimum payment of ₹1000 using Paytm wallet and postpaid.
                    <br>
                    Minimum assured cashback ₹30
                </div>
            </div>
            <button wire:click="makePayment"
                class="float-right p-2 pl-4 pr-4 mt-10 font-extrabold text-white rounded-full bg-colorx"
                wire:loading.remove wire:target="makePayment">
                Make Payment
            </button>
            <button disabled wire:loading wire:target="makePayment"
                class="float-right p-2 pl-4 pr-4 mt-10 font-extrabold text-white rounded-full bg-colorx">
                Processing...
            </button>
            <small><b>Note: Please do not refresh or close the page during the entire payment process.</b></small>
        </div>

    </div>

    <x-jet-dialog-modal wire:model="addGuestDialog">
        <x-slot name="title">
            <div class="text-lg font-extrabold">Add Guest</div>
        </x-slot>
        <x-slot name="content">
            <div class="p-10 border border-dashed">
                <div class="text-xs">Name should be as per official govt. ID & travelers below 18 years of age
                    cannot travel alone</div>
                <input wire:model.lazy="newGuestName" type="text" class="w-full mt-4" placeholder="Full Name">
                <div class="flex flex-row items-center content-center hover:cursor-pointer" wire:click="below12">
                    <div class="flex-col">
                        <input wire:model.lazy="below12Years" disabled type="checkbox" name="Below 12 years of age"
                            id="below">
                    </div>
                    <div class="flex-col ml-2 text-xs">Below 12 years of age</div>
                </div>
                <div class="mt-8">
                    <button
                        wire:click.lazy="addNewGuest('{{ $newGuestName }}', {{ $below12Years == false ? '0' : '1' }})"
                        class="p-2 pl-4 pr-4 font-extrabold text-white rounded-full bg-colorx">Add to Guest</button>
                </div>
            </div>
        </x-slot>
    </x-jet-dialog-modal>

    {{-- Popup when user click back button while making payment, then attempts to make payment again causing error --}}
    <x-jet-dialog-modal wire:model="errorPopupMessage">
        <x-slot name="title">
            <div class="text-lg font-extrabold text-center">Error</div>
        </x-slot>
        <x-slot name="content">
            <div class="text-center text-md"> Something went wrong. Please wait for some time and try again.</div>
        </x-slot>
    </x-jet-dialog-modal>
</div>
