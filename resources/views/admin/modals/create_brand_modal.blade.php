<!-- Modal -->
<div class="modal fade" id="createProductBrandModal" tabindex="-1" aria-labelledby="createProductBrandLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createProductBrandLabel">Create Product Brand</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createProductBrandForm" action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="brandName" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="brandName" name="name" required>
          </div>
          <div class="mb-3">
            <label for="brandImage" class="form-label">Image</label>
            <input type="file" class="form-control" id="brandImage" name="image" accept="image/*">
            <div id="brandImagePreview" class="mt-2 d-none">
              <img src="" id="brandImagePreviewImg" alt="Brand Image" width="100" height="100" style="object-fit: cover; border-radius: 4px;">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="createProductBrandSubmitBtn">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
