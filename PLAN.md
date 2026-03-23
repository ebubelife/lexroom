# FirstMediator — Project Build Plan
**Stack:** Laravel 12 · Blade · Alpine.js · Tailwind CSS · Laravel Reverb · Laravel Horizon · MySQL · Redis · Claude API · Paystack  
**Client:** Symbi Technologies Ltd  
**Version:** 1.0 — March 2026  

---

## AI Prompt Template
Use this when passing tasks to Claude / Gemini / Cursor / Windsurf:

> "I'm building FirstMediator — an AI-assisted legal mediation SaaS platform. Stack: Laravel 12, Blade, Alpine.js, Tailwind CSS, Laravel Reverb (WebSockets), Laravel Horizon (queues), MySQL, Redis, Claude API (claude-sonnet-4-20250514), Paystack. Brand colors: deep navy #0D1B2A, antique gold #C9A84C, white. Build me [specific task]."

---

## Timeline Overview

| Phase | Weeks | Focus |
|-------|-------|-------|
| 01 | Week 1 | Laravel Setup · Auth · Design System |
| 02 | Week 1–2 | Landing Page · Room Creation Flow |
| 03 | Week 3–4 | Live Room · Real-time Chat · Timer |
| 04 | Week 5–6 | Evidence Vault · Lex AI Mediator |
| 05 | Week 7 | Session Billing · Paystack |
| 06 | Week 8 | Mediation Report · LexRefer |
| 07 | Week 9–10 | LexConsole Admin · QA · Launch |

---

## Phase 01 — Laravel Setup + Auth + Design System
**Week 1**

### Project Scaffold
- Fresh Laravel 12 install — configure `.env`, MySQL, Redis
- Install Laravel Sanctum (session auth), Laravel Horizon (queue monitoring)
- Migrations: `users`, `rooms`, `session_messages`, `evidence_files`, `billing`, `reports`, `lawyers`, `commissions`, `wallet`
- Laravel Horizon setup — queues: `lex-processing`, `report-gen`, `notifications`
- Laravel Reverb install — WebSocket server for live room
  - Private channel: `room.{roomId}`

### Authentication
- Email/password registration + login via Sanctum
- Google OAuth via Laravel Socialite
- Guest token system for Party B room access — no signup required
  - Signed URL: `/room/{uuid}?token=xxx`
- Phone OTP verification via Twilio SMS before session starts

### Blade + Tailwind Design System
- Tailwind config — brand colors: navy `#0D1B2A`, gold `#C9A84C`
- CSS variables for consistent theming across all views
- Base Blade components: `x-button`, `x-input`, `x-modal`, `x-badge`, `x-card`
- Alpine.js global store — user state, room state, timer state
- Typography: bold serif for "Lex" identity, clean sans for UI

### Stack
`Laravel 12` `MySQL` `Redis` `Laravel Reverb` `Laravel Horizon` `Sanctum` `Socialite` `Twilio` `Alpine.js` `Tailwind CSS`

---

## Phase 02 — Landing Page + Room Creation Flow
**Week 1–2**

### Landing Page (Blade)
- Hero section — headline, CTA, Lex introduction, brand visuals
- How it works — 5-step illustrated flow
- Pricing section — Starter / Standard / Extended session cards
- Dispute categories overview, social proof, footer

### Room Creation — Multi-step Alpine.js Form
- **Step 1** — Dispute category selector (6 categories: Tenancy, Freelance, Business, E-commerce, Debt, Employment)
- **Step 2** — Jurisdiction + language picker (Nigerian states, UK, South Africa, Ghana · English, Pidgin, Yoruba, Igbo, Hausa)
- **Step 3** — Case summary textarea — this feeds Lex context before the session
- **Step 4** — Duration selector (30 / 60 / 90 min) + full vs split payment toggle
- `RoomController@store` — create room record, generate signed Party B invite link
- Fire queued email invite to Party B via Resend

> **Note:** Multi-step form managed entirely in Alpine `x-data`. No page reload between steps. Room link = `/room/{uuid}?token=xxx`

### Stack
`Blade templates` `Alpine.js (x-data multi-step)` `Laravel Mail` `Resend` `Laravel Signed URLs`

---

## Phase 03 — The Live Room (Real-time Chat + Timer)
**Week 3–4**

### WebSocket Room (Laravel Reverb)
- Private channel: `room.{roomId}` — both parties join on page load
- Events to broadcast:
  - `MessageSent` — new chat message from either party
  - `LexResponded` — Lex AI reply
  - `PhaseChanged` — session phase transition
  - `TimerTick` — every-second timer broadcast
  - `PartyDisconnected` — one party dropped
