# Alumni Network System

A comprehensive alumni management system designed for Uka Tarsadia University graduates to maintain professional connections and track career progress.

## Project Structure
```
├── admin/
│ ├── alumni-network.code-workspace
│ ├── dashboard.php
│ ├── delete_alumni.php
│ ├── search_enrollment.php
│ ├── totalalumnis.php
│ ├── verify_alumni.php
│ └── view-portfolio.php
├── assets/
│ ├── css/
│ │ ├── admin-dark.css
│ │ ├── admin.css
│ │ ├── dashboard.css
│ │ ├── register.css
│ │ └── style.css
│ ├── img/
│ │ └── logo.png
│ ├── js/
│ │ ├── admin-login.js
│ │ ├── admin.js
│ │ ├── dashboard.js
│ │ ├── enrollment-autocomplete.js
│ │ ├── login.js
│ │ └── register.js
├── Authentication/
│ ├── AdminLogin/
│ │ ├── generate_hash.php
│ │ ├── login.php
│ │ ├── logout.php
│ │ └── process_login.php
│ ├── CompanyLogin/
│ │ ├── login.php
│ │ └── process_login.php
│ ├── Login/
│ │ ├── login.php
│ │ └── process_login.php
│ └── Registration/
│   ├── otpverification.php
│   ├── process_registration.php
│   └── register.php
├── company/
│ └── dashboard.php
├── config/
├── database/
├── send_otp/
│ ├── PHPMailerFunction.php
│ └── verify_otp.php
├── .gitignore
├── composer.json
├── composer.lock
├── dashboard.php
├── index.html
├── README.md
└── send_otp/resend_otp.php
```

## Features
- **Search Alumni**: Search for alumni by enrollment number.
- **Send OTP**: Send OTP to alumni email for verification.
- **View Statistics**: View total and verified alumni statistics.

### Implemented Features
- **Multi-step Registration Process**
  - Basic Information
  - Educational Details
  - Professional Status
  - Project Details
  - Career Goals

- **Secure Authentication System**
  - Password hashing
  - Session management

- **Admin Panel**
  - Secure admin login
  - Dashboard analytics
  - Alumni verification system
  - User management
  - Record deletion with confirmation
  - Real-time UI updates
  - Portfolio view
  - Educational verifications
  - **Total Alumni Count**: Admin dashboard now displays the total number of registered alumni in the system.
  - **Verified Alumni Count**: Admin dashboard now displays the total number of verified alumni in the system.
  - **Alumni Verification System**: Implemented a system for admins to verify alumni, including confirmation dialogs and real-time updates.

- **Responsive Design**
  - Mobile-friendly interface
  - Modern UI components
  - Smooth transitions
  - Delete animations
  - Card layouts
  - Status badges

### New Features Added
- **Contact Us Page**
  - Added a new page for users to contact the administrators.
- **Portfolio View System**
  - Detailed alumni information display

### Under Development
- **User Dashboard**
  - User dashboard is now under development and not currently implemented.

- **Professional Networking**
  - Alumni connections
  - Direct messaging
  - Job sharing


- **Event Management** (Under Development - To be decided later if needed)
  - Alumni meetups
  - Professional workshops
  - University events

- **Analytics Dashboard** (Under Development - To be decided later if needed)
  - Employment statistics
  - Alumni distribution
  - Industry trends

- **Email Notifications** (Under Development - To be decided later if needed)
  - Verification updates
  - Event reminders
  - Connection requests

## Database Structure

### Tables
- **users**
  - Basic user information
  - Authentication details
  - Status tracking

- **educational_details**
  - University information
  - Enrollment details
  - Verification status
  - Verification tracking

- **professional_status**
  - Current employment
  - Company details
  - Position
  - Employment history

- **status_history**
  - Career progression tracking
  - Timeline management

- **admins**
  - Role-based access
  - Department assignment

- **universities**
  - Institution database
  - Location tracking

- **projects**
  - User projects
  - Technologies used
  - Project duration

- **skills**
  - User skills
  - Proficiency level
  - Years of experience

- **career_goals**
  - User career goals
  - Goal type and status
  - Target date

- **certifications**
  - User certifications
  - Issuing organization
  - Credential details

## Tech Stack
- PHP
- MySQL
- HTML5/CSS3
- JavaScript
- Bootstrap 5.3.2
- GSAP
- jQuery 3.7.1

## Recent Updates
- Updated project directory structure in README.md
- Added portfolio view for admin
- Implemented project display section
- Enhanced verification system
- Improved responsive design
- Added status badges
- Implemented educational details verification
- Added OTP verification system
- Enhanced delete confirmation system
- Improved search functionality
- Added transaction-based deletions
- Added GSAP to tech stack
- Added Contact Us Page

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
