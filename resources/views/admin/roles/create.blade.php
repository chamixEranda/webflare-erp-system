@extends('admin.layouts.app')

@section('content')
@php
    $modules = [];
    $actionMap = [
        'view' => 'view', 'read' => 'view', 'show' => 'view',
        'add' => 'add', 'create' => 'add',
        'edit' => 'edit', 'update' => 'edit',
        'delete' => 'delete', 'destroy' => 'delete'
    ];

    foreach ($permissions as $permission) {
        $name = $permission->name;
        $action = '';
        $module = '';

        if (str_contains($name, ' ')) {
            $parts = explode(' ', $name, 2);
            $action = strtolower($parts[0]);
            $module = ucwords($parts[1]);
        } elseif (str_contains($name, '.')) {
            $parts = explode('.', $name, 2);
            $action = strtolower($parts[1]);
            $module = ucwords($parts[0]);
        } elseif (str_contains($name, ':')) {
            $parts = explode(':', $name, 2);
            $action = strtolower($parts[1]);
            $module = ucwords($parts[0]);
        } elseif (str_contains($name, '-')) {
            $parts = explode('-', $name, 2);
            $action = strtolower($parts[1]);
            $module = ucwords($parts[0]);
        } else {
            $action = $name;
            $module = 'General';
        }

        if (array_key_exists($action, $actionMap)) {
            $mappedAction = $actionMap[$action];
            $modules[$module][$mappedAction] = $permission;
        } else {
            $modules[$module]['other'][] = $permission;
        }
    }

    if (! auth()->user()->hasRole('Super Admin')) {
        unset($modules['Roles']);
        unset($modules['Users']);
    }
@endphp

<div class="pc-container">
    <div class="pc-content">

        {{-- Main Form Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent py-4 px-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-icon btn-light rounded-circle" style="width: 38px; height: 38px;">
                            <i class="ti ti-arrow-left fs-5"></i>
                        </a>
                        <div>
                            <h5 class="mb-1 fw-semibold d-flex align-items-center gap-2">
                                Create New Role
                            </h5>
                            <small class="text-muted">Define a role name and configure its permissions matrix.</small>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="card-body px-4 pt-0 pb-4">
                    <div class="mb-4">
                        <label class="form-label fw-medium" for="add_name">Role Name</label>
                        <div class="input-group" style="max-width: 500px;">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-shield text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="add_name" name="name" required placeholder="e.g. Manager">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium mb-3">Group Permissions</label>
                        
                        <div class="table-responsive border rounded-3 bg-white">
                            <table class="table align-middle mb-0" id="permissionMatrixTable">
                                <thead class="bg-light-50">
                                    <tr>
                                        <th style="width: 20%; padding: 12px 16px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #6c757d;">Module Name</th>
                                        <th class="text-center" style="width: 12%; padding: 12px 16px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #6c757d;">View</th>
                                        <th class="text-center" style="width: 12%; padding: 12px 16px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #6c757d;">Add</th>
                                        <th class="text-center" style="width: 12%; padding: 12px 16px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #6c757d;">Edit</th>
                                        <th class="text-center" style="width: 12%; padding: 12px 16px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #6c757d;">Delete</th>
                                        <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #6c757d;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span>Other</span>
                                                <div class="form-check mb-0 me-2">
                                                    <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                                                    <label class="form-check-label fw-semibold text-muted" style="font-size: 0.77rem;" for="selectAllPermissions">
                                                        Select All
                                                    </label>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $moduleName => $actions)
                                        <tr>
                                            <td class="fw-semibold ps-4" style="font-size: 0.88rem; color: #495057;">{{ $moduleName }}</td>
                                            
                                            {{-- View --}}
                                            <td class="text-center">
                                                @if(isset($actions['view']))
                                                    <div class="d-inline-block">
                                                        <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $actions['view']->name }}" id="perm_{{ $actions['view']->id }}">
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Add --}}
                                            <td class="text-center">
                                                @if(isset($actions['add']))
                                                    <div class="d-inline-block">
                                                        <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $actions['add']->name }}" id="perm_{{ $actions['add']->id }}">
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Edit --}}
                                            <td class="text-center">
                                                @if(isset($actions['edit']))
                                                    <div class="d-inline-block">
                                                        <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $actions['edit']->name }}" id="perm_{{ $actions['edit']->id }}">
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Delete --}}
                                            <td class="text-center">
                                                @if(isset($actions['delete']))
                                                    <div class="d-inline-block">
                                                        <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $actions['delete']->name }}" id="perm_{{ $actions['delete']->id }}">
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Other --}}
                                            <td class="ps-4">
                                                @if(isset($actions['other']))
                                                    <div class="d-flex flex-wrap gap-3">
                                                        @foreach($actions['other'] as $otherPerm)
                                                            <div class="form-check mb-0">
                                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $otherPerm->name }}" id="perm_{{ $otherPerm->id }}">
                                                                <label class="form-check-label text-muted" style="font-size: 0.82rem;" for="perm_{{ $otherPerm->id }}">
                                                                    {{ $otherPerm->name }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-2">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-plus me-1"></i> Create Role
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    /* ── Input Polish ───────────────────────────────── */
    .input-group-text {
        border-color: #dee2e6;
    }
    .input-group .form-control {
        border-left: none;
    }
    .input-group .form-control:focus {
        border-color: #dee2e6;
        box-shadow: none;
    }
    .input-group:focus-within .input-group-text {
        border-color: var(--bs-primary);
    }
    .input-group:focus-within .form-control {
        border-color: var(--bs-primary);
        box-shadow: none;
    }
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: background 0.15s ease, transform 0.1s ease;
    }
    .btn-icon:hover {
        transform: scale(1.05);
    }
    /* ── Permission Matrix Table ─────────────────────── */
    #permissionMatrixTable thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    #permissionMatrixTable tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    #permissionMatrixTable tbody tr:last-child td {
        border-bottom: none;
    }
    #permissionMatrixTable tbody tr:hover {
        background-color: #f8fafc;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ── Select All Permissions Toggle ──────────────────────
        const selectAllCb = document.getElementById('selectAllPermissions');
        if (selectAllCb) {
            selectAllCb.addEventListener('change', function (e) {
                const checkboxes = document.querySelectorAll('#permissionMatrixTable .permission-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = e.target.checked;
                });
            });

            // Keep "Select All" checkbox state in sync
            const checkboxes = document.querySelectorAll('#permissionMatrixTable .permission-checkbox');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    const noneChecked = Array.from(checkboxes).every(c => !c.checked);
                    selectAllCb.checked = allChecked;
                    selectAllCb.indeterminate = !allChecked && !noneChecked;
                });
            });
        }
    });
</script>
@endpush
