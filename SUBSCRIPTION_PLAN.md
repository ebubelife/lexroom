# Subscription System — Build Plan
**FirstMediator / Symbi Technologies Ltd**  
**Currency: GBP (£)**  
**Version: 1.0 — April 2026**

---

## Overview

Users can either pay per session or subscribe to a monthly/quarterly/yearly plan.
Subscribers get a credit allowance each billing cycle which is deducted when they create sessions or extend them.
All prices are in **GBP (£)**.

---

## Database Schema

### 1. `subscription_plans`
Seeded with 3 plans. Admin can edit/add/disable from LexConsole.

```
id
name                  — Starter / Standard / Pro
slug                  — starter / standard / pro
description           — Short marketing description
credits_per_cycle     — e.g. 10.00 (£10 worth of credits)
price_monthly         — e.g. 9.00
price_quarterly       — e.g. 24.00 (saves ~11%)
price_yearly          — e.g. 86.00 (saves ~20%)
stripe_monthly_price_id    — Stripe Price ID for monthly
stripe_quarterly_price_id  — Stripe Price ID for quarterly
stripe_yearly_price_id     — Stripe Price ID for yearly
features              — JSON array of feature strings (display only)
is_active             — boolean
sort_order            — integer (for display ordering)
created_at / updated_at
```

### 2. `user_subscriptions`
One active record per user at a time.

```
id
user_id               — FK → users
plan_id               — FK → subscription_plans
stripe_subscription_id
stripe_customer_id
status                — active / cancelled / past_due / expired
billing_cycle         — monthly / quarterly / yearly
current_period_start  — timestamp
current_period_end    — timestamp
cancelled_at          — nullable timestamp
created_at / updated_at
```

### 3. `credit_settings`
Key-value store. Admin-editable.

```
id
key                   — e.g. 'credits_expire_on_renewal', 'gbp_to_usd_rate'
value                 — e.g. 'true', '1.27'
label                 — Human-readable label for admin UI
group                 — 'credits' / 'currency' / 'topup'
updated_at
```

### 4. `topup_packages`
Admin-defined fixed top-up options.

```
id
label                 — e.g. "Small Top-up"
credits               — e.g. 5.00
price                 — e.g. 5.00 (£5)
bonus_credits         — e.g. 0.50 (10% bonus)
stripe_price_id
is_active             — boolean
sort_order
created_at / updated_at
```

### 5. `credit_transactions`
Full audit trail of every credit movement.

```
id
user_id               — FK → users
amount                — positive = credit, negative = debit
type                  — subscription_grant / topup / session_deduction /
                         extension_deduction / refund / admin_adjustment / expiry
description           — Human-readable e.g. "Standard Plan — Monthly renewal"
room_id               — nullable FK (for session/extension deductions)
created_at
```

---

## Seed Data (3 Plans)

```php
// Starter
name: 'Starter'
credits_per_cycle: 12.00   // £12 worth of sessions
price_monthly: 9.00
price_quarterly: 24.00     // save £3
price_yearly: 86.00        // save £22
features: [
    '£12 session credits per month',
    'Up to 2-3 sessions/month',
    'AI mediation with Lex',
    'PDF mediation report',
    'Evidence vault',
]

// Standard
name: 'Standard'
credits_per_cycle: 27.00
price_monthly: 19.00
price_quarterly: 51.00     // save £6
price_yearly: 182.00       // save £46
features: [
    '£27 session credits per month',
    'Up to 5-6 sessions/month',
    'Everything in Starter',
    'Priority Lex responses',
    'FM Refer access',
]

// Pro
name: 'Pro'
credits_per_cycle: 60.00
price_monthly: 39.00
price_quarterly: 105.00    // save £12
price_yearly: 374.00       // save £94
features: [
    '£60 session credits per month',
    'Unlimited sessions',
    'Everything in Standard',
    'Dedicated support',
    'Custom dispute categories',
]
```

---

## Seed Data (Top-up Packages)

```php
['label' => 'Small',   'credits' => 5.00,  'price' => 5.00,  'bonus' => 0.00]
['label' => 'Medium',  'credits' => 10.00, 'price' => 10.00, 'bonus' => 1.00]  // +10%
['label' => 'Large',   'credits' => 25.00, 'price' => 25.00, 'bonus' => 5.00]  // +20%
['label' => 'XL',      'credits' => 50.00, 'price' => 50.00, 'bonus' => 15.00] // +30%
```

---

## Seed Data (Credit Settings)

```php
['key' => 'credits_expire_on_renewal',  'value' => 'true',  'label' => 'Expire unused credits on renewal', 'group' => 'credits']
['key' => 'credits_to_minutes_rate',    'value' => '4',     'label' => 'Minutes per £1 credit',            'group' => 'credits']
['key' => 'gbp_to_usd_rate',            'value' => '1.27',  'label' => 'GBP → USD rate',                   'group' => 'currency']
['key' => 'gbp_to_eur_rate',            'value' => '1.17',  'label' => 'GBP → EUR rate',                   'group' => 'currency']
```

