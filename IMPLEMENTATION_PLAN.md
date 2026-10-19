# IMPLEMENTATION_PLAN — Projex: Website Project Management

**Versi:** 1.0  
**Tanggal:** 19 Oktober 2026  
**Status:** Rencana implementasi (belum ada kode yang diubah)  
**Dasar:** PRD.md versi 1.0, kondisi repository aktual Projex  
**Stack:** Laravel 13, PHP 8.3+, MySQL, Inertia v3, Vue 3, Tailwind CSS v4, Pest, Pint, Larastan

---

## 1. Analisis Keadaan Project vs PRD

### Kondisi repository saat ini (hasil pemeriksaan)

| Item                 | Kondisi saat ini                                     | Kebutuhan PRD                               | Status            |
| -------------------- | ---------------------------------------------------- | ------------------------------------------- | ----------------- |
| Route                | Hanya `GET /` (home)                                 | Auth, project, task, anggota, dashboard     | Belum ada         |
| Model                | Hanya `App\Models\User`                              | Project, Task, pivot anggota                | Belum ada         |
| Migration            | `users`, `password_reset_tokens`, `sessions`         | `projects`, `tasks`, `project_user`         | Belum ada         |
| Factory              | Hanya `UserFactory`                                  | ProjectFactory, TaskFactory                 | Belum ada         |
| Seeder               | `DatabaseSeeder` (1 user uji)                        | Data awal project/task (opsional)           | Belum ada         |
| `resources/js/pages` | Kosong                                               | Login, Register, Dashboard, Project*, Task* | Belum ada         |
| Auth starter kit     | Tidak ada (Breeze/Jetstream/Fortify tidak terpasang) | Register, login, logout manual              | Belum ada         |
| Middleware `auth`    | Tersedia di Laravel, belum diterapkan pada route     | Proteksi route selain login/register        | Belum diterapkan  |
| Testing              | Pest, `RefreshDatabase`, SQLite in-memory            | Feature test setiap fitur utama             | Hanya ExampleTest |

### Kebutuhan yang belum diimplementasikan

1. **Database:** Migration dan model untuk `projects`, `tasks`, `project_user`.
2. **Autentikasi:** Controller auth, route login/register/logout, halaman Vue, middleware `auth`.
3. **Manajemen project:** Controller, route, halaman Vue (dashboard, daftar, buat, edit, detail, hapus).
4. **Manajemen tugas:** Controller, route, halaman Vue (buat, edit, ubah status, hapus, filter).
5. **Anggota tim:** Controller, route, halaman Vue (undang, lihat daftar, hapus).
6. **Testing:** Feature test untuk auth, project, task, anggota.
7. **Wayfinder:** Regenerate TypeScript route functions setelah route baru ditambahkan.

---

## 2. Rancangan Database & Relasi

### Tabel yang sudah ada (tidak diubah)

**`users`**

- `id` (bigIncrements, PK)
- `name` (string)
- `email` (string, unique)
- `email_verified_at` (timestamp, nullable)
- `password` (string)
- `remember_token` (string, nullable)
- `created_at`, `updated_at` (timestamps)

### Tabel baru yang akan dibuat

#### `projects`

| Kolom                      | Tipe                          | Keterangan                               |
| -------------------------- | ----------------------------- | ---------------------------------------- |
| `id`                       | bigIncrements                 | PK                                       |
| `user_id`                  | bigInteger, unsigned, indexed | FK → `users.id` (pemilik project)        |
| `name`                     | string                        | Nama project                             |
| `description`              | text, nullable                | Deskripsi project                        |
| `start_date`               | date                          | Tanggal mulai                            |
| `end_date`                 | date                          | Tanggal selesai                          |
| `status`                   | string                        | `'aktif'` / `'arsip'`, default `'aktif'` |
| `created_at`, `updated_at` | timestamps                    |                                          |

#### `tasks`

| Kolom                      | Tipe                                    | Keterangan                                               |
| -------------------------- | --------------------------------------- | -------------------------------------------------------- |
| `id`                       | bigIncrements                           | PK                                                       |
| `project_id`               | bigInteger, unsigned, indexed           | FK → `projects.id`                                       |
| `title`                    | string                                  | Judul tugas                                              |
| `description`              | text, nullable                          | Deskripsi tugas                                          |
| `status`                   | string                                  | `'todo'` / `'in-progress'` / `'done'`, default `'todo'`  |
| `priority`                 | string                                  | `'rendah'` / `'sedang'` / `'tinggi'`, default `'sedang'` |
| `due_date`                 | date, nullable                          | Tanggal jatuh tempo                                      |
| `assigned_to`              | bigInteger, unsigned, nullable, indexed | FK → `users.id` (penanggung jawab)                       |
| `created_at`, `updated_at` | timestamps                              |                                                          |

