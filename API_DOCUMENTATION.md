# 🏪 Brayne Jewelry Manager - API Documentation

## Overview

The Brayne Jewelry Manager API provides RESTful endpoints for integrating with external systems, mobile applications, and third-party services. This API follows REST conventions and returns JSON responses.

## Base URL

```
http://yourdomain.com/api/v1
```

## Authentication

All API endpoints require authentication using Bearer tokens. Include the token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Response Format

All API responses follow this standard format:

```json
{
    "success": true,
    "data": {},
    "message": "Operation completed successfully",
    "errors": null
}
```

Error responses:

```json
{
    "success": false,
    "data": null,
    "message": "Error message",
    "errors": {
        "field": ["Error description"]
    }
}
```

## Endpoints

### Authentication

#### POST /auth/login
Authenticate a user and receive an access token.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "distributor"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "expires_at": "2025-07-24T10:00:00Z"
    },
    "message": "Login successful"
}
```

#### POST /auth/logout
Logout and invalidate the current token.

**Response:**
```json
{
    "success": true,
    "data": null,
    "message": "Logged out successfully"
}
```

### Orders

#### GET /orders
Get a list of orders with pagination and filtering.

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15)
- `status` (optional): Filter by order status
- `payment_status` (optional): Filter by payment status
- `customer_id` (optional): Filter by customer ID
- `date_from` (optional): Filter orders from date (YYYY-MM-DD)
- `date_to` (optional): Filter orders to date (YYYY-MM-DD)
- `priority` (optional): Filter by priority (low, normal, high)

**Response:**
```json
{
    "success": true,
    "data": {
        "orders": [
            {
                "id": 1,
                "order_number": "ORD-2025-001",
                "customer": {
                    "id": 1,
                    "name": "John Customer",
                    "email": "customer@example.com"
                },
                "total_amount": 2500.00,
                "payment_status": "partially_paid",
                "order_status": "approved",
                "priority": "normal",
                "created_at": "2025-07-24T01:00:00Z",
                "updated_at": "2025-07-24T01:30:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 50,
            "last_page": 4
        }
    },
    "message": "Orders retrieved successfully"
}
```

#### POST /orders
Create a new order.

**Request Body:**
```json
{
    "customer_id": 1,
    "total_amount": 2500.00,
    "payment_status": "unpaid",
    "notes": "Special instructions for this order",
    "products": [
        {
            "product_id": 1,
            "quantity": 2,
            "price": 1250.00,
            "metal": "Stainless",
            "font": "Arial"
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "order": {
            "id": 1,
            "order_number": "ORD-2025-001",
            "total_amount": 2500.00,
            "payment_status": "unpaid",
            "order_status": "pending_payment",
            "created_at": "2025-07-24T01:00:00Z"
        }
    },
    "message": "Order created successfully"
}
```

#### GET /orders/{id}
Get a specific order by ID.

**Response:**
```json
{
    "success": true,
    "data": {
        "order": {
            "id": 1,
            "order_number": "ORD-2025-001",
            "customer": {
                "id": 1,
                "name": "John Customer",
                "email": "customer@example.com",
                "phone": "+1234567890"
            },
            "distributor": {
                "id": 1,
                "company_name": "Golden Jewelers"
            },
            "total_amount": 2500.00,
            "payment_status": "partially_paid",
            "order_status": "approved",
            "priority": "normal",
            "notes": "Special instructions",
            "products": [
                {
                    "id": 1,
                    "name": "Diamond Ring",
                    "sku": "DR-001",
                    "quantity": 2,
                    "price": 1250.00,
                    "metal": "Stainless",
                    "font": "Arial"
                }
            ],
            "status_history": [
                {
                    "status": "pending_payment",
                    "created_at": "2025-07-24T01:00:00Z"
                },
                {
                    "status": "approved",
                    "created_at": "2025-07-24T01:30:00Z"
                }
            ],
            "created_at": "2025-07-24T01:00:00Z",
            "updated_at": "2025-07-24T01:30:00Z"
        }
    },
    "message": "Order retrieved successfully"
}
```

#### PUT /orders/{id}/status
Update the status of an order.

**Request Body:**
```json
{
    "order_status": "in_production",
    "notes": "Production started"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "order": {
            "id": 1,
            "order_status": "in_production",
            "updated_at": "2025-07-24T02:00:00Z"
        }
    },
    "message": "Order status updated successfully"
}
```

### Products

#### GET /products
Get a list of products with filtering.

**Query Parameters:**
- `category` (optional): Filter by category
- `active` (optional): Filter by active status (true/false)
- `search` (optional): Search by name or SKU

**Response:**
```json
{
    "success": true,
    "data": {
        "products": [
            {
                "id": 1,
                "name": "Diamond Ring",
                "sku": "DR-001",
                "category": "Rings",
                "sub_category": "Engagement Rings",
                "metals": ["Stainless", "Brass Gold", "925 Pure Sterling Silver"],
                "local_pricing": {
                    "Stainless": 2500.00,
                    "Brass Gold": 2800.00,
                    "925 Pure Sterling Silver": 3200.00
                },
                "international_pricing": {
                    "Stainless": 50.00,
                    "Brass Gold": 56.00,
                    "925 Pure Sterling Silver": 64.00
                },
                "fonts": ["Arial", "Times New Roman"],
                "font_requirement": 1,
                "is_active": true,
                "image_url": "http://example.com/images/diamond-ring.jpg"
            }
        ]
    },
    "message": "Products retrieved successfully"
}
```

#### GET /products/{id}
Get a specific product by ID.

**Response:**
```json
{
    "success": true,
    "data": {
        "product": {
            "id": 1,
            "name": "Diamond Ring",
            "sku": "DR-001",
            "category": "Rings",
            "sub_category": "Engagement Rings",
            "custom_sub_category": null,
            "metals": ["Stainless", "Brass Gold", "925 Pure Sterling Silver"],
            "local_pricing": {
                "Stainless": 2500.00,
                "Brass Gold": 2800.00,
                "925 Pure Sterling Silver": 3200.00
            },
            "international_pricing": {
                "Stainless": 50.00,
                "Brass Gold": 56.00,
                "925 Pure Sterling Silver": 64.00
            },
            "fonts": ["Arial", "Times New Roman"],
            "font_requirement": 1,
            "is_active": true,
            "image_url": "http://example.com/images/diamond-ring.jpg",
            "created_at": "2025-07-24T01:00:00Z",
            "updated_at": "2025-07-24T01:00:00Z"
        }
    },
    "message": "Product retrieved successfully"
}
```

### Customers

#### GET /customers
Get a list of customers.

**Query Parameters:**
- `search` (optional): Search by name or email
- `distributor_id` (optional): Filter by distributor ID

**Response:**
```json
{
    "success": true,
    "data": {
        "customers": [
            {
                "id": 1,
                "name": "John Customer",
                "email": "customer@example.com",
                "phone": "+1234567890",
                "address": {
                    "street": "123 Main St",
                    "city": "New York",
                    "province": "NY",
                    "postal_code": "10001",
                    "country": "USA"
                },
                "distributor": {
                    "id": 1,
                    "company_name": "Golden Jewelers"
                },
                "created_at": "2025-07-24T01:00:00Z"
            }
        ]
    },
    "message": "Customers retrieved successfully"
}
```

#### POST /customers
Create a new customer.

**Request Body:**
```json
{
    "name": "Jane Customer",
    "email": "jane@example.com",
    "phone": "+1234567890",
    "street": "456 Oak Ave",
    "city": "Los Angeles",
    "province": "CA",
    "postal_code": "90210",
    "country": "USA"
}
```

### Order Templates

#### GET /order-templates
Get a list of order templates.

**Response:**
```json
{
    "success": true,
    "data": {
        "templates": [
            {
                "id": 1,
                "name": "Wedding Ring Template",
                "description": "Standard wedding ring configuration",
                "products": [
                    {
                        "product_id": 1,
                        "quantity": 1,
                        "price": 2500.00,
                        "metal": "Stainless",
                        "font": "Arial"
                    }
                ],
                "created_at": "2025-07-24T01:00:00Z"
            }
        ]
    },
    "message": "Order templates retrieved successfully"
}
```

#### POST /order-templates
Create a new order template.

**Request Body:**
```json
{
    "name": "Engagement Ring Template",
    "description": "Standard engagement ring configuration",
    "products": [
        {
            "product_id": 1,
            "quantity": 1,
            "price": 2500.00,
            "metal": "Stainless",
            "font": "Arial"
        }
    ]
}
```

### Statistics

#### GET /statistics
Get system statistics and analytics.

**Response:**
```json
{
    "success": true,
    "data": {
        "orders": {
            "total": 150,
            "pending_payment": 25,
            "approved": 30,
            "in_production": 45,
            "finishing": 20,
            "ready_for_delivery": 15,
            "delivered": 15
        },
        "revenue": {
            "total": 375000.00,
            "this_month": 45000.00,
            "last_month": 42000.00
        },
        "customers": {
            "total": 75,
            "new_this_month": 12
        },
        "products": {
            "total": 45,
            "active": 42
        }
    },
    "message": "Statistics retrieved successfully"
}
```

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Invalid input data |
| 401 | Unauthorized - Invalid or missing token |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Validation Error - Invalid request data |
| 500 | Internal Server Error - Server error |

## Rate Limiting

API requests are limited to:
- 60 requests per minute for authenticated users
- 10 requests per minute for unauthenticated users

## Webhooks

The API supports webhooks for real-time notifications. Configure webhooks to receive notifications for:

- Order status changes
- Payment status updates
- New order creation
- Customer registration

**Webhook Payload Example:**
```json
{
    "event": "order.status_changed",
    "data": {
        "order_id": 1,
        "order_number": "ORD-2025-001",
        "old_status": "pending_payment",
        "new_status": "approved",
        "timestamp": "2025-07-24T01:30:00Z"
    }
}
```

## SDKs and Libraries

### PHP
```bash
composer require brayne/jewelry-manager-api
```

### JavaScript/Node.js
```bash
npm install @brayne/jewelry-manager-api
```

### Python
```bash
pip install brayne-jewelry-manager-api
```

## Support

For API support and questions:
- Email: api@braynejewelry.com
- Documentation: https://docs.braynejewelry.com/api
- GitHub Issues: https://github.com/brayne/jewelry-manager/issues

---

**API Version:** v1.0  
**Last Updated:** July 24, 2025 