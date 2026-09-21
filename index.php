<?php
// index.php - Entry point utama SISFOR Anggaran
if (file_exists(__DIR__ . '/index.html')) {
    include_once __DIR__ . '/index.html';
} else {
    echo "SISFOR Anggaran Active";
}
