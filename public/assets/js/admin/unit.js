$(document).ready(function() {
    const table = $('#units-table').DataTable({
        dom: 'Brtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        processing: true,
        serverSide: true,
        language: {
            info: "Showing _START_–_END_ of _TOTAL_ units",
            infoEmpty: "Showing 0–0 of 0 units",
            infoFiltered: "(filtered from _MAX_ total units)",
            zeroRecords: "No units found",
            emptyTable: "No units found",
            paginate: {
                previous: "‹",
                next: "›"
            }
        },
        ajax: {
            url: '/admin/product-catalog/units/list',
            type: 'POST',
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            }
        },
        columns: [
            { data: 'key' },
            { data: 'name' },
            { data: 'short_name' },
            { data: 'uom_type' },
            { data: 'base_unit_id' },
            { data: 'conversion_factor' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order:[['1', 'asc']],
        columnDefs: [
            {
                "orderable": false,
                'targets': [0]
            }
        ],
    });

    // Custom inline controls integration
    const searchInput = document.getElementById('userTableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            table.search(this.value).draw();
        });
    }

    const perPageSelect = document.getElementById('userTablePerPage');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            table.page.len(parseInt(this.value, 10)).draw();
        });
    }

    // Export button triggers
    $('#exportCsvBtn').on('click', function() {
        table.button('.buttons-csv').trigger();
    });
    $('#exportExcelBtn').on('click', function() {
        table.button('.buttons-excel').trigger();
    });
    $('#exportPdfBtn').on('click', function() {
        table.button('.buttons-pdf').trigger();
    });

    // Add custom class to pagination row for styling
    $('#units-table').closest('.dataTables_wrapper').find('.dataTables_info').closest('.row').addClass('custom-pagination-row');
});

// Initialize SlimSelects with afterChange events (SlimSelect v4 uses afterChange, not onChange)
const uomTypeSelect = new SlimSelect({
    select: '#unitUomType',
    events: {
        afterChange: (newVal) => {
            const selectedUomType = (newVal && newVal.length > 0) ? newVal[0].value : '';
            updateBaseUnits(selectedUomType, baseUnitSelect);
            toggleConversionFactor('', 'unitConversionFactor', 'conversionFactorContainer');
        }
    }
});

const baseUnitSelect = new SlimSelect({
    select: '#unitBaseUnit',
    events: {
        afterChange: (newVal) => {
            const selectedBaseUnit = (newVal && newVal.length > 0) ? newVal[0].value : '';
            toggleConversionFactor(selectedBaseUnit, 'unitConversionFactor', 'conversionFactorContainer');
        }
    }
});

const uomTypeSelectEdit = new SlimSelect({
    select: '#unitUomTypeEdit',
    events: {
        afterChange: (newVal) => {
            const selectedUomType = (newVal && newVal.length > 0) ? newVal[0].value : '';
            const editUnitId = document.getElementById('editUnitId').value;
            updateBaseUnits(selectedUomType, baseUnitSelectEdit, editUnitId);
            toggleConversionFactor('', 'unitConversionFactorEdit', 'conversionFactorContainerEdit');
        }
    }
});

const baseUnitSelectEdit = new SlimSelect({
    select: '#unitBaseUnitEdit',
    events: {
        afterChange: (newVal) => {
            const selectedBaseUnit = (newVal && newVal.length > 0) ? newVal[0].value : '';
            toggleConversionFactor(selectedBaseUnit, 'unitConversionFactorEdit', 'conversionFactorContainerEdit');
        }
    }
});

// Helper function to update base units dropdown based on UOM type (fetches from server)
async function updateBaseUnits(uomTypeId, slimSelectInstance, currentUnitId = null) {
    const data = [
        { text: '-- No Base Unit (Is Base Unit itself) --', value: '' }
    ];

    if (!uomTypeId) {
        slimSelectInstance.setData(data);
        return;
    }

    try {
        let url = `/admin/product-catalog/units/base-units?uom_type=${encodeURIComponent(uomTypeId)}`;
        if (currentUnitId) {
            url += `&exclude_id=${encodeURIComponent(currentUnitId)}`;
        }

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (response.ok && result.data) {
            result.data.forEach(unit => {
                data.push({ text: `${unit.name} (${unit.short_name || ''})`, value: String(unit.id) });
            });
        }
    } catch (error) {
        console.error('Error fetching base units:', error);
    }

    slimSelectInstance.setData(data);
}

