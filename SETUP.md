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

### Step 2: Backend Setup

```bash
cd backend

# Copy example config to create .env (git-ignored)
cp .env.example .env

# Edit .env if needed for MySQL (default SQLite works out of the box)
nano .env  # or use your preferred editor
```

### Step 2b: Initialize Database

From the project root directory:

```bash
php setup.php
```

This works for both **SQLite (default)** and **MySQL**. For MySQL, create your database first, then run the command above.

### Step 3: Start Both Servers

**Recommended (One Command):**

```bash
php start.php
```

This automatically starts both servers:

- **Backend API:** http://localhost:3001
- **Frontend:** http://localhost:3000

The script detects your OS (Mac, Linux, Windows) and handles startup automatically. Press `Ctrl+C` to stop.

**Alternative: Manual Startup**

If you prefer to control servers separately:

**Terminal 1 - Backend:**

```bash
cd backend
php -S localhost:3001 -t public
```

**Terminal 2 - Frontend:**

```bash
cd frontend
php -S localhost:3000
```

### Step 4: Access the Application

Open your browser to:

```
http://localhost:3000
```

## Configuration

### Database Configuration

Edit `backend/.env`:

```env
# SQLite (default, no setup needed)
DB_TYPE=sqlite
SQLITE_PATH=./database/database.sqlite

# OR MySQL
DB_TYPE=mysql
DB_HOST=localhost
DB_NAME=ecommerce
DB_USER=root
DB_PASS=your_password
```

### API Configuration

Frontend API configuration is in `frontend/config/app.php` and already points to:

```
http://localhost:3001/api
```

No additional configuration needed for local development.

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

**Error:** `SQLSTATE[HY000]`

**Solution:** Rebuild database

```bash
# Reset SQLite
rm backend/database/database.sqlite
php backend/setup.php
php backend/seeder.php
```

## Project Structure Details

```
backend/
php reset-database.php
```

Or manually:

```bash
rm backend/database/database.sqlite
php setup
│   ├── DatabaseAdapter.php        # Database interface (adapter pattern)
│   ├── MySQLDatabase.php          # MySQL adapter
│   ├── SQLiteDatabase.php         # SQLite adapter
│   ├── Database.php               # Singleton factory for DB adapters
│   ├── User.php                   # User model with auth methods
│   ├── Product.php                # Product model
│   ├── Order.php                  # Order model
│   └── Coupon.php                 # Coupon model
├── database/
│   └── database.sqlite            # SQLite database file
├── .env                           # Configuration (git-ignored)
├── .env.example                   # Example configuration
├── setup.php                      # Database schema initialization
└── seeder.php                     # Test data seeding

frontend/
├── config/
│   └── app.php                    # API configuration
├── controllers/                   # Page controllers
├── models/
│   ├── ApiClient.php              # API communication wrapper
│   └── [Model files]
├── views/                         # Page templates
├── public/
│   ├── js/                        # JavaScript (app.js + page-specific)
│   └── css/                       # Stylesheets
├── index.php                      # Frontend router
└── api.php                        # API proxy for logout/session
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

- Follow PSR-12 PHP coding standard
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

## Security Checklist

- [ ] `.env` file in `.gitignore`
- [ ] Database credentials not in code
- [ ] All SQL queries parameterized
- [ ] Input validation on all endpoints
- [ ] CORS restricted to trusted origins
- [ ] Error messages don't expose system details
- [ ] Passwords hashed with bcrypt
- [ ] HTTPS enabled in production
- [ ] Rate limiting implemented
- [ ] SQL injection tests passed

## Deployment

### Quick Deploy (for testing)

```bash
# Build production bundle
composer install --no-dev

# Deploy to server
rsync -az --delete backend/ user@server:/var/www/api/
rsync -az --delete frontend/ user@server:/var/www/frontend/
```

### Docker Deployment

See `Dockerfile` for containerized deployment.

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

### Debug Mode

Add to backend `.env`:

```env
DEBUG=true
```

Then check error output in PHP server terminal.

### Test Database

```bash
# Open SQLite shell
sqlite3 backend/database/database.sqlite

# List tables
.tables

# Query coupons
SELECT * FROM coupons;

# Exit
.quit
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

## License

MIT License - Free to use for portfolio and interview preparation.
