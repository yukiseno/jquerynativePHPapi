# jQuery Native PHP E-Commerce

A modern, full-stack e-commerce application built with **Native PHP** backend and **PHP MVC frontend**, demonstrating professional web development practices.

## 🎯 Architecture Overview

- **Backend:** Native PHP REST API (port 3001) - No frameworks, pure PDO
- **Frontend:** PHP MVC with Server-Side Rendering (port 3000) - Clean URLs, Bootstrap 5
- **Database:** SQLite (development) / MySQL (production)
- **Authentication:** Bearer Tokens + PHP sessions
- **Styling:** Bootstrap 5.3.0

## ✨ Features

### Backend API

- ✅ Native PHP - Pure PHP with PDO, no dependencies
- ✅ Bearer Token Authentication - Database-backed token-based auth
- ✅ RESTful API - Professional HTTP conventions
- ✅ Product Management - Browse, filter, search
- ✅ Coupon System - Discount codes with expiry
- ✅ Order Processing - Complete order flow
- ✅ Security - Parameterized queries, bcrypt hashing, input validation

### Frontend

- ✅ PHP MVC Architecture - Clean separation of concerns
- ✅ Server-Side Authentication - PHP sessions with fallback to bearer tokens
- ✅ Clean URLs - `/product/slug` instead of `/?page=product`
- ✅ Responsive Design - Mobile-friendly with Bootstrap
- ✅ Client-Side Interactions - jQuery for smooth UX
- ✅ Shopping Cart - localStorage-based cart management
- ✅ Full Checkout - Address, coupon application, order placement

## 📁 Project Structure

```
jquerynativePHPapi/
├── backend/                    # REST API
│   ├── public/
│   │   └── api/
│   │       └── index.php       # Main API router
│   ├── classes/
│   │   ├── Database.php        # PDO singleton
│   │   ├── DatabaseAdapter.php # Database interface
│   │   ├── MySQLDatabase.php   # MySQL adapter
│   │   ├── SQLiteDatabase.php  # SQLite adapter
│   │   ├── Product.php         # Product queries
│   │   ├── User.php            # User auth
│   │   ├── Coupon.php          # Coupon logic
│   │   └── Order.php           # Order processing
│   ├── middleware.php          # Response helpers (apiSuccess, apiError)
│   ├── database/                # Database files directory
│   │   └── database.sqlite     # SQLite database (created by setup.php)
│   ├── setup.php               # Initialize database schema
│   ├── seeder.php              # Add test data
│   ├── .env.example            # Example config (copy to .env)
│   └── .gitignore              # Ignores .env and database.sqlite
│
├── frontend/                   # PHP MVC Application
│   ├── index.php               # Main router
│   ├── api.php                 # API proxy for sessions
│   ├── config/
│   │   └── app.php             # Global config & helpers
│   ├── controllers/            # Page controllers
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── LoginController.php
│   │   ├── RegisterController.php
│   │   ├── CartController.php
│   │   ├── ProfileController.php
│   │   ├── OrdersController.php
│   │   └── CheckoutController.php
│   ├── models/                 # Business logic
│   │   ├── ApiClient.php       # REST API client
│   │   ├── Product.php
│   │   ├── User.php
│   │   └── Coupon.php
│   ├── views/                  # HTML templates
│   │   ├── layout.php          # Master layout
│   │   ├── home.php
│   │   ├── product.php
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── cart.php
│   │   ├── profile.php
│   │   ├── orders.php
│   │   ├── checkout.php
│   │   ├── 404.php
│   │   └── ...
│   ├── public/
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── js/
│   │   │   ├── app.js          # Global utilities
│   │   │   └── pages/          # Page-specific JS
│   │   └── images/
│   ├── api.php                 # PHP session AJAX handler
│   └── ...
│
├── start.sh                    # Start both servers
├── README.md
└── ...
```

## 🚀 Quick Start

### 1. Clone & Setup

```bash
git clone https://github.com/yukiseno/jquerynativePHPapi.git
cd jquerynativePHPapi

# Create database and seed data
cd backend
php setup.php
cd ..
```

### 2. Start Both Servers

```bash
./start.sh
```

This starts:

- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:3001

Or manually in separate terminals:

```bash
# Terminal 1: Backend
cd backend
php -S localhost:3001 -t public

# Terminal 2: Frontend
cd frontend
php -S localhost:3000
```

### 3. Access the Application

- **Home:** http://localhost:3000 (browse products)

## 🧪 Test Data

After setup, the database is seeded with:

**Coupons:**

- `WELCOME10` - 10% discount
- `SUMMER20` - 20% discount

**Products:**

