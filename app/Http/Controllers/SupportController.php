<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->withCount('messages')
            ->with('latestMessage')
            ->latest()
            ->paginate(15);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        $types = SupportTicket::TYPES;
        $user  = auth()->user();
        return view('support.create', compact('types', 'user'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:200'],
            'type'    => ['required', 'in:' . implode(',', array_keys(SupportTicket::TYPES))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'user_id'      => $user?->id,
            'name'         => $data['name'],
            'email'        => $data['email'],
            'subject'      => $data['subject'],
            'type'         => $data['type'],
            'status'       => 'open',
            'priority'     => 'normal',
            'last_reply_at'=> now(),
        ]);

        $ticket->messages()->create([
            'sender_type' => 'user',
            'user_id'     => $user?->id,
            'sender_name' => $data['name'],
            'body'        => $data['message'],
        ]);

        $redirect = $user
            ? redirect()->route('support.show', $ticket->uuid)->with('success', 'Your ticket has been submitted. We\'ll get back to you shortly.')
            : redirect()->route('support.create')->with('success', 'Your message has been sent. If you have an account, log in to track replies.');

        return $redirect;
    }

    public function show(string $uuid)
    {
        $ticket = SupportTicket::where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->with('messages')
            ->firstOrFail();

        return view('support.show', compact('ticket'));
    }

    public function reply(Request $request, string $uuid)
    {
        $ticket = SupportTicket::where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$ticket->isOpen()) {
            return back()->with('error', 'This ticket is closed and no longer accepting replies.');
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        $ticket->messages()->create([
            'sender_type' => 'user',
            'user_id'     => auth()->id(),
            'sender_name' => auth()->user()->name,
            'body'        => $data['message'],
        ]);

        $ticket->update([
            'status'        => 'open',
            'last_reply_at' => now(),
        ]);

        return back()->with('success', 'Reply sent.');
    }
}
