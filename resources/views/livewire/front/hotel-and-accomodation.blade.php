<div class="mt-10 mb-10">
  <div class="flex flex-row gap-10 justify-center">
    <div wire:click="changeType('Hotel')" class="{{ $type == 'Hotel' ? 'border-b-2 text-black' : 'border-none text-whitish-gray' }} font-bold flex-cols-1 border-colorx text-right hover:cursor-pointer">
      Hotel
    </div>
    <div wire:click="changeType('Homestay')" class="{{ $type == 'Homestay' ? 'border-b-2 text-black' : 'border-none text-whitish-gray' }} font-bold flex-cols-1 border-colorx hover:cursor-pointer">
      Homestay
    </div>
  </div>
  <div class="mt-10 mb-10">
    <div class="font-kushan text-center text-4xl">List of {{ $type }}</div>
    <div class="text-center text-sm">{{ count($accomodations) }} {{ $type }}</div>
  </div>
  @if ($type == 'Hotel')
    <div class="m-4">
      <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-12 gap-4">
        @foreach ($districts as $district)
        <div wire:click="getData({{ $district->id }})" class="{{ $this->districtsId == $district->id ? 'border-colorx' : 'border-district-outline' }} border pl-4 pr-4 p-2 rounded-lg text-center hover:cursor-pointer hover:shadow-lg font-bold hover:scale-105 transition duration-300 ease-in-out">
          {{ $district->district_name }}
        </div>
        @endforeach
      </div>
    </div>
  @endif
  <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-4 p-4">
    @if (count($accomodations) == 0)
      <div class="italic text-xs">
        No operators found
      </div>  
    @endif
    @foreach ($accomodations as $accomodation)
    <div class="flex flex-row border border-card-border hover:shadow-xl pt-4 p-2 hover:cursor-pointer hover:scale-105 transition duration-300 ease-in-out">
      <div class="flex-col p-2"><img src="/image/icons/hotel.png" alt="guide" class="h-10 w-10"></div>
      <div class="flex-col">
        <div class="font-bold text-xl">{{ $accomodation->name }}</div>
        <div class="mt-4">
          <ul>
            <li class="location">{{ $accomodation->address }}</li>
            <li class="phone">{{ $type == 'Hotel' ? $accomodation->phone_one : $accomodation->phone }}</li>
            <li class="mail">{{ $accomodation->email }}</li>
          </ul>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
