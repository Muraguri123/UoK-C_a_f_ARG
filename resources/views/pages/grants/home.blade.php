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

            <!--add grant Modal -->
            <div class="modal fade" id="addgrantmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Add Call for Grants</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="form_addgrant" method="POST">
                                @csrf 
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class="form-control-label">Financial Year</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" name="title" placeholder="Title for the grant"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="row form-group mt-2">
                                    <div class="col col-md-3">
                                        <label class="form-control-label">Financial Year</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" name="finyear" placeholder="Financial Year"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="row form-group mt-2">
                                    <div class="col col-md-3">
                                        <label class="form-control-label">Status</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <select type="text" name="status" class="form-control">

                                            <option>Open</option>
                                            <option>Closed</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button id="btn_closegrantmodal" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button id="btn_savegrant" type="button"  class="btn btn-primary">Save Grant</button>
                        </div>
                    </div>
                </div>
            </div>
            <form class="form-horizontal">
                <div class="row form-group">
                    <div class="col-lg-10 col-md-9 col-sm-9 col-xs-6">

                        <input type="text" id="searchInput" class="form-control text-center"
                            style="::placeholder { color: red; }" placeholder="Search by Grant No, Year or Status">
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">
                    @if (auth()->user()->haspermission('canaddgrant'))
                    <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                            data-bs-target="#addgrantmodal">
                            Add Grant
                        </button>
                    @endif
                    </div>
                </div>
            </form>

            <table id="grantstable" class="table table-responsive table-bordered table-striped table-hover"
                style="margin:4px">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th scope="col">Grant No</th>
                        <th scope="col">Title</th>
                        <th scope="col">Fin Year</th>
                        <th scope="col">Status</th>
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
        document.getElementById('btn_savegrant').addEventListener('click', function () {

            var formData = $('#form_addgrant').serialize();
             
            // Function to fetch data using AJAX
            $.ajax({
                url: "{{ route('api.grants.post') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    var closebtn=document.getElementById('btn_closegrantmodal');
                    if(closebtn){closebtn.click();}
                    showtoastmessage(response);
                    fetchgrantsData();
                },
                error: function (xhr, status, error) {
                    var mess = JSON.stringify(xhr.responseJSON.message);
                    var type = JSON.stringify(xhr.responseJSON.type);
                    var result = {
                        message: mess,
                        type: type
                    };
                    showtoastmessage(result);

                    console.error('Error fetching data:', error);
                }
            });
        }); 
        // Function to fetch data using AJAX
        function fetchgrantsData() {
            $.ajax({
                url: "{{ route('api.grants.fetchallgrants') }}",
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
                url: "{{ route('api.grants.fetchsearchgrants') }}",
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

        var routeUrlTemplate = "{{ route('pages.grants.viewgrant', ['id' => '__ID__']) }}";
        // Function to populate table with data
        function populateTable(data) {
            var tbody = $('#grantstable tbody');
            tbody.empty(); // Clear existing table rows
            if (data.length > 0) {
                $.each(data, function (index, data) {
                    var granturl = routeUrlTemplate.replace('__ID__', data.grantid);
                    var row = '<tr>' +
                    '<td>' + data.grantid + '</td>' +
                    '<td><a class="nav-link" href="' + granturl + '">' + data.title + '</a></td>' +
                        '<td>' + data.finyear + '</td>' +
                        '<td>' + data.status + '</td>' +
                        '<td>' + new Date(data.created_at).toDateString("en-US") + '</td>' +
                        '</tr>';
                    tbody.append(row);
                });
            }
            else {
                var row = '<tr><td colspan="5" class="text-center text-dark"><b>No Grants found</b></td></tr>';
                tbody.append(row);
            }
        }

        // Initial fetch when the page loads
        fetchgrantsData();

        // Search input keyup event
        $('#searchInput').on('keyup', function () {
            var searchTerm = $(this).val().toLowerCase();
            if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
                searchData(searchTerm);
            } else if (searchTerm.length === 0) {
                fetchgrantsData(); // Fetch all data when search input is empty
            }
        });
    });
</script>
@endsection