#### `project_user` (pivot many-to-many)

| Kolom                      | Tipe                          | Keterangan         |
| -------------------------- | ----------------------------- | ------------------ |
| `id`                       | bigIncrements                 | PK                 |
| `project_id`               | bigInteger, unsigned, indexed | FK → `projects.id` |
| `user_id`                  | bigInteger, unsigned, indexed | FK → `users.id`    |
| `created_at`, `updated_at` | timestamps                    |                    |

### Relasi Eloquen (asumsi nama, perlu konfirmasi saat implementasi)

- `User` hasMany `Project` (pemilik) — melalui `user_id` di `projects`
- `Project` belongsTo `User` (pemilik)
- `Project` hasMany `Task`
- `Task` belongsTo `Project`
- `Project` belongsToMany `User` (anggota) melalui pivot `project_user`
- `User` belongsToMany `Project` (project diikuti sebagai anggota) melalui pivot `project_user`
- `Task` belongsTo `User` (penanggung jawab, opsional) — melalui `assigned_to`

### Catatan

- Nama tabel dan kolom di atas adalah asumsi dari PRD.md. Belum ada migration di repository, perlu konfirmasi sebelum dibuat.
- Cascade delete: saat project dihapus, semua tugas dan anggota dalam project ikut terhapus.

---

## 3. Urutan Implementasi (6 Fase)

### Fase 1: Fondasi Database & Model (Estimasi 1 minggu)

**Tujuan:** Siapkan tabel, model, factory, dan seeder dasar untuk project, task, dan anggota.

**Aksi:**

1. Buat migration `create_projects_table`.
2. Buat migration `create_tasks_table`.
3. Buat migration `create_project_user_table`.
4. Buat model `Project` dengan relasi (belongsTo User, hasMany Task, belongsToMany User).
5. Buat model `Task` dengan relasi (belongsTo Project, belongsTo User).
6. Buat `ProjectFactory` dan `TaskFactory`.
7. Perbarui `DatabaseSeeder` untuk data awal (opsional, sesuai kebutuhan pengembang).
8. Tambahkan route dasar (placeholder) di `routes/web.php` untuk verifikasi.
9. Jalankan `php artisan migrate` di development (MySQL) untuk verifikasi struktur.
10. Jalankan `vendor/bin/pint --format agent` dan `vendor/bin/phpstan analyse` untuk memastikan kode baru sesuai standar.

**Output yang diharapkan:** Tabel database siap, model Eloquen dengan relasi siap, factory siap digunakan untuk testing.

### Fase 2: Autentikasi (Estimasi 1 minggu)

**Tujuan:** User dapat register, login, dan logout. Hanya user terautentikasi yang dapat mengakses aplikasi.

**Aksi:**

1. Buat `AuthController` dengan metode: `register`, `login`, `logout`.
2. Tambahkan route `register`, `login`, `logout` di `routes/web.php`.
3. Buat halaman Vue: `Login.vue`, `Register.vue` di `resources/js/pages`.
4. Terapkan middleware `auth` pada route yang membutuhkan login (project, task, dashboard, anggota).
5. Atur redirect post-login ke route `dashboard` (nama route perlu ditambahkan).
6. Tambahkan flash message untuk sukses/error autentikasi.
7. Buat feature test: `AuthFeatureTest` (register, login, logout, proteksi route).
8. Regenerate Wayfinder TypeScript route functions (`php artisan wayfinder:generate` jika tersedia, atau perbarui `resources/js/routes/` secara manual sesuai konvensi project).
9. Jalankan `vendor/bin/pint --format agent` dan `vendor/bin/phpstan analyse`.
10. Jalankan `vendor/bin/pest` untuk memastikan test lulus.

**Output yang diharapkan:** Halaman login/register berfungsi, middleware `auth` melindungi route, feature test passing.

### Fase 3: Manajemen Project (Estimasi 1 minggu)

**Tujuan:** User dapat membuat, melihat, mengedit, dan menghapus project. Dashboard menampilkan daftar project milik user.

**Aksi:**

1. Buat `ProjectController` dengan metode: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.
2. Tambahkan route project (resource atau individual) di `routes/web.php`, dilindungi middleware `auth`.
3. Buat halaman Vue: `Dashboard.vue`, `ProjectsIndex.vue`, `ProjectCreate.vue`, `ProjectEdit.vue`, `ProjectShow.vue`.
4. Implementasi otorisasi: hanya pemilik project yang dapat edit/hapus project (middleware atau policy sederhana).
5. Implementasi cascade delete: menghapus project akan menghapus semua tugas dan anggota dalam project tersebut.
6. Buat feature test: `ProjectFeatureTest` (CRUD project, otorisasi pemilik vs anggota).
7. Regenerate Wayfinder TypeScript route functions.
8. Jalankan `vendor/bin/pint --format agent` dan `vendor/bin/phpstan analyse`.
9. Jalankan `vendor/bin/pest`.

