-- Fix Learn AI Courses admin access on Hostinger (wc_ prefix).
-- Cause: dump kept lac_user_roles / lac_capabilities while WP expects wc_*.
-- Run in phpMyAdmin on the live database, then log in again.

-- 1) Rename role definitions option to the live prefix.
UPDATE wc_options
SET option_name = 'wc_user_roles'
WHERE option_name = 'lac_user_roles';

-- 2) Reset admin capability meta for user id 1.
DELETE FROM wc_usermeta
WHERE user_id = 1
  AND meta_key IN (
    'wc_capabilities', 'wc_user_level',
    'lac_capabilities', 'lac_user_level',
    'wp_capabilities', 'wp_user_level'
  );

INSERT INTO wc_usermeta (user_id, meta_key, meta_value) VALUES
(1, 'wc_capabilities', 'a:1:{s:13:"administrator";b:1;}'),
(1, 'wc_user_level', '10');

-- 3) Verify.
SELECT option_name, LEFT(option_value, 60) AS option_value
FROM wc_options
WHERE option_name IN ('wc_user_roles', 'lac_user_roles');

SELECT user_id, meta_key, meta_value
FROM wc_usermeta
WHERE user_id = 1
  AND meta_key LIKE '%capabilities%';
