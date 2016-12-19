/*
Multi User Store Finder
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `account_activations`
-- ----------------------------
DROP TABLE IF EXISTS `account_activations`;
CREATE TABLE `account_activations` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `account_activations_email_index` (`email`),
  KEY `account_activations_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of account_activations
-- ----------------------------

-- ----------------------------
-- Table structure for `categories`
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `map_style_id` int(10) unsigned NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marker` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `undeletable` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_user_id_foreign` (`user_id`),
  CONSTRAINT `categories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of categories
-- ----------------------------

-- ----------------------------
-- Table structure for `items`
-- ----------------------------
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `map_style_id` int(10) unsigned NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marker` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `undeletable` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_user_id_foreign` (`user_id`),
  KEY `items_category_id_foreign` (`category_id`),
  KEY `items_map_style_id_foreign` (`map_style_id`),
  CONSTRAINT `items_map_style_id_foreign` FOREIGN KEY (`map_style_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of items
-- ----------------------------

-- ----------------------------
-- Table structure for `item_options`
-- ----------------------------
DROP TABLE IF EXISTS `item_options`;
CREATE TABLE `item_options` (
  `item_id` int(10) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  KEY `item_options_item_id_foreign` (`item_id`),
  KEY `item_options_option_id_foreign` (`option_id`),
  CONSTRAINT `item_options_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_options_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of item_options
-- ----------------------------

-- ----------------------------
-- Table structure for `map_styles`
-- ----------------------------
DROP TABLE IF EXISTS `map_styles`;
CREATE TABLE `map_styles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thumb` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `style` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of map_styles
-- ----------------------------
INSERT INTO `map_styles` VALUES ('1', 'default.png', '');
INSERT INTO `map_styles` VALUES ('2', 'subtle-grayscale.png', '[{\"featureType\":\"landscape\",\"stylers\":[{\"saturation\":-100},{\"lightness\":65},{\"visibility\":\"on\"}]},{\"featureType\":\"poi\",\"stylers\":[{\"saturation\":-100},{\"lightness\":51},{\"visibility\":\"simplified\"}]},{\"featureType\":\"road.highway\",\"stylers\":[{\"saturation\":-100},{\"visibility\":\"simplified\"}]},{\"featureType\":\"road.arterial\",\"stylers\":[{\"saturation\":-100},{\"lightness\":30},{\"visibility\":\"on\"}]},{\"featureType\":\"road.local\",\"stylers\":[{\"saturation\":-100},{\"lightness\":40},{\"visibility\":\"on\"}]},{\"featureType\":\"transit\",\"stylers\":[{\"saturation\":-100},{\"visibility\":\"simplified\"}]},{\"featureType\":\"administrative.province\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"water\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"on\"},{\"lightness\":-25},{\"saturation\":-100}]},{\"featureType\":\"water\",\"elementType\":\"geometry\",\"stylers\":[{\"hue\":\"#ffff00\"},{\"lightness\":-25},{\"saturation\":-97}]}]');
INSERT INTO `map_styles` VALUES ('3', 'pale-dawn.png', '[{\"featureType\":\"water\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#acbcc9\"}]},{\"featureType\":\"landscape\",\"stylers\":[{\"color\":\"#f2e5d4\"}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#c5c6c6\"}]},{\"featureType\":\"road.arterial\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#e4d7c6\"}]},{\"featureType\":\"road.local\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#fbfaf7\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#c5dac6\"}]},{\"featureType\":\"administrative\",\"stylers\":[{\"visibility\":\"on\"},{\"lightness\":33}]},{\"featureType\":\"road\"},{\"featureType\":\"poi.park\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"on\"},{\"lightness\":20}]},{},{\"featureType\":\"road\",\"stylers\":[{\"lightness\":20}]}]');
INSERT INTO `map_styles` VALUES ('4', 'blue-water.png', '[{\"featureType\":\"water\",\"stylers\":[{\"color\":\"#46bcec\"},{\"visibility\":\"on\"}]},{\"featureType\":\"landscape\",\"stylers\":[{\"color\":\"#f2f2f2\"}]},{\"featureType\":\"road\",\"stylers\":[{\"saturation\":-100},{\"lightness\":45}]},{\"featureType\":\"road.highway\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"road.arterial\",\"elementType\":\"labels.icon\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"administrative\",\"elementType\":\"labels.text.fill\",\"stylers\":[{\"color\":\"#444444\"}]},{\"featureType\":\"transit\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi\",\"stylers\":[{\"visibility\":\"off\"}]}]');
INSERT INTO `map_styles` VALUES ('5', 'midnight-commander.png', '[{\"featureType\":\"water\",\"stylers\":[{\"color\":\"#021019\"}]},{\"featureType\":\"landscape\",\"stylers\":[{\"color\":\"#08304b\"}]},{\"featureType\":\"poi\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#0c4152\"},{\"lightness\":5}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#000000\"}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#0b434f\"},{\"lightness\":25}]},{\"featureType\":\"road.arterial\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#000000\"}]},{\"featureType\":\"road.arterial\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#0b3d51\"},{\"lightness\":16}]},{\"featureType\":\"road.local\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"}]},{\"elementType\":\"labels.text.fill\",\"stylers\":[{\"color\":\"#ffffff\"}]},{\"elementType\":\"labels.text.stroke\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":13}]},{\"featureType\":\"transit\",\"stylers\":[{\"color\":\"#146474\"}]},{\"featureType\":\"administrative\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#000000\"}]},{\"featureType\":\"administrative\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#144b53\"},{\"lightness\":14},{\"weight\":1.4}]}]');
INSERT INTO `map_styles` VALUES ('6', 'retro.png', '[{\"featureType\":\"administrative\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"road\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"water\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"transit\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"landscape\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"road.highway\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"road.local\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"water\",\"stylers\":[{\"color\":\"#84afa3\"},{\"lightness\":52}]},{\"stylers\":[{\"saturation\":-17},{\"gamma\":0.36}]},{\"featureType\":\"transit.line\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#3f518c\"}]}]');
INSERT INTO `map_styles` VALUES ('7', 'shades-of-grey.png', '[{\"featureType\":\"water\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":17}]},{\"featureType\":\"landscape\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":20}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":17}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":29},{\"weight\":0.2}]},{\"featureType\":\"road.arterial\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":18}]},{\"featureType\":\"road.local\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":16}]},{\"featureType\":\"poi\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":21}]},{\"elementType\":\"labels.text.stroke\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#000000\"},{\"lightness\":16}]},{\"elementType\":\"labels.text.fill\",\"stylers\":[{\"saturation\":36},{\"color\":\"#000000\"},{\"lightness\":40}]},{\"elementType\":\"labels.icon\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"transit\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":19}]},{\"featureType\":\"administrative\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":20}]},{\"featureType\":\"administrative\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":17},{\"weight\":1.2}]}]');
INSERT INTO `map_styles` VALUES ('8', 'light-monochrome.png', '[{\"featureType\":\"water\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#e9ebed\"},{\"saturation\":-78},{\"lightness\":67},{\"visibility\":\"simplified\"}]},{\"featureType\":\"landscape\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#ffffff\"},{\"saturation\":-100},{\"lightness\":100},{\"visibility\":\"simplified\"}]},{\"featureType\":\"road\",\"elementType\":\"geometry\",\"stylers\":[{\"hue\":\"#bbc0c4\"},{\"saturation\":-93},{\"lightness\":31},{\"visibility\":\"simplified\"}]},{\"featureType\":\"poi\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#ffffff\"},{\"saturation\":-100},{\"lightness\":100},{\"visibility\":\"off\"}]},{\"featureType\":\"road.local\",\"elementType\":\"geometry\",\"stylers\":[{\"hue\":\"#e9ebed\"},{\"saturation\":-90},{\"lightness\":-8},{\"visibility\":\"simplified\"}]},{\"featureType\":\"transit\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#e9ebed\"},{\"saturation\":10},{\"lightness\":69},{\"visibility\":\"on\"}]},{\"featureType\":\"administrative.locality\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#2c2e33\"},{\"saturation\":7},{\"lightness\":19},{\"visibility\":\"on\"}]},{\"featureType\":\"road\",\"elementType\":\"labels\",\"stylers\":[{\"hue\":\"#bbc0c4\"},{\"saturation\":-93},{\"lightness\":31},{\"visibility\":\"on\"}]},{\"featureType\":\"road.arterial\",\"elementType\":\"labels\",\"stylers\":[{\"hue\":\"#bbc0c4\"},{\"saturation\":-93},{\"lightness\":-2},{\"visibility\":\"simplified\"}]}]');
INSERT INTO `map_styles` VALUES ('9', 'paper.png', '[{\"featureType\":\"administrative\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"road\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"water\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"transit\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"landscape\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"road.highway\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"road.local\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"road.arterial\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"water\",\"stylers\":[{\"color\":\"#5f94ff\"},{\"lightness\":26},{\"gamma\":5.86}]},{},{\"featureType\":\"road.highway\",\"stylers\":[{\"weight\":0.6},{\"saturation\":-85},{\"lightness\":61}]},{\"featureType\":\"road\"},{},{\"featureType\":\"landscape\",\"stylers\":[{\"hue\":\"#0066ff\"},{\"saturation\":74},{\"lightness\":100}]}]');
INSERT INTO `map_styles` VALUES ('10', 'apple-maps-esque.png', '[{\"featureType\":\"water\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#a2daf2\"}]},{\"featureType\":\"landscape.man_made\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#f7f1df\"}]},{\"featureType\":\"landscape.natural\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#d0e3b4\"}]},{\"featureType\":\"landscape.natural.terrain\",\"elementType\":\"geometry\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#bde6ab\"}]},{\"featureType\":\"poi\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi.medical\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#fbd3da\"}]},{\"featureType\":\"poi.business\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"road\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"road\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#ffe15f\"}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#efd151\"}]},{\"featureType\":\"road.arterial\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#ffffff\"}]},{\"featureType\":\"road.local\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"black\"}]},{\"featureType\":\"transit.station.airport\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#cfb2db\"}]}]');
INSERT INTO `map_styles` VALUES ('11', 'neutral-blue.png', '[{\"featureType\":\"water\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#193341\"}]},{\"featureType\":\"landscape\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#2c5a71\"}]},{\"featureType\":\"road\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#29768a\"},{\"lightness\":-37}]},{\"featureType\":\"poi\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#406d80\"}]},{\"featureType\":\"transit\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#406d80\"}]},{\"elementType\":\"labels.text.stroke\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#3e606f\"},{\"weight\":2},{\"gamma\":0.84}]},{\"elementType\":\"labels.text.fill\",\"stylers\":[{\"color\":\"#ffffff\"}]},{\"featureType\":\"administrative\",\"elementType\":\"geometry\",\"stylers\":[{\"weight\":0.6},{\"color\":\"#1a3541\"}]},{\"elementType\":\"labels.icon\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#2c5a71\"}]}]');
INSERT INTO `map_styles` VALUES ('12', 'mapbox.png', '[{\"featureType\":\"water\",\"stylers\":[{\"saturation\":43},{\"lightness\":-11},{\"hue\":\"#0088ff\"}]},{\"featureType\":\"road\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"hue\":\"#ff0000\"},{\"saturation\":-100},{\"lightness\":99}]},{\"featureType\":\"road\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#808080\"},{\"lightness\":54}]},{\"featureType\":\"landscape.man_made\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#ece2d9\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#ccdca1\"}]},{\"featureType\":\"road\",\"elementType\":\"labels.text.fill\",\"stylers\":[{\"color\":\"#767676\"}]},{\"featureType\":\"road\",\"elementType\":\"labels.text.stroke\",\"stylers\":[{\"color\":\"#ffffff\"}]},{\"featureType\":\"poi\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"landscape.natural\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#b8cb93\"}]},{\"featureType\":\"poi.park\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"poi.sports_complex\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"poi.medical\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"poi.business\",\"stylers\":[{\"visibility\":\"simplified\"}]}]');
INSERT INTO `map_styles` VALUES ('13', 'flat-map.png', '[{\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"road\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#ffffff\"}]},{\"featureType\":\"road.arterial\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#fee379\"}]},{\"featureType\":\"road.highway\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#fee379\"}]},{\"featureType\":\"landscape\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#f3f4f4\"}]},{\"featureType\":\"water\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#7fc8ed\"}]},{},{\"featureType\":\"road\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#83cead\"}]},{\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"landscape.man_made\",\"elementType\":\"geometry\",\"stylers\":[{\"weight\":0.9},{\"visibility\":\"off\"}]}]');
INSERT INTO `map_styles` VALUES ('14', 'lunar-landscape.png', '[{\"stylers\":[{\"hue\":\"#ff1a00\"},{\"invert_lightness\":true},{\"saturation\":-100},{\"lightness\":33},{\"gamma\":0.5}]},{\"featureType\":\"water\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#2D333C\"}]}]');
INSERT INTO `map_styles` VALUES ('15', 'icy-blue.png', '[{\"stylers\":[{\"hue\":\"#2c3e50\"},{\"saturation\":250}]},{\"featureType\":\"road\",\"elementType\":\"geometry\",\"stylers\":[{\"lightness\":50},{\"visibility\":\"simplified\"}]},{\"featureType\":\"road\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"off\"}]}]');

-- ----------------------------
-- Table structure for `migrations`
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('2014_04_29_064827_create_users_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_29_064849_create_password_reminders_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_29_064857_create_account_activations_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_29_064905_create_settings_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_29_064912_create_categories_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_29_064952_create_items_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_29_065021_create_options_table', '1');
INSERT INTO `migrations` VALUES ('2014_04_29_065037_create_item_options_table', '1');
INSERT INTO `migrations` VALUES ('2014_05_19_142139_create_map_styles_table', '1');

-- ----------------------------
-- Table structure for `options`
-- ----------------------------
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `undeletable` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `options_user_id_foreign` (`user_id`),
  KEY `options_category_id_foreign` (`category_id`),
  CONSTRAINT `options_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `options_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of options
-- ----------------------------

-- ----------------------------
-- Table structure for `password_reminders`
-- ----------------------------
DROP TABLE IF EXISTS `password_reminders`;
CREATE TABLE `password_reminders` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_reminders_email_index` (`email`),
  KEY `password_reminders_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of password_reminders
-- ----------------------------

-- ----------------------------
-- Table structure for `settings`
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  KEY `settings_name_index` (`name`),
  KEY `settings_value_index` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of settings
-- ----------------------------

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'UTC',
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `role` smallint(5) unsigned NOT NULL DEFAULT '3',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `persist_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `expires` datetime DEFAULT NULL,
  `expire_notifications` smallint(5) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_parent_id_foreign` (`parent_id`),
  CONSTRAINT `users_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', null, 'Admin', 'info@example.com', '$2y$10$7jS21OeO5ttMd/mpT9mbGefK0a.sMFbYR6fPijmupVFKdNCSqgOd.', 'en', 'UTC', '0', null, '1', '1', null, null, null, null, '0', '2014-07-04 19:55:55', '2014-07-04 19:55:55', null);
