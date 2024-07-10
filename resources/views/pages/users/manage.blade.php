@extends('layouts.master')

@section('content')
<div class="row">
    <style>
        #searchInput::placeholder {
            color: #cdc8c8;
            /* Change to your desired color */
        }
    </style>

    @auth

        <div class="row form-group" style="padding-top:4px">

            <!--Collaborator Modal -->
            <div class="modal fade" id="addgrantmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Add User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="form_collaborators" method="POST" >
                                @csrf
                                <!-- Collaborators details form fields -->
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class="form-control-label">Financial Year</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" id="collaboratorname" placeholder="Financial Year"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class="form-control-label">Status</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <select type="text" id="position" class="form-control">

                                            <option>Open</option>
                                            <option>Closed</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save User</button>
                        </div>
                    </div>
                </div>
            </div>
            <form class="form-horizontal">
                <div class="row form-group">
                    <div class="col-lg-10 col-md-9 col-sm-9 col-xs-6">

                        <input type="text" id="searchInput" class="form-control text-center"
                            style="::placeholder { color: red; }"
                            placeholder="Search by User Name, Email, PFNO or Is Active Status">
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">
                        <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                            data-bs-target="#addgrantmodal">
                            Add User
                        </button>
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
                        <th scope="col">Is Active</th>
                        <th scope="col">Date Created</th> 
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    @endauth
</div>
<script>
    $(document).ready(function () {

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
                        '<td><a class="nav-link pt-0 pb-0" href="' + userurl + '">' + data.email + '</a></td>' +
                        '<td>' + data.pfno + '</td>' +
                        '<td>' + Boolean(data.isactive) + '</td>' +
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
@endsection