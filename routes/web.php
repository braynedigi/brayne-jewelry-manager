<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\OrderTemplateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->isDistributor()) {
            return redirect()->route('distributor.dashboard');
        } elseif (auth()->user()->isFactory()) {
            return redirect()->route('factory.dashboard');
        }
        return redirect()->route('login');
    })->name('dashboard');
    
    Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard')->middleware('role:admin');
    Route::get('/distributor/dashboard', [AuthController::class, 'distributorDashboard'])->name('distributor.dashboard')->middleware('role:distributor');
    Route::get('/factory/dashboard', [AuthController::class, 'factoryDashboard'])->name('factory.dashboard')->middleware('role:factory');
    
    // Factory workflow routes
    Route::middleware('role:factory')->group(function () {
        Route::get('/factory/queue', [FactoryController::class, 'queue'])->name('factory.queue');
        Route::put('/factory/orders/{order}/status', [FactoryController::class, 'updateStatus'])->name('factory.update-status');
        Route::put('/factory/orders/{order}/priority', [FactoryController::class, 'updatePriority'])->name('factory.update-priority');
        Route::put('/factory/orders/{order}/timeline', [FactoryController::class, 'updateTimeline'])->name('factory.update-timeline');
        Route::get('/factory/workload', [FactoryController::class, 'workload'])->name('factory.workload');
    });
    
    // Distributor profile management
    Route::middleware('role:distributor')->group(function () {
        Route::get('/distributor/profile/create', [DistributorController::class, 'createProfile'])->name('distributor.profile.create');
        Route::post('/distributor/profile', [DistributorController::class, 'storeProfile'])->name('distributor.profile.store');
        Route::get('/distributor/profile/edit', [DistributorController::class, 'editProfile'])->name('distributor.profile.edit');
        Route::put('/distributor/profile', [DistributorController::class, 'updateProfile'])->name('distributor.profile.update');
    });
    
    // Customer management
    Route::resource('customers', CustomerController::class);
    
    // Order management
    Route::resource('orders', OrderController::class);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    
    // Order Templates (Distributor only)
    Route::middleware('role:distributor')->group(function () {
        Route::resource('order-templates', OrderTemplateController::class);
        Route::post('order-templates/{template}/use', [OrderTemplateController::class, 'useTemplate'])->name('order-templates.use');
        Route::get('orders/{order}/create-template', [OrderTemplateController::class, 'createFromOrder'])->name('order-templates.create-from-order');
        Route::post('orders/{order}/quick-reorder', [OrderTemplateController::class, 'quickReorder'])->name('orders.quick-reorder');
    });
    
    // API route for getting product pricing
    Route::get('/api/products/{product}/pricing', [ProductController::class, 'getPricing'])->name('api.products.pricing');
Route::get('/api/products/subcategories', [ProductController::class, 'getSubCategories'])->name('api.products.subcategories');

// Temporary debug route
Route::get('/debug/product/{sku}', function($sku) {
    $product = App\Models\Product::where('sku', $sku)->first();
    if (!$product) {
        return response()->json(['error' => 'Product not found']);
    }
    
    return response()->json([
        'name' => $product->name,
        'sku' => $product->sku,
        'has_fonts' => $product->hasFonts(),
        'fonts' => $product->fonts,
        'font_requirement' => $product->font_requirement,
        'fonts_count' => count($product->fonts)
    ]);
});
    

    


    // Product management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        
        // Product Categories Management (MUST come before products resource route)
        Route::get('/products/categories', [ProductCategoryController::class, 'index'])->name('products.categories.index');
        
        // Categories
        Route::post('/products/categories', [ProductCategoryController::class, 'storeCategory'])->name('products.categories.store');
        Route::put('/products/categories/{category}', [ProductCategoryController::class, 'updateCategory'])->name('products.categories.update');
        Route::delete('/products/categories/{category}', [ProductCategoryController::class, 'destroyCategory'])->name('products.categories.destroy');
        
        // Metals
        Route::post('/products/metals', [ProductCategoryController::class, 'storeMetal'])->name('products.metals.store');
        Route::put('/products/metals/{metal}', [ProductCategoryController::class, 'updateMetal'])->name('products.metals.update');
        Route::delete('/products/metals/{metal}', [ProductCategoryController::class, 'destroyMetal'])->name('products.metals.destroy');
        
        // Stones
        Route::post('/products/stones', [ProductCategoryController::class, 'storeStone'])->name('products.stones.store');
        Route::put('/products/stones/{stone}', [ProductCategoryController::class, 'updateStone'])->name('products.stones.update');
        Route::delete('/products/stones/{stone}', [ProductCategoryController::class, 'destroyStone'])->name('products.stones.destroy');
        
        // Fonts
        Route::post('/products/fonts', [ProductCategoryController::class, 'storeFont'])->name('products.fonts.store');
        Route::put('/products/fonts/{font}', [ProductCategoryController::class, 'updateFont'])->name('products.fonts.update');
        Route::delete('/products/fonts/{font}', [ProductCategoryController::class, 'destroyFont'])->name('products.fonts.destroy');
        
        // Ring Sizes
        Route::post('/products/ring-sizes', [ProductCategoryController::class, 'storeRingSize'])->name('products.ring-sizes.store');
        Route::put('/products/ring-sizes/{ringSize}', [ProductCategoryController::class, 'updateRingSize'])->name('products.ring-sizes.update');
        Route::delete('/products/ring-sizes/{ringSize}', [ProductCategoryController::class, 'destroyRingSize'])->name('products.ring-sizes.destroy');
        
        // API endpoints for active options
        Route::get('/api/categories', [ProductCategoryController::class, 'getActiveCategories'])->name('api.categories');
        Route::get('/api/metals', [ProductCategoryController::class, 'getActiveMetals'])->name('api.metals');
        Route::get('/api/stones', [ProductCategoryController::class, 'getActiveStones'])->name('api.stones');
        Route::get('/api/fonts', [ProductCategoryController::class, 'getActiveFonts'])->name('api.fonts');
        Route::get('/api/ring-sizes', [ProductCategoryController::class, 'getActiveRingSizes'])->name('api.ring-sizes');
        
        // Products resource route (MUST come after specific product routes)
        Route::resource('products', ProductController::class);
        
        Route::resource('couriers', CourierController::class);
            Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/test-email', [SettingsController::class, 'testEmail'])->name('admin.settings.test-email');
    Route::get('/settings/refresh', [SettingsController::class, 'refresh'])->name('admin.settings.refresh');
    
    // Admin courier management routes
    Route::post('/couriers', [SettingsController::class, 'storeCourier'])->name('admin.couriers.store');
    Route::put('/couriers/{courier}', [SettingsController::class, 'updateCourier'])->name('admin.couriers.update');
    Route::delete('/couriers/{courier}', [SettingsController::class, 'destroyCourier'])->name('admin.couriers.destroy');
        
        // Order approval routes
        Route::get('/approval', [ApprovalController::class, 'index'])->name('admin.approval.index');
        Route::get('/approval/{order}', [ApprovalController::class, 'show'])->name('admin.approval.show');
        Route::put('/approval/{order}', [ApprovalController::class, 'update'])->name('admin.approval.update');
        Route::post('/approval/bulk', [ApprovalController::class, 'bulkUpdate'])->name('admin.approval.bulk');
        Route::get('/approval/stats', [ApprovalController::class, 'stats'])->name('admin.approval.stats');
    });

    // Notifications
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationsController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationsController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationsController::class, 'destroy'])->name('notifications.destroy');
});

