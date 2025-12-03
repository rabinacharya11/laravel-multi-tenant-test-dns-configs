# SSL/HTTPS Setup Guide for Multi-Tenant Application

This guide covers SSL certificate setup for both **wildcard subdomains** and **custom domains**.

---

## Option 1: Using Caddy (Recommended - Automatic SSL)

Caddy automatically handles SSL certificates for both wildcard subdomains and custom domains.

### Installation

```bash
# Install Caddy
sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list
sudo apt update
sudo apt install caddy
```

### Caddyfile Configuration

Create `/etc/caddy/Caddyfile`:

```caddy
# Wildcard subdomain for all tenants
*.petmelo.com {
    reverse_proxy localhost:8080
    encode gzip
    
    log {
        output file /var/log/caddy/access.log
    }
}

# Main domain
petmelo.com {
    reverse_proxy localhost:8080
    encode gzip
    
    log {
        output file /var/log/caddy/access.log
    }
}

# Custom domains will be handled automatically
# When a tenant adds a custom domain, Caddy will automatically
# request and renew SSL certificates for it
```

### Enable and Start Caddy

```bash
sudo systemctl enable caddy
sudo systemctl start caddy
sudo systemctl status caddy
```

### Configure Your Application

Update your Nginx to listen on port 8080 (or update Caddy to proxy to your current port).

**Benefits:**
- ✅ Automatic SSL for wildcard subdomains
- ✅ Automatic SSL for custom domains
- ✅ Auto-renewal
- ✅ HTTP/2 and HTTP/3 support
- ✅ Zero configuration needed

---

## Option 2: Using Certbot with Nginx (Manual Setup)

### Step 1: Install Certbot

```bash
sudo apt update
sudo apt install certbot python3-certbot-nginx -y
```

### Step 2: Get Wildcard Certificate for Subdomains

```bash
# Wildcard certificate for *.petmelo.com
sudo certbot certonly \
  --manual \
  --preferred-challenges dns \
  --server https://acme-v02.api.letsencrypt.org/directory \
  --agree-tos \
  -d '*.petmelo.com' \
  -d 'petmelo.com'
```

**Follow the prompts:**
1. Certbot will ask you to create a TXT record in your DNS
2. Create the TXT record: `_acme-challenge.petmelo.com`
3. Wait for DNS propagation (5-30 minutes)
4. Press Enter to continue

### Step 3: Configure Nginx for HTTPS

Update `/etc/nginx/sites-available/default`:

```nginx
# HTTP - Redirect to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name *.petmelo.com petmelo.com;
    return 301 https://$host$request_uri;
}

# HTTPS - Wildcard subdomain
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    
    server_name *.petmelo.com petmelo.com;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/petmelo.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/petmelo.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    root /var/www/html/public;
    index index.php index.html;
    
    # ... rest of your Nginx config
}
```

### Step 4: Reload Nginx

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Step 5: Auto-Renewal

```bash
# Test renewal
sudo certbot renew --dry-run

# Add cron job for auto-renewal
sudo crontab -e

# Add this line:
0 3 * * * /usr/bin/certbot renew --quiet --nginx
```

---

## Handling Custom Domains (tenant1.com)

### With Caddy (Automatic)

Caddy automatically handles SSL for custom domains! Just ensure:

1. The custom domain's DNS points to your server
2. Caddy is running
3. The domain is added via your API

Caddy will automatically:
- Detect the new domain
- Request SSL certificate
- Configure HTTPS
- Auto-renew

### With Certbot (Manual)

For each custom domain, run:

```bash
sudo certbot --nginx -d tenant1.com -d www.tenant1.com
```

**Automate for scale:**

Create a script `/usr/local/bin/add-custom-domain.sh`:

```bash
#!/bin/bash
DOMAIN=$1

if [ -z "$DOMAIN" ]; then
    echo "Usage: $0 <domain>"
    exit 1
fi

# Request certificate
certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos -m admin@petmelo.com

# Reload Nginx
nginx -s reload

echo "SSL certificate installed for $DOMAIN"
```

Make it executable:

```bash
sudo chmod +x /usr/local/bin/add-custom-domain.sh
```

Usage:

```bash
sudo /usr/local/bin/add-custom-domain.sh tenant1.com
```

---

## DNS Configuration Requirements

### For Wildcard Subdomains (*.petmelo.com)

**A Record:**
```
Type: A
Name: @
Value: YOUR_SERVER_IP
TTL: Auto
```

**Wildcard A Record:**
```
Type: A
Name: *
Value: YOUR_SERVER_IP
TTL: Auto
```

### For Custom Domains (tenant1.com)

**Tenant Instructions:**

```
Type: CNAME
Name: @ (or www)
Value: petmelo.com
TTL: Auto
```

Or if CNAME at root is not supported:

```
Type: A
Name: @
Value: YOUR_SERVER_IP
TTL: Auto
```

---

## Testing SSL

### Test Wildcard:

```bash
curl -I https://tenant1.petmelo.com
curl -I https://tenant2.petmelo.com
```

### Test Custom Domain:

```bash
curl -I https://tenant1.com
```

### Check SSL Grade:

Visit: https://www.ssllabs.com/ssltest/

---

## Monitoring & Troubleshooting

### Check Certificate Expiry:

```bash
# Caddy
curl https://your-domain.com | openssl x509 -noout -dates

# Certbot
sudo certbot certificates
```

### View Logs:

```bash
# Caddy
sudo journalctl -u caddy -f

# Certbot
sudo tail -f /var/log/letsencrypt/letsencrypt.log

# Nginx
sudo tail -f /var/log/nginx/error.log
```

---

## Recommended: Caddy Setup

**Why Caddy?**
- ✅ Zero configuration SSL
- ✅ Automatic HTTPS for custom domains
- ✅ Built-in HTTP/2 and HTTP/3
- ✅ Automatic renewal
- ✅ Scales to thousands of custom domains

**Migration from Nginx to Caddy:**

1. Install Caddy (see above)
2. Stop Nginx: `sudo systemctl stop nginx`
3. Update your app to run on port 8080
4. Start Caddy: `sudo systemctl start caddy`
5. Test: `curl -I https://tenant1.petmelo.com`

---

## Production Checklist

- [ ] Wildcard certificate installed for `*.petmelo.com`
- [ ] Main domain certificate for `petmelo.com`
- [ ] Auto-renewal configured
- [ ] HTTP to HTTPS redirect enabled
- [ ] Security headers configured
- [ ] Monitoring/alerting for certificate expiry
- [ ] Backup of SSL certificates
- [ ] DNS properly configured
- [ ] Test SSL with SSLLabs

---

## Support & Resources

- **Caddy Documentation:** https://caddyserver.com/docs/
- **Certbot Documentation:** https://certbot.eff.org/
- **Let's Encrypt Rate Limits:** https://letsencrypt.org/docs/rate-limits/
- **SSL Test:** https://www.ssllabs.com/ssltest/

---

**Note:** For production with thousands of tenants, **Caddy is strongly recommended** over Certbot due to its automatic handling of custom domain SSL certificates.
