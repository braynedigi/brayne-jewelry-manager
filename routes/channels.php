<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Admin channel
Broadcast::channel('admin', function ($user) {
    return $user->isAdmin();
});

// Factory channel
Broadcast::channel('factory', function ($user) {
    return $user->isFactory();
});

// Distributor channel
Broadcast::channel('distributor.{id}', function ($user, $id) {
    return $user->isDistributor() && $user->distributor_id == $id;
});

// Order-specific channel
Broadcast::channel('order.{id}', function ($user, $id) {
    $order = \App\Models\Order::find($id);
    if (!$order) {
        return false;
    }

    // Admin can access all orders
    if ($user->isAdmin()) {
        return true;
    }

    // Factory can access production orders
    if ($user->isFactory()) {
        $productionStatuses = ['approved', 'in_production', 'finishing', 'ready_for_delivery'];
        return in_array($order->order_status, $productionStatuses);
    }

    // Distributor can access their own orders
    if ($user->isDistributor()) {
        return $order->distributor_id == $user->distributor_id;
    }

    return false;
});

// User-specific notifications
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// General notifications channel
Broadcast::channel('notifications', function ($user) {
    return auth()->check();
}); 