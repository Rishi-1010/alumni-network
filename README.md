# Alumni Network System

A comprehensive alumni management system designed for Uka Tarsadia University graduates to maintain professional connections and track career progress.

## Project Structure
```
├── admin/
│ ├── dashboard.php
│ ├── delete_alumni.php # New file
│ ├── verifications.php
│ ├── alumni-list.php
│ ├── reports.php
│ └── settings.php
├── Authentication/
│ ├── AdminLogin/
│ │ ├── login.php
│ │ ├── process_login.php
│ │ ├── logout.php
│ │ └── generate_hash.php
│ ├── Login/
│ │ ├── login.php
│ │ └── process_login.php
│ └── Registration/
│ ├── register.php
│ ├── process_registration.php
│ └── success.php
├── config/
│ └── db_connection.php
├── assets/
│ ├── css/
│ │ ├── style.css
│ │ ├── register.css
│ │ └── admin.css
│ ├── js/
│ │ ├── register.js
│ │ ├── admin.js # Updated with delete functionality
│ │ └── admin-login.js
│ └── images/
│ ├── logo.png
│ └── admin-avatar.png
├── database/
│ └── alumni_network.sql
├── index.html
├── .gitignore
└── README.md
```

## Features
### Implemented Features
- Multi-step registration process
  - Basic Information
  - Educational Details
  - Professional Status
- Secure authentication system
  - Password hashing
  - Session management
- Admin Panel
  - Secure admin login
  - Dashboard analytics
  - Alumni verification system
  - User management
  - Record deletion with confirmation
  - Real-time UI updates
- Responsive design
  - Mobile-friendly interface
  - Modern UI components
  - Smooth transitions
  - Delete animations

### Under Development
- Educational verification system
  - Document upload
  - Automated verification process
  - Manual admin verification
- Professional networking
  - Alumni connections
  - Direct messaging
  - Job sharing
- Event Management
  - Alumni meetups
  - Professional workshops
  - University events
- Analytics Dashboard
  - Employment statistics
  - Alumni distribution
  - Industry trends
- Email Notifications
  - Verification updates
  - Event reminders
  - Connection requests

## Tech Stack
- PHP 7.4+
- MySQL 5.7+
- HTML5/CSS3
- JavaScript
- Bootstrap 5.3.2
- jQuery 3.7.1

## Database Structure
- Users table (authentication and profile)
  - Cascade deletion support
  - Referential integrity
- Educational details (academic information)
- Professional status (current employment)
- Status history (career timeline)
- Admin users (system management)
- Universities list (verified institutions)

## Setup Instructions
1. Clone the repository
2. Import `database/alumni_network.sql` to your MySQL server
3. Configure database connection in `config/db_connection.php`
4. Set up admin credentials using `Authentication/AdminLogin/generate_hash.php`
5. Run using XAMPP/WAMP server
6. Access the application through localhost

## Security Features
- Password hashing using bcrypt
- Session management
- SQL injection prevention
- XSS protection
- CSRF protection
- Secure admin authentication
- Transaction-based deletions
- Data integrity checks

## Contributing
1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request