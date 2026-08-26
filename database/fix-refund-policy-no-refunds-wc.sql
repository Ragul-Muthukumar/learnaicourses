-- Set Refund Policy: digital purchases are final — no refunds (Hostinger wc_ prefix)
-- Run in phpMyAdmin on database u651014097_u4vym

UPDATE wc_posts
SET post_content = '<!-- wp:heading -->
<h2 class="wp-block-heading">Refund Policy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">All products on Learn AI Courses are <strong>digital purchases</strong> (online courses and related digital content). <strong>All sales are final. There are no refunds.</strong></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">1. Digital products — no refunds</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">When you complete a purchase, you receive immediate access to digital course materials. Because this is a digital product and access is delivered right away, we do not offer refunds, returns, exchanges, or cancellations after payment—including if you bought the wrong course or changed your mind.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">2. Before you buy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">Please review the course title, description, curriculum, and price carefully before checkout. If you have any questions, contact us <strong>before</strong> purchasing.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">3. Access problems</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">If you paid but cannot access your digital course because of a technical issue on our side, email <a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a> or use our <a href="/contact/">Contact</a> page. We will help restore your access. Fixing access is not a refund.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">4. Free courses</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">Free enrollments have no purchase amount, so no refund applies.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">5. Contact</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">For billing or access help, visit <a href="/contact/">Contact</a> or email <a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a>.</p>
<!-- /wp:paragraph -->',
    post_modified = NOW(),
    post_modified_gmt = UTC_TIMESTAMP()
WHERE post_name = 'refund-policy'
  AND post_type = 'page';

-- Align Terms wording with digital no-refund policy
UPDATE wc_posts
SET post_content = REPLACE(
  post_content,
  'Refund Policy</a> for refund rules.',
  'Refund Policy</a> (digital purchases are final; no refunds).'
)
WHERE post_content LIKE '%Refund Policy</a> for refund rules.%';

UPDATE wc_posts
SET post_content = REPLACE(
  post_content,
  'Refund Policy</a> (all sales are final; no refunds).',
  'Refund Policy</a> (digital purchases are final; no refunds).'
)
WHERE post_content LIKE '%Refund Policy</a> (all sales are final; no refunds).%';

-- Verify:
-- SELECT ID, post_title, LEFT(post_content, 280) FROM wc_posts WHERE post_name = 'refund-policy';
