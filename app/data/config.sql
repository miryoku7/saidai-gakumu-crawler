CREATE TABLE info (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT
  href TEXT,
  registered DATE DEFAULT (datetime('now', '+09:00:00'))
)