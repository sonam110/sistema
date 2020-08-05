<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Notification;
use Auth;

class NotificationController extends Controller
{
    public function readNotification($id)
	{
		Auth::user()->unreadNotifications->where('id', $id)->markAsRead();
		return redirect()->back();
	}

	public function readAllNotification()
	{
		Auth::user()->unreadNotifications->markAsRead();
		return redirect()->back();
	}
}
