# Server Configuration Guide

Complete guide for configuring web server security settings to complement the application-level security headers.

## Overview

This application implements comprehensive security headers at both the application level (via `SecurityHeadersMiddleware`) and server level (via `.htaccess` for Apache or Nginx configuration). This guide helps you configure your web server for optimal security.

## Apache Configuration

### Option 1: Using .htaccess (Recommended for Shared Hosting)

The `.htaccess` file in the `public/` directory is already configured with comprehensive security settings. No additional configuration is needed if you're using Apache with `.htaccess` support enabled.

**Verify .htaccess is working:**

```bash
# Check if mod_rewrite is enabled
apache2ctl -M | grep rewrite

# Check if mod_headers is enabled
apache2ctl -M | grep headers
```

If these modules are not enabled, enable them:

```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

### Option 2: VirtualHost Configuration (Recommended for VPS/Dedicated Servers)

For better performance, move the `.htaccess` rules to your VirtualHost configuration:

**Location:** `/etc/apache2/sites-available/your-site.conf`

```apache
<VirtualHost *:443>
    ServerName natus.id
    ServerAlias www.natus.id
    DocumentRoot /var/www/e-natus/public

    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/privkey.pem
    SSLCertificateChainFile /path/to/chain.pem

    # Modern SSL/TLS Configuration
    SSLProtocol -all +TLSv1.2 +TLSv1.3
    SSLCipherSuite ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384
    SSLHonorCipherOrder off
    SSLSessionTickets off

    # Hide Server Information
    ServerSignature Off
    ServerTokens Prod

    # Security Headers
    <IfModule mod_headers.c>
        # Remove identifying headers
        Header unset Server
        Header unset X-Powered-By
        Header always unset X-Powered-By

        # Core security headers
        Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
        Header always set X-Frame-Options "SAMEORIGIN"
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-XSS-Protection "1; mode=block"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
        Header always set X-Download-Options "noopen"
        Header always set X-Permitted-Cross-Domain-Policies "none"

        # Bot protection for sensitive paths
        Header always set X-Robots-Tag "noindex, nofollow, noarchive" "expr=%{REQUEST_URI} =~ m#^/(api|admin|login)#"

        # Cache control for sensitive pages
        Header always set Cache-Control "no-store, no-cache, must-revalidate, private" "expr=%{REQUEST_URI} =~ m#^/(login|admin|api)#"
    </IfModule>

    # Directory Configuration
    <Directory /var/www/e-natus/public>
        Options -Indexes -MultiViews
        AllowOverride All
        Require all granted

        # Bot blocking
        RewriteEngine On
        RewriteCond %{HTTP_USER_AGENT} (AhrefsBot|SemrushBot|DotBot|MJ12bot|BLEXBot|PetalBot) [NC]
        RewriteRule .* - [F,L]

        # Block empty user agents
        RewriteCond %{HTTP_USER_AGENT} ^$
        RewriteRule .* - [F,L]

        # Protect sensitive files
        RewriteCond %{REQUEST_URI} (\.env|\.git|\.htaccess|composer\.json|composer\.lock|package\.json)$ [NC]
        RewriteRule .* - [F,L]
    </Directory>

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/natus-error.log
    CustomLog ${APACHE_LOG_DIR}/natus-access.log combined
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName natus.id
    ServerAlias www.natus.id

    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}$1 [R=301,L]
</VirtualHost>
```

**Apply the configuration:**

```bash
# Test configuration
sudo apache2ctl configtest

# Enable site and restart
sudo a2ensite your-site.conf
sudo systemctl restart apache2
```

---

## Nginx Configuration

For Nginx users, add the following configuration to your server block:

**Location:** `/etc/nginx/sites-available/natus.id`

```nginx
# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name natus.id www.natus.id;

    return 301 https://$server_name$request_uri;
}

# HTTPS Server Block
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name natus.id www.natus.id;

    root /var/www/e-natus/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /path/to/fullchain.pem;
    ssl_certificate_key /path/to/privkey.pem;

    # Modern SSL/TLS Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;

    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    ssl_trusted_certificate /path/to/chain.pem;
    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;

    # Hide Server Information
    server_tokens off;
    more_clear_headers Server;
    more_clear_headers X-Powered-By;

    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header X-Download-Options "noopen" always;
    add_header X-Permitted-Cross-Domain-Policies "none" always;

    # Bot Protection for Sensitive Paths
    location ~ ^/(api|admin|login) {
        add_header X-Robots-Tag "noindex, nofollow, noarchive" always;
        add_header Cache-Control "no-store, no-cache, must-revalidate, private" always;
        add_header Pragma "no-cache" always;

        try_files $uri $uri/ /index.php?$query_string;
    }

    # Block Bad Bots
    if ($http_user_agent ~* (AhrefsBot|SemrushBot|DotBot|MJ12bot|BLEXBot|PetalBot)) {
        return 403;
    }

    # Block Empty User Agents
    if ($http_user_agent = "") {
        return 403;
    }

    # Protect Sensitive Files
    location ~ /\.(env|git|htaccess) {
        deny all;
        return 404;
    }

    location ~ \.(json|lock)$ {
        deny all;
        return 404;
    }

    # PHP Processing
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;  # Adjust PHP version
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;

        # Security
        fastcgi_hide_header X-Powered-By;
    }

    # Laravel Front Controller
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Logging
    access_log /var/log/nginx/natus-access.log;
    error_log /var/log/nginx/natus-error.log;
}
```

**For the `more_clear_headers` directive, install the headers-more module:**

```bash
# Ubuntu/Debian
sudo apt-get install nginx-extras

