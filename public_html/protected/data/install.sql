SET NAMES 'utf8';

CREATE TABLE IF NOT EXISTS `order` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL,
  `comment` VARCHAR(255) DEFAULT NULL,
  products TEXT NOT NULL,
  payment TEXT NOT NULL,
  payment_complete TINYINT(1) NOT NULL DEFAULT 0,
  completed TINYINT(1) NOT NULL DEFAULT 0,
  created DATETIME NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS blog (
  id INT(11) NOT NULL AUTO_INCREMENT,
  alias VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  ordering INT(11) NOT NULL,
  params TEXT NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS category (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description MEDIUMTEXT NOT NULL,
  ordering INT(11) NOT NULL DEFAULT 1,
  root INT(11) NOT NULL,
  lft INT(11) NOT NULL,
  rgt INT(11) NOT NULL,
  level SMALLINT(5) NOT NULL,
  PRIMARY KEY (id),
  INDEX level (level),
  INDEX lft (lft),
  INDEX rgt (rgt),
  INDEX root (root)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS event (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  intro TEXT NOT NULL,
  `text` TEXT NOT NULL,
  created DATETIME NOT NULL,
  publish TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS file (
  id INT(11) NOT NULL AUTO_INCREMENT,
  model VARCHAR(20) NOT NULL,
  item_id INT(11) NOT NULL,
  filename VARCHAR(100) NOT NULL,
  description VARCHAR(500) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS image (
  id INT(11) NOT NULL AUTO_INCREMENT,
  model VARCHAR(20) NOT NULL,
  item_id INT(11) NOT NULL,
  filename VARCHAR(100) NOT NULL,
  description VARCHAR(500) NOT NULL,
  ordering INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  url VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS menu (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  type VARCHAR(255) NOT NULL DEFAULT 'model',
  options VARCHAR(255) NOT NULL DEFAULT '',
  ordering INT(11) NOT NULL DEFAULT 1,
  `default` TINYINT(1) NOT NULL DEFAULT 0,
  hidden TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE INDEX id (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS metadata (
  id INT(11) NOT NULL AUTO_INCREMENT,
  owner_name VARCHAR(50) NOT NULL,
  owner_id INT(11) NOT NULL,
  meta_title VARCHAR(255) NOT NULL,
  meta_key TEXT NOT NULL,
  meta_desc TEXT NOT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX id (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS page (
  id INT(11) NOT NULL AUTO_INCREMENT,
  blog_id INT(11) NOT NULL,
  alias VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  intro TEXT NOT NULL,
  `text` MEDIUMTEXT NOT NULL,
  created DATETIME NOT NULL,
  modified DATETIME NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS product (
  id INT(11) NOT NULL AUTO_INCREMENT,
  category_id INT(11) NOT NULL,
  code VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  price INT(11) NOT NULL DEFAULT 0,
  notexist TINYINT(1) NOT NULL DEFAULT 0,
  sale TINYINT(1) NOT NULL DEFAULT 0,
  ordering INT(11) NOT NULL DEFAULT 1,
  new TINYINT(1) NOT NULL,
  created DATETIME NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS settings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  category VARCHAR(64) NOT NULL DEFAULT 'system',
  `key` VARCHAR(255) NOT NULL,
  value TEXT NOT NULL,
  PRIMARY KEY (id),
  INDEX category_key (category, `key`)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS question(
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  question VARCHAR(255) NOT NULL,
  answer TEXT NOT NULL,
  created DATETIME NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS slide (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  link VARCHAR(255) NOT NULL,
  filename VARCHAR(255) NOT NULL,
  `type` int(11) not null DEFAULT 0,
  ordering INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;


CREATE TABLE `feedback` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`username` varchar(255) not null,
	`mail` varchar(255) not null,
	`question` varchar(255) not null,
	`answer` text not null,
	`published` text not null,
	`created` datetime not null,
	PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE `banner` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) not null,
	`type` int(11) not null DEFAULT 0,
	`link` varchar(255) not null,
	`filename` varchar(255) not null,
	`ordering` int(11) not null DEFAULT 1,
	PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

CREATE TABLE `product_review` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`product_id` int(11) not null,
	`mark` int(11) not null,
	`username` varchar(255) not null,
	`text` mediumtext not null,
	`ts` timestamp not null,
	`ip` int(11) not null,
	`published` tinyint(1) not null,
	PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8;

INSERT INTO event VALUES (1, 'Создали сайт', '', '<p>Мы создали сайт!</p>', now(), 1);
INSERT INTO menu VALUES
  (1, 'Главная', 'model', '{"model":"page","id":"1"}', -2, 1, 1),
  (2, 'Новости', 'model', '{"model":"event"}', 1, 0, 0);
INSERT INTO page VALUES (1, 0, 'index', 'Главная', '', '<p>Сайт находится в разработке</p>', now(), now());
