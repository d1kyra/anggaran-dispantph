const fs = require('fs');

const db1 = fs.readFileSync('./database.js', 'utf8');
const db2 = fs.readFileSync('./database2.js', 'utf8');

// A little hack to get the variables in node context
eval(db1.replace('const dataAPBD =', 'var dataAPBD ='));
eval(db2.replace('const dataAPBN =', 'var dataAPBN ='));

let sql = `CREATE DATABASE IF NOT EXISTS sisfor_anggaran;
USE sisfor_anggaran;

CREATE TABLE IF NOT EXISTS apbd_unit (
    kode VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    urutan INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS apbd_anggaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_kode VARCHAR(20) NOT NULL,
    tahun INT NOT NULL,
    pagu_awal BIGINT DEFAULT 0,
    pagu_anggaran BIGINT DEFAULT 0,
    pagu_efisiensi BIGINT DEFAULT 0,
    pagu_apbd BIGINT DEFAULT 0,
    pagu_tahunan BIGINT DEFAULT 0,
    realisasi_keuangan BIGINT DEFAULT 0,
    realisasi_persen DECIMAL(5,2) DEFAULT 0.00,
    realisasi_fisik DECIMAL(5,2) DEFAULT 0.00,
    FOREIGN KEY (unit_kode) REFERENCES apbd_unit(kode) ON DELETE CASCADE,
    UNIQUE KEY unit_tahun (unit_kode, tahun)
);

CREATE TABLE IF NOT EXISTS apbn_kegiatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tahun INT NOT NULL,
    kode_satker VARCHAR(50) NOT NULL,
    nama_kegiatan VARCHAR(255) NOT NULL,
    kewenangan VARCHAR(20) NOT NULL,
    pagu_dipa BIGINT DEFAULT 0,
    pagu_revisi BIGINT DEFAULT 0,
    pagu_setelah_blokir BIGINT DEFAULT 0,
    realisasi_rp BIGINT DEFAULT 0,
    realisasi_persen DECIMAL(5,2) DEFAULT 0.00,
    realisasi_fisik DECIMAL(5,2) DEFAULT 0.00,
    sisa_anggaran BIGINT DEFAULT 0
);

-- DATA INSERTION
`;

// Insert APBD
let urutan = 1;
for (const [kode, unit] of Object.entries(dataAPBD)) {
    const nama = unit.nama.replace(/'/g, "''");
    sql += `INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('${kode}', '${nama}', ${urutan});\n`;
    urutan++;

    for (const [tahun, data] of Object.entries(unit)) {
        if (tahun === 'nama') continue;
        
        sql += `INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES `;
        sql += `('${kode}', ${tahun}, ${data.paguAwal || 0}, ${data.paguAnggaran || 0}, ${data.paguEfisiensi || 0}, ${data.paguApbd || 0}, ${data.paguTahunan || 0}, ${data.realisasiKeuangan || 0}, ${data.realisasiPersen || 0}, ${data.realisasiFisik || 0});\n`;
    }
}

sql += '\n-- APBN DATA INSERTION\n';

// Insert APBN
for (const [tahun, arr] of Object.entries(dataAPBN)) {
    for (const data of arr) {
        const satker = data.kodeSatker.replace(/'/g, "''");
        const nama = data.namaKegiatan.replace(/'/g, "''");
        const kewenangan = data.kewenangan.replace(/'/g, "''");
        
        let paguRevisi = data.paguRevisi || data.paguRev2024 || data.paguBlokir || 0;
        
        sql += `INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES `;
        sql += `(${tahun}, '${satker}', '${nama}', '${kewenangan}', ${data.paguDipa || 0}, ${paguRevisi}, ${data.paguSetelahBlokir || 0}, ${data.realisasiRp || 0}, ${data.realisasiPersen || 0}, ${data.realisasiFisik || 0}, ${data.sisaAnggaran || 0});\n`;
    }
}

fs.writeFileSync('./database.sql', sql);
console.log('database.sql generated successfully.');
