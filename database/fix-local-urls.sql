-- Fix leftover local URLs after deploying to learnaicourses.com
-- Run in phpMyAdmin on database u651014097_u4vym (wc_ prefix)

UPDATE wc_options
SET option_value = REPLACE(option_value, 'http://learnaicourses.local', 'https://learnaicourses.com')
WHERE option_value LIKE '%learnaicourses.local%';

UPDATE wc_options
SET option_value = REPLACE(option_value, 'http://127.0.0.1:8088', 'https://learnaicourses.com')
WHERE option_value LIKE '%127.0.0.1:8088%';

UPDATE wc_posts
SET post_content = REPLACE(post_content, 'http://learnaicourses.local', 'https://learnaicourses.com')
WHERE post_content LIKE '%learnaicourses.local%';

UPDATE wc_posts
SET guid = REPLACE(guid, 'http://learnaicourses.local', 'https://learnaicourses.com')
WHERE guid LIKE '%learnaicourses.local%';

UPDATE wc_postmeta
SET meta_value = REPLACE(meta_value, 'http://learnaicourses.local', 'https://learnaicourses.com')
WHERE meta_value LIKE '%learnaicourses.local%';

-- Ensure site URL is production HTTPS
UPDATE wc_options
SET option_value = 'https://learnaicourses.com'
WHERE option_name IN ('siteurl', 'home');

-- Emails
UPDATE wc_options SET option_value = REPLACE(option_value, 'fenllinskiii16@gmail.com', 'fenllinskiii16@gmail.com') WHERE option_value LIKE '%fenllinskiii16@gmail.com%';
UPDATE wc_posts SET post_content = REPLACE(post_content, 'fenllinskiii16@gmail.com', 'fenllinskiii16@gmail.com') WHERE post_content LIKE '%fenllinskiii16@gmail.com%';
UPDATE wc_posts SET post_content = REPLACE(post_content, 'fenllinskiii16@gmail.com', 'fenllinskiii16@gmail.com') WHERE post_content LIKE '%fenllinskiii16@gmail.com%';
UPDATE wc_posts SET post_content = REPLACE(post_content, 'fenllinskiii16@gmail.com', 'fenllinskiii16@gmail.com') WHERE post_content LIKE '%fenllinskiii16@gmail.com%';
