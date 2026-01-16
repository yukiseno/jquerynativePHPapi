# API Documentation

## Base URL

```
http://localhost:3001/api
```

## Authentication

The API uses Bearer Token authentication for protected endpoints.

### Protected Endpoints

Endpoints requiring authentication use Bearer tokens in the Authorization header:

```
Authorization: Bearer {access_token}
```

### Public Endpoints

These endpoints don't require authentication:

- `GET /products` - List products
- `POST /user/register` - Register new user
- `POST /user/login` - Login user
- `POST /apply/coupon` - Apply coupon code

## Response Format

All responses are JSON with the following structure:

**Success Response:**

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {}
}
```

**Error Response:**

```json
{
  "success": false,
  "error": "Error description"
}
```

## Endpoints

### Authentication

#### Register User

**POST** `/user/register`

Create a new user account.

**Request:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

**Success Response (201):**

```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

**Error Response (400):**

```json
{
  "success": false,
  "error": "Email already exists"
}
```

#### Login User

**POST** `/user/login`

Authenticate user and get access token.

**Request:**

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Success Response (200):**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "profile_completed": 0
  }
}
```

**Error Response (401):**

```json
{
  "success": false,
  "error": "Invalid email or password"
}
```

#### Logout User

**POST** `/user/logout`

Revoke user's access token.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Success Response (200):**

```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

#### Get User Profile

**GET** `/user/profile`

Get current authenticated user's profile information.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Success Response (200):**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone_number": "555-1234",
    "address": "123 Main St",
    "city": "New York",
    "country": "USA",
    "zip_code": "10001",
    "profile_completed": 1
  }
}
```

#### Update User Profile

**PUT** `/user/profile/update`

Update user's profile information (address, phone, etc.).

**Headers:**

```
Authorization: Bearer {access_token}
Content-Type: application/json
```

**Request:**

```json
{
  "phone_number": "555-1234",
  "address": "123 Main St",
  "city": "New York",
  "country": "USA",
  "zip_code": "10001"
}
```

**Success Response (200):**

```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    /* updated user data */
  }
}
```

### Products

#### List All Products

**GET** `/products`

Get all products with available colors and sizes.

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "description": "Product description",
      "slug": "product-name",
      "thumbnail": "/images/product.jpg",
      "price": 2999,
      "created_at": "2026-01-16 10:00:00",
      "colors": [
        { "id": 1, "name": "Black" },
        { "id": 2, "name": "White" }
      ],
      "sizes": [
        { "id": 1, "name": "S" },
        { "id": 2, "name": "M" }
      ]
    }
  ],
  "colors": [{ "id": 1, "name": "Black", "created_at": "2026-01-16 10:00:00" }],
  "sizes": [{ "id": 1, "name": "S", "created_at": "2026-01-16 10:00:00" }]
}
```

#### Get Product Details

**GET** `/products/{id}`

Get specific product with all details.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "T-Shirt",
    "description": "Comfortable cotton t-shirt",
    "slug": "t-shirt",
    "thumbnail": "/images/tshirt.jpg",
    "price": 1999,
    "colors": [
      { "id": 1, "name": "Black" },
      { "id": 2, "name": "White" }
    ],
    "sizes": [
      { "id": 1, "name": "XS" },
      { "id": 2, "name": "S" }
    ]
  }
}
```

### Coupons

#### Apply Coupon

**POST** `/apply/coupon`

Apply a coupon code to get discount information.

**Request:**

```json
{
  "coupon_code": "WELCOME10"
}
```

**Success Response (200):**

```json
{
  "success": true,
  "message": "Coupon applied successfully",
  "data": {
    "id": 1,
    "code": "WELCOME10",
    "discount_amount": 10,
    "valid_until": "2026-03-16"
  }
}
```

**Error Response (400):**

```json
{
  "success": false,
  "error": "Invalid or expired coupon"
}
```

#### Available Test Coupons

| Code      | Discount | Valid Until |
| --------- | -------- | ----------- |
| WELCOME10 | 10%      | 2026-03-16  |
| SUMMER20  | 20%      | 2026-04-16  |

### Orders

#### Create Order

**POST** `/orders/store`

Create a new order with cart items and delivery address.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request:**

```json
{
  "cartItems": [
    {
      "id": 1,
      "name": "T-Shirt",
      "price": 1999,
      "quantity": 2,
      "colorId": 1,
      "colorName": "Black",
      "sizeId": 1,
      "sizeName": "M"
    }
  ],
  "address": {
    "phoneNumber": "555-1234",
    "address": "123 Main St",
    "city": "New York",
    "country": "USA",
    "zip": "10001"
  },
  "couponId": 1
}
```

**Success Response (201):**

```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "order": {
      "id": 1,
      "user_id": 1,
      "subtotal": 3998,
      "discount_total": 399,
      "total": 3599,
      "status": "paid",
      "created_at": "2026-01-16 10:00:00"
    },
    "items": [
      {
        "id": 1,
        "product_name": "T-Shirt",
        "color_name": "Black",
        "size_name": "M",
        "qty": 2,
        "price": 1999,
        "subtotal": 3998
      }
    ]
  }
}
```

#### Get User's Orders

**GET** `/orders`

Get all orders for authenticated user.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "subtotal": 3998,
      "discount_total": 399,
      "total": 3599,
      "status": "paid",
      "created_at": "2026-01-16 10:00:00"
    }
  ]
}
```

#### Get Order Details

**GET** `/orders/{id}`

Get specific order with all items.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "order": {
      "id": 1,
      "user_id": 1,
      "subtotal": 3998,
      "discount_total": 399,
      "total": 3599,
      "status": "paid",
      "created_at": "2026-01-16 10:00:00"
    },
    "items": [
      {
        "id": 1,
        "product_name": "T-Shirt",
        "color_name": "Black",
        "size_name": "M",
        "qty": 2,
        "price": 1999
      }
    ]
  }
}
```

## HTTP Status Codes

- `200` - OK: Request succeeded
- `201` - Created: Resource created successfully
- `400` - Bad Request: Invalid input or validation error
- `401` - Unauthorized: Missing or invalid authentication token
- `404` - Not Found: Endpoint or resource doesn't exist
- `500` - Internal Server Error: Server error occurred

## CORS

All endpoints support CORS for frontend integration:

```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
```

## Testing

### Using cURL

```bash
# Register user
curl -X POST http://localhost:3001/api/user/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
  }'

# Login
curl -X POST http://localhost:3001/api/user/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'

# Get products
curl -X GET http://localhost:3001/api/products

# Apply coupon
curl -X POST http://localhost:3001/api/apply/coupon \
  -H "Content-Type: application/json" \
  -d '{"coupon_code":"WELCOME10"}'
```

### Using Postman

1. Import the API endpoints
2. Set `{{base_url}}` variable to `http://localhost:3001/api`
3. Set `{{token}}` variable with your access token after login
4. Use Bearer token in Authorization header for protected endpoints

### Using Frontend

The frontend application at `http://localhost:3000` provides full UI for all API endpoints.

## Database Support

The API supports both SQLite and MySQL databases. Configure via `.env`:

```
DB_TYPE=mysql          # or 'sqlite'
DB_HOST=localhost
DB_NAME=ecommerce
DB_USER=root
DB_PASS=
```

## Architecture

The API uses a **Database Adapter Pattern** to abstract database-specific logic:

- `DatabaseAdapter` interface defines the contract
- `MySQLDatabase` and `SQLiteDatabase` implementations
- Business logic classes (`User`, `Order`, `Product`) use adapters

This allows seamless switching between database types without code changes.
