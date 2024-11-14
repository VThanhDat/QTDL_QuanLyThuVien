-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 14, 2024 at 07:30 PM
-- Server version: 8.0.39
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quanlythuvien`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertBook` (IN `bookname` VARCHAR(255), IN `category` INT, IN `author` INT, IN `isbn` VARCHAR(255), IN `price` DECIMAL(10,2), IN `image` VARCHAR(255), IN `quantity` INT, IN `stock` INT, IN `method` TINYINT)   BEGIN
    INSERT INTO sach(BookName, CatId, AuthorId, ISBNNumber, BookPrice,Image,Quantity, Stock, Method)
    VALUES(bookname, category, author, isbn, price, image, quantity, stock, method);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LaySachDaMuonChuaTra` (IN `status` TINYINT(4))   BEGIN
    SELECT id
    FROM ctmuontra
    WHERE BorrowStatus = status;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LaySachDocGiaDaMuon` (IN `sid` VARCHAR(100))   BEGIN SELECT ctmuontra.id, sach.BookName AS ten_sach, sach.ISBNNumber AS ma_isbn, ctmuontra.IssuesDate AS ngay_muon, ctmuontra.ReturnDate AS ngay_tra 
FROM ctmuontra JOIN sach ON ctmuontra.BookId = sach.id WHERE ctmuontra.ReaderId = sid; END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `getReservedBooks` (`sid` INT) RETURNS INT DETERMINISTIC READS SQL DATA BEGIN
    DECLARE reservedBooksCount INT;

    -- Đếm số lượng sách đang chờ
    SELECT COUNT(*) INTO reservedBooksCount
    FROM ctmuontra
    WHERE ReaderId = sid AND BorrowStatus = 0;

    -- Trả về kết quả
    RETURN reservedBooksCount;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `adminName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `adminEmail` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `updationDate` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `adminName`, `adminEmail`, `UserName`, `Password`, `updationDate`) VALUES
(1, NULL, NULL, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '2024-10-08 12:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `ctmuontra`
--

CREATE TABLE `ctmuontra` (
  `id` int NOT NULL,
  `BookId` int NOT NULL,
  `ReaderId` int NOT NULL,
  `IssuesDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ReturnDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `QuantityBorrow` int NOT NULL,
  `Method` tinyint NOT NULL,
  `BorrowStatus` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ctmuontra`
--

INSERT INTO `ctmuontra` (`id`, `BookId`, `ReaderId`, `IssuesDate`, `ReturnDate`, `QuantityBorrow`, `Method`, `BorrowStatus`) VALUES
(69, 27, 1, '2024-11-08 06:01:30', '2024-11-14 18:48:50', 1, 1, 2),
(70, 26, 1, '2024-11-08 06:11:18', '2024-11-08 13:11:41', 1, 1, NULL),
(71, 36, 2, '2024-11-08 13:13:02', '2024-11-08 13:13:09', 1, 0, 2),
(72, 36, 6, '2024-11-14 18:31:09', '2024-11-14 18:38:05', 1, 0, 2),
(73, 36, 6, '2024-11-14 18:42:02', '2024-11-14 18:42:44', 1, 0, 2),
(74, 28, 6, '2024-11-14 18:42:33', '2024-11-14 18:43:37', 1, 0, 2),
(75, 36, 6, '2024-11-14 18:49:33', '2024-11-14 18:51:21', 1, 0, 2),
(76, 36, 1, '2024-11-14 18:50:00', '2024-11-14 18:50:24', 1, 0, 2),
(77, 36, 2, '2024-11-14 18:51:08', '2024-11-14 19:05:49', 1, 0, 2),
(78, 36, 1, '2024-11-14 11:53:15', '2024-11-14 19:05:52', 1, 1, 2),
(80, 36, 1, '2024-11-14 12:06:51', '2024-11-16 17:00:00', 1, 1, 1),
(81, 36, 4, '2024-11-14 12:07:09', '2024-11-14 19:08:52', 1, 1, 2),
(82, 36, 6, '2024-11-14 12:07:40', '2024-11-16 17:00:00', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `docgia`
--

CREATE TABLE `docgia` (
  `id` int NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `EmailId` varchar(100) DEFAULT NULL,
  `MobileNumber` char(11) DEFAULT NULL,
  `Password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Status` tinyint DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `docgia`
--

INSERT INTO `docgia` (`id`, `FullName`, `EmailId`, `MobileNumber`, `Password`, `Status`, `RegDate`, `UpdationDate`) VALUES
(1, 'dat', 'dat@gmail.com', '0123334450', 'e10adc3949ba59abbe56e057f20f883e', 1, '2024-10-10 13:50:29', '2024-11-14 19:26:55'),
(2, 'thinh', 'thinh@gmail.com', '0123334459', 'e10adc3949ba59abbe56e057f20f883e', 1, '2024-10-15 11:18:55', '2024-11-14 19:26:51'),
(3, 'qui', 'qui@gmail.com', '0123334458', 'e10adc3949ba59abbe56e057f20f883e', 1, '2024-10-15 15:50:17', '2024-11-14 19:26:44'),
(4, 'khai', 'khai@gmail.com', '0123334457', 'e10adc3949ba59abbe56e057f20f883e', 1, '2024-11-14 17:27:47', '2024-11-14 19:26:40'),
(6, 'cuong', 'cuong@gmail.com', '0123334456', 'e10adc3949ba59abbe56e057f20f883e', 1, '2024-11-14 17:28:52', '2024-11-14 19:26:33');

-- --------------------------------------------------------

--
-- Table structure for table `sach`
--

CREATE TABLE `sach` (
  `id` int NOT NULL,
  `BookName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `CatId` int DEFAULT NULL,
  `AuthorId` int DEFAULT NULL,
  `ISBNNumber` int DEFAULT NULL,
  `BookPrice` decimal(10,2) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Quantity` int NOT NULL,
  `Stock` int NOT NULL,
  `Method` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sach`