// Helper to toggle conversion factor input visibility/disabled state
function toggleConversionFactor(baseUnitVal, inputId, containerId) {
    const input = document.getElementById(inputId);
    const container = document.getElementById(containerId);
    if (!baseUnitVal) {
        input.value = 1;
        input.disabled = true;
        container.style.display = 'none';
    } else {
        input.disabled = false;
        container.style.display = 'block';
    }
}

// Initial state for creation modal
toggleConversionFactor('', 'unitConversionFactor', 'conversionFactorContainer');

// Ajax Submit for Create Form
const createUnitForm = document.getElementById('createUnitForm');
createUnitForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const submitBtn = document.getElementById('createUnitSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

    const formData = new FormData(this);

    try {
        const url = this.action;
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok && data.status === 201) {
            $('#createUnitModal').modal('hide');
            toastr.success(data.message || "Unit created successfully.");
            window.location.reload();
        } else {
            toastr.error(data.message || "Something went wrong.");
        }
    } catch (error) {
        console.error("Error:", error);
        toastr.error("An unexpected error occurred.");
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit';
    }
});

// Edit Unit Action
async function editUnit(unitId) {
    const url = `/admin/product-catalog/units/${unitId}/edit`;
    const response = await fetch(url, {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        }
    });

    const result = await response.json();
    if (response.ok && result.status === 200) {
        document.getElementById('editUnitId').value = result.data.id;
        document.getElementById('unitNameEdit').value = result.data.name;
        document.getElementById('unitShortNameEdit').value = result.data.short_name || '';
        
        uomTypeSelectEdit.setSelected(result.data.uom_type);
        
        // Update the base unit options filtering out the unit itself
        updateBaseUnits(result.data.uom_type, baseUnitSelectEdit, result.data.id);
        
        baseUnitSelectEdit.setSelected(result.data.base_unit_id || '');
        
        toggleConversionFactor(result.data.base_unit_id, 'unitConversionFactorEdit', 'conversionFactorContainerEdit');
        document.getElementById('unitConversionFactorEdit').value = result.data.conversion_factor;

        $('#editUnitModal').modal('show');
    } else {
        toastr.error(result.message || "Failed to load unit details.");
    }
}

// Ajax Submit for Edit Form
const editUnitForm = document.getElementById('editUnitForm');
editUnitForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const submitBtn = document.getElementById('editUnitSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

    const unitId = document.getElementById('editUnitId').value;
    const formData = new FormData(this);

    try {
        const url = `/admin/product-catalog/units/${unitId}`;
        const response = await fetch(url, {
            method: "POST", // Sending POST with _method=PUT inside FormData
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            },
            body: formData
        });
        const result = await response.json();
        if (response.ok && result.status === 200) {
            toastr.success(result.message || "Unit updated successfully.");
            $('#editUnitModal').modal('hide');
            window.location.reload();
        } else {
            toastr.error(result.message || "Something went wrong.");
        }
    } catch (error) {
        console.error("Error:", error);
        toastr.error("An unexpected error occurred.");
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit';
    }
});

// Delete Unit Action via AJAX
async function deleteUnit(unitId) {
    if (confirm('Are you sure you want to delete this unit?')) {
        try {
            const url = `/admin/product-catalog/units/${unitId}`;
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    _method: "DELETE"
                })
            });

            const result = await response.json();
            if (response.ok && result.status === 200) {
                toastr.success(result.message || "Unit deleted successfully.");
                window.location.reload();
            } else {
                toastr.error(result.message || "Something went wrong.");
            }
        } catch (error) {
            console.error("Error:", error);
            toastr.error("An unexpected error occurred.");
        }
    }
}