---

## Session Pricing (GBP)

| Plan | Duration | Full Price | Split (each) |
|------|----------|------------|--------------|
| Starter | 30 min | £3.50 | £1.75 |
| Standard | 60 min | £6.00 | £3.00 |
| Extended | 90 min | £8.00 | £4.00 |

### Extension Pricing (GBP)
| Extension | Price |
|-----------|-------|
| +30 mins | £2.00 |
| +60 mins | £3.50 |

---

## User Flow

### Flow 1: No Subscription — Creating a Room

```
User fills room creation form → clicks "Continue to Payment"
        ↓
System checks: active subscription?
        ↓ NO
Modal appears:

┌─────────────────────────────────────────────┐
│  How would you like to pay?                 │
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │ 📋 Subscribe & Save                 │   │
│  │                                     │   │
│  │ [Starter £9/mo] [Standard £19/mo]  │   │
│  │ [Pro £39/mo]                        │   │
│  │                                     │   │
│  │ Monthly / Quarterly / Yearly tabs   │   │
│  │                                     │   │
│  │ [Subscribe Now →]                   │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  ──────────── or ────────────               │
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │ 💳 Pay for this session only        │   │
│  │    £6.00 — no commitment            │   │
│  │    [Pay £6.00 →]                    │   │
│  └─────────────────────────────────────┘   │
└─────────────────────────────────────────────┘

If Subscribe → Stripe subscription checkout
  → Webhook fires → credits added to wallet
  → User redirected back to dashboard
  → User creates room again (uses credits this time)

If Pay session → Stripe one-off checkout
  → Webhook fires → room activated
```

### Flow 2: Has Subscription — Enough Credits

```
User creates room (£6.00 session)
        ↓
System checks: active subscription? YES
Credits sufficient? YES (£15.00 remaining)
        ↓
Confirmation shown:
"£6.00 will be deducted from your credits (£15.00 → £9.00)"
[Confirm & Create Room]
        ↓
Credits deducted → room created → session starts
```

### Flow 3: Has Subscription — Insufficient Credits

```
User creates room (£6.00 session)
        ↓
System checks: active subscription? YES
Credits sufficient? NO (£3.00 remaining, need £6.00)
        ↓
Modal appears:
"You have £3.00 credits remaining. Not enough for this session (£6.00)."

Options:
1. Top up credits → top-up packages shown
2. Pay for this session only → Stripe one-off
3. Upgrade plan → subscription plans shown
```

### Flow 4: Subscription Renewal (Stripe Webhook)

```
Stripe fires: invoice.payment_succeeded
        ↓
Webhook handler:
  - Find user_subscription by stripe_subscription_id
  - Check credit_settings: credits_expire_on_renewal
    - TRUE  → reset wallet credits_balance to plan credits_per_cycle
    - FALSE → add plan credits_per_cycle to existing balance
  - Log credit_transaction (type: subscription_grant)
  - Update current_period_start / current_period_end
```

---

## Stripe Setup Required

### Products & Prices to Create in Stripe Dashboard

For each plan × billing cycle = 9 Stripe Prices total:
```
Starter Monthly    £9.00/month   → stripe_monthly_price_id
Starter Quarterly  £24.00/3mo   → stripe_quarterly_price_id
Starter Yearly     £86.00/year  → stripe_yearly_price_id
Standard Monthly   £19.00/month
Standard Quarterly £51.00/3mo
Standard Yearly    £182.00/year
Pro Monthly        £39.00/month
Pro Quarterly      £105.00/3mo
Pro Yearly         £374.00/year
```

Also create Stripe Prices for top-up packages (one-off payments):
```
Top-up Small  £5.00
Top-up Medium £10.00
Top-up Large  £25.00
Top-up XL     £50.00
```

### Webhook Events to Add in Stripe Dashboard

Add these to the existing `fm-webhook` endpoint:
```
checkout.session.completed       ← already listening
invoice.payment_succeeded        ← subscription renewal
customer.subscription.deleted    ← cancellation
customer.subscription.updated    ← upgrade/downgrade
invoice.payment_failed           ← already listening
```

---

## Pages to Build

### 1. Pricing Page (`/pricing`)
Public page. Shows all 3 plans with features.
- Monthly / Quarterly / Yearly toggle (Alpine.js)
- "Subscribe Now" → goes to checkout if logged in, else register
- "Pay per session" option at bottom
- Brand colors, dark/light mode, mobile responsive

### 2. Payment Modal (on Room Creation)
Alpine.js modal triggered after room form submission.
- Shows when user has no subscription or insufficient credits
- Plan cards with billing cycle toggle
- "Pay for session only" option
- Dark/light mode, mobile responsive

