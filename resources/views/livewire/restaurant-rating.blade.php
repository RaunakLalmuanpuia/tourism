<div>
  <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-4 p-4">
      @foreach ($restaurants as $restaurant)
      <div wire:click="onGuideClick({{$restaurant}})" class="flex flex-row justify-between border border-card-border hover:shadow-xl pt-4 p-2 hover:cursor-pointer hover:scale-105 transition duration-300 ease-in-out">
        <div class="flex-col">
          <div class="flex flex-row">
            <div class="flex-col p-2"><img src="/image/restaurant.png" alt="guide" class="h-10 w-10"></div>
            <div class="flex-col">
              <div class="font-bold text-xl">{{ $restaurant->name }} <span class="text-orange rounded-full bg-[#fff8ed] text-sm pl-2 pr-2">{{ $restaurant->average_ratings }} &#9733;</span></div>
              <div class="text-colorx text-[10px] bg-reg-background rounded-full pl-1 pr-1">{{ $restaurant->license }}</div>
              <div class="mt-4">
                <ul>
                  <li class="location">{{ $restaurant->address }}</li>
                  <li class="phone">{{ $restaurant->phone_one }} / {{ $restaurant->phone_two }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    <div class="mt-4">
      {{ $restaurants->links() }}
    </div>

    <x-jet-dialog-modal wire:model="ratingDialog">
    <x-slot name="title">
      <div class="text-xl font-bold mb-4 text-black">
        Restaurant
      </div>  
    </x-slot>
    <x-slot name="content">
      <div class="grid grid-cols-2">
        <div class="">
          <div class="flex flex-row">
            <div class="flex-col p-2">
              <img src="/image/restaurant.png" alt="guide" class="h-10 w-10">
            </div>
            <div class="flex-col">
              <div class="font-bold text-xl">{{ $selectedRestaurant != null ? $selectedRestaurant['name'] : $restaurant->name }}</div>
              <div class="text-colorx text-[10px] bg-reg-background rounded-full pl-1 pr-1">{{ $selectedRestaurant != null ? $selectedRestaurant['license'] : $restaurant->license }}</div>
              <div class="mt-4">
                <ul>
                  <li class="location">{{ $selectedRestaurant != null ? $selectedRestaurant['address'] :$restaurant->address }}</li>
                  <li class="phone">{{ $selectedRestaurant != null ? $selectedRestaurant['phone_one'] : $restaurant->phone_one }} / {{ $selectedRestaurant != null ? $selectedRestaurant['phone_two'] : $restaurant->phone_two }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center">
          <div class="text-gray-400">OVERALL RATING</div>
          <div class="text-orange text-4xl font-bold text-center">{{ $selectedRestaurant != null ? $selectedRestaurant['average_ratings'] : $restaurant->average_ratings }} <span class="text-xl">&#9733;</span></div>
        </div>
      </div>
      <div class="text-center bg-gray-200 p-4">
        Give Rating
        <div class="">
          <div class="text-orange">
            @for ($i = 0; $i < $userRating; $i++)
            <span class="text-2xl hover:cursor-pointer" wire:click="onRatingClicked({{ $i + 1 }})">&#9733;</span>    
            @endfor
            @for ($i = 0; $i < 5 - $userRating; $i++)
            <span class="text-2xl hover:cursor-pointer" wire:click="onRatingClicked({{ $i + $userRating + 1 }})">&#9734;</span>
            @endfor
          </div>
          <textarea wire:model="review" name="" id="" cols="30" rows="4" class="bg-gray-200" placeholder="Review"></textarea>
        </div>
        <button class="bg-colorx p-2 pr-4 pl-4 text-white" wire:click="giveRating">Rate Now</button>
      </div>
    </x-slot>
  </x-jet-dialog-modal>
</div>
