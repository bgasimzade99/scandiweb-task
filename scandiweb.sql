-- MySQL database for Scandiweb Full Stack Test
-- Schema based on Scandiweb provided structure

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `attribute_values`;
DROP TABLE IF EXISTS `attributes`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `prices`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `products` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `in_stock` tinyint(1) NOT NULL DEFAULT 1,
  `description` text NOT NULL,
  `category_id` bigint NOT NULL,
  `brand` varchar(255) NOT NULL,
  `gallery` json NOT NULL,
  PRIMARY KEY (`id`),
  KEY `products_categories_id_fk` (`category_id`),
  CONSTRAINT `products_categories_id_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `attributes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `product_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `attributes_products_id_fk` (`product_id`),
  CONSTRAINT `attributes_products_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `attribute_values` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  `display_value` varchar(255) NOT NULL,
  `attribute_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_values_attributes_id_fk` (`attribute_id`),
  CONSTRAINT `attribute_values_attributes_id_fk` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `orders` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `order_details` json NOT NULL,
  `order_status` varchar(255) NOT NULL,
  `total` float NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `prices` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `amount` float NOT NULL,
  `currency` json NOT NULL,
  `product_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `prices_products_id_fk` (`product_id`),
  CONSTRAINT `prices_products_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `categories` VALUES (1,'all'),(2,'clothes'),(3,'tech');

INSERT INTO `products` VALUES
('apple-airpods-pro','AirPods Pro',0,'\n Magic like you\'ve never heard \n AirPods Pro have been designed to deliver Active Noise Cancellation for immersive sound, Transparency mode so you can hear your surroundings, and a customizable fit for all-day comfort.',3,'Apple','[\"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MWP22?wid=572&hei=572&fmt=jpeg&qlt=95&.v=1591634795000\"]'),
('apple-airtag','AirTag',1,'\n Lose your knack for losing things. \n AirTag is an easy way to keep track of your stuff. Attach one to your keys, slip another one in your backpack.',3,'Apple','[\"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/airtag-double-select-202104?wid=445&hei=370&fmt=jpeg&qlt=95&.v=1617761672000\"]'),
('apple-imac-2021','iMac 2021',1,'The new iMac!',3,'Apple','[\"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/imac-24-blue-selection-hero-202104?wid=904&hei=840&fmt=jpeg&qlt=80&.v=1617492405000\"]'),
('apple-iphone-12-pro','iPhone 12 Pro',1,'This is iPhone 12. Nothing else to say.',1,'Apple','[\"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-family-hero?wid=940&hei=1112&fmt=jpeg&qlt=80&.v=1604021663000\"]'),
('huarache-x-stussy-le','Nike Air Huarache Le',1,'Great sneakers for everyday use!',2,'Nike x Stussy','[\"https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_2_720x.jpg?v=1612816087\",\"https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_1_720x.jpg?v=1612816087\",\"https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_3_720x.jpg?v=1612816087\",\"https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_5_720x.jpg?v=1612816087\",\"https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_4_720x.jpg?v=1612816087\"]'),
('jacket-canada-goosee','Jacket',1,'Awesome winter jacket',2,'Canada Goose','[\"https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016105/product-image/2409L_61.jpg\",\"https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016107/product-image/2409L_61_a.jpg\",\"https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016108/product-image/2409L_61_b.jpg\",\"https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016109/product-image/2409L_61_c.jpg\",\"https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016110/product-image/2409L_61_d.jpg\",\"https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058169/product-image/2409L_61_o.png\",\"https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058159/product-image/2409L_61_p.png\"]'),
('ps-5','PlayStation 5',0,'A good gaming console. Plays games of PS4!',3,'Sony','[\"https://images-na.ssl-images-amazon.com/images/I/510VSJ9mWDL._SL1262_.jpg\",\"https://images-na.ssl-images-amazon.com/images/I/610%2B69ZsKCL._SL1500_.jpg\",\"https://images-na.ssl-images-amazon.com/images/I/51iPoFwQT3L._SL1230_.jpg\",\"https://images-na.ssl-images-amazon.com/images/I/61qbqFcvoNL._SL1500_.jpg\",\"https://images-na.ssl-images-amazon.com/images/I/51HCjA3rqYL._SL1230_.jpg\"]'),
('xbox-series-s','Xbox Series S 512GB',0,'Hardware-beschleunigtes Raytracing.',3,'Microsoft','[\"https://images-na.ssl-images-amazon.com/images/I/71vPCX0bS-L._SL1500_.jpg\",\"https://images-na.ssl-images-amazon.com/images/I/71q7JTbRTpL._SL1500_.jpg\",\"https://images-na.ssl-images-amazon.com/images/I/71iQ4HGHtsL._SL1500_.jpg\",\"https://images-na.ssl-images-amazon.com/images/I/61IYrCrBzxL._SL1500_.jpg\",\"https://images-na.ssl-images-amazon.com/images/I/61RnXmpAmIL._SL1500_.jpg\"]');

