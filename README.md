# Alumni Network System

A comprehensive alumni management system designed for Uka Tarsadia University graduates to maintain professional connections and track career progress.

## Project Structure
```
alumni_network/
├── Authentication/
│ └── Registration/
│ ├── register.php
│ ├── process_registration.php
│ └── success.php
├── config/
│ └── db_connection.php
├── assets/
│ ├── css/
│ │ ├── style.css
│ │ └── register.css
│ ├── js/
│ │ └── register.js
│ └── images/
│ └── university-bg.jpg
├── database/
│ └── alumni_network.sql
├── index.html
├── .gitignore
└── README.md
```
## Features
- Multi-step registration process
  - Basic Information
  - Educational Details
  - Professional Status
- Secure authentication system
  - Password hashing
  - Session management
- Educational verification system
  - University selection
  - Enrollment validation
  - Graduation year tracking
- Professional status tracking
  - Current employment status
  - Company details
  - Position tracking
- Responsive design
  - Mobile-friendly interface
  - Modern UI components
  - Smooth transitions

## Tech Stack
- PHP 7.4+
- MySQL 5.7+
- HTML5/CSS3
- JavaScript
- PDO Database Connection
- Bootstrap (for styling)

## Database Structure
- Users table (authentication and profile)
- Educational details (academic information)
- Professional status (current employment)
- Status history (career timeline)
- Universities list (verified institutions)
- Admin users (system management)

## Setup Instructions
1. Clone the repository
2. Import `database/alumni_network.sql` to your MySQL server
3. Configure database connection in `config/db_connection.php`
4. Run using XAMPP/WAMP server
5. Access the application through localhost

## Contributing
1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request