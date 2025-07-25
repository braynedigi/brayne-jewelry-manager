@extends('layouts.app')

@section('title', 'Email Templates')

@section('page-title', 'Email Templates')
@section('page-subtitle', 'Manage email notification templates')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Email Templates</h5>
                        <p class="text-muted mb-0">Create and manage email templates for notifications</p>
                    </div>
                    <a href="{{ route('email-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Template
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($templates->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Template Name</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($templates as $template)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $template->name }}</div>
                                        @if($template->description)
                                            <small class="text-muted">{{ $template->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $template->type)) }}</span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $template->subject }}">
                                            {{ $template->subject }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }}">
                                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $template->updated_at->format('M d, Y g:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('email-templates.show', $template) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('email-templates.edit', $template) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="previewTemplate({{ $template->id }})" title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="testTemplate({{ $template->id }})" title="Test">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                            <form action="{{ route('email-templates.destroy', $template) }}" 
                                                  method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this template?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Email Templates Found</h5>
                        <p class="text-muted mb-4">Create your first email template to get started with custom notifications.</p>
                        <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Your First Template
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Template Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Subject:</label>
                    <div id="previewSubject" class="p-2 bg-light rounded"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Content:</label>
                    <div id="previewContent" class="p-3 bg-light rounded" style="max-height: 400px; overflow-y: auto;"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Sample Data Used:</label>
                    <div id="previewData" class="p-2 bg-light rounded">
                        <pre class="mb-0" style="font-size: 12px;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane me-2"></i>Test Email Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="testForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="testEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="testEmail" name="email" required>
                        <div class="form-text">Enter the email address where you want to send the test email.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Test Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewTemplate(templateId) {
    fetch(`/email-templates/${templateId}/preview`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('previewSubject').textContent = data.subject;
            document.getElementById('previewContent').innerHTML = data.content;
            document.querySelector('#previewData pre').textContent = JSON.stringify(data.sample_data, null, 2);
            
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        })
        .catch(error => {
            alert('Error loading preview: ' + error.message);
        });
}

function testTemplate(templateId) {
    document.getElementById('testForm').onsubmit = function(e) {
        e.preventDefault();
        
        const email = document.getElementById('testEmail').value;
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        submitBtn.disabled = true;
        
        fetch(`/email-templates/${templateId}/test`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Test email sent successfully! Check your inbox.');
                bootstrap.Modal.getInstance(document.getElementById('testModal')).hide();
            } else {
                alert('Failed to send test email: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error sending test email: ' + error.message);
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    };
    
    new bootstrap.Modal(document.getElementById('testModal')).show();
}
</script>
@endpush 