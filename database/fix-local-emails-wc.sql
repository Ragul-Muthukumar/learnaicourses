-- Replace ALL @learnaicourses.local emails with fenllinskiii16@gmail.com
-- Run in Hostinger phpMyAdmin (wc_ prefix)

UPDATE wc_posts SET post_content = REPLACE(post_content, 'privacy@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE post_content LIKE '%privacy@learnaicourses.local%';
UPDATE wc_posts SET post_content = REPLACE(post_content, 'billing@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE post_content LIKE '%billing@learnaicourses.local%';
UPDATE wc_posts SET post_content = REPLACE(post_content, 'legal@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE post_content LIKE '%legal@learnaicourses.local%';
UPDATE wc_posts SET post_content = REPLACE(post_content, 'support@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE post_content LIKE '%support@learnaicourses.local%';
UPDATE wc_posts SET post_content = REPLACE(post_content, 'admin@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE post_content LIKE '%admin@learnaicourses.local%';
UPDATE wc_posts SET post_content = REPLACE(post_content, 'mailto:privacy@learnaicourses.local', 'mailto:fenllinskiii16@gmail.com') WHERE post_content LIKE '%mailto:privacy@learnaicourses.local%';

UPDATE wc_posts SET post_excerpt = REPLACE(post_excerpt, 'privacy@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE post_excerpt LIKE '%privacy@learnaicourses.local%';
UPDATE wc_posts SET post_excerpt = REPLACE(post_excerpt, 'billing@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE post_excerpt LIKE '%billing@learnaicourses.local%';
UPDATE wc_posts SET post_excerpt = REPLACE(post_excerpt, 'legal@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE post_excerpt LIKE '%legal@learnaicourses.local%';
UPDATE wc_posts SET post_excerpt = REPLACE(post_excerpt, 'support@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE post_excerpt LIKE '%support@learnaicourses.local%';

UPDATE wc_postmeta SET meta_value = REPLACE(meta_value, 'privacy@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE meta_value LIKE '%privacy@learnaicourses.local%';
UPDATE wc_postmeta SET meta_value = REPLACE(meta_value, 'billing@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE meta_value LIKE '%billing@learnaicourses.local%';
UPDATE wc_postmeta SET meta_value = REPLACE(meta_value, 'legal@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE meta_value LIKE '%legal@learnaicourses.local%';
UPDATE wc_postmeta SET meta_value = REPLACE(meta_value, 'support@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE meta_value LIKE '%support@learnaicourses.local%';

UPDATE wc_options SET option_value = REPLACE(option_value, 'privacy@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE option_value LIKE '%privacy@learnaicourses.local%';
UPDATE wc_options SET option_value = REPLACE(option_value, 'billing@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE option_value LIKE '%billing@learnaicourses.local%';
UPDATE wc_options SET option_value = REPLACE(option_value, 'legal@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE option_value LIKE '%legal@learnaicourses.local%';
UPDATE wc_options SET option_value = REPLACE(option_value, 'support@learnaicourses.local', 'fenllinskiii16@gmail.com') WHERE option_value LIKE '%support@learnaicourses.local%';

UPDATE wc_users SET user_email = 'fenllinskiii16@gmail.com' WHERE user_email LIKE '%@learnaicourses.local';

-- Verify:
-- SELECT ID, post_title FROM wc_posts WHERE post_content LIKE '%@%.local%';
