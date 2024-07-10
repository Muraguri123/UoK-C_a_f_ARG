<!-- Left Panel -->
<aside id="left-panel" class="left-panel">
  <nav class="navbar navbar-expand-sm navbar-default">
    <div id="main-menu" class="main-menu collapse navbar-collapse">
      <ul class="nav nav-link navbar-nav">
        <li class="active">
          <a href="{{ route('pages.dashboard') }}"><i class="menu-icon fa fa-laptop"></i>Dashboard </a>

        </li>
        @foreach($permissions as $permission)
      <li class="menu-item-has-children">
        <a href="{{ route($permission->path) }}"> <i class="nav-item menu-icon fa fa-plus"></i>{{ $permission->shortname }}</a>
      </li>
    @endforeach 
        <li class="menu-item-has-children">
          <a href="{{route("pages.myprofile")}}"> <i class="menu-icon fa fa-plus"></i>My Profile</a>
        </li>



      </ul>
    </div>
  </nav>

</aside>