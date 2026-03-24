# Phase 03 - Polling-Based Live Room Implementation ✅

## What Was Built

### 1. Database Migration
- **File:** `database/migrations/2026_03_23_120738_create_session_messages_table.php`
- **Schema:**
  - `room_id` - Foreign key to rooms table
  - `sender_type` - Enum: party_a, party_b, lex
  - `content` - Message text
  - `phase` - Session phase (opening, evidence, cross_examination, analysis, resolution)
  - Indexed on `room_id` and `created_at` for fast polling queries

### 2. Models
- **SessionMessage** (`app/Models/SessionMessage.php`)
  - Relationship to Room model
  - Stores all chat messages

### 3. Controllers
- **ChatController** (`app/Http/Controllers/ChatController.php`)
  - `poll()` - Returns new messages since last poll + timer state + phase
  - `sendMessage()` - Saves message and triggers Lex response
  - `startSession()` - Activates room and initializes timer
  - `changePhase()` - Updates session phase

### 4. Jobs
- **ProcessLexResponse** (`app/Jobs/ProcessLexResponse.php`)
  - Queued job that calls ClaudeService
  - Processes conversation history
  - Saves Lex AI response to database
  - Runs asynchronously so chat doesn't block

### 5. Commands
- **DecrementRoomTimers** (`app/Console/Commands/DecrementRoomTimers.php`)
  - Runs every second via Laravel Scheduler
  - Decrements Redis timer for all active rooms
  - Auto-completes rooms when time expires

### 6. Frontend
- **Live Room View** (`resources/views/rooms/show.blade.php`)
  - Alpine.js polling component
  - Polls `/rooms/{uuid}/poll` every 2 seconds
  - Real-time message display (Party A blue, Party B purple, Lex gold)
  - Timer countdown display
  - Phase indicator
  - Evidence vault integration
  - Auto-scroll to new messages

### 7. Routes
```php
GET  /rooms/{uuid}/poll          - Poll for new messages/state
POST /rooms/{uuid}/messages      - Send message
POST /rooms/{uuid}/start         - Start session
POST /rooms/{uuid}/phase         - Change phase
```

---

## How It Works

### Polling Flow
```
Frontend (every 2 seconds)
    ↓
GET /rooms/{uuid}/poll?since=123
    ↓
ChatController::poll()
    ↓
Returns: {
    messages: [...new messages...],
    timer: { remaining_seconds: 1800 },
    phase: 'opening',
    lex_processing: false,
    status: 'active'
}
    ↓
Frontend updates UI
```

### Message Flow
```
User types message
    ↓
POST /rooms/{uuid}/messages
    ↓
ChatController::sendMessage()
    ↓
Save to session_messages table
    ↓
Dispatch ProcessLexResponse job
    ↓
Job calls ClaudeService
    ↓
Lex response saved to database
    ↓
Next poll picks up both messages
```

### Timer Flow
```
Laravel Scheduler (every second)
    ↓
DecrementRoomTimers command
    ↓
Redis::decr("room:{id}:timer")
    ↓
Frontend polls and displays countdown
    ↓
When timer hits 0:
    - Room status → 'completed'
    - Session ends
```

---

## Setup Instructions

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Start Queue Worker (Required for Lex responses)
```bash
php artisan queue:work --queue=default
```

### 3. Start Scheduler (Required for timer)
```bash
php artisan schedule:work
```

Or add to crontab:
```bash
* * * * * cd /path/to/lexroom && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Ensure Redis is Running
```bash
redis-cli ping
# Should return: PONG
```

---

## Testing the System

### 1. Create a Room
- Go to `/rooms/create`
- Fill in dispute details
- Submit to create room

### 2. Start Session
- Open room: `/rooms/{uuid}`
- Click "Start Session" button
- Timer begins counting down
- Lex sends welcome message

### 3. Send Messages
- Type message as Party A or Party B
- Press Enter or click Send
- Message appears immediately
- Lex response appears after 5-15 seconds

### 4. Watch Polling
- Open browser DevTools → Network tab
- See `/poll` requests every 2 seconds
- Status 200, fast response (<100ms)

---

## Performance Optimization

### Efficient Polling
- Only fetches messages with `id > lastMessageId`
- Uses indexed query on `room_id` and `created_at`
- Returns minimal JSON payload
- 2-second interval balances real-time feel vs server load

### Redis Timer
- Timer stored in Redis (fast in-memory)
- Single decrement operation per room per second
- No database writes until session ends

### Queued Lex Responses
- Lex API calls run in background queue
- Chat doesn't block waiting for AI
- "Lex is analyzing..." indicator shows processing state

---

## cPanel Deployment

### Queue Worker on cPanel
Since cPanel doesn't support long-running processes, use **cron jobs**:

```bash
# Run queue worker every minute (processes pending jobs)
* * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty

# Run scheduler every minute (handles timer decrements)
* * * * * cd /home/username/public_html && php artisan schedule:run
```

### Alternative: Database Queue Driver
Already configured in `.env`:
```env
QUEUE_CONNECTION=database
```

This stores jobs in `jobs` table - works perfectly on cPanel.

---

## What's Different from WebSockets

| Feature | WebSockets (Reverb) | Polling (This Implementation) |
|---------|---------------------|-------------------------------|
| Real-time | Instant | 2-second delay |
| Server Load | Low (persistent connection) | Moderate (HTTP requests) |
| cPanel Support | ❌ No | ✅ Yes |
| Complexity | High | Low |
| Fallback | Requires polling anyway | Native |
| Scalability | Requires separate server | Works on shared hosting |

---

## Next Steps

### Immediate
1. ✅ Run migration: `php artisan migrate`
2. ✅ Start queue worker
3. ✅ Start scheduler
4. ✅ Test room creation and chat

### Phase 04 - Evidence Vault (Next)
- PDF text extraction
- Image OCR
- Lex evidence analysis
- Contradiction detection

### Phase 05 - Paystack Billing
- Payment integration
- Split payment flow
- Session activation on payment

### Phase 06 - Reports
- PDF generation
- Email delivery
- LexRefer lawyer directory

---

## Files Created/Modified

### New Files
- `database/migrations/2026_03_23_120738_create_session_messages_table.php`
- `app/Models/SessionMessage.php`
- `app/Http/Controllers/ChatController.php`
- `app/Jobs/ProcessLexResponse.php`
- `app/Console/Commands/DecrementRoomTimers.php`
- `resources/views/rooms/show.blade.php` (replaced)

### Modified Files
- `routes/web.php` - Added chat routes
- `routes/console.php` - Added scheduler

### Existing Files Used
- `app/Services/ClaudeService.php` - Already created, now integrated
- `config/services.php` - Claude config already present
- `.env` - CLAUDE_API_KEY already added

---

## Troubleshooting

### Messages not appearing
- Check queue worker is running: `php artisan queue:work`
- Check Redis is running: `redis-cli ping`
- Check browser console for polling errors

### Timer not counting down
- Check scheduler is running: `php artisan schedule:work`
- Check Redis connection in `.env`
- Verify room status is 'active'

### Lex not responding
- Check CLAUDE_API_KEY in `.env`
- Check `storage/logs/laravel.log` for API errors
- Verify queue worker is processing jobs

### Polling too slow/fast
- Adjust interval in `show.blade.php`: `setInterval(() => this.poll(), 2000)`
- 2000ms = 2 seconds (recommended)
- Lower = more real-time, higher load
- Higher = less load, more delay

---

**Status: Phase 03 Complete ✅**
**Next: Phase 04 - Evidence Vault + Lex AI Mediator**
