CREATE TABLE `announcement` (
  `announcement_id` int(10) NOT NULL,
  `title` varchar(20) CHARACTER SET utf8 NOT NULL,
  `outline` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `content` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `announcement_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`announcement_id`);
ALTER TABLE `announcement`
  MODIFY `announcement_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

CREATE TABLE `area` (
  `city_name` varchar(11) NOT NULL,
  `city_district` varchar(11) NOT NULL,
  `Postal_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `area`
  ADD PRIMARY KEY (`Postal_code`);

CREATE TABLE `company` (
  `company_id` int(10) NOT NULL,
  `company_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `img` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `address` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `staff_num` int(4) NOT NULL,
  `url` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `line_id` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `philosophy` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `service_area` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `service_item` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `vehicle_type` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `carton_type` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `packaging_material` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `free_evaluate` tinyint(1) NOT NULL,
  `free_deposit` tinyint(1) NOT NULL,
  `free_cancel` tinyint(1) NOT NULL,
  `short_discount` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `long_discount` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `small_amount` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `comment_score` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `comment_num` int(9) NOT NULL,
  `order_num` int(3) NOT NULL,
  `certification` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `account` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(20) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`);
ALTER TABLE `company`
  MODIFY `company_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

CREATE TABLE `announcement_company` (
  `announcement_id` int(10) NOT NULL,
  `company_id` int(10) NOT NULL,
  `new` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `announcement_company`
  ADD PRIMARY KEY (`announcement_id`,`company_id`),
  ADD KEY `company_id` (`company_id`);
ALTER TABLE `announcement_company`
  ADD CONSTRAINT `announcement_company_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcement` (`announcement_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcement_company_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

CREATE TABLE `discount` (
  `company_id` int(10) NOT NULL,
  `valuate` tinyint(1) DEFAULT 0,
  `deposit` tinyint(1) DEFAULT 0,
  `cancel` tinyint(1) DEFAULT 0,
  `update_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `discount`
  ADD PRIMARY KEY (`company_id`,`update_time`);
ALTER TABLE `discount`
  ADD CONSTRAINT `discount_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

CREATE TABLE `furniture` (
  `furniture_id` int(10) NOT NULL,
  `space_type` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `furniture_name` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `img` varchar(99) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `furniture`
  ADD PRIMARY KEY (`furniture_id`);
ALTER TABLE `furniture`
  MODIFY `furniture_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

CREATE TABLE `furniture_list_web` (
  `id` int(11) NOT NULL,
  `order_id` int(10) DEFAULT NULL,
  `furniture_num` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `furniture_list_web`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `furniture_list_web`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

CREATE TABLE `member` (
  `member_id` int(10) NOT NULL,
  `member_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `contact_way` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `contact_time` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `care_thing` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `move_exp` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `move_num` int(11) DEFAULT NULL,
  `interest` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `occupation` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `old_children` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `gender` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `contact_address` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `family_num` int(11) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `family_size` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `id_card` varchar(10) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `member`
  ADD PRIMARY KEY (`member_id`);
ALTER TABLE `member`
  MODIFY `member_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

CREATE TABLE `orders` (
  `order_id` int(10) NOT NULL,
  `member_id` int(10) DEFAULT NULL,
  `additional` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `memo` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `from_address` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `to_address` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `from_elevator` tinyint(1) DEFAULT NULL,
  `to_elevator` tinyint(1) DEFAULT NULL,
  `storage_space` varchar(10) CHARACTER SET utf8 NOT NULL,
  `carton_num` int(10) NOT NULL,
  `program` varchar(4) CHARACTER SET utf8 NOT NULL,
  `self_valuation_progress` int(2) DEFAULT NULL,
  `order_status` enum('evaluating','scheduled','assigned','done','cancel','paid') DEFAULT NULL,
  `auto` tinyint(1) DEFAULT 1,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `signature` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `member_id` (`member_id`);
ALTER TABLE `orders`
  MODIFY `order_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE SET NULL;

CREATE TABLE `choose` (
  `order_id` int(10) NOT NULL,
  `company_id` int(10) NOT NULL,
  `valuation_date` date DEFAULT NULL,
  `valuation_time` varchar(99) DEFAULT NULL,
  `moving_date` datetime DEFAULT NULL,
  `estimate_fee` int(10) DEFAULT NULL,
  `accurate_fee` int(10) DEFAULT NULL,
  `estimate_worktime` int(3) DEFAULT NULL,
  `confirm` tinyint(1) DEFAULT 0,
  `new` tinyint(1) DEFAULT 1,
  `valuation_status` enum('self','booking','match','cancel','chosen') DEFAULT 'self'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `choose`
  ADD PRIMARY KEY (`order_id`,`company_id`),
  ADD KEY `company_id` (`company_id`);
ALTER TABLE `choose`
  ADD CONSTRAINT `choose_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `choose_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

CREATE TABLE `comments` (
  `comment_id` int(10) NOT NULL,
  `order_id` int(10) NOT NULL,
  `member_id` int(10) NOT NULL,
  `company_id` int(10) NOT NULL,
  `comment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `service_quality` float NOT NULL CHECK (`service_quality` between 0 and 5),
  `work_attitude` float NOT NULL CHECK (`work_attitude` between 0 and 5),
  `price_grade` float NOT NULL CHECK (`price_grade` between 0 and 5),
  `comment` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `reply` varchar(300) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `company_id` (`company_id`);
ALTER TABLE `comments`
  MODIFY `comment_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

CREATE TABLE `furniture_list_app` (
  `order_id` int(10) NOT NULL,
  `company_id` int(20) NOT NULL,
  `furniture_id` int(10) NOT NULL,
  `num` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `furniture_list_app`
  ADD PRIMARY KEY (`order_id`,`company_id`,`furniture_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `furniture_id` (`furniture_id`);
ALTER TABLE `furniture_list_app`
  ADD CONSTRAINT `furniture_list_app_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `furniture_list_app_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `furniture_list_app_ibfk_3` FOREIGN KEY (`furniture_id`) REFERENCES `furniture` (`furniture_id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE `period_discount` (
  `discount_id` int(10) NOT NULL,
  `company_id` int(10) NOT NULL,
  `discount_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `discount` float NOT NULL CHECK (`discount` between 0 and 100),
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `enable` tinyint(1) DEFAULT 0,
  `isDelete` tinyint(1) DEFAULT 0,
  `enable_time` datetime DEFAULT NULL,
  `disable_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `period_discount`
  ADD PRIMARY KEY (`discount_id`),
  ADD KEY `company_id` (`company_id`);
ALTER TABLE `period_discount`
  MODIFY `discount_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `period_discount`
  ADD CONSTRAINT `period_discount_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

CREATE TABLE `room` (
  `room_id` int(10) NOT NULL,
  `order_id` int(10) DEFAULT NULL,
  `floor` int(10) DEFAULT NULL,
  `room_name` int(10) DEFAULT NULL,
  `room_type` enum('房間','廳','戶外') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `order_id` (`order_id`);
ALTER TABLE `room`
  MODIFY `room_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

CREATE TABLE `furniture_position` (
  `room_id` int(10) NOT NULL,
  `furniture_id` int(10) NOT NULL,
  `num` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `furniture_position`
  ADD PRIMARY KEY (`room_id`,`furniture_id`),
  ADD KEY `furniture_id` (`furniture_id`);
ALTER TABLE `furniture_position`
  ADD CONSTRAINT `furniture_position_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`),
  ADD CONSTRAINT `furniture_position_ibfk_2` FOREIGN KEY (`furniture_id`) REFERENCES `furniture` (`furniture_id`) ON DELETE CASCADE;

CREATE TABLE `service_class` (
  `service_id` int(10) NOT NULL,
  `service_name` varchar(20) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `service_class`
  ADD PRIMARY KEY (`service_id`),
  ADD UNIQUE KEY `service_name` (`service_name`);

CREATE TABLE `service_item` (
  `company_id` int(10) NOT NULL,
  `item_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` datetime DEFAULT NULL,
  `service_id` int(10) NOT NULL,
  `isDelete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `service_item`
  ADD PRIMARY KEY (`company_id`,`item_name`,`start_time`),
  ADD KEY `service_id` (`service_id`);
ALTER TABLE `service_item`
  ADD CONSTRAINT `service_item_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_item_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service_class` (`service_id`) ON DELETE CASCADE;

CREATE TABLE `special` (
  `special_id` int(10) NOT NULL,
  `order_id` int(10) DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `num` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `special`
  ADD PRIMARY KEY (`special_id`),
  ADD KEY `order_id` (`order_id`);
ALTER TABLE `special`
  MODIFY `special_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;
ALTER TABLE `special`
  ADD CONSTRAINT `special_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

CREATE TABLE `staff` (
  `staff_id` int(10) NOT NULL,
  `staff_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `company_id` int(10) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `company_id` (`company_id`);
ALTER TABLE `staff`
  MODIFY `staff_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

CREATE TABLE `staff_assignment` (
  `order_id` int(10) NOT NULL,
  `staff_id` int(10) NOT NULL,
  `pay` int(10) DEFAULT -1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `staff_assignment`
  ADD PRIMARY KEY (`order_id`,`staff_id`),
  ADD KEY `staff_id` (`staff_id`);
ALTER TABLE `staff_assignment`
  ADD CONSTRAINT `staff_assignment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_assignment_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE;

CREATE TABLE `staff_leave` (
  `staff_id` int(10) NOT NULL,
  `leave_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `staff_leave`
  ADD PRIMARY KEY (`staff_id`,`leave_date`);
ALTER TABLE `staff_leave`
  ADD CONSTRAINT `staff_leave_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE;

CREATE TABLE `vehicle` (
  `vehicle_id` int(10) NOT NULL,
  `plate_num` varchar(99) NOT NULL,
  `vehicle_weight` varchar(10) DEFAULT NULL,
  `vehicle_type` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `company_id` int(10) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `company_id` (`company_id`);
ALTER TABLE `vehicle`
  MODIFY `vehicle_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `vehicle`
  ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

CREATE TABLE `vehicle_assignment` (
  `order_id` int(10) NOT NULL,
  `vehicle_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `vehicle_assignment`
  ADD PRIMARY KEY (`order_id`,`vehicle_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);
ALTER TABLE `vehicle_assignment`
  ADD CONSTRAINT `vehicle_assignment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_assignment_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`);

CREATE TABLE `vehicle_demand` (
  `order_id` int(10) NOT NULL,
  `num` int(10) DEFAULT NULL,
  `vehicle_weight` varchar(10) NOT NULL,
  `vehicle_type` varchar(99) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `vehicle_demand`
  ADD PRIMARY KEY (`order_id`,`vehicle_weight`,`vehicle_type`);
ALTER TABLE `vehicle_demand`
  ADD CONSTRAINT `vehicle_demand_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

CREATE TABLE `vehicle_maintain` (
  `vehicle_id` int(10) NOT NULL,
  `maintain_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `vehicle_maintain`
  ADD PRIMARY KEY (`vehicle_id`,`maintain_date`);
ALTER TABLE `vehicle_maintain`
  ADD CONSTRAINT `vehicle_maintain_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`) ON DELETE CASCADE;




COMMIT;
