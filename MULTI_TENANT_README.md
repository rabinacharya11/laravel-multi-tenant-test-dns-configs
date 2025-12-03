# Multi-Tenant Laravel Application with Custom Domain Support

A scalable multi-tenant SaaS application built with Laravel and Stancl Tenancy. Supports both **wildcard subdomains** and **custom domains** with automatic SSL/HTTPS.

---

## 🚀 Features

- ✅ **Multi-Tenancy** - Single database with tenant_id scoping
- ✅ **Wildcard Subdomains** - `tenant1.petmelo.com`, `tenant2.petmelo.com`
- ✅ **Custom Domains** - Map `tenant1.com` to any tenant
- ✅ **Automatic DNS Verification** - Validates DNS before activation
- ✅ **Beautiful Admin UI** - Dashboard for domain management
- ✅ **SSL/HTTPS Ready** - Works with Caddy or Certbot
- ✅ **Scalable** - Designed for thousands of tenants

---

## 📋 Requirements

- PHP 8.2+
- MySQL 8.0+ or PostgreSQL 13+
- Composer
- Node.js & NPM (for frontend assets)
- Docker & Docker Compose (optional)

---

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/multi-tenant-app.git
cd multi-tenant-app
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your configuration:

```env
APP_NAME="Multi-Tenant SaaS"
APP_ENV=production
APP_URL=https://petmelo.com
APP_DOMAIN=petmelo.com
APP_SERVER_IP=YOUR_SERVER_IP

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_tenant
DB_USERNAME=root
DB_PASSWORD=your_password

CENTRAL_DOMAIN=petmelo.com
TENANT_DOMAIN=petmelo.com
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Seed Initial Data (Optional)

```bash
php artisan db:seed
```

---

## 🏃 Running the Application

### Development (Local)

```bash
php artisan serve
```

Visit: `http://localhost:8000`

### Production (Docker)

```bash
docker-compose up -d
```

---

## 🌐 DNS Configuration

### For Base Domain (petmelo.com)

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

This enables all subdomains (`*.petmelo.com`) to work automatically.

---

## 🔐 SSL/HTTPS Setup

### Option 1: Caddy (Recommended)

Caddy automatically handles SSL for both wildcard and custom domains.

**Install Caddy:**
```bash
sudo apt install caddy
```

**Configure Caddyfile** (`/etc/caddy/Caddyfile`):
```caddy
*.petmelo.com, petmelo.com {
    reverse_proxy localhost:8080
}
```

**Start Caddy:**
```bash
sudo systemctl enable caddy
sudo systemctl start caddy
```

✅ **Done!** SSL certificates are automatic.

### Option 2: Certbot with Nginx

See [SSL_SETUP.md](SSL_SETUP.md) for detailed instructions.

---

## 📊 Usage

### Central Admin Dashboard

Access the admin panel at your central domain:

```
https://petmelo.com/admin
```

**Features:**
- Create new tenants
- View all tenants
- Monitor domain usage

### Tenant Dashboard

Each tenant has their own dashboard:

```
https://tenant1.petmelo.com/dashboard
```

**Features:**
- View users
- Add custom domains
- Verify DNS configuration
- View DNS setup instructions
- Manage domains

---

## 🏢 Creating a Tenant

### Via Admin UI

1. Go to `https://petmelo.com/admin`
2. Click "Create New Tenant"
3. Enter tenant name and subdomain
4. Click "Create Tenant"

### Via API

```bash
curl -X POST https://petmelo.com/api/tenants \
  -H "Content-Type: application/json" \
  -d '{
    "name": "My Business",
    "subdomain": "mybusiness"
  }'
```

**Response:**
```json
{
  "success": true,
  "tenant": {
    "id": "uuid-here",
    "name": "My Business",
    "subdomain": "mybusiness.petmelo.com",
    "url": "https://mybusiness.petmelo.com"
  }
}
```

---

## 🌍 Adding Custom Domains

### 1. Tenant Configures DNS

Tenant creates a CNAME record:

```
Type: CNAME
Name: @ (or www)
Value: petmelo.com
TTL: Auto
```

