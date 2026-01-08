# Aplikasi Job Delegation

Aplikasi untuk membuat dan memantau delegasi pekerjaan (job delegation) dengan fitur tracking progress, dibuat menggunakan Laravel dan Breeze.

## Fitur

- ✅ **Manajemen Task**: Buat, edit, dan hapus tasks
- ✅ **Delegasi Pekerjaan**: Delegasikan tasks ke user lain
- ✅ **Tracking Progress**: Pantau progress pekerjaan dengan update real-time
- ✅ **Dashboard**: Overview semua tasks dan delegasi
- ✅ **Status Management**: Kelola status task dan delegasi (pending, in_progress, completed, dll)
- ✅ **Priority System**: Set prioritas task (low, medium, high)
- ✅ **Progress Updates**: Update progress dengan catatan dan history

## Instalasi

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Konfigurasi Database**
   Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jobdel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan Migration**
   ```bash
   php artisan migrate
   ```

5. **Build Assets**
   ```bash
   npm run build
   ```

6. **Jalankan Server**
   ```bash
   php artisan serve
   ```

## Penggunaan

### 1. Registrasi dan Login
- Buka aplikasi di browser
- Daftar akun baru atau login dengan akun yang sudah ada
- Setelah login, Anda akan diarahkan ke Dashboard

### 2. Membuat Task
- Klik menu **Tasks** atau tombol **Buat Task Baru** di dashboard
- Isi form:
  - Judul Task (wajib)
  - Deskripsi (opsional)
  - Prioritas (low, medium, high)
  - Tanggal Jatuh Tempo (opsional)
  - Delegasikan ke user lain (opsional)
- Klik **Buat Task**

### 3. Delegasi Task
- Dari halaman detail task, klik **Tambah Delegasi**
- Pilih user yang akan menerima delegasi
- Tambahkan catatan jika diperlukan
- Klik **Buat Delegasi**

### 4. Menerima/Menolak Delegasi
- Buka menu **Delegations** untuk melihat delegasi yang ditujukan untuk Anda
- Klik pada delegasi untuk melihat detail
- Klik **Terima Delegasi** atau **Tolak Delegasi**

### 5. Update Progress
- Buka detail delegasi yang sudah diterima
- Di bagian "Update Progress", masukkan:
  - Progress percentage (0-100%)
  - Catatan (opsional)
- Klik **Update Progress**
- History update progress akan ditampilkan di bawah

### 6. Menyelesaikan Task
- Setelah progress mencapai 100%, klik **Tandai Selesai**
- Atau update progress ke 100% dan sistem akan otomatis menandai sebagai selesai

## Struktur Database

### Tabel `tasks`
- `id`: Primary key
- `title`: Judul task
- `description`: Deskripsi task
- `priority`: Prioritas (low, medium, high)
- `status`: Status (pending, in_progress, completed, cancelled)
- `due_date`: Tanggal jatuh tempo
- `created_by`: User yang membuat task
- `timestamps`

### Tabel `delegations`
- `id`: Primary key
- `task_id`: ID task yang didelegasikan
- `delegated_to`: User yang menerima delegasi
- `delegated_by`: User yang memberikan delegasi
- `notes`: Catatan delegasi
- `status`: Status (pending, accepted, rejected, in_progress, completed)
- `progress_percentage`: Persentase progress (0-100)
- `accepted_at`: Waktu diterima
- `completed_at`: Waktu diselesaikan
- `timestamps`

### Tabel `progress_updates`
- `id`: Primary key
- `delegation_id`: ID delegasi
- `updated_by`: User yang melakukan update
- `progress_percentage`: Persentase progress
- `notes`: Catatan update
- `attachments`: File attachments (JSON)
- `timestamps`

## Routes

- `GET /` - Redirect ke dashboard
- `GET /dashboard` - Dashboard utama
- `GET /tasks` - Daftar semua tasks
- `GET /tasks/create` - Form buat task baru
- `POST /tasks` - Simpan task baru
- `GET /tasks/{task}` - Detail task
- `GET /tasks/{task}/edit` - Form edit task
- `PATCH /tasks/{task}` - Update task
- `DELETE /tasks/{task}` - Hapus task
- `GET /delegations` - Daftar delegasi untuk user
- `GET /tasks/{task}/delegations/create` - Form buat delegasi
- `POST /tasks/{task}/delegations` - Simpan delegasi
- `GET /delegations/{delegation}` - Detail delegasi
- `PATCH /delegations/{delegation}` - Update delegasi (accept/reject/complete)
- `POST /delegations/{delegation}/progress` - Update progress
- `DELETE /delegations/{delegation}` - Hapus delegasi

## Teknologi

- **Laravel 12**: PHP Framework
- **Laravel Breeze**: Authentication scaffolding
- **Tailwind CSS**: Styling
- **MySQL**: Database

## Catatan

- Pastikan database sudah dibuat sebelum menjalankan migration
- Untuk development, gunakan `npm run dev` untuk watch mode
- Pastikan PHP version >= 8.2

## Lisensi

Aplikasi ini dibuat untuk keperluan training dan pembelajaran.
