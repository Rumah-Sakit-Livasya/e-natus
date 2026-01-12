#!/bin/bash

# Security Headers Verification Script
# Tests all security headers and configurations

echo "=================================="
echo "Security Headers Verification"
echo "=================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test URL (change this to your actual URL)
URL="${1:-http://localhost:8000}"

echo "Testing URL: $URL"
echo ""

# Function to check header
check_header() {
    local header_name="$1"
    local expected_value="$2"
    local actual_value=$(curl -s -I "$URL" | grep -i "^$header_name:" | cut -d' ' -f2- | tr -d '\r')

    if [ -z "$actual_value" ]; then
        echo -e "${RED}✗${NC} $header_name: ${RED}MISSING${NC}"
        return 1
    elif [ -n "$expected_value" ] && [[ ! "$actual_value" =~ $expected_value ]]; then
        echo -e "${YELLOW}⚠${NC} $header_name: ${YELLOW}$actual_value${NC} (expected: $expected_value)"
        return 2
    else
        echo -e "${GREEN}✓${NC} $header_name: $actual_value"
        return 0
    fi
}

echo "=== Core Security Headers ==="
check_header "Strict-Transport-Security" "max-age=31536000"
check_header "X-Frame-Options" "SAMEORIGIN"
check_header "X-Content-Type-Options" "nosniff"
check_header "X-XSS-Protection" "1; mode=block"
check_header "Referrer-Policy" "strict-origin-when-cross-origin"
echo ""

echo "=== Content Security Policy ==="
check_header "Content-Security-Policy" "default-src"
echo ""

echo "=== Permissions Policy ==="
check_header "Permissions-Policy" "geolocation"
echo ""

echo "=== Rate Limiting Headers ==="
check_header "X-RateLimit-Limit" ""
check_header "X-RateLimit-Remaining" ""
check_header "X-RateLimit-Reset" ""
echo ""

echo "=== Additional Security Headers ==="
check_header "X-Download-Options" "noopen"
check_header "X-Permitted-Cross-Domain-Policies" "none"
check_header "X-DNS-Prefetch-Control" "off"
echo ""

echo "=== Server Information ==="
server_header=$(curl -s -I "$URL" | grep -i "^Server:" | cut -d' ' -f2- | tr -d '\r')
x_powered_by=$(curl -s -I "$URL" | grep -i "^X-Powered-By:" | cut -d' ' -f2- | tr -d '\r')

if [ -z "$server_header" ] || [[ "$server_header" == "Server" ]]; then
    echo -e "${GREEN}✓${NC} Server header: Hidden or generic"
else
    echo -e "${YELLOW}⚠${NC} Server header: $server_header (version info may be exposed)"
fi

if [ -z "$x_powered_by" ]; then
    echo -e "${GREEN}✓${NC} X-Powered-By: Hidden"
else
    echo -e "${RED}✗${NC} X-Powered-By: $x_powered_by (should be hidden)"
fi
echo ""

echo "=== Bot Protection Test ==="
bot_response=$(curl -s -o /dev/null -w "%{http_code}" -A "AhrefsBot" "$URL")
if [ "$bot_response" == "403" ]; then
    echo -e "${GREEN}✓${NC} Bad bots are blocked (HTTP 403)"
else
    echo -e "${YELLOW}⚠${NC} Bad bots may not be blocked (HTTP $bot_response)"
fi
echo ""

echo "=== Cookie Security ==="
cookies=$(curl -s -I "$URL" | grep -i "^Set-Cookie:")
if [ -z "$cookies" ]; then
    echo -e "${GREEN}✓${NC} No cookies set on homepage (expected for public pages)"
else
    echo "Cookies found:"
    echo "$cookies"
    if [[ "$cookies" =~ "Secure" ]] && [[ "$cookies" =~ "HttpOnly" ]] && [[ "$cookies" =~ "SameSite=strict" ]]; then
        echo -e "${GREEN}✓${NC} Cookies have security attributes"
    else
        echo -e "${RED}✗${NC} Cookies missing security attributes"
    fi
fi
echo ""

echo "=================================="
echo "Verification Complete"
echo "=================================="
echo ""
echo "Next Steps:"
echo "1. If testing locally, deploy to production server"
echo "2. Run this script against production URL"
echo "3. Use online security scanners:"
echo "   - https://securityheaders.com/"
echo "   - https://observatory.mozilla.org/"
echo "4. Test SSL/TLS configuration:"
echo "   - https://www.ssllabs.com/ssltest/"
echo ""
