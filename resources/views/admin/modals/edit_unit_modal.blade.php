<!-- Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editUnitLabel">Edit Unit</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editUnitForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <input type="hidden" id="editUnitId" name="unit_id">
          <div class="mb-3">
            <label for="unitNameEdit" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="unitNameEdit" name="name" required>
          </div>
          <div class="mb-3">
            <label for="unitShortNameEdit" class="form-label">Short Name</label>
            <input type="text" class="form-control" id="unitShortNameEdit" name="short_name">
          </div>
          <div class="mb-3">
            <label for="unitUomTypeEdit" class="form-label">UOM Type <span class="text-danger">*</span></label>
            <select id="unitUomTypeEdit" name="uom_type" required>
              <option value="">-- Select UOM Type --</option>
              @foreach(\App\Enums\UomType::all() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="unitBaseUnitEdit" class="form-label">Base Unit</label>
            <select id="unitBaseUnitEdit" name="base_unit_id">
              <option value="">-- No Base Unit (Is Base Unit itself) --</option>
            </select>
            <small class="form-text text-muted">Select a base unit if this is a sub-unit (e.g. Gram is a sub-unit of Kilogram).</small>
          </div>
          <div class="mb-3" id="conversionFactorContainerEdit">
            <label for="unitConversionFactorEdit" class="form-label">Conversion Factor</label>
            <input type="number" step="0.00000001" class="form-control" id="unitConversionFactorEdit" name="conversion_factor" value="1" min="0.00000001">
            <small class="form-text text-muted">How many of this unit equals one Base Unit?</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="editUnitSubmitBtn">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
