# 🚀 API Documentation

Complete API reference for the Short URL Management System.

## Base URL
```
https://yourdomain.com/api.php
```

## Authentication

All API requests require Bearer token authentication.

### Headers
```
Authorization: Bearer {ADMIN_KEY}
Content-Type: application/json
```

### Getting Your API Key
The API key is defined in your `.env` file as `ADMIN_KEY`.

## Endpoints

### 1. Create Short URL

Create a new short URL.

**Endpoint**: `POST /api.php?action=create`

**Request Body**:
```json
{
  "target": "https://example.com/very/long/url",
  "code": "custom-code",
  "redirect_type": 302,
  "auto_generate": false
}
```

**Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| target | string | Yes | The full URL to shorten (must be HTTP/HTTPS) |
| code | string | No | Custom short code (3-50 chars, alphanumeric, hyphens, underscores) |
| redirect_type | integer | No | 301 (permanent) or 302 (temporary). Default: 302 |
| auto_generate | boolean | No | Auto-generate code if true. Default: false |

**Success Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "code": "custom-code",
    "target": "https://example.com/very/long/url",
    "short_url": "https://yourdomain.com/custom-code",
    "redirect_type": 302
  }
}
```

**Error Responses**:

400 Bad Request - Invalid input:
```json
{
  "success": false,
  "error": "Target URL is required"
}
```

409 Conflict - Code already exists:
```json
{
  "success": false,
  "error": "Code already exists"
}
```

**Example**:
```bash
curl -X POST "https://yourdomain.com/api.php?action=create" \
  -H "Authorization: Bearer your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "target": "https://example.com/long-url",
    "code": "example",
    "redirect_type": 302
  }'
```

---

### 2. List Short URLs

Get a list of all short URLs with optional filtering and sorting.

**Endpoint**: `GET /api.php?action=list`

**Query Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search | string | No | Search term to filter by code or target URL |
| order_by | string | No | Sort field: code, target, clicks, created_at, last_click_at. Default: created_at |
| order_dir | string | No | Sort direction: ASC or DESC. Default: DESC |

**Success Response** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "example",
      "target": "https://example.com/long-url",
      "short_url": "https://yourdomain.com/example",
      "redirect_type": 302,
      "clicks": 42,
      "last_click_at": "2025-10-08 12:30:00",
      "created_at": "2025-10-01 10:00:00",
      "updated_at": "2025-10-08 12:30:00"
    }
  ],
  "count": 1
}
```

**Example**:
```bash
# Get all URLs
curl "https://yourdomain.com/api.php?action=list" \
  -H "Authorization: Bearer your_api_key_here"

# Search and sort
curl "https://yourdomain.com/api.php?action=list&search=example&order_by=clicks&order_dir=DESC" \
  -H "Authorization: Bearer your_api_key_here"
```

---

### 3. Delete Short URL

Delete a short URL by its code.

**Endpoint**: `POST /api.php?action=delete`

**Request Body**:
```json
{
  "code": "example"
}
```

**Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| code | string | Yes | The short code to delete |

**Success Response** (200 OK):
```json
{
  "success": true,
  "message": "Short URL deleted successfully"
}
```

**Error Responses**:

400 Bad Request - Missing code:
```json
{
  "success": false,
  "error": "Code is required"
}
```

404 Not Found - Code doesn't exist:
```json
{
  "success": false,
  "error": "Short URL not found"
}
```

**Example**:
```bash
curl -X POST "https://yourdomain.com/api.php?action=delete" \
  -H "Authorization: Bearer your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "example"
  }'
```

---

### 4. Get Statistics

Get system-wide statistics.

**Endpoint**: `GET /api.php?action=stats`

**Success Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "total_urls": 150,
    "total_clicks": 5420,
    "total_click_records": 5420,
    "average_clicks_per_url": 36.13,
    "top_urls": [
      {
        "code": "popular",
        "target": "https://example.com/popular-page",
        "clicks": 1250
      }
    ]
  }
}
```

**Example**:
```bash
curl "https://yourdomain.com/api.php?action=stats" \
  -H "Authorization: Bearer your_api_key_here"
