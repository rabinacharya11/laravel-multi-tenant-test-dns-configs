# 📖 Tenant Instructions for Custom Domain Setup

**Share this guide with your tenants to help them set up custom domains.**

---

## Overview

Your tenant account includes a default subdomain (e.g., `yourname.petmelo.com`). If you'd like to use your own domain name instead (e.g., `yourcompany.com`), follow the steps below.

---

## 📋 What You'll Need

- ✅ Your own domain name registered with a domain provider
- ✅ Access to your domain's DNS settings
- ✅ 5-30 minutes for DNS changes to take effect

---

## 🚀 Step-by-Step Setup

### Step 1: Log in to Your Tenant Dashboard

Visit your tenant dashboard:
```
https://yourname.petmelo.com/dashboard
```

### Step 2: Navigate to Domain Management

Click on the **"Domain Management"** tab in the dashboard navigation.

You'll see:
- Your current subdomain (already active)
- A button to add a custom domain

### Step 3: Add Your Custom Domain

1. Click **"Add Custom Domain"** button
2. Enter your domain name (e.g., `yourcompany.com` or `www.yourcompany.com`)
   - ⚠️ **Important:** Do NOT include `http://` or `https://`
3. Choose whether to make it your primary domain (optional)
4. Click **"Add Domain"**

You'll receive:
- ✅ Confirmation that domain was added
- 📋 DNS instructions
- 🔑 A verification token (saved automatically)

### Step 4: Configure Your DNS

Now you need to point your domain to our platform.

**Login to your domain provider** (where you bought your domain):
- GoDaddy
- Namecheap  
- Cloudflare
- Google Domains
- etc.

**Find the DNS Management section** (it might be called DNS Settings, DNS Zone Editor, or Manage DNS).

**Add a CNAME Record:**

| Setting | Value |
|---------|-------|
| **Type** | CNAME |
| **Name** | @ (for root) or www (for subdomain) |
| **Target/Value** | petmelo.com |
| **TTL** | Auto (or 3600) |

**Example Screenshots by Provider:**

#### GoDaddy:
```
DNS Management → Add → CNAME
Name: @
Points to: petmelo.com
TTL: 1 hour
```

#### Namecheap:
```
Advanced DNS → Add New Record → CNAME
Host: @
Value: petmelo.com
TTL: Automatic
```

#### Cloudflare:
```
DNS → Add Record → CNAME
Name: @
Target: petmelo.com
Proxy status: DNS only (gray cloud)
TTL: Auto
```

**Alternative: Use A Record** (if CNAME at root is not supported):
```
Type: A
Name: @
Value: [Contact support for IP address]
TTL: Auto
```

### Step 5: Wait for DNS Propagation

DNS changes take time to spread across the internet:
- **Minimum:** 5 minutes
- **Average:** 30 minutes  
- **Maximum:** 24 hours

**Check DNS Status:**

Open a terminal/command prompt and run:
```bash
# Windows
nslookup yourcompany.com

# Mac/Linux
dig yourcompany.com
```

You should see it resolving to `petmelo.com` or our server IP.

**Online Tools:**
- https://www.whatsmydns.net/
- https://dnschecker.org/

### Step 6: Verify Your Domain

Once DNS has propagated:

1. Return to your **Domain Management** tab
2. Find your custom domain in the list
3. Click **"Verify DNS"** button

**If successful:**
- ✅ Domain will be marked as "Verified"
- ✅ SSL certificate will be issued automatically
- ✅ Your site will be accessible via HTTPS

**If unsuccessful:**
- ❌ Check DNS configuration
- ❌ Wait longer for propagation
- ❌ Try verifying again

### Step 7: Set as Primary (Optional)

If you want your custom domain to be your main domain:

1. Click **"Set as Primary"** next to your custom domain
2. Confirm the action

Now visitors can access your site at:
```
https://yourcompany.com
```

---

## ✅ Verification Checklist

- [ ] Domain added in dashboard
- [ ] CNAME record created at domain provider
- [ ] DNS propagation completed (check with dig/nslookup)
- [ ] Domain verified in dashboard
- [ ] SSL certificate issued (automatic)
- [ ] Site accessible via custom domain with HTTPS

---

## 🎯 Quick Reference

### Your Domains

**Subdomain (always works):**
- `https://yourname.petmelo.com`

**Custom Domain (after setup):**
- `https://yourcompany.com`

### DNS Configuration

**CNAME Record:**
```
@ → petmelo.com
```

**Or A Record:**
```
@ → [Server IP from support]
```

---

## ❓ Frequently Asked Questions

### Q: Can I use multiple custom domains?
**A:** Yes! You can add as many custom domains as you need. Just repeat the process for each domain.

### Q: Can I use a subdomain like `app.mycompany.com`?
**A:** Yes! Just use `app` as the Name/Host in your CNAME record instead of `@`.

### Q: Do I need to pay extra for custom domains?
**A:** No, custom domain mapping is included in your plan.

### Q: What about SSL/HTTPS?
**A:** SSL certificates are issued automatically for all verified domains. No action needed!

### Q: Can I delete a custom domain?
**A:** Yes, but you cannot delete your primary domain. Set another domain as primary first.

### Q: My domain isn't verifying. What should I do?
**A:** 
1. Double-check your DNS settings
2. Wait up to 24 hours for propagation
3. Try the verification again
4. Contact support if still not working

### Q: Will my old subdomain stop working?
**A:** No! Your subdomain will continue to work even after adding custom domains.

### Q: Can I change my primary domain?
**A:** Yes! Go to Domain Management and click "Set as Primary" on any verified domain.

---

## 🆘 Need Help?

### Check DNS Propagation
```bash
# Check if your domain resolves
dig yourcompany.com

# Check CNAME record
dig yourcompany.com CNAME
```

### View DNS Instructions Again
1. Go to Domain Management tab
2. Click "View Instructions" next to your domain

### Contact Support
- **Dashboard:** Click "DNS Setup Guide" tab for detailed instructions
- **Email:** support@petmelo.com
- **Documentation:** Available in your dashboard

---

## 📊 Example: Complete Setup

**Scenario:** You own `mybakery.com` and want to use it for your tenant.

### 1. Current State
```
✅ Subdomain: mybakery.petmelo.com (works)
❌ Custom: mybakery.com (not configured)
```

### 2. Add Domain
- Dashboard → Domain Management → Add Custom Domain
- Enter: `mybakery.com`
- Click Add

### 3. Configure DNS at Your Registrar
```
Type: CNAME
Name: @
Value: petmelo.com
```

### 4. Wait & Verify
- Wait 30 minutes
- Return to dashboard
- Click "Verify DNS"

### 5. Result
```
✅ Subdomain: mybakery.petmelo.com (works)
✅ Custom: mybakery.com (works with HTTPS!)
```

---

## 🎉 Success!

Your custom domain is now live with automatic HTTPS! Your customers can access your site at your branded domain name.

**Share your new domain:**
```
🌐 Visit us at: https://yourcompany.com
```

---

**For more help, check the DNS Instructions tab in your dashboard or contact support.**
