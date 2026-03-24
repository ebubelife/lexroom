# Quick Start - Test Polling System

## Prerequisites
- MySQL running
- Redis running
- Composer dependencies installed
- Node dependencies installed

## 1. Database Setup

```bash
# Start MySQL (if not running)
mysql.server start

# Run migrations
php artisan migrate

# Verify tables created
php artisan tinker
>>> \DB::table('session_messages')->count()
```

## 2. Start Required Services

Open **3 terminal windows**:

### Terminal 1 - Laravel Server
```bash
php artisan serve
```
Access: http://127.0.0.1:8000

### Terminal 2 - Queue Worker (for Lex responses)
```bash
php artisan queue:work --queue=default
```

### Terminal 3 - Scheduler (for timer)
```bash
php artisan schedule:work
```

## 3. Test the System

### Step 1: Register/Login
1. Go to http://127.0.0.1:8000/register
2. Create account
3. Verify email (check logs: `storage/logs/laravel.log`)
4. Verify phone (OTP: `111111`)

### Step 2: Create a Room
1. Go to Dashboard → "Create New Room"
2. Fill in:
   - Category: Freelance
   - Jurisdiction: Nigeria
   - Language: English
   - Duration: 30 minutes
   - Case summary: "Dispute over website payment"
3. Submit

### Step 3: Open Room
1. Click on created room
2. You'll see the live room interface
3. Click "Start Session" button
4. Timer starts counting down
5. Lex sends welcome message

### Step 4: Test Chat
1. Type a message: "I hired the developer for $1,500"
2. Press Enter or click Send
3. Message appears immediately (blue bubble)
4. Wait 5-15 seconds
5. Lex responds (gold box)

### Step 5: Monitor Polling
1. Open Browser DevTools (F12)
2. Go to Network tab
3. Filter: "poll"
4. See requests every 2 seconds
5. Click on a request → Preview → See JSON response

## 4. Test Evidence Upload

1. In the room, click "Upload Evidence" (right sidebar)
2. Select a PDF/image file
3. File appears in Evidence Vault
4. Lex can reference it in responses

## 5. Verify Timer

1. Watch timer countdown in real-time
2. Check Redis:
   ```bash
   redis-cli
   > KEYS room:*:timer
   > GET room:1:timer
   ```
3. Timer decrements every second

## 6. Test Queue Processing

Check queue worker terminal - you should see:
```
[2026-03-23 12:07:38] Processing: App\Jobs\ProcessLexResponse
[2026-03-23 12:07:45] Processed:  App\Jobs\ProcessLexResponse
```

## 7. Check Logs

```bash
tail -f storage/logs/laravel.log
```

Look for:
- OTP codes
- Lex API calls
- Queue job processing
- Any errors

## Troubleshooting

### "Connection refused" error
**Problem:** MySQL not running  
**Solution:** `mysql.server start`

### "Connection to Redis failed"
**Problem:** Redis not running  
**Solution:** `redis-server` or `brew services start redis`

### Messages not appearing
**Problem:** Queue worker not running  
**Solution:** Start queue worker in Terminal 2

### Timer not counting down
**Problem:** Scheduler not running  
**Solution:** Start scheduler in Terminal 3

### Lex not responding
**Problem:** Invalid Claude API key  
**Solution:** Check `.env` → `CLAUDE_API_KEY`

### Polling not working
**Problem:** JavaScript error  
**Solution:** Check browser console (F12)

## Expected Behavior

### Polling Request (every 2 seconds)
```
GET /rooms/abc-123/poll?since=0
Response: {
  "messages": [
    {
      "id": 1,
      "sender_type": "lex",
      "content": "Welcome to FirstMediator...",
      "phase": "opening",
      "created_at": "2026-03-23T12:00:00.000000Z"
    }
  ],
  "timer": {
    "remaining_seconds": 1800,
    "total_seconds": 1800
  },
  "phase": "opening",
  "lex_processing": false,
  "status": "active"
}
```

### Send Message Request
```
POST /rooms/abc-123/messages
Body: {
  "content": "I hired the developer for $1,500",
  "sender_type": "party_a"
}
Response: {
  "success": true,
  "message": { ... }
}
```

## Performance Metrics

### Good Performance
- Poll response time: <100ms
- Lex response time: 5-15 seconds
- Timer accuracy: ±1 second
- Message delivery: <2 seconds

### If Slow
- Check Redis connection
- Check database indexes
- Reduce poll interval (increase from 2s to 3s)
- Check Claude API latency

## Next Steps

Once polling works:
1. ✅ Test with 2 browser windows (Party A + Party B)
2. ✅ Test evidence upload
3. ✅ Test timer expiration
4. ✅ Test phase changes
5. ✅ Move to Phase 05 - Paystack integration

## Demo Script

**Perfect demo flow:**
1. Register → Verify → Dashboard (30 seconds)
2. Create room → Fill details (1 minute)
3. Start session → Timer begins (5 seconds)
4. Send message → Lex responds (20 seconds)
5. Upload evidence → Appears in vault (10 seconds)
6. Continue conversation → Show real-time updates (2 minutes)

**Total demo time: ~4 minutes**

---

**Status:** Ready to test! 🚀  
**Next:** Run the 3 terminals and follow Step 1-7 above.
