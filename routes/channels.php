<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Define a channel for order status updates
Broadcast::channel('orders.{userId}', function ($user, $userId) {
    // Check if the user is authorized to listen to this order's events
    return (int) $user->id === (int) $userId;
});
