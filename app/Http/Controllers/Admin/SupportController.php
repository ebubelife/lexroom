<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SupportReplyMail;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('user', 'latestMessage')
            ->withCount('messages');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $tickets = $query->latest('last_reply_at')->paginate(25)->withQueryString();

        $counts = [
            'open'        => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'waiting'     => SupportTicket::where('status', 'waiting')->count(),
            'resolved'    => SupportTicket::where('status', 'resolved')->count(),
            'closed'      => SupportTicket::where('status', 'closed')->count(),
        ];

        $types      = SupportTicket::TYPES;
        $statuses   = SupportTicket::STATUSES;
        $priorities = SupportTicket::PRIORITIES;

        return view('admin.support.index', compact('tickets', 'counts', 'types', 'statuses', 'priorities'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('messages.admin', 'messages.user', 'user');
        $types      = SupportTicket::TYPES;
        $statuses   = SupportTicket::STATUSES;
        $priorities = SupportTicket::PRIORITIES;

        return view('admin.support.show', compact('ticket', 'types', 'statuses', 'priorities'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'body'        => ['required', 'string', 'min:5', 'max:10000'],
            'is_internal' => ['boolean'],
            'status'      => ['nullable', 'in:open,in_progress,waiting,resolved,closed'],
            'priority'    => ['nullable', 'in:low,normal,high,urgent'],
        ]);

        $isInternal = (bool) ($data['is_internal'] ?? false);

        $message = $ticket->messages()->create([
            'sender_type' => 'admin',
            'admin_id'    => auth('admin')->id(),
            'sender_name' => auth('admin')->user()->name,
            'body'        => $data['body'],
            'is_internal' => $isInternal,
        ]);

        $updates = ['last_reply_at' => now()];

        if (!empty($data['status'])) {
            $updates['status'] = $data['status'];
        } elseif (!$isInternal) {
            $updates['status'] = 'waiting';
        }

        if (!empty($data['priority'])) {
            $updates['priority'] = $data['priority'];
        }

        $ticket->update($updates);

        if (!$isInternal) {
            Mail::to($ticket->email)->send(new SupportReplyMail($ticket, $message));
        }

        auth('admin')->user()->log('support_reply', 'SupportTicket', $ticket->id, [
            'internal' => $isInternal,
        ]);

        return back()->with('success', $isInternal ? 'Internal note added.' : 'Reply sent and email delivered.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'status'   => ['required', 'in:open,in_progress,waiting,resolved,closed'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
        ]);

        $ticket->update(array_filter($data));

        auth('admin')->user()->log('support_status_change', 'SupportTicket', $ticket->id, $data);

        return back()->with('success', 'Ticket updated.');
    }
}
