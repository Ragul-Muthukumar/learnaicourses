-- Remove duplicate Legal & privacy email on Contact page (Hostinger wc_ prefix)

UPDATE wc_posts
SET post_content = REPLACE(
  post_content,
  '<a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a><br><a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a>',
  '<a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a>'
)
WHERE post_content LIKE '%Legal%privacy%'
  AND post_content LIKE '%fenllinskiii16@gmail.com</a><br><a href="mailto:fenllinskiii16@gmail.com"%';

UPDATE wc_posts
SET post_content = REPLACE(
  post_content,
  '<a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a><br><a href="mailto:privacy@fenllinskiii16@gmail.com">privacy@fenllinskiii16@gmail.com</a>',
  '<a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a>'
)
WHERE post_content LIKE '%privacy@fenllinskiii16@gmail.com%';

UPDATE wc_posts
SET post_content = REPLACE(post_content, 'privacy@fenllinskiii16@gmail.com', 'fenllinskiii16@gmail.com')
WHERE post_content LIKE '%privacy@fenllinskiii16@gmail.com%';
