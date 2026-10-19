# PRD — Projex: Website Project Management

**Versi:** 1.0 (MVP)  
**Tanggal:** 19 Oktober 2026  
**Status:** Draft untuk acuan implementasi  
**Aplikasi:** Projex (Laravel 13, PHP 8.3+, MySQL, Inertia v3, Vue 3, Tailwind CSS v4)

---

## 1. Project Overview

### Deskripsi

Projex adalah website project management sederhana yang dirancang untuk keperluan memantau progress pengerjaan project. Aplikasi memungkinkan pengguna membuat project, menambahkan tugas ke dalam project, mengelola anggota tim, dan melacak status setiap tugas dari awal hingga selesai. Aplikasi dibangun sebagai monolit web dengan Inertia.js dan Vue 3, tanpa API terpisah.

### Tujuan

- Membantu individu atau tim kecil dalam mengorganisir pekerjaan project.
- Menyediakan tempat terpusat untuk daftar project, tugas, dan progres pelaksanaan.
- Melacak status tugas (belum dikerjakan, sedang dikerjakan, selesai) agar tidak terlewat.

### Masalah yang Ingin Diselesaikan

- Tidak ada sistem terpusat untuk mencatat project
- Progres tugas sulit dipantau karena tersimpan secara terpisah (catatan fisik / file terpisah).
- Tidak ada klarifikasi siapa yang bertanggung jawab atas tugas tertentu.

---

## 2. Target Users & Roles

| Role                | Deskripsi                                         | Hak Akses                                                                                                                                                                                                 |
| ------------------- | ------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Pengguna (User)** | Orang yang terdaftar dan login ke Projex.         | Melihat daftar project miliknya, membuat/mengedit/menghapus project sendiri, membuat/mengedit tugas dalam project, mengubah status tugas yang ditugaskan, mengundang anggota ke project yang dimilikinya. |
| **Pemilik Project** | User yang membuat suatu project.                  | Penuh atas project tersebut: edit project, hapus project, kelola anggota, membuat dan mengelola semua tugas di dalamnya.                                                                                  |
| **Anggota Tim**     | User yang diundang ke dalam project oleh pemilik. | Melihat project dan tugas, mengubah status tugas yang ditugaskan kepadanya (asumsi).                                                                                                                      |

**Asumsi:** Pada MVP, tidak ada peran admin global atau role-based access control yang kompleks. Semua user memiliki hak dasar yang sama; pembedaan hanya pada pemilik project vs anggota.

---

## 3. Scope & MVP Features

### Fitur Wajib (Must Have)

1. **Autentikasi** — Register, login, logout. Hanya user terautentikasi yang dapat mengakses fitur aplikasi.
2. **Manajemen Project** — Buat, lihat, edit, hapus project (nama, deskripsi, tanggal mulai, tanggal selesai, status).
3. **Manajemen Tugas** — Buat, lihat, edit, hapus tugas dalam project (judul, deskripsi, status, prioritas, tanggal jatuh tempo, penanggung jawab).
4. **Anggota Tim** — Undang anggota ke project, lihat daftar anggota per project.
5. **Dashboard** — Ringkasan project milik user dan tugas menunggu / selesai.
6. **Detail Project** — Lihat semua tugas dan anggota dalam satu project.

### Fitur Opsional (Nice to Have — setelah MVP)

- Upload lampiran file pada tugas.
- Komentar pada tugas.
- Filter dan pencarian project / tugas.
- Notifikasi email (asumsi: memerlukan konfigurasi SMTP yang belum ada).
- Role-based access control (admin, manajer, anggota).
- Profil pengguna dan avatar.
- Reset password via email.
- Kanban board / export laporan.

---

## 4. Functional Requirements

### 4.1 Autentikasi

| ID    | Requirement                                                                                       | Ket                                               |
| ----- | ------------------------------------------------------------------------------------------------- | ------------------------------------------------- |
| FR-01 | Sistem menyediakan halaman register (nama, email, password, konfirmasi password).                 | Validasi email unik, password minimal 8 karakter. |
| FR-02 | Sistem menyediakan halaman login (email, password).                                               | Sesuai guard `web` session yang sudah ada.        |
| FR-03 | Setelah login, user diarahkan ke dashboard.                                                       | Route bernama `dashboard` (asumsi).               |
| FR-04 | User dapat logout dan dikembalikan ke halaman login.                                              | Session dihancurkan.                              |
| FR-05 | Halaman yang memerlukan login tidak dapat diakses oleh user yang belum login (middleware `auth`). | Proteksi route project, task, dashboard.          |

