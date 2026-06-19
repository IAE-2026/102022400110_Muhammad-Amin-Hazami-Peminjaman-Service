# Prompt Untuk Amin - Peminjaman Service

Copy seluruh prompt di bawah ini ke AI assistant Amin.

```text
Kamu adalah coding assistant saya. Saya sedang membuat ulang repository gabungan Tugas Besar IAE Kelompok 5 dari repository kosong agar commit history menunjukkan kontribusi tiap anggota secara jujur.

Konteks proyek:
- Repository gabungan: https://github.com/IAE-2026/Tubes-IAE-Kelompok-5
- Source service saya: https://github.com/IAE-2026/102022400110_Muhammad-Amin-Hazami-Peminjaman-Service
- Target folder di repo gabungan: Peminjaman-Service-Amin
- Service saya adalah Peminjaman Service.
- API key Peminjaman Service: 102022400110
- Team ID: TEAM-05
- SSO URL: https://iae-sso.virtualfri.id
- Member Service internal URL: http://member-service:8000
- Member Service API key: 102022400255
- Katalog Service internal URL: http://katalog-buku-service:8000
- Katalog Service API key: KEY-MHS-44

Requirement integrasi:
- Semua akses dari host harus lewat API Gateway.
- Service internal saling panggil lewat Docker network.
- Flow bisnis end-to-end:
  1. Peminjaman menerima POST /api/v1/loans dengan member_id dan book_id.
  2. Peminjaman memvalidasi member ke Member Service.
  3. Peminjaman memvalidasi buku dan stok ke Katalog Service.
  4. Jika valid, Peminjaman meminta Katalog mengurangi available_stock.
  5. Saat POST /api/v1/loans/{id}/return, Peminjaman mengembalikan stok katalog.
  6. Setelah return berhasil, Peminjaman menjalankan SSO, SOAP Audit, dan publish event RabbitMQ/AMQP.

Aturan penting:
- Jangan membuat commit atas nama anggota lain.
- Jangan mengubah folder Member-Service-Faris atau Katalog-Buku-Service-Dewinda.
- Jangan commit vendor, node_modules, .env, database lokal, cache, atau file generated yang tidak perlu.
- Pastikan git user.name dan user.email adalah identitas GitHub saya sendiri sebelum commit.
- Selalu git pull sebelum mulai.

Tugas 1 - Ambil repo gabungan:
1. Clone atau pull repo gabungan:
   git clone https://github.com/IAE-2026/Tubes-IAE-Kelompok-5.git
   cd Tubes-IAE-Kelompok-5
   git pull
2. Cek identitas Git:
   git config user.name
   git config user.email
3. Kalau belum benar, set sesuai akun GitHub Amin.

Tugas 2 - Tambahkan Peminjaman Service:
1. Clone source service ke folder sementara:
   git clone https://github.com/IAE-2026/102022400110_Muhammad-Amin-Hazami-Peminjaman-Service.git _tmp-peminjaman
2. Copy isi source service ke folder Peminjaman-Service-Amin tanpa membawa folder .git.
   Di PowerShell bisa pakai:
   robocopy _tmp-peminjaman Peminjaman-Service-Amin /E /XD .git vendor node_modules /XF .env
   Remove-Item -Recurse -Force _tmp-peminjaman
3. Commit:
   git add Peminjaman-Service-Amin
   git commit -m "Add Peminjaman Service"
   git push

Tugas 3 - Integrasikan loan flow:
Lakukan perubahan hanya di folder Peminjaman-Service-Amin.

1. Update app/Http/Controllers/LoanController.php:
   - Import Illuminate\Support\Facades\Http dan Illuminate\Support\Facades\Log.
   - Pada store(Request $request):
     - Validasi member_id dan book_id sebagai integer required.
     - Panggil helper fetchMember(member_id).
     - Jika member tidak ditemukan atau service gagal, return JSON error sesuai status.
     - Tolak peminjaman jika member status bukan active atau is_active bukan true.
     - Panggil helper fetchBook(book_id, bearer token).
     - Tolak peminjaman jika available_stock < 1.
     - Panggil helper postCatalogStockAction(book_id, 'borrow', bearer token).
     - Setelah stok berhasil dikurangi, baru create Loan dengan borrow_date hari ini dan status active.
     - Response 201 harus menyertakan loan, validated_member, dan validated_book.
   - Pada returnBook(Request $request, $id):
     - Cari loan.
     - Tolak jika loan tidak ada.
     - Tolak jika status sudah returned.
     - Panggil postCatalogStockAction(book_id, 'return', bearer token).
     - Setelah stok berhasil dikembalikan, update return_date dan status returned.
     - Jalankan SSO token, SOAP Audit, dan AMQP publish seperti requirement Tugas 3.
     - Response harus menyertakan loan terbaru, catalog_stock, dan audit status.

2. Tambahkan helper private di LoanController:
   - fetchMember(int $memberId): array
     - Base URL dari env('MEMBER_SERVICE_URL', 'http://member-service:8000')
     - Header X-IAE-KEY dari env('MEMBER_SERVICE_API_KEY', '102022400255')
     - GET /api/v1/members/{id}
     - Return struktur array: ok, message, data, code.
   - fetchBook(int $bookId, ?string $bearerToken): array
     - Base URL dari env('CATALOG_SERVICE_URL', 'http://katalog-buku-service:8000')
     - Header X-IAE-KEY dari env('CATALOG_SERVICE_API_KEY', 'KEY-MHS-44')
     - Jika bearer token ada, forward sebagai Authorization Bearer.
     - GET /api/v1/books/{id}
   - postCatalogStockAction(int $bookId, string $action, ?string $bearerToken): array
     - POST /api/v1/books/{id}/stock/{action}
     - action bernilai borrow atau return.
     - Header X-IAE-KEY dan bearer token sama seperti fetchBook.

3. Update app/Http/Middleware/JwtMiddleware.php:
   - Ambil base SSO dari env('SSO_URL', 'https://iae-sso.virtualfri.id').
   - Ambil JWKS dari /api/v1/auth/jwks.
   - Jika gagal, fallback ke /.well-known/jwks.json.
   - Jangan hardcode domain selain fallback env default.

4. Update app/Services/SoapAuditService.php:
   - SOAP URL harus memakai env('SSO_URL', 'https://iae-sso.virtualfri.id') . '/soap/v1/audit'.
   - TEAM_ID harus memakai env('TEAM_ID', 'TEAM-XX').
   - LogContent dikirim sebagai JSON dalam CDATA.

5. Update phpunit.xml:
   Tambahkan env testing:
   APP_KEY=base64:TSpbPOJlerDCDNKMVPZkwE4w3uWc0nBp/fDMa3TRkYw=
   API_KEY=102022400110
   TEAM_ID=TEAM-05
   SSO_URL=https://iae-sso.virtualfri.id
   MEMBER_SERVICE_URL=http://member-service:8000
   MEMBER_SERVICE_API_KEY=102022400255
   CATALOG_SERVICE_URL=http://katalog-buku-service:8000
   CATALOG_SERVICE_API_KEY=KEY-MHS-44
   DB_CONNECTION=sqlite
   DB_DATABASE=:memory:
   CACHE_STORE=array
   SESSION_DRIVER=array
   QUEUE_CONNECTION=sync

Tugas 4 - Test:
Masuk ke folder Peminjaman-Service-Amin lalu jalankan:
   composer install
   php -l app/Http/Controllers/LoanController.php
   php -l app/Http/Middleware/JwtMiddleware.php
   php -l app/Services/SoapAuditService.php
   php artisan test

Kalau ada warning dependency deprecation tapi exit code test 0, catat sebagai PASS with warnings.

Tugas 5 - Commit patch:
Balik ke root repo gabungan lalu commit:
   git add Peminjaman-Service-Amin/app/Http/Controllers/LoanController.php Peminjaman-Service-Amin/app/Http/Middleware/JwtMiddleware.php Peminjaman-Service-Amin/app/Services/SoapAuditService.php Peminjaman-Service-Amin/phpunit.xml
   git commit -m "Integrate loan flow with member and catalog services"
   git push

Kalau kamu memisahkan test config ke commit sendiri, gunakan:
   git commit -m "Configure Peminjaman Service testing"

Output akhir yang saya butuhkan:
- Ringkasan file yang ditambahkan/diubah.
- Hasil test.
- Commit hash yang berhasil dipush.
```

