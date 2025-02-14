# Hospital Management Information System(HMIS)

## Overview

This Hospital Management Information System(HMIS) is a comprehensive web-based solution designed to streamline hospital operations, improve patient care, and enhance administrative efficiency. Built using PHP, MySQL, HTML, and modern web technologies, the system provides a robust platform for managing various aspects of healthcare delivery.

## Features

### 1. Patient Management

- Comprehensive patient profile creation
- Unique patient ID generation
- Detailed medical history tracking
- Easy patient record search and retrieval

### 2. Role-Based Access Control(RBAC)

- Granular permission management
- Secure user authentication
- Role-specific access levels
- Supports multiple user roles:
  - Admin
  - Doctor
  - Nurse
  - Receptionist
  - Pharmacist
  - Billing Staff
  - Laboratory Technician

### 3. Electronic Medical Records(EMR)

- Digital storage of medical records
- Secure and confidential record management
- Integration with diagnostic tools
- Comprehensive patient history tracking

### 4. Appointment Management

- Online appointment booking
- Doctor and staff scheduling
- Automated appointment reminders
- Queue management system

### 5. Billing and Financial Management

- Automated billing processing
- Multiple payment options
- Insurance claims management
- Detailed financial reporting

### 6. Inventory and Pharmacy Management

- Real-time medical supplies tracking
- Automated reordering
- Expiry date management
- Comprehensive inventory reports

## Technology Stack

- Backend: PHP 7.4+
- Database: MySQL 8.0+
- Frontend: HTML5, CSS3, JavaScript
- Authentication: Password hashing with BCrypt
- Security: Prepared statements, role-based access control

## Prerequisites

- PHP 7.4 or higher
- MySQL 8.0 or higher
- Web Server (Apache/Nginx)
- Composer (Dependency Management)

## Installation

### 1. Clone the Repository

```bash
git clone http://github.com/yourusername/hospital-management-system.git
cd hospital-management-system
```
### 2. Database Setup

1. Create a new MySQL database
2. Import the database schema from `database/schema.sql`
3. Configure database connection in `config/database.php`

### 3. Install Dependencies

```bash
composer install
```

### 4. Initialize RBAC

```php
// Run in your admin setup script
$rbacSetup = new RBACSetup($databaseConnection);
$rbacSetup->initializeRBAC();
```

### Configuration

### Database Configuration

Edit `config/database.php`:

```php
private $host = 'localhost';
private $username = 'your_username';
private $password = 'your_password';
private $database = 'hospital_management_system';
```

## Security Configurations

- Enable HTTPS
- Implement strong password policies
- Regular security audits

## Security Features

- Secure password hashing
- Role-based access control
- Input validation and sanitization
- Prepared SQL statements
- Comprehensive logging
- Session management
- Protection against common web vulnerabilities

## User Roles and Permissions

### Admin

- Full system access
- User and role management
- System configuration

### Doctor

- View and update patient records
- Create medical records
- Manage appointments

### Nurse

- View patient records
- Basic medical record access

## Receptionist

- Patient registration
- Appointment scheduling

### Compliance

- HIPAA Compliance
- GDPR Data Protection
- Medical data confidentiality standards

### Recommended Hosting Environment

- Linux-based server
- Apache/Nginx
- PHP-FPM
- MySQL/MariaDB
- SSL Certificate

### Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

MIT