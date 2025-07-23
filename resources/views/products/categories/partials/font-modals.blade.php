<!-- Add Font Modal -->
<div class="modal fade" id="addFontModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Font
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addFontForm" method="POST" action="{{ route('products.fonts.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="font_name" class="form-label">Font Name *</label>
                        <input type="text" class="form-control" id="font_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="font_description" class="form-label">Description</label>
                        <textarea class="form-control" id="font_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="font_is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="font_is_active">
                                Active Font
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive fonts won't appear in product forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Font
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Font Modal -->
<div class="modal fade" id="editFontModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Font
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFontForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_font_name" class="form-label">Font Name *</label>
                        <input type="text" class="form-control" id="edit_font_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_font_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_font_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_font_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_font_is_active">
                                Active Font
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive fonts won't appear in product forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Font
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Font Modal -->
<div class="modal fade" id="deleteFontModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Delete Font
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteFontName"></strong>?</p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteFontForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Font
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Font management functions
function editFont(id, name, description, isActive) {
    document.getElementById('edit_font_name').value = name;
    document.getElementById('edit_font_description').value = description || '';
    document.getElementById('edit_font_is_active').checked = isActive;
    
    document.getElementById('editFontForm').action = `/products/fonts/${id}`;
    
    new bootstrap.Modal(document.getElementById('editFontModal')).show();
}

function deleteFont(id, name) {
    document.getElementById('deleteFontName').textContent = name;
    document.getElementById('deleteFontForm').action = `/products/fonts/${id}`;
    
    new bootstrap.Modal(document.getElementById('deleteFontModal')).show();
}

// Handle form submissions
document.getElementById('addFontForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while adding the font.');
    });
});

document.getElementById('editFontForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while updating the font.');
    });
});

document.getElementById('deleteFontForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while deleting the font.');
    });
});
</script> 