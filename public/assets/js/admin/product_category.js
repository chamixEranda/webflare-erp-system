$(document).ready(function() {
    const table = $('#basic-btn').DataTable({
        dom: 'Brtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        processing: true,
        serverSide: true,
        language: {
            info: "Showing _START_–_END_ of _TOTAL_ categories",
            infoEmpty: "Showing 0–0 of 0 categories",
            infoFiltered: "(filtered from _MAX_ total categories)",
            zeroRecords: "No categories found",
            emptyTable: "No categories found",
            paginate: {
                previous: "‹",
                next: "›"
            }
        },
        ajax: {
            url: '/admin/product-catalog/categories/list',
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
            { data: 'parent_id' },
            { data: 'slug' },
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

new SlimSelect({
    select: '#parentCategory'
});

const parentCategorySlim = new SlimSelect({
    select: '#parentCategoryEdit'
});

const createProductCategoryForm = document.getElementById('createProductCategoryForm');

function preloadState(buttonId, loading) {
    const button = document.getElementById(buttonId);
    button.disabled = loading;
    button.innerHTML = loading ? '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...' : 'Submit';
}

createProductCategoryForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    preloadState('createProductCategorySubmitBtn', true);
    const formData = new FormData(this);

    try {
        const url = createProductCategoryForm.action;
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
            // Close Bootstrap Modal
            $('#createProductCategoryModal').modal('hide');

            // Refresh table or append new row
           toastr.success(data.message || "Category created successfully.");
           window.location.reload(); // Reload the page to reflect changes
        } else {
            console.log(data);
            toastr.error(data.message || "Something went wrong.");
        }

    } catch (error) {
        console.error("Error:", error);
        toastr.error("An unexpected error occurred.");
    } finally {
        preloadState('createProductCategorySubmitBtn', false);
    }
});

const categoryImageEdit = document.getElementById('categoryImageEdit');
categoryImageEdit.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('categoryImagePreviewEditImg').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

const categoryImage = document.getElementById('categoryImage');
categoryImage.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
                document.getElementById('categoryImagePreview').classList.remove('d-none');
            document.getElementById('categoryImagePreviewImg').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

async function editProductCategory(categoryId) {
    const url = `/admin/product-catalog/categories/${categoryId}/edit`;
    const response = await fetch(url, {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        }
    });

    const result = await response.json();
    if (response.ok && result.status === 200) {
        document.getElementById('editCategoryId').value = result.data.id;
        document.getElementById('categoryNameEdit').value = result.data.name;
        document.getElementById('categorySlugEdit').value = result.data.slug;
        parentCategorySlim.setSelected(result.data.parent_id);
        document.getElementById('categoryDescriptionEdit').value = result.data.description;
        document.getElementById('categoryImagePreviewEditImg').src = result.data.image;
        $('#editProductCategoryModal').modal('show');
    }
}

const editProductCategoryForm = document.getElementById('editProductCategoryForm');
editProductCategoryForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    preloadState('editProductCategorySubmit', true);

    const categoryId = document.getElementById('editCategoryId').value;
    const formData = new FormData(this);

    try {
        const url = `/admin/product-catalog/categories/${categoryId}`;
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
            toastr.success(result.message || "Category updated successfully.");
            $('#editProductCategoryModal').modal('hide');
           window.location.reload(); // Reload the page to reflect changes
        } else {
            toastr.error(result.message || "Something went wrong.");
        }
    } catch (error) {
        console.error("Error:", error);
        toastr.error("An unexpected error occurred.");
    } finally {
        preloadState('editProductCategorySubmit', false);
    }
});