# Or compile Nginx with the module
# https://github.com/openresty/headers-more-nginx-module
```

**Apply the configuration:**

```bash
# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

---

## SSL/TLS Best Practices

### 1. Obtain SSL Certificate

**Option A: Let's Encrypt (Free, Recommended)**

```bash
# Install Certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-apache  # For Apache
# OR
sudo apt-get install certbot python3-certbot-nginx   # For Nginx

# Obtain certificate
sudo certbot --apache -d natus.id -d www.natus.id    # For Apache
# OR
sudo certbot --nginx -d natus.id -d www.natus.id     # For Nginx

# Auto-renewal is configured automatically
# Test renewal
sudo certbot renew --dry-run
```

**Option B: Commercial SSL Certificate**

-   Purchase from a trusted CA (DigiCert, Sectigo, etc.)
-   Follow provider's installation instructions

### 2. HSTS Preloading

To get your domain on the HSTS preload list:

1. Ensure HSTS header is set with `preload` directive (already configured)
2. Visit https://hstspreload.org/
3. Enter your domain and submit
4. Wait for inclusion (can take several months)

### 3. SSL Labs Test

Verify your SSL/TLS configuration:

```bash
# Visit SSL Labs
https://www.ssllabs.com/ssltest/analyze.html?d=natus.id

# Target: A+ rating
```

---

## Verification Steps

### 1. Check Security Headers

```bash
curl -I https://natus.id/ | grep -E "(Strict-Transport|X-Frame|X-Content|X-XSS|Referrer)"
```

**Expected output:**

```
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

### 2. Verify Server Information is Hidden

```bash
curl -I https://natus.id/ | grep -i "server:"
```

**Expected output:**

```
Server: (empty or generic)
```

Should NOT show version information like "Apache/2.4.41" or "nginx/1.18.0"

### 3. Test Bot Blocking

```bash
curl -I -A "AhrefsBot" https://natus.id/
```

**Expected output:**

```
HTTP/1.1 403 Forbidden
```

### 4. Test HTTPS Redirect

```bash
curl -I http://natus.id/
```

**Expected output:**

```
HTTP/1.1 301 Moved Permanently
Location: https://natus.id/
```

### 5. Online Security Scanners

Run comprehensive security scans:

-   **SSL Labs:** https://www.ssllabs.com/ssltest/
-   **Security Headers:** https://securityheaders.com/
-   **Mozilla Observatory:** https://observatory.mozilla.org/

---

## Troubleshooting

### Apache Issues

**Problem:** Headers not appearing

```bash
# Enable mod_headers
sudo a2enmod headers
sudo systemctl restart apache2
```

**Problem:** .htaccess not working

```bash
# Check AllowOverride in Apache config
# Should be: AllowOverride All
sudo nano /etc/apache2/apache2.conf
```

### Nginx Issues

**Problem:** Headers not appearing

```bash
# Check if headers-more module is installed
nginx -V 2>&1 | grep -o with-http_headers_more_module

# If not, install nginx-extras or compile with module
```

**Problem:** Syntax errors

```bash
# Test configuration
sudo nginx -t

# Check error log
sudo tail -f /var/log/nginx/error.log
```

---

## Production Checklist

-   [ ] SSL/TLS certificate installed and valid
-   [ ] HTTPS redirect configured (HTTP → HTTPS)
-   [ ] Server tokens/signatures hidden
-   [ ] Security headers configured
-   [ ] Bot protection rules active
-   [ ] Sensitive files blocked (.env, .git, etc.)
-   [ ] SSL Labs test shows A+ rating
-   [ ] Security headers scanner shows all green
-   [ ] Application functions correctly over HTTPS
-   [ ] HSTS preload submitted (optional but recommended)

---

## Additional Resources

-   [OWASP Secure Headers Project](https://owasp.org/www-project-secure-headers/)
-   [Mozilla SSL Configuration Generator](https://ssl-config.mozilla.org/)
-   [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
-   [Apache Security Tips](https://httpd.apache.org/docs/2.4/misc/security_tips.html)
-   [Nginx Security Controls](https://docs.nginx.com/nginx/admin-guide/security-controls/)
