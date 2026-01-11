@extends('layouts.app')

@section('title', 'Movies')

@section('content')
<div class="relative mx-6 lg:mx-16 py-10 mb-10">
    <h1 class="text-3xl font-bold text-white">
        {{ $userName}} {{ $type }}
    </h1>

</div>

  <!-- Movie Grid -->
<div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10 mt-3">
        @foreach($movies as $movie)
            <x-movie-card :movie="$movie->movie" />
        @endforeach
    </div>
@endsection