--

INSERT INTO `sach` (`id`, `BookName`, `CatId`, `AuthorId`, `ISBNNumber`, `BookPrice`, `RegDate`, `updationDate`, `Image`, `Quantity`, `Stock`, `Method`) VALUES
(26, 'Sức Mạnh Của EQ - Đánh Thức Trí Tuệ Cảm Xúc - Làm Chủ Ngôn Ngữ - Thu Phục Lòng Người', 1, 1, 12323, 20000.00, '2024-10-30 11:21:03', '2024-11-13 11:54:07', 'bia_1_suc-manh-cua-eq.webp', 20, 20, 1),
(27, 'Trở Về Không - Trải Nghiệm Ho\'oponopono', 2, 2, 1123, 45120.00, '2024-10-30 11:22:51', '2024-11-14 18:48:50', 'sach-tro-ve-khong-trai-nghiem-hooponopono.webp', 20, 20, 1),
(28, '30 Tuổi - Mọi Thứ Chỉ Mới Bắt Đầu', 1, 2, 1000, 87750.00, '2024-10-30 11:23:14', '2024-11-08 11:37:31', '30Tuổi-MọiThứChỉMớiBắtĐầu.webp', 20, 20, 0),
(29, 'Bắt Đầu Cuộc Hành Trình Nội Tại Bản Lĩnh', 2, 2, 212, 30420.00, '2024-10-30 11:24:04', '2024-11-08 08:43:41', 'NoiTaiBanLinh.webp', 20, 20, 1),
(31, 'Chia Sẻ Từ Trái Tim (Thích Pháp Hòa)', 2, 2, 1123, 122640.00, '2024-10-30 11:22:51', '2024-10-30 12:49:26', 'chiasetutraitim-bia.webp', 20, 20, 0),
(32, 'Hiểu Về Trái Tim (Tái Bản 2023)', 2, 2, 1000, 135880.00, '2024-10-30 11:23:14', '2024-11-14 18:23:51', 'hiuvetraitim.webp', 30, 30, 0),
(33, 'Vượt Qua Bản Ngã - Ego Is The Enemy', 2, 2, 212, 114730.00, '2024-10-30 11:24:04', '2024-10-30 12:54:07', 'ego.webp', 30, 30, 0),
(34, 'Ảo Tưởng Tích Cực - Useful Delusions', 2, 1, 12345, 61620.00, '2024-10-30 11:47:11', '2024-11-08 12:11:42', 'AoTuongTichCuc.webp', 50, 50, 0),
(35, 'Giải Mã Hoóc-Môn Dopamin', 1, 1, 12345, 128700.00, '2024-10-30 12:55:02', '2024-11-08 08:34:38', 'Giaima.webp', 10, 10, 0),
(36, 'Tư Duy Ngược', 1, 1, 111, 69500.00, '2024-11-08 12:42:19', '2024-11-14 19:08:56', '1731069739.webp', 2, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tacgia`
--

CREATE TABLE `tacgia` (
  `id` int NOT NULL,
  `AuthorName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tacgia`
--

INSERT INTO `tacgia` (`id`, `AuthorName`, `CreationDate`, `UpdationDate`) VALUES
(1, 'Nam Cao', '2024-10-08 13:07:13', '2024-10-08 13:07:13'),
(2, 'Nguyễn Nhật Ánh', '2024-10-08 13:11:55', '2024-10-09 14:47:11');

--
-- Triggers `tacgia`
--
DELIMITER $$
CREATE TRIGGER `before_edit_tacgia` BEFORE UPDATE ON `tacgia` FOR EACH ROW BEGIN
    DECLARE author_count INT;

    SELECT COUNT(*) INTO author_count
    FROM tacgia
    WHERE AuthorName = NEW.AuthorName;

    IF author_count > 0 THEN
        SIGNAL SQLSTATE '23000'
        SET MESSAGE_TEXT = 'Tên tác giả đã tồn tại';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_tacgia` BEFORE INSERT ON `tacgia` FOR EACH ROW BEGIN
    DECLARE author_count INT;

    SELECT COUNT(*) INTO author_count
    FROM tacgia
    WHERE AuthorName = NEW.AuthorName;

    IF author_count > 0 THEN
        SIGNAL SQLSTATE '23000'
        SET MESSAGE_TEXT = 'Tên tác giả đã tồn tại';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `theloai`
--

CREATE TABLE `theloai` (
  `id` int NOT NULL,
  `CategoryName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Status` tinyint DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `theloai`
--

INSERT INTO `theloai` (`id`, `CategoryName`, `Status`, `CreationDate`, `UpdationDate`) VALUES
(1, 'Truyện ngắn', 1, '2024-10-08 12:20:16', '2024-10-08 12:55:32'),
(2, 'Truyện dài', 1, '2024-10-08 12:24:31', '2024-10-08 12:24:31');

--
-- Triggers `theloai`
--
DELIMITER $$
CREATE TRIGGER `before_edit_theloai` BEFORE UPDATE ON `theloai` FOR EACH ROW BEGIN
    DECLARE category_count INT;

    -- Đếm số lượng thể loại có tên giống tên mới
    SELECT COUNT(*) INTO category_count
    FROM theloai
    WHERE CategoryName = NEW.CategoryName;

    -- Nếu đã tồn tại tên thể loại, báo lỗi
    IF category_count > 0 THEN
        SIGNAL SQLSTATE '23000'
        SET MESSAGE_TEXT = 'Tên thể loại đã tồn tại';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_theloai` BEFORE INSERT ON `theloai` FOR EACH ROW BEGIN
    DECLARE category_count INT;

    -- Đếm số lượng thể loại có tên giống tên mới
    SELECT COUNT(*) INTO category_count
    FROM theloai
    WHERE CategoryName = NEW.CategoryName;

    -- Nếu đã tồn tại tên thể loại, báo lỗi
    IF category_count > 0 THEN
        SIGNAL SQLSTATE '23000'
        SET MESSAGE_TEXT = 'Tên thể loại đã tồn tại';
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ctmuontra`
--
ALTER TABLE `ctmuontra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Sach_KhoaNgoai` (`BookId`),
  ADD KEY `DocGia_KhoaNgoai` (`ReaderId`);

--
-- Indexes for table `docgia`
--
ALTER TABLE `docgia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Cat_KhoaNgoai` (`CatId`),
  ADD KEY `TacGia_KhoaNgoai` (`AuthorId`);

--
-- Indexes for table `tacgia`
--
ALTER TABLE `tacgia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theloai`
--
ALTER TABLE `theloai`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ctmuontra`
--
ALTER TABLE `ctmuontra`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `docgia`
--
ALTER TABLE `docgia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sach`
--
ALTER TABLE `sach`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tacgia`
--
ALTER TABLE `tacgia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `theloai`
--
ALTER TABLE `theloai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ctmuontra`
--
ALTER TABLE `ctmuontra`
  ADD CONSTRAINT `DocGia_KhoaNgoai` FOREIGN KEY (`ReaderId`) REFERENCES `docgia` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `Sach_KhoaNgoai` FOREIGN KEY (`BookId`) REFERENCES `sach` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `Cat_KhoaNgoai` FOREIGN KEY (`CatId`) REFERENCES `theloai` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `TacGia_KhoaNgoai` FOREIGN KEY (`AuthorId`) REFERENCES `tacgia` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
