<div id="overlay" class="overlay"></div>
<nav id="sidebar" class="sidebar sidebar-collapsed bg-light">
  <button class="close-btn" type="button" id="closeSidebar" aria-label="Close sidebar">
    <i class="bi bi-x"></i>
  </button>
  <div class="position-sticky sidebar-sticky">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="list-group-item list-group-item-action" style="border:0px" href="{{ route('pages.dashboard')  }}">
          <i class="bi bi-house"> </i>Dashboard
        </a>

      </li> @foreach($menu as $menuitem)
      <li class="nav-item">
      <a class="nav-link  " style="border:0px" href="{{ route($menuitem->path) }}">
        <i class="bi bi-arrow-right-short"></i>{{ $menuitem->menuname }}</a>
      </li>
    @endforeach 
      <li class="nav-item ">
        <a class="nav-link " href="{{route('pages.notifications')}}">
          <i class="bi bi-bell-fill"></i> Notifications </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route("pages.myprofile")}}"> <i class="bi bi-person-circle"> </i>My
          Account</a>
      </li>
    </ul>
  </div>
</nav>