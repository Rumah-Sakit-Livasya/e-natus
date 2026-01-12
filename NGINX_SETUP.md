# ============================================

# NGINX SETUP GUIDE FOR NATUS.ID

# ============================================

## Prerequisites

1. **Install Nginx with headers-more module:**

```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install nginx-extras

# Verify installation
nginx -V 2>&1 | grep -o with-http_headers_more_module
```

2. **Install Certbot for SSL:**

```bash
sudo apt-get install certbot python3-certbot-nginx
```

## Installation Steps

### 1. Copy Configuration File

```bash
# Copy the nginx-natus.conf to Nginx sites-available
sudo cp nginx-natus.conf /etc/nginx/sites-available/natus.id

# Create symbolic link to sites-enabled
sudo ln -s /etc/nginx/sites-available/natus.id /etc/nginx/sites-enabled/

# Remove default site (optional)
sudo rm /etc/nginx/sites-enabled/default
```

### 2. Adjust Configuration

Edit the configuration file:

```bash
sudo nano /etc/nginx/sites-available/natus.id
```

**Update these values:**

```nginx
# Line 16: Your domain
server_name natus.id www.natus.id;

# Line 18: Your project path
root /var/www/e-natus/public;

# Line 25-27: SSL certificate paths (will be auto-configured by Certbot)
ssl_certificate /etc/letsencrypt/live/natus.id/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/natus.id/privkey.pem;

# Line 119: PHP version (check with: php -v)
fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
```

### 3. Obtain SSL Certificate

```bash
# Get SSL certificate from Let's Encrypt
sudo certbot --nginx -d natus.id -d www.natus.id

# Follow the prompts:
# - Enter email address
# - Agree to terms
# - Choose redirect option (2)

# Test auto-renewal
sudo certbot renew --dry-run
```

### 4. Test Nginx Configuration

```bash
# Test configuration syntax
sudo nginx -t

# Expected output:
# nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
# nginx: configuration file /etc/nginx/nginx.conf test is successful
```

### 5. Restart Nginx

```bash
# Restart Nginx to apply changes
sudo systemctl restart nginx

# Check status
sudo systemctl status nginx

# Enable auto-start on boot
sudo systemctl enable nginx
```

## Verification

### 1. Check Security Headers

```bash
curl -I https://natus.id/ | grep -E "(Strict-Transport|Content-Security|X-Frame|X-Content)"
```

**Expected output:**

```
Strict-Transport-Security: max-age=63072000; includeSubDomains; preload
Content-Security-Policy: default-src 'self'; ...
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
```

### 2. Test HTTPS Redirect

```bash
curl -I http://natus.id/
```

**Expected output:**

```
HTTP/1.1 301 Moved Permanently
Location: https://natus.id/
```

### 3. Test Bot Blocking

```bash
curl -I -A "AhrefsBot" https://natus.id/
```

**Expected output:**

```
HTTP/1.1 403 Forbidden
```

### 4. Test Rate Limiting

```bash
# Send 125 requests rapidly
for i in {1..125}; do curl -I https://natus.id/ 2>&1 | grep "HTTP"; done
```

**Expected:** After 120 requests, should see `HTTP/1.1 429 Too Many Requests`

### 5. Online Security Scanners

-   **SSL Labs:** https://www.ssllabs.com/ssltest/analyze.html?d=natus.id

    -   Target: **A+** rating

-   **Security Headers:** https://securityheaders.com/?q=natus.id
    -   Target: **A+** rating

## Troubleshooting

### Issue: "headers-more module not found"

```bash
# Install nginx-extras
sudo apt-get install nginx-extras

# Or compile Nginx with module
# https://github.com/openresty/headers-more-nginx-module
```

### Issue: "Permission denied" for PHP socket

```bash
# Check PHP-FPM status
sudo systemctl status php8.3-fpm

# Adjust socket path in nginx config
# Find correct path:
ls -la /var/run/php/
```

### Issue: SSL certificate errors

```bash
# Re-run Certbot
sudo certbot --nginx -d natus.id -d www.natus.id --force-renewal

# Check certificate expiry
sudo certbot certificates
```

### Issue: Rate limiting too strict

```bash
# Edit nginx config
sudo nano /etc/nginx/sites-available/natus.id

# Adjust rate limits (line 78-80):
limit_req_zone $binary_remote_addr zone=general:10m rate=240r/m;  # Increase from 120

# Reload Nginx
sudo nginx -s reload
```

## Performance Optimization

### 1. Enable Gzip Compression

Add to `/etc/nginx/nginx.conf`:

```nginx
http {
    # Gzip Settings
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/rss+xml font/truetype font/opentype application/vnd.ms-fontobject image/svg+xml;
}
```

### 2. Increase Worker Connections

Edit `/etc/nginx/nginx.conf`:

```nginx
events {
    worker_connections 2048;  # Increase from default 768
}
```

### 3. Enable HTTP/2

Already enabled in config (line 14):

```nginx
listen 443 ssl http2;
```

## Monitoring

### 1. Check Access Logs

```bash
# Real-time access log
sudo tail -f /var/log/nginx/natus-access.log

# Check blocked bots
sudo tail -f /var/log/nginx/natus-blocked.log

# Error log
sudo tail -f /var/log/nginx/natus-error.log
```

### 2. Monitor Rate Limiting

```bash
# Check rate limit violations
sudo grep "limiting requests" /var/log/nginx/natus-error.log
```

### 3. SSL Certificate Expiry

```bash
# Check certificate expiry date
sudo certbot certificates

# Auto-renewal is configured via cron
sudo systemctl status certbot.timer
```

## Maintenance

### Update SSL Certificate

```bash
# Certbot auto-renews, but you can force renewal:
sudo certbot renew --force-renewal

# Reload Nginx after renewal
sudo systemctl reload nginx
```

### Update Nginx Configuration

```bash
# Edit config
sudo nano /etc/nginx/sites-available/natus.id

# Test config
sudo nginx -t

# Reload (no downtime)
sudo nginx -s reload

# Or restart (brief downtime)
sudo systemctl restart nginx
```

### Backup Configuration

```bash
# Backup current config
sudo cp /etc/nginx/sites-available/natus.id /etc/nginx/sites-available/natus.id.backup.$(date +%Y%m%d)

# List backups
ls -la /etc/nginx/sites-available/natus.id.backup.*
```

## Security Checklist

-   [x] HTTPS enabled with valid SSL certificate
-   [x] HTTP to HTTPS redirect configured
-   [x] HSTS header with 2-year max-age
-   [x] Content Security Policy (CSP) configured
-   [x] Server version information hidden
-   [x] Bot protection rules active
-   [x] Rate limiting configured
-   [x] Sensitive files blocked (.env, .git, etc.)
-   [x] Admin/API paths protected
-   [x] OCSP stapling enabled
-   [x] Modern TLS protocols only (1.2, 1.3)
-   [x] Strong cipher suites configured

## Additional Resources

-   **Nginx Documentation:** https://nginx.org/en/docs/
-   **Mozilla SSL Config Generator:** https://ssl-config.mozilla.org/
-   **Let's Encrypt Docs:** https://letsencrypt.org/docs/
-   **Security Headers Guide:** https://securityheaders.com/

## Support

If you encounter issues:

1. Check Nginx error log: `sudo tail -f /var/log/nginx/error.log`
2. Test configuration: `sudo nginx -t`
3. Check PHP-FPM: `sudo systemctl status php8.3-fpm`
4. Verify SSL: `sudo certbot certificates`

---

**Last Updated:** 2026-01-12
**Nginx Version:** 1.18+
**PHP Version:** 8.3+
