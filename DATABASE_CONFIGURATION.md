# Laravel-Style Database Configuration

The Swifty REST API now uses Laravel-style database configuration, making it extremely easy to switch between MySQL, PostgreSQL, SQLite, and other databases using convention over configuration.

## Quick Setup (Laravel Way)

### 1. SQLite (Zero Configuration!)

```env
DATABASE_CONNECTION=sqlite
```

That's it! The system automatically uses `db.sqlite` in your project root. No additional configuration needed.

### 2. MySQL

```env
DATABASE_CONNECTION=mysql

DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=your_password
DB_NAME=swifty
```

### 3. PostgreSQL

```env
DATABASE_CONNECTION=pgsql

DB_HOST=localhost
DB_PORT=5432
DB_USERNAME=postgres
DB_PASSWORD=your_password
DB_NAME=swifty
```

## Laravel-Style Conventions

### Convention Over Configuration

The system follows Laravel's approach of sensible defaults:

- **SQLite**: Automatically uses `db.sqlite` in project root
- **MySQL**: Uses standard MySQL defaults (localhost:3306)
- **PostgreSQL**: Uses standard PostgreSQL defaults (localhost:5432)

### Environment Variable Priority

The system supports both Laravel-style and legacy configuration:

1. `DATABASE_CONNECTION` (Laravel-style, preferred)
2. `DB_DRIVER` (legacy, still supported)

### Database-Specific Defaults

| Database | Default File/Host | Default Port | Default User |
|----------|------------------|--------------|--------------|
| SQLite | `db.sqlite` (project root) | N/A | N/A |
| MySQL | localhost | 3306 | root |
| PostgreSQL | localhost | 5432 | postgres |

## Supported Database Types

- **MySQL** - Full support with optimization
- **PostgreSQL** - Full support with proper defaults
- **SQLite** - Zero-config setup
- **SQL Server** - Experimental support
- **Oracle** - Experimental support

## Quick Start Examples

### Using SQLite (Recommended for Development)

1. Set your connection:
   ```env
   DATABASE_CONNECTION=sqlite
   ```

2. Create the database file:
   ```bash
   touch db.sqlite
   ```

3. That's it! Your app is ready to use SQLite.

### Using MySQL (Production Ready)

1. Copy the MySQL template:
   ```bash
   cp .env.mysql.example .env
   ```

2. Update your credentials:
   ```env
   DATABASE_CONNECTION=mysql
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   DB_NAME=your_database
   ```

3. Create your database and import schema.

### Using PostgreSQL (Enterprise Ready)

1. Copy the PostgreSQL template:
   ```bash
   cp .env.postgresql.example .env
   ```

2. Update your credentials:
   ```env
   DATABASE_CONNECTION=pgsql
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   DB_NAME=your_database
   ```

## Advanced Configuration

### Custom SQLite Path

```env
DATABASE_CONNECTION=sqlite
DB_DATABASE=/custom/path/to/database.sqlite
```

### Custom Ports

```env
DATABASE_CONNECTION=mysql
DB_PORT=3307  # Custom MySQL port
```

### Custom Charset

```env
DATABASE_CONNECTION=mysql
DB_CHARSET=utf8  # Instead of default utf8mb4
```

## Migration from Old Configuration

### Before (Legacy)
```env
DB_DRIVER=mysql
DB_HOST=localhost
# ... other settings
```

### After (Laravel-style)
```env
DATABASE_CONNECTION=mysql
DB_HOST=localhost
# ... other settings (unchanged)
```

**Note**: Legacy configuration is still supported for backward compatibility.

## Environment Files

We provide ready-to-use environment files:

- `.env.mysql.example` - MySQL configuration
- `.env.postgresql.example` - PostgreSQL configuration  
- `.env.sqlite.example` - SQLite configuration (minimal)

## Automatic Database Detection

The system automatically detects your database choice and:

1. **Sets appropriate defaults** for your database type
2. **Applies driver-specific optimizations** 
3. **Handles connection pooling** automatically
4. **Manages SQL syntax differences** transparently

## Best Practices

### Development
```env
# Perfect for development - zero config
DATABASE_CONNECTION=sqlite
```

### Testing
```env
# Fast and isolated
DATABASE_CONNECTION=sqlite
DB_DATABASE=test.sqlite
```

### Production
```env
# Robust and scalable
DATABASE_CONNECTION=mysql
DB_HOST=your-production-host
DB_USERNAME=production_user
DB_PASSWORD=secure_password
DB_NAME=production_db
```

## Troubleshooting

### SQLite Issues
- **File not found**: Ensure `db.sqlite` exists in project root
- **Permission denied**: Check file permissions on SQLite file
- **Path issues**: Use absolute paths if relative paths don't work

### MySQL/PostgreSQL Issues
- **Connection refused**: Verify database server is running
- **Authentication failed**: Check username/password
- **Database not found**: Ensure database exists

### Common Solutions

1. **Check environment loading**: Ensure `.env` file is loaded properly
2. **Verify file paths**: SQLite paths should be accessible
3. **Test connections**: Use database client to test credentials
4. **Check logs**: Application logs show detailed error messages

## Performance Notes

- **SQLite**: Best for development, small applications
- **MySQL**: Excellent for web applications, good performance
- **PostgreSQL**: Best for complex queries, concurrent writes

## Migration Tools

The Laravel-style configuration works seamlessly with:
- Database migration scripts
- Seeders and fixtures  
- Schema builders
- Connection pooling systems

This approach provides the simplicity of Laravel's database configuration while maintaining the flexibility and power needed for production applications.