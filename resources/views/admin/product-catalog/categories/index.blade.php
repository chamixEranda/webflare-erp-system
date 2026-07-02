@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        {{-- Header Card --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body py-3.5 px-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <h5 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                                        <span class="d-inline-flex align-items-center justify-content-center bg-primary rounded-2 text-white" style="width:30px; height:30px;">
                                            <i class="ti ti-folders" style="font-size:1rem;"></i>
                                        </span>
                                        Categories
                                    </h5>
                                </div>
                                <div>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#createProductCategoryModal" class="btn btn-primary d-flex align-items-center gap-1 px-3" style="height: 38px; border-radius: 8px; font-size: 0.85rem;">
                                        <i class="ti ti-plus"></i> Add Category
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Table Card --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-0 bg-transparent py-3 px-4 pb-0">
                            {{-- Row 2: Inline controls --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                {{-- Left side: perPage selector --}}
                                <div class="d-flex align-items-center gap-2" style="width: 150px;">
                                    <select id="userTablePerPage" class="form-select form-select-sm" style="width: 75px; border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; height: 38px; outline: none; background-color: #fff;">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>

                                {{-- Centered Search Box --}}
                                <div class="position-relative mx-auto" style="width: 100%; max-width: 320px;">
                                    <input type="text" id="userTableSearch" class="form-control" placeholder="Search categories..." style="border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 7px 14px 7px 36px; font-size: 0.85rem; outline: none; transition: border-color 0.2s; height: 38px; width: 100%;">
                                    <i class="ti ti-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px; font-size: 0.95rem;"></i>
                                </div>

                                {{-- Export Options (Three inline buttons showing icons) --}}
                                <div class="d-flex align-items-center gap-2 ms-auto ms-md-0" style="width: 150px; justify-content: flex-end;">
                                    <button type="button" class="btn btn-light btn-icon border" id="exportCsvBtn" title="Export CSV" style="width: 38px; height: 38px; border-radius: 8px; background: #fff; color: #2e7d32; border-color: #e2e8f0 !important;">
                                        <i class="ti ti-file-text" style="font-size: 1.2rem;"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-icon border" id="exportExcelBtn" title="Export Excel" style="width: 38px; height: 38px; border-radius: 8px; background: #fff; color: #1565c0; border-color: #e2e8f0 !important;">
                                        <i class="ti ti-file-analytics" style="font-size: 1.2rem;"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-icon border" id="exportPdfBtn" title="Export PDF" style="width: 38px; height: 38px; border-radius: 8px; background: #fff; color: #c62828; border-color: #e2e8f0 !important;">
                                        <i class="ti ti-file-report" style="font-size: 1.2rem;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-4 pt-3">
                            <div class="table-responsive">
                                <table id="basic-btn" class="table mb-0 w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Parent Category</th>
                                            <th>Slug</th>
                                            <th class="text-center not-exported" style="width: 110px; text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="categoriesTableBody">
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
        </div>
    </div>

@include('admin.modals.create_product_category_modal')
@include('admin.modals.edit_product_category_modal')
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/custom-datatable.css') }}?v={{ time() }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/js/admin/product_category.js') }}"></script>
@endpush