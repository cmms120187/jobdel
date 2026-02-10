# 📋 Panduan Backup Database Sebelum Migration

## ⚠️ PENTING: Backup Database SEBELUM Migration!

Migration akan mengubah struktur database. **SELALU backup database terlebih dahulu** untuk memastikan data tetap aman.

---

## 🛡️ Langkah-Langkah Keamanan

### **Langkah 1: Backup Database**

#### **Cara 1: Via phpMyAdmin (Paling Mudah)**

1. Login ke **phpMyAdmin** di cPanel
2. Pilih database: `u764740581_jobdel`
3. Klik tab **"Export"**
4. Pilih metode: **"Quick"** atau **"Custom"**
5. Format: **SQL**
6. Klik **"Go"** untuk download backup
7. Simpan file backup di komputer Anda dengan nama jelas, contoh: `backup_jobdel_2026-01-29.sql`

#### **Cara 2: Via Terminal/SSH**

```bash
# Masuk ke direktori aplikasi
cd ~/public_html

# Backup database (ganti dengan kredensial dari .env)
mysqldump -h 127.0.0.1 -u u764740581_jobdel -p u764740581_jobdel > backup_$(date +%Y%m%d_%H%M%S).sql

# Kompres backup (opsional, untuk menghemat space)
gzip backup_*.sql
```

#### **Cara 3: Via Script Otomatis**

```bash
# Set environment variables dari .env
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_DATABASE=u764740581_jobdel
export DB_USERNAME=u764740581_jobdel
export DB_PASSWORD=your_password

# Jalankan script backup
chmod +x backup-database.sh
./backup-database.sh
```

---

### **Langkah 2: Verifikasi Backup**

Pastikan backup berhasil:

```bash
# Cek ukuran file backup (harus > 0)
ls -lh backup_*.sql

# Cek isi backup (harus ada CREATE TABLE statements)
head -n 50 backup_*.sql
```

---

### **Langkah 3: Simpan Backup di Tempat Aman**

- ✅ Simpan di komputer lokal
- ✅ Upload ke cloud storage (Google Drive, Dropbox, dll)
- ✅ Simpan di server lain sebagai cadangan
- ✅ Jangan hapus backup sampai migration selesai dan aplikasi berjalan normal

---

### **Langkah 4: Aktifkan Maintenance Mode (Opsional)**

Sebelum migration, aktifkan maintenance mode agar user tidak mengakses aplikasi:

```bash
php artisan down
```

Setelah migration selesai, nonaktifkan:

```bash
php artisan up
```

---

### **Langkah 5: Jalankan Migration**

```bash
# Cek status migration terlebih dahulu
php artisan migrate:status

# Jalankan migration
php artisan migrate

# Atau dengan force (jika di production)
php artisan migrate --force
```

---

### **Langkah 6: Verifikasi Setelah Migration**

```bash
# Cek status migration (semua harus "Ran")
php artisan migrate:status

# Test aplikasi
# - Buka halaman dashboard
# - Cek apakah data masih ada
# - Test fitur-fitur penting
```

---

## 🔄 Restore Database (Jika Migration Gagal)

Jika migration gagal dan perlu restore backup:

### **Via phpMyAdmin:**

1. Login ke phpMyAdmin
2. Pilih database: `u764740581_jobdel`
3. Klik tab **"Import"**
4. Pilih file backup yang sudah didownload
5. Klik **"Go"** untuk restore

### **Via Terminal:**

```bash
# Restore dari backup SQL
mysql -h 127.0.0.1 -u u764740581_jobdel -p u764740581_jobdel < backup_20260129_120000.sql

# Atau jika backup terkompres
gunzip < backup_20260129_120000.sql.gz | mysql -h 127.0.0.1 -u u764740581_jobdel -p u764740581_jobdel
```

---

## 📊 Migration yang Akan Dijalankan

Migration berikut akan dijalankan (dari status "Pending"):

1. ✅ `create_positions_table` - Tabel baru (aman)
2. ✅ `add_position_id_to_users_table` - Menambah kolom (aman, data tetap)
3. ✅ `add_nik_to_users_table` - Menambah kolom (aman, data tetap)
4. ✅ `add_additional_fields_to_tasks_table` - Menambah kolom (aman, data tetap)
5. ✅ `create_rooms_table` - Tabel baru (aman)
6. ✅ `change_factory_to_room_id_in_tasks_table` - Mengubah kolom (perlu perhatian)
7. ✅ `add_add_request_to_tasks_table` - Menambah kolom (aman, data tetap)
8. ✅ `add_leader_id_to_users_table` - Menambah kolom (aman, data tetap)
9. ✅ `create_task_histories_table` - Tabel baru (aman)
10. ✅ `create_task_items_table` - Tabel baru (aman)
11. ✅ `create_task_item_updates_table` - Tabel baru (aman)
12. ✅ `add_update_date_to_task_item_updates_table` - Menambah kolom (aman)
13. ✅ `add_time_from_and_time_to_to_task_item_updates_table` - Menambah kolom (aman)
14. ✅ `add_start_date_to_task_items_table` - Menambah kolom (aman)
15. ✅ `add_time_fields_to_task_items_table` - Menambah kolom (aman)
16. ✅ `add_leader_id_to_users_table` (duplicate) - Akan di-skip jika sudah ada
17. ✅ `create_task_attachments_table` - Tabel baru (aman) ⭐ **Ini yang menyebabkan error**
18. ✅ `add_description_to_task_attachments_table` - Menambah kolom (aman)

**Kesimpulan:** Semua migration ini **AMAN** untuk data yang sudah ada. Mereka hanya:
- Membuat tabel baru (tidak menghapus data lama)
- Menambah kolom baru (data lama tetap ada)
- Tidak ada DROP TABLE atau DELETE data

---

## ✅ Checklist Sebelum Migration

- [ ] ✅ Backup database sudah dibuat
- [ ] ✅ Backup sudah diverifikasi (ukuran > 0, bisa dibuka)
- [ ] ✅ Backup sudah disimpan di tempat aman
- [ ] ✅ Maintenance mode aktif (opsional, tapi disarankan)
- [ ] ✅ Tidak ada user aktif yang sedang menggunakan aplikasi
- [ ] ✅ Sudah membaca migration yang akan dijalankan
- [ ] ✅ Siap untuk restore jika terjadi masalah

---

## 🆘 Jika Ada Masalah

1. **Migration Error:**
   - Jangan panik!
   - Cek error message
   - Restore backup jika perlu
   - Hubungi developer jika perlu bantuan

2. **Data Hilang:**
   - Segera restore dari backup
   - Jangan jalankan migration lagi sebelum restore

3. **Aplikasi Error Setelah Migration:**
   - Cek log: `storage/logs/laravel.log`
   - Cek apakah semua migration berhasil: `php artisan migrate:status`
   - Restore backup jika perlu

---

## 📞 Kontak Support

Jika ada pertanyaan atau masalah, hubungi developer atau tim IT.

---

**Selamat! Data Anda akan tetap aman dengan backup yang sudah dibuat.** 🎉
