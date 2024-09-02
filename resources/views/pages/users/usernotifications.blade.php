@extends('layouts.master')

@section('content')
@if (isset($notificationtype))
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

                <table id="notifiableuserstable" class="table table-responsive table-bordered table-striped table-hover"
                    style="margin:4px">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th scope="col">Name</th> 
                            <th scope="col">PFNO</th>
                            <th scope="col">Role</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            @endif
        </div>
        <script>

            $(document).ready(function () {
                let typeid = "{{ isset($notificationtype) ? $notificationtype->typeuuid : '' }}"; // Check if proposalId is set
                const typeurl = `{{ route('api.notificationtype.fetchtypewiseusers', ['id' => ':id']) }}`.replace(':id', typeid);


                // Function to fetch data using AJAX
                function fetchData() {
                    $.ajax({
                        url: typeurl,
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
                        url: typeurl,
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

                // Function to populate table with data
                function populateTable(data) {
                    var tbody = $('#notifiableuserstable tbody');
                    tbody.empty(); // Clear existing table rows
                    if (data.length > 0) {
                        $.each(data, function (index, data) {
                            var row = '<tr>' +
                                '<td>' + data.applicant?.name + '</td>' +
                                '<td>' + data.pfno + '</td>' +
                                '<td>' + getrolename(data.role) + '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                    }
                    else {
                        var row = '<tr><td colspan="3">No Users found</td></tr>';
                        tbody.append(row);
                    }
                }
                function getrolename(roleid) {
                    if (roleid == 1) {
                        return 'Committee';
                    }
                    else if (roleid == 2) {
                        return 'Researcher';
                    }
                    else if (roleid == 3) {
                        return 'Co-opted';
                    }
                    else {
                        return 'unknown';
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
@endif

@endsection