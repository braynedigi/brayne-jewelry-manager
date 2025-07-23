<!-- Add Ring Size Modal -->
<div class="modal fade" id="addRingSizeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Ring Size
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRingSizeForm" method="POST" action="{{ route('products.ring-sizes.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ring_size_size" class="form-label">Ring Size *</label>
                        <input type="text" class="form-control" id="ring_size_size" name="size" required>
                        <small class="form-text text-muted">e.g., 6, 6.5, 7, 7.5, etc.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ring_size_description" class="form-label">Description</label>
                        <textarea class="form-control" id="ring_size_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ring_size_is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="ring_size_is_active">
                                Active Ring Size
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive ring sizes won't appear in product forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Ring Size
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Ring Size Modal -->
<div class="modal fade" id="editRingSizeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Ring Size
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRingSizeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_ring_size_size" class="form-label">Ring Size *</label>
                        <input type="text" class="form-control" id="edit_ring_size_size" name="size" required>
                        <small class="form-text text-muted">e.g., 6, 6.5, 7, 7.5, etc.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_ring_size_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_ring_size_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_ring_size_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_ring_size_is_active">
                                Active Ring Size
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive ring sizes won't appear in product forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Ring Size
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Ring Size Modal -->
<div class="modal fade" id="deleteRingSizeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Delete Ring Size
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteRingSizeName"></strong>?</p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteRingSizeForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Ring Size
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Ring Size management functions
function editRingSize(id, size, description, isActive) {
    document.getElementById('edit_ring_size_size').value = size;
    document.getElementById('edit_ring_size_description').value = description || '';
    document.getElementById('edit_ring_size_is_active').checked = isActive;
    
    document.getElementById('editRingSizeForm').action = `/products/ring-sizes/${id}`;
    
    new bootstrap.Modal(document.getElementById('editRingSizeModal')).show();
}

function deleteRingSize(id, size) {
    document.getElementById('deleteRingSizeName').textContent = size;
    document.getElementById('deleteRingSizeForm').action = `/products/ring-sizes/${id}`;
    
    new bootstrap.Modal(document.getElementById('deleteRingSizeModal')).show();
}

// Handle form submissions
document.getElementById('addRingSizeForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while adding the ring size.');
    });
});

document.getElementById('editRingSizeForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while updating the ring size.');
    });
});

document.getElementById('deleteRingSizeForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while deleting the ring size.');
    });
});
</script> 