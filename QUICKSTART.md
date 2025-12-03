# ⚡ Quick Start Guide

Get your multi-tenant application running in 5 minutes!

---

## Prerequisites

- PHP 8.2+ installed
- MySQL or PostgreSQL database
- Composer installed
- Docker (optional, but recommended)

---

## 🚀 Local Development Setup

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME="My Multi-Tenant App"
APP_URL=http://localhost
APP_DOMAIN=localhost

DB_DATABASE=multi_tenant
DB_USERNAME=root
DB_PASSWORD=your_password

CENTRAL_DOMAIN=localhost
```

### 3. Run Setup Script

```bash
chmod +x setup.sh
./setup.sh
```

This will:
- Run migrations
- Create sample tenant (optional)
- Create sample users

### 4. Start the Application

**Option A: PHP Built-in Server**
```bash
php artisan serve
```

**Option B: Docker**
```bash
docker-compose up -d
```

### 5. Access the Application

**Central Admin:**
```
http://localhost/admin
```

**Sample Tenant (if created):**
```
http://demo.localhost
```

---

## 🌐 Production Deployment

### 1. Server Requirements

- Ubuntu 20.04+ or similar
- Nginx
- PHP 8.2+ with extensions: mysql, redis, mbstring, xml, curl
- MySQL 8.0+ or PostgreSQL 13+
- Caddy (for automatic SSL)

### 2. Deploy Application

```bash
git clone your-repo.git /var/www/html
cd /var/www/html
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Configure DNS

**Your Base Domain (e.g., petmelo.com):**

1. **A Record:**
   - Name: `@`
   - Value: `YOUR_SERVER_IP`

2. **Wildcard A Record:**
   - Name: `*`
   - Value: `YOUR_SERVER_IP`

### 4. Set Up SSL with Caddy

```bash
# Install Caddy
sudo apt install caddy

# Create Caddyfile
sudo nano /etc/caddy/Caddyfile
```

Add:
```caddy
*.petmelo.com, petmelo.com {
    reverse_proxy localhost:8080
}
```

```bash
# Start Caddy
sudo systemctl enable caddy
sudo systemctl start caddy
```

### 5. Configure Nginx

```bash
sudo cp docker/nginx/default.conf /etc/nginx/sites-available/default
sudo nginx -t
sudo systemctl reload nginx
```

Make sure Nginx listens on port 8080 (since Caddy is on 80/443).

### 6. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html/storage
sudo chmod -R 755 /var/www/html/bootstrap/cache
```

---

## ✅ Verify Installation

### 1. Check Central Admin

```bash
curl http://your-domain.com/admin
```

Should show the admin dashboard.

### 2. Create Test Tenant

Via API:
```bash
curl -X POST "http://your-domain.com/api/tenants" \
  -H "Content-Type: application/json" \
  -d '{"name": "Test", "subdomain": "test"}'
```

### 3. Access Tenant

```bash
curl http://test.your-domain.com/stats
```

Should return tenant stats JSON.

### 4. Test Custom Domain

1. Add domain via tenant dashboard
2. Configure DNS: `CNAME @ → your-domain.com`
3. Wait for propagation
4. Verify via dashboard
5. Access: `https://custom-domain.com`

---

## 🔧 Common Issues

### Issue: "Tenant not found"

**Solution:**
- Check DNS is pointing correctly
- Verify domain exists in database
- Check `CENTRAL_DOMAIN` in `.env`

### Issue: SSL not working

**Solution:**
- Ensure Caddy is running: `sudo systemctl status caddy`
- Check Caddy logs: `sudo journalctl -u caddy -f`
- Verify DNS propagation: `dig your-domain.com`

### Issue: Database connection error

**Solution:**
- Check database credentials in `.env`
- Ensure database exists
- Run: `php artisan migrate`

### Issue: 404 on tenant routes

**Solution:**
- Clear cache: `php artisan cache:clear`
- Clear route cache: `php artisan route:clear`
- Check domain in database: `SELECT * FROM domains;`

---

## 📚 Next Steps

1. Read [MULTI_TENANT_README.md](MULTI_TENANT_README.md) for detailed documentation
2. Check [API_EXAMPLES.md](API_EXAMPLES.md) for API usage
3. Review [SSL_SETUP.md](SSL_SETUP.md) for SSL configuration
4. See [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) for architecture details

---

## 🆘 Getting Help

- **Documentation:** See README files
- **API Examples:** Check API_EXAMPLES.md
- **Logs:** `tail -f storage/logs/laravel.log`
- **Caddy Logs:** `sudo journalctl -u caddy -f`
- **Nginx Logs:** `sudo tail -f /var/log/nginx/error.log`

---

## 📞 Support

For issues and questions:
- Check documentation first
- Review error logs
- Create an issue on GitHub
- Contact: support@your-domain.com

---

**Happy Multi-Tenanting! 🎉**
