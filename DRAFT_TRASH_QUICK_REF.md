# Draft & Trash — Quick Reference

## 🎯 For Users

### Save as Draft
**When:** Creating a case but not ready to pay  
**How:** Click "Save as Draft" button on create form  
**Result:** Case saved, no payment, can continue later  
**Location:** Appears in "My Cases" with gray "Draft" badge

### Continue Draft
**When:** Ready to complete a draft case  
**How:** Click "Continue Draft" on draft case card  
**Result:** Taken to payment/completion page

### Move to Trash
**When:** Want to delete a case (reversible)  
**How:** Click "🗑 Move to Trash" on case card  
**Result:** Case moved to trash, can be restored  
**Location:** Trash page (click "Trash" button in header)

### Restore from Trash
**When:** Want to recover a deleted case  
**How:** Go to Trash → Click "↺ Restore"  
**Result:** Case returns to active cases

### Permanently Delete
**When:** Want to remove case forever  
**How:** Go to Trash → Click "✗ Delete" → Confirm  
**Result:** Case and all data deleted (cannot undo)

---

## 🔍 Where to Find Things

| Feature | Location |
|---------|----------|
| Active Cases | `/rooms` |
| Trash | `/rooms/trash` |
| Create Case | `/rooms/create` |
| Trash Button | Header (next to "New Case") |
| Trash Action | Bottom of each case card |

---

## 🎨 Visual Indicators

| Status | Color | Meaning |
|--------|-------|---------|
| Draft | Gray | Incomplete, not paid |
| Pending | Yellow | Awaiting Party B |
| Active | Gold | Session in progress |
| Completed | Green | Finished |
| Trashed | Red accent | In trash |

---

## ⚠️ Important

- **Only creators** can trash/restore cases
- **Trash is reversible** - restore anytime
- **Permanent delete cannot be undone**
- **Drafts don't send emails** or create billing
- **Trashed cases excluded** from dashboard stats

---

## 🚀 Quick Actions

```
Create Draft:     Fill form → "Save as Draft"
Complete Draft:   Case card → "Continue Draft"
Trash Case:       Case card → "Move to Trash"
View Trash:       Header → "Trash" button
Restore:          Trash page → "↺ Restore"
Delete Forever:   Trash page → "✗ Delete" → Confirm
```

---

## 💡 Pro Tips

1. **Save drafts** when unsure about details
2. **Use trash** instead of permanent delete
3. **Review trash** before permanent deletion
4. **Restore quickly** if deleted by mistake
5. **Clean trash** periodically

---

**Need Help?** Check `DRAFT_TRASH_SYSTEM.md` for full details.
