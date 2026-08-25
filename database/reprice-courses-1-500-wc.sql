-- Reprice published paid courses to random whole dollars from $1 to $500.
-- Run in Hostinger phpMyAdmin on database u651014097_u4vym (wc_ prefix).
-- Free ($0) courses are left unchanged.

START TRANSACTION;

UPDATE wc_postmeta AS pm
INNER JOIN wc_posts AS p ON p.ID = pm.post_id
SET pm.meta_value = FLOOR(1 + (RAND() * 500))
WHERE p.post_type = 'lac_course'
  AND p.post_status = 'publish'
  AND pm.meta_key = '_lac_course_price'
  AND CAST(pm.meta_value AS DECIMAL(10,2)) > 0;

-- Refresh level bands from the new prices.
UPDATE wc_postmeta AS pm_level
INNER JOIN wc_posts AS p ON p.ID = pm_level.post_id
INNER JOIN wc_postmeta AS pm_price
  ON pm_price.post_id = p.ID AND pm_price.meta_key = '_lac_course_price'
SET pm_level.meta_value = CASE
  WHEN CAST(pm_price.meta_value AS DECIMAL(10,2)) <= 150 THEN 'beginner'
  WHEN CAST(pm_price.meta_value AS DECIMAL(10,2)) <= 350 THEN 'intermediate'
  ELSE 'advanced'
END
WHERE p.post_type = 'lac_course'
  AND p.post_status = 'publish'
  AND pm_level.meta_key = '_lac_course_level'
  AND CAST(pm_price.meta_value AS DECIMAL(10,2)) > 0;

-- Refresh hours estimate from the new prices (cap at 25h).
UPDATE wc_postmeta AS pm_hours
INNER JOIN wc_posts AS p ON p.ID = pm_hours.post_id
INNER JOIN wc_postmeta AS pm_price
  ON pm_price.post_id = p.ID AND pm_price.meta_key = '_lac_course_price'
SET pm_hours.meta_value = ROUND(GREATEST(0.5, LEAST(25, CAST(pm_price.meta_value AS DECIMAL(10,2)) * 0.05)), 1)
WHERE p.post_type = 'lac_course'
  AND p.post_status = 'publish'
  AND pm_hours.meta_key = '_lac_course_hours'
  AND CAST(pm_price.meta_value AS DECIMAL(10,2)) > 0;

-- Mark complete so the plugin one-time job does not overwrite again.
INSERT INTO wc_options (option_name, option_value, autoload)
VALUES ('lac_course_prices_randomized_v1', '1', 'on')
ON DUPLICATE KEY UPDATE option_value = '1';

COMMIT;

-- Quick check:
-- SELECT p.post_title, pm.meta_value AS price
-- FROM wc_posts p
-- JOIN wc_postmeta pm ON pm.post_id = p.ID AND pm.meta_key = '_lac_course_price'
-- WHERE p.post_type = 'lac_course' AND p.post_status = 'publish'
-- ORDER BY CAST(pm.meta_value AS DECIMAL(10,2));
