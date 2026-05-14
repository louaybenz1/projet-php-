-- ============================================================
-- TEK-UP GYM DATABASE
-- Created for: First-year PHP OOP student project
-- ============================================================

CREATE DATABASE IF NOT EXISTS gym_db;
USE gym_db;

-- ============================================================
-- TABLE 1: users
-- Stores ALL accounts: clients, coaches, and the admin.
-- The 'role' column tells us who is who.
-- ============================================================

CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100)    NOT NULL,
    email       VARCHAR(150)    NOT NULL UNIQUE,
    password    VARCHAR(255)    NOT NULL,   -- stored as a hashed string
    phone       VARCHAR(20),
    age         INT,
    role        ENUM('client', 'coach', 'admin') NOT NULL DEFAULT 'client',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABLE 2: subscriptions
-- Each client has one active subscription at a time.
-- Linked to users via client_id (foreign key).
-- ============================================================

CREATE TABLE subscriptions (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    client_id       INT NOT NULL,
    plan_name       VARCHAR(100) NOT NULL,       -- e.g. "Gold - 6 Months"
    start_date      DATE NOT NULL,
    end_date        DATE NOT NULL,
    status          ENUM('active', 'expired', 'pending') DEFAULT 'active',

    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE 3: sessions
-- Gym class schedule. Visible to all clients.
-- Optionally assigned to a coach.
-- ============================================================

CREATE TABLE sessions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(100) NOT NULL,           -- e.g. "Morning Cardio"
    description TEXT,
    coach_id    INT,                             -- which coach leads it
    session_date DATE NOT NULL,
    start_time  TIME NOT NULL,
    end_time    TIME NOT NULL,

    FOREIGN KEY (coach_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================
-- TABLE 4: coach_messages
-- A coach writes an announcement visible to their clients.
-- ============================================================

CREATE TABLE coach_messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    coach_id    INT NOT NULL,
    title       VARCHAR(150) NOT NULL,
    body        TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (coach_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE 5: coach_clients
-- A simple pivot/link table.
-- Tells us: "which coach is assigned to which client?"
-- ============================================================

CREATE TABLE coach_clients (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    coach_id    INT NOT NULL,
    client_id   INT NOT NULL,

    FOREIGN KEY (coach_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);


-- ============================================================
-- SAMPLE DATA
-- Enough to test every part of the app right away.
-- Passwords are hashed versions of "password123"
-- (generated with PHP's password_hash())
-- ============================================================

-- Admin account
INSERT INTO users (full_name, email, password, phone, age, role) VALUES
(
    'Admin TEK-UP',
    'admin@tekupgym.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '+216 11 000 000',
    35,
    'admin'
);

-- Coach accounts
INSERT INTO users (full_name, email, password, phone, age, role) VALUES
(
    'Ahmed Musculation',
    'ahmed@tekupgym.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '+216 22 111 111',
    30,
    'coach'
),
(
    'Lobna Pilates',
    'lobna@tekupgym.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '+216 22 222 222',
    27,
    'coach'
),
(
    'Djo YES YOU CAN',
    'djo@tekupgym.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '+216 22 333 333',
    32,
    'coach'
);

-- Client accounts
INSERT INTO users (full_name, email, password, phone, age, role) VALUES
(
    'Louay Benzarti',
    'louay@mail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '+216 55 100 100',
    22,
    'client'
),
(
    'Asma Detox',
    'asma@mail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '+216 55 200 200',
    24,
    'client'
),
(
    'Kamel Musculation',
    'kamel@mail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '+216 55 300 300',
    28,
    'client'
);

-- Subscriptions for each client
-- Client IDs: Louay=5, Asma=6, Kamel=7
INSERT INTO subscriptions (client_id, plan_name, start_date, end_date, status) VALUES
(5, '6 Months - Gold',   '2025-01-01', '2025-07-01', 'active'),
(6, '1 Month - Basic',   '2025-04-01', '2025-05-01', 'expired'),
(7, '12 Months - Elite', '2025-03-01', '2026-03-01', 'active');

-- Sessions (coach IDs: Ahmed=2, Lobna=3, Djo=4)
INSERT INTO sessions (title, description, coach_id, session_date, start_time, end_time) VALUES
('Morning Strength',    'Full body strength training session.',  2, '2025-05-10', '08:00:00', '09:30:00'),
('Pilates Flow',        'Core and flexibility focused class.',   3, '2025-05-11', '10:00:00', '11:00:00'),
('Cardio Blast',        'High intensity cardio endurance.',      4, '2025-05-12', '17:00:00', '18:30:00'),
('Upper Body Focus',    'Chest, back and shoulders.',           2, '2025-05-13', '08:00:00', '09:30:00'),
('Evening Stretch',     'Cool-down and mobility session.',      3, '2025-05-14', '19:00:00', '20:00:00');

-- Coach messages
-- coach_id 2 = Ahmed, 3 = Lobna, 4 = Djo
INSERT INTO coach_messages (coach_id, title, body) VALUES
(
    2,
    'Reminder: Chest Day Thursday',
    'Don''t forget to bring your gym gloves. We will be going heavy on bench press this Thursday. Stay hydrated!'
),
(
    3,
    'New Pilates Schedule',
    'Starting next week, Pilates Flow moves to 10am on Saturdays as well. See you on the mat!'
),
(
    4,
    'Welcome New Members!',
    'Big welcome to everyone who joined this month. Feel free to approach me anytime for advice on your cardio programme.'
);

-- Assign coaches to clients (coach_clients)
-- Ahmed (2) coaches Louay (5) and Kamel (7)
-- Lobna (3) coaches Asma (6)
INSERT INTO coach_clients (coach_id, client_id) VALUES
(2, 5),
(2, 7),
(3, 6);


-- ============================================================
-- PATCH: contact_messages table
-- Run this after gym_db.sql if the database already exists,
-- OR add this block at the end of gym_db.sql for a fresh setup.
-- ============================================================
 
USE gym_db;
 
CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL,
    message    TEXT          NOT NULL,
    is_read    TINYINT(1)    NOT NULL DEFAULT 0,   -- 0 = unread, 1 = read
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);
 
-- ============================================================
-- Sample data — three realistic contact submissions
-- so the admin panel has something to display immediately.
-- ============================================================
INSERT INTO contact_messages (name, email, message, is_read) VALUES
(
    'Rami Bouaziz',
    'rami@mail.com',
    'Hello, I am interested in joining the gym. Could you please tell me more about the Elite 12-month plan and whether personal training sessions are included?',
    0
),
(
    'Nour Khelil',
    'nour@mail.com',
    'Hi! I visited the gym yesterday and I really liked the atmosphere. I wanted to ask about group Pilates classes — do I need a special subscription for those?',
    1
),
(
    'Bilel Trabelsi',
    'bilel@mail.com',
    'I lost my gym card last week. Is there a way to get a replacement? Also, what are your opening hours on weekends?',
    0
);