<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // Ambil semua notifikasi user, terbaru dulu, paginate 15 per halaman
        $notifications = $user->notifications()->latest()->paginate(15);

        return view('notifications.index', compact('notifications'));
    }
}
