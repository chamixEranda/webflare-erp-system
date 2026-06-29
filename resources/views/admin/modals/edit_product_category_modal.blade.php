<!-- Modal -->
<div class="modal fade" id="editProductCategoryModal" tabindex="-1" aria-labelledby="editProductCategoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editProductCategoryLabel">Edit Product Category</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editProductCategoryForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body">
        <input type="hidden" id="editCategoryId" name="category_id">
          <div class="mb-3">
            <label for="categoryNameEdit" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="categoryNameEdit" name="name" required>
          </div>
          <div class="mb-3">
            <label for="categorySlugEdit" class="form-label">Slug</label>
            <input type="text" class="form-control" id="categorySlugEdit" name="slug">
          </div>
          <div class="mb-3">
            <label for="parentCategoryEdit" class="form-label">Parent Category</label>
            <select id="parentCategoryEdit" name="parent_id">
              <option value="">-- Select Parent Category --</option>
              @foreach(\App\Models\ProductCategory::whereNull('parent_id')->where('is_active', true)->get() as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="categoryDescriptionEdit" class="form-label">Description</label>
            <textarea class="form-control" id="categoryDescriptionEdit" name="description" rows="4"></textarea>
          </div>
          <div class="mb-3">
            <label for="categoryImageEdit" class="form-label">Image</label>
            <input type="file" class="form-control" id="categoryImageEdit" name="image" accept="image/*">
            <div id="categoryImagePreviewEdit" class="mt-2">
                <img src="" id="categoryImagePreviewEditImg" alt="Category Image" width="100" height="100">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="editProductCategorySubmit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>