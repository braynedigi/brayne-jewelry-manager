<!-- Add Metal Modal -->
<div class="modal fade" id="addMetalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Metal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMetalForm" method="POST" action="{{ route('products.metals.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="metal_name" class="form-label">Metal Name *</label>
                        <input type="text" class="form-control" id="metal_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="metal_description" class="form-label">Description</label>
                        <textarea class="form-control" id="metal_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="metal_is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="metal_is_active">
                                Active Metal
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive metals won't appear in product forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Metal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Metal Modal -->
<div class="modal fade" id="editMetalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Metal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMetalForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_metal_name" class="form-label">Metal Name *</label>
                        <input type="text" class="form-control" id="edit_metal_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_metal_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_metal_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_metal_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_metal_is_active">
                                Active Metal
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive metals won't appear in product forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Metal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Metal Modal -->
<div class="modal fade" id="deleteMetalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Delete Metal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteMetalName"></strong>?</p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteMetalForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Metal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Metal management functions
function editMetal(id, name, description, isActive) {
    document.getElementById('edit_metal_name').value = name;
    document.getElementById('edit_metal_description').value = description || '';
    document.getElementById('edit_metal_is_active').checked = isActive;
    
    document.getElementById('editMetalForm').action = `/products/metals/${id}`;
    
    new bootstrap.Modal(document.getElementById('editMetalModal')).show();
}

function deleteMetal(id, name) {
    document.getElementById('deleteMetalName').textContent = name;
    document.getElementById('deleteMetalForm').action = `/products/metals/${id}`;
    
    new bootstrap.Modal(document.getElementById('deleteMetalModal')).show();
}

// Handle form submissions
document.getElementById('addMetalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the metal.');
    });
});

document.getElementById('editMetalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the metal.');
    });
});

document.getElementById('deleteMetalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the metal.');
    });
});
</script> 