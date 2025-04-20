# Alumni Network System

A comprehensive alumni management system designed for Uka Tarsadia University graduates to maintain professional connections and track career progress.

## Project Structure
```
├── admin/
│ ├── Alumni Management/
│ │ ├── delete_alumni.php
│ | ├── export_alumni.php
│ | ├── get_employed_count.php
│ | ├── search_alumni.php
│ | ├── totalalumnis.php
| │ ├── verify_alumni.php
│ | └── view-portfolio.php
│ ├── alumni-network.code-workspace
│ ├── dashboard.php
│ ├── search_enrollment.php
├── assets/
│ ├── certifications/
│ │ └── [user_enrollment_number]/ (User-specific folders for storing certificates)
│ ├── css/
│ │ ├── admin.css
│ │ ├── contactus.css # Added contactus.css if not already present
│ │ ├── dashboard.css
│ │ ├── portfolio.css # Added portfolio.css
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
│ │ ├── register.js
│ │ └── rollnumformat.js
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
  - Career Goals
  - Language Specialization, Tools & Technologies

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
  - user_id (INT)
  - language_specialization (VARCHAR(255))
  - tools (VARCHAR(255))
  - technologies (VARCHAR(255))
  - proficiency_level (ENUM('beginner','intermediate','advanced','expert'))

- **career_goals**
  - User career goals
  - Goal type and status

- **certifications**
  - User certifications

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
- Enhanced Skills Management System
  - Modified skills table structure with language specialization, tools, technologies, and proficiency levels
  - Removed redundant "Add Another Skill" functionality
  - Improved skills display in portfolio view

- Improved Educational Details System
  - Enhanced educational_details table structure
  - Added course information display in graduation section
  - Streamlined educational data management

- Enhanced Certificate Management
  - Improved certificate upload and storage system
  - Added certificate count display in portfolio
  - Enhanced certificate directory structure
  - Improved certificate deletion handling

- Robust Alumni Management
  - Implemented secure alumni deletion system with directory cleanup
  - Added transaction-based deletions for data integrity
  - Enhanced file system management for user assets
  - Improved user data cleanup processes

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
