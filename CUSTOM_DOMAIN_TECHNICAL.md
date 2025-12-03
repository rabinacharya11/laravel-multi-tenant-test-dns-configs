# Custom Domain Handling - Technical Summary

## ✅ Domain Fields in Database

The `domains` table has all necessary fields for custom domain support:

### Fields Added:
```php
- domain (string, unique)           // The actual domain name
- tenant_id (string, FK)            // Links to tenant
- is_primary (boolean)              // Is this the primary domain?
- is_verified (boolean)             // Has DNS been verified?
- verification_token (string)       // Token for verification
- verified_at (timestamp)           // When was it verified
- type (string)                     // 'subdomain' or 'custom'
- created_at (timestamp)
- updated_at (timestamp)
```

### Migration Location:
- `database/migrations/2019_09_15_000020_create_domains_table.php` - Base table
- `database/migrations/2025_12_03_000001_add_verification_to_domains_table.php` - Custom domain fields

---

## ✅ Middleware Configuration

### Current Middleware Stack (Tenant Routes):

```php
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,      // ← Stancl: Finds tenant by domain
    PreventAccessFromCentralDomains::class, // ← Stancl: Blocks central domains
    EnsureDomainIsVerified::class,         // ← Custom: Blocks unverified custom domains
])->group(function () {
    // All tenant routes
});
```

### How It Works:

1. **InitializeTenancyByDomain** (Stancl Package)
   - Looks up the request domain in `domains` table
   - Finds the associated tenant
   - Initializes tenant context
   - **Works automatically for BOTH subdomains AND custom domains**

2. **PreventAccessFromCentralDomains** (Stancl Package)
   - Prevents tenants from being accessed via central domains
   - E.g., blocks `localhost` from showing tenant data

3. **EnsureDomainIsVerified** (Our Custom Middleware)
   - Checks if domain is verified
   - If custom domain is NOT verified → Shows error page
   - If subdomain or verified custom domain → Allows access

---

## ✅ Custom Domain Flow

### Example: Tenant adds `mybusiness.com`

#### Step 1: Domain Added (Unverified)
```
POST /api/domains
{
  "domain": "mybusiness.com",
  "is_primary": false
}

Database:
- domain: "mybusiness.com"
- type: "custom"
- is_verified: false ← Not accessible yet!
- verification_token: "abc123..."
```

#### Step 2: Tenant Configures DNS
Tenant creates CNAME:
```
mybusiness.com → CNAME → petmelo.com
```

#### Step 3: DNS Propagation
Waits 5-30 minutes for DNS to propagate globally.

#### Step 4: Verification
```
POST /api/domains/2/verify

System checks:
- DNS CNAME record exists?
- Points to correct target?

If yes:
- is_verified = true
- verified_at = now()
```

#### Step 5: Domain Active
```
Request to: https://mybusiness.com

1. InitializeTenancyByDomain:
   - Looks up "mybusiness.com" in domains table
   - Finds tenant_id
   - Initializes tenant context

2. EnsureDomainIsVerified:
   - Checks is_verified = true ✓
   - Allows request

3. Route handles request with tenant context
```

---

## ✅ Verification Process

### DNS Verification (`DomainService::verifyDNS()`)

```php
// Checks CNAME record
$records = dns_get_record($domain, DNS_CNAME);
// Looks for: mybusiness.com → petmelo.com

// Or checks A record
$records = dns_get_record($domain, DNS_A);
// Looks for: mybusiness.com → SERVER_IP
```

### Verification Endpoint

```
POST /api/domains/{id}/verify

Response if successful:
{
  "success": true,
  "message": "Domain verified and activated!",
  "dns_info": {
    "verified": true,
    "type": "CNAME",
    "target": "petmelo.com"
  }
}

Response if failed:
{
  "success": false,
  "message": "DNS not properly configured..."
}
```

---

## ✅ Security Features

### 1. Only Verified Domains Work
- Unverified custom domains show error page
- Prevents domain hijacking
- Ensures DNS ownership

### 2. Domain Validation
```php
DomainService::validateDomain()
- Checks format (regex)
- Prevents reserved domains
- Checks for duplicates
- Validates not subdomain of base domain
```

### 3. Type Distinction
```php
type: 'subdomain'  → Auto-verified (you control DNS)
type: 'custom'     → Must verify DNS (tenant controls DNS)
```

---

## 📊 Database Schema

### domains table
```sql
CREATE TABLE domains (
    id INT PRIMARY KEY AUTO_INCREMENT,
    domain VARCHAR(255) UNIQUE,
    tenant_id VARCHAR(255),
    is_primary BOOLEAN DEFAULT false,
    is_verified BOOLEAN DEFAULT true,
    verification_token VARCHAR(64),
    verified_at TIMESTAMP,
    type VARCHAR(20) DEFAULT 'subdomain',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    INDEX idx_verified_type (is_verified, type)
);
```

