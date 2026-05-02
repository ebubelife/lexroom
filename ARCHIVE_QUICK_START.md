# Archive System — Quick Start Guide

## 🚀 Setup (One-Time)

```bash
# Run the migration
php artisan migrate
```

## 📍 Admin Panel Access

### View Archived Rooms
```
URL: /f-med/admin/rooms/archived
```

### From Active Rooms Page
Click the **"📦 Archived"** button in the top-right toolbar

## 🎯 Actions Available

### 1. Archive a Room (Manual)
- Go to room details page
- Click **"📦 Archive Room"** in actions panel
- Room is marked as archived but stays visible
- Sets `archived_at` timestamp

### 2. Delete a Room (Soft Delete)
- Go to room details page
- Click **"🗑 Delete Room"**
- Enter case ID to confirm
- Room is soft deleted (can be restored)
- Sets `deleted_at` timestamp

### 3. Restore an Archived Room
- Go to **Archived Rooms** page
- Find the room
- Click **"Actions"** → **"✓ Restore"**
- Room returns to active list
- Clears both timestamps

### 4. Permanently Delete (⚠️ Cannot Undo)
- Go to **Archived Rooms** page
- Find the room
- Click **"Actions"** → **"✗ Delete Forever"**
- Confirm the action
- Room and ALL data permanently deleted

## 🔍 What Gets Archived

When a room is archived/deleted:
- ✅ Room record preserved
- ✅ All messages preserved
- ✅ All evidence files preserved
- ✅ All billing records preserved
- ✅ All extensions preserved
- ✅ Report preserved
- ✅ All relationships intact

## 🗑️ What Gets Permanently Deleted

When you force delete:
- ❌ Room record deleted
- ❌ All messages deleted
- ❌ All evidence files deleted (from storage too)
- ❌ All billing records deleted
- ❌ All extensions deleted
- ❌ Report deleted
- ⚠️ **This cannot be undone!**

## 📊 Archived Rooms View

Shows:
- Case ID
- Title
- Parties
- Category
- Status
- Archived date (if manually archived)
- Deleted date (if soft deleted)
- Action buttons

## 🎨 Visual Indicators

- **Purple badge** = Archived rooms link
- **Green button** = Restore action
- **Red button** = Permanent delete action
- **Timestamps** = Shows when archived/deleted

## 🔒 Safety Features

1. **Soft delete by default** - Nothing lost accidentally
2. **Confirmation required** - For permanent deletes
3. **Audit logging** - All actions tracked
4. **Search preserved** - Can search archived rooms
5. **Separate view** - Archived rooms don't clutter active list

## 💡 Best Practices

1. **Archive completed rooms** after 90 days
2. **Review archived rooms** quarterly
3. **Permanently delete** only after legal retention period
4. **Export data** before permanent deletion (if needed)
5. **Document reasons** in admin notes

## 🐛 Troubleshooting

### Room not appearing in archived view?
- Check if it's actually deleted (not just completed)
- Verify `deleted_at` or `archived_at` is set

### Can't restore a room?
- Ensure you're using the correct room ID
- Check admin permissions

### Permanent delete not working?
- Verify confirmation prompt
- Check for foreign key constraints

## 📝 Admin Audit Log

All actions are logged:
- `archived_room` - Manual archive
- `deleted_room` - Soft delete
- `restored_room` - Restore from archive
- `permanently_deleted_room` - Force delete

Check admin audit log for full history.

---

**Need Help?** Check `ARCHIVE_SYSTEM.md` for full technical details.
