# Alumni Network System

A comprehensive alumni management system designed for Uka Tarsadia University graduates to maintain professional connections and track career progress.

## Project Structure
```
alumni_network/
├── Authentication/
│   ├── login.php
│   └── register.php
├── config/
│   └── db_connection.php
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── register.css
│   ├── js/
│   │   └── register.js
│   └── images/
├── database/
│   └── alumni_network.sql
├── index.html
├── process_registration.php
├── process_login.php
└── README.md
```
## Features
- Multi-step registration process
- Secure authentication system
- Professional status tracking
- Educational verification system
- Responsive design

## Tech Stack
- PHP 7.4+
- MySQL 5.7+
- HTML5/CSS3
- JavaScript
- PDO Database Connection

## Setup Instructions
1. Clone the repository
2. Import `database/alumni_network.sql` to your MySQL server
3. Configure database connection in `config/db_connection.php`
4. Run using XAMPP/WAMP server

## Contributing
1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request