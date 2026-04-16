# CRM295 - Lead & Prospect Management

CRM295 adalah aplikasi CRM sederhana berbasis Laravel 12 untuk tracking prospek sales dari tahap Cold sampai Deal/Lost.

## Fitur Utama

- Lead management: create, list, detail, edit, delete
- Activity tracking: histori call/meeting/WA/email + next follow up
- Quotation tracking: pending, nego, accepted, rejected
- Status lifecycle: Cold, Warm, Hot, Deal, Lost
- Follow-up tasks: due, overdue, today
- Dashboard KPI operasional
- Reporting:
  - Closing vs Lost per bulan
  - Performa per sales
  - Top client berdasarkan nilai quotation
  - Funnel conversion rate (Cold->Warm, Warm->Hot, Hot->Deal)
  - Export lead CSV
  - Export sales monthly CSV

## Stack

- PHP 8.2+
- Laravel 12
- Sanctum
- SQLite/MySQL (testing default sqlite in-memory)

## Setup Cepat

1. Install dependency

```bash
composer install
```

2. Buat environment file

```bash
copy .env.example .env
php artisan key:generate
```

3. Migrasi dan seeding

```bash
php artisan migrate
php artisan db:seed
```

4. Jalankan aplikasi

```bash
php artisan serve
```

Akses aplikasi di `http://127.0.0.1:8000`.

## Akun Seed Default

Seeder membuat akun user awal:

- superadmin@295.com / Rewdcxz@295
- admin@295.com / Rewdcxz@admin
- sales1@295.com / Rewdcxz@sales
- sales2@295.com / Rewdcxz@sales

Catatan: fitur CRUD user login hanya dapat diakses oleh role `superadmin` melalui menu `Users`.

## Halaman Web

Semua halaman web CRM sekarang dilindungi login session (`auth` middleware). Guest akan diarahkan ke `/login`.

- `/dashboard`
- `/leads`
- `/activities`
- `/quotations`
- `/follow-ups`
- `/reports`
- `/login`

Halaman reports mendukung filter:

- Year
- Sales
- Status lead
- Sumber lead
- Date range (from-to)

## API Ringkas

Base path: `/api`

- `GET /api/dashboard`
- `GET /api/reports/summary`
- `GET /api/reports/sales-monthly`
- `GET /api/reports/funnel-conversion`
- `GET /api/reports/followups-health`
- CRUD `/api/leads`
- CRUD `/api/activities`
- CRUD `/api/quotations`

Catatan:

- API dilindungi `auth:sanctum`
- Quotation hanya dapat dibuat untuk lead status `Hot`
- Quotation `accepted` akan otomatis mengubah lead menjadi `Deal`
- Perubahan status lead tercatat ke `lead_status_histories`

Contoh query API reports:

- `year`, `sales_id`, `status`, `sumber_lead`
- `from_date` dan `to_date` untuk date-range

## Reminder Overdue Follow-up

Tersedia command reminder digest overdue:

```bash
php artisan crm:followups:overdue-digest
```

Command ini dijadwalkan harian pukul `08:00` dan melakukan:

- Rekap overdue follow-up per sales
- Logging digest ke aplikasi
- Pengiriman email digest ke email sales (jika ada)
- Pengiriman payload webhook (WA placeholder) bila `CRM_FOLLOWUP_WEBHOOK_URL` diisi
- Retry webhook otomatis 2x bila request gagal sementara

Contoh payload webhook:

```json
{
  "channel": "wa-placeholder",
  "type": "overdue_followup_digest",
  "sales": {
    "id": 2,
    "name": "Sales 1",
    "email": "sales1@crm.test"
  },
  "overdue_count": 3,
  "message": "Reminder overdue follow-up ...",
  "sent_at": "2026-04-06T08:00:00+07:00"
}
```

Contoh simulasi webhook lokal dengan endpoint mock:

```bash
php artisan crm:followups:overdue-digest --date=2026-04-06
```

Set env berikut untuk mengarahkan payload ke webhook gateway/placeholder:

```env
CRM_FOLLOWUP_WEBHOOK_URL=https://your-webhook-endpoint.test/crm/followups
```

Untuk menjalankan scheduler di environment lokal/dev:

```bash
php artisan schedule:work
```

## Workflow Bisnis

1. Input lead baru
2. Follow up melalui activity
3. Ubah status lead Cold -> Warm -> Hot
4. Tambahkan quotation pada lead Hot
5. Jika quotation accepted, lead pindah ke Deal otomatis
6. Pantau overdue follow up di Follow Up Tasks
7. Pantau performa di Reports

## Menjalankan Test

```bash
php artisan test
```

Saat ini mencakup test API workflow inti dan test web report/export.
Termasuk juga test autentikasi web (login, logout, proteksi guest).
Termasuk test security rate-limit untuk login dan API write endpoint (assert HTTP 429).

## Security Hardening (Production)

Sebelum deploy production, pastikan nilai environment ini sudah aman:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.tld

SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

SANCTUM_EXPIRATION=120
```

Catatan:

- Web app sekarang mengirim security headers (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy).
- Header HSTS dikirim otomatis saat request menggunakan HTTPS.
- Kontrol akses data sudah menerapkan policy ownership: user sales hanya dapat mengakses data lead/activity/quotation miliknya, admin dapat melihat semua.
- Rate limiting aktif:
  - Login POST: 20 request/menit per IP (`throttle:login`) + validasi login internal (5 percobaan gagal per email+IP).
  - API read endpoint: admin 240/menit, sales 120/menit (`throttle:api-read`).
  - API write endpoint: admin 120/menit, sales 60/menit (`throttle:api-write`).

## Struktur Modul Inti

- Leads
- Activities
- Quotations
- Lead Status Histories
- Reports

Dokumen tambahan blueprint MVP tersedia di `docs/mvp-blueprint.md`.
Dokumen acuan rombak alur marketing tersedia di `docs/marketing-flow-revamp-plan.md`.

Panduan deploy VPS tanpa Node dengan best practice (Pola B) tersedia di `docs/deploy-pattern-b.md`.
Alternatif build-artifact workflow tersedia di `docs/deploy-pattern-a.md`.
