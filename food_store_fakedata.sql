-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 10, 2026 at 03:56 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

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
  `ma_chi_tiet_ban_lo` int NOT NULL,
  `so_luong` int NOT NULL,
  `ma_chi_tiet_hd` int NOT NULL,
  `ma_lo_hang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `chi_tiet_ban_lo`
--

INSERT INTO `chi_tiet_ban_lo` (`ma_chi_tiet_ban_lo`, `so_luong`, `ma_chi_tiet_hd`, `ma_lo_hang`) VALUES
(1, 2, 1, 'LO20260401-001'),
(2, 5, 2, 'LO20260402-001'),
(3, 10, 3, 'LO20260401-002'),
(4, 3, 4, 'LO20260401-002'),
(5, 2, 5, 'LO20260401-001'),
(6, 1, 6, 'LO20260402-001'),
(7, 20, 7, 'LO20260404-001'),
(8, 10, 8, 'LO20260404-001'),
(9, 8, 9, 'LO20260401-002'),
(10, 3, 10, 'LO20260410-001'),
(11, 5, 11, 'LO20260411-001'),
(12, 10, 12, 'LO20260410-002'),
(13, 2, 13, 'LO20260411-001'),
(14, 15, 14, 'LO20260411-002');

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_hoa_don`
--

CREATE TABLE `chi_tiet_hoa_don` (
  `ma_chi_tiet_hd` int NOT NULL,
  `so_luong` int NOT NULL,
  `gia_ban` double NOT NULL,
  `tong_tien` double NOT NULL,
  `ma_hoa_don` varchar(20) NOT NULL,
  `ma_hang_hoa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `chi_tiet_hoa_don`
--

INSERT INTO `chi_tiet_hoa_don` (`ma_chi_tiet_hd`, `so_luong`, `gia_ban`, `tong_tien`, `ma_hoa_don`, `ma_hang_hoa`) VALUES
(1, 2, 285000, 570000, 'HD20260408-001', 'HH001'),
(2, 5, 32000, 160000, 'HD20260408-001', 'HH005'),
(3, 10, 25000, 250000, 'HD20260408-002', 'HH003'),
(4, 3, 180000, 540000, 'HD20260409-001', 'HH0023'),
(5, 2, 450000, 900000, 'HD20260409-001', 'HH011'),
(6, 1, 65000, 65000, 'HD20260409-001', 'HH013'),
(7, 20, 9000, 180000, 'HD20260410-001', 'HH016'),
(8, 10, 28000, 280000, 'HD20260410-001', 'HH009'),
(9, 8, 25000, 200000, 'HD20260410-001', 'HH003'),
(10, 3, 135000, 405000, 'HD20260410-002', 'HH021'),
(11, 5, 45000, 225000, 'HD20260410-002', 'HH023'),
(12, 10, 22000, 220000, 'HD20260410-002', 'HH022'),
(13, 2, 125000, 250000, 'HD20260411-001', 'HH027'),
(14, 15, 18000, 270000, 'HD20260411-001', 'HH025');

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_huy_lo`
--

CREATE TABLE `chi_tiet_huy_lo` (
  `ma_chi_tiet_huy_lo` int NOT NULL,
  `so_luong` int NOT NULL,
  `ma_chi_tiet_huy` int NOT NULL,
  `ma_lo_hang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `chi_tiet_huy_lo`
--

INSERT INTO `chi_tiet_huy_lo` (`ma_chi_tiet_huy_lo`, `so_luong`, `ma_chi_tiet_huy`, `ma_lo_hang`) VALUES
(1, 15, 1, 'LO20260401-002'),
(2, 10, 2, 'LO20260401-002'),
(3, 15, 3, 'LO20260404-001');

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_phieu_huy`
--

CREATE TABLE `chi_tiet_phieu_huy` (
  `ma_chi_tiet_huy` int NOT NULL,
  `so_luong` int NOT NULL,
  `ma_phieu_huy` varchar(20) NOT NULL,
  `ma_hang_hoa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `chi_tiet_phieu_huy`
--

INSERT INTO `chi_tiet_phieu_huy` (`ma_chi_tiet_huy`, `so_luong`, `ma_phieu_huy`, `ma_hang_hoa`) VALUES
(1, 15, 'PH20260409-001', 'HH003'),
(2, 10, 'PH20260409-001', 'HH004'),
(3, 15, 'PH20260410-001', 'HH009');

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_phieu_nhap`
--

CREATE TABLE `chi_tiet_phieu_nhap` (
  `ma_chi_tiet_nhap` int NOT NULL,
  `so_luong` int NOT NULL,
  `don_gia_nhap` double NOT NULL,
  `ma_phieu_nhap` varchar(20) NOT NULL,
  `ma_hang_hoa` varchar(20) NOT NULL,
  `ma_lo_hang` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `chi_tiet_phieu_nhap`
--

INSERT INTO `chi_tiet_phieu_nhap` (`ma_chi_tiet_nhap`, `so_luong`, `don_gia_nhap`, `ma_phieu_nhap`, `ma_hang_hoa`, `ma_lo_hang`) VALUES
(1, 150, 245000, 'PN20260408-001', 'HH001', 'LO20260401-001'),
(2, 300, 18000, 'PN20260408-002', 'HH003', 'LO20260401-002'),
(3, 200, 28000, 'PN20260408-001', 'HH005', 'LO20260402-001'),
(4, 200, 115000, 'PN20260410-001', 'HH021', 'LO20260410-001'),
(5, 400, 18000, 'PN20260410-001', 'HH022', 'LO20260410-002'),
(6, 120, 38000, 'PN20260411-001', 'HH023', 'LO20260411-001'),
(7, 300, 14000, 'PN20260411-001', 'HH025', 'LO20260411-002');

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_phieu_xuat`
--

CREATE TABLE `chi_tiet_phieu_xuat` (
  `ma_chi_tiet_xuat` int NOT NULL,
  `so_luong` int NOT NULL,
  `ma_phieu_xuat` varchar(20) NOT NULL,
  `ma_hang_hoa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `chi_tiet_phieu_xuat`
--

INSERT INTO `chi_tiet_phieu_xuat` (`ma_chi_tiet_xuat`, `so_luong`, `ma_phieu_xuat`, `ma_hang_hoa`) VALUES
(1, 50, 'PX20260408-001', 'HH001'),
(2, 30, 'PX20260408-001', 'HH005'),
(3, 40, 'PX20260409-001', 'HH003'),
(4, 20, 'PX20260409-001', 'HH009');

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_xuat_lo`
--

CREATE TABLE `chi_tiet_xuat_lo` (
  `ma_chi_tiet_xuat_lo` int NOT NULL,
  `so_luong` int NOT NULL,
  `ma_chi_tiet_xuat` int NOT NULL,
  `ma_lo_hang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `chi_tiet_xuat_lo`
--

INSERT INTO `chi_tiet_xuat_lo` (`ma_chi_tiet_xuat_lo`, `so_luong`, `ma_chi_tiet_xuat`, `ma_lo_hang`) VALUES
(1, 50, 1, 'LO20260401-001'),
(2, 30, 2, 'LO20260402-001'),
(3, 40, 3, 'LO20260401-002'),
(4, 20, 4, 'LO20260404-001');

-- --------------------------------------------------------

--
-- Table structure for table `chuc_vu`
--

CREATE TABLE `chuc_vu` (
  `ma_chuc_vu` varchar(20) NOT NULL,
  `ten_chuc_vu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

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
  `mo_ta` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `danh_muc`
--

INSERT INTO `danh_muc` (`ma_danh_muc`, `ten_danh_muc`, `mo_ta`) VALUES
('DM001', 'Thịt tươi & Hải sản', 'Thịt bò, gà, cá, tôm...'),
('DM002', 'Rau củ quả', 'Rau sạch, củ quả tươi mới'),
('DM003', 'Sữa & Sản phẩm từ sữa', 'Sữa tươi, sữa chua, phô mai'),
('DM004', 'Đồ khô & Gia vị', 'Mì, bún, gia vị, dầu ăn'),
('DM005', 'Đồ uống', 'Nước ngọt, nước khoáng, trà sữa'),
('DM006', 'Bánh kẹo & Snacks', 'Bánh quy, kẹo, bim bim'),
('DM007', 'Hàng đông lạnh', 'Kem, đồ đông lạnh'),
('DM008', 'Thực phẩm đóng hộp', 'Đồ hộp, xúc xích, pate'),
('DM0081', 'Đồ ăn chay nấu sẵn ', 'Các đồ ăn làm từ thực vật có hình dạng giống món ăn bình thường');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `hang_hoa`
--

INSERT INTO `hang_hoa` (`ma_hang_hoa`, `ten_hang_hoa`, `don_vi_tinh`, `gia_ban`, `ma_vach`, `trang_thai`, `ma_danh_muc`, `ma_nha_cung_cap`) VALUES
('HH001', 'Thịt bò bắp Úc', 'kg', 285000, '8850123456789', 'DANG_KINH_DOANH', 'DM001', 'NCC001'),
('HH002', 'Cá basa fillet', 'kg', 95000, '8850123456790', 'DANG_KINH_DOANH', 'DM001', 'NCC002'),
('HH0023', 'Gà chay', 'Hộp', 187000, '023548135848', 'DANG_KINH_DOANH', 'DM0081', 'NCC003'),
('HH003', 'Cà chua tươi', 'kg', 25000, '8850123456791', 'DANG_KINH_DOANH', 'DM002', 'NCC003'),
('HH004', 'Khoai tây', 'kg', 18000, '8850123456792', 'DANG_KINH_DOANH', 'DM002', 'NCC003'),
('HH005', 'Sữa tươi Vinamilk 1L', 'chai', 32000, '8934567890123', 'DANG_KINH_DOANH', 'DM003', 'NCC004'),
('HH006', 'Sữa chua uống Probi', 'chai', 8000, '8934567890124', 'DANG_KINH_DOANH', 'DM003', 'NCC004'),
('HH007', 'Mì Omachi xốt bò hầm', 'gói', 12000, '8934567890125', 'DANG_KINH_DOANH', 'DM004', 'NCC005'),
('HH008', 'Dầu ăn Simply 1L', 'chai', 45000, '8934567890126', 'DANG_KINH_DOANH', 'DM004', 'NCC005'),
('HH009', 'Coca Cola 1.5L', 'chai', 28000, '8850123456793', 'DANG_KINH_DOANH', 'DM005', 'NCC001'),
('HH010', 'Bánh Oreo', 'gói', 18000, '8850123456794', 'DANG_KINH_DOANH', 'DM006', 'NCC005'),
('HH011', 'Cá hồi Na Uy', 'kg', 450000, '8850123456795', 'DANG_KINH_DOANH', 'DM001', 'NCC002'),
('HH012', 'Rau bina hữu cơ', 'kg', 45000, '8850123456796', 'DANG_KINH_DOANH', 'DM002', 'NCC003'),
('HH013', 'Phô mai con bò cười', 'hộp', 65000, '8934567890127', 'DANG_KINH_DOANH', 'DM003', 'NCC004'),
('HH014', 'Nước mắm Nam Ngư 750ml', 'chai', 42000, '8934567890128', 'DANG_KINH_DOANH', 'DM004', 'NCC001'),
('HH015', 'Pepsi 1.5L', 'chai', 26000, '8850123456797', 'DANG_KINH_DOANH', 'DM005', 'NCC001'),
('HH016', 'Bim bim Oishi', 'gói', 9000, '8850123456798', 'DANG_KINH_DOANH', 'DM006', 'NCC005'),
('HH017', 'Kem Wall\'s Cup', 'cốc', 15000, '8850123456799', 'DANG_KINH_DOANH', 'DM007', 'NCC004'),
('HH018', 'Xúc xích CP', 'gói', 35000, '8934567890129', 'DANG_KINH_DOANH', 'DM008', 'NCC001'),
('HH019', 'Đậu xanh', 'kg', 28000, '8850123456800', 'DANG_KINH_DOANH', 'DM004', 'NCC003'),
('HH020', 'Chuối già', 'kg', 22000, '8850123456801', 'DANG_KINH_DOANH', 'DM002', 'NCC003'),
('HH021', 'Thịt gà ta sạch', 'kg', 135000, '8850123456802', 'DANG_KINH_DOANH', 'DM001', 'NCC006'),
('HH022', 'Cà rốt tươi', 'kg', 22000, '8850123456803', 'DANG_KINH_DOANH', 'DM002', 'NCC007'),
('HH023', 'Sữa hạt óc chó', 'hộp', 45000, '8934567890130', 'DANG_KINH_DOANH', 'DM003', 'NCC004'),
('HH024', 'Nước tương Maggi', 'chai', 38000, '8934567890131', 'DANG_KINH_DOANH', 'DM004', 'NCC005'),
('HH025', 'Trà sữa trân châu đóng chai', 'chai', 18000, '8850123456804', 'DANG_KINH_DOANH', 'DM005', 'NCC006'),
('HH026', 'Bánh quy bơ Danisa', 'hộp', 65000, '8850123456805', 'DANG_KINH_DOANH', 'DM006', 'NCC005'),
('HH027', 'Kem Häagen-Dazs', 'hộp', 125000, '8850123456806', 'DANG_KINH_DOANH', 'DM007', 'NCC004'),
('HH028', 'Pate gan gà', 'hộp', 42000, '8934567890132', 'DANG_KINH_DOANH', 'DM008', 'NCC006'),
('HH029', 'Đậu phụ tươi', 'kg', 15000, '8850123456807', 'DANG_KINH_DOANH', 'DM0081', 'NCC007'),
('HH030', 'Nước ép cam tươi', 'chai', 32000, '8850123456808', 'DANG_KINH_DOANH', 'DM005', 'NCC007');

-- --------------------------------------------------------

--
-- Table structure for table `hoa_don`
--

CREATE TABLE `hoa_don` (
  `ma_hoa_don` varchar(20) NOT NULL,
  `ngay_tao` datetime NOT NULL,
  `tong_tien` double NOT NULL DEFAULT '0',
  `trang_thai` enum('DANG_XU_LY','HOAN_TAT','HUY') DEFAULT 'DANG_XU_LY',
  `phuong_thuc_thanh_toan` varchar(50) NOT NULL DEFAULT 'Tiền mặt',
  `tien_khach_dua` double DEFAULT NULL,
  `ma_nhan_vien` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `hoa_don`
--

INSERT INTO `hoa_don` (`ma_hoa_don`, `ngay_tao`, `tong_tien`, `trang_thai`, `phuong_thuc_thanh_toan`, `tien_khach_dua`, `ma_nhan_vien`) VALUES
('HD20260408-001', '2026-04-08 09:15:00', 1250000, 'HOAN_TAT', 'Tiền mặt', 1300000, 'NV001'),
('HD20260408-002', '2026-04-08 10:30:00', 850000, 'HOAN_TAT', 'Chuyển khoản', 850000, 'NV002'),
('HD20260409-001', '2026-04-09 14:20:00', 2450000, 'HOAN_TAT', 'Tiền mặt', 2500000, 'NV001'),
('HD20260410-001', '2026-04-10 08:45:00', 680000, 'DANG_XU_LY', 'Tiền mặt', NULL, 'NV002'),
('HD20260410-002', '2026-04-10 16:30:00', 1680000, 'HOAN_TAT', 'Tiền mặt', 1700000, 'NV003'),
('HD20260411-001', '2026-04-11 09:20:00', 1240000, 'HOAN_TAT', 'Chuyển khoản', 1240000, 'NV001');

-- --------------------------------------------------------

--
-- Table structure for table `lo_hang`
--

CREATE TABLE `lo_hang` (
  `ma_lo_hang` varchar(30) NOT NULL,
  `ngay_san_xuat` date NOT NULL,
  `han_su_dung` date NOT NULL,
  `so_luong_trong_kho` int NOT NULL DEFAULT '0',
  `so_luong_tren_ke` int NOT NULL DEFAULT '0',
  `ma_hang_hoa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `lo_hang`
--

INSERT INTO `lo_hang` (`ma_lo_hang`, `ngay_san_xuat`, `han_su_dung`, `so_luong_trong_kho`, `so_luong_tren_ke`, `ma_hang_hoa`) VALUES
('LO20260401-001', '2026-03-15', '2026-09-15', 150, 50, 'HH001'),
('LO20260401-002', '2026-04-01', '2026-06-01', 500, 120, 'HH003'),
('LO20260402-001', '2026-03-20', '2026-07-20', 200, 80, 'HH005'),
('LO20260403-001', '2026-04-05', '2026-10-05', 180, 60, 'HH007'),
('LO20260404-001', '2026-04-08', '2026-05-08', 500, 200, 'HH009'),
('LO20260410-001', '2026-04-08', '2026-10-08', 200, 80, 'HH021'),
('LO20260410-002', '2026-04-09', '2026-06-09', 400, 150, 'HH022'),
('LO20260411-001', '2026-04-10', '2026-09-10', 120, 60, 'HH023'),
('LO20260411-002', '2026-04-10', '2026-05-10', 300, 100, 'HH025');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `nhan_vien`
--

INSERT INTO `nhan_vien` (`ma_nhan_vien`, `ten_nhan_vien`, `gioi_tinh`, `so_dien_thoai`, `email`, `dia_chi`, `ngay_sinh`, `ma_chuc_vu`) VALUES
('NV001', 'Quản trị viên', 'Nam', '0900000001', 'admin@foodstore.vn', 'Hà Nộia', '1990-01-13', 'ADMIN'),
('NV002', 'こんばんは', 'Nam', '090000003', 'ivanogawau56f@trendstex.com', NULL, NULL, 'QUAN_LY'),
('NV004', 'Lê Minh Quân', 'Nam', '0918234567', 'minhquan@foodstore.vn', 'Hà Nội', '1995-11-20', 'THU_KHO'),
('NV005', 'Phạm Thị Hương', 'Nữ', '0976345678', 'huongpt@foodstore.vn', 'Hà Nội', '2000-03-15', 'NV_QUAY_CAN'),
('NV006', 'Trần Thị Ngọc Anh', 'Nữ', '0987123456', 'ngocanh@foodstore.vn', 'Hà Nội', '1998-05-12', 'THU_NGAN');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`ma_nha_cung_cap`, `ten_nha_cung_cap`, `dia_chi`, `email`, `so_dien_thoai`, `ten_nguoi_lien_he`, `trang_thai`) VALUES
('NCC001', 'Công ty TNHH Thực phẩm Sạch Việt', 'Hà Nội', 'contact@thucphamsach.vn', '0912345678', 'Nguyễn Văn A', 'HOAT_DONG'),
('NCC002', 'Hải Sản Đại Dương', 'Hải Phòng', 'sales@haidauduong.com', '0987654321', 'Trần Thị B', 'HOAT_DONG'),
('NCC003', 'Rau Củ Organic Farm', 'Đà Lạt', 'info@organicfarm.vn', '0934567890', 'Lê Văn C', 'HOAT_DONG'),
('NCC004', 'Vinamilk', 'TP.HCM', 'support@vinamilk.com.vn', '18001234', 'Phạm Thị D', 'HOAT_DONG'),
('NCC005', 'Acecook Việt Nam', 'Bình Dương', 'acecook@acecook.vn', '02743891234', 'Hoàng Văn E', 'HOAT_DONG'),
('NCC006', 'Công ty CP Thực phẩm Minh Phú', 'TP.HCM', 'minhphu@foodsupply.vn', '02839391234', 'Nguyễn Thị Lan', 'HOAT_DONG'),
('NCC007', 'Nông trại Sạch Ba Vì', 'Hà Nội', 'bavi@organic.vn', '0912456789', 'Đặng Văn Hải', 'HOAT_DONG'),
('NCC0089', 'Công ty Bánh kẹo Hồng Đạt', 'Phường Đông A, tỉnh Ninh Bình', 'hongdat@gmail.com', '01258935899', 'Phạm Hồng Đạt', 'HOAT_DONG');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_huy_hang`
--

CREATE TABLE `phieu_huy_hang` (
  `ma_phieu_huy` varchar(20) NOT NULL,
  `ngay_tao` date NOT NULL,
  `tong_so_luong` int NOT NULL DEFAULT '0',
  `ly_do_huy` text NOT NULL,
  `trang_thai` enum('CHO_DUYET','DA_DUYET','TU_CHOI') DEFAULT 'CHO_DUYET',
  `ma_nhan_vien` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `phieu_huy_hang`
--

INSERT INTO `phieu_huy_hang` (`ma_phieu_huy`, `ngay_tao`, `tong_so_luong`, `ly_do_huy`, `trang_thai`, `ma_nhan_vien`) VALUES
('PH20260409-001', '2026-04-09', 25, 'Hết hạn sử dụng', 'DA_DUYET', 'NV001'),
('PH20260410-001', '2026-04-10', 15, 'Hỏng vỏ bao bì', 'CHO_DUYET', 'NV002');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_nhap_hang`
--

CREATE TABLE `phieu_nhap_hang` (
  `ma_phieu_nhap` varchar(20) NOT NULL,
  `ngay_tao` date NOT NULL,
  `tong_so_luong` int NOT NULL DEFAULT '0',
  `tong_tien` double NOT NULL DEFAULT '0',
  `ghi_chu` text,
  `ma_nhan_vien` varchar(20) DEFAULT NULL,
  `ma_nha_cung_cap` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `phieu_nhap_hang`
--

INSERT INTO `phieu_nhap_hang` (`ma_phieu_nhap`, `ngay_tao`, `tong_so_luong`, `tong_tien`, `ghi_chu`, `ma_nhan_vien`, `ma_nha_cung_cap`) VALUES
('PN20260408-001', '2026-04-08', 850, 12500000, 'Nhập lô hàng tháng 4', 'NV001', 'NCC001'),
('PN20260408-002', '2026-04-08', 450, 8500000, 'Nhập rau củ', 'NV001', 'NCC003'),
('PN20260410-001', '2026-04-10', 620, 18500000, 'Nhập lô gà và rau mới', 'NV004', 'NCC006'),
('PN20260411-001', '2026-04-11', 520, 9800000, 'Nhập đồ uống và sữa hạt', 'NV004', 'NCC007');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_xuat_hang`
--

CREATE TABLE `phieu_xuat_hang` (
  `ma_phieu_xuat` varchar(20) NOT NULL,
  `ngay_tao` date NOT NULL,
  `tong_so_luong` int NOT NULL DEFAULT '0',
  `ghi_chu` text,
  `ma_nhan_vien` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `phieu_xuat_hang`
--

INSERT INTO `phieu_xuat_hang` (`ma_phieu_xuat`, `ngay_tao`, `tong_so_luong`, `ghi_chu`, `ma_nhan_vien`) VALUES
('PX20260408-001', '2026-04-08', 120, 'Xuất hàng cho quầy bán', 'NV001'),
('PX20260409-001', '2026-04-09', 80, 'Xuất hàng bổ sung', 'NV002');

-- --------------------------------------------------------

--
-- Table structure for table `tai_khoan`
--

CREATE TABLE `tai_khoan` (
  `ma_tai_khoan` int NOT NULL,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `trang_thai` enum('HOAT_DONG','VO_HIEU_HOA') DEFAULT 'HOAT_DONG',
  `ma_nhan_vien` varchar(20) DEFAULT NULL,
  `ma_chuc_vu` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tai_khoan`
--

INSERT INTO `tai_khoan` (`ma_tai_khoan`, `ten_dang_nhap`, `password`, `trang_thai`, `ma_nhan_vien`, `ma_chuc_vu`) VALUES
(1, 'admin', '$2y$10$zcDerUzSHxoOQHfsB64bZOafXf/kYUUZvEdNIeuCMSHTwbBsaFSUu', 'HOAT_DONG', 'NV001', 'ADMIN'),
(2, 'admin1', '$2y$10$Xss06fXdiixuH0y4aIXjSO8w54FXsBFN49Fcf4htSW/vPTUUedLi6', 'HOAT_DONG', 'NV002', 'QUAN_LY'),
(4, 'ngocanh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HOAT_DONG', 'NV006', 'THU_NGAN'),
(5, 'minhquan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HOAT_DONG', 'NV004', 'THU_KHO'),
(6, 'huongquaycan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HOAT_DONG', 'NV005', 'NV_QUAY_CAN');

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
  MODIFY `ma_chi_tiet_ban_lo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  MODIFY `ma_chi_tiet_hd` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `chi_tiet_huy_lo`
--
ALTER TABLE `chi_tiet_huy_lo`
  MODIFY `ma_chi_tiet_huy_lo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chi_tiet_phieu_huy`
--
ALTER TABLE `chi_tiet_phieu_huy`
  MODIFY `ma_chi_tiet_huy` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  MODIFY `ma_chi_tiet_nhap` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `chi_tiet_phieu_xuat`
--
ALTER TABLE `chi_tiet_phieu_xuat`
  MODIFY `ma_chi_tiet_xuat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chi_tiet_xuat_lo`
--
ALTER TABLE `chi_tiet_xuat_lo`
  MODIFY `ma_chi_tiet_xuat_lo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tai_khoan`
--
ALTER TABLE `tai_khoan`
  MODIFY `ma_tai_khoan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
