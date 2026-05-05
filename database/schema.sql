CREATE DATABASE IF NOT EXISTS auth_system;
USE auth_system;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mobile VARCHAR(15) NOT NULL UNIQUE,
  email VARCHAR(191) NOT NULL UNIQUE,
  profile_image LONGTEXT NULL,
  password_hash VARCHAR(255) NOT NULL,

  email_otp VARCHAR(6) NULL,
  email_otp_generated_at DATETIME NULL,
  email_otp_used TINYINT(1) NOT NULL DEFAULT 0,

  mobile_otp VARCHAR(6) NULL,
  mobile_otp_generated_at DATETIME NULL,
  mobile_otp_used TINYINT(1) NOT NULL DEFAULT 0,

  successful_login_count INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
);
