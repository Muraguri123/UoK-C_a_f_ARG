@extends('layouts.master')

@section('content')
@auth
    @if (isset($project))
        <div>
            <style>
                .prop-tabcontainer {
                    background-color: #FAF9F6;
                    border-radius: 4px;
                }

                .prop-tabpanel {
                    border-width: 1px;
                    border-color: gray;
                    background-color: #FAF9F6;
                    border-style: solid;
                    border-radius: 4px;
                    padding: 8px;
                }

                .form-group {
                    margin-bottom: 6px;
                }
            </style>

            <div class="prop-tabcontainer">
                <!-- Nav tabs -->
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-researchinfo-tab" data-bs-toggle="tab"
                            data-bs-target="#panel-researchinfo" type="button" role="tab" aria-controls="panel-researchinfo"
                            aria-selected="true">Research Info</button>
                        <button class="nav-link" id="nav-researchprogress-tab" data-bs-toggle="tab"
                            data-bs-target="#panel-researchprogress" type="button" role="tab"
                            aria-controls="panel-researchprogress" aria-selected="false">Progress</button>
                        <button class="nav-link" id="nav-funding-tab" data-bs-toggle="tab" data-bs-target="#panel-funding"
                            type="button" role="tab" aria-controls="panel-funding" aria-selected="false">Funding</button>
                    </div>
                </nav>

                <!-- Tab panes -->
                <div class="tab-content prop-tabpanel">

                    <!-- researchinfo Details -->
                    <div role="tabpanel" class="tab-pane active" id="panel-researchinfo">
                        <div>
                            <form method="POST" id="basicdetails" enctype="multipart/form-data" class="form-horizontal">
                                @csrf
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class="form-control-label">Research No</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" value="{{$project->researchnumber}}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class="form-control-label">Full Name</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" value="{{$project->applicant->name}}" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class="form-control-label">Research Title</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" value="{{ $project->proposal->researchtitle}}" class="form-control"
                                            readonly>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class="form-control-label">Hypothesis</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" id="pfnofk" name="pfnofk" placeholder="Your PF Number"
                                            value="{{$project->proposal->hypothesis }}" class="form-control" readonly>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <script>

                            $(document).ready(function () {
                                var canviewuser = false;
                                @if(Auth::user()->haspermission('canedituserprofile'))
                                    canviewuser = true;
                                @endif

                                // Function to fetch data using AJAX
                                function fetchData() {
                                    $.ajax({
                                        url: "{{ route('api.users.fetchallusers') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function (response) {
                                            populateTable(response);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error('Error fetching data:', error);
                                        }
                                    });
                                }

                                // Function to search data using AJAX
                                function searchData(searchTerm) {
                                    $.ajax({
                                        url: "{{ route('api.users.fetchsearchusers') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            search: searchTerm
                                        },
                                        success: function (response) {

                                            populateTable(response);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error('Error searching data:', error);
                                        }
                                    });
                                }
                                var routeUrlTemplate = "{{ route('pages.users.viewsingleuser', ['id' => '__ID__']) }}";

                                // Function to populate table with data
                                function populateTable(data) {
                                    var tbody = $('#proposalstable tbody');
                                    tbody.empty(); // Clear existing table rows
                                    if (data.length > 0) {
                                        $.each(data, function (index, data) {
                                            var userurl = routeUrlTemplate.replace('__ID__', data.userid);
                                            var row = '<tr>' +
                                                '<td>' + data.name + '</td>' +
                                                (canviewuser ? '<td><a class="nav-link pt-0 pb-0" href="' + userurl + '">' + data.email + '</a></td>' : '<td>' + data.email + '</td>') +
                                                '<td>' + data.pfno + '</td>' +
                                                '<td>' + Boolean(data.isactive) + '</td>' +
                                                '<td>' + data.role + '</td>' +
                                                '<td>' + new Date(data.created_at).toDateString("en-US") + '</td>' +
                                                '</tr>';
                                            tbody.append(row);
                                        });
                                    }
                                    else {
                                        var row = '<tr><td colspan="5">No Users found</td></tr>';
                                        tbody.append(row);
                                    }
                                }
                                 // Initial fetch when the page loads
                                fetchData();

                                // Search input keyup event
                                $('#searchInput').on('keyup', function () {
                                    var searchTerm = $(this).val().toLowerCase();
                                    if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
                                        searchData(searchTerm);
                                    } else if (searchTerm.length === 0) {
                                        fetchData(); // Fetch all data when search input is empty
                                    }
                                });
                            });
                        </script>
                    </div>

                    <!-- researchprogress tab -->
                    <div role="tabpanel" class="tab-pane" id="panel-researchprogress">
                        <div>
                            <div class="row form-group">
                                @if (auth()->user()->userid == $project->applicant->userid)
                                    <div class="col text-center">
                                        <button id="btn_submitreport" type="button" class="btn btn-info ">Submit Report
                                        </button>
                                    </div>
                                @endif
                                @if (auth()->user()->haspermission('canpauseresearchproject') && $project->projectstatus == 'Active' && $project->ispaused == false)
                                    <div class="col text-center">
                                        <button id="btn_pauseproject" type="button" class="btn btn-info ">Pause
                                        </button>
                                    </div>
                                @endif
                                @if (auth()->user()->haspermission('canresumeresearchproject') && $project->projectstatus == 'Active' && $project->ispaused == true)
                                    <div class="col text-center">
                                        <button id="btn_resumeproject" type="button" class="btn btn-info ">Resume
                                            Project</button>
                                    </div>
                                @endif
                                @if (auth()->user()->haspermission('cancancelresearchprojecct'))
                                    <div class="col text-center">
                                        <button id="btn_cancelproject" type="button" class="btn btn-danger ">Cancel
                                            Project</button>
                                    </div>
                                @endif
                                @if (auth()->user()->haspermission('cancompleteresearchprojecct'))
                                    <div class="col text-center">
                                        <button id="btn_completeproject" type="button" class="btn btn-success ">Complete
                                        </button>
                                    </div>
                                @endif

                            </div>
                            <div>
                                <h5 class="text-center">Research Progress</h5>
                                <table id="researchprogresstable"
                                    class="table table-responsive table-bordered table-striped table-hover" style="margin:4px">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th scope="col">#No</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <script>

                            $(document).ready(function () {
                                var canviewuser = false;
                                @if(Auth::user()->haspermission('canedituserprofile'))
                                    canviewuser = true;
                                @endif

                                // Function to fetch data using AJAX
                                function fetchData() {
                                    $.ajax({
                                        url: "{{ route('api.users.fetchallusers') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function (response) {
                                            populateTable(response);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error('Error fetching data:', error);
                                        }
                                    });
                                }

                                // Function to search data using AJAX
                                function searchData(searchTerm) {
                                    $.ajax({
                                        url: "{{ route('api.users.fetchsearchusers') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            search: searchTerm
                                        },
                                        success: function (response) {

                                            populateTable(response);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error('Error searching data:', error);
                                        }
                                    });
                                }
                                var routeUrlTemplate = "{{ route('pages.users.viewsingleuser', ['id' => '__ID__']) }}";

                                // Function to populate table with data
                                function populateTable(data) {
                                    var tbody = $('#proposalstable tbody');
                                    tbody.empty(); // Clear existing table rows
                                    if (data.length > 0) {
                                        $.each(data, function (index, data) {
                                            var userurl = routeUrlTemplate.replace('__ID__', data.userid);
                                            var row = '<tr>' +
                                                '<td>' + data.name + '</td>' +
                                                (canviewuser ? '<td><a class="nav-link pt-0 pb-0" href="' + userurl + '">' + data.email + '</a></td>' : '<td>' + data.email + '</td>') +
                                                '<td>' + data.pfno + '</td>' +
                                                '<td>' + Boolean(data.isactive) + '</td>' +
                                                '<td>' + data.role + '</td>' +
                                                '<td>' + new Date(data.created_at).toDateString("en-US") + '</td>' +
                                                '</tr>';
                                            tbody.append(row);
                                        });
                                    }
                                    else {
                                        var row = '<tr><td colspan="3">No Reports found</td></tr>';
                                        tbody.append(row);
                                    }
                                }  // Initial fetch when the page loads
                                fetchData();

                                // Search input keyup event
                                $('#searchInput').on('keyup', function () {
                                    var searchTerm = $(this).val().toLowerCase();
                                    if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
                                        searchData(searchTerm);
                                    } else if (searchTerm.length === 0) {
                                        fetchData(); // Fetch all data when search input is empty
                                    }
                                });
                            });
                        </script>


                    </div>

                    <!-- funding tab -->
                    <div role="tabpanel" class="tab-pane" id="panel-funding">
                        <div>
                            <form class="form-horizontal">
                                <div class="row form-group">
                                    <div class="col-12">

                                        <input type="text" id="searchInput" class="form-control text-center"
                                            style="::placeholder { color: red; }"
                                            placeholder="Search by User Name, Email, PFNO or Is Active Status">
                                    </div>
                                </div>
                            </form>
                            <table id="allprojectstable" class="table table-responsive table-bordered table-striped table-hover"
                                style="margin:4px">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Researcher</th>
                                        <th scope="col">Start Date</th>
                                        <th scope="col">End Date</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <script>

                            $(document).ready(function () {
                                var canviewuser = false;
                                @if(Auth::user()->haspermission('canedituserprofile'))
                                    canviewuser = true;
                                @endif

                                // Function to fetch data using AJAX
                                function fetchData() {
                                    $.ajax({
                                        url: "{{ route('api.users.fetchallusers') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function (response) {
                                            populateTable(response);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error('Error fetching data:', error);
                                        }
                                    });
                                }

                                // Function to search data using AJAX
                                function searchData(searchTerm) {
                                    $.ajax({
                                        url: "{{ route('api.users.fetchsearchusers') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            search: searchTerm
                                        },
                                        success: function (response) {

                                            populateTable(response);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error('Error searching data:', error);
                                        }
                                    });
                                }
                                var routeUrlTemplate = "{{ route('pages.users.viewsingleuser', ['id' => '__ID__']) }}";

                                // Function to populate table with data
                                function populateTable(data) {
                                    var tbody = $('#proposalstable tbody');
                                    tbody.empty(); // Clear existing table rows
                                    if (data.length > 0) {
                                        $.each(data, function (index, data) {
                                            var userurl = routeUrlTemplate.replace('__ID__', data.userid);
                                            var row = '<tr>' +
                                                '<td>' + data.name + '</td>' +
                                                (canviewuser ? '<td><a class="nav-link pt-0 pb-0" href="' + userurl + '">' + data.email + '</a></td>' : '<td>' + data.email + '</td>') +
                                                '<td>' + data.pfno + '</td>' +
                                                '<td>' + Boolean(data.isactive) + '</td>' +
                                                '<td>' + data.role + '</td>' +
                                                '<td>' + new Date(data.created_at).toDateString("en-US") + '</td>' +
                                                '</tr>';
                                            tbody.append(row);
                                        });
                                    }
                                    else {
                                        var row = '<tr><td colspan="5">No Users found</td></tr>';
                                        tbody.append(row);
                                    }
                                }
                                  fetchData();

                                // Search input keyup event
                                $('#searchInput').on('keyup', function () {
                                    var searchTerm = $(this).val().toLowerCase();
                                    if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
                                        searchData(searchTerm);
                                    } else if (searchTerm.length === 0) {
                                        fetchData(); // Fetch all data when search input is empty
                                    }
                                });
                            });
                        </script>


                    </div>

                </div>
            </div>
        </div>
    @endif
@endauth
@endsection