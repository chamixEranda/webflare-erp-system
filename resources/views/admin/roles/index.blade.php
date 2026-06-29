@extends('admin.layouts.app')

@section('content')
<div class="pc-container">
    <div class="pc-content">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                <i class="ti ti-circle-check fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                <i class="ti ti-alert-circle fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger d-flex align-items-start gap-2 alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                <i class="ti ti-alert-triangle fs-5 mt-1"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Main Table Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent py-3 px-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1 fw-semibold d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center bg-primary rounded-2 text-white" style="width:30px; height:30px;">
                                <i class="ti ti-shield" style="font-size:1rem;"></i>
                            </span>
                            All Roles
                        </h5>
                    </div>
                    @can('add roles')
                        <a href="{{ route('admin.roles.create') }}"
                            class="btn btn-primary d-flex align-items-center gap-1 px-3">
                            <i class="ti ti-plus"></i>
                            <span>Add Role</span>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="card-body px-4 pt-0">
                <div class="table-responsive" id="roles-table-wrapper">
                    <table id="rolesTable" class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Role</th>
                                <th class="text-center" style="width: 150px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $avatarColors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary'];
                            @endphp
                            @forelse($roles as $index => $role)
                                @php $color = $avatarColors[$index % count($avatarColors)]; @endphp
                                <tr class="align-middle role-row">
                                    <td class="text-muted fw-normal" style="font-size:0.82rem;">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="role-avatar bg-light-{{ $color }} text-{{ $color }} rounded-circle fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width:40px; height:40px; font-size:0.95rem;">
                                                {{ strtoupper(substr($role->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-semibold lh-1" style="font-size:0.92rem;">{{ $role->name }}</p>
                                                <small class="text-muted" style="font-size:0.77rem;">ID #{{ $role->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            @if($role->name !== 'Super Admin')
                                                @can('edit roles')
                                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                                        class="btn btn-icon btn-sm rounded-2"
                                                        style="background:rgba(var(--bs-primary-rgb),0.1); color:var(--bs-primary); border:none;"
                                                        title="Edit Role">
                                                        <i class="ti ti-edit" style="font-size:1rem;"></i>
                                                    </a>
                                                @endcan
                                                @can('delete roles')
                                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline-block delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-icon btn-sm rounded-2"
                                                            style="background:rgba(var(--bs-danger-rgb),0.1); color:var(--bs-danger); border:none;"
                                                            title="Delete Role">
                                                            <i class="ti ti-trash" style="font-size:1rem;"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @if(!auth()->user()->can('edit roles') && !auth()->user()->can('delete roles'))
                                                    <span class="text-muted d-inline-flex align-items-center gap-1" style="font-size:0.78rem;">
                                                        <i class="ti ti-lock" style="font-size:0.9rem;"></i>
                                                        Restricted
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted d-inline-flex align-items-center gap-1" style="font-size:0.78rem;">
                                                    <i class="ti ti-lock" style="font-size:0.9rem;"></i>
                                                    System Role
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="ti ti-shield-off d-block mb-2" style="font-size:2.5rem;"></i>
                                        <p class="mb-1 fw-medium">No roles found</p>
                                        <small>Click "Add Role" to create the first role.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
<style>
    /* ── Table custom styles ───────────────────────── */
    #rolesTable thead th {
        font-size: 0.73rem;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #6c757d;
        font-weight: 600;
        border-bottom: 2px solid #f0f2f5;
        padding: 12px 16px;
        white-space: nowrap;
    }
    #rolesTable tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f6f8fa;
        vertical-align: middle;
    }
    #rolesTable tbody tr:last-child td {
        border-bottom: none;
    }
    #rolesTable tbody tr.role-row {
        transition: background 0.15s ease;
    }
    #rolesTable tbody tr.role-row:hover {
        background-color: #f9fafb;
    }
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: filter 0.15s ease, transform 0.1s ease;
    }
    .btn-icon:hover {
        filter: brightness(0.9);
        transform: scale(1.07);
    }
    /* ── DataTable controls ────────────────────────── */
    .datatable-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 4px 16px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .datatable-search input {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 14px 6px 36px;
        font-size: 0.85rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 10px center;
        width: 220px;
        transition: border-color 0.2s;
        outline: none;
    }
    .datatable-search input:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.12);
    }
    .datatable-selector {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 0.85rem;
        color: #495057;
        background: #fff;
    }
    .datatable-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 4px 4px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .datatable-info {
        font-size: 0.82rem;
        color: #6c757d;
    }
    .datatable-pagination ul {
        display: flex;
        gap: 4px;
        align-items: center;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .datatable-pagination li a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 8px;
        font-size: 0.83rem;
        color: #495057;
        border: 1.5px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.15s;
    }
    .datatable-pagination li a:hover {
        background: var(--bs-primary);
        color: #fff;
        border-color: var(--bs-primary);
    }
    .datatable-pagination .datatable-active a {
        background: var(--bs-primary);
        color: #fff;
        border-color: var(--bs-primary);
        font-weight: 600;
    }
    .datatable-pagination .datatable-disabled a {
        opacity: 0.4;
        pointer-events: none;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ── DataTable ──────────────────────────────────────────
        const tableEl = document.getElementById('rolesTable');
        if (tableEl) {
            new simpleDatatables.DataTable(tableEl, {
                searchable: true,
                fixedHeight: false,
                perPage: 10,
                perPageSelect: [5, 10, 25, 50],
                columns: [
                    { select: 0, sortable: false },  // # column
                    { select: 2, sortable: false },  // Actions column
                ],
                labels: {
                    placeholder: "Search roles...",
                    noRows: "No roles found",
                    info: "Showing {start}–{end} of {rows} roles",
                    perPage: ""
                }
            });
        }

        // ── Delete confirmation ────────────────────────────────
        document.querySelectorAll('.delete-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