### 4.2 Manajemen Project

| ID    | Requirement                                                                       | Ket                                                                                  |
| ----- | --------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------ |
| FR-06 | User terautentikasi dapat membuat project baru.                                   | Field wajib: nama, deskripsi, tanggal mulai, tanggal selesai. Status default: aktif. |
| FR-07 | User dapat melihat daftar project miliknya di dashboard.                          | Diurutkan berdasarkan tanggal pembuatan terbaru.                                     |
| FR-08 | Pemilik project dapat mengedit project yang dimiliki.                             | Tidak dapat mengubah pemilik project (asumsi).                                       |
| FR-09 | Pemilik project dapat menghapus project yang dimiliki.                            | Semua tugas dan anggota dalam project ikut terhapus (cascade).                       |
| FR-10 | User hanya dapat mengakses project yang dimilikinya atau diikuti sebagai anggota. | Otorisasi di sisi server.                                                            |

### 4.3 Manajemen Tugas

| ID    | Requirement                                                                           | Ket                                                                                                                            |
| ----- | ------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------ |
| FR-11 | User dapat membuat tugas dalam project.                                               | Field wajib: judul, project_id, status (default: todo). Opsional: deskripsi, prioritas, tanggal jatuh tempo, penanggung jawab. |
| FR-12 | User dapat mengedit tugas yang ditugaskan kepadanya atau dalam project yang dimiliki. |                                                                                                                                |
| FR-13 | User dapat menghapus tugas.                                                           | Hanya pemilik project atau pembuat tugas (asumsi).                                                                             |
| FR-14 | Status tugas terbatas pada: `todo`, `in-progress`, `done`.                            | Enum / string terbatas.                                                                                                        |
| FR-15 | Prioritas tugas terbatas pada: `rendah`, `sedang`, `tinggi`.                          | Default: `sedang`.                                                                                                             |
| FR-16 | Daftar tugas dapat difilter berdasarkan status.                                       | Di halaman detail project.                                                                                                     |

### 4.4 Anggota Tim

| ID    | Requirement                                                              | Ket                                                          |
| ----- | ------------------------------------------------------------------------ | ------------------------------------------------------------ |
| FR-17 | Pemilik project dapat menambahkan anggota ke project berdasarkan email.  | User target harus sudah terdaftar di sistem (asumsi).        |
| FR-18 | Pemilik project dapat menghapus anggota dari project.                    | Tidak dapat menghapus diri sendiri sebagai pemilik (asumsi). |
| FR-19 | Anggota dapat melihat daftar anggota lain dalam project yang diikutinya. |                                                              |

---

## 5. User Flows

### Flow 1: Register dan Login

1. User membuka halaman login.
2. Jika belum punya akun, klik "Daftar" → halaman register.
3. Isi nama, email, password, konfirmasi password → submit.
4. Sistem membuat akun dan otomatis login.
5. Dialihkan ke dashboard.

### Flow 2: Membuat Project

1. Login → dashboard.
2. Klik "Buat Project Baru".
3. Isi nama, deskripsi, tanggal mulai, tanggal selesai → submit.
4. Project muncul di daftar dashboard.
5. Klik project → halaman detail project.

### Flow 3: Menambah Tugas

1. Di halaman detail project, klik "Tambah Tugas".
2. Isi judul, deskripsi, pilih prioritas, tanggal jatuh tempo, pilih penanggung jawab → submit.
3. Tugas muncul dalam daftar tugas project dengan status `todo`.

### Flow 4: Mengubah Status Tugas

1. Di daftar tugas, pilih tugas.
2. Ubah status dari dropdown: `todo` → `in-progress` → `done`.
3. Simpan. Status terupdate di daftar tugas.

### Flow 5: Mengundang Anggota

1. Di halaman detail project, buka tab "Anggota".
2. Masukkan email teman tim yang sudah terdaftar → klik "Undang".
3. Anggota muncul dalam daftar anggota project.

---

## 6. Non-Functional Requirements

