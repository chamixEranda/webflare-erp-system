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


        {{-- Main Table Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent py-3 px-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1 fw-semibold d-flex align-items-center gap-2">
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
                            data-bs-target="#addUserModal">
                            <i class="ti ti-user-plus"></i>
                            <span>Add User</span>
                        </button>
                    @endcan
                </div>
            </div>

            <div class="card-body px-4 pt-0">
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
<style>
    /* ── Table custom styles ───────────────────────── */
    #usersTable thead th {
        font-size: 0.73rem;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #6c757d;
        font-weight: 600;
        border-bottom: 2px solid #f0f2f5;
        padding: 12px 16px;
        white-space: nowrap;
    }
    #usersTable tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f6f8fa;
        vertical-align: middle;
    }
    #usersTable tbody tr:last-child td {
        border-bottom: none;
    }
    #usersTable tbody tr.user-row {
        transition: background 0.15s ease;
    }
    #usersTable tbody tr.user-row:hover {
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
    /* ── Modal polish ──────────────────────────────── */
    .modal-content {
        border-radius: 16px;
    }
    .input-group-text {
        border-color: #dee2e6;
    }
    .input-group .form-control,
    .input-group .form-select {
        border-left: none;
    }
    .input-group .form-control:focus,
    .input-group .form-select:focus {
        border-color: #dee2e6;
        box-shadow: none;
    }
    .input-group:focus-within .input-group-text {
        border-color: var(--bs-primary);
    }
    .input-group:focus-within .form-control,
    .input-group:focus-within .form-select {
        border-color: var(--bs-primary);
        box-shadow: none;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ── DataTable ──────────────────────────────────────────
        const tableEl = document.getElementById('usersTable');
        if (tableEl) {
            new simpleDatatables.DataTable(tableEl, {
                searchable: true,
                fixedHeight: false,
                perPage: 10,
                perPageSelect: [5, 10, 25, 50],
                columns: [
                    { select: 0, sortable: false },  // # column
                    { select: 5, sortable: false },  // Actions column
                ],
                labels: {
                    placeholder: "Search users...",
                    noRows: "No users found",
                    info: "Showing {start}–{end} of {rows} users",
                    perPage: ""
                }
            });
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
