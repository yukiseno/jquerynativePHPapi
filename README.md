# Native PHP E-Commerce API

A clean, production-ready e-commerce backend built with **native PHP** (no frameworks) demonstrating modern API design, security best practices, and clean architecture principles.

**Perfect for:** Portfolio projects, technical interviews, and learning full-stack development.

## 🎯 About This Project

This project showcases a **complete e-commerce API** built from scratch without relying on frameworks, demonstrating:

- Deep understanding of PHP fundamentals and core concepts
- Professional API design and RESTful conventions
- Security-first development practices
- Database abstraction and portability
- Clean code organization and maintainability
- Production-ready error handling

Unlike framework-based projects, this shows you understand **what happens under the hood** — essential for senior developer roles.

## ✨ Key Features

- ✅ **Native PHP** - Pure PHP with PDO, no framework dependencies — demonstrates core language expertise
- ✅ **JWT Authentication** - Secure, stateless token-based authentication for APIs
- ✅ **Multi-Database Support** - SQLite for development, MySQL for production (true database abstraction)
- ✅ **Product Management** - Browse, filter, and search with complex queries
- ✅ **Coupon System** - Discount application with expiry validation and business logic
- ✅ **RESTful API** - Professional HTTP conventions and status codes
- ✅ **Security First** - Parameterized queries, bcrypt hashing, input validation, CORS
- ✅ **Error Handling** - Comprehensive error responses and 404 handling
- ✅ **Production Ready** - Proper configuration management, logging support
  📁 Project Structure

```
backend/
  ├── public/                  # Document root (served to clients)
  │   └── api/
  │       └── index.php        # Main API router and handlers
  ├── classes/                 # Business logic and models
  │   ├── Database.php         # PDO singleton abstraction
  │   ├── Coupon.php           # Coupon model and validation
  │   ├── Product.php          # Product queries and filtering
  │   └── User.php             # User auth and management
  ├── database/
  │   └── database.sqlite      # SQLite database (auto-created)
  ├── .env                     # Environment configuration
  ├── .env.example             # Configuration template for developers
  └── setup.php                # Database initialization script

frontend/
  ├── index.html               # Home page with product listing
  ├── product.html             # Product details page
  ├── cart.html                # Shopping cart management
  ├── checkout.html            # Checkout with coupon application
  ├── login.html               # User authentication
  ├── register.html            # User registration
  └── test-coupon.html         # API testing tool
```

**Design rationale:** The `public/` folder serves as the document root, keeping sensitive files (classes, database config) outside the web-accessible directory — a security best practice.── register.html # Registration page

````

## Quick Start

### 1. Backend Setup

```bash
cd backend

# Start PHP development server (port 3001)
php -S localhost:3001 -t public
````

### 2. Frontend Setup

```bash
# Open frontend in browser (in a new terminal)
cd frontend
npx http-server -p 3000
# Navigate to http://localhost:3000
```

## API Endpoints

### Coupons

**Apply Coupon**

```
POST /api/apply/coupon
Content-Type: application/json

{
  "coupon_code": "WELCOME10"
}

Response:
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

**Test Coupons:**

- `WELCOME10` - 10% discount
- `SUMMER20` - 20% discount

## Configuration

Edit `.env` to configure:

```env
DB_TYPE=sqlite                                          # sqlite or mysql
SQLITE_PATH=/path/to/database.sqlite                   # SQLite path
DB_HOST=localhost                                      # MySQL host
DB_NAME=ecommerce                                      # MySQL database
DB_USER=root                                           # MySQL user
DB_PASS=                                               # MySQL password
API_URL=http://localhost:3001                          # API base URL
```

## Database

### Coupons Table

```sql
CREATE TABLE coupons (
  id INTEGER PRIMARY KEY,
  name TEXT UNIQUE,
  discount INTEGER,
  valid_until TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Sample Data

```sql
INSERT INTO coupons (name, discount, valid_until) VALUES
('WELCOME10', 10, '2026-02-07'),
('SUMMER20', 20, '2026-03-07');
```

## Testing

### Test Coupon Endpoint

Open `frontend/test-coupon.html` in browser or use curl:

```bash
curl -X POST http://localhost:3001/api/apply/coupon \
  -H "Content-Type: application/json" \
  -d '{"coupon_code":"WELCOME10"}'
