# CRM295 Revamp Plan - Marketing Flow Simplification

## 1. Tujuan Dokumen
Dokumen ini menjadi acuan resmi untuk rombak alur CRM295 agar sesuai kebutuhan terbaru tim marketing dan sales.

Tujuan utama sistem setelah revamp:
- Monitoring jumlah client.
- Monitoring penawaran yang sedang berjalan.
- Monitoring penawaran yang sudah deal.

## 2. Ringkasan Arah Baru
Alur bisnis baru dibuat lebih sederhana:
1. Client chat menanyakan produk.
2. Jika tertarik, dicatat sebagai calon klien.
3. Jika lanjut, status menjadi klien.
4. Dibuat penawaran pertama.
5. Jika nego, quotation yang sama diupdate, bukan membuat quotation baru.
6. Jika setuju, hasil menjadi deal.
7. Jika batal, hasil menjadi batal dan transaksi tidak lanjut.

Prinsip utama:
- Satu lead aktif memiliki satu quotation aktif.
- Negosiasi adalah update nilai pada quotation yang sama.
- Fokus sistem adalah monitoring, bukan alur dokumen finansial kompleks.

## 3. Scope Revamp
Dalam scope:
- Penyederhanaan status lead/client.
- Penyederhanaan status quotation.
- Perubahan UI halaman lead detail dan quotation workflow.
- Perubahan KPI dashboard dan report.
- Perubahan export agar sesuai KPI baru.
- Penyesuaian API dan validasi bisnis.
- Penambahan test end-to-end untuk flow baru.

Di luar scope saat ini:
- Integrasi invoice dan accounting.
- Integrasi notifikasi WhatsApp real-time dua arah.
- Multi-currency pricing.
- Approval bertingkat antar role.

## 4. Definisi Status Baru
### 4.1 Status Entitas Client
Usulan status client:
- chat_masuk
- calon_klien
- klien
- deal
- batal

Definisi singkat:
- chat_masuk: inquiry awal dari chat.
- calon_klien: sudah tertarik dan layak diprospek.
- klien: sudah masuk tahap penawaran aktif.
- deal: quotation disetujui.
- batal: tidak jadi transaksi.

### 4.2 Status Entitas Quotation
Usulan status quotation:
- berjalan
- nego
- deal
- batal

Definisi singkat:
- berjalan: penawaran pertama dikirim.
- nego: ada proses negosiasi harga.
- deal: penawaran disetujui.
- batal: penawaran ditolak atau dibatalkan.

## 5. Business Rules Inti
1. Satu lead hanya boleh memiliki satu quotation dengan status berjalan atau nego.
2. Aksi nego harus mengupdate quotation yang aktif.
3. Aksi deal pada quotation otomatis mengubah status client menjadi deal.
4. Aksi batal pada quotation otomatis mengubah status client menjadi batal, jika disepakati bisnis.
5. Jika quotation deal atau batal, quotation dianggap selesai.
6. Nilai quotation dan hpp wajib tersimpan untuk kebutuhan monitoring margin.

## 6. Dampak Data dan Migrasi
### 6.1 Dampak Model/Tabel
Area yang terdampak:
- leads.status
- quotations.status
- histories status lead
- report aggregation query

### 6.2 Strategi Migrasi Data
Strategi yang direkomendasikan:
1. Fase transisi: data lama tetap valid, data baru mengikuti status baru.
2. Mapping status lama ke baru disimpan dalam dokumen migrasi.
3. Setelah stabil, lakukan migrasi penuh jika diperlukan.

### 6.3 Mapping Status Lama ke Baru
Usulan mapping awal:
- Cold -> chat_masuk
- Warm -> calon_klien
- Hot -> klien
- Deal -> deal
- Lost -> batal

Catatan:
- Mapping ini wajib disetujui stakeholder sebelum implementasi migrasi massal.

## 7. Dampak UI dan UX
### 7.1 Halaman Leads List
Perubahan:
- Fokus kolom pada nama client, status, quotation terakhir, nilai quotation, assigned sales.
- Filter diprioritaskan ke status baru dan kondisi quotation aktif.
- Tambahan indikator cepat: berjalan, nego, deal, batal.

### 7.2 Halaman Lead Detail
Perubahan:
- Panel ringkas profil client.
- Panel quotation tunggal sebagai pusat aksi.
- Aksi utama jelas: update ke nego, update harga, set deal, set batal.
- Riwayat update quotation ditampilkan sebagai timeline singkat.

### 7.3 Halaman Quotations
Perubahan:
- Bukan lagi daftar multi quotation per lead.
- Menjadi monitoring quotation aktif dan selesai.
- Tombol create quotation baru dibatasi bila quotation aktif sudah ada.

