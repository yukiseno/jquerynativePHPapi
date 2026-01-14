# Setup Guide

## Prerequisites

- PHP 8.0 or higher
- SQLite (bundled with PHP)
- A modern web browser
- Optional: MySQL for production

## Installation

### Step 1: Clone/Download Project

```bash
git clone <repository-url>
cd jquerynativePHPapi
```

### Step 2: Backend Setup

```bash
cd backend

# Create .env file (if not exists)
cp .env.example .env

# Edit .env if needed (default SQLite works out of the box)
nano .env  # or use your preferred editor
```

### Step 3: Start Backend Server

**Option A: PHP Built-in Server (Recommended for Development)**

```bash
php -S localhost:3001 -t public
```

You should see:

```
PHP 8.4.13 Development Server (http://localhost:3001) started
```

**Option B: Apache/Nginx (Production)**

Configure your web server to point to the `backend/public` directory and route all requests through `api/index.php`.

### Step 4: Start Frontend Server

In a new terminal:

```bash
cd frontend

# Using http-server (Node.js - recommended)
npx http-server -p 3000

# Or Python 3
python3 -m http.server 3000

# Or Python 2
python -m SimpleHTTPServer 3000
```

You should see output indicating the server is running on port 3000.

### Step 5: Access the Application

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

```env
# Frontend can access backend API at this URL
API_URL=http://localhost:3001
```

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
- [ ] Products display
- [ ] Can search/filter products
- [ ] Can view product details
- [ ] Can add to cart
- [ ] Can checkout
- [ ] Can apply coupon

### 3. Use Test Page

Open `http://localhost:3000/test-coupon.html` for interactive API testing.

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
php -S localhost:3002 -t .
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
rm database/database.sqlite
php artisan migrate:fresh --seed  # if using Laravel migration

# Or manually create tables - see database-schema.sql
```

## Project Structure Details

```
backend/
├── api/
│   └── index.php              # Main API router - handles all requests
├── classes/
│   ├── Database.php           # PDO singleton for DB connections
│   ├── Coupon.php             # Coupon model with queries
│   └── [Model files]          # Additional models
├── database/
│   └── database.sqlite        # SQLite database file
├── .env                       # Configuration (git-ignored)
├── .env.example               # Example configuration
└── [PHP Files]                # Utility files

frontend/
├── index.html                 # Home page with product listing
├── product.html               # Product detail page
├── cart.html                  # Shopping cart
├── checkout.html              # Checkout with coupon
├── login.html                 # User login
├── register.html              # User registration
├── test-coupon.html           # API testing page
├── css/                       # Stylesheets
└── js/                        # JavaScript files
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
   // backend/api/index.php
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

- Use PHP built-in server for quick iteration
- Enable error reporting in .env
- Monitor PHP server logs

### Production

- Use real web server (Apache/Nginx)
- Enable OpCache for PHP
- Set `DB_TYPE=mysql` for better performance
- Implement caching layer (Redis)
- Use CDN for static files

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
2. **Explore the code:** Read through backend/api/index.php
3. **Try API:** Use test-coupon.html to test endpoints
4. **Extend features:** Add new endpoints as needed
5. **Prepare for interview:** Practice explaining architecture

## Getting Help

### Check Logs

```bash
# PHP error logs
tail -f /var/log/php-fpm.log

# See active requests to PHP server
# Check terminal where you ran: php -S localhost:3001
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
