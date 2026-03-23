# FirstMediator

**AI-assisted legal dispute mediation SaaS platform**

FirstMediator is a modern web application that helps resolve legal disputes through AI-assisted mediation, eliminating the need for expensive lawyers and lengthy court processes.

## 🚀 Features

### Authentication & Security
- ✅ Complete user registration and login system
- ✅ Google OAuth integration
- ✅ Email and phone OTP verification
- ✅ Password reset functionality
- ✅ Nigerian phone number validation (all formats)
- ✅ Secure session management

### User Experience
- ✅ Responsive dashboard with sidebar navigation
- ✅ Light/dark theme switching
- ✅ Mobile-first responsive design
- ✅ Smooth animations and transitions
- ✅ Professional brand styling

### Core Platform
- ✅ Room management system for dispute sessions
- ✅ User wallet and credits system
- ✅ Multi-party dispute handling
- ✅ Status tracking and reporting
- ✅ Evidence management (planned)

## 🛠 Tech Stack

- **Backend:** Laravel 12
- **Frontend:** Blade Templates + Alpine.js
- **Styling:** Tailwind CSS
- **Database:** MySQL
- **Cache:** Redis
- **Authentication:** Laravel Socialite
- **Build Tool:** Vite
- **Fonts:** Instrument Serif + DM Sans

## 📱 Phone Number Support

FirstMediator accepts all Nigerian phone number formats:
- `08012345678` (Standard format)
- `+2348012345678` (International with +)
- `2348012345678` (International without +)
- `8012345678` (Missing leading 0)

Supports all Nigerian networks: MTN, Airtel, Glo, 9mobile, NTEL, Visafone, and more.

## 🎨 Design System

### Colors
- **Navy:** `#0D1B2A` (Primary)
- **Gold:** `#C9A84C` (Accent)
- **Gold Light:** `#E8C96A`
- **Gold Pale:** `#F5EDD6`

### Typography
- **Display Font:** Instrument Serif (headings, brand)
- **Body Font:** DM Sans (text, UI elements)

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- MySQL
- Redis (optional)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/ebubelife/firstmediator.git
   cd firstmediator
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your `.env` file**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=firstmediator
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   # Google OAuth (optional)
   GOOGLE_CLIENT_ID=your_google_client_id
   GOOGLE_CLIENT_SECRET=your_google_client_secret
   GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

8. **Start the server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to see the application.

## 🔐 Authentication Flow

### Registration
1. User registers with name, email, phone, and password
2. Redirected to verification page
3. Must verify both email and phone before dashboard access
4. Phone OTP: `111111` (testing mode)
5. Email OTP: Generated and logged

### Google OAuth
1. Users can sign up/login with Google
2. Email is pre-verified for Google users
3. Still requires phone verification
4. Seamless account linking for existing users

## 📊 Dashboard Features

- **Stats Overview:** Total sessions, active sessions, resolved disputes, credits balance
- **Active Sessions:** Quick access to sessions needing attention
- **Room Management:** Tabbed interface for "My Rooms" and "Invited" sessions
- **Navigation:** Sidebar with Dashboard, Rooms, Reports, Wallet, LexRefer, Settings
- **Theme Toggle:** Light/dark mode with persistent preference

## 🏗 Project Structure

```
app/
├── Helpers/
│   └── PhoneHelper.php          # Nigerian phone validation
├── Http/Controllers/
│   ├── AuthController.php       # Authentication logic
│   ├── DashboardController.php  # Dashboard data
│   └── OtpController.php        # OTP verification
├── Models/
│   ├── User.php                 # User model with verification
│   ├── Room.php                 # Dispute room model
│   ├── Wallet.php               # User wallet system
│   └── Otp.php                  # OTP verification codes
└── Rules/
    └── NigerianPhone.php        # Phone validation rule

resources/views/
├── auth/                        # Authentication pages
├── dashboard/                   # Dashboard views
└── layouts/                     # Layout templates
```

## 🧪 Testing

### Phone Validation Testing
The system includes comprehensive testing for Nigerian phone numbers:

```bash
php artisan tinker
```

```php
use App\Helpers\PhoneHelper;

// Test various formats
$numbers = ['08012345678', '+2348012345678', '2348012345678', '8012345678'];
foreach ($numbers as $number) {
    echo $number . ' -> ' . PhoneHelper::validateAndNormalize($number) . PHP_EOL;
}
```

## 🔒 Security Features

- CSRF protection on all forms
- Rate limiting on login attempts
- Secure password hashing
- Email verification
- Phone number verification
- Session management
- Input validation and sanitization

## 🌟 Upcoming Features

- [ ] AI-powered dispute resolution
- [ ] Real-time chat system
- [ ] Evidence file uploads
- [ ] PDF report generation
- [ ] Payment integration
- [ ] SMS notifications via Termii
- [ ] Email notifications
- [ ] Advanced reporting
- [ ] Multi-language support

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Ebube Emeka**
- GitHub: [@ebubelife](https://github.com/ebubelife)
- Email: ebubeemeka19@gmail.com

## 🙏 Acknowledgments

- Laravel team for the amazing framework
- Tailwind CSS for the utility-first CSS framework
- Alpine.js for lightweight JavaScript functionality
- Google Fonts for Instrument Serif and DM Sans

---

**FirstMediator** - Resolving disputes the smart way. No lawyers needed. 🏛️⚖️