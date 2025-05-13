# Karis Antikvariat - Inventory Management System

## Project Overview

Karis Antikvariat is a comprehensive web-based application designed to manage inventory for a bookstore specializing in Nordic literature, music, and collectibles. The system streamlines inventory management and provides easy search capabilities for both staff and customers.

## Core Features

- **Multilingual Support**: Complete Swedish/Finnish language interface
- **Role-Based Access Control**: Admin, Editor, and Guest user levels
- **Comprehensive Product Management**: Support for multiple authors, genres, and images per product
- **Advanced Search and Filtering**: Intuitive search across all product attributes
- **Batch Operations**: Powerful tools for managing multiple products simultaneously
- **Special Product Sections**: Sale items, rare items, and staff recommendations
- **Database Backup**: One-click database backup and restoration
- **User Management**: Complete user administration with event logging
- **Newsletter Subscription**: Customer engagement through newsletter

## Technical Architecture

- **Frontend**: HTML5, CSS3, Bootstrap 5, jQuery, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Additional Features**: WebP image optimization, CSV export/import

## System Requirements

- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser with JavaScript enabled

## Installation

1. Clone the repository to your web server directory:
   ```
   git clone https://github.com/your-username/karis-antikvariat.git
   ```

2. Import database schema and seed data:
   ```
   mysql -u username -p database_name < db/schema.sql
   mysql -u username -p database_name < db/seed.sql
   ```

3. Configure database connection in `/config/config.php`

4. Ensure proper file permissions:
   ```
   chmod 755 -R assets/uploads/
   chmod 755 -R backups/
   chmod 755 -R logs/
   ```

5. Access the system via web browser

## User Roles

- **Admin** (Role ID 1): Full access to all system features, user management, and database management
- **Editor** (Role ID 2): Create, edit, sell items, and generate reports/lists
- **Guest** (Role ID 3): Search-only access on the public-facing website

## Directory Structure

```
/
├── admin/                  # Admin interface files
├── assets/                 # Static assets
│   ├── css/                # Stylesheets
│   ├── images/             # Static images
│   ├── js/                 # JavaScript files
│   └── uploads/            # User uploaded files
├── backups/                # Database backups
├── config/                 # Configuration files
├── db/                     # Database structure files
├── includes/               # Core functionality
├── languages/              # Localization files
├── logs/                   # Error and activity logs
├── templates/              # Page templates
```

## Key Files and Components

- `Database.php`: Centralized database operations
- `ErrorHandler.php`: Standardized error handling
- `ImageProcessor.php`: Image optimization (WebP conversion)
- `sale.php`: Special price items display
- `admin/backup.php`: Database backup functionality
- `admin/lists.php`: Advanced product management

## Default Login

Default admin credentials for development environment:
- Username: `admin`
- Password: `admin`

**IMPORTANT**: Change these credentials immediately in production!

## Developer Guidelines

- Follow PHPDoc and JSDoc standards for comments
- Use safeEcho() for all content output to prevent XSS
- Put all database operations in transaction blocks
- Add descriptive log entries for all significant events
- Follow the established error handling pattern

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Developed for Karis Antikvariat to streamline inventory management
- Uses Bootstrap for responsive design
- Implements a custom database abstraction layer