- Classic Red T-Shirt ($25)
- Classic Green T-Shirt ($25)
- Classic Blue T-Shirt ($25)
- T-Shirt ($30) - Multiple colors

**Colors:**

Black, White, Red, Blue, Green, Gray, Navy

**Sizes:**

XS, S, M, L, XL, XXL

## 🔄 Workflow

1. **Browse** products on home page
2. **View** product details and select color/size
3. **Add to Cart** (stored in localStorage)
4. **Login** (required to checkout)
5. **Apply Coupon** (WELCOME10 or SUMMER20)
6. **Place Order** with billing address
7. **View** order history and profile

## 🔐 Security Features

- Bearer token-based API authentication (database-backed)
- PHP session-based frontend authentication
- Parameterized SQL queries (prevent SQL injection)
- Bcrypt password hashing
- Input validation and sanitization
- CORS-aware API design
- Secure session handling

## 📊 API Endpoints

### Products

```
GET  /api/products                    # List all products
GET  /api/product/{slug}/slug         # Get product by slug
GET  /api/product/{id}/show           # Get product by ID
```

### Authentication

```
POST /api/user/register               # Create account
POST /api/user/login                  # Get bearer token
GET  /api/user/profile                # Get user info (requires token)
POST /api/user/profile/update         # Update profile
```

### Orders

```
POST /api/orders/store                # Place order
GET  /api/user/orders                 # Get user orders
GET  /api/orders/{id}                 # Get order details
```

### Coupons

```
POST /api/apply/coupon                # Apply discount code
```

## 💡 Key Technologies

| Layer          | Technology                          |
| -------------- | ----------------------------------- |
| Backend API    | Native PHP 8.0+, PDO                |
| Frontend       | PHP 8.0+, Bootstrap 5.3, jQuery 3.6 |
| Database       | SQLite (dev), MySQL (prod)          |
| Authentication | Bearer Tokens + PHP Sessions        |
| Styling        | Bootstrap 5.3.0 CDN                 |

## 🎓 Learning Outcomes

This project demonstrates:

- **Backend:**
  - Native PHP without frameworks
  - RESTful API design
  - Bearer token authentication (database-backed)
  - **Database Adapter Pattern** - Seamless database switching (SQLite/MySQL) without if/else checks in business logic
  - Error handling and security

- **Frontend:**
  - Server-side rendering with PHP MVC
  - Clean URL routing
  - Client-side state management (localStorage)
  - Bootstrap responsive design
  - jQuery DOM manipulation
  - AJAX for API communication

## 👨‍💻 Author

Created by [Yuki Seno](https://github.com/yukiseno)

````

**Test Coupons:**

- `WELCOME10` - 10% discount (valid for 1 month)
- `SUMMER20` - 20% discount (valid for 2 months)

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
````

## Design Decisions

### Why Native PHP?

- **Clean Code** - No framework bloat, every line is visible and understood
- **Performance** - Minimal overhead for interview demonstration
- **Control** - Full control over routing, database, and logic
- **Simplicity** - Easy to review and understand the codebase

### Code Architecture

- **Database Adapter Pattern** - Abstracts SQLite/MySQL differences, eliminates database type checks in business logic
- **Separation of Concerns** - Models in `/classes`, routes in `/api`
- **Singleton Pattern** - Database connection pooling via singleton
- **RESTful Design** - Clean HTTP methods and status codes

### Security

- **Password Hashing** - bcrypt with automatic salting
- **SQL Injection Prevention** - Parameterized queries throughout
- **Bearer Token Auth** - Database-backed tokens for API authentication
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

**CORS errors in browser:**

```bash
# 1. Verify backend is running on port 3001
curl http://localhost:3001/api/products

# 2. Verify frontend API_URL is correct
# Check: frontend/config/app.php -> define('API_URL', '...')

# 3. Check browser DevTools Network tab for failed requests

# 4. Verify no mixed HTTP/HTTPS (both should be HTTP for local dev)

# Common causes:
# - Backend not running
# - Wrong API_URL in frontend config
# - Browser caching CORS rejection
# - Port 3001 blocked by firewall
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
- **Architecture:** **Database Adapter Pattern** for multi-database support, Singleton pattern for connection pooling, MVC separation of concerns
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
- ✅ Clear separation of concerns (MVC pattern)
- ✅ DRY principle (Don't Repeat Yourself) - standardized response helpers
- ✅ Proper HTTP semantics (status code constants)
- ⏳ Input validation (partial - email validation, basic checks)
- ⏳ Error messages (some are meaningful, some are generic)

## 🤝 Contributing

This is a portfolio project. For improvements or questions, feel free to create an issue or pull request.
