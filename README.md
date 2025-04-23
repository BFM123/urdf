# URDF System

A department details management system built with PHP and MySQL.

## Features
- User authentication and authorization
- Department management
- Excel import functionality
- Progress tracking dashboard
- User registration system

## Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server
- Composer (for dependencies)

## Installation
1. Clone the repository
```bash
git clone https://github.com/YOUR_USERNAME/REPO_NAME.git
```

2. Install dependencies
```bash
composer install
```

3. Configure database
- Copy `config.example.php` to `config.php`
- Update database credentials

4. Import database schema
```sql
mysql -u username -p database_name < schema.sql
```

5. Configure web server
- Point document root to the project directory
- Ensure mod_rewrite is enabled (Apache)

## Usage
1. Access the application through web browser
2. Login with credentials
3. Navigate through dashboard

## Security
- Password hashing using bcrypt
- Session management
- XSS protection
- CSRF protection

## License