// API Routes for real-time dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/api/dashboard/stats', function () {
        $user = auth()->user();
        $counters = [];
        
        if ($user->isAdmin()) {
            $counters = [
                'pending-payment' => Order::where('order_status', 'pending_payment')->count(),
                'approved' => Order::where('order_status', 'approved')->count(),
                'in-production' => Order::where('order_status', 'in_production')->count(),
                'finishing' => Order::where('order_status', 'finishing')->count(),
                'ready' => Order::where('order_status', 'ready_for_delivery')->count(),
                'delivered-brayne' => Order::where('order_status', 'delivered_to_brayne')->count(),
                'delivered-client' => Order::where('order_status', 'delivered_to_client')->count(),
            ];
        } elseif ($user->isFactory()) {
            $counters = [
                'approved' => Order::where('order_status', 'approved')->count(),
                'in-production' => Order::where('order_status', 'in_production')->count(),
                'finishing' => Order::where('order_status', 'finishing')->count(),
                'ready' => Order::where('order_status', 'ready_for_delivery')->count(),
            ];
        } elseif ($user->isDistributor()) {
            $counters = [
                'pending-payment' => Order::where('order_status', 'pending_payment')
                    ->where('distributor_id', $user->distributor_id)->count(),
                'approved' => Order::where('order_status', 'approved')
                    ->where('distributor_id', $user->distributor_id)->count(),
                'in-production' => Order::where('order_status', 'in_production')
                    ->where('distributor_id', $user->distributor_id)->count(),
                'finishing' => Order::where('order_status', 'finishing')
                    ->where('distributor_id', $user->distributor_id)->count(),
                'ready' => Order::where('order_status', 'ready_for_delivery')
                    ->where('distributor_id', $user->distributor_id)->count(),
                'delivered-brayne' => Order::where('order_status', 'delivered_to_brayne')
                    ->where('distributor_id', $user->distributor_id)->count(),
                'delivered-client' => Order::where('order_status', 'delivered_to_client')
                    ->where('distributor_id', $user->distributor_id)->count(),
            ];
        }
        
        return response()->json([
            'counters' => $counters,
            'total' => array_sum($counters),
            'timestamp' => now()->toISOString()
        ]);
    })->name('api.dashboard.stats');
});

// Default home page redirects to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Temporary test route for logo debugging
Route::get('/test-logo', function () {
    $logoPath = \App\Models\Setting::getValue('login_logo');
    $logoUrl = asset('storage/' . $logoPath);
    $filePath = storage_path('app/public/' . $logoPath);
    
    return response()->json([
        'logo_path' => $logoPath,
        'logo_url' => $logoUrl,
        'file_path' => $filePath,
        'file_exists' => file_exists($filePath),
        'file_size' => file_exists($filePath) ? filesize($filePath) : null,
        'mime_type' => file_exists($filePath) ? mime_content_type($filePath) : null,
    ]);
});

// API route for notification count
Route::get('/api/notifications/count', function () {
    if (!auth()->check()) {
        return response()->json(['count' => 0]);
    }
    
    $count = \App\Services\NotificationService::getUnreadCount(auth()->id());
    return response()->json(['count' => $count]);
})->name('api.notifications.count');