```

---

## Error Handling

All error responses follow this format:
```json
{
  "success": false,
  "error": "Error message description"
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Invalid or missing API key |
| 404 | Not Found - Resource doesn't exist |
| 409 | Conflict - Resource already exists |
| 500 | Internal Server Error |

---

## Rate Limiting

Currently, there is no rate limiting implemented. Consider implementing rate limiting in production:

```php
// Example rate limiting (add to api.php)
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$key = "api_rate_limit:" . $ip;
$requests = $redis->incr($key);
if ($requests == 1) {
    $redis->expire($key, 60); // 60 seconds window
}
if ($requests > 100) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Rate limit exceeded']);
    exit;
}
```

---

## Code Examples

### PHP
```php
<?php
$apiKey = 'your_api_key_here';
$baseUrl = 'https://yourdomain.com/api.php';

// Create short URL
$data = [
    'target' => 'https://example.com/long-url',
    'code' => 'example',
    'redirect_type' => 302
];

$ch = curl_init($baseUrl . '?action=create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
?>
```

### Python
```python
import requests

API_KEY = 'your_api_key_here'
BASE_URL = 'https://yourdomain.com/api.php'

headers = {
    'Authorization': f'Bearer {API_KEY}',
    'Content-Type': 'application/json'
}

# Create short URL
data = {
    'target': 'https://example.com/long-url',
    'code': 'example',
    'redirect_type': 302
}

response = requests.post(
    f'{BASE_URL}?action=create',
    headers=headers,
    json=data
)

print(response.json())
```

### JavaScript (Node.js)
```javascript
const axios = require('axios');

const API_KEY = 'your_api_key_here';
const BASE_URL = 'https://yourdomain.com/api.php';

const headers = {
    'Authorization': `Bearer ${API_KEY}`,
    'Content-Type': 'application/json'
};

// Create short URL
async function createShortUrl() {
    try {
        const response = await axios.post(
            `${BASE_URL}?action=create`,
            {
                target: 'https://example.com/long-url',
                code: 'example',
                redirect_type: 302
            },
            { headers }
        );
        console.log(response.data);
    } catch (error) {
        console.error(error.response.data);
    }
}

createShortUrl();
```

### cURL
```bash
# Create
curl -X POST "https://yourdomain.com/api.php?action=create" \
  -H "Authorization: Bearer your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{"target":"https://example.com","code":"test"}'

# List
curl "https://yourdomain.com/api.php?action=list" \
  -H "Authorization: Bearer your_api_key_here"

# Delete
curl -X POST "https://yourdomain.com/api.php?action=delete" \
  -H "Authorization: Bearer your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{"code":"test"}'

# Stats
curl "https://yourdomain.com/api.php?action=stats" \
  -H "Authorization: Bearer your_api_key_here"
```

---

## Webhook Integration

You can integrate the API with automation tools like Zapier, Make (Integromat), or n8n.

### Example: Zapier Integration
1. Create a new Zap
2. Choose trigger (e.g., "New Row in Google Sheets")
3. Add action: "Webhooks by Zapier"
4. Choose "POST"
5. URL: `https://yourdomain.com/api.php?action=create`
6. Headers:
   - `Authorization`: `Bearer your_api_key_here`
   - `Content-Type`: `application/json`
7. Data: Map your trigger fields to API parameters

---

## Best Practices

1. **Keep API Key Secret**: Never expose your API key in client-side code
2. **Use HTTPS**: Always use HTTPS for API requests
3. **Handle Errors**: Implement proper error handling in your code
4. **Validate Input**: Validate data before sending to API
5. **Cache Responses**: Cache list responses to reduce API calls
6. **Monitor Usage**: Track API usage for debugging and optimization

---

## Testing

### Test with Postman
1. Import this collection or create requests manually
2. Set environment variable for `API_KEY`
3. Test all endpoints

### Test with curl
```bash
# Set your API key
export API_KEY="your_api_key_here"

# Test create
curl -X POST "https://yourdomain.com/api.php?action=create" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"target":"https://example.com","auto_generate":true}'
```

---

## Support

For API issues or questions:
1. Check this documentation
2. Review error messages
3. Check server logs
4. Test with curl first

---

**API Version**: 1.0  
**Last Updated**: 2025-10-08
