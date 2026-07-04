<!-- Modal -->
<div class="modal fade" id="editProductBrandModal" tabindex="-1" aria-labelledby="editProductBrandLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editProductBrandLabel">Edit Product Brand</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editProductBrandForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <input type="hidden" id="editBrandId" name="brand_id">
          <div class="mb-3">
            <label for="brandNameEdit" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="brandNameEdit" name="name" required>
          </div>
          <div class="mb-3">
            <label for="brandImageEdit" class="form-label">Image</label>
            <input type="file" class="form-control" id="brandImageEdit" name="image" accept="image/*">
            <div id="brandImagePreviewEdit" class="mt-2">
              <img src="" id="brandImagePreviewEditImg" alt="Brand Image" width="100" height="100" style="object-fit: cover; border-radius: 4px;">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="editProductBrandSubmit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