### 2. Add Domain via Dashboard

1. Go to tenant dashboard: `https://tenant1.petmelo.com/dashboard`
2. Click "Domain Management" tab
3. Click "Add Custom Domain"
4. Enter domain: `tenant1.com`
5. Click "Add Domain"

### 3. Verify DNS

1. Wait for DNS propagation (5-30 minutes)
2. Click "Verify DNS" button
3. System checks DNS and activates domain
4. SSL certificate is automatically issued

### 4. Done!

The custom domain is now active with HTTPS:
```
https://tenant1.com
```

---

## 🔌 API Endpoints

### Central Domain (Admin)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tenants` | List all tenants |
| POST | `/api/tenants` | Create new tenant |
| GET | `/api/tenants/{id}` | Get tenant details |

### Tenant Domain

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/domains` | List tenant domains |
| POST | `/api/domains` | Add custom domain |
| POST | `/api/domains/{id}/verify` | Verify DNS |
| GET | `/api/domains/{id}/instructions` | Get DNS instructions |
| POST | `/api/domains/{id}/set-primary` | Set as primary domain |
| DELETE | `/api/domains/{id}` | Delete custom domain |

---

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── DomainController.php      # Domain management
│   │   └── TenantController.php      # Tenant management
│   ├── Models/
│   │   ├── Domain.php                # Domain model
│   │   ├── Tenant.php                # Tenant model
│   │   └── User.php                  # User model (tenant-scoped)
│   └── Services/
│       └── DomainService.php         # Domain validation & DNS verification
├── database/
│   └── migrations/
│       ├── *_create_tenants_table.php
│       ├── *_create_domains_table.php
│       └── *_add_verification_to_domains_table.php
├── resources/views/
│   ├── admin.blade.php               # Central admin UI
│   └── dashboard.blade.php           # Tenant dashboard UI
├── routes/
│   ├── web.php                       # Central routes
│   └── tenant.php                    # Tenant routes
└── config/
    └── tenancy.php                   # Tenancy configuration
```

---

## 🧪 Testing

### Test Subdomain

```bash
curl https://tenant1.petmelo.com/stats
```

### Test Custom Domain

```bash
curl https://tenant1.com/stats
```

### Verify DNS

```bash
dig tenant1.com CNAME
dig tenant1.com A
```

---

## 🔧 Troubleshooting

### Domain Not Working

1. **Check DNS propagation:**
   ```bash
   dig yourdomain.com
   ```

2. **Verify CNAME/A record:**
   ```bash
   dig yourdomain.com CNAME
   ```

3. **Check domain verification status:**
   - Go to tenant dashboard
   - View domain in "Domain Management" tab
   - Click "Verify DNS"

### SSL Not Working

1. **Check Caddy logs:**
   ```bash
   sudo journalctl -u caddy -f
   ```

2. **Verify certificate:**
   ```bash
   curl -I https://yourdomain.com
   ```

3. **Test SSL:**
   https://www.ssllabs.com/ssltest/

### Database Issues

```bash
# Reset and migrate
php artisan migrate:fresh

# Run seeders
php artisan db:seed
```

---

## 📈 Scaling

### For Thousands of Tenants

1. **Use Caddy** for automatic SSL (not Certbot)
2. **Enable Redis** for caching
3. **Use Queue Workers** for background jobs
4. **Optimize Database** with indexes
5. **Use CDN** for static assets

### Performance Tips

- Enable **OPcache** in PHP
- Use **Redis** for session/cache
- Enable **database query caching**
- Use **eager loading** for relationships
- Implement **rate limiting**

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🆘 Support

For issues and questions:
- Create an [Issue](https://github.com/yourusername/multi-tenant-app/issues)
- Email: support@petmelo.com

---

## 🎯 Roadmap

- [ ] Email verification for tenants
- [ ] Billing & subscription management
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] API rate limiting per tenant
- [ ] Webhooks for domain events
- [ ] White-label customization

---

**Built with ❤️ using Laravel & Stancl Tenancy**
