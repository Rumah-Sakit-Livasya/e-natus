# DNSSEC Setup Guide

Complete guide for configuring DNSSEC (Domain Name System Security Extensions) to protect your domain from DNS spoofing and cache poisoning attacks.

## What is DNSSEC?

DNSSEC adds cryptographic signatures to DNS records, ensuring that DNS responses are authentic and haven't been tampered with. This protects against:

-   **DNS Spoofing:** Attackers redirecting your domain to malicious servers
-   **Cache Poisoning:** Corrupting DNS resolver caches with false information
-   **Man-in-the-Middle Attacks:** Intercepting and modifying DNS queries

## Benefits

✅ **Enhanced Security:** Cryptographic validation of DNS responses
✅ **Trust Chain:** Verification from root DNS to your domain
✅ **Attack Prevention:** Protection against DNS-based attacks
✅ **Compliance:** Required for some security standards and government contracts

## Prerequisites

Before enabling DNSSEC:

1. Your domain registrar must support DNSSEC
2. Your DNS provider must support DNSSEC
3. Your domain must be using the DNS provider's nameservers

## Configuration by DNS Provider

### Cloudflare (Recommended)

Cloudflare offers the easiest DNSSEC setup with automatic key management.

**Steps:**

1. **Log in to Cloudflare Dashboard**

    - Visit https://dash.cloudflare.com/
    - Select your domain (natus.id)

2. **Enable DNSSEC**

    - Go to **DNS** → **Settings**
    - Scroll to **DNSSEC** section
    - Click **Enable DNSSEC**

3. **Get DS Record Information**

    - Cloudflare will display DS record details:
        - Key Tag
        - Algorithm
        - Digest Type
        - Digest

4. **Add DS Record to Domain Registrar**

    - Log in to your domain registrar (where you purchased natus.id)
    - Find DNSSEC settings (usually under DNS or Advanced settings)
    - Add the DS record with information from Cloudflare
    - Save changes

5. **Verify DNSSEC**

    ```bash
    # Wait 24-48 hours for propagation, then test
    dig natus.id +dnssec

    # Should show RRSIG records
    # Or use online tool: https://dnssec-analyzer.verisignlabs.com/
    ```

**Cloudflare Advantages:**

-   Automatic key rotation
-   One-click setup
-   Free on all plans
-   Excellent documentation

---

### AWS Route53

AWS Route53 provides robust DNSSEC support with KMS integration.

**Steps:**

1. **Enable DNSSEC Signing**

    ```bash
    # Using AWS CLI
    aws route53 enable-hosted-zone-dnssec \
      --hosted-zone-id Z1234567890ABC
    ```

    Or via AWS Console:

    - Open Route53 console
    - Select your hosted zone
    - Click **DNSSEC signing** tab
    - Click **Enable DNSSEC signing**

2. **Create KMS Key (if needed)**

    - Route53 will prompt to create a KMS key
    - Accept default settings or customize
    - Note: KMS keys incur additional costs (~$1/month)

3. **Get DS Records**

    ```bash
    # Using AWS CLI
    aws route53 get-dnssec \
      --hosted-zone-id Z1234567890ABC
    ```

    Or via Console:

    - View DS records in DNSSEC signing tab
    - Copy all DS record values

4. **Add DS Records to Registrar**

    - Log in to your domain registrar
    - Add DS records (Route53 provides multiple records)
    - Save changes

5. **Monitor DNSSEC Status**
    ```bash
    # Check status
    aws route53 get-dnssec \
      --hosted-zone-id Z1234567890ABC
    ```

**Route53 Documentation:** https://docs.aws.amazon.com/Route53/latest/DeveloperGuide/dns-configuring-dnssec.html

---

### Google Cloud DNS

Google Cloud DNS offers DNSSEC with automatic key rotation.

**Steps:**

1. **Enable DNSSEC via Console**

    - Open Cloud Console: https://console.cloud.google.com/
    - Navigate to **Network Services** → **Cloud DNS**
    - Select your DNS zone
    - Click **DNSSEC** tab
    - Click **Turn on DNSSEC**

2. **Configure DNSSEC Settings**

    - Choose signing algorithm (RSASHA256 recommended)
    - Enable automatic key rotation
    - Click **Turn on**

3. **Get DS Records**

    ```bash
    # Using gcloud CLI
    gcloud dns dns-keys list \
      --zone=natus-id-zone
    ```

    Or via Console:

    - View DS records in DNSSEC tab
    - Copy DS record information

4. **Add DS Records to Registrar**

    - Log in to domain registrar
    - Add DS records
    - Save changes

