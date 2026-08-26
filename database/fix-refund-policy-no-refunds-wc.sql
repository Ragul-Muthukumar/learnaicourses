-- OPTIONAL: manual Refund Policy update (Hostinger wc_ prefix)
-- Prefer deploying lac-lms plugin v1.4.4+ which auto-syncs this page on first load.
-- Run only if the page still shows the old 7-day refund text.

UPDATE wc_posts
SET post_content = '<!-- wp:heading -->
<h2 class="wp-block-heading">Refund Policy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">All products on Learn AI Courses are <strong>digital purchases</strong> only (online courses delivered instantly as digital content — not physical goods). <strong>All sales are final. There are no refunds.</strong></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">1. Digital purchase — no refunds</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">When you pay, you receive immediate online access to digital course materials. Because this is a digital product and access starts right away, we do not offer refunds, returns, exchanges, or cancellations after payment—including mistaken purchases or change of mind.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">2. Before you buy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">Please review the course title, description, curriculum, and price carefully before checkout. If you have questions, email <a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a> <strong>before</strong> purchasing.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">3. Access problems</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">If you paid but cannot open your digital course because of a technical issue on our side, email <a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a>. We will help restore access. Access support is not a refund.</p>
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
<p class="wp-block-paragraph">For billing or access help, email <a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a>.</p>
<!-- /wp:paragraph -->',
    post_modified = NOW(),
    post_modified_gmt = UTC_TIMESTAMP()
WHERE post_name = 'refund-policy'
  AND post_type = 'page';

INSERT INTO wc_options (option_name, option_value, autoload)
VALUES ('lac_refund_policy_digital_v2', '1', 'on')
ON DUPLICATE KEY UPDATE option_value = '1';
