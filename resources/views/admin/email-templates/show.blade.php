@extends('layouts.app')

@section('title', 'View Email Template')

@section('page-title', 'Email Template Details')
@section('page-subtitle', 'View template information and content')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $emailTemplate->name }}</h5>
                        <p class="text-muted mb-0">{{ $emailTemplate->description }}</p>
                    </div>
                    <div>
                        <a href="{{ route('email-templates.edit', $emailTemplate) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Template
                        </a>
                        <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Template Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">Name:</td>
                                <td>{{ $emailTemplate->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Type:</td>
                                <td>
                                    <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $emailTemplate->type)) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    <span class="badge bg-{{ $emailTemplate->is_active ? 'success' : 'secondary' }}">
                                        {{ $emailTemplate->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Created:</td>
                                <td>{{ $emailTemplate->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Last Updated:</td>
                                <td>{{ $emailTemplate->updated_at->format('M d, Y g:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Available Variables</h6>
                        @if($emailTemplate->variables && count($emailTemplate->variables) > 0)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($emailTemplate->variables as $variable)
                                    <span class="badge bg-light text-dark border">{{ $variable }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No specific variables defined for this template.</p>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-bold">Email Subject</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $emailTemplate->subject }}
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="fw-bold">Email Content</h6>
                        <div class="p-3 bg-light rounded" style="max-height: 400px; overflow-y: auto;">
                            {!! $emailTemplate->content !!}
                        </div>
                    </div>
                </div>

                @if($emailTemplate->description)
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="fw-bold">Description</h6>
                        <p class="text-muted">{{ $emailTemplate->description }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 