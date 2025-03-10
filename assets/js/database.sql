CREATE TABLE category (
      id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(150) NOT NULL
);

CREATE TABLE device (
    id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
    parent BIGINT(20),
    user BIGINT(20) NOT NULL,
    slug VARCHAR(250) NOT NULL,
    body LONGTEXT NOT NULL,
    price DOUBLE NOT NULL,
    show_phone TINYINT(1),
    created DATETIME NOT NULL,
    deleted DATETIME,
    title VARCHAR(100) NOT NULL,
    status VARCHAR(10) NOT NULL,
    location VARCHAR(100) NOT NULL,
    phone_number VARCHAR(10)
);

CREATE TABLE category_device (
     id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
     device BIGINT(20) NOT NULL,
     category BIGINT(20) NOT NULL
);

CREATE TABLE device_picture (
    id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
    device BIGINT(20) NOT NULL,
    created DATETIME NOT NULL,
    deleted DATETIME
);

CREATE TABLE favorite (
  id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
  user BIGINT(20) NOT NULL,
  device BIGINT(20) NOT NULL
);

CREATE TABLE user (
      id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
      type BIGINT(20) NOT NULL,
      created DATETIME NOT NULL,
      siret VARCHAR(30),
      is_verified TINYINT(1) NOT NULL,
      is_deleted TINYINT(1),
      username VARCHAR(100) NOT NULL,
      firstname VARCHAR(255),
      lastname VARCHAR(255),
      password VARCHAR(255) NOT NULL,
      email VARCHAR(255) NOT NULL,
      avatar VARCHAR(255),
      role JSON
);

CREATE TABLE notice (
    id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
    parent BIGINT(20),
    device BIGINT(20) NOT NULL,
    user BIGINT(20) NOT NULL,
    rate DOUBLE NOT NULL,
    created DATETIME NOT NULL,
    content LONGTEXT,
    is_deleted TINYINT(1)
);

CREATE TABLE rating (
    id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
    user BIGINT(20) NOT NULL,
    device BIGINT(20) NOT NULL,
    mean_rate DOUBLE NOT NULL
);

CREATE TABLE rating_detail (
   id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
   parent BIGINT(20),
   device BIGINT(20) NOT NULL,
   rate DOUBLE NOT NULL,
   created DATETIME NOT NULL,
   content LONGTEXT,
   is_deleted TINYINT(1) NOT NULL
);

CREATE TABLE alert (
   id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
   author BIGINT(20) NOT NULL,
   message BIGINT(20),
   notice BIGINT(20) ,
   user BIGINT(20),
   created DATETIME NOT NULL,
   content LONGTEXT
);

CREATE TABLE message (
     id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
     parent BIGINT(20),
     sender BIGINT(20) NOT NULL,
     receiver BIGINT(20) NOT NULL,
     content LONGTEXT NOT NULL,
     is_deleted TINYINT(1),
     created DATETIME NOT NULL
);

CREATE TABLE Plan (
      id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
      slug VARCHAR(255) NOT NULL,
      stripe_id VARCHAR(255) NOT NULL,
      rate INTEGER NOT NULL,
      created DATETIME NOT NULL,
      name VARCHAR(255) NOT NULL,
      description LONGTEXT NOT NULL
);

CREATE TABLE subscription (
      id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
      user BIGINT(20) NOT NULL,
      plan BIGINT(20) NOT NULL,
      stripe_id VARCHAR(255) NOT NULL,
      current_period_start DATETIME NOT NULL,
      current_period_end DATETIME NOT NULL,
      is_active TINYINT(1) NOT NULL
);

CREATE TABLE reservation (
     id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
     user BIGINT(20) NOT NULL,
     status BIGINT(20) NOT NULL,
     device BIGINT(20) NOT NULL,
     price DOUBLE NOT NULL,
     created DATETIME NOT NULL,
     ended DATETIME
);

CREATE TABLE ReservationStatus (
   id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
   name VARCHAR(150) NOT NULL
);

CREATE TABLE warn_user (
   id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
   user BIGINT(20) NOT NULL,
   description LONGTEXT NOT NULL,
   is_banned TINYINT(1),
   created DATETIME NOT NULL,
   started DATETIME,
   ended DATETIME
);

CREATE TABLE TypeUser (
  id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL
);

ALTER TABLE device ADD FOREIGN KEY (user) REFERENCES user (id);
ALTER TABLE device ADD FOREIGN KEY (parent) REFERENCES device (id);
ALTER TABLE category_device ADD FOREIGN KEY (device) REFERENCES device (id);
ALTER TABLE category_device ADD FOREIGN KEY (category) REFERENCES category (id);
ALTER TABLE device_picture ADD FOREIGN KEY (device) REFERENCES device (id);
ALTER TABLE favorite ADD FOREIGN KEY (user) REFERENCES user (id);
ALTER TABLE favorite ADD FOREIGN KEY (device) REFERENCES device (id);
ALTER TABLE user ADD FOREIGN KEY (type) REFERENCES TypeUser (id);
ALTER TABLE notice ADD FOREIGN KEY (device) REFERENCES device (id);
ALTER TABLE notice ADD FOREIGN KEY (user) REFERENCES user (id);
ALTER TABLE rating ADD FOREIGN KEY (device) REFERENCES device (id);
ALTER TABLE rating ADD FOREIGN KEY (user) REFERENCES user (id);
ALTER TABLE rating_detail ADD FOREIGN KEY (device) REFERENCES device (id);
ALTER TABLE rating_detail ADD FOREIGN KEY (parent) REFERENCES rating_detail (id);
ALTER TABLE alert ADD FOREIGN KEY (user) REFERENCES user (id);
ALTER TABLE alert ADD FOREIGN KEY (message) REFERENCES message (id);
ALTER TABLE alert ADD FOREIGN KEY (notice) REFERENCES notice (id);
ALTER TABLE message ADD FOREIGN KEY (sender) REFERENCES user (id);
ALTER TABLE message ADD FOREIGN KEY (receiver) REFERENCES user (id);
ALTER TABLE subscription ADD FOREIGN KEY (user) REFERENCES user (id);
ALTER TABLE subscription ADD FOREIGN KEY (plan) REFERENCES Plan (id);
ALTER TABLE reservation ADD FOREIGN KEY (user) REFERENCES user (id);
ALTER TABLE reservation ADD FOREIGN KEY (device) REFERENCES device (id);
ALTER TABLE reservation ADD FOREIGN KEY (status) REFERENCES ReservationStatus (id);
ALTER TABLE warn_user ADD FOREIGN KEY (user) REFERENCES user (id);