INSERT INTO `prices` VALUES
(1,145,'{\"label\": \"USD\", \"symbol\": \"$\"}','huarache-x-stussy-le'),
(2,518,'{\"label\": \"USD\", \"symbol\": \"$\"}','jacket-canada-goosee'),
(3,844,'{\"label\": \"USD\", \"symbol\": \"$\"}','ps-5'),
(4,334,'{\"label\": \"USD\", \"symbol\": \"$\"}','xbox-series-s'),
(5,1688,'{\"label\": \"USD\", \"symbol\": \"$\"}','apple-imac-2021'),
(6,1001,'{\"label\": \"USD\", \"symbol\": \"$\"}','apple-iphone-12-pro'),
(7,300.23,'{\"label\": \"USD\", \"symbol\": \"$\"}','apple-airpods-pro'),
(8,121,'{\"label\": \"USD\", \"symbol\": \"$\"}','apple-airtag');

INSERT INTO `attributes` VALUES
(1,'Size','text','huarache-x-stussy-le'),
(2,'Size','text','jacket-canada-goosee'),
(3,'Color','swatch','ps-5'),
(4,'Capacity','text','ps-5'),
(5,'Color','swatch','xbox-series-s'),
(6,'Capacity','text','xbox-series-s'),
(7,'Capacity','text','apple-imac-2021'),
(8,'With USB 3 ports','text','apple-imac-2021'),
(9,'Touch ID in keyboard','text','apple-imac-2021'),
(10,'Capacity','text','apple-iphone-12-pro'),
(11,'Color','swatch','apple-iphone-12-pro');

INSERT INTO `attribute_values` VALUES
(9,'40','40',1),(10,'41','41',1),(11,'42','42',1),(12,'43','43',1),
(13,'S','Small',2),(14,'M','Medium',2),(15,'L','Large',2),(16,'XL','Extra Large',2),
(17,'#44FF03','Green',3),(18,'#03FFF7','Cyan',3),(19,'#030BFF','Blue',3),(20,'#000000','Black',3),(21,'#FFFFFF','White',3),
(22,'512G','512G',4),(23,'1T','1T',4),
(24,'#44FF03','Green',5),(25,'#03FFF7','Cyan',5),(26,'#030BFF','Blue',5),(27,'#000000','Black',5),(28,'#FFFFFF','White',5),
(29,'512G','512G',6),(30,'1T','1T',6),
(31,'256GB','256GB',7),(32,'512G','512G',7),(33,'Yes','Yes',8),(34,'No','No',8),(35,'Yes','Yes',9),(36,'No','No',9),
(37,'512G','512G',10),(38,'1T','1T',10),
(39,'#44FF03','Green',11),(40,'#03FFF7','Cyan',11),(41,'#030BFF','Blue',11),(42,'#000000','Black',11),(43,'#FFFFFF','White',11);
