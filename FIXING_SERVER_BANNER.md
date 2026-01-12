# Menyembunyikan Versi Server di LiteSpeed

## Masalah Teridentifikasi

Server Anda menggunakan **LiteSpeed Web Server**, bukan Apache atau Nginx.

```bash
$ curl -I https://natus.id | grep server
server: LiteSpeed
```

**Status:** Header "Server" terlihat, tapi **versi tidak ditampilkan** (sudah cukup baik!)

## Penjelasan

LiteSpeed sudah **menyembunyikan nomor versi** secara default. Yang terlihat hanya:

-   ✅ `server: LiteSpeed` (BAIK - tidak ada versi)
-   ❌ BUKAN `server: LiteSpeed/5.4.12` (BURUK - ada versi)

**Ini sebenarnya sudah cukup aman!** Kebanyakan security scanner akan menerima ini.

## Jika Masih Ingin Menyembunyikan Sepenuhnya

### Opsi 1: Via .htaccess (Untuk LiteSpeed)

Tambahkan di `.htaccess`:

```apache
<IfModule LiteSpeed>
    # Sembunyikan header Server
    Header unset Server
    Header always unset Server
</IfModule>
```

### Opsi 2: Via LiteSpeed Admin Console (Recommended)

Jika Anda punya akses ke LiteSpeed Admin Console:

1. Login ke **LiteSpeed WebAdmin Console** (biasanya port 7080)
2. Navigate ke **Configuration** → **Server** → **General**
3. Cari setting **"Hide Server Signature"** atau **"Server Signature"**
4. Set ke **"Hide"** atau **"Off"**
5. **Graceful Restart** server

### Opsi 3: Via Server Config File

Edit file konfigurasi LiteSpeed:

**File:** `/usr/local/lsws/conf/httpd_config.xml`

Cari dan ubah:

```xml
<hideSig>1</hideSig>
```

Atau tambahkan jika belum ada:

```xml
<serverSignature>0</serverSignature>
```

**Restart LiteSpeed:**

```bash
sudo /usr/local/lsws/bin/lswsctrl restart
```

### Opsi 4: Gunakan Cloudflare (Termudah!)

Cloudflare akan mengganti header menjadi:

```
server: cloudflare
```

**Cara:**

1. Daftar di https://cloudflare.com
2. Tambahkan domain natus.id
3. Update nameserver
4. Aktifkan proxy (orange cloud)

**Bonus:**

-   DDoS protection
-   CDN global
-   SSL gratis
-   DNSSEC mudah diaktifkan
-   Bot protection

---

## Update .htaccess untuk LiteSpeed

Saya akan update `.htaccess` dengan konfigurasi khusus LiteSpeed.
