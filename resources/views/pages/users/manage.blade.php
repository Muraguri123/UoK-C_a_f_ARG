@extends('layouts.master')

@section('content')
<div class="row">
    <style>
        #searchInput::placeholder {
            color: #cdc8c8;
            /* Change to your desired color */
        }
    </style>

    <div class="row form-group" style="padding-top:4px">
        @if (auth()->user()->haspermission('canviewallusers'))
            <form class="form-horizontal">
                <div class="row form-group">
                    <div class="col-12">

                        <input type="text" id="searchInput" class="form-control text-center"
                            style="::placeholder { color: red; }"
                            placeholder="Search by User Name, Email, PFNO or Is Active Status">
                    </div>
                </div>
            </form>

            <table id="proposalstable" class="table table-responsive table-bordered table-striped table-hover"
                style="margin:4px">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">PFNO</th>
                        <th scope="col">Can Login</th>
                        <th scope="col">Role</th>
                        <th scope="col">Date Created</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        @endif
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
                        (canviewuser ? '<td><a class="nav-link pt-0 pb-0" href="' +  userurl  + '">' + data.email + '</a></td>'  : '<td>' + data.email + '</td>') +
                        '<td>' + data.pfno + '</td>' +
                        '<td>' + Boolean(data.isactive) + '</td>' +
                        '<td>' + getrolename(data.role) + '</td>' +
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
        function getrolename(roleid){
            if(roleid==1){
                return 'Committee';
            }
            else if(roleid==2){
                return 'Researcher';
            }  
            else if(roleid==3){
                return 'Co-opted';
            }
            else{
                return 'unknown';
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
@endsection