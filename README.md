# News Management System

A simple news management system built with PHP, MySQL, and vanilla JavaScript.

## Features

- User authentication (admin/test)
- Create, read, update, and delete news articles
- Live form updates with JavaScript
- CSRF protection
- Responsive design
- Text overflow handling with expand/collapse functionality

## Technologies

- PHP 8.3 (Object-Oriented)
- MySQL Database
- Twig Template Engine
- Vanilla JavaScript
- HTML5 & CSS3
- Docker for development

## Quick Start

### Using Docker

1. Clone the repository
2. Run `docker-compose up -d` 
   - The database will be automatically initialized with the schema and sample data from `database/init.sql`
3. Access the application at `http://127.0.0.1:8190`

### Manual Installation

1. Clone the repository
2. Configure your web server to point to the `public` directory
3. Create a database and import `database/init.sql`
4. Copy `.env.example` to `.env` and update database credentials
5. Run `composer install`

## Usage

1. Login with credentials:
   - Username: `admin`
   - Password: `test`

2. Manage news articles:
   - View all news articles on the dashboard
   - Create new articles using the form at the bottom
   - Edit articles by clicking the pencil icon
   - Delete articles by clicking the X icon
   - Expand/collapse long text by clicking the arrow icon

## Security Features

- Session-based authentication
- CSRF token validation for all form submissions and AJAX requests
- Prepared statements for database queries
- Input validation on both client and server side
- XSS prevention through Twig's auto-escaping
- Secure error handling and logging

## Development

- Use `docker-compose up -d` to start the development environment
- Changes to PHP files are immediately reflected
- For frontend changes, simply refresh the browser

## API Endpoints

- `GET /admin` - Admin dashboard
- `GET /admin/article/{id}` - Get article by ID
- `POST /admin/article/create` - Create new article
- `PATCH /admin/article/{id}` - Update existing article
- `DELETE /admin/article/{id}` - Delete article by ID
- `GET /login` - Login page
- `POST /login` - Login authentication
- `GET /logout` - Logout current user

## Credits

This project was created as a demonstration of building a simple but secure news management system using pure PHP and JavaScript without relying on frameworks or external libraries.
