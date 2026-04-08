-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2026 at 10:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_ban_lo`
--

CREATE TABLE `chi_tiet_ban_lo` (
  `ma_chi_tiet_ban_lo` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `ma_chi_tiet_hd` int(11) NOT NULL,
  `ma_lo_hang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_hoa_don`
--

CREATE TABLE `chi_tiet_hoa_don` (
  `ma_chi_tiet_hd` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `gia_ban` double NOT NULL,
  `tong_tien` double NOT NULL,
  `ma_hoa_don` varchar(20) NOT NULL,
  `ma_hang_hoa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_huy_lo`
--

CREATE TABLE `chi_tiet_huy_lo` (
  `ma_chi_tiet_huy_lo` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `ma_chi_tiet_huy` int(11) NOT NULL,
  `ma_lo_hang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_phieu_huy`
--

CREATE TABLE `chi_tiet_phieu_huy` (
  `ma_chi_tiet_huy` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `ma_phieu_huy` varchar(20) NOT NULL,
  `ma_hang_hoa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_phieu_nhap`
--

CREATE TABLE `chi_tiet_phieu_nhap` (
  `ma_chi_tiet_nhap` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `don_gia_nhap` double NOT NULL,
  `ma_phieu_nhap` varchar(20) NOT NULL,
  `ma_hang_hoa` varchar(20) NOT NULL,
  `ma_lo_hang` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_phieu_xuat`
--

