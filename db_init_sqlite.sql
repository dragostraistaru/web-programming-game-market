-- SQLite initialization for GameMarket application
-- The application also creates these tables automatically from config/pdo.php.

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL UNIQUE,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL DEFAULT 'cumparator'
    CHECK (role IN ('user', 'admin', 'vanzator', 'cumparator', 'ambele')),
  avatar TEXT DEFAULT NULL,
  bio TEXT DEFAULT NULL,
  remember_token TEXT DEFAULT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS seller_listings (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  seller_id INTEGER NOT NULL,
  game_title TEXT NOT NULL,
  platform TEXT NOT NULL,
  key_type TEXT NOT NULL,
  price REAL NOT NULL,
  stock INTEGER NOT NULL,
  status TEXT NOT NULL DEFAULT 'activ',
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS app_events (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT,
  event_name TEXT NOT NULL,
  created_at TEXT NOT NULL
);
