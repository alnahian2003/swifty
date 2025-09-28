# Laravel Database Configuration

The Swifty REST API uses Laravel's exact database configuration format for maximum compatibility and familiarity.

## Laravel-Style Configuration

### Single .env File Approach

Just like Laravel, use a single `.env` file with these exact variables:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=swifty
DB_USERNAME=root
DB_PASSWORD=
```

### Supported Database Types

#### 1. SQLite (Zero Configuration!)

```env
DB_CONNECTION=sqlite
```

That's it! The system automatically uses `db.sqlite` in your project root. No other database variables needed.

#### 2. MySQL

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=swifty
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 3. PostgreSQL

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=swifty
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

## Laravel Environment Variables

| Variable | Description | Required | Default |
|----------|-------------|----------|---------|
| `DB_CONNECTION` | Database driver (mysql, pgsql, sqlite) | Yes | mysql |
| `DB_HOST` | Database host | For network DBs | 127.0.0.1 |
| `DB_PORT` | Database port | No | 3306 (MySQL), 5432 (PostgreSQL) |
| `DB_DATABASE` | Database name or file path (SQLite) | Yes | swifty |
| `DB_USERNAME` | Database username | For network DBs | root |
| `DB_PASSWORD` | Database password | For network DBs | (empty) |

## Convention Over Configuration

### SQLite Behavior
- **Just set** `DB_CONNECTION=sqlite`
- **Automatically uses** `db.sqlite` in project root
- **No additional configuration** needed
- **Perfect for development**

### MySQL/PostgreSQL Behavior
- **Requires** standard database credentials
- **Uses sensible defaults** (127.0.0.1, standard ports)
- **Works exactly like Laravel**

## Quick Examples

### Development Setup (SQLite)
```bash
echo "DB_CONNECTION=sqlite" > .env
touch db.sqlite
# Ready to develop!
```

### Production Setup (MySQL)
```bash
cat > .env << EOF
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=swifty_production
DB_USERNAME=swifty_user
DB_PASSWORD=secure_password
EOF
```

### Enterprise Setup (PostgreSQL)
```bash
cat > .env << EOF
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=swifty_enterprise
DB_USERNAME=swifty_admin
DB_PASSWORD=admin_password
EOF
```

## Laravel Compatibility

This implementation is 100% compatible with Laravel's database configuration:

- **Same environment variables**
- **Same conventions**
- **Same behavior**
- **Same defaults**

You can copy `.env` database settings directly from a Laravel project and they will work seamlessly.

## Migration from Previous Versions

### Old Format (No longer needed)
```env
DATABASE_CONNECTION=mysql  # Old way
DB_DRIVER=mysql            # Legacy way
```

### New Format (Laravel-compatible)
```env
DB_CONNECTION=mysql        # Laravel way ✅
```

The system maintains backward compatibility but uses Laravel's standard `DB_CONNECTION` as the primary variable.

## Troubleshooting

### SQLite Issues
- Ensure `db.sqlite` file exists in project root
- Check file permissions
- For custom paths, use absolute paths

### MySQL/PostgreSQL Issues
- Verify database server is running
- Check credentials are correct
- Ensure database exists
- Test connection with database client

### Common Errors
- `"Connection failed"` - Check DB credentials
- `"Database not found"` - Create the database first
- `"Access denied"` - Verify username/password

## Best Practices

1. **Use SQLite for development** - Zero configuration
2. **Use MySQL for production** - Proven and reliable
3. **Use PostgreSQL for complex apps** - Advanced features
4. **Keep .env secure** - Never commit to version control
5. **Use different databases per environment** - Development, staging, production

This Laravel-compatible configuration provides the perfect balance of simplicity and power, making database setup as easy as changing a single environment variable.