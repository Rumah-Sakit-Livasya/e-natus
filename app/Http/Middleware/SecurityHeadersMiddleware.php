<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ============================================
        // CORE SECURITY HEADERS
        // ============================================

        // HSTS (PCI DSS Compliance)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // X-Frame-Options - Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-Content-Type-Options - Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection - Legacy XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy (GDPR Compliance)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ============================================
        // CONTENT SECURITY POLICY
        // ============================================

        // CSP - Balanced security with application compatibility
        // Note: Uses 'unsafe-inline' and 'unsafe-eval' for compatibility with Filament/Laravel
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self'",
            "frame-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "upgrade-insecure-requests"
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // ============================================
        // PERMISSIONS POLICY
        // ============================================

        // Allow geolocation for attendance features, block other sensitive APIs
        $permissions = 'camera=(), microphone=(), geolocation=(self), payment=(), usb=()';
        $response->headers->set('Permissions-Policy', $permissions);

        // ============================================
        // DYNAMIC RATE LIMITING HEADERS
        // ============================================

        // Get rate limit key for this request
        $rateLimitKey = 'global:' . $request->ip();
        $maxAttempts = 120;
        $decayMinutes = 1;

        // Get current rate limit status
        $remaining = RateLimiter::remaining($rateLimitKey, $maxAttempts);

        // Calculate reset time (current time + decay period)
        $resetTime = time() + ($decayMinutes * 60);

        // Set dynamic rate limit headers
        $response->headers->set('X-RateLimit-Limit', (string)$maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', (string)max(0, $remaining));
        $response->headers->set('X-RateLimit-Reset', (string)$resetTime);

        // Add Retry-After header if rate limit exceeded
        if ($remaining <= 0) {
            $response->headers->set('Retry-After', (string)($decayMinutes * 60));
        }

        // ============================================
        // ADDITIONAL SECURITY HEADERS
        // ============================================

        $response->headers->set('X-Download-Options', 'noopen');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('X-DNS-Prefetch-Control', 'off');

        // ============================================
        // BOT PROTECTION
        // ============================================

        // Comprehensive bot detection
        $userAgent = $request->userAgent() ?? '';
        $isSuspiciousBot = $this->isSuspiciousBot($userAgent);

        if ($isSuspiciousBot) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        }

        // Add general bot discouragement for sensitive paths
        if ($request->is('admin/*') || $request->is('api/*') || $request->is('login')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
        }

        // ============================================
        // SECURE COOKIE CONFIGURATION
        // ============================================

        // Ensure all cookies have security flags (GDPR + PCI DSS)
        $cookies = $response->headers->getCookies();
        foreach ($cookies as $cookie) {
            $response->headers->setCookie(
                new \Symfony\Component\HttpFoundation\Cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresTime(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    true,  // Secure - HTTPS only
                    true,  // HttpOnly - No JavaScript access
                    false, // Raw
                    'strict' // SameSite - CSRF protection
                )
            );
        }

        return $response;
    }

    /**
     * Detect suspicious bot user agents
     *
     * @param string $userAgent
     * @return bool
     */
    private function isSuspiciousBot(string $userAgent): bool
    {
        $suspiciousBots = [
            'AhrefsBot',
            'SemrushBot',
            'DotBot',
            'MJ12bot',
            'BLEXBot',
            'PetalBot',
            'scrapy',
            'curl',
            'wget',
            'python-requests',
            'go-http-client',
            'Bytespider',
            'DataForSeoBot',
            'serpstatbot',
            'BLEXBot'
        ];

        foreach ($suspiciousBots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }
}
