<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Notification;
use Auth;

class NotificationController extends Controller
{
	public function readNotification($id)
	{
		$url = '';
		$notification = Auth::user()->unreadNotifications->where('id', $id)->first()->toArray();
		foreach ($notification['data'] as $key => $data) {
			$url = $data['actionURL'];
		}
		Auth::user()->unreadNotifications->where('id', $id)->markAsRead();
		return redirect()->to($url);
	}

	public function readAllNotification()
	{
		Auth::user()->unreadNotifications->markAsRead();
		return redirect()->back();
	}
}
