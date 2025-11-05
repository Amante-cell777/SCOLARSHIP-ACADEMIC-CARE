-- ==============================================================
--  Scholarship Management System – FULL DB SCHEMA (WITH full_name)
-- ==============================================================

-- 1. Create Database
CREATE DATABASE IF NOT EXISTS scholarship_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;
USE scholarship_db;

-- ==============================================================
--  STEP 1: Disable foreign key checks
-- ==============================================================
SET FOREIGN_KEY_CHECKS = 0;

-- ==============================================================
--  STEP 2: Drop tables (children first)
-- ==============================================================
DROP TABLE IF EXISTS student_documents;
DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS otps;
DROP TABLE IF EXISTS scholarships;
DROP TABLE IF EXISTS users;

-- ==============================================================
--  STEP 3: Re-create tables
-- ==============================================================

-- 2. Users Table – NOW INCLUDES full_name GENERATED COLUMN
CREATE TABLE users (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    first_name       VARCHAR(100) NOT NULL,
    middle_name      VARCHAR(100),
    last_name        VARCHAR(100) NOT NULL,
    dob              DATE,
    sex              ENUM('Male','Female','Other'),
    email            VARCHAR(255) NOT NULL UNIQUE,
    contact          VARCHAR(20) NOT NULL,
    program          VARCHAR(100) NOT NULL,
    term             VARCHAR(50) NOT NULL,
    applying_as      VARCHAR(50) NOT NULL,
    guardian_name    VARCHAR(150) NOT NULL,
    guardian_contact VARCHAR(20) NOT NULL,
    gpa              DECIMAL(3,2),
    year_level       VARCHAR(20) NOT NULL,
    password         VARCHAR(255) NOT NULL,
    role             ENUM('student','admin') DEFAULT 'student',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- GENERATED FULL NAME (Last, First Middle)
    full_name VARCHAR(255) GENERATED ALWAYS AS
        (CONCAT(
            last_name, ', ',
            first_name,
            IF(middle_name IS NOT NULL AND middle_name != '', CONCAT(' ', middle_name), '')
        )) STORED,

    -- Indexes
    INDEX idx_email (email),
    INDEX idx_contact (contact),
    INDEX idx_role (role),
    INDEX idx_full_name (full_name)
) ENGINE=InnoDB;

-- 3. Scholarships
CREATE TABLE scholarships (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    amount      DECIMAL(10,2),
    gpa_min     DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    gpa_max     DECIMAL(5,2) NOT NULL DEFAULT 155.00,
    deadline    DATE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_deadline (deadline)
) ENGINE=InnoDB;

-- 4. Applications
CREATE TABLE applications (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    scholarship_id  INT NOT NULL,
    gpa             DECIMAL(5,2) NOT NULL,
    status          ENUM('pending','approved','rejected') DEFAULT 'pending',
    documents       JSON,
    submitted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY fk_app_student (student_id)
        REFERENCES users(id) ON DELETE CASCADE,

    FOREIGN KEY fk_app_scholarship (scholarship_id)
        REFERENCES scholarships(id) ON DELETE CASCADE,

    INDEX idx_student (student_id),
    INDEX idx_scholarship (scholarship_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- 5. Student Documents
CREATE TABLE student_documents (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    student_id    INT NOT NULL,
    file_path     VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    upload_type   ENUM('transcript','recommendation','id_proof','other') DEFAULT 'other',
    status        ENUM('pending','approved','rejected') DEFAULT 'pending',
    uploaded_at   DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY fk_doc_student (student_id)
        REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_student_id (student_id),
    INDEX idx_status (status),
    INDEX idx_uploaded (uploaded_at)
) ENGINE=InnoDB;

-- 6. OTP Table
CREATE TABLE otps (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(255) NOT NULL,
    code        VARCHAR(6) NOT NULL,
    expires_at  DATETIME NOT NULL,
    used        TINYINT(1) DEFAULT 0,

    INDEX idx_email_expires (email, expires_at)
) ENGINE=InnoDB;

-- ==============================================================
--  STEP 4: Re-enable foreign keys
-- ==============================================================
SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================
--  SUCCESS: Database ready + full_name column added!
-- ==============================================================