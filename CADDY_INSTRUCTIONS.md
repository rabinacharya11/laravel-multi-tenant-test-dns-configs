# On-Demand SSL with Caddy & Laravel Tenancy

This guide explains how to configure your server to handle on-demand SSL certificates for your multi-tenant application using Caddy.

## 1. DNS Configuration

You need to point your domains to your server's public IP address.

### For your Central Domain (e.g., `saas.com`)
- **Type**: A Record
- **Name**: `@` (root)
- **Value**: `YOUR_SERVER_IP`

- **Type**: A Record
- **Name**: `*` (wildcard for subdomains like `foo.saas.com`)
- **Value**: `YOUR_SERVER_IP`

### For Tenant Custom Domains (e.g., `tenant-store.com`)
The tenant should configure their DNS to point to your server.
- **Type**: A Record
- **Name**: `@`
- **Value**: `YOUR_SERVER_IP`

OR

- **Type**: CNAME Record
- **Name**: `www` (or subdomain)
- **Value**: `saas.com` (your central domain)

## 2. Laravel Configuration

We have added a route `/caddy-check` in `routes/web.php`. This route is used by Caddy to verify if a domain is valid before issuing an SSL certificate.

- Ensure your `.env` file has the correct `CENTRAL_DOMAIN` set.
- Ensure PHP-FPM is running and listening on `127.0.0.1:9000`.

## 3. Caddy Configuration

A `Caddyfile` has been created in the root of your project. **IMPORTANT**: You must update the `root` directive in the `Caddyfile` to point to the absolute path of your project's `public` directory (e.g., `/var/www/html/public`).

### Installation
Follow the official Caddy installation instructions for your OS: [https://caddyserver.com/docs/install](https://caddyserver.com/docs/install)

### Running Caddy
1. **Stop any existing web server** (like Nginx or Apache) on ports 80/443.
2. **Start Caddy**:
   ```bash
   sudo caddy run --config Caddyfile
   ```
   Or run it in the background:
   ```bash
   sudo caddy start --config Caddyfile
   ```

### How it works
1. A user visits `tenant.custom-domain.com`.
2. Caddy receives the request.
3. Caddy checks if it has a certificate. If not, it pauses the request.
4. Caddy makes a background HTTP request to `http://127.0.0.1:8080/caddy-check?domain=tenant.custom-domain.com` (handled internally by Caddy via PHP-FPM).
5. Laravel checks the database.
   - If the domain exists (mapped to a tenant), it returns `200 OK`.
   - If not, it returns `404 Not Found`.
6. If `200 OK`, Caddy obtains an SSL certificate from Let's Encrypt (or ZeroSSL) and serves the request.
7. If `404`, Caddy denies the connection (preventing abuse).

## 4. Mapping Tenants to Domains

Your application is now set up to handle both subdomains and custom domains automatically.

### Subdomains (e.g., `tenant1.saas.com`)
1.  Ensure your central domain (e.g., `saas.com`) is configured in `.env` (`CENTRAL_DOMAIN=saas.com`).
2.  When creating a tenant, create a domain record for `tenant1.saas.com`.
3.  Ensure you have a Wildcard DNS record (`*.saas.com`) pointing to your server IP.

### Custom Domains (e.g., `client-site.com`)
1.  Create a domain record in your database for `client-site.com` linked to the tenant.
2.  Ask your client to point their domain's **A Record** to your server's IP address.
3.  Caddy will automatically provision SSL for `client-site.com` when the first visitor arrives, after verifying with Laravel that the domain exists in your DB.

### Handling `www`
If you want to support `www.client-site.com`, you must add `www.client-site.com` as a domain record for that tenant in your database. Caddy treats `www` as a separate domain.

## Troubleshooting

- **Caddy Logs**: Check Caddy logs if SSL issuance fails.
- **Laravel Logs**: Check `storage/logs/laravel.log` to see if the `/caddy-check` endpoint is being hit.
- **Firewall**: Ensure ports 80 and 443 are open on your server.
