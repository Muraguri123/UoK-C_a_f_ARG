@extends('layouts.master')

@section('content')
<div class="row bg-light" style="padding-right:0px">
   @auth
      <div>
        <div class="row bg-white">
          <div class="col-12 ">
            <div>
               <h5 class="text-dark text-center">Welcome, {{Auth::user()->name}}</h5>

            </div>
            <div>

            </div>
          </div>
        </div>
      </div>

   @endauth
</div>
@endsection