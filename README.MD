# Karis Antikvariat - Inventory System

## Project Overview

Karis Antikvariat is a comprehensive web-based application designed to manage inventory for a bookstore. The system aims to streamline inventory management and facilitate easier search capabilities for staff and visitors.

## Features

- Multi-language support (Swedish/Finnish)
- Role-based access control
- Comprehensive product management
- Advanced search and filtering
- List generation with batch operations
- User management
- Newsletter subscription
- Event logging for audit trail

## Technologies

- PHP 7.4+
- MySQL 5.7+
- HTML5/CSS3 with Bootstrap
- jQuery and JavaScript

## Requirements

- Web server (Apache/Nginx)
- MySQL database
- PHP 7.4 or higher
- Modern web browser

## Installation

1. Clone the repository to your web server directory
2. Import database schema (`/db/schema.sql`)
3. Import initial seed data (`/db/seed.sql`)
4. Configure database connection in `/config/config.php`
5. Ensure proper file permissions (especially for `/uploads/` directory)
6. Access the system via web browser

## User Roles

- **Admin**: Full access to all system features, user management, and settings
- **Editor**: Create, edit, sell items, and generate reports/lists
- **Guest**: Search-only access on the public-facing website

## Development

### Development Environment

We recommend using XAMPP or Laragon for local development.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Developed for Karis Antikvariat to streamline inventory management
- Uses Bootstrap for responsive design
- Implements a custom database abstraction layer

## Contact

For support or inquiries, please contact the project maintainers.
