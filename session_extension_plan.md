# Graceful Session Conclusion & Extension Plan

This plan outlines the UX and logic for handling the end of a First Mediator live session. It incorporates a robust, hybrid extension payment system (Option 3) that handles existing credit balances, top-ups, split payments, and system safety limits.

## Goal
To provide a clear end-of-session experience, prevent abrupt cutoffs, and offer a flexible, frictionless way to extend time using the platform's credit system while protecting backend AI limits.

---

## 1. Countdown & Reminder Notifications

- **5-Minute Warning:**
  - **UI Update:** The timer color changes to a warning color (e.g., orange). A non-intrusive banner appears: "5 minutes remaining."
  - **System Message:** Inject chat message from `FM MEDIATOR`: *"⚠️ You have 5 minutes remaining. Please begin wrapping up your closing statements."*
- **1-Minute Warning:**
  - **UI Update:** The timer color turns red and pulses.
  - **System Message:** Inject chat message from `FM MEDIATOR`: *"⚠️ 1 minute remaining. The chat will be locked shortly unless extended."*

---

## 2. Robust Session Extension Flow (The Hybrid Approach)

### 2.1 Triggering an Extension
- An **"Extend Session"** button appears when the timer drops below 5 minutes (or is available in a sticky header throughout the session).
- Clicking it opens the **Extension Payment Modal**.

### 2.2 The Extension Payment Modal
The modal presents the user with two choices, dynamically checking their credit balance:
1. **Pay in Full (Cost: X Credits):** The user bears the full cost of the extension. Chat extends immediately upon confirmation.
2. **Request Split Payment (Cost: X/2 Credits):** The user offers to pay half, sending a real-time request to the other party to pay the remaining half.

### 2.3 Handling Credit Balances
When a user selects an option, the system evaluates their available credits (from subscription or prior top-ups):

- **Sufficient Credits:**
  - Show UI: `"Your balance: Y Credits. Cost: Z Credits."` + **[Confirm & Extend]** button.
- **Insufficient Credits:**
  - Show UI: `"Your balance: Y Credits. You need Z more credits."`
  - Provide a **"Top Up Now"** button → opens a Stripe checkout overlay.
  - Upon successful top-up, user is returned directly to the Extension Modal to finalize.

### 2.4 The Split Payment Flow (Party B's Experience)
If Party A chooses "Request Split Payment":
- **Party A UI:** `"Waiting for the other party to accept the split..."`
- **Party B UI:** A modal appears:
  > *"Party A requested a 15-minute extension and offered to split the cost. It will cost you X/2 credits. [Accept & Pay] [Decline]"*
- **If Party B Declines:** Party A is notified with the option to "Pay in Full" instead.
- **If Party B has Insufficient Credits:** Party B sees the same "Top Up Now" Stripe flow before they can accept.

---

## 3. Graceful Session Completion (Timer hits 00:00)

### 3.1 Standard Completion
- **Chat Lock:** Disable the chat input field and send button immediately.
- **UI Update:** Change the timer to the `COMPLETED` pill.

### 3.2 The "Pending Extension" Grace Period
If the timer hits `00:00` while a split request is pending or a user is in the Stripe checkout:
- The chat input locks, but the AI verdict is **NOT** triggered yet.
- **UI Update:** Show a banner: *"Session paused. Awaiting extension payment completion (Expires in 2:00)."*
- A 2-minute countdown is shown.
- If payment/split completes within the grace period → time is added and chat unlocks.
- If the 2-minute grace period expires → session officially concludes and verdict is triggered.

---

## 4. Verdict Generation (First Mediator AI — Claude)

- **Trigger:** Triggered only when the timer hits `00:00` AND there are no pending extension requests or active grace periods.
- **UI Transition:** Display a full-width system message in the chat:
  > *"Session Concluded. The Mediator AI is generating the verdict..."*
- **Display:** Frontend listens for the verdict payload (via WebSocket or polling). Once received, it is displayed as a styled **"Verdict Card"** at the bottom of the chat stream.

---

## 5. Edge Cases & Safety Limits

### 5.1 Claude AI Context Window Limit (Max Extensions)
To prevent the chat transcript from exceeding Claude's context window (which would break verdict generation):
- **Hard Limit:** Maximum of **3 extensions** per session (or a hard time cap of e.g., 90 minutes total).
- **UI Feedback:** Once the limit is reached, the "Extend Session" button is hidden and a tooltip explains:
  > *"Maximum session time reached to ensure accurate AI verdict generation."*

### 5.2 The "Double-Click" Conflict
- If Party A initiates an extension (opens modal or requests a split), a server-side flag is set.
- If Party B tries to click "Extend" at the same time, they see a toast:
  > *"Party A is currently arranging an extension. Please wait."*
- This prevents double-billing.

### 5.3 Offline / Ghosted User Handling
- **Detection:** If the WebSocket detects Party B has disconnected or gone offline.
- **Fallback:** The "Split Payment" option is disabled for Party A. They can only choose "Pay in Full."
- **UI Feedback:** A subtle label under the split option:
  > *"Split unavailable — the other party is currently offline."*

---

## TODO (Execution Checklist)

- [ ] Implement countdown warning system messages (5-min and 1-min) injected into chat stream
- [ ] Implement timer color transitions (yellow → orange → red with pulse animation)
- [ ] Build "Extend Session" modal with Pay in Full / Request Split options
- [ ] Integrate credit balance check into Extension Modal
- [ ] Build "Insufficient Credits" flow with Stripe top-up redirect and return
- [ ] Build Split Payment request + real-time notification to Party B
- [ ] Handle Party B accept/decline flow
- [ ] Implement chat lock on timer completion
- [ ] Implement 2-minute Grace Period state with countdown
- [ ] Implement verdict generation trigger + Verdict Card UI
- [ ] Enforce max 3 extensions hard limit + UI feedback
- [ ] Implement server-side flag to prevent double-extension conflict
- [ ] Detect offline Party B and disable split payment option
