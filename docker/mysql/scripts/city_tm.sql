CREATE TABLE `city_tm` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `cityId` char(36) CHARACTER SET ascii NOT NULL,
  `dst` int(1) NOT NULL DEFAULT 0,
  `zone1` timestamp NULL,
  `zone2` timestamp NULL,
  `zone3` timestamp NULL,
  `gmtOffset1` int(6) DEFAULT 0,
  `gmtOffset2` int(6) DEFAULT 0,
  `tzone` varchar(100) NOT NULL,
  FOREIGN KEY (`cityId`)  REFERENCES `city`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;