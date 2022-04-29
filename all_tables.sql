SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `announcement`;
DROP TABLE IF EXISTS `service_item`;
DROP TABLE IF EXISTS `service_class`;
DROP TABLE IF EXISTS `discount`;
DROP TABLE IF EXISTS `period_discount`;
DROP TABLE IF EXISTS `furniture_position`;
DROP TABLE IF EXISTS `furniture_list_app`;
DROP TABLE IF EXISTS `room`;
DROP TABLE IF EXISTS `furniture`;
DROP TABLE IF EXISTS `vehicle_demand`;
DROP TABLE IF EXISTS `vehicle_assignment`;
DROP TABLE IF EXISTS `vehicle`;
DROP TABLE IF EXISTS `staff_assignment`;
DROP TABLE IF EXISTS `staff`;
DROP TABLE IF EXISTS `choose`;
DROP TABLE IF EXISTS `special`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `company`;
DROP TABLE IF EXISTS `member`;
DROP TABLE IF EXISTS `area`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `area` (
  `city_name`     varchar(11) NOT NULL, /*縣市名*/
  `city_district` varchar(11) NOT NULL, /*鄉鎮市區名*/
  `Postal_code`   varchar(10) NOT NULL, /*郵遞區號*/
  PRIMARY KEY(`Postal_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `member` (
  `member_id` int(10) NOT NULL AUTO_INCREMENT,
  `member_name` varchar(50) CHARACTER SET utf8,
  `gender` varchar(10) CHARACTER SET utf8,
  `phone` varchar(10) CHARACTER SET utf8,
  `contact_address` varchar(100) CHARACTER SET utf8,
  `contact_way` varchar(100) CHARACTER SET utf8,
  `contact_time` varchar(100) CHARACTER SET utf8,
  PRIMARY KEY(`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `company` (
  `company_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `img` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `address` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `staff_num` int(4) NOT NULL,
  `url` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `line_id` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `philosophy` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `last_distribution` float DEFAULT 0,
  PRIMARY KEY(`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `orders`(
  `order_id`          int(10) NOT NULL AUTO_INCREMENT, /*訂單ID*/
  `member_id`         int(10),  /*客戶ID*/
  `additional`        varchar(300) CHARACTER SET utf8, /*補充，注意事項*/
  `memo`              varchar(300) CHARACTER SET utf8, /*備註*/
  `from_address`      varchar(100) CHARACTER SET utf8, /*搬出地址*/
  `to_address`        varchar(100) CHARACTER SET utf8, /*搬入地址*/
  `from_elevator`     boolean, /*是否有電梯*/
  `to_elevator`       boolean, /*是否有電梯*/
  `storage_space`     varchar(10)  CHARACTER SET utf8 NOT NULL, /*倉儲需求*/
  `carton_num`        int(10)      NOT NULL,     /*紙箱數量*/
  `program`           varchar(4)   CHARACTER SET utf8 NOT NULL, /*方案名稱*/
  `order_status`      enum('evaluating', 'scheduled', 'assigned', 'done', 'cancel', 'paid'), /*訂單狀態*/
  `new`               boolean   DEFAULT TRUE, /*有無按過*/
  `auto`              boolean   DEFAULT TRUE, /*是否為系統新增的或是手動新增的*/
  `last_update`       timestamp NOT NULL DEFAULT current_timestamp()
                                ON UPDATE current_timestamp(),       /*更新時間*/
   `signature` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (order_id),
  FOREIGN KEY (member_id)
  REFERENCES member(member_id) ON DELETE SET NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `special`( /*特殊物品*/
	`special_id`  int(10) NOT NULL AUTO_INCREMENT, /*特殊物品ID*/
	`order_id`  	int(10), /*訂單ID*/
	`name` 				varchar(100) CHARACTER SET utf8, /*物品名稱*/
	`num` 				int(10), /*數量*/
	PRIMARY KEY(special_id),
	FOREIGN KEY(order_id)
	REFERENCES orders(order_id) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `choose`( /*估價單-公司表*/
    `order_id`         int(10) NOT NULL, /*訂單ID*/
    `company_id`       int(10) NOT NULL, /*公司ID*/
    `valuation_date`   date,        /*估價日期*/
    `valuation_time`   varchar(99), /*估價時間*/
    `moving_date`      datetime,    /*搬家時間*/
    `estimate_fee`     int(10),     /*估價價格*/
    `accurate_fee`     int(10),     /*搬家費用*/
    `estimate_worktime` int(3),   /*預計工時*/
    `confirm`          boolean DEFAULT FALSE,     /*確認*/
    `valuation_status` enum('self', 'booking', 'match', 'cancel', 'chosen') DEFAULT 'self', /*估價單的狀態, chosen狀態代表已變成訂單*/
    PRIMARY KEY(order_id, company_id),
    FOREIGN KEY (order_id)
    REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (company_id)
    REFERENCES company(company_id) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `staff`(
  `staff_id`   int(10) NOT NULL AUTO_INCREMENT,
  `staff_name` varchar(50) CHARACTER SET utf8,
  `company_id` int(10) NOT NULL, /*所屬公司*/
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (staff_id),
  FOREIGN KEY (company_id)
  REFERENCES company(company_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `staff_assignment`(
  `order_id` int(10),
  `staff_id` int(10),
  `pay` int(10) DEFAULT -1,  /*酬勞分配*/
  PRIMARY KEY (order_id, staff_id),
  FOREIGN KEY (order_id)
  REFERENCES orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (staff_id)
  REFERENCES staff(staff_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vehicle`(
  `vehicle_id` int(10) NOT NULL AUTO_INCREMENT,
  `plate_num` varchar(99) NOT NULL, /*車牌號碼*/
  `vehicle_weight` varchar(10), /*車輛噸位*/
  `vehicle_type` varchar(99)  CHARACTER SET utf8, /*車輛種類*/
  `company_id` int(10) NOT NULL, /*所屬公司*/
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` datetime DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  PRIMARY KEY (vehicle_id),
  FOREIGN KEY (company_id)
  REFERENCES company(company_id) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vehicle_assignment`(
  `order_id` int(10),   /*訂單*/
  `vehicle_id` int(10), /*車子*/
  PRIMARY KEY (order_id, vehicle_id),
  FOREIGN KEY (order_id)
  REFERENCES orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (vehicle_id)
  REFERENCES vehicle(vehicle_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vehicle_demand`(
  `order_id` int(10), /*訂單&估價單*/
  `num` int(10),      /*數量*/
  `vehicle_weight` varchar(10), /*車輛噸位*/
  `vehicle_type`   varchar(99)  CHARACTER SET utf8, /*車輛種類*/
  PRIMARY KEY (order_id, vehicle_weight, vehicle_type),
  FOREIGN KEY (order_id)
  REFERENCES orders(order_id) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `furniture` (
  `furniture_id` int(10) NOT NULL AUTO_INCREMENT,
  `space_type` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `furniture_name` varchar(99) CHARACTER SET utf8 DEFAULT NULL,
  `img` varchar(99) DEFAULT NULL,
  PRIMARY KEY (furniture_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `room`( /*房間*/
	`room_id`    int(10) NOT NULL AUTO_INCREMENT, /*房間ID*/
	`order_id`   int(10), /*訂單ID*/
	`floor`  	   int(10), /*樓層*/
	`room_name`  int(10), /*房間名稱*/
	`room_type`  enum('房間', '廳', '戶外'), /*房間類別*/
	PRIMARY KEY(room_id),
	FOREIGN KEY(order_id)
	REFERENCES orders(order_id) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `furniture_list_app` ( /*家具清單*/
	`order_id`     		int(10),	/*訂單ID*/
	`company_id`      int(20),	/*公司ID*/
	`furniture_id`		int(10), 	/*家具ID*/
	`num`          		int(10),	/*家具數量*/
	PRIMARY KEY (order_id,company_id,furniture_id),
	FOREIGN KEY (order_id)
	REFERENCES orders(order_id) ON DELETE CASCADE,
	FOREIGN KEY (company_id)
	REFERENCES company(company_id),
	FOREIGN KEY (furniture_id)
	REFERENCES furniture(furniture_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `furniture_position`(
  `room_id`      int(10), /*房間ID*/
  `furniture_id` int(10), /*家具ID*/
  `num`          int(5),  /*該家具在同個房間的數量*/
  PRIMARY KEY( room_id, furniture_id),
  FOREIGN KEY(room_id) REFERENCES room(room_id),
  FOREIGN KEY(furniture_id)
  REFERENCES furniture(furniture_id) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `discount` (
  `company_id` int(10) NOT NULL,
  `valuate` boolean DEFAULT FALSE,
  `deposit` boolean DEFAULT FALSE,
  `cancel` boolean DEFAULT FALSE,
  `update_time` timestamp NOT NULL DEFAULT current_timestamp()
              ON UPDATE current_timestamp(),
  PRIMARY KEY (company_id, update_time),
  FOREIGN KEY (company_id)
  REFERENCES company(company_id) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `period_discount` (
  `discount_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL,
  `discount_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `discount` float(10) NOT NULL CHECK (discount between 0 and 100),
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `turn_on` boolean DEFAULT FALSE,
  `update_time` timestamp NOT NULL DEFAULT current_timestamp()
              ON UPDATE current_timestamp(),
  PRIMARY KEY (discount_id),
  FOREIGN KEY (company_id)
    REFERENCES company(company_id) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `service_class` (
  `service_id` int(10) NOT NULL,
  `service_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (service_id),
  UNIQUE (service_name)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `service_item` (
  `company_id` int(10) NOT NULL,
  `item_name` varchar(20) CHARACTER SET utf8 NOT NULL, /*項目名稱*/
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` datetime  DEFAULT NULL,
  `service_id` int(10) NOT NULL, /*服務項目*/
  `isDelete` boolean DEFAULT FALSE,
  PRIMARY KEY (company_id, item_name, start_time),
  FOREIGN KEY (company_id)
  REFERENCES company(company_id) ON DELETE CASCADE,
  FOREIGN KEY (service_id)
  REFERENCES service_class(service_id) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `announcement` (
  `announcement_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL,
  `title` varchar(20) CHARACTER SET utf8 NOT NULL,
  `content` varchar(300)  CHARACTER SET utf8,
  `date` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (announcement_id),
  FOREIGN KEY (company_id)
  REFERENCES company(company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
