# Setup Guide

## Prerequisites

- PHP 8.0 or higher
- SQLite (bundled with PHP)
- A modern web browser
- Optional: MySQL for production

## Installation

### Step 1: Clone/Download Project

```bash
git clone https://github.com/yukiseno/jquerynativePHPapi.git
cd jquerynativePHPapi
```

### Step 2: Backend Configuration

```bash
cd backend

# Copy example config to create .env (git-ignored)
cp .env.example .env

# Edit .env if needed for MySQL (default SQLite works out of the box)
nano .env  # or use your preferred editor
```

### Step 3: Initialize Database

From the project root directory:

```bash
# Create schema only (safe, no data loss)
php setup.php

# Or create schema + seed test data
php setup.php --seed

# Or reset (drop all tables) + recreate schema
php setup.php --reset

# Or reset + seed with test data
php setup.php --reset --seed
```

This handles both **SQLite** (default) and **MySQL** automatically.

### Step 4: Start Both Servers

**Recommended (One Command):**

```bash
php start.php
```

This automatically starts both servers on your OS (Mac, Linux, Windows):

- **Backend API:** http://localhost:3001
- **Frontend:** http://localhost:3000

Press `Ctrl+C` to stop.

**Alternative: Manual Startup**

```bash
# Terminal 1: Backend
cd backend
php -S localhost:3001 -t public

# Terminal 2: Frontend
cd frontend
php -S localhost:3000
```

### Step 5: Access the Application

Open your browser to: `http://localhost:3000`

## Testing

### 1. Test Backend API

**Test Coupon Endpoint:**

```bash
curl -X POST http://localhost:3001/api/apply/coupon \
  -H "Content-Type: application/json" \
  -d '{"coupon_code":"WELCOME10"}'
```

Expected response:

```json
{
  "success": true,
  "message": "Coupon applied successfully",
  "data": {
    "id": 1,
    "name": "WELCOME10",
    "discount_amount": 10,
    "valid_until": "2026-02-07"
  }
}
```

### 2. Test Frontend

Navigate to `http://localhost:3000` and:

- [ ] Homepage loads
- [ ] Products display with colors and sizes
- [ ] Can search/filter products
- [ ] Can view product details
- [ ] Can add to cart
- [ ] Can checkout with shipping address
- [ ] Can apply test coupon (summer20)
- [ ] Can view order history in profile

## Troubleshooting

### Port Already in Use

**Error:** `Failed to listen on localhost:3001`

**Solution:** Kill existing process or use different port

```bash
# Find process using port 3001
lsof -i :3001

# Kill process (replace PID with actual number)
kill -9 PID

# Or use different port
php -S localhost:8001
```

### Database Not Found

**Error:** `Unable to open database file`

**Solution:** Verify SQLite path in `.env`

```env
SQLITE_PATH=/full/path/to/database.sqlite
```

Or check file permissions:

```bash
chmod 644 database/database.sqlite
```

### CORS Errors

**Error:** `Access to XMLHttpRequest blocked by CORS`

**Solution:** API already has CORS headers. If error persists:

1. Verify backend is running
2. Check browser console for actual error
3. Use `http://` not `https://` for local testing
4. Try in incognito/private mode

### PHP Version Issues

**Error:** `syntax error, unexpected token`

**Solution:** Upgrade PHP to 8.0+

```bash
# Check version
php -v

# Update PHP (macOS with Homebrew)
brew upgrade php
```

### Database Errors

**Error:** `SQLSTATE[HY000]` or `Unable to open database file`

**Solution:** Rebuild database

```bash
# Reset database (drop all tables + recreate + seed)
php setup.php --reset --seed

# Or just recreate schema
php setup.php
```

## Project Structure Details

