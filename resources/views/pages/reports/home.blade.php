@extends('layouts.master')

@section('content')
<div class="row">
    <div class="text-center">All reports</div>
    <div class="prop-tabcontainer">
        <!-- Nav tabs -->
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-personal-tab" data-bs-toggle="tab"
                    data-bs-target="#panel-basicdetails" type="button" role="tab" aria-controls="panel-basicdetails"
                    aria-selected="true">Proposals</button>
                <button class="nav-link" id="nav-research-tab" data-bs-toggle="tab" data-bs-target="#panel-research"
                    type="button" role="tab" aria-controls="panel-profile" aria-selected="false">School Analysis</button>
                <button class="nav-link" id="nav-collaboration-tab" data-bs-toggle="tab"
                    data-bs-target="#panel-collaboration" type="button" role="tab" aria-controls="panel-collaboration"
                    aria-selected="false">Theme Analysis</button>
                <button class="nav-link" id="nav-finance-tab" data-bs-toggle="tab" data-bs-target="#panel-finance"
                    type="button" role="tab" aria-controls="panel-finance" aria-selected="false">Year & Gender</button>
                <button class="nav-link" id="nav-researchdesign-tab" data-bs-toggle="tab"
                    data-bs-target="#panel-researchdesign" type="button" role="tab" aria-controls="panel-researchdesign"
                    aria-selected="false">Archives</button>
                <button class="nav-link" id="nav-workplan-tab" data-bs-toggle="tab" data-bs-target="#panel-workplan"
                    type="button" role="tab" aria-controls="panel-workplan" aria-selected="false">Workplan</button>

            </div>
        </nav>
    </div>
</div>
@endsection