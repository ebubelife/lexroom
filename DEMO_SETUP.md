# LexRoom Demo Setup Guide

## ✅ What's Been Implemented

### 1. User Interfaces (Complete)
- ✅ **Dashboard** - Stats overview, active sessions, room management
- ✅ **Room Creation Flow** - Multi-step form (Category → Jurisdiction → Summary → Payment)
- ✅ **Live Chat Room** - Party-labeled messages (Blue/Purple), Lex AI messages (Gold), Evidence Vault sidebar
- ✅ **User Profile/Settings** - Profile management with BVN/NIN optional fields
- ✅ **Password Management** - Change password functionality
- ✅ **Theme Support** - Full light/dark mode support across all interfaces

### 2. Email System (Ready)
- ✅ **Mailgun Integration** - Configured and ready to use
- ✅ **Themed Email Templates** - Beautiful branded invitation emails
- ✅ **Party B Invitation** - Automatic email sent when room is created

### 3. Claude AI Integration (Ready)
- ✅ **Claude Service** - Complete API integration for Lex AI mediator
- ✅ **System Prompts** - Context-aware prompts based on dispute category and jurisdiction
- ✅ **Conversation Management** - Message formatting and history tracking
- ✅ **Evidence Analysis** - Document processing capabilities
- ✅ **Report Generation** - Mediation report creation

### 4. Guest Access (Party B Flow)
- ✅ **Token-Based Access** - Party B can join without account
- ✅ **Room Link** - Unique shareable link with invite token
- ✅ **Guest Interface** - Full chat and evidence upload access

## 🔧 Configuration Required

### Step 1: Add Your API Keys to `.env`

```env
# Mailgun Configuration
MAIL_MAILER=mailgun
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_smtp_username
MAIL_PASSWORD=your_mailgun_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@lexroom.com"
MAIL_FROM_NAME="LexRoom"

MAILGUN_DOMAIN=your_mailgun_domain
MAILGUN_SECRET=your_mailgun_api_key
MAILGUN_ENDPOINT=api.mailgun.net

# Claude API Configuration
CLAUDE_API_KEY=your_claude_api_key_here
CLAUDE_MODEL=claude-sonnet-4-20250514
```

### Step 2: Test Email Sending

```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email from LexRoom', function($message) {
    $message->to('your-email@example.com')->subject('Test');
});
```

## 🎯 Demo Navigation Guide

### For Your Client to Navigate:

#### 1. **Dashboard** (`/dashboard`)
- View stats overview
- See active sessions
- Access room management tabs

#### 2. **Create a Room** (`/rooms/create`)
- Click "Create a Room" button in top bar
- Go through 4-step process:
  - Step 1: Select dispute category
  - Step 2: Choose jurisdiction & language
  - Step 3: Write case summary
  - Step 4: Select duration & payment type, enter Party B email

#### 3. **Live Chat Room** (`/rooms/{uuid}?token=xxx`)
- View party-labeled messages
- See Lex AI mediator responses (gold-tinted)
- Upload evidence files
- Real-time countdown timer
- Evidence vault sidebar

#### 4. **User Settings** (`/settings`)
- Update profile information
- Add BVN/NIN (optional)
- Change password

## 📧 Party B Workflow

### How It Works:

1. **Party A creates room** → System generates unique link
2. **Email sent to Party B** → Beautiful themed invitation
3. **Party B clicks link** → Joins as guest (no signup required)
4. **Party B can optionally create account** → To access session history later
5. **Both parties participate** → Equal access to chat and evidence vault
6. **Session ends** → Both receive mediation report

### Guest Access Features:
- ✅ No account required to join
- ✅ Full chat access
- ✅ Evidence upload capability
- ✅ View Lex AI responses
- ✅ Optional account creation for history

## 🎨 UI Features

### Theme System
- Light/Dark mode toggle (top bar)
- Persistent theme preference
- All interfaces fully themed

### Chat Room Features
- **Party A Messages**: Blue, left-aligned
- **Party B Messages**: Purple, right-aligned
- **Lex AI Messages**: Gold-tinted, full-width system messages
- **Evidence Vault**: Sidebar with file upload
- **Timer**: Live countdown (currently mock, will be server-side)

### Responsive Design
- Mobile-first approach
- Sidebar collapses on mobile
- Touch-friendly interfaces
- Optimized for all screen sizes

## 🚧 What's NOT Implemented Yet (Integrations)

### Payment Integration
- ❌ Paystack webhook verification
- ❌ Split payment confirmation
- ❌ Credits system
- ❌ Session extension payment

### Real-Time Features
- ❌ Laravel Reverb (WebSocket) setup
- ❌ Live message broadcasting
- ❌ Real-time timer sync
- ❌ Disconnect detection

### Evidence Processing
- ❌ File storage (Cloudflare R2)
- ❌ PDF text extraction
- ❌ OCR for images
- ❌ Document analysis pipeline

### Session Management
- ❌ Server-side timer
- ❌ 5-minute warning system
- ❌ Session pause/lock mechanism
- ❌ Recording consent flow

## 📝 Mock Data in Demo

The live chat room currently shows **mock messages** to demonstrate the UI:
- Sample Party A message (blue)
- Sample Party B message (purple)
- Sample Lex AI responses (gold)
- Sample uploaded evidence files

These will be replaced with real data once WebSocket integration is complete.

## 🎬 Demo Script for Client

### Recommended Demo Flow:

1. **Start at Dashboard**
   - Show stats overview
   - Explain room management tabs

2. **Create a Room**
   - Walk through 4-step process
   - Show validation and progress indicators
   - Enter real email to test invitation

3. **Check Email**
   - Show beautiful themed invitation
   - Click link to join room

4. **Live Chat Room**
   - Show party-labeled messages
   - Demonstrate Lex AI responses
   - Show evidence vault
   - Explain timer functionality

5. **User Settings**
   - Show profile management
   - Explain BVN/NIN optional fields

6. **Theme Toggle**
   - Switch between light/dark modes
   - Show consistency across all pages

## 🔐 Security Features

- ✅ Token-based guest access
- ✅ CSRF protection
- ✅ Password hashing
- ✅ Email verification
- ✅ Phone verification
- ✅ Session management

## 📊 Next Steps for Full Implementation

1. **Laravel Reverb Setup** - Real-time WebSocket server
2. **Paystack Webhooks** - Complete payment verification
3. **File Storage** - Cloudflare R2 integration
4. **Evidence Processing** - PDF/OCR pipeline
5. **Server-Side Timer** - Redis-based countdown
6. **Claude API Testing** - Live AI responses
7. **Report Generation** - PDF mediation reports

## 💡 Tips for Demo

- Use **real email addresses** to test invitation flow
- **Toggle theme** to show dark mode support
- **Navigate freely** - all interfaces are functional
- **Mock data** in chat room shows intended design
- **Explain** that integrations are next phase

---

**LexRoom** - Resolving disputes the smart way. No lawyers needed. 🏛️⚖️