5. **Verify Configuration**
    ```bash
    # Check DNSSEC status
    gcloud dns managed-zones describe natus-id-zone
    ```

**Google Cloud DNS Documentation:** https://cloud.google.com/dns/docs/dnssec

---

### DigitalOcean

DigitalOcean provides straightforward DNSSEC configuration.

**Steps:**

1. **Access DNS Settings**

    - Log in to DigitalOcean Control Panel
    - Go to **Networking** → **Domains**
    - Select your domain

2. **Enable DNSSEC**

    - Click **DNSSEC** tab
    - Click **Enable DNSSEC**
    - DigitalOcean will generate keys automatically

3. **Get DS Record**

    - Copy the DS record information displayed
    - Note: DigitalOcean provides one DS record

4. **Add DS Record to Registrar**

    - Log in to your domain registrar
    - Add the DS record
    - Save changes

5. **Verify DNSSEC**
    ```bash
    dig natus.id +dnssec
    ```

**DigitalOcean Documentation:** https://docs.digitalocean.com/products/networking/dns/how-to/enable-dnssec/

---

### Namecheap (Registrar + DNS)

If using Namecheap for both registration and DNS hosting:

**Steps:**

1. **Enable DNSSEC in DNS Settings**

    - Log in to Namecheap account
    - Go to **Domain List** → Select domain
    - Click **Advanced DNS** tab
    - Scroll to **DNSSEC** section
    - Click **Enable DNSSEC**

2. **Namecheap Generates Keys**

    - Namecheap automatically generates and manages keys
    - DS records are automatically added to the domain

3. **Verify DNSSEC**
    - Wait 24-48 hours for propagation
    - Use DNSSEC validator: https://dnssec-debugger.verisignlabs.com/

**Note:** If using Namecheap as registrar but different DNS provider:

-   Get DS records from your DNS provider
-   Add them in Namecheap under **Domain** → **DNSSEC**

**Namecheap Documentation:** https://www.namecheap.com/support/knowledgebase/article.aspx/9722/2232/managing-dnssec-for-domains-pointed-to-custom-dns/

---

### GoDaddy (Registrar + DNS)

GoDaddy supports DNSSEC for domains using GoDaddy nameservers.

**Steps:**

1. **Access Domain Settings**

    - Log in to GoDaddy account
    - Go to **My Products** → **Domains**
    - Click on your domain

2. **Enable DNSSEC**

    - Scroll to **Additional Settings**
    - Click **Manage** next to **DNSSEC**
    - Click **Set Up**

3. **GoDaddy Manages Keys**

    - GoDaddy automatically generates and manages keys
    - DS records are automatically configured

4. **Verify DNSSEC**
    ```bash
    dig natus.id +dnssec
    ```

**Note:** If using external DNS provider:

-   Get DS records from DNS provider
-   Add them in GoDaddy DNSSEC settings manually

**GoDaddy Documentation:** https://www.godaddy.com/help/enable-dnssec-6420

---

### Porkbun (Registrar)

Porkbun supports DNSSEC for domains using external DNS providers.

**Steps:**

1. **Get DS Records from DNS Provider**

    - Obtain DS record information from your DNS provider (Cloudflare, Route53, etc.)

2. **Add DS Records in Porkbun**

    - Log in to Porkbun account
    - Go to **Domain Management**
    - Select your domain
    - Click **DNSSEC** tab
    - Click **Add DNSSEC Record**

3. **Enter DS Record Information**

    - Key Tag: (from DNS provider)
    - Algorithm: (from DNS provider)
    - Digest Type: (from DNS provider)
    - Digest: (from DNS provider)
    - Click **Submit**

4. **Verify DNSSEC**
    ```bash
    dig natus.id +dnssec
    ```

---

## Verification Steps

### 1. Command Line Verification

**Check for DNSSEC signatures:**

```bash
dig natus.id +dnssec
```

**Expected output should include:**

-   `RRSIG` records (signatures)
-   `ad` flag (authenticated data)

**Example:**

```
;; flags: qr rd ra ad; QUERY: 1, ANSWER: 2, AUTHORITY: 0, ADDITIONAL: 1
```

The `ad` flag indicates DNSSEC validation succeeded.

### 2. Online DNSSEC Validators

**Verisign DNSSEC Debugger:**

-   Visit: https://dnssec-debugger.verisignlabs.com/
-   Enter: natus.id
-   Click **Trace**
-   Should show all green checkmarks

**DNSViz:**

-   Visit: https://dnsviz.net/
-   Enter: natus.id
-   Should show complete trust chain

**DNSSEC Analyzer:**

