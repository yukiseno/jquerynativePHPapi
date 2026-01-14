# API Documentation

## Base URL

```
http://localhost:3001/api
```

## Authentication

Currently, the API supports two authentication methods:

### 1. No Authentication

Most endpoints are public and don't require authentication.

### 2. Bearer Token (for protected endpoints)

```
Authorization: Bearer {token}
```

## Endpoints

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
    "name": "WELCOME10",
    "discount_amount": 10,
    "valid_until": "2026-02-07"
  }
}
```

**Error Response (400):**

```json
{
  "error": "Invalid or expired coupon"
}
```

**Status Codes:**

- `200` - Coupon applied successfully
- `400` - Invalid or expired coupon
- `500` - Server error

**Example:**

```bash
curl -X POST http://localhost:3001/api/apply/coupon \
  -H "Content-Type: application/json" \
  -d '{"coupon_code":"WELCOME10"}'
```

#### Available Test Coupons

| Code      | Discount | Valid Until |
| --------- | -------- | ----------- |
| WELCOME10 | 10       | 2026-02-07  |
| SUMMER20  | 20       | 2026-03-07  |

### Error Responses

All errors follow this format:

```json
{
  "error": "Error message describing what went wrong"
}
```

Common error messages:

- `"Coupon code is required"` - Missing coupon_code parameter
- `"Invalid or expired coupon"` - Coupon not found or expired
- `"Server error: ..."` - Internal server error

## Response Format

All responses are JSON:

- `success` (boolean) - Whether the request was successful
- `message` (string) - Human-readable message
- `data` (object) - Response data (structure varies by endpoint)
- `error` (string) - Error message (only in error responses)

## HTTP Methods

- `GET` - Retrieve data
- `POST` - Create/apply data
- `PUT` - Update data (reserved for future use)
- `DELETE` - Delete data (reserved for future use)

## Status Codes

- `200` - OK: Request succeeded
- `201` - Created: Resource created successfully
- `400` - Bad Request: Invalid input or validation error
- `401` - Unauthorized: Missing or invalid authentication
- `404` - Not Found: Endpoint doesn't exist
- `500` - Internal Server Error: Server error occurred

## Rate Limiting

Currently no rate limiting. In production, implement:

- Per-IP rate limits
- Per-user rate limits
- Token-based quota system

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
# Test coupon endpoint
curl -X POST http://localhost:3001/api/apply/coupon \
  -H "Content-Type: application/json" \
  -d '{"coupon_code":"WELCOME10"}'

# Test with invalid coupon
curl -X POST http://localhost:3001/api/apply/coupon \
  -H "Content-Type: application/json" \
  -d '{"coupon_code":"INVALID"}'
```

### Using Postman

1. Create new POST request
2. URL: `http://localhost:3001/api/apply/coupon`
3. Headers: `Content-Type: application/json`
4. Body (raw JSON):

```json
{
  "coupon_code": "WELCOME10"
}
```

5. Click Send

### Using Frontend Test Page

Open `frontend/test-coupon.html` in browser for interactive testing.

## Upcoming Endpoints (Planned)

These endpoints are planned for future implementation:

### Products

- `GET /products` - List all products
- `GET /products/:id` - Get product details
- `GET /products/search` - Search products

### Users

- `POST /register` - Create new user account
- `POST /login` - Login and get token
- `POST /logout` - Logout user

### Orders

- `POST /orders` - Create new order
- `GET /orders` - List user's orders
- `GET /orders/:id` - Get order details

### Admin

- `POST /products` - Create product (admin only)
- `PUT /products/:id` - Update product (admin only)
- `DELETE /products/:id` - Delete product (admin only)

## Integration Guide

### JavaScript/jQuery

```javascript
// Apply coupon
function applyCoupon(couponCode) {
  $.ajax({
    url: "http://localhost:3001/api/apply/coupon",
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify({
      coupon_code: couponCode,
    }),
    success: function (response) {
      console.log("Discount amount:", response.data.discount_amount);
    },
    error: function (error) {
      console.log("Error:", error.responseJSON.error);
    },
  });
}
```

### Python/Requests

```python
import requests

response = requests.post(
    'http://localhost:3001/api/apply/coupon',
    json={'coupon_code': 'WELCOME10'}
)

if response.status_code == 200:
    data = response.json()
    discount = data['data']['discount_amount']
else:
    error = response.json()['error']
```

### Fetch API

```javascript
// Apply coupon with Fetch
fetch("http://localhost:3001/api/apply/coupon", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    coupon_code: "WELCOME10",
  }),
})
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("Discount:", data.data.discount_amount);
    } else {
      console.error("Error:", data.error);
    }
  })
  .catch((error) => console.error("Request failed:", error));
```

## Version History

### v1.0 (Current)

- [x] Coupon API endpoint
- [x] SQLite/MySQL support
- [x] CORS headers
- [x] Error handling
- [x] Validation

### v2.0 (Planned)

- [ ] Product endpoints
- [ ] User authentication (JWT)
- [ ] Order management
- [ ] Admin dashboard
- [ ] Payment integration

## Support

For issues or questions about the API:

1. Check the error response message
2. Verify request format matches documentation
3. Check PHP server logs for errors
4. Test with cURL to isolate frontend issues
