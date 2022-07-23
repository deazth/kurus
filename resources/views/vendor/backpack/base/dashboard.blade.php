@extends(backpack_view('blank'))

@section('header')
	<section class="container-fluid">
	  <h2>
        <span class="text-capitalize">Upcoming Courses</span>
	  </h2>
	</section>
@endsection

@section('content')
  <div class="card-columns">
    @foreach ($upcoming as $key => $value)
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">{{ $value->name }}</h5>
            <p class="card-text">{{ $value->extra_info }}</p>
            <p class="card-text"><small class="text-muted">
              @if($value->start_date == $value->end_date)
                On {{ $value->start_date }}
              @else
                From {{ $value->start_date }} until {{ $value->end_date }}
              @endif
            </small></p>
          </div>
        </div>
    @endforeach
  </div>
@endsection
