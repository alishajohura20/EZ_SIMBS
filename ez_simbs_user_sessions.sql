-- EZ_SIMBS User Sessions table
-- Supports multiple concurrent login sessions per user with per-session
-- tokens, remember-me cookies, expiry, and device tracking.

USE ez_simbs;

CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(128) NOT NULL UNIQUE,
    remember_token VARCHAR(128) DEFAULT NULL UNIQUE,
    ip_address VARCHAR(45) DEFAULT '',
    user_agent VARCHAR(255) DEFAULT '',
    device VARCHAR(100) DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_activity DATETIME DEFAULT NULL,
    expires_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_sessions_user (user_id),
    INDEX idx_user_sessions_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;