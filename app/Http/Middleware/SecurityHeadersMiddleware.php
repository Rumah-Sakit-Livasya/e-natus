<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        // Path-specific CSP: Strict for public pages, relaxed for admin panel
        $isAdminPath = $request->is('admin/*') || $request->is('dashboard/*') || $request->is('filament/*');

        if ($isAdminPath) {
            // Relaxed CSP for admin panel (Filament compatibility)
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
                "form-action 'self'"
            ]);
        } else {
            // Strict CSP for public pages (better security score)
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' https://cdn.jsdelivr.net",
                "style-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net",
                "font-src 'self' data: https://fonts.gstatic.com",
                "img-src 'self' data: https:",
                "connect-src 'self'",
                "frame-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "upgrade-insecure-requests"
            ]);
        }

        $response->headers->set('Content-Security-Policy', $csp);

        // ============================================
        // PERMISSIONS POLICY
        // ============================================

        // Allow geolocation for attendance features, block other sensitive APIs
        $permissions = 'camera=(), microphone=(), geolocation=(self), payment=(), usb=()';
        $response->headers->set('Permissions-Policy', $permissions);

        // ============================================
        // RATE LIMITING HEADERS
        // ============================================

        // Set static rate limit headers (actual limiting handled by throttle middleware)
        // Using static values to avoid cache access on every request
        $maxAttempts = 120;
        $decayMinutes = 1;
        $resetTime = time() + ($decayMinutes * 60);

        $response->headers->set('X-RateLimit-Limit', (string)$maxAttempts);
        $response->headers->set('X-RateLimit-Reset', (string)$resetTime);

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
