-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2026 at 09:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `website_ban_dienthoai`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `hoan_thanh_phieu_nhap` (IN `p_ma_phieu` INT)   BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE v_ma_sp INT;
    DECLARE v_so_luong_nhap INT;
    DECLARE v_gia_nhap DECIMAL(15,2);
    DECLARE v_so_luong_ton INT;
    DECLARE v_gia_nhap_bq DECIMAL(15,2);
    DECLARE v_gia_bq_moi DECIMAL(15,2);

    DECLARE cur CURSOR FOR
        SELECT ma_sp, so_luong, gia_nhap
        FROM chi_tiet_phieu_nhap
        WHERE ma_phieu = p_ma_phieu;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    IF (SELECT trang_thai FROM phieu_nhap WHERE ma_phieu = p_ma_phieu) = 0 THEN

        OPEN cur;
        read_loop: LOOP
            FETCH cur INTO v_ma_sp, v_so_luong_nhap, v_gia_nhap;
            IF done THEN LEAVE read_loop; END IF;

            SELECT so_luong_ton, gia_nhap_bq
            INTO v_so_luong_ton, v_gia_nhap_bq
            FROM san_pham WHERE ma_sp = v_ma_sp;

            IF (v_so_luong_ton + v_so_luong_nhap) > 0 THEN
                SET v_gia_bq_moi = (
                    (v_so_luong_ton * v_gia_nhap_bq + v_so_luong_nhap * v_gia_nhap)
                    / (v_so_luong_ton + v_so_luong_nhap)
                );
            ELSE
                SET v_gia_bq_moi = v_gia_nhap;
            END IF;

            UPDATE san_pham
            SET so_luong_ton = so_luong_ton + v_so_luong_nhap,
                gia_nhap_bq  = v_gia_bq_moi
            WHERE ma_sp = v_ma_sp;

        END LOOP;
        CLOSE cur;

        UPDATE phieu_nhap SET trang_thai = 1 WHERE ma_phieu = p_ma_phieu;

    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