- Alpine.js chat component — listens to Echo channel, appends messages reactively
  - Party A = left blue bubble
  - Party B = right purple bubble
  - Lex = full-width gold-tinted system message
- Every message saved to `session_messages` table with: party, timestamp, phase, room_id

### Server-Hosted Timer
- Timer stored in Redis key: `room:{id}:remaining_seconds`
- Decremented server-side every second — never trust the client clock
- `TimerTickJob` dispatched every second via Laravel Scheduler → broadcasts `TimerTick` event
- Alpine timer component receives broadcast tick, renders countdown
- 5-minute warning broadcast — modal prompt: extend or conclude
- At 0:00 — session pauses, 60-second grace period, then room locks

### Session Phases UI
- Phase tracker: Opening Statements → Evidence Submission → Cross-Examination → Analysis & Findings → Resolution Proposal
- Consent screen before session opens (`session_recording_consent` field in DB)
- Disconnect logging — timestamp + reason stored, other party notified via broadcast

### Stack
`Laravel Reverb` `Laravel Echo (JS)` `Alpine.js` `Redis` `Laravel Scheduler` `MySQL`

---

## Phase 04 — Evidence Vault + Lex AI Mediator
**Week 5–6**

### Evidence Vault
- File upload endpoint — PDF, DOCX, PNG, JPG (20MB max, 20 files per session)
- Store to Cloudflare R2 via Laravel Filesystem (S3-compatible driver)
- Files tagged by party on upload
- Evidence Vault locked read-only after session ends (DB flag)
- PDF/DOCX text extraction via `smalot/pdfparser` or `spatie/pdf-to-text`
- Image OCR via Google Cloud Vision API

### Lex AI Mediator (Claude API)
- `LexService` class — wraps all Claude API calls
  - Accepts: session ID, dispute category, jurisdiction, conversation history
  - Returns: Lex response text + phase recommendation + contradiction flags
- Pre-session context load — Lex reads both opening summaries before room opens
- Conversation history maintained per session in Redis (array of messages)
- Real-time contradiction flagging — after each message, Lex checks against prior statements + uploaded evidence
- Cross-examination phase — Lex generates targeted questions dispatched as `ProcessLexResponse` queued job
- Final analysis — factual findings, resolution proposal, confidence score (0–100%)
- Lex always identifies itself as AI — not a lawyer, not a court

### Prompt Library
- One system prompt per dispute category × jurisdiction combination
- 6 categories × 4 jurisdictions = 24 prompt variants
- Stored in `lex_prompts` DB table — editable from LexConsole admin
- Base prompt structure:
  ```
  You are Lex, an impartial AI mediator for FirstMediator...
  Dispute category: {category}
  Jurisdiction: {jurisdiction}
  Session language: {language}
  Party A summary: {summary_a}
  Party B summary: {summary_b}
  ```

### Stack
`Claude API (claude-sonnet-4-20250514)` `smalot/pdfparser` `Spatie Media Library` `Google Cloud Vision` `Cloudflare R2` `Laravel Jobs`

---

## Phase 05 — Session Billing (Paystack)
**Week 7**

### Pricing
| Plan | Duration | Full Price | Split (each) |
|------|----------|------------|--------------|
| Starter | 30 min | ₦4,500 | ₦2,250 |
| Standard | 60 min | ₦7,500 | ₦3,750 |
| Extended | 90 min | ₦10,000 | ₦5,000 |

### Paystack Integration
- Paystack checkout — initialize transaction per session plan
- `PaystackWebhookController@handle` — verify signature, update billing table
- Room activates ONLY on confirmed Paystack webhook — never from frontend
- Split payment flow:
  - Party A pays their half
  - System emails Party B a payment link
  - Room locked in Redis until both webhook confirmations received
- Session extension — new Paystack charge, timer reset on webhook success
- Credits system — unused time stored as platform credits in `wallet` table (no cash refunds)
- 10-minute grace — signed room URL expires if payment not confirmed

### Stack
`Paystack API` `spatie/laravel-webhook-client` `MySQL (billing, wallet)` `Redis (payment state)`

> **Note:** Use `spatie/laravel-webhook-client` for Paystack webhook verification and processing. Never activate a room from the frontend — only from a verified webhook callback.

---

## Phase 06 — Mediation Report + LexRefer
**Week 8**

### Report Generation
- `GenerateReportJob` — dispatched at session end on `report-gen` queue
- Blade PDF template — formal legal document layout
  - Contents: both party positions, evidence reviewed, Lex factual findings, resolution recommendation, confidence score
  - Letterhead, session ID, date, duration, jurisdiction, both party names
