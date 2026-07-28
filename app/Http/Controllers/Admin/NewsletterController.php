<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->search, fn($q, $v) => $q->where('email', 'like', "%{$v}%"))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $email = $subscriber->email;
        $subscriber->delete();

        auth('admin')->user()->log('deleted_newsletter_subscriber', 'NewsletterSubscriber', null, ['email' => $email]);

        return back()->with('success', "Removed '{$email}' from the subscriber list.");
    }
}