| Kategori           | Requirement                                                                                                                                                                              |
| ------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Keamanan**       | Semua route kecuali login/register dilindungi middleware `auth`. Password disimpan dalam bentuk hash (bcrypt). Hanya pemilik project yang dapat mengedit/menghapus project dan tugasnya. |
| **Validasi**       | Validasi input di sisi server menggunakan Laravel Validation. Email unik, password minimal 8 karakter, tanggal selesai tidak boleh lebih awal dari tanggal mulai (asumsi).               |
| **Performa**       | Halaman utama (dashboard, daftar project) harus terbuka dalam 2 detik pada jaringan normal.                                                                                              |
| **Usability**      | Antarmuka dalam bahasa Indonesia (`APP_LOCALE=id`). Responsif untuk desktop dan mobile (Tailwind CSS). Satu elemen root di setiap komponen Vue (aturan Inertia + Vue).                   |
| **Kompatibilitas** | Berjalan di Laravel 13, PHP 8.3+, MySQL, Vue 3, Inertia v3.                                                                                                                              |
| **Pengujian**      | Setiap fitur utama diutami dengan feature test menggunakan Pest. Pengujian menggunakan database SQLite in-memory.                                                                        |

---

## 7. Data Model Overview

Berikut entitas utama yang akan dibuat (asumsi nama tabel, belum diimplementasikan di repository):

### 7.1 `users` (sudah ada)

- `id` (PK), `name`, `email` (unique), `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`.

### 7.2 `projects` (baru)

- `id` (PK), `user_id` (FK → users, pemilik), `name`, `description`, `start_date`, `end_date`, `status` (aktif/arsip), `created_at`, `updated_at`.

### 7.3 `tasks` (baru)

- `id` (PK), `project_id` (FK → projects), `title`, `description`, `status` (todo/in-progress/done), `priority` (rendah/sedang/tinggi), `due_date`, `assigned_to` (FK → users, nullable), `created_at`, `updated_at`.

### 7.4 `project_user` (baru, pivot many-to-many)

- `id` (PK), `project_id` (FK → projects), `user_id` (FK → users), `created_at`, `updated_at`.

**Relasi:**

- User has many Projects (pemilik).
- Project belongs to User (pemilik).
- Project has many Tasks.
- Task belongs to Project.
- Project belongs to many Users (anggota) melalui pivot `project_user`.
- User belongs to many Projects (anggotanya) melalui pivot `project_user`.
- Task belongs to User (penanggung jawab, opsional).

**Asumsi:** Nama tabel dan kolom di atas dapat disesuaikan saat implementasi migration.

---

## 8. UI/UX Guidelines

- Bahasa antarmuka: Indonesia.
- CSS framework: Tailwind CSS v4 (sudah terinstal).
- Komponen UI: Vue 3 Single File Components (`.vue`) di `resources/js/pages`.
- Setiap halaman: satu root element, layout konsisten melalui `app.blade.php` (Inertia root template).
- Warna dan tipografi: sesuai konfigurasi Tailwind yang ada (`resources/css/app.css`, font Instrument Sans via Vite).
- Navigasi: tautan ke dashboard, daftar project, buat project, detail project, login/logout.
- Responsif: tampilan harus nyaman di layar lebar dan layar sempit (mobile-first).
- Status tugas ditandai dengan warna (contoh: biru = todo, kuning = in-progress, hijau = done) — asumsi desain, dapat disesuaikan.
- Pesan flash (sukses / error) ditampilkan setelah aksi create / update / delete.

---

## 9. Technical Constraints

Berdasarkan kondisi repository aktual (hasil analisis):

1. **Tidak boleh menambahkan dependency baru** (Composer atau npm). Semua fitur harus dibangun dengan package yang sudah ada.
2. **Tidak ada authentication starter kit** (Breeze / Jetstream / Fortify). Autentikasi dibangun manual dengan controller, route, dan view Vue.
3. **Database production:** MySQL (`DB_CONNECTION=mysql`, database `projex`). Testing: SQLite in-memory.
4. **Hanya satu route yang ada:** `GET /` (home). Semua route baru (auth, project, task, anggota) harus ditambahkan di `routes/web.php`.
5. **`resources/js/pages` kosong.** Semua halaman Vue harus dibuat dari nol.
6. **Hanya model `User` yang ada.** Model `Project`, `Task`, dan pivot anggota harus dibuat baru beserta migration dan factory.
7. **Inertia SSR aktif** di `config/inertia.php`.
8. **Wayfinder aktif** untuk generate TypeScript route functions di `resources/js/routes/`.
9. **Pest PHP** untuk pengujian, **Laravel Pint** untuk formatting, **Larastan** level 7 untuk analisis statis.
10. **Bahasa aplikasi:** Indonesia (`APP_LOCALE=id`).
11. **Tidak ada fitur kompleks:** billing, chat real-time, AI assistant, atau integrasi eksternal (di luar scope MVP).

