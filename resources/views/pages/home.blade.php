@extends('layouts.master')

@section('content')
<div class="row bg-light" style="padding-right:0px">
   @auth
      @if (Auth::check() && Auth::user()->role == 1)

        <div class="row text-center " style="padding-top:8px">
         <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
           <div class="card text-white  bg-secondary  mb-3 " style="max-width: 18rem;">
            <div class="card-header">Total Proposals</div>
            <div class="card-body">
               <h1 class="card-title">{{isset($allProposalscount) ? $allProposalscount : 0 }}</h1>

            </div>
           </div>
         </div>
         <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
           <div class="card text-white bg-success mb-3  " style="max-width: 18rem;">
            <div class="card-header">Approved</div>
            <div class="card-body">
               <h1 class="card-title">{{isset($approvedProposalsCount) ? $approvedProposalsCount : 0 }}</h1>

            </div>
           </div>
         </div>
         <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

           <div class="card text-white bg-info mb-3 " style="max-width: 18rem;">
            <div class="card-header ">Pending</div>
            <div class="card-body">
               <h1 class="card-title">{{isset($pendingProposalsCount) ? $pendingProposalsCount : 0 }}</h1>

            </div>
           </div>
         </div>

         <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

           <div class="card text-white bg-danger mb-3 " style="max-width: 18rem;">
            <div class="card-header ">Rejected</div>
            <div class="card-body">
               <h1 class="card-title">{{isset($rejectedProposalsCount) ? $rejectedProposalsCount : 0 }}</h1>

            </div>
           </div>
         </div>
        </div>
        <div class="row text-center ">
         <div class="text-center text-dark">
           <br />
           <h5><b>Applications Count by Themes</b></h5>
         </div>
         <div id="barchart_material" class="col-xs-12 " style="width: 99%;height: 300px;margin:8px">
         </div>
         <script type="text/javascript">
           google.charts.load('current', { 'packages': ['bar'] });
           google.charts.setOnLoadCallback(drawChart);
           console.log({{ Js::from($themeCounts) }})
           function drawChart() {
            var data = google.visualization.arrayToDataTable({{ Js::from($themeCounts) }});
            var chart = new google.charts.Bar(document.getElementById('barchart_material'));
            var options = {
               legend: { position: 'none', maxLines: 3 },
               chartArea: { width: '100%' },
               isStacked: false,
               colors: ['#9fc5e8', '#6aa84f'],
               bar: { groupWidth: '75%' },
               bars: 'horizontal',
               hAxis: { title: 'Proposal Counts' },
               vAxis: { title: 'Research Themes', minValue: 0 },
               animation: { startup: true, duration: 1000, easing: 'out' }
            };

            chart.draw(data, google.charts.Bar.convertOptions(options));
            window.addEventListener('resize', () => {
               chart.draw(data, google.charts.Bar.convertOptions(options));
            });
           }
         </script>
        </div>
     @elseif (Auth::check() && Auth::user()->role == 2)
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
     @else
        <div class=" text-center"><b>Unknown Role kindly consult admin</b></div>
     @endif

   @endauth
</div>
@endsection