**Output yang diharapkan:** CRUD project berfungsi, dashboard menampilkan project milik user, detail project menampilkan tugas dan anggota, feature test passing.

### Fase 4: Manajemen Tugas (Estimasi 1 minggu)

**Tujuan:** User dapat membuat, melihat, mengedit, menghapus tugas, dan mengubah status tugas dalam project.

**Aksi:**

1. Buat `TaskController` dengan metode: `store`, `update`, `destroy`, `updateStatus` (atau gabungkan dalam update).
2. Tambahkan route task di `routes/web.php` (nested under project atau terpisah), dilindungi middleware `auth`.
3. Tambahkan komponen Vue untuk form tugas dan daftar tugas (di dalam `ProjectShow.vue` atau halaman terpisah).
4. Implementasi status tugas: `todo`, `in-progress`, `done`.
5. Implementasi prioritas tugas: `rendah`, `sedang`, `tinggi` (default `sedang`).
6. Implementasi filter tugas berdasarkan status di halaman detail project.
7. Implementasi penugasan tugas kepada anggota project (`assigned_to`).
8. Buat feature test: `TaskFeatureTest` (CRUD task, perubahan status, filter, penugasan).
9. Regenerate Wayfinder TypeScript route functions.
10. Jalankan `vendor/bin/pint --format agent` dan `vendor/bin/phpstan analyse`.
11. Jalankan `vendor/bin/pest`.

**Output yang diharapkan:** CRUD tugas berfungsi, status dapat diubah, filter per status berfungsi, feature test passing.

### Fase 5: Anggota Tim (Estimasi 1 minggu)

**Tujuan:** Pemilik project dapat mengundang dan menghapus anggota, serta melihat daftar anggota per project.

**Aksi:**

1. Buat `ProjectMemberController` dengan metode: `store`, `destroy`.
2. Tambahkan route untuk undang/hapus anggota di `routes/web.php`, dilindungi middleware `auth`.
3. Tambahkan tab "Anggota" di halaman detail project (`ProjectShow.vue`).
4. Implementasi validasi: email target harus sudah terdaftar di sistem.
5. Implementasi pembatasan: pemilik tidak dapat menghapus diri sendiri sebagai pemilik.
6. Buat feature test: `ProjectMemberFeatureTest` (undang, lihat daftar, hapus anggota).
7. Regenerate Wayfinder TypeScript route functions.
8. Jalankan `vendor/bin/pint --format agent` dan `vendor/bin/phpstan analyse`.
9. Jalankan `vendor/bin/pest`.

**Output yang diharapkan:** Undang/hapus anggota berfungsi, daftar anggota ditampilkan, pembatasan pemilik berlaku, feature test passing.

### Fase 6: Finishing (Estimasi 1 minggu)

**Tujuan:** Memastikan semua fitur berfungsi end-to-end, kode sesuai standar, dan dokumentasi lengkap.

**Aksi:**

1. Uji coba end-to-end semua user flow dari PRD.md (Flow 1 s/d Flow 5).
2. Perbaikan bug yang ditemukan selama uji coba.
3. Jalankan `vendor/bin/pint --format agent` untuk seluruh PHP yang diubah.
4. Jalankan `vendor/bin/phpstan analyse` (Larastan level 7) dan perbaiki kesalahan statis.
5. Jalankan seluruh test suite: `vendor/bin/pest`.
6. Verifikasi halaman Vue satu root elemen per komponen (aturan Inertia + Vue).
7. Verifikasi responsivitas dan bahasa Indonesia di seluruh antarmuka.
8. Dokumentasi akhir (catatan implementasi, keputusan yang diambil).

**Output yang diharapkan:** MVP selesai, semua feature test passing, kode sesuai Pint dan Larastan, dokumentasi lengkap.

**Total estimasi:** 6 minggu (asumsi dari PRD.md, dapat disesuaikan).

---

## 4. Strategi Testing

### Konvensi Pest (sesuai AGENTS.md)

- Buat test dengan `php artisan make:test --pest {NamaTest}`.
- Jangan sertakan direktori suite dalam nama (contoh: `AuthFeatureTest`, bukan `Feature/AuthFeatureTest`).
- Gunakan factory untuk membuat model dalam test.
- Feature test diutami dibanding unit test.
- Jalankan test terperincı: `vendor/bin/pest --filter=TestNama` atau `vendor/bin/pest path/to/test.php`.
- Jalankan seluruh suite setelah selesai: `vendor/bin/pest`.

