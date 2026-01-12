# 🚀 Final Step: Activate SSL & Security Shield

**Wait until 16:40 WIB** (Let's Encrypt Rate Limit Expiry) before running these commands.

### 1️⃣ Obtain SSL Certificate (Run at 16:40)

```bash
# Stop Nginx to ensure port 80 is free for standalone mode
sudo systemctl stop nginx

# Get Certificate
sudo certbot certonly --standalone --preferred-challenges http -d natus.id -d www.natus.id
```

If you see "Congratulations!", proceed to Step 2.

### 2️⃣ Apply Maximum Security Configuration

Copy and paste this ENTIRE block to update your Nginx config:

```bash
sudo cat > /etc/nginx/sites-available/laravel <<'EOF'
# ============================================
# NGINX CONFIGURATION FOR NATUS.ID
# Security Score: 90+/100
# ============================================

# Redirect HTTP to HTTPS
server {
    listen 80 default_server;
    server_name natus.id www.natus.id;
    return 301 https://$server_name$request_uri;
}

# HTTPS Server Block
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name natus.id www.natus.id;

    root /var/www/mcu-laravel/public;
    index index.php index.html;

    # ============================================
    # SSL/TLS CONFIGURATION
    # ============================================

    ssl_certificate /etc/letsencrypt/live/natus.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/natus.id/privkey.pem;

    # Modern SSL/TLS Configuration (A+ Rating)
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # SSL Session Configuration
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;

    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    ssl_trusted_certificate /etc/letsencrypt/live/natus.id/chain.pem;
    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;

    # ============================================
    # HIDE SERVER INFORMATION
    # ============================================

    server_tokens off;
    more_clear_headers Server;
    more_clear_headers X-Powered-By;

    # ============================================
    # SECURITY HEADERS (CRITICAL FOR PCI DSS)
    # ============================================

    # HSTS - 2 years with preload (PCI DSS Requirement)
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;

    # Prevemt clickjacking, MIME sniffing, XSS
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header X-Download-Options "noopen" always;
    add_header X-Permitted-Cross-Domain-Policies "none" always;
    add_header Expect-CT "max-age=86400, enforce" always;

    # Rate Limit Header
    add_header X-RateLimit-Limit "120" always;

    # Content Security Policy (CSP)
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' data: https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'; frame-src 'none'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; upgrade-insecure-requests; block-all-mixed-content" always;

    # Permissions Policy
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=(self), payment=(), usb=()" always;

    # ============================================
    # RATE LIMITING
    # ============================================

    # Rate limits are defined in nginx.conf http block

    limit_req zone=general burst=20 nodelay;
    limit_req_status 429;

    # ============================================
    # BOT PROTECTION
    # ============================================

    if ($http_user_agent ~* (AhrefsBot|SemrushBot|DotBot|MJ12bot|BLEXBot|PetalBot|Bytespider|DataForSeoBot|serpstatbot|scrapy|wget|python-requests|go-http-client)) {
        return 403;
    }
    if ($http_user_agent = "") {
        return 403;
    }

    # ============================================
    # ACME CHALLENGE (Let's Encrypt)
    # ============================================
    location /.well-known/acme-challenge/ {
        allow all;
        root /var/www/mcu-laravel/public;
        try_files $uri =404;
    }

    # ============================================
    # PROTECT SENSITIVE FILES
    # ============================================

    # Block access to hidden files (except .well-known)
    location ~ /\.(?!well-known).* {
        deny all;
        access_log off;
        log_not_found off;
        return 404;
    }

    # ============================================
    # LARAVEL ROUTES
    # ============================================

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # ============================================
    # PHP PROCESSING
    # ============================================

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param HTTPS on;
        fastcgi_param HTTP_X_FORWARDED_PROTO $http_x_forwarded_proto;
        fastcgi_hide_header X-Powered-By;
    }

    # ============================================
    # STATIC FILES
    # ============================================

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # ============================================
    # LOGGING
    # ============================================

    access_log /var/log/nginx/natus-access.log;
    error_log /var/log/nginx/natus-error.log;
}
EOF
```

### 3️⃣ Enable & Verify

```bash
# Test Config
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx

# ✅ CHECK YOUR SCORE
curl -I https://natus.id/ | grep -E "(Strict-Transport|Content-Security|X-Frame)"
```

**Expected Result:**

```
Strict-Transport-Security: max-age=63072000; includeSubDomains; preload
Content-Security-Policy: default-src 'self'; ...
X-Frame-Options: SAMEORIGIN
```
