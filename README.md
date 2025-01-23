# Alumni Network System

A comprehensive alumni management system designed for Uka Tarsadia University graduates to maintain professional connections and track career progress.

## Changes Made

### Date: [23-01-2025]

1. **Fixed Horizontal Scrolling Issue**:
   - Ensured that all elements within the page are responsive and do not exceed the viewport width.
   - Updated CSS to prevent horizontal scrolling on mobile devices.

2. **Improved Mobile Responsiveness**:
   - Added media queries to ensure the layout adapts to smaller screens.
   - Ensured the suggestion box is positioned correctly relative to the input field.

3. **Updated CSS**:
   - Added styles to ensure the search form and its elements are properly contained.
   - Ensured the suggestion box is properly positioned and does not overlap with other elements.

4. **Updated HTML Structure**:
   - Ensured the HTML structure is correct and the CSS classes are applied properly.
   - Added necessary attributes and classes to ensure responsiveness.


## Project Structure
```
├── admin/
│ ├── dashboard.php
│ ├── view-portfolio.php
│ ├── delete_alumni.php
│ └── search_enrollment.php
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
│   ├── otpverification.php
│   ├── register.php
│   ├── process_registration.php
│   └── success.php
├── config/
│ └── db_connection.php
├── assets/
│ ├── css/
│ │ ├── style.css
│ │ ├── register.css
│ │ ├── dashboard.css
│ │ └── admin.css
│ ├── js/
│ │ ├── register.js
│ │ ├── admin.js
│ │ ├── dashboard.js
│ │ ├── enrollment-autocomplete.js
│ │ └── admin-login.js
│ └── images/
│ ├── logo.png
│ └── admin-avatar.png
├── database/
│ └── alumni_network.sql
├── send_otp/
│ ├── verify_otp.php
│ ├── PHPMailerFunction.php
│ └── resend_otp.php
├── index.html
├── dashboard.php
├── .gitignore
└── README.md
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
  - Skills and Certifications
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
  - Educational verification

- **Responsive Design**
  - Mobile-friendly interface
  - Modern UI components
  - Smooth transitions
  - Delete animations
  - Card layouts
  - Status badges

### New Features Added
- **Portfolio View System**
  - Detailed alumni information display
  - Educational verification status
  - Professional history
  - Project showcase
  - Skills and certifications
  - Career goals

- **Enhanced Admin Controls**
  - OTP generation and sending
  - Real-time search functionality
  - Status badge system
  - Transaction-based deletions

- **Improved UI/UX**
  - Progress bars for registration
  - Dynamic form validation
  - Interactive project addition
  - Responsive sidebar
  - Custom status indicators

### Under Development
- **Professional Networking**
  - Alumni connections
  - Direct messaging
  - Job sharing

- **Event Management**
  - Alumni meetups
  - Professional workshops
  - University events

- **Analytics Dashboard**
  - Employment statistics
  - Alumni distribution
  - Industry trends

- **Email Notifications**
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
- jQuery 3.7.1

## Recent Updates
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