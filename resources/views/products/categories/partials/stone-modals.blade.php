<!-- Add Stone Modal -->
<div class="modal fade" id="addStoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Stone
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStoneForm" method="POST" action="{{ route('products.stones.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="stone_name" class="form-label">Stone Name *</label>
                        <input type="text" class="form-control" id="stone_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="stone_description" class="form-label">Description</label>
                        <textarea class="form-control" id="stone_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="stone_is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="stone_is_active">
                                Active Stone
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive stones won't appear in product forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Stone
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Stone Modal -->
<div class="modal fade" id="editStoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Stone
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStoneForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_stone_name" class="form-label">Stone Name *</label>
                        <input type="text" class="form-control" id="edit_stone_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_stone_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_stone_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_stone_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_stone_is_active">
                                Active Stone
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive stones won't appear in product forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Stone
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Stone Modal -->
<div class="modal fade" id="deleteStoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Delete Stone
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteStoneName"></strong>?</p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteStoneForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Stone
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Stone management functions
function editStone(id, name, description, isActive) {
    document.getElementById('edit_stone_name').value = name;
    document.getElementById('edit_stone_description').value = description || '';
    document.getElementById('edit_stone_is_active').checked = isActive;
    
    document.getElementById('editStoneForm').action = `/products/stones/${id}`;
    
    new bootstrap.Modal(document.getElementById('editStoneModal')).show();
}

function deleteStone(id, name) {
    document.getElementById('deleteStoneName').textContent = name;
    document.getElementById('deleteStoneForm').action = `/products/stones/${id}`;
    
    new bootstrap.Modal(document.getElementById('deleteStoneModal')).show();
}

// Handle form submissions
document.getElementById('addStoneForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while adding the stone.');
    });
});

document.getElementById('editStoneForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while updating the stone.');
    });
});

document.getElementById('deleteStoneForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while deleting the stone.');
    });
});
</script> 