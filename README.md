# Learn AI Courses

WordPress LMS product for practical AI courses.

## Stack

- WordPress in this folder (Apache DocumentRoot `/var/www/html`)
- MySQL 8 via Docker Compose on host port `13306`
- Theme: `learn-ai-courses`
- Plugin: `lac-lms` (courses, lessons, enrollment, REST)

## Quick start

```bash
cd /var/www/html/learnaicourses
./start.sh
```

Or manually:

```bash
docker compose up -d
php -S 127.0.0.1:8088 router.php
```

- Site: http://127.0.0.1:8088
- Admin: http://127.0.0.1:8088/wp-admin
- Username: `admin`
- Password: `Admin@LearnAI2026`

Apache note: `steorra.local` currently uses a catch-all `*` alias and returns 503 for `localhost`. Use the PHP server above, or enable `learnaicourses.local.conf` as a named vhost.

## Environment

Copy values live in `.env` (`DB_*`, debug flags). `wp-config.php` loads debug constants from `.env`.

## Product features

- Course + lesson custom post types
- Free enrollment with progress tracking
- REST API under `/wp-json/lac-lms/v1/` (encrypted ids only; no personal fields)
- Demo courses seeded on plugin activation

## Clone & configure

```bash
git clone https://github.com/Ragul-Muthukumar/learnaicourses.git
cd learnaicourses
cp wp-config-example.php wp-config.php
# Generate real salts at https://api.wordpress.org/secret-key/1.1/salt/
docker compose up -d
./start.sh
```

`wp-config.php` and `.env` are gitignored — do not commit secrets.
