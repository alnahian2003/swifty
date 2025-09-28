# Swifty REST API

A modern REST API built with PHP following current best practices and standards.

## Features

- **Modern PHP 8.0+** with strict types
- **PSR-4 Autoloading** with Composer
- **Multi-Database Support** (MySQL, PostgreSQL, SQLite)
- **Proper Error Handling** with custom exceptions
- **Input Validation** and sanitization
- **Database Connection Pooling** with PDO
- **Backward Compatibility** maintained
- **Environment Configuration** support
- **Structured Logging** for debugging
- **Flexible Primary Keys** (Laravel-style)

## Requirements

- PHP 8.0 or higher
- One of the supported databases:
  - MySQL 5.7+ (default)
  - PostgreSQL 9.6+
  - SQLite 3.8+
  - SQL Server (experimental)
- Composer (for autoloading)

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy and configure your environment:
   ```bash
   cp .env.example .env
   ```
4. Edit `.env` with your database configuration
5. Import the appropriate SQL file to your database

## Database Configuration

The API uses Laravel's exact database configuration format:

```env
# SQLite (Zero configuration - perfect for development)
DB_CONNECTION=sqlite

# MySQL (Production ready)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=swifty
DB_USERNAME=root
DB_PASSWORD=your_password

# PostgreSQL (Enterprise ready)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=swifty
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### Quick Setup

```bash
# SQLite (Recommended for development)
echo "DB_CONNECTION=sqlite" > .env
touch db.sqlite

# MySQL
echo "DB_CONNECTION=mysql" > .env
echo "DB_HOST=127.0.0.1" >> .env
echo "DB_DATABASE=swifty" >> .env
echo "DB_USERNAME=root" >> .env
echo "DB_PASSWORD=" >> .env

# PostgreSQL
echo "DB_CONNECTION=pgsql" > .env
echo "DB_HOST=127.0.0.1" >> .env
echo "DB_DATABASE=swifty" >> .env
echo "DB_USERNAME=postgres" >> .env
echo "DB_PASSWORD=" >> .env
```

See [DATABASE_CONFIGURATION.md](DATABASE_CONFIGURATION.md) for complete setup instructions.

## API Endpoints

### Posts
- `GET /api/post/read.php` - Get all posts
- `GET /api/post/single.php?id={id}` - Get single post
- `POST /api/post/create.php` - Create new post
- `PUT /api/post/update.php` - Update existing post  
- `DELETE /api/post/delete.php` - Delete post

### Categories
- `GET /api/category/read.php` - Get all categories
- `GET /api/category/single.php?id={id}` - Get single category
- `POST /api/category/create.php` - Create new category
- `PUT /api/category/update.php` - Update existing category
- `DELETE /api/category/delete.php` - Delete category

## Architecture

The API now follows a modern architecture with:

- **Controllers** (`src/Controllers/`) - Handle HTTP requests and responses
- **Models** (`src/Models/`) - Handle database operations and business logic
- **Exceptions** (`src/Exceptions/`) - Custom exception classes
- **Config** (`src/Config/`) - Configuration and database connection

## Backward Compatibility

The legacy models and endpoints are still functional. The API automatically detects if the modern autoloader is available and uses the new architecture, otherwise falls back to the legacy code.

## Error Handling

All endpoints now return proper HTTP status codes and structured JSON error responses:

```json
{
    "error": "Validation failed",
    "validation_errors": {
        "title": "This field is required"
    }
}
```

## Security Improvements

- Input sanitization with proper encoding
- SQL injection prevention with prepared statements
- XSS protection with output encoding
- Parameter type validation
- Error message sanitization (no sensitive data exposure)

## Development

The API includes proper error reporting and logging for development environments. Set `APP_ENV=development` in your `.env` file for detailed error information.