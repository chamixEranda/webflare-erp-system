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

        {{-- Stats Row --}}


        {{-- Header Card --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3.5 px-4">
                {{-- Row 1: Title and Add User --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h5 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center bg-primary rounded-2 text-white" style="width:30px; height:30px;">
                                <i class="ti ti-users" style="font-size:1rem;"></i>
                            </span>
                            All Users
                        </h5>
                    </div>

                    @can('add users')
                        <button type="button"
                            class="btn btn-primary d-flex align-items-center gap-1 px-3"
                            data-bs-toggle="modal"
                            data-bs-target="#addUserModal"
                            style="height: 38px; border-radius: 8px; font-size: 0.85rem;">
                            <i class="ti ti-user-plus"></i>
                            <span>Add User</span>
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
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
                        <input type="text" id="userTableSearch" class="form-control" placeholder="Search users..." style="border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 7px 14px 7px 36px; font-size: 0.85rem; outline: none; transition: border-color 0.2s; height: 38px; width: 100%;">
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
                <div class="table-responsive" id="users-table-wrapper">
                    <table id="usersTable" class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th style="width: 120px;">Status</th>
                                <th class="text-center" style="width: 110px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $avatarColors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary'];
                            @endphp
                            @forelse($users as $index => $user)
                                @php $color = $avatarColors[$index % count($avatarColors)]; @endphp
                                <tr class="align-middle user-row">
                                    <td class="text-muted fw-normal" style="font-size:0.82rem;">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="user-avatar bg-light-{{ $color }} text-{{ $color }} rounded-circle fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width:40px; height:40px; font-size:0.95rem;">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-semibold lh-1" style="font-size:0.92rem;">{{ $user->name }}</p>
                                                <small class="text-muted" style="font-size:0.77rem;">ID #{{ $user->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-mail text-muted" style="font-size:0.9rem;"></i>
                                            <span class="text-muted" style="font-size:0.87rem;">{{ $user->email }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @forelse($user->roles as $role)
                                            <span class="badge rounded-pill px-3 py-1 bg-light-primary text-primary fw-semibold me-1" style="font-size:0.77rem;">
                                                <i class="ti ti-shield me-1" style="font-size:0.72rem;"></i>{{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="text-muted" style="font-size:0.82rem;">—</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if(auth()->id() === $user->id)
                                            <span class="badge bg-light-success text-success px-3 py-1 rounded-pill fw-semibold" style="font-size:0.77rem;">
                                                <span class="me-1" style="display:inline-block; width:6px; height:6px; background:currentColor; border-radius:50%; vertical-align:middle;"></span>
                                                Active (You)
                                            </span>
                                        @else
                                            <span class="badge bg-light-secondary text-secondary px-3 py-1 rounded-pill fw-semibold" style="font-size:0.77rem;">
                                                <span class="me-1" style="display:inline-block; width:6px; height:6px; background:currentColor; border-radius:50%; vertical-align:middle;"></span>
                                                Member
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            @if($canManageMap[$user->id])
                                                @can('edit users')
                                                    <button type="button"
                                                        class="btn btn-icon btn-sm rounded-2"
                                                        style="background:rgba(var(--bs-primary-rgb),0.1); color:var(--bs-primary); border:none;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editUserModal{{ $user->id }}"
                                                        title="Edit User">
                                                        <i class="ti ti-edit" style="font-size:1rem;"></i>
                                                    </button>
                                                @endcan
                                                @if(auth()->id() !== $user->id)
                                                    @can('delete users')
                                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline-block delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-icon btn-sm rounded-2"
                                                                style="background:rgba(var(--bs-danger-rgb),0.1); color:var(--bs-danger); border:none;"
                                                                title="Delete User">
                                                                <i class="ti ti-trash" style="font-size:1rem;"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @endif
                                                @if(!auth()->user()->can('edit users') && (!auth()->user()->can('delete users') || auth()->id() === $user->id))
                                                    @if(auth()->id() === $user->id && auth()->user()->can('edit users'))
                                                        {{-- Can edit self but not delete self --}}
                                                    @else
                                                        <span class="text-muted d-inline-flex align-items-center gap-1" style="font-size:0.78rem;">
                                                            <i class="ti ti-lock" style="font-size:0.9rem;"></i>
                                                            Restricted
                                                        </span>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="text-muted d-inline-flex align-items-center gap-1" style="font-size:0.78rem;">
                                                    <i class="ti ti-lock" style="font-size:0.9rem;"></i>
                                                    Protected
                                                </span>
                                            @endif
                                        </div>

                                        @can('edit users')
                                            {{-- Edit Modal --}}
                                            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editLabel{{ $user->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header border-0 pb-0">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="user-avatar bg-light-{{ $color }} text-{{ $color }} rounded-circle fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                                                    style="width:38px; height:38px; font-size:0.9rem;">
                                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                                </div>
                                                                <div>
                                                                    <h6 class="modal-title mb-0" id="editLabel{{ $user->id }}">Edit User</h6>
                                                                    <small class="text-muted">{{ $user->name }}</small>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body pt-3">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-medium" for="edit_name{{ $user->id }}">Full Name</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-user text-muted"></i></span>
                                                                        <input type="text" class="form-control border-start-0 ps-0" id="edit_name{{ $user->id }}" name="name" value="{{ $user->name }}" required placeholder="Full name">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-medium" for="edit_email{{ $user->id }}">Email Address</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail text-muted"></i></span>
                                                                        <input type="email" class="form-control border-start-0 ps-0" id="edit_email{{ $user->id }}" name="email" value="{{ $user->email }}" required placeholder="Email address">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-medium" for="edit_password{{ $user->id }}">
                                                                        New Password
                                                                        <span class="text-muted fw-normal" style="font-size:0.8rem;">(leave blank to keep)</span>
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-lock text-muted"></i></span>
                                                                        <input type="password" class="form-control border-start-0 ps-0" id="edit_password{{ $user->id }}" name="password" placeholder="Min. 8 characters">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-1">
                                                                    <label class="form-label fw-medium" for="edit_role{{ $user->id }}">Assign Role</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-shield text-muted"></i></span>
                                                                        <select class="form-select border-start-0 ps-0" id="edit_role{{ $user->id }}" name="role" required>
                                                                            @foreach($roles as $role)
                                                                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                                                    {{ $role->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0 pt-0">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary px-4">
                                                                    <i class="ti ti-device-floppy me-1"></i> Save Changes
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="ti ti-users-off d-block mb-2" style="font-size:2.5rem;"></i>
                                        <p class="mb-1 fw-medium">No users found</p>
                                        <small>Click "Add User" to create the first user.</small>
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

@can('add users')
{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center rounded-2 bg-primary text-white" style="width:36px; height:36px;">
                        <i class="ti ti-user-plus" style="font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title mb-0" id="addUserModalLabel">Create New User</h6>
                        <small class="text-muted">Fill in the details to add a new user.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-medium" for="add_name">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-user text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="add_name" name="name" required placeholder="e.g. John Doe">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium" for="add_email">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail text-muted"></i></span>
                            <input type="email" class="form-control border-start-0 ps-0" id="add_email" name="email" required placeholder="e.g. john@example.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium" for="add_password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-lock text-muted"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0" id="add_password" name="password" required placeholder="Min. 8 characters">
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-medium" for="add_role">Assign Role</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-shield text-muted"></i></span>
                            <select class="form-select border-start-0 ps-0" id="add_role" name="role" required>
                                <option value="" disabled selected>— Select a role —</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-user-plus me-1"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/custom-datatable.css') }}?v={{ time() }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ── DataTable ──────────────────────────────────────────
        const tableEl = document.getElementById('usersTable');
        if (tableEl) {
            const dataTable = new simpleDatatables.DataTable(tableEl, {
                searchable: false,
                fixedHeight: false,
                perPage: 10,
                perPageSelect: false, // Disable simple-datatables built-in per page select to use our custom inline select
                columns: [
                    { select: 0, sortable: false },  // # column
                    { select: 5, sortable: false },  // Actions column
                ],
                labels: {
                    noRows: "No users found",
                    info: "Showing {start}–{end} of {rows} users"
                }
            });

            // Inline search integration
            const searchInput = document.getElementById('userTableSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    dataTable.search(searchInput.value);
                });
            }

            // Custom Per Page select integration
            const perPageSelect = document.getElementById('userTablePerPage');
            if (perPageSelect) {
                perPageSelect.addEventListener('change', function() {
                    dataTable.options.perPage = parseInt(perPageSelect.value, 10);
                    dataTable.update();
                });
            }

            // Export CSV integration
            const exportCsvBtn = document.getElementById('exportCsvBtn');
            if (exportCsvBtn) {
                exportCsvBtn.addEventListener('click', function() {
                    simpleDatatables.exportCSV(dataTable, {
                        download: true,
                        skipColumn: [0, 5],
                        filename: 'users_export_' + new Date().toISOString().slice(0, 10)
                    });
                });
            }

            // Export Excel integration
            const exportExcelBtn = document.getElementById('exportExcelBtn');
            if (exportExcelBtn) {
                exportExcelBtn.addEventListener('click', function() {
                    // Extract active headings and rows (skipping sequence # and actions columns)
                    const headings = dataTable.data.headings.filter((h, idx) => idx !== 0 && idx !== 5).map(h => h.text ?? h.data);
                    const rows = dataTable.data.data.map(row => row.filter((cell, idx) => idx !== 0 && idx !== 5).map(cell => cell.text ?? cell.data));

                    // Generate Excel HTML XML format (escaped tags to prevent early script closing or parser breaks)
                    let html = '<' + 'html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
                    html += '<' + 'head><!--[if gte mso 9]><xml><x' + ':ExcelWorkbook><x' + ':ExcelWorksheets><x' + ':ExcelWorksheet><x' + ':Name>Users</x' + ':Name><x' + ':WorksheetOptions><x' + ':DisplayGridlines/></x' + ':WorksheetOptions></x' + ':ExcelWorksheet></x' + ':ExcelWorksheets></x' + ':ExcelWorkbook></xml><![endif]--><meta charset="UTF-8"><' + '/head>';
                    html += '<' + 'body><' + 'table border="1">';
                    html += '<' + 'thead><' + 'tr style="background-color: #5c6bc0; color: #ffffff; font-weight: bold;">';
                    headings.forEach(h => {
                        html += '<' + 'th>' + h + '<' + '/th>';
                    });
                    html += '<' + '/tr><' + '/thead><' + 'tbody>';
                    rows.forEach(row => {
                        html += '<' + 'tr>';
                        row.forEach(cell => {
                            html += '<' + 'td>' + cell + '<' + '/td>';
                        });
                        html += '<' + '/tr>';
                    });
                    html += '<' + '/tbody><' + '/table><' + '/body><' + '/html>';

                    const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'users_export_' + new Date().toISOString().slice(0, 10) + '.xls';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                });
            }

            // Export PDF integration
            const exportPdfBtn = document.getElementById('exportPdfBtn');
            if (exportPdfBtn) {
                exportPdfBtn.addEventListener('click', function() {
                    const headings = dataTable.data.headings.filter((h, idx) => idx !== 0 && idx !== 5).map(h => h.text ?? h.data);
                    const rows = dataTable.data.data.map(row => row.filter((cell, idx) => idx !== 0 && idx !== 5).map(cell => cell.text ?? cell.data));

                    const printWindow = window.open('', '_blank');
                    printWindow.document.write('<' + 'html><' + 'head><' + 'title>Users Export<' + '/title>');
                    printWindow.document.write('<' + 'style>');
                    printWindow.document.write('body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; padding: 30px; color: #333; }');
                    printWindow.document.write('h2 { color: #5c6bc0; font-weight: 600; margin-bottom: 20px; font-size: 24px; }');
                    printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 10px; }');
                    printWindow.document.write('th, td { border: 1px solid #e2e8f0; padding: 12px; text-align: left; font-size: 13px; }');
                    printWindow.document.write('th { background-color: #f8fafc; font-weight: 600; color: #475569; }');
                    printWindow.document.write('tr:nth-child(even) { background-color: #f8fafc; }');
                    printWindow.document.write('<' + '/style><' + '/head><' + 'body>');
                    printWindow.document.write('<h2>Users List</h2>');
                    printWindow.document.write('<' + 'table><' + 'thead><' + 'tr>');
                    headings.forEach(h => {
                        printWindow.document.write('<' + 'th>' + h + '<' + '/th>');
                    });
                    printWindow.document.write('<' + '/tr><' + '/thead><' + 'tbody>');
                    rows.forEach(row => {
                        printWindow.document.write('<' + 'tr>');
                        row.forEach(cell => {
                            printWindow.document.write('<' + 'td>' + cell + '<' + '/td>');
                        });
                        printWindow.document.write('<' + '/tr>');
                    });
                    printWindow.document.write('<' + '/tbody><' + '/table>');
                    printWindow.document.write('<' + '/body><' + '/html>');
                    printWindow.document.close();
                    
                    // Trigger print dialog
                    setTimeout(() => {
                        printWindow.print();
                        printWindow.close();
                    }, 250);
                });
            }
        }

        // ── Delete confirmation ────────────────────────────────
        document.querySelectorAll('.delete-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
