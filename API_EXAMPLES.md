# API Usage Examples

This file contains example API requests for testing the multi-tenant application.

---

## Central Domain (Admin) APIs

### 1. List All Tenants

```bash
curl -X GET "http://localhost/api/tenants" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "tenants": [
    {
      "id": "uuid-here",
      "name": "Demo Tenant",
      "primary_domain": "demo.petmelo.com",
      "domains_count": 1,
      "created_at": "2025-12-03T00:00:00.000000Z"
    }
  ]
}
```

---

### 2. Create New Tenant

```bash
curl -X POST "http://localhost/api/tenants" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "My Business",
    "subdomain": "mybusiness"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Tenant created successfully",
  "tenant": {
    "id": "uuid-here",
    "name": "My Business",
    "subdomain": "mybusiness.petmelo.com",
    "url": "https://mybusiness.petmelo.com"
  }
}
```

---

### 3. Get Tenant Details

```bash
curl -X GET "http://localhost/api/tenants/{tenant_id}" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "tenant": {
    "id": "uuid-here",
    "name": "My Business",
    "domains": [
      {
        "domain": "mybusiness.petmelo.com",
        "type": "subdomain",
        "is_primary": true,
        "is_verified": true
      }
    ],
    "created_at": "2025-12-03T00:00:00.000000Z"
  }
}
```

---

## Tenant Domain APIs

All these endpoints are accessed from the tenant's domain (e.g., `mybusiness.petmelo.com`).

### 1. List Tenant's Domains

```bash
curl -X GET "http://mybusiness.petmelo.com/api/domains" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "domains": [
    {
      "id": 1,
      "domain": "mybusiness.petmelo.com",
      "type": "subdomain",
      "is_primary": true,
      "is_verified": true,
      "verified_at": "2025-12-03T00:00:00.000000Z",
      "created_at": "2025-12-03T00:00:00.000000Z"
    }
  ]
}
```

---

### 2. Add Custom Domain

```bash
curl -X POST "http://mybusiness.petmelo.com/api/domains" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "domain": "mybusiness.com",
    "is_primary": false
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Custom domain added successfully. Please verify DNS configuration.",
  "domain": {
    "id": 2,
    "domain": "mybusiness.com",
    "type": "custom",
    "is_verified": false,
    "verification_token": "abc123..."
  },
  "dns_instructions": {
    "domain": "mybusiness.com",
    "instructions": [
      {
        "step": 1,
        "title": "Go to your domain provider",
        "description": "Log in to your domain registrar..."
      }
    ]
  }
}
```

---

### 3. Verify Custom Domain DNS

```bash
curl -X POST "http://mybusiness.petmelo.com/api/domains/2/verify" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

**Success Response:**
```json
{
  "success": true,
  "message": "Domain verified and activated successfully!",
  "dns_info": {
    "verified": true,
    "type": "CNAME",
    "target": "petmelo.com"
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "DNS not properly configured. Please create a CNAME record pointing to petmelo.com"
}
```

---

### 4. Get DNS Instructions for Domain

```bash
curl -X GET "http://mybusiness.petmelo.com/api/domains/2/instructions" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "domain": "mybusiness.com",
  "is_verified": false,
  "instructions": {
    "domain": "mybusiness.com",
    "instructions": [
      {
        "step": 1,
        "title": "Go to your domain provider",
        "description": "Log in to your domain registrar (GoDaddy, Namecheap, Cloudflare, etc.)"
      },
      {
        "step": 2,
        "title": "Create CNAME record",
        "description": "Add a CNAME record with the following details:",
        "details": {
          "Type": "CNAME",
          "Name": "@",
          "Value/Target": "petmelo.com",
          "TTL": "Auto or 3600"
        }
      }
    ]
  }
}
```

---

### 5. Set Domain as Primary

```bash
curl -X POST "http://mybusiness.petmelo.com/api/domains/2/set-primary" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Domain set as primary successfully"
}
```

---

### 6. Delete Custom Domain

```bash
curl -X DELETE "http://mybusiness.petmelo.com/api/domains/2" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Domain deleted successfully"
}
```

**Error (if primary domain):**
```json
{
  "error": "Cannot delete primary domain. Please set another domain as primary first."
}
```

---

### 7. Get Tenant Stats

```bash
curl -X GET "http://mybusiness.petmelo.com/stats" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "tenant_id": "uuid-here",
  "tenant_domain": "mybusiness.petmelo.com",
  "users_count": 5,
  "database_connection": "mysql",
  "tenancy_initialized": true
}
```

---

### 8. Get Tenant Users

```bash
curl -X GET "http://mybusiness.petmelo.com/users" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "tenant_id": "uuid-here",
  "users": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "tenant_id": "uuid-here",
      "created_at": "2025-12-03T00:00:00.000000Z"
    }
  ]
}
```

---

## Testing Workflow

### Complete Custom Domain Setup Flow

1. **Create Tenant**
```bash
curl -X POST "http://localhost/api/tenants" \
  -H "Content-Type: application/json" \
  -d '{"name": "Test Business", "subdomain": "testbiz"}'
```

2. **Access Tenant Subdomain**
```bash
curl "http://testbiz.petmelo.com/stats"
```

3. **Add Custom Domain**
```bash
curl -X POST "http://testbiz.petmelo.com/api/domains" \
  -H "Content-Type: application/json" \
  -d '{"domain": "testbusiness.com", "is_primary": false}'
```

4. **Configure DNS** (do this in your domain provider)
- Create CNAME: `@` → `petmelo.com`

5. **Wait for DNS Propagation**
```bash
dig testbusiness.com CNAME
```

6. **Verify Domain**
```bash
curl -X POST "http://testbiz.petmelo.com/api/domains/2/verify" \
  -H "Content-Type: application/json"
```

7. **Set as Primary (Optional)**
```bash
curl -X POST "http://testbiz.petmelo.com/api/domains/2/set-primary" \
  -H "Content-Type: application/json"
```

8. **Access via Custom Domain**
```bash
curl "https://testbusiness.com/stats"
```

---

## Error Responses

### Validation Error
```json
{
  "success": false,
  "errors": [
    "Invalid domain format. Please enter a valid domain (e.g., example.com or www.example.com)"
  ]
}
```

### Domain Already Exists
```json
{
  "success": false,
  "errors": [
    "This domain is already registered to another tenant."
  ]
}
```

### DNS Verification Failed
```json
{
  "success": false,
  "message": "DNS not properly configured. Please create a CNAME record pointing to petmelo.com"
}
```

### Tenant Context Not Initialized
```json
{
  "error": "Tenant context not initialized"
}
```

---

## Notes

- Replace `localhost` with your actual domain in production
- Replace `petmelo.com` with your configured base domain
- All authenticated endpoints should include proper authentication headers (to be implemented)
- Domain verification may take 5-30 minutes for DNS propagation
- SSL certificates are automatically issued after DNS verification (with Caddy)