```
jquerynativePHPapi/
├── setup.php                      # Database setup (root level)
│                                  #   php setup.php              (schema only)
│                                  #   php setup.php --seed       (schema + test data)
│                                  #   php setup.php --reset      (drop all + recreate)
│                                  #   php setup.php --reset --seed (drop + recreate + seed)
├── start.php                      # Start both servers (Mac/Linux/Windows)
├── backend/                       # REST API
│   ├── public/
│   │   └── api/
│   │       └── index.php          # Main API router
│   ├── classes/
│   │   ├── DatabaseAdapter.php    # Database interface (adapter pattern)
│   │   ├── MySQLDatabase.php      # MySQL adapter
│   │   ├── SQLiteDatabase.php     # SQLite adapter
│   │   ├── Database.php           # Singleton factory for DB adapters
│   │   ├── User.php               # User model
│   │   ├── Product.php            # Product model
│   │   ├── Order.php              # Order model
│   │   └── Coupon.php             # Coupon model
│   ├── database/                  # Database files
│   │   └── database.sqlite        # SQLite database (created by setup.php)
│   ├── seeder.php                 # Test data seeding (called by setup.php --seed)
│   ├── .env                       # Configuration (git-ignored)
│   └── .env.example               # Example configuration
├── frontend/                      # PHP MVC Application
│   ├── config/app.php             # API configuration
│   ├── controllers/               # Page controllers
│   ├── models/                    # Business logic
│   ├── views/                     # HTML templates
│   ├── public/
│   │   ├── js/                    # JavaScript
│   │   └── css/                   # Stylesheets
│   ├── index.php                  # Frontend router
│   └── api.php                    # API proxy for sessions
```

## Development Workflow

### Adding New Endpoint

1. **Create Model Class** (if needed)

   ```php
   // backend/classes/MyModel.php
   class MyModel {
       // Model logic
   }
   ```

2. **Add Route in API**

   ```php
   // backend/public/api/index.php
   if ($method === 'POST' && $path === 'my/endpoint') {
       // Handle request
   }
   ```

3. **Test Endpoint**

   ```bash
   curl -X POST http://localhost:3001/api/my/endpoint \
     -H "Content-Type: application/json" \
     -d '{"key":"value"}'
   ```

4. **Update Documentation**
   - Add endpoint to API_DOCS.md
   - Update ARCHITECTURE.md if design changes

### Code Style

- Use 4 spaces for indentation
- Meaningful variable names
- Document complex logic with comments
- Use parameterized queries for DB access

## Performance Tips

### Development

- Use PHP built-in server for quick iteration (php -S localhost:3001 -t public in backend folder)
- Enable error reporting in .env: `DEBUG=true`
- Monitor PHP server logs in terminal where server is running
- Use SQLite for development (default DB_TYPE=sqlite)

### Production

- Use real web server (Apache/Nginx)
- Enable OpCache for PHP
- Set `DB_TYPE=mysql` for better performance and scalability
- Use database connection pooling
- Implement caching layer (Redis)
- Use CDN for static files (images, CSS, JS)
- Enable HTTPS with valid SSL certificate

## Next Steps

1. **Run the tests:** Follow the Testing section above
2. **Explore the code:** Read ARCHITECTURE.md for design patterns, then review [backend/public/api/index.php](backend/public/api/index.php)
3. **Try API:** Use curl examples above to test endpoints
4. **Understand Database Adapter Pattern:** Review how backend/classes/DatabaseAdapter.php, MySQLDatabase.php, and SQLiteDatabase.php work together
5. **Extend features:** Add new endpoints as needed following existing patterns
6. **Prepare for interview:** Review ARCHITECTURE.md and practice explaining the Database Adapter Pattern

## Getting Help

### Check Logs

```bash
# See active requests and errors in PHP server
# Check terminal where you ran: php -S localhost:3001 -t public

# PHP built-in server outputs errors directly to terminal
# Look for error messages and stack traces there
```

## Support Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [PDO Tutorial](https://www.php.net/manual/en/book.pdo.php)
- [REST API Best Practices](https://restfulapi.net/)
- [SQLite Documentation](https://www.sqlite.org/docs.html)

## Contributing

To improve this project:

1. Test changes locally
2. Update documentation
3. Follow code style guidelines
4. Submit improvements