---

## 10. Acceptance Criteria

### Autentikasi

- [ ] User dapat register dengan data valid dan otomatis login.
- [ ] Register ditolak jika email sudah terdaftar atau password tidak cocok konfirmasi.
- [ ] User dapat login dengan email dan password yang benar.
- [ ] Login ditolak jika kredensial salah.
- [ ] User dapat logout dan tidak dapat mengakses halaman yang dilindungi setelah logout.

### Project

- [ ] User login dapat membuat project baru dengan nama, deskripsi, tanggal mulai, dan tanggal selesai.
- [ ] Project yang dibuat muncul di daftar dashboard milik pemiliknya.
- [ ] Pemilik project dapat mengedit dan menghapus projectnya.
- [ ] User tidak dapat melihat/mengedit project yang bukan miliknya atau diikuti.

### Tugas

- [ ] Tugas dapat dibuat dalam project dengan judul dan status default `todo`.
- [ ] Status tugas dapat diubah antara `todo`, `in-progress`, dan `done`.
- [ ] Tugas dapat ditugaskan kepada anggota project.
- [ ] Daftar tugas dapat difilter berdasarkan status.

### Anggota Tim

- [ ] Pemilik project dapat menambahkan anggota berdasarkan email yang terdaftar.
- [ ] Anggota terdaftar muncul dalam daftar anggota project.
- [ ] Pemilik project dapat menghapus anggota (kecuali dirinya sendiri).

### Umum

- [ ] Semua fitur utama memiliki feature test yang passing.
- [ ] Kode diformat dengan `pint` dan lulus analisis Larastan level 7.

---

## 11. Development Phases

| Fase                          | Fokus                                                                                   | Estimasi |
| ----------------------------- | --------------------------------------------------------------------------------------- | -------- |
| **Fase 1: Fondasi**           | Migration dan model `Project`, `Task`, `project_user`; factory dan seeder; route dasar. | 1 minggu |
| **Fase 2: Autentikasi**       | Register, login, logout, middleware `auth`, halaman login/register Vue.                 | 1 minggu |
| **Fase 3: Manajemen Project** | CRUD project, dashboard, detail project (Inertia + Vue).                                | 1 minggu |
| **Fase 4: Manajemen Tugas**   | CRUD tugas, perubahan status, filter status.                                            | 1 minggu |
| **Fase 5: Anggota Tim**       | Undang/hapus anggota, daftar anggota per project.                                       | 1 minggu |
| **Fase 6: Finishing**         | Uji coba end-to-end, perbaikan bug, formatting Pint, analisis Larastan, dokumentasi.    | 1 minggu |

**Total estimasi:** 6 minggu (asumsi, dapat disesuaikan).

---

## 12. Out of Scope (Tidak Dikerjakan di MVP)

- Billing / pembayaran / langganan.
- Chat real-time atau notifikasi push/WebSocket.
- AI assistant atau fitur cerdas lainnya.
- Integrasi eksternal (Slack, Google Calendar, GitHub, dll).
- Upload dan penyimpanan file lampiran.
- Komentar pada tugas.
- Role-based access control yang kompleks (admin, manajer, dll).
- Reset password via email.
- Export laporan (PDF / Excel).
- API publik (hanya web routes Inertia).
- Multi-tenansi atau organisasi.

---

## Keputusan Penting yang Masih Perlu Dikonfirmasi

1. **Nama tabel dan kolom baru** (`projects`, `tasks`, `project_user`, dst.) — saat ini adalah asumsi, belum ada migration di repository.
2. **Kebijakan penugasan tugas** — apakah hanya pemilik project yang dapat menugaskan, atau anggota juga dapat.
3. **Konfigurasi email** — `MAIL_MAILER=log` saat ini; fitur email (reset password, notifikasi) di luar scope MVP.
4. **Desain UI detail** — warna status tugas dan layout komponen belum ditentukan secara pasti.
