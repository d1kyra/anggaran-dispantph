// database.js
const dataAPBD = {
    "A": {
        nama: "Gaji dan Tunjangan",
        "2025": { paguAwal: 57749620989, paguAnggaran: 60752717357, paguEfisiensi: -3003096368, paguApbd: 60752717357, paguTahunan: 60752717357, realisasiKeuangan: 52776512219, realisasiPersen: 86.87, realisasiFisik: 100.0 },
        "2026": { paguAwal: 61692790268, paguAnggaran: 61692790268, paguEfisiensi: 0, paguApbd: 61692790268, paguTahunan: 61692790268, realisasiKeuangan: 28908305961, realisasiPersen: 46.86, realisasiFisik: 50.74 }
    },
    "B.1": {
        nama: "Jumlah Pagu Dinas Induk",
        "2022": { paguAwal: 100701772346, paguAnggaran: 1292607600, paguEfisiensi: 0, paguApbd: 101994380006, paguTahunan: 101994380006, realisasiKeuangan: 92063945829, realisasiPersen: 90.26, realisasiFisik: 96.89 },
        "2023": { paguAwal: 103142595565, paguAnggaran: 1765818449, paguEfisiensi: 0, paguApbd: 104908414014, paguTahunan: 104908414014, realisasiKeuangan: 97547729757, realisasiPersen: 92.98, realisasiFisik: 96.89 },
        "2024": { paguAwal: 100462784405, paguAnggaran: -1415301423, paguEfisiensi: 0, paguApbd: 99047482982, paguTahunan: 99047482982, realisasiKeuangan: 87123858568, realisasiPersen: 87.96, realisasiFisik: 88.9 },
        "2025": {
            paguAwal: 45828097752,
            paguAnggaran: 30495722686,
            paguEfisiensi: -15332375066,
            paguApbd: 30495722686,
            paguTahunan: 30495722686,
            realisasiKeuangan: 17570194482,
            realisasiPersen: 57.62,
            realisasiFisik: 62.2
        },
        "2026": { paguAwal: 4230100704, paguAnggaran: 4230100704, paguEfisiensi: 0, paguApbd: 4230100704, paguTahunan: 4230100704, realisasiKeuangan: 969952681, realisasiPersen: 22.93, realisasiFisik: 25.41 }
    },
    "B.2": {
        nama: "Jumlah Pagu UPTD",
        "2022": { paguAwal: 19847838157, paguAnggaran: 1031705336, paguEfisiensi: 0, paguApbd: 20879543493, paguTahunan: 20879543493, realisasiKeuangan: 19872904066, realisasiPersen: 95.18, realisasiFisik: 96.5 },
        "2023": { paguAwal: 74029731435, paguAnggaran: 3368121600, paguEfisiensi: 0, paguApbd: 77397853035, paguTahunan: 77397853035, realisasiKeuangan: 65862826353, realisasiPersen: 85.1, realisasiFisik: 94.15 },
        "2024": { paguAwal: 27717014944, paguAnggaran: 18656659803, paguEfisiensi: 0, paguApbd: 46373674747, paguTahunan: 46373674747, realisasiKeuangan: 44357599815, realisasiPersen: 95.65, realisasiFisik: 97.0 },
        "2025": {
            paguAwal: 38422578190,
            paguAnggaran: 38207357678,
            paguEfisiensi: -215220512,
            paguApbd: 38207357678,
            paguTahunan: 38207357678,
            realisasiKeuangan: 34176733176,
            realisasiPersen: 89.45,
            realisasiFisik: 90.67
        },
        "2026": { paguAwal: 7708567844, paguAnggaran: 7708567844, paguEfisiensi: 0, paguApbd: 7708567844, paguTahunan: 7708567844, realisasiKeuangan: 1631505437, realisasiPersen: 21.16, realisasiFisik: 22.1 }
    },
    "1": {
        nama: "Sekretariat Dinas",
        "2022": { paguAwal: 49657783366, paguAnggaran: 58060, paguEfisiensi: 0, paguApbd: 49657841426, paguTahunan: 49657841426, realisasiKeuangan: 44930032264, realisasiPersen: 90.48, realisasiFisik: 92.5 },
        "2023": { paguAwal: 65262163115, paguAnggaran: -10843519551, paguEfisiensi: 0, paguApbd: 54418643564, paguTahunan: 54418643564, realisasiKeuangan: 49812034551, realisasiPersen: 91.53, realisasiFisik: 96.0 },
        "2024": { paguAwal: 63290717395, paguAnggaran: -370700000, paguEfisiensi: 0, paguApbd: 62920017395, paguTahunan: 62920017395, realisasiKeuangan: 59364810439, realisasiPersen: 94.35, realisasiFisik: 98.0 },
        "2025": {
            paguAwal: 14143294581,
            paguAnggaran: 9033381518,
            paguEfisiensi: -5109913063,
            paguApbd: 9033381518,
            paguTahunan: 9033381518,
            realisasiKeuangan: 8223594878,
            realisasiPersen: 91.04,
            realisasiFisik: 100.0
        },
        "2026": { paguAwal: 1831804501, paguAnggaran: 1831804501, paguEfisiensi: 0, paguApbd: 1831804501, paguTahunan: 1831804501, realisasiKeuangan: 600351815, realisasiPersen: 32.77, realisasiFisik: 34.3 }
    },
    "2": {
        nama: "Bidang KKP",
        "2022": { paguAwal: 3214399900, paguAnggaran: 0, paguEfisiensi: 0, paguApbd: 3214399900, paguTahunan: 3214399900, realisasiKeuangan: 3177980349, realisasiPersen: 98.87, realisasiFisik: 99.0 },
        "2023": { paguAwal: 3250000000, paguAnggaran: 0, paguEfisiensi: 0, paguApbd: 3250000000, paguTahunan: 3250000000, realisasiKeuangan: 3119470319, realisasiPersen: 95.98, realisasiFisik: 99.0 },
        "2024": { paguAwal: 4038764000, paguAnggaran: 0, paguEfisiensi: 0, paguApbd: 4038764000, paguTahunan: 4038764000, realisasiKeuangan: 3165678124, realisasiPersen: 78.38, realisasiFisik: 90.0 },
        "2025": {
            paguAwal: 3551801444,
            paguAnggaran: 2858967332,
            paguEfisiensi: -692834112,
            paguApbd: 2858967332,
            paguTahunan: 2858967332,
            realisasiKeuangan: 2576602500,
            realisasiPersen: 90.12,
            realisasiFisik: 95.74
        },
        "2026": { paguAwal: 486679512, paguAnggaran: 486679512, paguEfisiensi: 0, paguApbd: 486679512, paguTahunan: 486679512, realisasiKeuangan: 81056250, realisasiPersen: 16.65, realisasiFisik: 27.37 }
    },
    "3": {
        nama: "Bidang KDP",
        "2022": { paguAwal: 2495812400, paguAnggaran: 164199600, paguEfisiensi: 0, paguApbd: 2660012000, paguTahunan: 2660012000, realisasiKeuangan: 2351040207, realisasiPersen: 88.38, realisasiFisik: 90.5 },
        "2023": { paguAwal: 3442231250, paguAnggaran: 0, paguEfisiensi: 0, paguApbd: 3442231250, paguTahunan: 3442231250, realisasiKeuangan: 2721525267, realisasiPersen: 79.06, realisasiFisik: 82.0 },
        "2024": { paguAwal: 4986800000, paguAnggaran: -100800000, paguEfisiensi: 0, paguApbd: 4886000000, paguTahunan: 4886000000, realisasiKeuangan: 4309015752, realisasiPersen: 88.19, realisasiFisik: 95.0 },
        "2025": {
            paguAwal: 2432845645,
            paguAnggaran: 1497637477,
            paguEfisiensi: -935208168,
            paguApbd: 1497637477,
            paguTahunan: 1497637477,
            realisasiKeuangan: 1287026335,
            realisasiPersen: 85.94,
            realisasiFisik: 94.5
        },
        "2026": { paguAwal: 683999777, paguAnggaran: 683999777, paguEfisiensi: 0, paguApbd: 683999777, paguTahunan: 683999777, realisasiKeuangan: 84805790, realisasiPersen: 12.4, realisasiFisik: 13.9 }
    },
    "4": {
        nama: "Bidang Produksi Tanaman Pangan (TP)",
        "2022": { paguAwal: 40092170180, paguAnggaran: 528190000, paguEfisiensi: 0, paguApbd: 40620360180, paguTahunan: 40620360180, realisasiKeuangan: 37502736819, realisasiPersen: 92.32, realisasiFisik: 92.5 },
        "2023": { paguAwal: 25438199940, paguAnggaran: 9815898000, paguEfisiensi: 0, paguApbd: 35254097940, paguTahunan: 35254097940, realisasiKeuangan: 33766556390, realisasiPersen: 95.78, realisasiFisik: 99.0 },
        "2024": { paguAwal: 19263103010, paguAnggaran: -2490933500, paguEfisiensi: 0, paguApbd: 16772169510, paguTahunan: 16772169510, realisasiKeuangan: 10829035414, realisasiPersen: 64.57, realisasiFisik: 85.0 },
        "2025": {
            paguAwal: 15206274565,
            paguAnggaran: 8220337840,
            paguEfisiensi: -6985936725,
            paguApbd: 8220337840,
            paguTahunan: 8220337840,
            realisasiKeuangan: 3125849928,
            realisasiPersen: 38.03,
            realisasiFisik: 42.29
        },
        "2026": { paguAwal: 545815007, paguAnggaran: 545815007, paguEfisiensi: 0, paguApbd: 545815007, paguTahunan: 545815007, realisasiKeuangan: 67518066, realisasiPersen: 12.37, realisasiFisik: 14.71 }
    },
    "5": {
        nama: "Bidang Produksi Hortikultura",
        "2022": { paguAwal: 5241606500, paguAnggaran: 600160000, paguEfisiensi: 0, paguApbd: 5841766500, paguTahunan: 5841766500, realisasiKeuangan: 4102156190, realisasiPersen: 70.22, realisasiFisik: 80.5 },
        "2023": { paguAwal: 5750001260, paguAnggaran: 2793440000, paguEfisiensi: 0, paguApbd: 8543441260, paguTahunan: 8543441260, realisasiKeuangan: 8128143230, realisasiPersen: 95.14, realisasiFisik: 99.0 },
        "2024": { paguAwal: 8883400000, paguAnggaran: 1547132077, paguEfisiensi: 0, paguApbd: 10430532077, paguTahunan: 10430532077, realisasiKeuangan: 9455318839, realisasiPersen: 90.65, realisasiFisik: 95.0 },
        "2025": {
            paguAwal: 10493881517,
            paguAnggaran: 8885398519,
            paguEfisiensi: -1608482998,
            paguApbd: 8885398519,
            paguTahunan: 8885398519,
            realisasiKeuangan: 2357120841,
            realisasiPersen: 26.53,
            realisasiFisik: 28.42
        },
        "2026": { paguAwal: 681801907, paguAnggaran: 681801907, paguEfisiensi: 0, paguApbd: 681801907, paguTahunan: 681801907, realisasiKeuangan: 136220760, realisasiPersen: 19.98, realisasiFisik: 20.26 }
    },
    "6": {
        nama: "UPTD PSBTPH",
        "2022": { paguAwal: 2717999916, paguAnggaran: 0, paguEfisiensi: 0, paguApbd: 2717999916, paguTahunan: 2717999916, realisasiKeuangan: 2586473560, realisasiPersen: 95.16, realisasiFisik: 96.5 },
        "2023": { paguAwal: 2945935130, paguAnggaran: 60238080, paguEfisiensi: 0, paguApbd: 3006173210, paguTahunan: 3006173210, realisasiKeuangan: 2923090671, realisasiPersen: 97.24, realisasiFisik: 100.0 },
        "2024": { paguAwal: 3735263186, paguAnggaran: 0, paguEfisiensi: 0, paguApbd: 3735263186, paguTahunan: 3735263186, realisasiKeuangan: 3595340627, realisasiPersen: 96.25, realisasiFisik: 99.0 },
        "2025": {
            paguAwal: 2985869848,
            paguAnggaran: 5540428438,
            paguEfisiensi: 2554558590,
            paguApbd: 5540428438,
            paguTahunan: 5540428438,
            realisasiKeuangan: 2219979840,
            realisasiPersen: 40.07,
            realisasiFisik: 44.88
        },
        "2026": { paguAwal: 1635077632, paguAnggaran: 1635077632, paguEfisiensi: 0, paguApbd: 1635077632, paguTahunan: 1635077632, realisasiKeuangan: 312000624, realisasiPersen: 19.08, realisasiFisik: 20.51 }
    },
    "7": {
        nama: "UPTD BPPSDMP",
        "2022": { paguAwal: 5531712796, paguAnggaran: 591762996, paguEfisiensi: 0, paguApbd: 6123475792, paguTahunan: 6123475792, realisasiKeuangan: 5979816096, realisasiPersen: 97.65, realisasiFisik: 98.5 },
        "2023": { paguAwal: 17509377819, paguAnggaran: 83664000, paguEfisiensi: 0, paguApbd: 17593041819, paguTahunan: 17593041819, realisasiKeuangan: 16042624280, realisasiPersen: 91.19, realisasiFisik: 100.0 },
        "2024": { paguAwal: 10908168687, paguAnggaran: 0, paguEfisiensi: 0, paguApbd: 10908168687, paguTahunan: 10908168687, realisasiKeuangan: 10245568004, realisasiPersen: 93.93, realisasiFisik: 97.0 },
        "2025": {
            paguAwal: 6449139091,
            paguAnggaran: 4925273394,
            paguEfisiensi: -1523865697,
            paguApbd: 4925273394,
            paguTahunan: 4925273394,
            realisasiKeuangan: 4677672382,
            realisasiPersen: 94.97,
            realisasiFisik: 100.0
        },
        "2026": { paguAwal: 2435548560, paguAnggaran: 2435548560, paguEfisiensi: 0, paguApbd: 2435548560, paguTahunan: 2435548560, realisasiKeuangan: 414906262, realisasiPersen: 17.04, realisasiFisik: 17.55 }
    },
    "8": {
        nama: "UPTD BBI TPH",
        "2022": { paguAwal: 7096838462, paguAnggaran: 439942340, paguEfisiensi: 0, paguApbd: 7536780802, paguTahunan: 7536780802, realisasiKeuangan: 7229423712, realisasiPersen: 95.92, realisasiFisik: 96.5 },
        "2023": { paguAwal: 46562562546, paguAnggaran: 3130515840, paguEfisiensi: 0, paguApbd: 49693078386, paguTahunan: 49693078386, realisasiKeuangan: 40724065940, realisasiPersen: 81.95, realisasiFisik: 92.36 },
        "2024": { paguAwal: 6695212892, paguAnggaran: 18630731215, paguEfisiensi: 0, paguApbd: 25325944107, paguTahunan: 25325944107, realisasiKeuangan: 24666204802, realisasiPersen: 97.4, realisasiFisik: 99.0 },
        "2025": {
            paguAwal: 23791380301,
            paguAnggaran: 24402731450,
            paguEfisiensi: 611351149,
            paguApbd: 24402731450,
            paguTahunan: 24402731450,
            realisasiKeuangan: 24028051325,
            realisasiPersen: 98.46,
            realisasiFisik: 100.0
        },
        "2026": { paguAwal: 2133760674, paguAnggaran: 2133760674, paguEfisiensi: 0, paguApbd: 2133760674, paguTahunan: 2133760674, realisasiKeuangan: 455868172, realisasiPersen: 21.36, realisasiFisik: 22.51 }
    },
    "9": {
        nama: "UPTD PTPH",
        "2022": { paguAwal: 4501286983, paguAnggaran: 0, paguEfisiensi: 0, paguApbd: 4501286983, paguTahunan: 4501286983, realisasiKeuangan: 4077190698, realisasiPersen: 90.58, realisasiFisik: 92.5 },
        "2023": { paguAwal: 7011855940, paguAnggaran: 93703680, paguEfisiensi: 0, paguApbd: 7105559620, paguTahunan: 7105559620, realisasiKeuangan: 6173045462, realisasiPersen: 86.88, realisasiFisik: 89.76 },
        "2024": { paguAwal: 6378370179, paguAnggaran: 25928588, paguEfisiensi: 0, paguApbd: 6404298767, paguTahunan: 6404298767, realisasiKeuangan: 5850486382, realisasiPersen: 91.35, realisasiFisik: 96.0 },
        "2025": {
            paguAwal: 5196188950,
            paguAnggaran: 3338924396,
            paguEfisiensi: -1857264554,
            paguApbd: 3338924396,
            paguTahunan: 3338924396,
            realisasiKeuangan: 3251029629,
            realisasiPersen: 97.37,
            realisasiFisik: 99.71
        },
        "2026": { paguAwal: 1504180978, paguAnggaran: 1504180978, paguEfisiensi: 0, paguApbd: 1504180978, paguTahunan: 1504180978, realisasiKeuangan: 448730379, realisasiPersen: 29.83, realisasiFisik: 30.58 }
    },
    "C": {
        nama: "Total Pagu Dinas & UPTD (B.1+B.2)",
        "2022": { paguAwal: 120549610503, paguAnggaran: 2324312996, paguEfisiensi: 0, paguApbd: 122873923499, paguTahunan: 122873923499, realisasiKeuangan: 111936849895, realisasiPersen: 91.10, realisasiFisik: 93.5 },
        "2023": { paguAwal: 177172327000, paguAnggaran: 5133940049, paguEfisiensi: 0, paguApbd: 182306267049, paguTahunan: 182306267049, realisasiKeuangan: 163410556110, realisasiPersen: 89.64, realisasiFisik: 95.73 },
        "2024": { paguAwal: 128179799349, paguAnggaran: 17241358380, paguEfisiensi: 0, paguApbd: 145421157729, paguTahunan: 145421157729, realisasiKeuangan: 131481458383, realisasiPersen: 90.41, realisasiFisik: 97.8 },
        "2025": {
            paguAwal: 142000296931,
            paguAnggaran: 129455797721,
            paguEfisiensi: -12544499210,
            paguApbd: 129455797721,
            paguTahunan: 129455797721,
            realisasiKeuangan: 104523439877,
            realisasiPersen: 80.74,
            realisasiFisik: 88.3
        },
        "2026": { paguAwal: 73631458816, paguAnggaran: 73631458816, paguEfisiensi: 0, paguApbd: 73631458816, paguTahunan: 73631458816, realisasiKeuangan: 31509764079, realisasiPersen: 42.79, realisasiFisik: 46.3 }
    }
};