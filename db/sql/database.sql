CREATE DATABASE IF NOT EXISTS sisfor_anggaran;
USE sisfor_anggaran;

CREATE TABLE IF NOT EXISTS apbd_unit (
    kode VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    periode_custom VARCHAR(100) DEFAULT NULL,
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
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('1', 'Sekretariat Dinas', 1);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('1', 2022, 49657783366, 58060, 0, 49657841426, 49657841426, 44930032264, 90.48, 92.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('1', 2023, 65262163115, -10843519551, 0, 54418643564, 54418643564, 49812034551, 91.53, 96);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('1', 2024, 63290717395, -370700000, 0, 62920017395, 62920017395, 59364810439, 94.35, 98);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('1', 2025, 14143294581, 9033381518, -5109913063, 9033381518, 9033381518, 8223594878, 91.04, 100);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('1', 2026, 1831804501, 1831804501, 0, 1831804501, 1831804501, 600351815, 32.77, 34.3);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('2', 'Bidang KKP', 2);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('2', 2022, 3214399900, 0, 0, 3214399900, 3214399900, 3177980349, 98.87, 99);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('2', 2023, 3250000000, 0, 0, 3250000000, 3250000000, 3119470319, 95.98, 99);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('2', 2024, 4038764000, 0, 0, 4038764000, 4038764000, 3165678124, 78.38, 90);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('2', 2025, 3551801444, 2858967332, -692834112, 2858967332, 2858967332, 2576602500, 90.12, 95.74);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('2', 2026, 486679512, 486679512, 0, 486679512, 486679512, 81056250, 16.65, 27.37);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('3', 'Bidang KDP', 3);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('3', 2022, 2495812400, 164199600, 0, 2660012000, 2660012000, 2351040207, 88.38, 90.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('3', 2023, 3442231250, 0, 0, 3442231250, 3442231250, 2721525267, 79.06, 82);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('3', 2024, 4986800000, -100800000, 0, 4886000000, 4886000000, 4309015752, 88.19, 95);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('3', 2025, 2432845645, 1497637477, -935208168, 1497637477, 1497637477, 1287026335, 85.94, 94.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('3', 2026, 683999777, 683999777, 0, 683999777, 683999777, 84805790, 12.4, 13.9);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('4', 'Bidang Produksi Tanaman Pangan (TP)', 4);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('4', 2022, 40092170180, 528190000, 0, 40620360180, 40620360180, 37502736819, 92.32, 92.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('4', 2023, 25438199940, 9815898000, 0, 35254097940, 35254097940, 33766556390, 95.78, 99);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('4', 2024, 19263103010, -2490933500, 0, 16772169510, 16772169510, 10829035414, 64.57, 85);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('4', 2025, 15206274565, 8220337840, -6985936725, 8220337840, 8220337840, 3125849928, 38.03, 42.29);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('4', 2026, 545815007, 545815007, 0, 545815007, 545815007, 67518066, 12.37, 14.71);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('5', 'Bidang Produksi Hortikultura', 5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('5', 2022, 5241606500, 600160000, 0, 5841766500, 5841766500, 4102156190, 70.22, 80.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('5', 2023, 5750001260, 2793440000, 0, 8543441260, 8543441260, 8128143230, 95.14, 99);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('5', 2024, 8883400000, 1547132077, 0, 10430532077, 10430532077, 9455318839, 90.65, 95);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('5', 2025, 10493881517, 8885398519, -1608482998, 8885398519, 8885398519, 2357120841, 26.53, 28.42);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('5', 2026, 681801907, 681801907, 0, 681801907, 681801907, 136220760, 19.98, 20.26);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('6', 'UPTD PSBTPH', 6);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('6', 2022, 2717999916, 0, 0, 2717999916, 2717999916, 2586473560, 95.16, 96.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('6', 2023, 2945935130, 60238080, 0, 3006173210, 3006173210, 2923090671, 97.24, 100);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('6', 2024, 3735263186, 0, 0, 3735263186, 3735263186, 3595340627, 96.25, 99);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('6', 2025, 2985869848, 5540428438, 2554558590, 5540428438, 5540428438, 2219979840, 40.07, 44.88);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('6', 2026, 1635077632, 1635077632, 0, 1635077632, 1635077632, 312000624, 19.08, 20.51);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('7', 'UPTD BPPSDMP', 7);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('7', 2022, 5531712796, 591762996, 0, 6123475792, 6123475792, 5979816096, 97.65, 98.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('7', 2023, 17509377819, 83664000, 0, 17593041819, 17593041819, 16042624280, 91.19, 100);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('7', 2024, 10908168687, 0, 0, 10908168687, 10908168687, 10245568004, 93.93, 97);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('7', 2025, 6449139091, 4925273394, -1523865697, 4925273394, 4925273394, 4677672382, 94.97, 100);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('7', 2026, 2435548560, 2435548560, 0, 2435548560, 2435548560, 414906262, 17.04, 17.55);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('8', 'UPTD BBI TPH', 8);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('8', 2022, 7096838462, 439942340, 0, 7536780802, 7536780802, 7229423712, 95.92, 96.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('8', 2023, 46562562546, 3130515840, 0, 49693078386, 49693078386, 40724065940, 81.95, 92.36);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('8', 2024, 6695212892, 18630731215, 0, 25325944107, 25325944107, 24666204802, 97.4, 99);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('8', 2025, 23791380301, 24402731450, 611351149, 24402731450, 24402731450, 24028051325, 98.46, 100);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('8', 2026, 2133760674, 2133760674, 0, 2133760674, 2133760674, 455868172, 21.36, 22.51);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('9', 'UPTD PTPH', 9);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('9', 2022, 4501286983, 0, 0, 4501286983, 4501286983, 4077190698, 90.58, 92.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('9', 2023, 7011855940, 93703680, 0, 7105559620, 7105559620, 6173045462, 86.88, 89.76);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('9', 2024, 6378370179, 25928588, 0, 6404298767, 6404298767, 5850486382, 91.35, 96);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('9', 2025, 5196188950, 3338924396, -1857264554, 3338924396, 3338924396, 3251029629, 97.37, 99.71);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('9', 2026, 1504180978, 1504180978, 0, 1504180978, 1504180978, 448730379, 29.83, 30.58);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('A', 'Gaji dan Tunjangan', 10);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('A', 2025, 57749620989, 60752717357, -3003096368, 60752717357, 60752717357, 52776512219, 86.87, 100);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('A', 2026, 61692790268, 61692790268, 0, 61692790268, 61692790268, 28908305961, 46.86, 50.74);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('B.1', 'Jumlah Pagu Dinas Induk', 11);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.1', 2022, 100701772346, 1292607600, 0, 101994380006, 101994380006, 92063945829, 90.26, 96.89);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.1', 2023, 103142595565, 1765818449, 0, 104908414014, 104908414014, 97547729757, 92.98, 96.89);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.1', 2024, 100462784405, -1415301423, 0, 99047482982, 99047482982, 87123858568, 87.96, 88.9);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.1', 2025, 45828097752, 30495722686, -15332375066, 30495722686, 30495722686, 17570194482, 57.62, 62.2);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.1', 2026, 4230100704, 4230100704, 0, 4230100704, 4230100704, 969952681, 22.93, 25.41);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('B.2', 'Jumlah Pagu UPTD', 12);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.2', 2022, 19847838157, 1031705336, 0, 20879543493, 20879543493, 19872904066, 95.18, 96.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.2', 2023, 74029731435, 3368121600, 0, 77397853035, 77397853035, 65862826353, 85.1, 94.15);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.2', 2024, 27717014944, 18656659803, 0, 46373674747, 46373674747, 44357599815, 95.65, 97);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.2', 2025, 38422578190, 38207357678, -215220512, 38207357678, 38207357678, 34176733176, 89.45, 90.67);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('B.2', 2026, 7708567844, 7708567844, 0, 7708567844, 7708567844, 1631505437, 21.16, 22.1);
INSERT IGNORE INTO apbd_unit (kode, nama, urutan) VALUES ('C', 'Total Pagu Dinas & UPTD (B.1+B.2)', 13);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('C', 2022, 120549610503, 2324312996, 0, 122873923499, 122873923499, 111936849895, 91.1, 93.5);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('C', 2023, 177172327000, 5133940049, 0, 182306267049, 182306267049, 163410556110, 89.64, 95.73);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('C', 2024, 128179799349, 17241358380, 0, 145421157729, 145421157729, 131481458383, 90.41, 97.8);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('C', 2025, 142000296931, 129455797721, -12544499210, 129455797721, 129455797721, 104523439877, 80.74, 88.3);
INSERT IGNORE INTO apbd_anggaran (unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan, realisasi_keuangan, realisasi_persen, realisasi_fisik) VALUES ('C', 2026, 73631458816, 73631458816, 0, 73631458816, 73631458816, 31509764079, 42.79, 46.3);

-- APBN DATA INSERTION
TRUNCATE TABLE apbn_kegiatan;
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2022, '018.03.169073', 'Ditjen Tanaman Pangan', 'DK', 2729254000, -851603000, 1877651000, 1852479378, 98.66, 100, 25171622);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2022, '018.03.169112', 'Ditjen Tanaman Pangan', 'TP', 6120113000, -4197724000, 1922389000, 1892590651, 98.45, 100, 29798349);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2022, '018.04.169025', 'Ditjen Hortikultura', 'DK', 5439963000, -937620000, 4502343000, 4416323880, 98.09, 100, 86019120);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2022, '018.04.169113', 'Ditjen Hortikultura', 'TP', 4351124000, -283500000, 4067624000, 4046047500, 99.47, 100, 21576500);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2022, '018.08.169027', 'Ditjen Prasarana & Sarana Pertanian', 'DK', 528140000, 0, 528140000, 527373900, 99.85, 100, 766100);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2022, '018.08.169121', 'Ditjen Prasarana & Sarana Pertanian', 'TP', 5386875000, 0, 5386875000, 5386656700, 100, 100, 218300);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2022, '018.10.169065', 'BPPSDMP', 'DK', 3620308000, -130503000, 3489805000, 3462011428, 99.2, 100, 27793572);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2022, '-', 'BKP (Bapanas)', 'BKP', 650000000, -240000000, 410000000, 394791000, 96.29, 100, 15209000);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2023, '018.03.169073', 'Ditjen Tanaman Pangan', 'DK', 2861410000, -342954000, 2518456000, 2389540811, 94.88, 95, 128915189);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2023, '018.03.169112', 'Ditjen Tanaman Pangan', 'TP', 7477439000, -2309925000, 5167514000, 4246923350, 82.19, 89, 920590650);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2023, '018.04.169025', 'Ditjen Hortikultura', 'DK', 3168671000, -284040000, 2884631000, 2787319800, 96.63, 100, 97311200);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2023, '018.04.169113', 'Ditjen Hortikultura', 'TP', 6727800000, -2679765000, 4048035000, 4039243660, 99.78, 100, 8791340);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2023, '018.08.169027', 'Ditjen Prasarana & Sarana Pertanian', 'DK', 977395000, -296575000, 680820000, 662565400, 97.32, 100, 18254600);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2023, '018.08.169121', 'Ditjen Prasarana & Sarana Pertanian', 'TP', 6266180000, -5120041000, 1146139000, 1135047000, 99.03, 100, 11092000);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2023, '018.10.169065', 'BPPSDMP', 'DK', 3684990000, 184708000, 3869698000, 3854744528, 99.61, 100, 14953472);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2023, '125.01.3.690714', 'Badan Pangan Nasional', 'DK', 3066083000, 0, 3066083000, 2348875929, 76.61, 78, 717207071);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2024, '018.03.169073', 'Ditjen Tanaman Pangan', 'DK', 2401241000, -890660000, 1510581000, 1445135810, 95.67, 97.2, 65445190);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2024, '018.03.169112', 'Ditjen Tanaman Pangan', 'TP', 5950456000, -3607192000, 2343264000, 2150067100, 91.76, 92.86, 193196900);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2024, '018.04.169025', 'Ditjen Hortikultura', 'DK', 1934135000, -1562480000, 371655000, 157458500, 42.37, 43.96, 214196500);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2024, '018.04.169113', 'Ditjen Hortikultura', 'TP', 6122320000, -5302320000, 820000000, 817695000, 99.72, 100, 2305000);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2024, '018.08.169027', 'Ditjen PSP', 'DK', 175000000, -100000000, 75000000, 74029000, 98.71, 100, 971000);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2024, '018.08.169121', 'Ditjen PSP', 'TP', 6882548000, -21863000, 6860685000, 6751966400, 98.42, 99.09, 108718600);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2024, '018.10.169065', 'BPPSDMP', 'DK', 3799130000, -421462000, 3377668000, 3317529522, 98.22, 100, 60138478);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2024, '125.01.690714', 'BAPANAS', 'DK', 3241209000, -316234000, 2924975000, 2661727250, 91, 92, 263247750);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2025, '018.03.169073', 'Ditjen Tanaman Pangan', 'DK', 133144000, 0, 133144000, 120221213, 90.29, 100, 12922787);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2025, '018.03.169112', 'Ditjen Tanaman Pangan', 'TP', 255550000, 0, 255550000, 225255000, 88.15, 100, 30295000);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2025, '018.08.169027', 'Ditjen PSP', 'DK', 150000000, -85000000, 65000000, 55925000, 86.04, 86.04, 9075000);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2025, '018.08.169121', 'Ditjen PSP', 'TP', 70673714000, -200000000, 70473714000, 67858333678, 96.29, 99.91, 2615380322);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2025, '018.13.691359', 'Ditjen Lahan dan Irigasi Pertanian (LIP)', 'TP', 85347419000, -815428000, 84531991000, 66957537232, 79.21, 79.21, 17574453768);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2025, '018.10.169065', 'BPPSDMP', 'DK', 137040000, 0, 137040000, 134840366, 98.39, 100, 2199634);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2025, '125.01.690714', 'BAPANAS', 'DK', 1298142000, -907207000, 390935000, 383403291, 98.07, 100, 7531709);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2026, '018.03.169073', 'Ditjen Tanaman Pangan', 'DK', 264629000, -226960000, 37669000, 20919800, 55.54, 62, 16749200);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2026, '018.03.169112', 'Ditjen Tanaman Pangan', 'TP', 17000000, -16150000, 850000, 850000, 100, 52, 0);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2026, '018.08.169027', 'Ditjen PSP', 'DK', 195280000, 881600000, 1076880000, 131001000, 12.16, 20, 945879000);
INSERT INTO apbn_kegiatan (tahun, kode_satker, nama_kegiatan, kewenangan, pagu_dipa, pagu_revisi, pagu_setelah_blokir, realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran) VALUES (2026, '125.01.690714', 'Bapanas', 'DK', 629047000, -166013000, 463034000, 199620403, 43.11, 50.02, 263413597);
