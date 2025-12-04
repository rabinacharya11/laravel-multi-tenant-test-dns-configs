# DNS Configuration Guide

To make your multi-tenant application work correctly with subdomains and custom domains, you need to configure your DNS records properly.

## 1. Server IP Address
First, find your server's public IP address. We will refer to this as `YOUR_SERVER_IP`.

## 2. Main Domain Configuration (e.g., `petmelo.com`)

You need two **A Records** for your main domain to handle the root domain and all subdomains (wildcard).

| Type | Name | Value | TTL | Purpose |
|------|------|-------|-----|---------|
| A | `@` | `YOUR_SERVER_IP` | Auto | Points `petmelo.com` to your server |
| A | `*` | `YOUR_SERVER_IP` | Auto | Points `anything.petmelo.com` to your server |

**Why the wildcard (`*`)?**
This allows any subdomain (e.g., `tenant1.petmelo.com`, `shop.petmelo.com`) to reach your server without you needing to add a new DNS record for every new tenant.

## 3. Custom Domain Configuration (e.g., `client-shop.com`)

When a tenant wants to use their own domain name instead of a subdomain, they need to point their domain to your server.

### Option A: Root Domain (e.g., `client-shop.com`)
If they want to use the root domain:

| Type | Name | Value | TTL |
|------|------|-------|-----|
| A | `@` | `YOUR_SERVER_IP` | Auto |

### Option B: Subdomain (e.g., `store.client-brand.com`)
If they want to use a subdomain:

| Type | Name | Value | TTL |
|------|------|-------|-----|
| CNAME | `store` | `petmelo.com` | Auto |

*Note: Using a CNAME is often better for subdomains as it automatically follows IP changes if you move your server.*

## 4. Verification

After updating DNS records, it may take a few minutes to propagate. You can verify them using the `dig` command or an online tool like [whatsmydns.net](https://whatsmydns.net).

```bash
# Check main domain
dig +short petmelo.com

# Check wildcard (try any random subdomain)
dig +short random123.petmelo.com

# Check custom domain
dig +short client-shop.com
```

All of these should return `YOUR_SERVER_IP`.