## 8. Dampak Dashboard dan Reports
### 8.1 KPI Dashboard Baru
KPI minimum:
- Total client aktif.
- Total quotation berjalan.
- Total quotation deal.
- Nilai total quotation.
- Nilai total deal.
- Total hpp deal.
- Estimasi margin deal.

### 8.2 Reports Baru
Laporan minimum:
- Daftar quotation berjalan.
- Daftar quotation deal.
- Daftar quotation batal.
- Rekap nilai quotation, nilai deal, hpp, margin.
- Rekap per sales.

### 8.3 Export CSV
Export yang dipertahankan:
- Export lead/client summary.
- Export sales monthly summary.

Kolom minimum export:
- client
- sales
- status client
- status quotation
- quotation_value
- deal_value
- hpp_value
- margin_value
- updated_at

## 9. Dampak API dan Validasi
### 9.1 API Rules
Perubahan utama:
- Endpoint update quotation harus mendukung mode nego (update harga).
- Endpoint create quotation menolak jika quotation aktif sudah ada.
- Endpoint set deal dan set batal harus menutup quotation aktif.

### 9.2 Validasi Penting
Validasi minimum:
- status harus hanya dari daftar status baru.
- nilai quotation dan hpp harus numerik non-negatif.
- tidak boleh ada dua quotation aktif pada lead yang sama.

## 10. Rencana Implementasi Bertahap
### Sprint 1 - Business Alignment dan Data Contract
Deliverables:
- Status final dan rule final disepakati.
- Dokumen mapping status final.
- Draft migration plan.
- Draft API contract final.

### Sprint 2 - Domain dan Backend
Deliverables:
- Update model, rule validasi, service logic.
- Implementasi rule quotation tunggal aktif.
- Sinkronisasi status quotation ke status client.

### Sprint 3 - UI/UX Revamp
Deliverables:
- Update halaman lead list.
- Update halaman lead detail.
- Update halaman quotation monitoring.
- Update dashboard card sesuai KPI baru.

### Sprint 4 - Reports, Export, QA
Deliverables:
- Update seluruh query report.
- Update format export CSV.
- Feature test dan regression test.
- UAT bersama marketing dan sales.

### Sprint 5 - Rollout
Deliverables:
- Release bertahap ke user terbatas.
- Monitoring bug dan feedback.
- Final rollout ke semua user.

## 11. Test Plan
Test minimum yang wajib lulus:
1. Client baru masuk sampai status calon_klien.
2. Calon_klien menjadi klien dan membuat quotation pertama.
3. Nego mengubah quotation yang sama, bukan create quotation baru.
4. Deal menutup quotation dan mengubah status client ke deal.
5. Batal menutup quotation dan mengubah status client ke batal.
6. Report menampilkan metrik quotation, deal, hpp dengan benar.
7. Export menghasilkan kolom baru yang disepakati.

## 12. Risiko Utama dan Mitigasi
Risiko:
- Ambiguitas definisi status antar tim.
- Data lama tidak sesuai dengan rule quotation tunggal.
- User tetap membuat proses manual di luar alur sistem.
- Laporan menjadi tidak konsisten saat masa transisi.

Mitigasi:
- Kunci definisi status melalui sign-off tertulis.
- Jalankan fase transisi data sebelum migrasi penuh.
- Sediakan SOP singkat per halaman.
- Jalankan UAT berbasis skenario nyata sebelum go live.

## 13. Keputusan yang Harus Disetujui
Daftar keputusan wajib sign-off:
1. Daftar status final client.
2. Daftar status final quotation.
3. Aturan status client saat quotation batal.
4. Mapping data lama ke status baru.
5. KPI final dashboard.
6. Kolom final export CSV.

## 14. Checklist Siap Mulai Implementasi
Checklist:
- Status dan rule final disetujui marketing dan sales.
- Mapping migrasi disetujui product owner.
- Desain UI prioritas disetujui user utama.
- Daftar endpoint dan payload final disepakati.
- Skenario UAT disiapkan.

## 15. Lampiran Notasi Flow Singkat
Flow final operasional:
1. chat_masuk -> calon_klien
2. calon_klien -> klien
3. klien -> quotation berjalan
4. quotation berjalan <-> quotation nego
5. quotation nego -> quotation deal -> client deal
6. quotation berjalan atau quotation nego -> quotation batal -> client batal

---

Dokumen ini menjadi baseline revamp. Semua perubahan teknis, task sprint, dan testing harus merujuk ke dokumen ini sampai ada revisi versi berikutnya.
