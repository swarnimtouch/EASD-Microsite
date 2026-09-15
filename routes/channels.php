<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('comments.webinars.admin', function ($admin) {
    return $admin->type === 'admin' && $admin->status === 'active';
}, ['guards' => ['admin']]);