CREATE TABLE `admin` (
  `ma_admin` int(11) NOT NULL,
  `ten_dang_nhap` varchar(100) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `ho_ten` varchar(100) DEFAULT NULL,
  `trang_thai` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin` (`ma_admin`, `ten_dang_nhap`, `mat_khau`, `ho_ten`, `trang_thai`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản Trị Viên', 1);

-- --------------------------------------------------------

CREATE TABLE `chi_tiet_don_hang` (
  `ma_ctdh` int(11) NOT NULL,
  `ma_dh` int(11) NOT NULL,
  `ma_sp` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `gia_ban` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `chi_tiet_phieu_nhap` (
  `ma_ctpn` int(11) NOT NULL,
  `ma_phieu` int(11) NOT NULL,
  `ma_sp` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `gia_nhap` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `don_hang` (
  `ma_dh` int(11) NOT NULL,
  `ma_kh` int(11) NOT NULL,
  `ngay_dat` datetime NOT NULL DEFAULT current_timestamp(),
  `ten_nguoi_nhan` varchar(100) NOT NULL,
  `dien_thoai_nhan` varchar(20) NOT NULL,
  `dia_chi_giao` varchar(300) NOT NULL,
  `phuong_xa_giao` varchar(100) DEFAULT NULL,
  `quan_huyen_giao` varchar(100) DEFAULT NULL,
  `tinh_tp_giao` varchar(100) DEFAULT NULL,
  `phuong_thuc_tt` varchar(30) NOT NULL DEFAULT 'tien_mat',
  `tong_tien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ghi_chu` text DEFAULT NULL,
  `trang_thai` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `khach_hang` (
  `ma_kh` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `dien_thoai` varchar(20) DEFAULT NULL,
  `dia_chi` varchar(300) DEFAULT NULL,
  `phuong_xa` varchar(100) DEFAULT NULL,
  `quan_huyen` varchar(100) DEFAULT NULL,
  `tinh_tp` varchar(100) DEFAULT NULL,
  `trang_thai` tinyint(1) NOT NULL DEFAULT 1,
  `ngay_tao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `loai_sp` (
  `ma_loai` int(11) NOT NULL,
  `ten_loai` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `an_hien` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `loai_sp` (`ma_loai`, `ten_loai`, `mo_ta`, `hinh_anh`, `an_hien`) VALUES
(1, 'iPhone',   'Điện thoại Apple iPhone',   NULL, 1),
(2, 'Samsung',  'Điện thoại Samsung Galaxy', NULL, 1),
(3, 'Xiaomi',   'Điện thoại Xiaomi',         NULL, 1),
(4, 'OPPO',     'Điện thoại OPPO',           NULL, 1),
(5, 'Vivo',     'Điện thoại Vivo',           NULL, 1);

-- --------------------------------------------------------

CREATE TABLE `phieu_nhap` (
  `ma_phieu` int(11) NOT NULL,
  `ngay_nhap` datetime NOT NULL DEFAULT current_timestamp(),
  `ghi_chu` text DEFAULT NULL,
  `trang_thai` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `san_pham` (
  `ma_sp` int(11) NOT NULL,
  `ma_loai` int(11) NOT NULL,
  `ten_sp` varchar(200) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `don_vi_tinh` varchar(50) NOT NULL DEFAULT 'Chiếc',
  `hinh_anh` varchar(255) DEFAULT NULL,
  `gia_nhap_bq` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ty_le_loi_nhuan` decimal(5,2) NOT NULL DEFAULT 0.00,
  `so_luong_ton` int(11) NOT NULL DEFAULT 0,
  `an_hien` tinyint(1) NOT NULL DEFAULT 1,
  `ngay_tao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `san_pham` (`ma_sp`, `ma_loai`, `ten_sp`, `mo_ta`, `don_vi_tinh`, `hinh_anh`, `gia_nhap_bq`, `ty_le_loi_nhuan`, `so_luong_ton`, `an_hien`, `ngay_tao`) VALUES
(1,  1, 'iPhone 17 256GB',
    'Chip A18, màn hình 6.1 inch Super Retina XDR, camera 48MP, thiết kế nhôm cao cấp',
    'Cái', 'assets/img/product/iphone/iphone_17_256gb-3_3.webp',
    25000000.00, 15.00, 10, 1, '2026-03-23 08:00:00'),

(2,  1, 'iPhone 17e',
    'Chip A16 Bionic, màn hình 6.1 inch, camera 48MP, pin cải tiến, giá phổ thông',
    'Cái', 'assets/img/product/iphone/iphone_17e_pink_1.webp',
    18000000.00, 15.00, 12, 1, '2026-03-23 08:00:00'),

(3,  1, 'iPhone 17 Pro',
    'Chip A18 Pro, camera 48MP Fusion + 12MP Ultra Wide, khung Titanium, Dynamic Island',
    'Cái', 'assets/img/product/iphone/iphone-17-pro-cam_4.webp',
    30000000.00, 15.00, 8, 1, '2026-03-23 08:00:00'),

(4,  2, 'Samsung Galaxy S24 Ultra 256GB',
    'Chip Snapdragon 8 Gen 3, bút S Pen tích hợp, camera 200MP, màn hình 6.8 inch QHD+',
    'Cái', 'assets/img/product/iphone/ss-s24-ultra-vang-222.webp',
    22000000.00, 18.00, 8, 1, '2026-03-23 08:00:00'),

(5,  2, 'Samsung Galaxy S26',
    'Chip Snapdragon 8 Elite, màn hình AMOLED 120Hz, camera 50MP, pin 4000mAh sạc nhanh',
    'Cái', 'assets/img/product/iphone/samsung-galaxy-s26.webp',
    20000000.00, 18.00, 10, 1, '2026-03-23 08:00:00'),

(6,  2, 'Samsung Galaxy Z Fold 7',
    'Màn hình gập 7.6 inch, chip Snapdragon 8 Elite, camera 200MP, RAM 12GB',
    'Cái', 'assets/img/product/iphone/samsung-galaxy-z-fold-7-xanh.webp',
    35000000.00, 15.00, 5, 1, '2026-03-23 08:00:00'),

(7,  3, 'Xiaomi 14 Ultra',
    'Camera Leica 50MP x4, Snapdragon 8 Gen 3, sạc nhanh 90W, màn hình LTPO AMOLED',
    'Cái', 'assets/img/product/iphone/xiaomi-14-ultra.webp',
    22000000.00, 18.00, 8, 1, '2026-03-23 08:00:00'),

(8,  3, 'Xiaomi Redmi Note 14 Pro Plus',
    'Camera 200MP, sạc nhanh 90W, màn hình AMOLED 120Hz, IP68, Chip Dimensity 9200+',
    'Cái', 'assets/img/product/iphone/xiaomi-redmi-note-14-pro-plus.webp',
    9000000.00, 20.00, 15, 1, '2026-03-23 08:00:00'),

(9, 3, 'Xiaomi Redmi Note 15 Series',
    'Chip Snapdragon 7s Gen 3, camera 108MP, pin 5500mAh, sạc nhanh 45W',
    'Cái', 'assets/img/product/iphone/redmi-note-15-series-6_1_2.webp',
    7000000.00, 20.00, 20, 1, '2026-03-23 08:00:00'),

(10, 4, 'OPPO Reno 15 5G',
    'Chip Dimensity 8350, camera AI 50MP, sạc nhanh 80W, màn hình AMOLED 120Hz',
    'Cái', 'assets/img/product/iphone/oppo-reno15-5g-9.webp',
    10000000.00, 18.00, 12, 1, '2026-03-23 08:00:00'),

(11, 4, 'OPPO Reno 14F',
    'Chip Snapdragon 6 Gen 1, camera 64MP, pin 5000mAh, sạc nhanh 67W',
    'Cái', 'assets/img/product/iphone/oppo-reno14-f-g.webp',
    7500000.00, 18.00, 15, 1, '2026-03-23 08:00:00'),

(12, 4, 'OPPO Find N3 Flip',
    'Màn hình gập dọc 6.8 inch, camera Hasselblad 50MP, Dimensity 9200, sạc 44W',
    'Cái', 'assets/img/product/iphone/oppo-find-n3-flip.webp',
    18000000.00, 18.00, 6, 1, '2026-03-23 08:00:00'),


(13, 5, 'Vivo X200 Pro',
    'Camera ZEISS 200MP, Dimensity 9400, sạc nhanh 90W, màn hình AMOLED 120Hz cong',
    'Cái', 'assets/img/product/iphone/photo_2025-04-16_11-45-57.webp',
    20000000.00, 17.00, 8, 1, '2026-03-23 08:00:00');

-- --------------------------------------------------------

CREATE TABLE `v_gia_ban` (
`ma_sp` int(11),
`ten_sp` varchar(200),
`ten_loai` varchar(100),
`gia_von` decimal(15,2),
`ty_le_loi_nhuan` decimal(5,2),
`gia_ban` decimal(18,0),
`so_luong_ton` int(11),
`an_hien` tinyint(1)
);

DROP TABLE IF EXISTS `v_gia_ban`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_gia_ban` AS
SELECT `sp`.`ma_sp`, `sp`.`ten_sp`, `l`.`ten_loai`, `sp`.`gia_nhap_bq` AS `gia_von`,
       `sp`.`ty_le_loi_nhuan`, ROUND(`sp`.`gia_nhap_bq` * (1 + `sp`.`ty_le_loi_nhuan` / 100), 0) AS `gia_ban`,
       `sp`.`so_luong_ton`, `sp`.`an_hien`
FROM `san_pham` `sp`
JOIN `loai_sp` `l` ON `sp`.`ma_loai` = `l`.`ma_loai`;

-- --------------------------------------------------------
-- Indexes
-- --------------------------------------------------------

ALTER TABLE `admin`
  ADD PRIMARY KEY (`ma_admin`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`);

ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`ma_ctdh`),
  ADD KEY `fk_ctdh_dh` (`ma_dh`),
  ADD KEY `fk_ctdh_sp` (`ma_sp`);

ALTER TABLE `chi_tiet_phieu_nhap`
  ADD PRIMARY KEY (`ma_ctpn`),
  ADD KEY `fk_ctpn_phieu` (`ma_phieu`),
  ADD KEY `fk_ctpn_sp` (`ma_sp`);

ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`ma_dh`),
  ADD KEY `fk_dh_kh` (`ma_kh`);

ALTER TABLE `khach_hang`
  ADD PRIMARY KEY (`ma_kh`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `loai_sp`
  ADD PRIMARY KEY (`ma_loai`);

ALTER TABLE `phieu_nhap`
  ADD PRIMARY KEY (`ma_phieu`);

ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`ma_sp`),
  ADD KEY `fk_sp_loai` (`ma_loai`);

-- --------------------------------------------------------
-- AUTO_INCREMENT
-- --------------------------------------------------------

ALTER TABLE `admin`
  MODIFY `ma_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `chi_tiet_don_hang`
  MODIFY `ma_ctdh` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chi_tiet_phieu_nhap`
  MODIFY `ma_ctpn` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `don_hang`
  MODIFY `ma_dh` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `khach_hang`
  MODIFY `ma_kh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `loai_sp`
  MODIFY `ma_loai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `phieu_nhap`
  MODIFY `ma_phieu` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `san_pham`
  MODIFY `ma_sp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

-- --------------------------------------------------------
-- Constraints
-- --------------------------------------------------------

ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `fk_ctdh_dh` FOREIGN KEY (`ma_dh`) REFERENCES `don_hang` (`ma_dh`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ctdh_sp` FOREIGN KEY (`ma_sp`) REFERENCES `san_pham` (`ma_sp`) ON UPDATE CASCADE;

ALTER TABLE `chi_tiet_phieu_nhap`
  ADD CONSTRAINT `fk_ctpn_phieu` FOREIGN KEY (`ma_phieu`) REFERENCES `phieu_nhap` (`ma_phieu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ctpn_sp` FOREIGN KEY (`ma_sp`) REFERENCES `san_pham` (`ma_sp`) ON UPDATE CASCADE;

ALTER TABLE `don_hang`
  ADD CONSTRAINT `fk_dh_kh` FOREIGN KEY (`ma_kh`) REFERENCES `khach_hang` (`ma_kh`) ON UPDATE CASCADE;

ALTER TABLE `san_pham`
  ADD CONSTRAINT `fk_sp_loai` FOREIGN KEY (`ma_loai`) REFERENCES `loai_sp` (`ma_loai`) ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;