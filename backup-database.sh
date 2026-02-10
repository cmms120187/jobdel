#!/bin/bash

# Script Backup Database untuk Job Delegation
# Gunakan script ini SEBELUM menjalankan migration

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Konfigurasi (sesuaikan dengan .env production)
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-u764740581_jobdel}"
DB_USERNAME="${DB_USERNAME:-u764740581_jobdel}"
DB_PASSWORD="${DB_PASSWORD}"

# Direktori backup
BACKUP_DIR="./backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/backup_${DB_DATABASE}_${TIMESTAMP}.sql"

# Buat direktori backup jika belum ada
mkdir -p "$BACKUP_DIR"

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Backup Database Job Delegation${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# Cek apakah mysqldump tersedia
if ! command -v mysqldump &> /dev/null; then
    echo -e "${RED}Error: mysqldump tidak ditemukan!${NC}"
    echo "Install MySQL client terlebih dahulu."
    exit 1
fi

# Backup database
echo -e "${YELLOW}Memulai backup database: ${DB_DATABASE}${NC}"
echo ""

if [ -z "$DB_PASSWORD" ]; then
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" "$DB_DATABASE" > "$BACKUP_FILE"
else
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE"
fi

# Cek apakah backup berhasil
if [ $? -eq 0 ]; then
    # Kompres backup
    echo -e "${YELLOW}Mengompres backup...${NC}"
    gzip "$BACKUP_FILE"
    
    BACKUP_FILE_GZ="${BACKUP_FILE}.gz"
    BACKUP_SIZE=$(du -h "$BACKUP_FILE_GZ" | cut -f1)
    
    echo ""
    echo -e "${GREEN}✓ Backup berhasil!${NC}"
    echo -e "${GREEN}File: ${BACKUP_FILE_GZ}${NC}"
    echo -e "${GREEN}Ukuran: ${BACKUP_SIZE}${NC}"
    echo ""
    echo -e "${YELLOW}Catatan:${NC}"
    echo "- Simpan file backup di tempat yang aman"
    echo "- File backup: ${BACKUP_FILE_GZ}"
    echo "- Jika migration gagal, restore dengan: mysql -u ${DB_USERNAME} -p ${DB_DATABASE} < ${BACKUP_FILE_GZ}"
else
    echo ""
    echo -e "${RED}✗ Backup gagal!${NC}"
    echo "Periksa kredensial database di .env"
    exit 1
fi

echo ""
echo -e "${GREEN}========================================${NC}"