CREATE TABLE `chi_tiet_phieu_xuat` (
  `ma_chi_tiet_xuat` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `ma_phieu_xuat` varchar(20) NOT NULL,
  `ma_hang_hoa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_xuat_lo`
--

CREATE TABLE `chi_tiet_xuat_lo` (
  `ma_chi_tiet_xuat_lo` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `ma_chi_tiet_xuat` int(11) NOT NULL,
  `ma_lo_hang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chuc_vu`
--

CREATE TABLE `chuc_vu` (
  `ma_chuc_vu` varchar(20) NOT NULL,
  `ten_chuc_vu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `chuc_vu`
--

INSERT INTO `chuc_vu` (`ma_chuc_vu`, `ten_chuc_vu`) VALUES
('NV_QUAY_CAN', 'Nhân viên Quầy cân'),
('QUAN_LY', 'Quản lý'),
('ADMIN', 'Quản trị viên'),
('THU_KHO', 'Thủ kho'),
('THU_NGAN', 'Thu ngân');

-- --------------------------------------------------------

--
-- Table structure for table `danh_muc`
--

CREATE TABLE `danh_muc` (
  `ma_danh_muc` varchar(20) NOT NULL,
  `ten_danh_muc` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hang_hoa`
--

CREATE TABLE `hang_hoa` (
  `ma_hang_hoa` varchar(20) NOT NULL,
  `ten_hang_hoa` varchar(150) NOT NULL,
  `don_vi_tinh` varchar(30) NOT NULL,
  `gia_ban` double NOT NULL,
  `ma_vach` varchar(50) DEFAULT NULL,
  `trang_thai` enum('DANG_KINH_DOANH','NGUNG_KINH_DOANH') DEFAULT 'DANG_KINH_DOANH',
  `ma_danh_muc` varchar(20) DEFAULT NULL,
  `ma_nha_cung_cap` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hoa_don`
--

CREATE TABLE `hoa_don` (
  `ma_hoa_don` varchar(20) NOT NULL,
  `ngay_tao` datetime NOT NULL,
  `tong_tien` double NOT NULL DEFAULT 0,
  `trang_thai` enum('DANG_XU_LY','HOAN_TAT','HUY') DEFAULT 'DANG_XU_LY',
  `phuong_thuc_thanh_toan` varchar(50) NOT NULL DEFAULT 'Tiền mặt',
  `tien_khach_dua` double DEFAULT NULL,
  `ma_nhan_vien` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lo_hang`
--

CREATE TABLE `lo_hang` (
  `ma_lo_hang` varchar(30) NOT NULL,
  `ngay_san_xuat` date NOT NULL,
  `han_su_dung` date NOT NULL,
  `so_luong_trong_kho` int(11) NOT NULL DEFAULT 0,
  `so_luong_tren_ke` int(11) NOT NULL DEFAULT 0,
  `ma_hang_hoa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nhan_vien`
--

CREATE TABLE `nhan_vien` (
  `ma_nhan_vien` varchar(20) NOT NULL,
  `ten_nhan_vien` varchar(100) NOT NULL,
  `gioi_tinh` varchar(10) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `ma_chuc_vu` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `nhan_vien`
--

INSERT INTO `nhan_vien` (`ma_nhan_vien`, `ten_nhan_vien`, `gioi_tinh`, `so_dien_thoai`, `email`, `dia_chi`, `ngay_sinh`, `ma_chuc_vu`) VALUES
('NV001', 'Quản trị viên', 'Nam', '0900000001', 'admin@foodstore.vn', 'Hà Nộia', '1990-01-13', 'ADMIN'),
('NV002', 'こんばんは', 'Nam', '090000003', 'ivanogawau56f@trendstex.com', NULL, NULL, 'QUAN_LY');

-- --------------------------------------------------------

--
-- Table structure for table `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `ma_nha_cung_cap` varchar(20) NOT NULL,
  `ten_nha_cung_cap` varchar(100) NOT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `ten_nguoi_lien_he` varchar(100) DEFAULT NULL,
  `trang_thai` enum('HOAT_DONG','VO_HIEU_HOA') DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phieu_huy_hang`
--

CREATE TABLE `phieu_huy_hang` (
  `ma_phieu_huy` varchar(20) NOT NULL,
  `ngay_tao` date NOT NULL,
  `tong_so_luong` int(11) NOT NULL DEFAULT 0,
  `ly_do_huy` text NOT NULL,
  `trang_thai` enum('CHO_DUYET','DA_DUYET','TU_CHOI') DEFAULT 'CHO_DUYET',
  `ma_nhan_vien` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phieu_nhap_hang`
--

CREATE TABLE `phieu_nhap_hang` (
  `ma_phieu_nhap` varchar(20) NOT NULL,
  `ngay_tao` date NOT NULL,
  `tong_so_luong` int(11) NOT NULL DEFAULT 0,
  `tong_tien` double NOT NULL DEFAULT 0,
  `ghi_chu` text DEFAULT NULL,
  `ma_nhan_vien` varchar(20) DEFAULT NULL,
  `ma_nha_cung_cap` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phieu_xuat_hang`
--

CREATE TABLE `phieu_xuat_hang` (
  `ma_phieu_xuat` varchar(20) NOT NULL,
  `ngay_tao` date NOT NULL,
  `tong_so_luong` int(11) NOT NULL DEFAULT 0,
  `ghi_chu` text DEFAULT NULL,
  `ma_nhan_vien` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tai_khoan`
--

CREATE TABLE `tai_khoan` (
  `ma_tai_khoan` int(11) NOT NULL,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `trang_thai` enum('HOAT_DONG','VO_HIEU_HOA') DEFAULT 'HOAT_DONG',
  `ma_nhan_vien` varchar(20) DEFAULT NULL,
  `ma_chuc_vu` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tai_khoan`
--

INSERT INTO `tai_khoan` (`ma_tai_khoan`, `ten_dang_nhap`, `password`, `trang_thai`, `ma_nhan_vien`, `ma_chuc_vu`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HOAT_DONG', 'NV001', 'ADMIN'),
(2, 'admin1', '$2y$10$Xss06fXdiixuH0y4aIXjSO8w54FXsBFN49Fcf4htSW/vPTUUedLi6', 'HOAT_DONG', 'NV002', 'QUAN_LY');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chi_tiet_ban_lo`
--
ALTER TABLE `chi_tiet_ban_lo`
  ADD PRIMARY KEY (`ma_chi_tiet_ban_lo`),
  ADD KEY `ma_chi_tiet_hd` (`ma_chi_tiet_hd`),
  ADD KEY `ma_lo_hang` (`ma_lo_hang`);

--
-- Indexes for table `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD PRIMARY KEY (`ma_chi_tiet_hd`),
  ADD KEY `ma_hoa_don` (`ma_hoa_don`),
  ADD KEY `ma_hang_hoa` (`ma_hang_hoa`);

--
-- Indexes for table `chi_tiet_huy_lo`
--
ALTER TABLE `chi_tiet_huy_lo`
  ADD PRIMARY KEY (`ma_chi_tiet_huy_lo`),
  ADD KEY `ma_chi_tiet_huy` (`ma_chi_tiet_huy`),
  ADD KEY `ma_lo_hang` (`ma_lo_hang`);

--
-- Indexes for table `chi_tiet_phieu_huy`
--
ALTER TABLE `chi_tiet_phieu_huy`
  ADD PRIMARY KEY (`ma_chi_tiet_huy`),
  ADD KEY `ma_phieu_huy` (`ma_phieu_huy`),
  ADD KEY `ma_hang_hoa` (`ma_hang_hoa`);

--
-- Indexes for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD PRIMARY KEY (`ma_chi_tiet_nhap`),
  ADD KEY `ma_phieu_nhap` (`ma_phieu_nhap`),
  ADD KEY `ma_hang_hoa` (`ma_hang_hoa`),
  ADD KEY `ma_lo_hang` (`ma_lo_hang`);

--
-- Indexes for table `chi_tiet_phieu_xuat`
--
ALTER TABLE `chi_tiet_phieu_xuat`
  ADD PRIMARY KEY (`ma_chi_tiet_xuat`),
  ADD KEY `ma_phieu_xuat` (`ma_phieu_xuat`),
  ADD KEY `ma_hang_hoa` (`ma_hang_hoa`);

--
-- Indexes for table `chi_tiet_xuat_lo`
--
ALTER TABLE `chi_tiet_xuat_lo`
  ADD PRIMARY KEY (`ma_chi_tiet_xuat_lo`),
  ADD KEY `ma_chi_tiet_xuat` (`ma_chi_tiet_xuat`),
  ADD KEY `ma_lo_hang` (`ma_lo_hang`);

--
-- Indexes for table `chuc_vu`
--
ALTER TABLE `chuc_vu`
  ADD PRIMARY KEY (`ma_chuc_vu`),
  ADD UNIQUE KEY `ten_chuc_vu` (`ten_chuc_vu`);

--
-- Indexes for table `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`ma_danh_muc`),
  ADD UNIQUE KEY `ten_danh_muc` (`ten_danh_muc`);

--
-- Indexes for table `hang_hoa`
--
ALTER TABLE `hang_hoa`
  ADD PRIMARY KEY (`ma_hang_hoa`),
  ADD UNIQUE KEY `ma_vach` (`ma_vach`),
  ADD KEY `ma_danh_muc` (`ma_danh_muc`),
  ADD KEY `ma_nha_cung_cap` (`ma_nha_cung_cap`);

--
-- Indexes for table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD PRIMARY KEY (`ma_hoa_don`),
  ADD KEY `ma_nhan_vien` (`ma_nhan_vien`);

--
-- Indexes for table `lo_hang`
--
ALTER TABLE `lo_hang`
  ADD PRIMARY KEY (`ma_lo_hang`),
  ADD KEY `ma_hang_hoa` (`ma_hang_hoa`);

--
-- Indexes for table `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD PRIMARY KEY (`ma_nhan_vien`),
  ADD KEY `ma_chuc_vu` (`ma_chuc_vu`);

--
-- Indexes for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`ma_nha_cung_cap`);

--
-- Indexes for table `phieu_huy_hang`
--
ALTER TABLE `phieu_huy_hang`
  ADD PRIMARY KEY (`ma_phieu_huy`),
  ADD KEY `ma_nhan_vien` (`ma_nhan_vien`);

--
-- Indexes for table `phieu_nhap_hang`
--
ALTER TABLE `phieu_nhap_hang`
  ADD PRIMARY KEY (`ma_phieu_nhap`),
  ADD KEY `ma_nhan_vien` (`ma_nhan_vien`),
  ADD KEY `ma_nha_cung_cap` (`ma_nha_cung_cap`);

--
-- Indexes for table `phieu_xuat_hang`
--
ALTER TABLE `phieu_xuat_hang`
  ADD PRIMARY KEY (`ma_phieu_xuat`),
  ADD KEY `ma_nhan_vien` (`ma_nhan_vien`);

--
-- Indexes for table `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD PRIMARY KEY (`ma_tai_khoan`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  ADD UNIQUE KEY `ma_nhan_vien` (`ma_nhan_vien`),
  ADD KEY `ma_chuc_vu` (`ma_chuc_vu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chi_tiet_ban_lo`
--
ALTER TABLE `chi_tiet_ban_lo`
  MODIFY `ma_chi_tiet_ban_lo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  MODIFY `ma_chi_tiet_hd` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_huy_lo`
--
ALTER TABLE `chi_tiet_huy_lo`
  MODIFY `ma_chi_tiet_huy_lo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_phieu_huy`
--
ALTER TABLE `chi_tiet_phieu_huy`
  MODIFY `ma_chi_tiet_huy` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  MODIFY `ma_chi_tiet_nhap` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_phieu_xuat`
--
ALTER TABLE `chi_tiet_phieu_xuat`
  MODIFY `ma_chi_tiet_xuat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_xuat_lo`
--
ALTER TABLE `chi_tiet_xuat_lo`
  MODIFY `ma_chi_tiet_xuat_lo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tai_khoan`
--
ALTER TABLE `tai_khoan`
  MODIFY `ma_tai_khoan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chi_tiet_ban_lo`
--
ALTER TABLE `chi_tiet_ban_lo`
  ADD CONSTRAINT `chi_tiet_ban_lo_ibfk_1` FOREIGN KEY (`ma_chi_tiet_hd`) REFERENCES `chi_tiet_hoa_don` (`ma_chi_tiet_hd`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chi_tiet_ban_lo_ibfk_2` FOREIGN KEY (`ma_lo_hang`) REFERENCES `lo_hang` (`ma_lo_hang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD CONSTRAINT `chi_tiet_hoa_don_ibfk_1` FOREIGN KEY (`ma_hoa_don`) REFERENCES `hoa_don` (`ma_hoa_don`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chi_tiet_hoa_don_ibfk_2` FOREIGN KEY (`ma_hang_hoa`) REFERENCES `hang_hoa` (`ma_hang_hoa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chi_tiet_huy_lo`
--
ALTER TABLE `chi_tiet_huy_lo`
  ADD CONSTRAINT `chi_tiet_huy_lo_ibfk_1` FOREIGN KEY (`ma_chi_tiet_huy`) REFERENCES `chi_tiet_phieu_huy` (`ma_chi_tiet_huy`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chi_tiet_huy_lo_ibfk_2` FOREIGN KEY (`ma_lo_hang`) REFERENCES `lo_hang` (`ma_lo_hang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chi_tiet_phieu_huy`
--
ALTER TABLE `chi_tiet_phieu_huy`
  ADD CONSTRAINT `chi_tiet_phieu_huy_ibfk_1` FOREIGN KEY (`ma_phieu_huy`) REFERENCES `phieu_huy_hang` (`ma_phieu_huy`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chi_tiet_phieu_huy_ibfk_2` FOREIGN KEY (`ma_hang_hoa`) REFERENCES `hang_hoa` (`ma_hang_hoa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_1` FOREIGN KEY (`ma_phieu_nhap`) REFERENCES `phieu_nhap_hang` (`ma_phieu_nhap`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_2` FOREIGN KEY (`ma_hang_hoa`) REFERENCES `hang_hoa` (`ma_hang_hoa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_3` FOREIGN KEY (`ma_lo_hang`) REFERENCES `lo_hang` (`ma_lo_hang`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `chi_tiet_phieu_xuat`
--
ALTER TABLE `chi_tiet_phieu_xuat`
  ADD CONSTRAINT `chi_tiet_phieu_xuat_ibfk_1` FOREIGN KEY (`ma_phieu_xuat`) REFERENCES `phieu_xuat_hang` (`ma_phieu_xuat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chi_tiet_phieu_xuat_ibfk_2` FOREIGN KEY (`ma_hang_hoa`) REFERENCES `hang_hoa` (`ma_hang_hoa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chi_tiet_xuat_lo`
--
ALTER TABLE `chi_tiet_xuat_lo`
  ADD CONSTRAINT `chi_tiet_xuat_lo_ibfk_1` FOREIGN KEY (`ma_chi_tiet_xuat`) REFERENCES `chi_tiet_phieu_xuat` (`ma_chi_tiet_xuat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chi_tiet_xuat_lo_ibfk_2` FOREIGN KEY (`ma_lo_hang`) REFERENCES `lo_hang` (`ma_lo_hang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hang_hoa`
--
ALTER TABLE `hang_hoa`
  ADD CONSTRAINT `hang_hoa_ibfk_1` FOREIGN KEY (`ma_danh_muc`) REFERENCES `danh_muc` (`ma_danh_muc`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `hang_hoa_ibfk_2` FOREIGN KEY (`ma_nha_cung_cap`) REFERENCES `nha_cung_cap` (`ma_nha_cung_cap`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `hoa_don_ibfk_1` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lo_hang`
--
ALTER TABLE `lo_hang`
  ADD CONSTRAINT `lo_hang_ibfk_1` FOREIGN KEY (`ma_hang_hoa`) REFERENCES `hang_hoa` (`ma_hang_hoa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD CONSTRAINT `nhan_vien_ibfk_1` FOREIGN KEY (`ma_chuc_vu`) REFERENCES `chuc_vu` (`ma_chuc_vu`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `phieu_huy_hang`
--
ALTER TABLE `phieu_huy_hang`
  ADD CONSTRAINT `phieu_huy_hang_ibfk_1` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `phieu_nhap_hang`
--
ALTER TABLE `phieu_nhap_hang`
  ADD CONSTRAINT `phieu_nhap_hang_ibfk_1` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `phieu_nhap_hang_ibfk_2` FOREIGN KEY (`ma_nha_cung_cap`) REFERENCES `nha_cung_cap` (`ma_nha_cung_cap`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `phieu_xuat_hang`
--
ALTER TABLE `phieu_xuat_hang`
  ADD CONSTRAINT `phieu_xuat_hang_ibfk_1` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD CONSTRAINT `tai_khoan_ibfk_1` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tai_khoan_ibfk_2` FOREIGN KEY (`ma_chuc_vu`) REFERENCES `chuc_vu` (`ma_chuc_vu`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
