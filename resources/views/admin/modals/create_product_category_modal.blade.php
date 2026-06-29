<!-- Modal -->
<div class="modal fade" id="createProductCategoryModal" tabindex="-1" aria-labelledby="createProductCategoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createProductCategoryLabel">Create Product Category</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createProductCategoryForm" action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="categoryName" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="categoryName" name="name" required>
          </div>
          <div class="mb-3">
            <label for="categorySlug" class="form-label">Slug</label>
            <input type="text" class="form-control" id="categorySlug" name="slug">
          </div>
          <div class="mb-3">
            <label for="parentCategory" class="form-label">Parent Category</label>
            <select id="parentCategory" name="parent_id">
              <option value="">-- Select Parent Category --</option>
              @foreach(\App\Models\ProductCategory::whereNull('parent_id')->where('is_active', true)->get() as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="categoryDescription" class="form-label">Description</label>
            <textarea class="form-control" id="categoryDescription" name="description" rows="4"></textarea>
          </div>
          <div class="mb-3">
            <label for="categoryImage" class="form-label">Image</label>
            <input type="file" class="form-control" id="categoryImage" name="image" accept="image/*">
            <div id="categoryImagePreview" class="mt-2 d-none">
              <img src="" id="categoryImagePreviewImg" alt="Category Image" width="100" height="100">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="createProductCategorySubmitBtn">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>