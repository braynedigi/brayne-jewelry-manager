<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Distributor;
use App\Models\Order;
use App\Models\Product;
use App\Models\Courier;
use App\Models\Customer;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isDistributor()) {
                return redirect()->route('distributor.dashboard');
            } elseif ($user->isFactory()) {
                return redirect()->route('factory.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    public function adminDashboard()
    {
        $totalUsers = User::count();
        $totalDistributors = User::where('role', 'distributor')->count();
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCouriers = Courier::count();
        $totalCustomers = Customer::count();
        $pendingOrders = Order::where('order_status', 'pending_payment')->count();

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalDistributors', 
            'totalOrders', 
            'totalProducts',
            'totalCouriers',
            'totalCustomers',
            'pendingOrders'
        ));
    }

    public function distributorDashboard()
    {
        $user = Auth::user();
        $distributor = $user->distributor;
        
        if (!$distributor) {
            return redirect()->route('distributor.profile.create');
        }
        
        $stats = [
            'total_customers' => $distributor->customers()->count(),
            'total_orders' => $distributor->orders()->count(),
            'pending_orders' => $distributor->orders()->where('order_status', 'pending_payment')->count(),
            'recent_orders' => $distributor->orders()->with('customer')->latest()->take(5)->get(),
        ];
        
        return view('distributor.dashboard', compact('stats', 'distributor'));
    }

    public function factoryDashboard()
    {
        // Redirect to the new factory controller dashboard
        return app(\App\Http\Controllers\FactoryController::class)->dashboard();
    }
} 