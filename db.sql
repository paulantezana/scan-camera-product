CREATE TABLE `app_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_name` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(64) DEFAULT '',
  `avatar` varchar(64) DEFAULT '',
  `full_name` varchar(255) NOT NULL,
  `state` tinyint(4) DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user` varchar(64) DEFAULT '',
  `updated_user` varchar(64) DEFAULT '',

  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB;

CREATE TABLE `log_exceptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text DEFAULT NULL,
  `host` varchar(128) DEFAULT '',
  `path` varchar(255) DEFAULT '',
  `stack` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `state` tinyint(4) DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user` varchar(64) DEFAULT '',
  `updated_user` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

insert into app_users (user_name, password, email, full_name)
values ('admin', '$2y$10$tyu3WMEgkyvFwkyncn/Rv.7pirfuVBFXcBF07xFGUN0rAMCWVBZva', 'admin@gmail.com', 'admin');


CREATE TABLE `app_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` varchar(128) NOT NULL,
  `description` varchar(128) NOT NULL,
  `verified` tinyint(4) DEFAULT 0,
  `verified_date` datetime DEFAULT NULL,

  `state` tinyint(4) DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user` varchar(64) DEFAULT '',
  `updated_user` varchar(64) DEFAULT ''
) ENGINE=InnoDB;
