@extends('layouts.app')

@php
use App\Models\Setting;
@endphp

@section('title', 'Admin Settings')

@section('page-title', 'Application Settings')
@section('page-subtitle', 'Configure your jewelry management system')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <!-- Simple tab navigation -->
                <div class="tab-navigation mb-3">
                    <button type="button" class="tab-button active" onclick="switchTab('appearance')">
                        <i class="fas fa-palette me-2"></i>Appearance
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('notifications')">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('general')">
                        <i class="fas fa-cog me-2"></i>General
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('couriers')">
                        <i class="fas fa-truck me-2"></i>Couriers
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('email')">
                        <i class="fas fa-envelope me-2"></i>Email
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('email-templates')">
                        <i class="fas fa-file-alt me-2"></i>Email Templates
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('import-export')">
                        <i class="fas fa-file-import me-2"></i>Import/Export
                    </button>
                </div>
                
                <style>
                    .tab-navigation {
                        display: flex;
                        gap: 10px;
                        border-bottom: 2px solid #e9ecef;
                        padding-bottom: 10px;
                    }
                    .tab-button {
                        background: none;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 5px 5px 0 0;
                        cursor: pointer;
                        font-weight: 500;
                        color: #6c757d;
                        transition: all 0.3s ease;
                    }
                    .tab-button:hover {
                        background-color: #f8f9fa;
                        color: #495057;
                    }
                    .tab-button.active {
                        background-color: #D4AF37;
                        color: white;
                        border-bottom: 2px solid #D4AF37;
                        margin-bottom: -2px;
                    }
                    .tab-section {
                        display: none;
                    }
                    .tab-section.active {
                        display: block;
                    }
                </style>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="tab-section active" id="appearance">
                        <!-- Login Page Appearance -->
                        <h5 class="mb-4">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Page Appearance
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="login_logo" class="form-label">Login Page Logo</label>
                                <input type="file" class="form-control" id="login_logo" name="settings[login_logo]" accept="image/*">
                                <div class="form-text">Upload a logo to display on the login page (recommended: 200x80px)</div>
                                
                                @if(Setting::getValue('login_logo'))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . Setting::getValue('login_logo')) }}" 
                                             alt="Current Logo" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="login_background_color" class="form-label">Background Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="login_background_color" name="settings[login_background_color]"
                                       value="{{ Setting::getValue('login_background_color', '#f8fafc') }}">
                                <div class="form-text">Choose the background color for the login page</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="login_background_image" class="form-label">Background Image</label>
                                <input type="file" class="form-control" id="login_background_image" 
                                       name="settings[login_background_image]" accept="image/*">
                                <div class="form-text">Upload a background image for the login page</div>
                                
                                @if(Setting::getValue('login_background_image'))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . Setting::getValue('login_background_image')) }}" 
                                             alt="Current Background" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Button Colors -->
                        <h5 class="mb-4">
                            <i class="fas fa-square me-2"></i>Button Colors
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="primary_button_color" class="form-label">Primary Button Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="primary_button_color" name="settings[primary_button_color]"
                                       value="{{ Setting::getValue('primary_button_color', '#0d6efd') }}">
                                <div class="form-text">Main action buttons (Create, Save, etc.)</div>
                                <div class="mt-2">
                                    <button type="button" class="btn" style="background-color: {{ Setting::getValue('primary_button_color', '#0d6efd') }}; color: white;">Preview</button>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="secondary_button_color" class="form-label">Secondary Button Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="secondary_button_color" name="settings[secondary_button_color]"
                                       value="{{ Setting::getValue('secondary_button_color', '#6c757d') }}">
                                <div class="form-text">Secondary action buttons (Cancel, Back, etc.)</div>
                                <div class="mt-2">
                                    <button type="button" class="btn" style="background-color: {{ Setting::getValue('secondary_button_color', '#6c757d') }}; color: white;">Preview</button>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="success_button_color" class="form-label">Success Button Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="success_button_color" name="settings[success_button_color]"
                                       value="{{ Setting::getValue('success_button_color', '#198754') }}">
                                <div class="form-text">Success action buttons (Approve, Complete, etc.)</div>
                                <div class="mt-2">
                                    <button type="button" class="btn" style="background-color: {{ Setting::getValue('success_button_color', '#198754') }}; color: white;">Preview</button>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="warning_button_color" class="form-label">Warning Button Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="warning_button_color" name="settings[warning_button_color]"
                                       value="{{ Setting::getValue('warning_button_color', '#ffc107') }}">
                                <div class="form-text">Warning action buttons (Edit, Update, etc.)</div>
                                <div class="mt-2">
                                    <button type="button" class="btn" style="background-color: {{ Setting::getValue('warning_button_color', '#ffc107') }}; color: black;">Preview</button>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="danger_button_color" class="form-label">Danger Button Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="danger_button_color" name="settings[danger_button_color]"
                                       value="{{ Setting::getValue('danger_button_color', '#dc3545') }}">
                                <div class="form-text">Danger action buttons (Delete, Cancel, etc.)</div>
                                <div class="mt-2">
                                    <button type="button" class="btn" style="background-color: {{ Setting::getValue('danger_button_color', '#dc3545') }}; color: white;">Preview</button>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="info_button_color" class="form-label">Info Button Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="info_button_color" name="settings[info_button_color]"
                                       value="{{ Setting::getValue('info_button_color', '#0dcaf0') }}">
                                <div class="form-text">Info action buttons (View, Details, etc.)</div>
                                <div class="mt-2">
                                    <button type="button" class="btn" style="background-color: {{ Setting::getValue('info_button_color', '#0dcaf0') }}; color: white;">Preview</button>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Sidebar & Navigation Colors -->
                        <h5 class="mb-4">
                            <i class="fas fa-bars me-2"></i>Sidebar & Navigation Colors
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="sidebar_background_color" class="form-label">Sidebar Background</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="sidebar_background_color" name="settings[sidebar_background_color]"
                                       value="{{ Setting::getValue('sidebar_background_color', '#343a40') }}">
                                <div class="form-text">Background color for the left sidebar</div>
                                <div class="mt-2 p-2" style="background-color: {{ Setting::getValue('sidebar_background_color', '#343a40') }}; border-radius: 4px; min-height: 40px;"></div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="sidebar_text_color" class="form-label">Sidebar Text Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="sidebar_text_color" name="settings[sidebar_text_color]"
                                       value="{{ Setting::getValue('sidebar_text_color', '#ffffff') }}">
                                <div class="form-text">Text color for sidebar menu items</div>
                                <div class="mt-2 p-2" style="background-color: {{ Setting::getValue('sidebar_background_color', '#343a40') }}; color: {{ Setting::getValue('sidebar_text_color', '#ffffff') }}; border-radius: 4px;">Sample Text</div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="sidebar_active_color" class="form-label">Active Menu Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="sidebar_active_color" name="settings[sidebar_active_color]"
                                       value="{{ Setting::getValue('sidebar_active_color', '#007bff') }}">
                                <div class="form-text">Color for active/selected menu items</div>
                                <div class="mt-2 p-2" style="background-color: {{ Setting::getValue('sidebar_active_color', '#007bff') }}; color: white; border-radius: 4px;">Active Item</div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="top_navbar_color" class="form-label">Top Navbar Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="top_navbar_color" name="settings[top_navbar_color]"
                                       value="{{ Setting::getValue('top_navbar_color', '#ffffff') }}">
                                <div class="form-text">Background color for the top navigation bar</div>
                                <div class="mt-2 p-2" style="background-color: {{ Setting::getValue('top_navbar_color', '#ffffff') }}; border: 1px solid #dee2e6; border-radius: 4px; min-height: 40px;"></div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="top_navbar_text_color" class="form-label">Top Navbar Text</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="top_navbar_text_color" name="settings[top_navbar_text_color]"
                                       value="{{ Setting::getValue('top_navbar_text_color', '#212529') }}">
                                <div class="form-text">Text color for the top navigation bar</div>
                                <div class="mt-2 p-2" style="background-color: {{ Setting::getValue('top_navbar_color', '#ffffff') }}; color: {{ Setting::getValue('top_navbar_text_color', '#212529') }}; border: 1px solid #dee2e6; border-radius: 4px;">Sample Text</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Card & Panel Colors -->
                        <h5 class="mb-4">
                            <i class="fas fa-credit-card me-2"></i>Card & Panel Colors
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="card_background_color" class="form-label">Card Background</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="card_background_color" name="settings[card_background_color]"
                                       value="{{ Setting::getValue('card_background_color', '#ffffff') }}">
                                <div class="form-text">Background color for content cards</div>
                                <div class="mt-2 p-3" style="background-color: {{ Setting::getValue('card_background_color', '#ffffff') }}; border: 1px solid #dee2e6; border-radius: 4px; min-height: 60px;"></div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="card_header_color" class="form-label">Card Header Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="card_header_color" name="settings[card_header_color]"
                                       value="{{ Setting::getValue('card_header_color', '#f8f9fa') }}">
                                <div class="form-text">Background color for card headers</div>
                                <div class="mt-2 p-2" style="background-color: {{ Setting::getValue('card_header_color', '#f8f9fa') }}; border: 1px solid #dee2e6; border-radius: 4px;">Card Header</div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="right_panel_color" class="form-label">Right Panel Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="right_panel_color" name="settings[right_panel_color]"
                                       value="{{ Setting::getValue('right_panel_color', '#f8f9fa') }}">
                                <div class="form-text">Background color for right sidebar panels</div>
                                <div class="mt-2 p-3" style="background-color: {{ Setting::getValue('right_panel_color', '#f8f9fa') }}; border: 1px solid #dee2e6; border-radius: 4px; min-height: 60px;"></div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Status Badge Colors -->
                        <h5 class="mb-4">
                            <i class="fas fa-tags me-2"></i>Status Badge Colors
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <label for="status_pending_color" class="form-label">Pending Status</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="status_pending_color" name="settings[status_pending_color]"
                                       value="{{ Setting::getValue('status_pending_color', '#ffc107') }}">
                                <div class="form-text">Color for pending status badges</div>
                                <div class="mt-2">
                                    <span class="badge" style="background-color: {{ Setting::getValue('status_pending_color', '#ffc107') }}; color: black;">Pending</span>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-4">
                                <label for="status_approved_color" class="form-label">Approved Status</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="status_approved_color" name="settings[status_approved_color]"
                                       value="{{ Setting::getValue('status_approved_color', '#0dcaf0') }}">
                                <div class="form-text">Color for approved status badges</div>
                                <div class="mt-2">
                                    <span class="badge" style="background-color: {{ Setting::getValue('status_approved_color', '#0dcaf0') }}; color: white;">Approved</span>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-4">
                                <label for="status_production_color" class="form-label">Production Status</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="status_production_color" name="settings[status_production_color]"
                                       value="{{ Setting::getValue('status_production_color', '#0d6efd') }}">
                                <div class="form-text">Color for production status badges</div>
                                <div class="mt-2">
                                    <span class="badge" style="background-color: {{ Setting::getValue('status_production_color', '#0d6efd') }}; color: white;">In Production</span>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-4">
                                <label for="status_completed_color" class="form-label">Completed Status</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="status_completed_color" name="settings[status_completed_color]"
                                       value="{{ Setting::getValue('status_completed_color', '#198754') }}">
                                <div class="form-text">Color for completed status badges</div>
                                <div class="mt-2">
                                    <span class="badge" style="background-color: {{ Setting::getValue('status_completed_color', '#198754') }}; color: white;">Completed</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Additional UI Colors -->
                        <h5 class="mb-4">
                            <i class="fas fa-paint-brush me-2"></i>Additional UI Colors
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="link_color" class="form-label">Link Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="link_color" name="settings[link_color]"
                                       value="{{ Setting::getValue('link_color', '#0d6efd') }}">
                                <div class="form-text">Color for hyperlinks throughout the application</div>
                                <div class="mt-2">
                                    <a href="#" style="color: {{ Setting::getValue('link_color', '#0d6efd') }};">Sample Link</a>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="border_color" class="form-label">Border Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="border_color" name="settings[border_color]"
                                       value="{{ Setting::getValue('border_color', '#dee2e6') }}">
                                <div class="form-text">Color for borders and dividers</div>
                                <div class="mt-2 p-2" style="border: 2px solid {{ Setting::getValue('border_color', '#dee2e6') }}; border-radius: 4px;">Sample Border</div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="shadow_color" class="form-label">Shadow Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="shadow_color" name="settings[shadow_color]"
                                       value="{{ Setting::getValue('shadow_color', '#000000') }}">
                                <div class="form-text">Color for shadows and depth effects</div>
                                <div class="mt-2 p-3" style="box-shadow: 0 2px 4px {{ Setting::getValue('shadow_color', '#000000') }}20; border-radius: 4px; background: white;">Sample Shadow</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Color Presets -->
                        <h5 class="mb-4">
                            <i class="fas fa-palette me-2"></i>Color Presets
                        </h5>
                        
                        <div class="row">
                            <div class="col-12 mb-4">
                                <p class="text-muted">Quick apply predefined color schemes:</p>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="applyPreset('default')">Default Blue</button>
                                    <button type="button" class="btn btn-outline-success" onclick="applyPreset('green')">Green Theme</button>
                                    <button type="button" class="btn btn-outline-warning" onclick="applyPreset('orange')">Orange Theme</button>
                                    <button type="button" class="btn btn-outline-danger" onclick="applyPreset('red')">Red Theme</button>
                                    <button type="button" class="btn btn-outline-info" onclick="applyPreset('purple')">Purple Theme</button>
                                    <button type="button" class="btn btn-outline-dark" onclick="applyPreset('dark')">Dark Theme</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-section" id="notifications">
                        <h5 class="mb-4">
                            <i class="fas fa-bell me-2"></i>Notification Preferences
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" 
                                           name="settings[email_notifications]" value="1"
                                           {{ Setting::getValue('email_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">
                                        <strong>Email Notifications</strong>
                                    </label>
                                    <div class="form-text">Send notifications via email</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="in_app_notifications" 
                                           name="settings[in_app_notifications]" value="1"
                                           {{ Setting::getValue('in_app_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="in_app_notifications">
                                        <strong>In-App Notifications</strong>
                                    </label>
                                    <div class="form-text">Show notifications within the application</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="order_notifications" 
                                           name="settings[order_notifications]" value="1"
                                           {{ Setting::getValue('order_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="order_notifications">
                                        <strong>Order Notifications</strong>
                                    </label>
                                    <div class="form-text">Send notifications for order-related actions</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="customer_notifications" 
                                           name="settings[customer_notifications]" value="1"
                                           {{ Setting::getValue('customer_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="customer_notifications">
                                        <strong>Customer Notifications</strong>
                                    </label>
                                    <div class="form-text">Send notifications for customer-related actions</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="product_notifications" 
                                           name="settings[product_notifications]" value="1"
                                           {{ Setting::getValue('product_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="product_notifications">
                                        <strong>Product Notifications</strong>
                                    </label>
                                    <div class="form-text">Send notifications for product-related actions</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> These settings control notifications for all users. Individual user preferences can be managed separately.
                        </div>
                    </div>

                    <div class="tab-section" id="general">
                        <h5 class="mb-4">
                            <i class="fas fa-cog me-2"></i>General Settings
                        </h5>
                        

                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" 
                                       name="settings[company_name]" 
                                       value="{{ Setting::getValue('company_name', 'Jewelry Manager') }}">
                                <div class="form-text">The name of your company</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="app_title" class="form-label">Application Title</label>
                                <input type="text" class="form-control" id="app_title" 
                                       name="settings[app_title]" 
                                       value="{{ Setting::getValue('app_title', 'Jewelry Manager') }}">
                                <div class="form-text">The title displayed in browser tabs and throughout the application</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="company_email" class="form-label">Company Email</label>
                                <input type="email" class="form-control" id="company_email" 
                                       name="settings[company_email]" 
                                       value="{{ Setting::getValue('company_email', 'admin@jewelrymanager.com') }}">
                                <div class="form-text">Primary email address for the company</div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-section" id="couriers">
                        <h5 class="mb-4">
                            <i class="fas fa-truck me-2"></i>Courier Management
                        </h5>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="mb-1">Manage Delivery Couriers</h6>
                                <p class="text-muted mb-0">Add, edit, and manage courier services available for order delivery</p>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourierModal">
                                <i class="fas fa-plus me-2"></i>Add New Courier
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Courier Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Orders</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(\App\Models\Courier::all() as $courier)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $courier->name }}</div>
                                        </td>
                                        <td>{{ $courier->phone }}</td>
                                        <td>{{ $courier->email ?: 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $courier->is_active ? 'success' : 'secondary' }}">
                                                {{ $courier->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $courier->orders->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="editCourier({{ $courier->id }}, '{{ $courier->name }}', '{{ $courier->phone }}', '{{ $courier->email }}', {{ $courier->is_active ? 'true' : 'false' }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteCourier({{ $courier->id }}, '{{ $courier->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-truck fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No couriers found. Add your first courier to get started.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-section" id="email">
                        <h5 class="mb-4">
                            <i class="fas fa-envelope me-2"></i>Email Configuration
                        </h5>
                        
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Email Setup Guide:</strong> Configure your email settings to enable email notifications. 
                            For Gmail, use SMTP with port 587 and TLS encryption. You may need to enable "Less secure app access" 
                            or use an App Password.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="mail_mailer" class="form-label">Mail Driver</label>
                                <select class="form-select" id="mail_mailer" name="settings[mail_mailer]">
                                    <option value="smtp" {{ Setting::getValue('mail_mailer', 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ Setting::getValue('mail_mailer', 'smtp') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    <option value="mailgun" {{ Setting::getValue('mail_mailer', 'smtp') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="ses" {{ Setting::getValue('mail_mailer', 'smtp') === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    <option value="postmark" {{ Setting::getValue('mail_mailer', 'smtp') === 'postmark' ? 'selected' : '' }}>Postmark</option>
                                    <option value="resend" {{ Setting::getValue('mail_mailer', 'smtp') === 'resend' ? 'selected' : '' }}>Resend</option>
                                    <option value="log" {{ Setting::getValue('mail_mailer', 'smtp') === 'log' ? 'selected' : '' }}>Log (for testing)</option>
                                </select>
                                <div class="form-text">The mail driver to use for sending emails</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="mail_host" class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="mail_host" 
                                       name="settings[mail_host]" 
                                       value="{{ Setting::getValue('mail_host', 'smtp.gmail.com') }}">
                                <div class="form-text">SMTP server hostname (e.g., smtp.gmail.com)</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="mail_port" class="form-label">SMTP Port</label>
                                <input type="text" class="form-control" id="mail_port" 
                                       name="settings[mail_port]" 
                                       value="{{ Setting::getValue('mail_port', '587') }}">
                                <div class="form-text">SMTP port number (587 for TLS, 465 for SSL)</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="mail_username" class="form-label">SMTP Username</label>
                                <input type="text" class="form-control" id="mail_username" 
                                       name="settings[mail_username]" 
                                       value="{{ Setting::getValue('mail_username', '') }}">
                                <div class="form-text">Your email address or SMTP username</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="mail_password" class="form-label">SMTP Password</label>
                                <input type="password" class="form-control" id="mail_password" 
                                       name="settings[mail_password]" 
                                       value="{{ Setting::getValue('mail_password', '') }}">
                                <div class="form-text">Your email password or app password</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="mail_encryption" class="form-label">SMTP Encryption</label>
                                <select class="form-select" id="mail_encryption" name="settings[mail_encryption]">
                                    <option value="tls" {{ Setting::getValue('mail_encryption', 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ Setting::getValue('mail_encryption', 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ Setting::getValue('mail_encryption', 'tls') === '' ? 'selected' : '' }}>None</option>
                                </select>
                                <div class="form-text">Encryption type for SMTP connection</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="mail_from_address" class="form-label">From Email Address</label>
                                <input type="email" class="form-control" id="mail_from_address" 
                                       name="settings[mail_from_address]" 
                                       value="{{ Setting::getValue('mail_from_address', 'noreply@jewelrymanager.com') }}">
                                <div class="form-text">Email address that notifications will be sent from</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="mail_from_name" class="form-label">From Name</label>
                                <input type="text" class="form-control" id="mail_from_name" 
                                       name="settings[mail_from_name]" 
                                       value="{{ Setting::getValue('mail_from_name', 'Jewelry Manager') }}">
                                <div class="form-text">Name that will appear as the sender</div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Test Email Configuration</h6>
                                <p class="text-muted mb-0">Send a test email to verify your configuration</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary" onclick="testEmail()">
                                <i class="fas fa-paper-plane me-2"></i>Send Test Email
                            </button>
                        </div>
                    </div>

                    <div class="tab-section" id="email-templates">
                        <h5 class="mb-4">
                            <i class="fas fa-file-alt me-2"></i>Email Template Management
                        </h5>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="mb-1">Manage Email Templates</h6>
                                <p class="text-muted mb-0">Create and customize email templates for system notifications</p>
                            </div>
                            <a href="{{ route('email-templates.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Template
                            </a>
                        </div>

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
                                    @forelse(\App\Models\EmailTemplate::latest()->get() as $template)
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
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteEmailTemplate({{ $template->id }}, '{{ $template->name }}')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-envelope fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No email templates found. Create your first template to get started.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-section" id="import-export">
                        <h5 class="mb-4">
                            <i class="fas fa-file-import me-2"></i>Import/Export Management
                        </h5>
                        
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Data Management:</strong> Import and export your data in CSV format. Download templates to ensure proper formatting.
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-box fa-2x mb-2"></i>
                                        <h5 class="card-title">{{ \App\Models\Product::count() }}</h5>
                                        <p class="card-text">Products</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-friends fa-2x mb-2"></i>
                                        <h5 class="card-title">{{ \App\Models\Customer::count() }}</h5>
                                        <p class="card-text">Customers</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                        <h5 class="card-title">{{ \App\Models\Order::count() }}</h5>
                                        <p class="card-text">Orders</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-file-csv fa-2x mb-2"></i>
                                        <h5 class="card-title">CSV</h5>
                                        <p class="card-text">Format</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-box me-2"></i>Products
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Export Products</h6>
                                        <p class="text-muted">Download all products as CSV file</p>
                                        <a href="{{ route('import-export.export-products') }}" class="btn btn-success">
                                            <i class="fas fa-download me-2"></i>Export Products
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Import Products</h6>
                                        <p class="text-muted">Upload CSV file to import products</p>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('import-export.download-product-template') }}" class="btn btn-outline-primary">
                                                <i class="fas fa-file-download me-2"></i>Template
                                            </a>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importProductsModal">
                                                <i class="fas fa-upload me-2"></i>Import
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customers Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-user-friends me-2"></i>Customers
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Export Customers</h6>
                                        <p class="text-muted">Download all customers as CSV file</p>
                                        <a href="{{ route('import-export.export-customers') }}" class="btn btn-success">
                                            <i class="fas fa-download me-2"></i>Export Customers
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Import Customers</h6>
                                        <p class="text-muted">Upload CSV file to import customers</p>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('import-export.download-customer-template') }}" class="btn btn-outline-primary">
                                                <i class="fas fa-file-download me-2"></i>Template
                                            </a>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importCustomersModal">
                                                <i class="fas fa-upload me-2"></i>Import
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-shopping-cart me-2"></i>Orders
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Export Orders</h6>
                                        <p class="text-muted">Download all orders as CSV file</p>
                                        <a href="{{ route('import-export.export-orders') }}" class="btn btn-success">
                                            <i class="fas fa-download me-2"></i>Export Orders
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Import Orders</h6>
                                        <p class="text-muted text-muted">Order import is not available for data integrity</p>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-ban me-2"></i>Not Available
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(session('import_results'))
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Import Results</h6>
                                <ul class="mb-0">
                                    @foreach(session('import_results') as $result)
                                        <li>{{ $result }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('import_errors'))
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Import Errors</h6>
                                <ul class="mb-0">
                                    @foreach(session('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.settings.refresh') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Cache
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Settings page loaded');
    
    // Function to show a specific tab
    window.switchTab = function(tabId) {
        console.log('Switching to tab:', tabId);
        
        // Hide all tab sections
        document.querySelectorAll('.tab-section').forEach(function(section) {
            section.classList.remove('active');
            section.style.display = 'none';
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(function(button) {
            button.classList.remove('active');
        });
        
        // Show target tab section
        var targetSection = document.getElementById(tabId);
        if (targetSection) {
            targetSection.classList.add('active');
            targetSection.style.display = 'block';
            console.log('Tab section shown:', tabId);
        } else {
            console.error('Tab section not found:', tabId);
        }
        
        // Add active class to clicked tab button
        var activeButton = document.querySelector(`.tab-button[onclick="switchTab('${tabId}')"]`);
        if (activeButton) {
            activeButton.classList.add('active');
            console.log('Tab button activated:', tabId);
        } else {
            console.error('Tab button not found for:', tabId);
        }
    }
    
    // Show the default active tab on page load
    switchTab('appearance');
    
    // Log all available tabs for debugging
    console.log('Available tabs: appearance, notifications, general, email, couriers, import-export');
    
    // Test email function
    window.testEmail = function() {
        const email = prompt('Enter email address to send test email:');
        if (!email) return;
        
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            alert('Please enter a valid email address.');
            return;
        }
        
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        button.disabled = true;
        
        fetch('{{ route("admin.settings.test-email") }}', {
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
            } else {
                alert('Failed to send test email: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error sending test email: ' + error.message);
        })
        .finally(() => {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
    });
</script>

<!-- Import Products Modal -->
<div class="modal fade" id="importProductsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Import Products
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('import-export.import-products') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong> Upload a CSV file with product data. Make sure to use the template format for best results.
                    </div>
                    
                    <div class="mb-3">
                        <label for="products_file" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="products_file" name="file" accept=".csv" required>
                        <div class="form-text">Select a CSV file to import products</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="products_skip_duplicates" name="skip_duplicates" value="1" checked>
                            <label class="form-check-label" for="products_skip_duplicates">
                                Skip duplicate products (based on SKU)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Import Products
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Customers Modal -->
<div class="modal fade" id="importCustomersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Import Customers
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('import-export.import-customers') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong> Upload a CSV file with customer data. Make sure to use the template format for best results.
                    </div>
                    
                    <div class="mb-3">
                        <label for="customers_file" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="customers_file" name="file" accept=".csv" required>
                        <div class="form-text">Select a CSV file to import customers</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="customers_skip_duplicates" name="skip_duplicates" value="1" checked>
                            <label class="form-check-label" for="customers_skip_duplicates">
                                Skip duplicate customers (based on email)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Import Customers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Courier Modal -->
<div class="modal fade" id="addCourierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Courier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCourierForm" method="POST" action="{{ route('admin.couriers.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="courier_name" class="form-label">Courier Name *</label>
                        <input type="text" class="form-control" id="courier_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="courier_phone" class="form-label">Phone Number *</label>
                        <input type="text" class="form-control" id="courier_phone" name="phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="courier_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="courier_email" name="email">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="courier_is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="courier_is_active">
                                Active Courier
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive couriers won't appear in order forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Courier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Courier Modal -->
<div class="modal fade" id="editCourierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Courier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCourierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_courier_name" class="form-label">Courier Name *</label>
                        <input type="text" class="form-control" id="edit_courier_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_courier_phone" class="form-label">Phone Number *</label>
                        <input type="text" class="form-control" id="edit_courier_phone" name="phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_courier_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="edit_courier_email" name="email">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_courier_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_courier_is_active">
                                Active Courier
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive couriers won't appear in order forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Courier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Courier Modal -->
<div class="modal fade" id="deleteCourierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Delete Courier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteCourierName"></strong>?</p>
                <p class="text-danger small">This action cannot be undone. If this courier has associated orders, they will be affected.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCourierForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Courier
                    </button>
                </form>
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

<script>
// Courier management functions
function editCourier(id, name, phone, email, isActive) {
    document.getElementById('edit_courier_name').value = name;
    document.getElementById('edit_courier_phone').value = phone;
    document.getElementById('edit_courier_email').value = email || '';
    document.getElementById('edit_courier_is_active').checked = isActive;
    
    document.getElementById('editCourierForm').action = `/admin/couriers/${id}`;
    
    new bootstrap.Modal(document.getElementById('editCourierModal')).show();
}

function deleteCourier(id, name) {
    document.getElementById('deleteCourierName').textContent = name;
    document.getElementById('deleteCourierForm').action = `/admin/couriers/${id}`;
    
    new bootstrap.Modal(document.getElementById('deleteCourierModal')).show();
}

// Handle form submissions
document.getElementById('addCourierForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while adding the courier.');
    });
});

document.getElementById('editCourierForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while updating the courier.');
    });
});

document.getElementById('deleteCourierForm').addEventListener('submit', function(e) {
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
        alert('An error occurred while deleting the courier.');
    });
});

// Email template functions
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

// Email template deletion function
function deleteEmailTemplate(templateId, templateName) {
    if (confirm('Are you sure you want to delete the template "' + templateName + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/email-templates/${templateId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Color customization functions
document.addEventListener('DOMContentLoaded', function() {
    // Live preview for color inputs
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateColorPreview(this);
        });
    });
});

function updateColorPreview(colorInput) {
    const color = colorInput.value;
    const inputId = colorInput.id;
    
    // Update button previews
    if (inputId.includes('button_color')) {
        const previewButton = colorInput.parentElement.querySelector('.btn');
        if (previewButton) {
            previewButton.style.backgroundColor = color;
            // Adjust text color based on background brightness
            const brightness = getBrightness(color);
            previewButton.style.color = brightness > 128 ? 'black' : 'white';
        }
    }
    
    // Update badge previews
    if (inputId.includes('status_')) {
        const previewBadge = colorInput.parentElement.querySelector('.badge');
        if (previewBadge) {
            previewBadge.style.backgroundColor = color;
            const brightness = getBrightness(color);
            previewBadge.style.color = brightness > 128 ? 'black' : 'white';
        }
    }
    
    // Update sidebar previews
    if (inputId === 'sidebar_background_color') {
        const previewDiv = colorInput.parentElement.querySelector('div[style*="background-color"]');
        if (previewDiv) {
            previewDiv.style.backgroundColor = color;
        }
    }
    
    if (inputId === 'sidebar_text_color') {
        const previewDiv = document.querySelector('div[style*="background-color"]');
        if (previewDiv && previewDiv.parentElement.querySelector('#sidebar_background_color')) {
            previewDiv.style.color = color;
        }
    }
    
    // Update link preview
    if (inputId === 'link_color') {
        const previewLink = colorInput.parentElement.querySelector('a');
        if (previewLink) {
            previewLink.style.color = color;
        }
    }
    
    // Update border preview
    if (inputId === 'border_color') {
        const previewDiv = colorInput.parentElement.querySelector('div[style*="border"]');
        if (previewDiv) {
            previewDiv.style.borderColor = color;
        }
    }
    
    // Update shadow preview
    if (inputId === 'shadow_color') {
        const previewDiv = colorInput.parentElement.querySelector('div[style*="box-shadow"]');
        if (previewDiv) {
            previewDiv.style.boxShadow = `0 2px 4px ${color}20`;
        }
    }
}

function getBrightness(hex) {
    // Convert hex to RGB
    const r = parseInt(hex.substr(1, 2), 16);
    const g = parseInt(hex.substr(3, 2), 16);
    const b = parseInt(hex.substr(5, 2), 16);
    
    // Calculate brightness
    return (r * 299 + g * 587 + b * 114) / 1000;
}

function applyPreset(presetName) {
    const presets = {
        'default': {
            'primary_button_color': '#0d6efd',
            'secondary_button_color': '#6c757d',
            'success_button_color': '#198754',
            'warning_button_color': '#ffc107',
            'danger_button_color': '#dc3545',
            'info_button_color': '#0dcaf0',
            'sidebar_background_color': '#343a40',
            'sidebar_text_color': '#ffffff',
            'sidebar_active_color': '#007bff',
            'top_navbar_color': '#ffffff',
            'top_navbar_text_color': '#212529',
            'card_background_color': '#ffffff',
            'card_header_color': '#f8f9fa',
            'right_panel_color': '#f8f9fa',
            'status_pending_color': '#ffc107',
            'status_approved_color': '#0dcaf0',
            'status_production_color': '#0d6efd',
            'status_completed_color': '#198754',
            'link_color': '#0d6efd',
            'border_color': '#dee2e6',
            'shadow_color': '#000000'
        },
        'green': {
            'primary_button_color': '#198754',
            'secondary_button_color': '#6c757d',
            'success_button_color': '#20c997',
            'warning_button_color': '#ffc107',
            'danger_button_color': '#dc3545',
            'info_button_color': '#0dcaf0',
            'sidebar_background_color': '#198754',
            'sidebar_text_color': '#ffffff',
            'sidebar_active_color': '#20c997',
            'top_navbar_color': '#ffffff',
            'top_navbar_text_color': '#212529',
            'card_background_color': '#ffffff',
            'card_header_color': '#f8f9fa',
            'right_panel_color': '#f8f9fa',
            'status_pending_color': '#ffc107',
            'status_approved_color': '#0dcaf0',
            'status_production_color': '#198754',
            'status_completed_color': '#20c997',
            'link_color': '#198754',
            'border_color': '#dee2e6',
            'shadow_color': '#000000'
        },
        'orange': {
            'primary_button_color': '#fd7e14',
            'secondary_button_color': '#6c757d',
            'success_button_color': '#198754',
            'warning_button_color': '#ffc107',
            'danger_button_color': '#dc3545',
            'info_button_color': '#0dcaf0',
            'sidebar_background_color': '#fd7e14',
            'sidebar_text_color': '#ffffff',
            'sidebar_active_color': '#ffc107',
            'top_navbar_color': '#ffffff',
            'top_navbar_text_color': '#212529',
            'card_background_color': '#ffffff',
            'card_header_color': '#f8f9fa',
            'right_panel_color': '#f8f9fa',
            'status_pending_color': '#ffc107',
            'status_approved_color': '#0dcaf0',
            'status_production_color': '#fd7e14',
            'status_completed_color': '#198754',
            'link_color': '#fd7e14',
            'border_color': '#dee2e6',
            'shadow_color': '#000000'
        },
        'red': {
            'primary_button_color': '#dc3545',
            'secondary_button_color': '#6c757d',
            'success_button_color': '#198754',
            'warning_button_color': '#ffc107',
            'danger_button_color': '#fd7e14',
            'info_button_color': '#0dcaf0',
            'sidebar_background_color': '#dc3545',
            'sidebar_text_color': '#ffffff',
            'sidebar_active_color': '#fd7e14',
            'top_navbar_color': '#ffffff',
            'top_navbar_text_color': '#212529',
            'card_background_color': '#ffffff',
            'card_header_color': '#f8f9fa',
            'right_panel_color': '#f8f9fa',
            'status_pending_color': '#ffc107',
            'status_approved_color': '#0dcaf0',
            'status_production_color': '#dc3545',
            'status_completed_color': '#198754',
            'link_color': '#dc3545',
            'border_color': '#dee2e6',
            'shadow_color': '#000000'
        },
        'purple': {
            'primary_button_color': '#6f42c1',
            'secondary_button_color': '#6c757d',
            'success_button_color': '#198754',
            'warning_button_color': '#ffc107',
            'danger_button_color': '#dc3545',
            'info_button_color': '#0dcaf0',
            'sidebar_background_color': '#6f42c1',
            'sidebar_text_color': '#ffffff',
            'sidebar_active_color': '#8b5cf6',
            'top_navbar_color': '#ffffff',
            'top_navbar_text_color': '#212529',
            'card_background_color': '#ffffff',
            'card_header_color': '#f8f9fa',
            'right_panel_color': '#f8f9fa',
            'status_pending_color': '#ffc107',
            'status_approved_color': '#0dcaf0',
            'status_production_color': '#6f42c1',
            'status_completed_color': '#198754',
            'link_color': '#6f42c1',
            'border_color': '#dee2e6',
            'shadow_color': '#000000'
        },
        'dark': {
            'primary_button_color': '#212529',
            'secondary_button_color': '#6c757d',
            'success_button_color': '#198754',
            'warning_button_color': '#ffc107',
            'danger_button_color': '#dc3545',
            'info_button_color': '#0dcaf0',
            'sidebar_background_color': '#212529',
            'sidebar_text_color': '#ffffff',
            'sidebar_active_color': '#343a40',
            'top_navbar_color': '#343a40',
            'top_navbar_text_color': '#ffffff',
            'card_background_color': '#343a40',
            'card_header_color': '#495057',
            'right_panel_color': '#495057',
            'status_pending_color': '#ffc107',
            'status_approved_color': '#0dcaf0',
            'status_production_color': '#6c757d',
            'status_completed_color': '#198754',
            'link_color': '#ffffff',
            'border_color': '#495057',
            'shadow_color': '#000000'
        }
    };
    
    const preset = presets[presetName];
    if (!preset) return;
    
    // Apply all colors from the preset
    Object.keys(preset).forEach(key => {
        const input = document.getElementById(key);
        if (input) {
            input.value = preset[key];
            updateColorPreview(input);
        }
    });
    
    // Show success message
    const toast = document.createElement('div');
    toast.className = 'toast show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <div class="toast-header bg-success text-white">
            <i class="fas fa-palette me-2"></i>
            <strong class="me-auto">Color Preset Applied</strong>
            <button type="button" class="btn-close btn-close-white" onclick="this.closest('.toast').remove()"></button>
        </div>
        <div class="toast-body">
            <div class="fw-bold">${presetName.charAt(0).toUpperCase() + presetName.slice(1)} Theme</div>
            <div class="small">All colors have been updated. Click "Save Changes" to apply.</div>
        </div>
    `;
    document.body.appendChild(toast);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}
</script>
@endpush 