- PDF rendered via `barryvdh/laravel-dompdf` or Browsershot
- Signed timestamp on every report
- Auto-email PDF to both parties via queued Mailable (Resend)
- Disclaimer on every report: *"This is not legal advice. Lex is an AI tool and its output does not constitute a legally binding ruling."*

### LexRefer — Lawyer Escalation
- `EscalationController` — packages transcript + evidence + report into case file
- Lawyer directory — filtered by jurisdiction + speciality, paginated listing
- Selected lawyer notified via email — 48–72hr SLA for Legal Opinion Report
- Legal Opinion Report delivered to both parties via email
- Commission tracking — 15–25% referral fee logged in `commissions` table
- Lawyers can subscribe to directory listing for lead generation

### Stack
`barryvdh/laravel-dompdf` `Browsershot (optional)` `Laravel Mailable` `Resend` `Spatie Media Library`

---

## Phase 07 — LexConsole Admin + QA + Launch
**Week 9–10**

### LexConsole Admin Dashboard
- Middleware: `EnsureAdminRole` — gates all `/admin/*` routes
- KPI dashboard — total sessions, revenue, resolution rate, escalation rate (Chart.js)
- Room management — list active, completed, paused, flagged sessions with filters
- User management — registered users + guest session participants
- Billing & revenue — session revenue, credits, lawyer commissions
- Dispute flags — rooms flagged for misconduct or abuse
- Lex config — edit prompt library per category × jurisdiction directly in DB via admin UI
- Lawyer directory — approve, suspend, manage SLAs

### QA Checklist
- [ ] End-to-end session flow — Party A creates, Party B joins, Lex mediates, report generates
- [ ] Split payment webhook — both confirmations, room activation
- [ ] Timer edge cases — extension, disconnect, 60s grace
- [ ] Lex AI quality — test all 6 dispute categories × 4 jurisdiction combos
- [ ] File upload — all supported types, size limits, OCR
- [ ] Guest Party B access — no signup required
- [ ] Report PDF — correct formatting, disclaimer, all fields populated
- [ ] 500+ concurrent room load test

### Security & Compliance
- NDPR compliance — consent flows, data processing agreement, privacy policy page
- AES-256 at rest for evidence vault files
- TLS 1.3 in transit (Cloudflare)
- Session recording consent obtained before chat begins
- Terms of Service + liability disclaimer pages

### Deployment
- **Server:** DigitalOcean VPS via Laravel Forge
  - Nginx + PHP-FPM + Supervisor (queue workers + Reverb process)
- **Frontend/CDN:** Cloudflare — DNS, SSL, DDoS protection
- **Storage:** Cloudflare R2
- **Monitoring:** Sentry (errors) + Laravel Horizon dashboard (queues)
- **Target SLAs:** 99.5% uptime · Chat <200ms · Lex response 8–15s · Doc processing <30s

### Stack
`Laravel Forge` `DigitalOcean` `Cloudflare` `Supervisor` `Chart.js` `Sentry` `Laravel Horizon`

---

## Full Package List

| Package | Purpose |
|---------|---------|
| `laravel/reverb` | WebSocket server |
| `laravel/horizon` | Queue monitoring |
| `laravel/sanctum` | Auth |
| `laravel/socialite` | Google OAuth |
| `spatie/laravel-webhook-client` | Paystack webhooks |
| `spatie/laravel-medialibrary` | File management |
| `barryvdh/laravel-dompdf` | PDF generation |
| `smalot/pdfparser` | PDF text extraction |
| `guzzlehttp/guzzle` | Claude API HTTP calls |
| `twilio/sdk` | SMS OTP |

---

## Database Schema Overview

```
users               — id, name, email, phone, role, google_id, otp_verified
rooms               — id, uuid, party_a_id, category, jurisdiction, language, duration, status, payment_type
session_messages    — id, room_id, sender_type (party_a|party_b|lex), content, phase, created_at
evidence_files      — id, room_id, party, filename, path, mime_type, extracted_text, locked
billing             — id, room_id, party, amount, plan, paystack_ref, status
wallet              — id, user_id, credits_balance
reports             — id, room_id, findings, resolution, confidence_score, pdf_path, generated_at
lawyers             — id, name, email, jurisdiction, speciality, verified, active
commissions         — id, lawyer_id, room_id, amount, status
lex_prompts         — id, category, jurisdiction, system_prompt, updated_at
```

---

*FirstMediator — Affordable first-line dispute resolution, before it becomes litigation.*  
*Built by [Your Name] for Symbi Technologies Ltd — March 2026*