# How the Money & Rewards System Works
**FirstMediator — Plain English Guide**

---

## 💳 Credits — The Basic Idea

Think of credits like a **prepaid balance** in the app, measured in pounds (£).

When a user wants to start a mediation session, the cost is deducted from their credit balance — just like buying something with a gift card.

**Session costs:**
- 30-minute session → £3.50
- 60-minute session → £6.00
- 90-minute session → £8.00

If a user doesn't have a subscription, they simply pay for each session individually via card (Stripe handles the payment).

---

## 📦 Subscriptions — Pay Monthly, Save Money

Instead of paying per session, users can subscribe to a monthly plan and get a bundle of credits upfront. It's cheaper than paying one by one.

**Three plans:**

| Plan | Monthly Price | Credits You Get | Best For |
|------|--------------|-----------------|----------|
| Starter | £9/month | £12 credits | 2–3 sessions/month |
| Standard | £19/month | £27 credits | 5–6 sessions/month |
| Pro | £39/month | £60 credits | Unlimited sessions |

**Example:** A user on the Standard plan pays £19 but gets £27 worth of credits — that's a saving of £8 every month.

They can also pay **quarterly** or **yearly** to save even more (up to 20% off).

**What happens each month:**
- Stripe automatically charges the user
- Their credit balance is refreshed with the plan's credits
- Unused credits from the previous month expire (this can be turned off from admin)

---

## 🔝 Top-ups — Buy Extra Credits Anytime

If a subscriber runs out of credits mid-month, they can buy a top-up — a one-off credit purchase with no commitment.

| Package | You Pay | Credits You Get | Bonus |
|---------|---------|-----------------|-------|
| Small | £5 | £5 credits | None |
| Medium | £10 | £10 credits | +£1 bonus |
| Large | £25 | £25 credits | +£5 bonus |
| XL | £50 | £50 credits | +£15 bonus |

Top-up credits **never expire** — only subscription credits expire on renewal.

---

## 🤝 Referrals — Earn by Inviting Friends

Every user gets a unique referral link they can share.

**How it works:**
1. User A shares their referral link with a friend
2. Friend signs up using that link
3. Friend completes their **first paid session**
4. User A automatically receives **£2.00 in free credits**

The £2.00 goes straight into their credit balance and can be used for any future session.

> The reward amount (£2.00) can be changed anytime from the admin panel under **Credits → Referral reward**.

---

## 🔄 How It All Connects

```
User signs up via referral link
        ↓
User subscribes to Standard plan (£19/month)
        ↓
Gets £27 credits added to wallet
        ↓
Creates a 60-min session → £6.00 deducted from credits
        ↓
Wallet now shows £21.00 remaining
        ↓
Runs low? Buys a Medium top-up (£10 + £1 bonus)
        ↓
Wallet now shows £32.00
        ↓
Month renews → balance resets to £27 (if expiry is on)
```

---

## 🛠 What You (Admin) Can Control

From the admin panel at `/f-med/admin`:

| What | Where |
|------|-------|
| Change plan prices & credits | Admin → Plans |
| Enable or disable a plan | Admin → Plans |
| Change top-up packages | Admin → Credits |
| Change referral reward amount | Admin → Credits |
| Turn credit expiry on/off | Admin → Credits |
| Manually adjust a user's balance | Admin → Users → User detail |
| View all subscriptions | Admin → Users |
| Issue refunds | Admin → Billing → Refunds |

---

## 💡 Key Rules to Know

- **Party B never uses credits** — if a session is split-payment, Party B always pays cash directly. Only Party A's credits are used.
- **Referral credits and subscription credits are the same balance** — they all sit in one wallet.
- **Top-up credits never expire** — only the monthly subscription grant expires on renewal.
- **Stripe handles all card payments** — no card data ever touches the FirstMediator servers.

---

*For the Stripe setup guide (creating prices in your Stripe dashboard), see `STRIPE_SETUP_GUIDE.md`.*
