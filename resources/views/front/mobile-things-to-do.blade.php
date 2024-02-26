@extends('layouts.layout')
@section('contents')
<div class="p-4 mt-10 mb-10 container mx-auto">
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
    @foreach ($tags as $tag)
      <a href="{{ Route('thingsToDo', $tag->name) }}" class="font-extrabold text-xl border-b border-opacity-20 p-4">
        {{ $tag->name }}
      </a>
    @endforeach
  </div>
</div>
@endsection