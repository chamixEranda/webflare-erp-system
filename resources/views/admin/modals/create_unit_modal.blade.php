<!-- Modal -->
<div class="modal fade" id="createUnitModal" tabindex="-1" aria-labelledby="createUnitLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createUnitLabel">Create Unit</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createUnitForm" action="{{ route('admin.units.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="unitName" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="unitName" name="name" required>
          </div>
          <div class="mb-3">
            <label for="unitShortName" class="form-label">Short Name</label>
            <input type="text" class="form-control" id="unitShortName" name="short_name">
          </div>
          <div class="mb-3">
            <label for="unitUomType" class="form-label">UOM Type <span class="text-danger">*</span></label>
            <select id="unitUomType" name="uom_type" required>
              <option value="">-- Select UOM Type --</option>
              @foreach(\App\Enums\UomType::all() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="unitBaseUnit" class="form-label">Base Unit</label>
            <select id="unitBaseUnit" name="base_unit_id">
              <option value="">-- No Base Unit (Is Base Unit itself) --</option>
            </select>
            <small class="form-text text-muted">Select a base unit if this is a sub-unit (e.g. Gram is a sub-unit of Kilogram).</small>
          </div>
          <div class="mb-3" id="conversionFactorContainer">
            <label for="unitConversionFactor" class="form-label">Conversion Factor</label>
            <input type="number" step="0.00000001" class="form-control" id="unitConversionFactor" name="conversion_factor" value="1" min="0.00000001">
            <small class="form-text text-muted">How many of this unit equals one Base Unit? (e.g. 1000 Grams = 1 Kilogram, or 0.001 Kilograms = 1 Gram depending on your base unit definition. Let's define it as: 1 base_unit = conversion_factor * this_unit. So if base unit is Kilogram and this is Gram, conversion factor is 1000).</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="createUnitSubmitBtn">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

