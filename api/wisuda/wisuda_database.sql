  CREATE TABLE `tabel_mahasiswa` (
    `id_mahasiswa` INT NOT NULL AUTO_INCREMENT,
    `nim` VARCHAR(20) NOT NULL,
    `nama_mahasiswa` VARCHAR(100) NOT NULL,
    `prodi` VARCHAR(100) NOT NULL,
    `ipk` DECIMAL(3,2) NOT NULL,
    PRIMARY KEY (`id_mahasiswa`)
  );


CREATE TABLE `tabel_periode_wisuda` (
  `id_periode` INT NOT NULL AUTO_INCREMENT,
  `tahun_periode` YEAR NOT NULL,
  `tanggal_pelaksanaan` DATE NOT NULL,
  `kuota_maksimal` INT NOT NULL,
  PRIMARY KEY (`id_periode`)
);


CREATE TABLE `tabel_staf_verifikator` (
  `id_staf` INT NOT NULL AUTO_INCREMENT,
  `nama_staf` VARCHAR(100) NOT NULL,
  `divisi` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_staf`)
);

CREATE TABLE `tabel_rekap_pendaftaran` (
  `id_pendaftaran` INT NOT NULL AUTO_INCREMENT,
  `id_mahasiswa` INT NOT NULL,
  `id_periode` INT NOT NULL,
  `id_staf` INT DEFAULT NULL,
  `nomor_kursi` INT DEFAULT NULL,
  `status_verifikasi` ENUM('Pending','Disetujui','Ditolak') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id_pendaftaran`),
  KEY `fk_mahasiswa` (`id_mahasiswa`),
  KEY `fk_periode` (`id_periode`),
  KEY `fk_staf` (`id_staf`),
  CONSTRAINT `fk_mahasiswa` FOREIGN KEY (`id_mahasiswa`) REFERENCES `tabel_mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_periode` FOREIGN KEY (`id_periode`) REFERENCES `tabel_periode_wisuda` (`id_periode`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_staf` FOREIGN KEY (`id_staf`) REFERENCES `tabel_staf_verifikator` (`id_staf`) ON DELETE SET NULL ON UPDATE CASCADE
);


INSERT INTO `tabel_mahasiswa` (`nim`, `nama_mahasiswa`, `prodi`, `ipk`) VALUES
('2021001', 'Ahmad Fauzi', 'Teknik Informatika', 3.75),
('2021002', 'Siti Nurhaliza', 'Sistem Informasi', 3.88),
('2021003', 'Budi Santoso', 'Teknik Elektro', 3.50),
('2021004', 'Dewi Anggraini', 'Manajemen', 3.92),
('2021005', 'Rizky Pratama', 'Teknik Informatika', 3.60);

INSERT INTO `tabel_periode_wisuda` (`tahun_periode`, `tanggal_pelaksanaan`, `kuota_maksimal`) VALUES
(2025, '2025-03-15', 500),
(2025, '2025-09-20', 450),
(2026, '2026-03-22', 550);

INSERT INTO `tabel_staf_verifikator` (`nama_staf`, `divisi`) VALUES
('Dr. Hendra Wijaya', 'Akademik'),
('Ir. Ratna Sari', 'Administrasi'),
('Drs. Agus Salim', 'Keuangan');

INSERT INTO `tabel_rekap_pendaftaran` (`id_mahasiswa`, `id_periode`, `id_staf`, `nomor_kursi`, `status_verifikasi`) VALUES
(1, 1, 1, 101, 'Disetujui'),
(2, 1, 2, 102, 'Disetujui'),
(3, 2, NULL, NULL, 'Pending'),
(4, 2, 3, 201, 'Disetujui'),
(5, 3, NULL, NULL, 'Pending');
