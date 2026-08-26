-- Point all public contact emails to fenllinskiii16@gmail.com
-- Run in Hostinger phpMyAdmin (wc_ prefix)

UPDATE wc_posts
SET post_content = REPLACE(post_content, 'billing@learnaicourses.local', 'fenllinskiii16@gmail.com')
WHERE post_content LIKE '%billing@learnaicourses.local%';

UPDATE wc_posts
SET post_content = REPLACE(post_content, 'legal@learnaicourses.local', 'fenllinskiii16@gmail.com')
WHERE post_content LIKE '%legal@learnaicourses.local%';

UPDATE wc_posts
SET post_content = REPLACE(post_content, 'support@learnaicourses.local', 'fenllinskiii16@gmail.com')
WHERE post_content LIKE '%support@learnaicourses.local%';

UPDATE wc_posts
SET post_content = REPLACE(post_content, 'billing@learnaicourses.com', 'fenllinskiii16@gmail.com')
WHERE post_content LIKE '%billing@learnaicourses.com%';

UPDATE wc_posts
SET post_content = REPLACE(post_content, 'legal@learnaicourses.com', 'fenllinskiii16@gmail.com')
WHERE post_content LIKE '%legal@learnaicourses.com%';

UPDATE wc_posts
SET post_content = REPLACE(post_content, 'support@learnaicourses.com', 'fenllinskiii16@gmail.com')
WHERE post_content LIKE '%support@learnaicourses.com%';

UPDATE wc_posts
SET post_excerpt = REPLACE(post_excerpt, 'billing@learnaicourses.local', 'fenllinskiii16@gmail.com')
WHERE post_excerpt LIKE '%billing@learnaicourses.local%';

UPDATE wc_posts
SET post_excerpt = REPLACE(post_excerpt, 'billing@learnaicourses.com', 'fenllinskiii16@gmail.com')
WHERE post_excerpt LIKE '%billing@learnaicourses.com%';

UPDATE wc_postmeta
SET meta_value = REPLACE(meta_value, 'billing@learnaicourses.local', 'fenllinskiii16@gmail.com')
WHERE meta_value LIKE '%billing@learnaicourses.local%';

UPDATE wc_postmeta
SET meta_value = REPLACE(meta_value, 'billing@learnaicourses.com', 'fenllinskiii16@gmail.com')
WHERE meta_value LIKE '%billing@learnaicourses.com%';

UPDATE wc_postmeta
SET meta_value = REPLACE(meta_value, 'legal@learnaicourses.local', 'fenllinskiii16@gmail.com')
WHERE meta_value LIKE '%legal@learnaicourses.local%';

UPDATE wc_postmeta
SET meta_value = REPLACE(meta_value, 'legal@learnaicourses.com', 'fenllinskiii16@gmail.com')
WHERE meta_value LIKE '%legal@learnaicourses.com%';

UPDATE wc_postmeta
SET meta_value = REPLACE(meta_value, 'support@learnaicourses.local', 'fenllinskiii16@gmail.com')
WHERE meta_value LIKE '%support@learnaicourses.local%';

UPDATE wc_postmeta
SET meta_value = REPLACE(meta_value, 'support@learnaicourses.com', 'fenllinskiii16@gmail.com')
WHERE meta_value LIKE '%support@learnaicourses.com%';

UPDATE wc_options
SET option_value = REPLACE(option_value, 'billing@learnaicourses.local', 'fenllinskiii16@gmail.com')
WHERE option_value LIKE '%billing@learnaicourses.local%';

UPDATE wc_options
SET option_value = REPLACE(option_value, 'billing@learnaicourses.com', 'fenllinskiii16@gmail.com')
WHERE option_value LIKE '%billing@learnaicourses.com%';

-- Verify:
-- SELECT ID, post_title FROM wc_posts WHERE post_content LIKE '%learnaicourses.local%' OR post_content LIKE '%billing@learnaicourses.com%';
-- SELECT ID, post_title FROM wc_posts WHERE post_content LIKE '%fenllinskiii16@gmail.com%';
