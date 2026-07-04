$(document).ready(function() {
    const table = $('#basic-btn').DataTable({
        dom: 'Brtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        processing: true,
        serverSide: true,
        language: {
            info: "Showing _START_–_END_ of _TOTAL_ brands",
            infoEmpty: "Showing 0–0 of 0 brands",
            infoFiltered: "(filtered from _MAX_ total brands)",
            zeroRecords: "No brands found",
            emptyTable: "No brands found",
            paginate: {
                previous: "‹",
                next: "›"
            }
        },
        ajax: {
            url: '/admin/product-catalog/brands/list',
            type: 'POST',
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            }
        },
        columns: [
            { data: 'key' },
            { data: 'image' },
            { data: 'name' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order:[['2', 'asc']],
        columnDefs: [
            {
                "orderable": false,
                'targets': [0, 1]
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
    $('#basic-btn').closest('.dataTables_wrapper').find('.dataTables_info').closest('.row').addClass('custom-pagination-row');
});

function preloadState(buttonId, loading) {
    const button = document.getElementById(buttonId);
    if (button) {
        button.disabled = loading;
        button.innerHTML = loading ? '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...' : 'Submit';
    }
}

const createProductBrandForm = document.getElementById('createProductBrandForm');
if (createProductBrandForm) {
    createProductBrandForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        preloadState('createProductBrandSubmitBtn', true);
        const formData = new FormData(this);

        try {
            const url = createProductBrandForm.action;
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
                $('#createProductBrandModal').modal('hide');
                toastr.success(data.message || "Brand created successfully.");
                window.location.reload();
            } else {
                console.log(data);
                toastr.error(data.message || "Something went wrong.");
            }

        } catch (error) {
            console.error("Error:", error);
            toastr.error("An unexpected error occurred.");
        } finally {
            preloadState('createProductBrandSubmitBtn', false);
        }
    });
}

const brandImageEdit = document.getElementById('brandImageEdit');
if (brandImageEdit) {
    brandImageEdit.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('brandImagePreviewEditImg').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
}

const brandImage = document.getElementById('brandImage');
if (brandImage) {
    brandImage.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('brandImagePreview').classList.remove('d-none');
                document.getElementById('brandImagePreviewImg').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
}

async function editProductBrand(brandId) {
    const url = `/admin/product-catalog/brands/${brandId}/edit`;
    const response = await fetch(url, {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        }
    });

    const result = await response.json();
    if (response.ok && result.status === 200) {
        document.getElementById('editBrandId').value = result.data.id;
        document.getElementById('brandNameEdit').value = result.data.name;
        document.getElementById('brandImagePreviewEditImg').src = result.data.image;
        $('#editProductBrandModal').modal('show');
    }
}

const editProductBrandForm = document.getElementById('editProductBrandForm');
if (editProductBrandForm) {
    editProductBrandForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        preloadState('editProductBrandSubmit', true);

        const brandId = document.getElementById('editBrandId').value;
        const formData = new FormData(this);

        try {
            const url = `/admin/product-catalog/brands/${brandId}`;
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: formData
            });
            const result = await response.json();
            if (response.ok && result.status === 200) {
                toastr.success(result.message || "Brand updated successfully.");
                $('#editProductBrandModal').modal('hide');
                window.location.reload();
            } else {
                toastr.error(result.message || "Something went wrong.");
            }
        } catch (error) {
            console.error("Error:", error);
            toastr.error("An unexpected error occurred.");
        } finally {
            preloadState('editProductBrandSubmit', false);
        }
    });
}