-   Visit: https://dnssec-analyzer.verisignlabs.com/
-   Enter: natus.id
-   Should show "DNSSEC is properly configured"

### 3. Browser Extension

Install **DNSSEC Validator** extension:

-   Chrome: https://chrome.google.com/webstore (search "DNSSEC Validator")
-   Firefox: https://addons.mozilla.org/ (search "DNSSEC Validator")

When visiting natus.id, the extension should show DNSSEC is validated.

---

## Troubleshooting

### Common Issues

**Problem:** DS records not propagating

```bash
# Check if DS records are visible at registrar
dig +trace natus.id DS

# Wait 24-48 hours for full propagation
```

**Problem:** DNSSEC validation failing

```bash
# Check for DNSSEC errors
dig natus.id +dnssec +cd

# The +cd flag bypasses validation to see raw responses
```

**Problem:** "SERVFAIL" responses

-   DNSSEC validation is failing
-   Check DS records match between DNS provider and registrar
-   Verify DNSSEC is enabled at DNS provider
-   Check for expired keys (if manual key management)

### Key Rotation

Most providers handle automatic key rotation:

-   **Cloudflare:** Automatic, no action needed
-   **AWS Route53:** Automatic with KMS
-   **Google Cloud DNS:** Automatic when enabled
-   **DigitalOcean:** Automatic
-   **Namecheap:** Automatic
-   **GoDaddy:** Automatic

If using manual DNSSEC:

-   Rotate keys every 3-6 months
-   Follow your DNS provider's key rotation procedure

---

## Security Best Practices

1. **Enable Automatic Key Rotation**

    - Use DNS providers that support automatic rotation
    - Reduces risk of expired keys causing outages

2. **Monitor DNSSEC Status**

    - Set up monitoring for DNSSEC validation
    - Use tools like DNSViz or DNSSEC Debugger regularly

3. **Keep DS Records Updated**

    - When changing DNS providers, update DS records
    - Remove old DS records after migration

4. **Test Before Enabling**

    - Use DNSSEC validators before going live
    - Ensure all records validate correctly

5. **Document Your Configuration**
    - Keep records of DS record values
    - Document which DNS provider manages keys
    - Note key rotation schedule if manual

---

## Impact on Security Score

Enabling DNSSEC will improve your security posture:

**Before DNSSEC:**

-   DNSSEC Configuration Test: 0/10

**After DNSSEC:**

-   DNSSEC Configuration Test: 10/10
-   Overall security score improvement: +10 points

**Note:** DNSSEC validation requires DNS queries and cannot be verified via HTTP headers. Use the verification tools above to confirm proper configuration.

---

## FAQ

**Q: Is DNSSEC required?**
A: Not required for most websites, but highly recommended for:

-   Financial services
-   Healthcare applications
-   Government websites
-   High-security applications

**Q: Does DNSSEC slow down DNS resolution?**
A: Minimal impact (typically <50ms). Modern DNS resolvers cache DNSSEC signatures efficiently.

**Q: What happens if DNSSEC breaks?**
A: Your domain becomes unreachable. This is why automatic key management is recommended.

**Q: Can I disable DNSSEC later?**
A: Yes, but you must remove DS records from your registrar first, then disable at DNS provider.

**Q: Does DNSSEC encrypt DNS queries?**
A: No. DNSSEC provides authentication, not encryption. For encryption, use DNS-over-HTTPS (DoH) or DNS-over-TLS (DoT).

---

## Additional Resources

-   **DNSSEC Overview:** https://www.icann.org/resources/pages/dnssec-what-is-it-why-important-2019-03-05-en
-   **DNSSEC Best Practices:** https://www.cloudflare.com/dns/dnssec/how-dnssec-works/
-   **IETF DNSSEC Standards:** https://datatracker.ietf.org/wg/dnsop/documents/
-   **DNSSEC Deployment Guide:** https://www.internetsociety.org/deploy360/dnssec/

---

## Quick Reference

| DNS Provider     | Automatic Keys | Setup Difficulty | Cost            |
| ---------------- | -------------- | ---------------- | --------------- |
| Cloudflare       | ✅ Yes         | Easy             | Free            |
| AWS Route53      | ✅ Yes         | Medium           | ~$1/month (KMS) |
| Google Cloud DNS | ✅ Yes         | Medium           | Free            |
| DigitalOcean     | ✅ Yes         | Easy             | Free            |
| Namecheap        | ✅ Yes         | Easy             | Free            |
| GoDaddy          | ✅ Yes         | Easy             | Free            |

**Recommendation:** Use Cloudflare for the easiest setup with excellent security features.
