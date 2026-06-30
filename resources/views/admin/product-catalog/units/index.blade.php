@extends('admin.layouts.app')

@section('title', 'Units')

@section('content')

    <section class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Product Catalog</a></li>
                                <li class="breadcrumb-item" aria-current="page">Units</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Units</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header table-card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5>Units</h5>
                                <small>List of all product units in the system.</small>
                            </div>
                            <div>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#createUnitModal" class="btn btn-primary"><i class="ti ti-plus"></i> Add Unit</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="dt-responsive table-responsive">
                                <table id="units-table" class="table table-striped table-bordered nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th class="not-exported"></th>
                                            <th>Name</th>
                                            <th>Short Name</th>
                                            <th>UOM Type</th>
                                            <th>Base Unit</th>
                                            <th>Conversion Factor</th>
                                            <th class="not-exported">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="unitsTableBody">
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('admin.modals.create_unit_modal')
@include('admin.modals.edit_unit_modal')
@endsection
@push('scripts')
<script src="{{ asset('assets/js/admin/unit.js') }}?v={{ time() }}"></script>
@endpush
