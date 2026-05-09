# Split Payment Fix — Party B Payment Link

## 🐛 Issue Identified

**Problem:**
- Party A creates room with split payment
- Party A pays their £3.50
- Party B receives invitation email
- ❌ Email has NO payment link
- Party B can't pay, room stays locked
- Status: "waiting_for_party_b"

## ✅ Fix Applied

### What Was Changed

**File: `app/Mail/RoomInvitation.php`**
- Added `$needsPayment` property
- Added `$paymentLink` property
- Logic: If split payment AND Party B hasn't paid → send payment link
- Includes `party_b_payment_token` in URL for security

**File: `resources/views/emails/room-invitation.blade.php`**
- Added conditional payment notice
- Shows payment amount for Party B
- Different CTA button text: "Complete Payment & Join Session"
- Step-by-step instructions for split payment
- Warning box highlighting payment requirement

## 🎯 How It Works Now

### Split Payment Flow (Fixed)

1. **Party A Creates Room**
   - Chooses split payment
   - Enters Party B email
   - Pays their £3.50

2. **Party A Gets Email**
   - Confirmation email
   - Link to room (can enter immediately)

3. **Party B Gets Email** ✅ FIXED
   - Invitation email
   - **⚠️ Payment Required** notice
   - Shows amount: £3.50
   - Button: "Complete Payment & Join Session"
   - Link includes secure payment token

4. **Party B Clicks Link**
   - Taken to payment page
   - Sees session details
   - Pays their £3.50
   - Redirected to room

5. **Room Unlocks**
   - Both parties can now enter
   - Status changes to "active"
   - Session can begin

### Full Payment Flow (Unchanged)

1. Party A creates room
2. Party A pays full amount
3. Party B gets invitation
4. Party B clicks link → enters room directly
5. No payment required for Party B

## 📧 Email Changes

### Before (Broken)
```
Subject: You've been invited...

[Session Details]

What happens next?
- Click button to join
- Present your case
- Upload evidence

[Join Mediation Session] ← Goes to locked room
```

### After (Fixed)
```
Subject: You've been invited...

[Session Details]

⚠️ Payment Required
This is a split payment session. You need to pay 
your share (£3.50) before you can join.

What happens next?
- Step 1: Complete your payment
- Step 2: Join the session
- Step 3: Present your case
- Step 4: Upload evidence
- Step 5: Get mediation report

[Complete Payment & Join Session] ← Goes to payment page
```

## 🔒 Security

### Payment Token
- Each Party B payment link includes unique token
- Token stored in `party_b_payment_token` field
- Expires after 7 days (`party_b_payment_expires_at`)
- Prevents unauthorized access
- One-time use (marked paid after use)

### Validation
- Token must match database
- Token must not be expired
- Party B must not have already paid
- Amount calculated from session package

## 🧪 Testing

### Test Split Payment Flow

1. **Create Room**
   ```
   - Login as Party A
   - Create new room
   - Choose "Split Payment"
   - Enter Party B email
   - Pay £3.50
   ```

2. **Check Party B Email**
   ```
   - Open Party B inbox
   - Find invitation email
   - Verify "Payment Required" notice shows
   - Verify amount shows: £3.50
   - Verify button says "Complete Payment & Join Session"
   ```

3. **Party B Payment**
   ```
   - Click email link
   - Should go to payment page (not room)
   - See session details
   - Pay £3.50
   - Should redirect to room
   ```

4. **Verify Room Unlocked**
   ```
   - Both parties can now enter
   - Status should be "active" or "waiting_for_party_b" → "active"
   - Session can begin
   ```

### Test Full Payment Flow

1. **Create Room**
   ```
   - Login as Party A
   - Create new room
   - Choose "Full Payment"
   - Enter Party B email
   - Pay £7.00
   ```

2. **Check Party B Email**
   ```
   - Open Party B inbox
   - Find invitation email
   - Should NOT show payment notice
   - Button says "Join Mediation Session"
   - Link goes directly to room
   ```

3. **Party B Access**
   ```
   - Click email link
   - Goes directly to room (no payment)
   - Can enter immediately
   ```

## 🔍 Edge Cases Handled

### 1. Party B Already Paid
- Email shows "Join Session" (not payment)
- Link goes to room
- No payment required

### 2. Payment Token Expired
- Shows expiration message
- Option to request new link
- Party A can resend invitation

### 3. Invalid Token
- 403 Forbidden error
- Security measure
- Prevents unauthorized access

### 4. Party B Registered User
- Can login before paying
- Payment still required
- Smoother experience

## 📊 Database Fields Used

```sql
rooms table:
├── payment_type              (full/split)
├── party_a_paid              (boolean)
├── party_b_paid              (boolean)
├── party_b_payment_token     (string, unique)
└── party_b_payment_expires_at (timestamp)

billing table:
├── room_id
├── party                     (party_a/party_b)
├── amount
├── stripe_session_id
└── status                    (pending/paid)
```

## ✨ Benefits

### For Party A
- Clear payment status
- Knows when Party B pays
- Can track progress

### For Party B
- Clear payment instructions
- Knows exact amount
- Secure payment link
- Can't access room without paying

### For Platform
- Prevents unpaid access
- Tracks split payments
- Secure token system
- Better user experience

## 🚀 Deployment

### No Migration Needed
- Uses existing database fields
- Only code changes
- Backward compatible

### Testing Checklist
- [ ] Test split payment creation
- [ ] Verify Party B email content
- [ ] Test Party B payment flow
- [ ] Verify room unlocks after payment
- [ ] Test full payment flow (unchanged)
- [ ] Test expired token handling
- [ ] Test already paid scenario

---

**Status**: ✅ Fixed  
**Files Changed**: 2  
**Breaking Changes**: None  
**Testing**: Required before production
