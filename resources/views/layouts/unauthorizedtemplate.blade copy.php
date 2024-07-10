<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="ARG Portal">
  <meta name="author" content="Tagile Solutions">
  <title>U.O.K ARG Portal</title>
  @include('partials.styles')

</head>

<body style="background-color: #eee;">

  <!-- Right Panel -->
  <div id="right-panel" class="right-panel" style="margin-left:0px">
    @include('partials.headercommon')
    <main class="content">
      <div id="mapCanvas">
        @yield('content')         </div>
      @include('partials.footer')
    </main>
  </div>

  @include('partials.scripts')



</body>

</html>