### Konfigurasi testing (sudah ada)

- `tests/Pest.php`: menggunakan `RefreshDatabase` untuk Feature, SQLite in-memory (`DB_DATABASE=:memory:`).
- `phpunit.xml`: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `MAIL_MAILER=array`, `QUEUE_CONNECTION=sync`, `SESSION_DRIVER=array`.

### Rencana file test

| File test                  | Cakupan                                                                                   |
| -------------------------- | ----------------------------------------------------------------------------------------- |
| `AuthFeatureTest`          | Register (sukses/gagal), login (sukses/gagal), logout, proteksi route tanpa login         |
| `ProjectFeatureTest`       | Buat project, lihat daftar, edit, hapus, otorisasi pemilik vs non-pemilik, cascade delete |
| `TaskFeatureTest`          | Buat tugas, edit, hapus, ubah status, filter status, penugasan anggota                    |
| `ProjectMemberFeatureTest` | Undang anggota, lihat daftar anggota, hapus anggota, pembatasan pemilik                   |

### Strategi data uji

- Menggunakan `RefreshDatabase` sehingga setiap test berjalan dengan database bersih.
- Menggunakan factory (`User::factory()`, `Project::factory()`, `Task::factory()`) untuk membuat data uji.
- Tidak mengandalkan seeder untuk data uji otomatis (kecuali data dummy sengaja).

---

## 5. Catatan & Keputusan yang Masih Perlu Dikonfirmasi

Berikut item yang tidak dapat dipastikan tanpa konfirmasi lebih lanjut, dan tidak boleh ditebak selama implementasi:

1. **Nama tabel dan kolom baru** (`projects`, `tasks`, `project_user`, dst.) — saat ini asumsi dari PRD.md, belum ada migration di repository.
2. **Kebijakan penugasan tugas** — apakah hanya pemilik project yang dapat menugaskan tugas, atau anggota juga dapat (PRD.md FR-12/FR-13 masih netral).
3. **Konfigurasi email** — `MAIL_MAILER=log` saat ini; fitur email (reset password, notifikasi) di luar scope MVP per PRD.md.
4. **Desain UI detail** — warna status tugas dan layout komponen belum ditentukan secara pasti (PRD.md menyebut asumsi).
5. **Nama route** — PRD.md menyebut route `dashboard` sebagai asumsi, belum ada di `routes/web.php`.
6. **Nama model** — `Project`, `Task` adalah asumsi, perlu disesuaikan dengan konvensi penamaan tim.

---

## 6. Batasan & Aturan Implementasi

- **Tidak menambah dependency baru** (Composer maupun npm). Semua fitur menggunakan package yang sudah ada.
- **Tidak menggunakan authentication starter kit** (Breeze/Jetstream/Fortify). Autentikasi dibangun manual dengan controller, route, dan view Vue.
- **Database production:** MySQL (`DB_CONNECTION=mysql`, database `projex`). Testing: SQLite in-memory.
- **Inertia SSR aktif** di `config/inertia.php`.
- **Wayfinder aktif** untuk generate TypeScript route functions di `resources/js/routes/`.
- **Pest PHP** untuk pengujian, **Laravel Pint** untuk formatting, **Larastan level 7** untuk analisis statis.
- **Bahasa aplikasi:** Indonesia (`APP_LOCALE=id`).
- **Setiap komponen Vue:** satu root elemen, di `resources/js/pages`.
- **Tidak ada fitur di luar scope MVP** (billing, chat real-time, AI, upload file, komentar, RBAC kompleks, reset password email, export laporan, API publik).

---

## 7. Output yang Diharapkan Setelah Implementasi Selesai

- Tabel database: `users` (ada), `projects`, `tasks`, `project_user` (baru).
- Model: `User` (ada), `Project`, `Task` (baru).
- Route: auth (`register`, `login`, `logout`), project (CRUD), task (CRUD + status), anggota (undang/hapus), dashboard.
- Halaman Vue: Login, Register, Dashboard, ProjectsIndex, ProjectCreate, ProjectEdit, ProjectShow.
- Controller: `AuthController`, `ProjectController`, `TaskController`, `ProjectMemberController`.
- Factory: `UserFactory` (ada), `ProjectFactory`, `TaskFactory` (baru).
- Feature test: `AuthFeatureTest`, `ProjectFeatureTest`, `TaskFeatureTest`, `ProjectMemberFeatureTest`.
- Kode diformat dengan Pint, lulus analisis Larastan level 7, semua test passing.
