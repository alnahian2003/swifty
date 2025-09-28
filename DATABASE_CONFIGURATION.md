# Database Configuration Guide

The Swifty REST API now supports multiple database drivers, making it easy to switch between MySQL, PostgreSQL, SQLite, and other PDO-compatible databases by simply changing your `.env` configuration.

## Supported Database Drivers

- **MySQL** (default)
- **PostgreSQL**
- **SQLite**
- **SQL Server** (experimental)
- **Oracle** (experimental)

## Configuration

### 1. MySQL Configuration

```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=your_password
DB_NAME=swifty
DB_CHARSET=utf8mb4
```

### 2. PostgreSQL Configuration

```env
DB_DRIVER=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_USERNAME=postgres
DB_PASSWORD=your_password
DB_NAME=swifty
DB_CHARSET=utf8
```

### 3. SQLite Configuration

```env
DB_DRIVER=sqlite
DB_NAME=/path/to/your/database.sqlite
# Note: For SQLite, HOST, PORT, USERNAME, PASSWORD are not needed
```

### 4. SQL Server Configuration

```env
DB_DRIVER=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_USERNAME=sa
DB_PASSWORD=your_password
DB_NAME=swifty
```

## Quick Setup Examples

### Using MySQL (Default)

1. Copy `.env.mysql.example` to `.env`
2. Update the database credentials
3. Create your MySQL database
4. Import the `swifty.sql` file

```bash
cp .env.mysql.example .env
# Edit .env with your MySQL credentials
mysql -u root -p < swifty.sql
```

### Using PostgreSQL

1. Copy `.env.postgresql.example` to `.env`
2. Update the database credentials
3. Create your PostgreSQL database
4. Import the schema (you may need to convert from MySQL syntax)

```bash
cp .env.postgresql.example .env
# Edit .env with your PostgreSQL credentials
createdb swifty
# Import your schema
```

### Using SQLite

1. Copy `.env.sqlite.example` to `.env`
2. Update the database file path
3. Create your SQLite database file

```bash
cp .env.sqlite.example .env
# Edit .env with your SQLite file path
sqlite3 /path/to/your/database.sqlite < swifty_sqlite.sql
```

## Database-Specific Features

### Automatic Driver Detection

The system automatically detects your database driver and applies appropriate configurations:

```php
$database = Database::fromEnv();
$connection = $database->connect();

// Check the driver
if ($database->isMysql()) {
    // MySQL-specific operations
} elseif ($database->isPostgresql()) {
    // PostgreSQL-specific operations
} elseif ($database->isSqlite()) {
    // SQLite-specific operations
}
```

### SQL Syntax Adaptation

The system automatically adapts SQL syntax for different databases:

```php
// LIMIT syntax is automatically adapted
$limitSql = $database->getLimitSyntax(10, 5);
// MySQL: "LIMIT 5, 10"
// PostgreSQL: "LIMIT 10 OFFSET 5"
```

### Last Insert ID

The system handles last insert ID differently for each driver:

```php
// PostgreSQL requires sequence name for certain tables
$lastId = $database->getLastInsertId('posts_id_seq');
```

## Environment Variables Reference

| Variable | Description | Required | Default |
|----------|-------------|----------|---------|
| `DB_DRIVER` | Database driver (mysql, pgsql, sqlite, sqlsrv) | No | mysql |
| `DB_HOST` | Database host | For network DBs | localhost |
| `DB_PORT` | Database port | No | 3306 (MySQL), 5432 (PostgreSQL) |
| `DB_USERNAME` | Database username | For network DBs | root |
| `DB_PASSWORD` | Database password | For network DBs | (empty) |
| `DB_NAME` | Database name or file path (SQLite) | Yes | swifty |
| `DB_CHARSET` | Database charset | No | utf8mb4 |

## Migration Guide

### From Hardcoded MySQL to Flexible Configuration

1. **Update your .env file** with the `DB_DRIVER` setting
2. **No code changes required** - existing code continues to work
3. **Test your application** with the new configuration

### Converting Between Database Types

When switching database types, you may need to:

1. **Convert schema**: Different databases have different data types
2. **Update SQL syntax**: Some queries may need database-specific adjustments
3. **Handle sequences**: PostgreSQL uses sequences for auto-incrementing fields
4. **Character encoding**: Different databases handle encoding differently

## Best Practices

1. **Use environment-specific .env files** for different deployment environments
2. **Test your application** with your chosen database before deployment
3. **Use database-agnostic SQL** when possible to maintain portability
4. **Implement proper error handling** for database-specific errors
5. **Use connection pooling** in production environments

## Troubleshooting

### Common Issues

1. **PDO driver not installed**: Install the appropriate PHP PDO extension
   ```bash
   # MySQL
   sudo apt-get install php-mysql
   
   # PostgreSQL
   sudo apt-get install php-pgsql
   
   # SQLite
   sudo apt-get install php-sqlite3
   ```

2. **Connection refused**: Check your database server is running and accessible

3. **Authentication failed**: Verify your credentials are correct

4. **Database not found**: Ensure the database exists and is accessible

### Error Messages

The system provides clear error messages for configuration issues:

- `"Unsupported database driver: xyz"` - Invalid DB_DRIVER value
- `"Connection failed: ..."` - Connection or authentication issues
- Database-specific PDO errors with detailed information

## Performance Considerations

- **MySQL**: Generally fastest for read-heavy applications
- **PostgreSQL**: Better for complex queries and concurrent writes
- **SQLite**: Best for development and small applications
- **Connection pooling**: Recommended for production environments

The flexible database configuration system maintains full backward compatibility while providing the flexibility to use the best database solution for your specific needs.