# Stripe Setup Guide — FirstMediator Subscriptions

Follow these steps exactly. Once done, paste the Price IDs into the admin panel at:
`/f-med/admin/plans` and `/f-med/admin/credits`

---

## Step 1 — Add Webhook Events

Go to **Stripe Dashboard → Developers → Webhooks → your existing webhook endpoint** and add these events:

```
checkout.session.completed       ← already there
invoice.payment_succeeded        ← ADD THIS
customer.subscription.deleted    ← ADD THIS
customer.subscription.updated    ← ADD THIS
```

---

## Step 2 — Create Subscription Products & Prices

Go to **Stripe Dashboard → Products → Add Product** for each plan below.

### Plan 1: Starter

1. Click **Add Product**
2. Name: `FirstMediator Starter`
3. Description: `£12 session credits per month`
4. Click **Add a price** for each billing cycle:

| Billing | Amount | Interval | Copy the Price ID |
|---------|--------|----------|-------------------|
| Monthly | £9.00 | Monthly | `price_xxx...` |
| Quarterly | £24.00 | Every 3 months | `price_xxx...` |
| Yearly | £86.00 | Yearly | `price_xxx...` |

---

### Plan 2: Standard

1. Click **Add Product**
2. Name: `FirstMediator Standard`
3. Description: `£27 session credits per month`
4. Add prices:

| Billing | Amount | Interval | Copy the Price ID |
|---------|--------|----------|-------------------|
| Monthly | £19.00 | Monthly | `price_xxx...` |
| Quarterly | £51.00 | Every 3 months | `price_xxx...` |
| Yearly | £182.00 | Yearly | `price_xxx...` |

---

### Plan 3: Pro

1. Click **Add Product**
2. Name: `FirstMediator Pro`
3. Description: `£60 session credits per month`
4. Add prices:

| Billing | Amount | Interval | Copy the Price ID |
|---------|--------|----------|-------------------|
| Monthly | £39.00 | Monthly | `price_xxx...` |
| Quarterly | £105.00 | Every 3 months | `price_xxx...` |
| Yearly | £374.00 | Yearly | `price_xxx...` |

---

## Step 3 — Create Top-up Products & Prices

These are **one-time payments** (not subscriptions).

Go to **Stripe Dashboard → Products → Add Product** for each:

| Product Name | Amount | Type | Copy the Price ID |
|---|---|---|---|
| `FM Top-up Small` | £5.00 | One-time | `price_xxx...` |
| `FM Top-up Medium` | £10.00 | One-time | `price_xxx...` |
| `FM Top-up Large` | £25.00 | One-time | `price_xxx...` |
| `FM Top-up XL` | £50.00 | One-time | `price_xxx...` |

> For each: Add Product → set name → Add a price → One time → set amount → Save

---

## Step 4 — Paste Price IDs into Admin

### Subscription Plans
Go to `/f-med/admin/plans`, click **Edit** on each plan and paste:
- Stripe Monthly Price ID
- Stripe Quarterly Price ID
- Stripe Yearly Price ID

### Top-up Packages
Go to `/f-med/admin/credits`, click **Edit** on each package and paste the Stripe Price ID.

---

## Step 5 — Verify Currency

Make sure your Stripe account is set to **GBP (£)** as the default currency:
**Settings → Business settings → Bank accounts and scheduling → Default currency → GBP**

---

## Step 6 — Test in Test Mode First

Before going live:
1. Use Stripe test card: `4242 4242 4242 4242`, any future expiry, any CVC
2. Subscribe to a plan → check credits appear in user wallet
3. Top up → check credits added
4. Cancel → check subscription marked cancelled

Once confirmed working, switch Stripe to **Live mode** and repeat Steps 2–4 with live Price IDs.

---

## Summary — IDs to collect

```
Starter Monthly:    price_
Starter Quarterly:  price_
Starter Yearly:     price_

Standard Monthly:   price_
Standard Quarterly: price_
Standard Yearly:    price_

Pro Monthly:        price_
Pro Quarterly:      price_
Pro Yearly:         price_

Top-up Small:       price_
Top-up Medium:      price_
Top-up Large:       price_
Top-up XL:          price_
```

Total: **13 Price IDs** to collect and paste into the admin panel.

---

*Once you have all 13 IDs, share them and I'll update the seeder so they're always in the DB.*