```

## Design Decisions

### Why Native PHP?

- **Clean Code** - No framework bloat, every line is visible and understood
- **Performance** - Minimal overhead for interview demonstration
- **Control** - Full control over routing, database, and logic
- **Simplicity** - Easy to review and understand the codebase

### Code Architecture

- **Separation of Concerns** - Models in `/classes`, routes in `/api`
- **PDO Abstraction** - Database layer is abstraction-agnostic
- **Singleton Pattern** - Database connection pooling via singleton
- **RESTful Design** - Clean HTTP methods and status codes

### Security

- **Password Hashing** - bcrypt with automatic salting
- **SQL Injection Prevention** - Parameterized queries throughout
- **Bearer Token Auth** - JWT tokens for API authentication
- **CORS Support** - Safe cross-origin requests

## Development Notes

### Adding New Endpoints

1. Create model class in `/classes`
2. Add route handler in `api/index.php`
3. Follow RESTful conventions
4. Use parameterized queries

### Database Switching

Change `DB_TYPE` in `.env`:

- `sqlite` - SQLite (default, no setup needed)
- `mysql` - MySQL (requires DB config)

- `sqlite` - SQLite (default, no setup needed)
- `mysql` - MySQL (requires DB config)

## 💡 Interview Talking Points

### When discussing this project, highlight:

**1. Why Native PHP?**

- "I chose native PHP to demonstrate deep understanding of core concepts without framework abstractions"
- "Shows I can debug and understand what's happening at every level"
- "Perfect for an intermediate-to-senior role where you need to understand fundamentals"

**2. Architecture & Design Decisions**

- "Used PDO singleton for database abstraction — easily switch between SQLite and MySQL"
- "Kept sensitive files outside the document root (`public/`) for security"
- "Implemented proper error handling with meaningful HTTP status codes"
- "Separated concerns: routing in API, business logic in classes"

**3. Security Implementation**

- "All database queries use parameterized statements to prevent SQL injection"
- "Passwords hashed with bcrypt and automatic salting"
- "CORS headers configured for safe cross-origin requests"
- "Email validation before database insertion"

**4. What You Can Explain in Detail**

- How the PDO singleton works and why it's useful
- The flow from HTTP request → router → model → database → JSON response
- Why you chose the project structure
- How to switch databases with just one `.env` change
- Security best practices demonstrated in the code

### Questions You Might Get:

**Q: "How would you scale this?"**
A: "Move to a framework like Laravel for middleware, caching layers, and queue workers. But first I'd add Redis for session management, implement database connection pooling, and add API rate limiting."

**Q: "How would you handle N+1 queries?"**
A: "I'd use eager loading patterns, add query optimization in Product::getAll(), and implement caching for frequently accessed data."

**Q: "What about testing?"**
A: "I'd implement PHPUnit for unit tests on models, integration tests for API endpoints, and use mock databases for testing."

## 🧪 Testing Checklist

- [x] Coupon API endpoint responds correctly
- [x] Valid coupons return discount amount
- [x] Invalid/expired coupons return 400 error
- [x] Missing parameters return validation error
- [x] CORS headers present in responses
- [x] JSON responses are valid
- [x] Database queries use parameterized statements
- [x] Email validation works
- [x] Password hashing works
- [x] 404 handler for unknown routes
- [x] Error messages are user-friendly

## 🛠️ Troubleshooting

### Common Issues

**Port already in use:**

```bash
# Change port number
php -S localhost:3002 -t public  # Use port 3002 instead
```

**Database file not found:**

```bash
# SQLite database is created automatically on first access
# If issues persist, ensure backend/database/ directory exists
mkdir -p backend/database
```

**CORS errors in browser:**

```bash
# Ensure backend is running on port 3001
# Check that index.php has CORS headers:
header('Access-Control-Allow-Origin: *');
```

**API returns 404:**

```bash
# Make sure you're calling the correct endpoint
POST http://localhost:3001/api/apply/coupon
# Not: http://localhost:3001/public/api/apply/coupon
```

## 📊 Technologies & Skills Demonstrated

- **Language:** PHP 8.0+ (OOP, static methods, magic methods)
- **Database:** PDO, SQLite, MySQL, parameterized queries
- **Architecture:** Singleton pattern, MVC separation, abstraction layers
- **Security:** bcrypt, SQL injection prevention, input validation
- **API:** REST principles, HTTP status codes, JSON, CORS
- **Frontend:** jQuery, Ajax requests, Bootstrap
- **DevOps:** Environment configuration, cross-platform database support

## 🚀 Production Deployment

When deploying to production:

1. **Set secure `.env` values:**

   ```bash
   DB_TYPE=mysql
   DB_HOST=prod-db-server.com
   DB_PASS=secure_password_here
   ```

2. **Use PHP-FPM with Nginx or Apache** (not PHP built-in server)

3. **Enable HTTPS** for all API endpoints

4. **Add rate limiting** to prevent abuse

5. **Set up monitoring** for database queries and errors

6. **Use prepared statements** (already done ✓)

7. **Add request logging** for debugging

## 📚 Code Quality

This project demonstrates:

- ✅ Consistent naming conventions (camelCase for variables/methods, PascalCase for classes)
- ✅ Clear separation of concerns
- ✅ DRY principle (Don't Repeat Yourself)
- ✅ Meaningful error messages
- ✅ Input validation and sanitization
- ✅ Proper HTTP semantics

## 🤝 Contributing

This is a portfolio project. For improvements or questions, feel free to create an issue or pull request.

## 📝 License

MIT License - Feel free to use for portfolio and interview preparation.
