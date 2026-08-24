# Learn AI Courses

WordPress LMS product for practical AI courses.

## Stack

- WordPress in this folder (Apache DocumentRoot `/var/www/html`)
- MySQL 8 via Docker Compose on host port `13306`
- Theme: `learn-ai-courses`
- Plugin: `lac-lms` (courses, lessons, enrollment, PayPal checkout, REST)

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

Copy `.env.example` to `.env` and fill values (`DB_*`, debug flags, PayPal). `wp-config.php` loads debug + PayPal constants from `.env`.

## PayPal checkout

Paid courses (`_lac_course_price` > 0) show PayPal Smart Buttons. Free courses still use **Enroll free**.

1. Create a sandbox app: https://developer.paypal.com/dashboard/applications/sandbox  
2. Put `PAYPAL_CLIENT_ID`, `PAYPAL_CLIENT_SECRET`, `PAYPAL_MODE=sandbox`, and `PAYPAL_CURRENCY=USD` in `.env`  
3. Restart PHP / reload the site  
4. Log in as a learner, open a priced course, pay with a [sandbox buyer account](https://developer.paypal.com/dashboard/accounts)

REST routes:

- `POST /wp-json/lac-lms/v1/paypal/create-order` — start checkout  
- `POST /wp-json/lac-lms/v1/paypal/capture-order` — capture payment and enroll  

Orders are stored in `{prefix}lac_orders`. Personal fields are never returned from the API.

## Product features

- Course + lesson custom post types
- Full product detail page for every course (works with Kadence)
- Free enrollment + paid purchase (PayPal or local mock checkout)
- Progress tracking after enrollment
- REST API under `/wp-json/lac-lms/v1/` (encrypted ids only; no personal fields)
- Demo courses seeded on plugin activation

## Browse & buy

- Catalog: `/courses/`
- Each course has its own detail URL under `/courses/{slug}/`
- Paid courses show **Buy now** (mock) or PayPal Smart Buttons
- Free courses show **Enroll free**

Set `PAYPAL_MODE=mock` in `.env` for local purchases without PayPal credentials. Switch to `sandbox` + credentials for real PayPal testing.

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


## Media & database for other devices

- Course/hero images are tracked under `wp-content/uploads/` so clones get the neon hero and course covers.
- A SQL dump is available at `database/learnaicourses.sql`.

Import database after `docker compose up -d`:

```bash
docker exec -i learnaicourses-mysql mysql -ulearnaicourses -plearnaicourses_pass < database/learnaicourses.sql
```

Then copy `wp-config-example.php` to `wp-config.php` and start the site.
