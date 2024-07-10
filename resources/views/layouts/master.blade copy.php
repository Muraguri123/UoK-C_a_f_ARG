<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="ARG Portal">
  <meta name="author" content="Tagile Solutions">
  <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
  <title>U.O.K ARG Portal</title>
  @include('partials.scripts')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body,
    html {
      height: 100%;
      margin: 0;
    }

    .wrapper {
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .main-content {
      display: flex;
      flex: 1;
      overflow: auto;
    }

    .navbar {
      width: 100%;
      z-index: 101;
    }

    .sidebar {
      width: 250px;
      /* background: #f8f9fa; */
      overflow-y: auto;
      transition: transform 0.3s ease-in-out;
      height: calc(100% - 56px - 32px);
      /* Adjust for navbar and footer height */
      position: fixed;
      /* top: 56px; */
      /* Adjust for navbar height */
      left: 0;
      z-index: 103;
    }

    .sidebar-collapsed {
      transform: translateX(-100%);
    }

    .content-wrapper {
      flex: 1;
      margin-left: 250px;
      transition: margin-left 0.3s;
      display: flex;
      flex-direction: column;

    }

    .footer {
      flex-shrink: 0;
      width: 100%;
      position: fixed;
      bottom: 0;
      color: white;
      height: 32px;
      z-index: 102;
      text-align: center;
    }

    .content {
      flex: 1 1 auto;
      overflow-y: none;
      padding: 4px;
      margin-bottom: 32px;
    }

    @media (max-width: 767.98px) {
      .sidebar {
        width: 80%;
        transform: translateX(-100%);
        z-index: 104;
        height: 100%;
        top: 0;

      }

      .sidebar-active {
        transform: translateX(0);
      }

      .content-wrapper {
        margin-left: 0;
      }

      .close-btn {
        display: block;
      }

      .navbar .navbar-brand {
        display: none;
      }

      .navbar {
        display: flex;
      }

      .navbar-hidden {
        display: none;
      }

      .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 101;
      }

      .overlay-active {
        display: block;
      }

      .rounded {
        display: none;
      }
    }

    @media (min-width: 768px) {
      .sidebar {
        transform: none;
      }

      .content-wrapper {
        margin-left: 250px;
      }

      .close-btn {
        display: none;
      }

      .overlay {
        display: none;
      }
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 1.5rem;
      background: none;
      border: none;
      cursor: pointer;
    }

    .navbar-brand img {
      height: 40px;
      margin-right: 10px;
    }

    .navbar .navbar-nav .nav-item .nav-link img {
      height: 40px;
      width: 40px;
      border-radius: 50%;
    }
  </style>
</head>

<body>
 
  <div class="wrapper">
    <nav class="navbar navbar-expand-lg navbar-light "
      style="background-color:#88CDFF; padding-top:0px; padding-bottom:0px;">
      <div class="container-fluid">
        <button class="btn btn-primary d-md-none me-2" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
          <i class="bi bi-list"></i>
        </button>
        <a class="navbar-brand d-flex align-items-center" href="#">
          <img src="{{asset('images/logo.png')}}" alt="Logo">
          <span class="d-none d-lg-inline">University of Kabianga</span>
        </a>
        <div class="mx-auto text-center">
          <h5 class="navbar-text text-dark" style="padding:0px;margin:0px;">Uok Call for Annual Grants</h5>
        </div>
        <a class="btn btn-outline-light ms-auto d-flex align-items-center" href="{{route('route.logout')}}">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    </nav>

    <div class="main-content">
      <div id="overlay" class="overlay"></div>
      <nav id="sidebar" class="sidebar sidebar-collapsed bg-light">
        <button class="close-btn" type="button" id="closeSidebar" aria-label="Close sidebar">
          <i class="bi bi-x"></i>
        </button>
        <div class="position-sticky sidebar-sticky">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="list-group-item list-group-item-action" style="border:0px"
                href="{{ route('pages.dashboard')  }}">
                <i class="bi bi-house"> </i>Dashboard
              </a>

            </li>
            @foreach($permissions as $permission)
        <li class="nav-item">
          <a class="nav-link  " style="border:0px" href="{{ route($permission->path) }}">
          <i class="bi bi-arrow-right-short"></i>{{ $permission->shortname }}</a>
        </li>
      @endforeach 
            <li class="nav-item">
              <a class="nav-link" href="{{route("pages.myprofile")}}"> <i class="bi bi-person-circle"> </i>My
                Account</a>
            </li>
          </ul>
        </div>
      </nav>

      <div class="content-wrapper">
        <div class="main">
          <main class="content">
            @yield('content')
          </main>
        </div>
      </div>
    </div>

    <footer class="footer navbar-light" style="background-color: lightblue;">
      <div class="container-fluid justify-content-center text-dark ">
        <b>UNIVERSITY OF KABIANGA - </b>Created by <a href="https://Tagile.com">Tagile Solutions</a> &middot; &copy;
        {{ date('Y') }}
      </div>
    </footer>

  </div>
  <div class="position-fixed bottom-0 end-0 p-1 mr-5 " style="z-index:1051">
    <div id="liveToast" class="toast " role="alert" aria-live="assertive" aria-atomic="true">

      <div id="toastheader" class="toast-header text-white ">
        <strong id="toastmessage_body" class="me-auto text-center">

        </strong>
        <button type="button" class="btn-close btn-light" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebar = document.getElementById('sidebar');
      const closeSidebar = document.getElementById('closeSidebar');
      const navbar = document.querySelector('.navbar');
      const overlay = document.getElementById('overlay');

      function toggleSidebar() {
        sidebar.classList.toggle('sidebar-active');
        overlay.classList.toggle('overlay-active');
        if (window.innerWidth <= 767.98) {
          navbar.classList.toggle('navbar-hidden');
        }
      }

      sidebarToggle.addEventListener('click', () => {
        console.log('Sidebar toggle clicked');
        toggleSidebar();
      });

      closeSidebar.addEventListener('click', () => {
        console.log('Close button clicked');
        toggleSidebar();
      });

      overlay.addEventListener('click', () => {
        console.log('Overlay clicked');
        toggleSidebar();
      });
    });
  </script>
</body>

</html>