<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['partyA', 'partyB'])
            ->withCount(['messages', 'evidenceFiles'])
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('case_id', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('partyA', fn($u) => $u->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"))
                  ->orWhereHas('partyB', fn($u) => $u->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $rooms = $query->paginate(25)->withQueryString();

        $statuses   = ['pending', 'waiting_for_party_b', 'active', 'paused', 'locked', 'completed', 'escalated', 'expired'];
        $categories = ['tenancy', 'freelance', 'business', 'ecommerce', 'employment', 'debt'];

        return view('admin.rooms.index', compact('rooms', 'statuses', 'categories'));
    }

    public function archived(Request $request)
    {
        $query = Room::onlyTrashed()
            ->orWhereNotNull('archived_at')
            ->with(['partyA', 'partyB'])
            ->withCount(['messages', 'evidenceFiles'])
            ->latest('deleted_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('case_id', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('partyA', fn($u) => $u->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"))
                  ->orWhereHas('partyB', fn($u) => $u->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        }

        $rooms = $query->paginate(25)->withQueryString();
        $categories = ['tenancy', 'freelance', 'business', 'ecommerce', 'employment', 'debt'];

        return view('admin.rooms.archived', compact('rooms', 'categories'));
    }

    public function show(Room $room)
    {
        $room->load([
            'partyA', 'partyB',
            'billing',
            'evidenceFiles',
            'extensions.user',
            'report',
        ]);

        $messages = $room->messages()->orderBy('created_at')->get();

        return view('admin.rooms.show', compact('room', 'messages'));
    }

    public function forceLock(Room $room)
    {
        $room->update(['status' => 'locked', 'ended_at' => $room->ended_at ?? now()]);
        auth('admin')->user()->log('force_locked_room', 'Room', $room->id);

        return back()->with('success', "Room {$room->case_id} locked.");
    }

    public function forceExpire(Room $room)
    {
        $room->update(['status' => 'expired', 'ended_at' => $room->ended_at ?? now()]);
        auth('admin')->user()->log('force_expired_room', 'Room', $room->id);

        return back()->with('success', "Room {$room->case_id} expired.");
    }

    public function addTime(Request $request, Room $room)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1|max:120',
            'reason'  => 'required|string|max:255',
        ]);

        $minutes = (int) $request->input('minutes');
        $room->increment('extended_minutes', $minutes);

        auth('admin')->user()->log('added_time_to_room', 'Room', $room->id, [
            'minutes' => $minutes,
            'reason'  => $request->input('reason'),
        ]);

        return back()->with('success', "{$minutes} minutes added to {$room->case_id}.");
    }

    public function destroy(Request $request, Room $room)
    {
        $request->validate([
            'confirm_case_id' => ['required', 'in:' . $room->case_id],
        ]);

        $caseId = $room->case_id;
        auth('admin')->user()->log('deleted_room', 'Room', $room->id, ['case_id' => $caseId]);
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', "Room {$caseId} deleted.");
    }

    public function archive(Room $room)
    {
        $room->update(['archived_at' => now()]);
        auth('admin')->user()->log('archived_room', 'Room', $room->id, ['case_id' => $room->case_id]);

        return back()->with('success', "Room {$room->case_id} archived.");
    }

    public function restore($id)
    {
        $room = Room::withTrashed()->findOrFail($id);
        $room->restore();
        $room->update(['archived_at' => null]);
        auth('admin')->user()->log('restored_room', 'Room', $room->id, ['case_id' => $room->case_id]);

        return back()->with('success', "Room {$room->case_id} restored.");
    }

    public function forceDelete($id)
    {
        $room = Room::withTrashed()->findOrFail($id);
        $caseId = $room->case_id;
        auth('admin')->user()->log('permanently_deleted_room', 'Room', $room->id, ['case_id' => $caseId]);
        $room->forceDelete();

        return back()->with('success', "Room {$caseId} permanently deleted.");
    }
}
