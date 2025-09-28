# Flexible Primary Keys in Swifty Models

The Swifty BaseModel now supports flexible primary key configuration, similar to Laravel's Eloquent models. By default, models use 'id' as the primary key, but this can be easily customized.

## Default Usage (ID as Primary Key)

```php
class Post extends BaseModel 
{
    public ?int $id = null;
    // ... other properties
    
    protected function getTableName(): string 
    {
        return "posts";
    }
}

// Usage
$post = new Post($db);
$post->get(123); // Gets post with id = 123
```

## Custom Primary Key Examples

### 1. Using Slug as Primary Key

```php
class Product extends BaseModel 
{
    protected string $primaryKey = 'slug'; // Custom primary key
    
    public ?int $id = null;
    public ?string $slug = null;
    public ?string $name = null;
    // ... other properties
    
    protected function getTableName(): string 
    {
        return "products";
    }
}

// Usage
$product = new Product($db);
$product->get('my-product-slug'); // Gets product with slug = 'my-product-slug'
$product->slug = 'new-slug';
$product->delete(); // Deletes by slug
```

### 2. Using Email as Primary Key

```php
class User extends BaseModel 
{
    protected string $primaryKey = 'email'; // Custom primary key
    
    public ?int $id = null;
    public ?string $email = null;
    public ?string $name = null;
    // ... other properties
    
    protected function getTableName(): string 
    {
        return "users";
    }
}

// Usage
$user = new User($db);
$user->get('user@example.com'); // Gets user with email = 'user@example.com'
```

### 3. Using UUID as Primary Key

```php
class Document extends BaseModel 
{
    protected string $primaryKey = 'uuid'; // Custom primary key
    
    public ?string $uuid = null;
    public ?string $title = null;
    public ?string $content = null;
    // ... other properties
    
    protected function getTableName(): string 
    {
        return "documents";
    }
}

// Usage
$document = new Document($db);
$document->get('550e8400-e29b-41d4-a716-446655440000'); // Gets document by UUID
```

## How It Works

1. **Default Primary Key**: If no `$primaryKey` is specified, 'id' is used by default
2. **Custom Primary Key**: Override the `$primaryKey` property in your model class  
3. **Automatic Queries**: All BaseModel methods (get, delete, read ordering) automatically use the specified primary key
4. **Type Flexibility**: Primary keys can be integers, strings, UUIDs, or any other type
5. **Sanitization**: Values are automatically sanitized based on their type

## BaseModel Methods That Use Primary Key

- `get($keyValue)`: Retrieves a record by primary key value
- `delete()`: Deletes a record using the primary key value from the object
- `read()`: Orders results by the primary key column (DESC)
- `getPrimaryKeyValue()`: Gets the current primary key value from the object
- `setPrimaryKeyValue($value)`: Sets the primary key value on the object

## Database Compatibility

This feature works with any database column that can serve as a unique identifier:
- Integer IDs (default)
- String slugs 
- Email addresses
- UUIDs
- Composite keys (with additional customization)

The implementation automatically handles proper sanitization and SQL parameter binding for different data types.