### 3. Settings → Subscription Tab (`/settings/subscription`)
- Current plan card (name, status, next billing date)
- Credits remaining + progress bar
- Upgrade / Cancel buttons
- Top-up credits section (package cards)
- Billing history table (last 10 transactions)
- Dark/light mode, mobile responsive

### 4. Admin → Subscription Plans (`/admin/plans`)
- List all plans with edit/toggle active
- Create new plan form
- Edit credits, prices, features
- Stripe Price ID fields

### 5. Admin → Credit Settings (`/admin/credits`)
- Toggle: expire credits on renewal
- Credits to minutes rate
- Currency rates (GBP→USD, GBP→EUR)
- Top-up packages (CRUD)

---

## Controllers & Services to Build

```
app/Services/SubscriptionService.php
  - getActiveSub(User)
  - hasEnoughCredits(User, amount)
  - deductCredits(User, amount, type, description, room_id)
  - grantCredits(User, amount, type, description)
  - cancelSubscription(User)
  - upgradeSubscription(User, newPlan, cycle)

app/Http/Controllers/SubscriptionController.php
  - index()          — pricing page
  - checkout()       — create Stripe subscription checkout
  - topup()          — create Stripe top-up checkout
  - cancel()         — cancel subscription
  - upgrade()        — upgrade plan

app/Http/Controllers/Admin/PlanController.php
  - index, create, store, edit, update, toggle

app/Http/Controllers/Admin/CreditSettingsController.php
  - index, update
```

### Webhook Updates
Add to `StripeWebhookController`:
```
handleSubscriptionRenewal()    — invoice.payment_succeeded
handleSubscriptionCancelled()  — customer.subscription.deleted
handleSubscriptionUpdated()    — customer.subscription.updated
handleTopupCompleted()         — checkout.session.completed (type: topup)
```

---

## Models to Build

```
SubscriptionPlan
UserSubscription
CreditSetting
TopupPackage
CreditTransaction
```

---

## Build Order

### Phase 1 — Database & Models (Day 1)
- [ ] Migrations: subscription_plans, user_subscriptions, credit_settings, topup_packages, credit_transactions
- [ ] Models with relationships
- [ ] Seeders: 3 plans, 4 top-up packages, credit settings
- [ ] SubscriptionService (core logic)

### Phase 2 — Stripe Setup (Day 1)
- [ ] Create 9 subscription prices in Stripe dashboard
- [ ] Create 4 top-up prices in Stripe dashboard
- [ ] Add new webhook events in Stripe dashboard
- [ ] Update StripeWebhookController

### Phase 3 — User Flows (Day 2)
- [ ] Payment modal on room creation
- [ ] Credits deduction on session/extension
- [ ] SubscriptionController (checkout, topup, cancel, upgrade)
- [ ] Settings → Subscription tab

### Phase 4 — Public Pricing Page (Day 2)
- [ ] /pricing page with plan cards
- [ ] Monthly/quarterly/yearly toggle
- [ ] Mobile responsive, dark/light mode

### Phase 5 — Admin Controls (Day 3)
- [ ] Admin plan management
- [ ] Admin credit settings
- [ ] Admin top-up package management

---

## Design Notes

- **Font:** Instrument Serif (headings) + DM Sans (body)
- **Colors:** Navy `#0D1B2A`, Gold `#C9A84C`, Gold Pale `#F5EDD6`
- **Dark mode:** All pages support `[data-theme="dark"]` CSS variables
- **Mobile:** All pages mobile-first, tested at 375px, 768px, 1280px
- **Buttons:** Gold for primary CTA, Navy for secondary, outlined for cancel
- **Cards:** Rounded-xl, subtle border, hover-lift effect
- **Icons:** Consistent white icons on navy background (matching category cards)

---

## What Admin Can Control

| Setting | Location |
|---------|----------|
| Plan names, prices, credits | Admin → Plans |
| Enable/disable plans | Admin → Plans |
| Top-up packages | Admin → Credits |
| Credits expire on renewal | Admin → Credits |
| Credits to minutes rate | Admin → Credits |
| GBP → USD rate | Admin → Credits |
| GBP → EUR rate | Admin → Credits |
| View all subscriptions | Admin → Users |
| Manually adjust credits | Admin → Users → User detail |

---

## Notes

- All prices in **GBP (£)**
- Stripe handles all recurring billing automatically
- No card data touches our servers
- Party B never benefits from Party A's subscription (Party B always pays cash for split)
- Subscription credits are separate from referral free minutes
- Top-up credits never expire (only subscription credits expire on renewal)
- Admin can manually adjust a user's credits from the user detail page

---

*Ready to build once Stripe prices are created in dashboard.*