### Example Data:
```sql
-- Subdomain (auto-verified)
INSERT INTO domains VALUES (
    1, 'tenant1.petmelo.com', 'uuid-1',
    true, true, NULL, NOW(), 'subdomain', NOW(), NOW()
);

-- Custom domain (verified)
INSERT INTO domains VALUES (
    2, 'tenant1.com', 'uuid-1',
    false, true, 'abc123', NOW(), 'custom', NOW(), NOW()
);

-- Custom domain (unverified - won't work!)
INSERT INTO domains VALUES (
    3, 'pending.com', 'uuid-1',
    false, false, 'xyz789', NULL, 'custom', NOW(), NOW()
);
```

---

## 🔄 Request Flow Diagram

```
User Request: https://mybusiness.com
         ↓
    [DNS Resolution]
         ↓
    [SSL/TLS - Caddy]
         ↓
    [Nginx → PHP-FPM]
         ↓
    [Laravel Router]
         ↓
┌────────────────────────────────────┐
│ InitializeTenancyByDomain          │
│ - SELECT * FROM domains            │
│   WHERE domain = 'mybusiness.com'  │
│ - Found: tenant_id = 'uuid-1'      │
│ - Set tenant context               │
└────────────────────────────────────┘
         ↓
┌────────────────────────────────────┐
│ PreventAccessFromCentralDomains    │
│ - Check if domain is central       │
│ - No → Continue                    │
└────────────────────────────────────┘
         ↓
┌────────────────────────────────────┐
│ EnsureDomainIsVerified             │
│ - Check is_verified = true ✓       │
│ - Check type = 'custom' ✓          │
│ - Both OK → Continue               │
└────────────────────────────────────┘
         ↓
    [Controller Action]
         ↓
    [Response to User]
```

---

## ✅ Key Features Implemented

1. **Automatic Domain Detection**
   - Stancl middleware handles lookup
   - Works for subdomains AND custom domains
   - No manual routing needed

2. **DNS Verification**
   - Real-time CNAME/A record checking
   - Prevents unauthorized domains
   - Activation after verification

3. **Type Tracking**
   - `subdomain`: Auto-verified, owned by platform
   - `custom`: Requires verification, owned by tenant

4. **Primary Domain Support**
   - Mark any verified domain as primary
   - Redirect logic can use primary domain

5. **Security**
   - Unverified custom domains blocked
   - Domain format validation
   - Duplicate prevention
   - Reserved domain protection

---

## 🎯 Answer to Your Questions

### Q: "Did you update the middleware to redirect from custom domain?"

**A:** The middleware doesn't redirect - it **identifies the tenant by domain**. Here's how:

- `InitializeTenancyByDomain` looks up ANY domain (subdomain OR custom) in the `domains` table
- If found, it initializes that tenant's context
- The request is served with the correct tenant's data
- **No redirect needed** - the custom domain works directly!

### Q: "Do you have fields in the domains as custom domain?"

**A:** Yes! The `domains` table has:

```php
type: 'subdomain' | 'custom'  // Distinguishes domain type
is_verified: boolean          // Verification status
verification_token: string    // For DNS verification
verified_at: timestamp        // When verified
is_primary: boolean          // Primary domain flag
```

---

## 🚀 What Happens in Practice

### Scenario: User visits `mybusiness.com`

1. DNS resolves to your server
2. Nginx forwards to Laravel
3. `InitializeTenancyByDomain` runs:
   ```sql
   SELECT * FROM domains WHERE domain = 'mybusiness.com'
   -- Returns: tenant_id = 'xyz'
   ```
4. `EnsureDomainIsVerified` checks:
   ```php
   if (!$domain->is_verified && $domain->type === 'custom') {
       return error_page(); // ❌ Blocked
   }
   ```
5. If verified: Request proceeds with tenant 'xyz' context
6. Response served from tenant 'xyz' data

**No redirect. Direct access. Seamless.**

---

## 📝 Files Involved

1. **Middleware:**
   - `app/Http/Middleware/EnsureDomainIsVerified.php` (NEW)
   - Stancl's `InitializeTenancyByDomain` (package)

2. **Models:**
   - `app/Models/Domain.php` - Enhanced with type tracking
   - `app/Models/Tenant.php` - Domain management methods

3. **Services:**
   - `app/Services/DomainService.php` - DNS verification

4. **Routes:**
   - `routes/tenant.php` - Uses middleware stack

5. **Migrations:**
   - `*_create_domains_table.php`
   - `*_add_verification_to_domains_table.php`

---

**Summary: Custom domains work seamlessly through Stancl's domain lookup + our verification layer. No redirects needed!** ✅
