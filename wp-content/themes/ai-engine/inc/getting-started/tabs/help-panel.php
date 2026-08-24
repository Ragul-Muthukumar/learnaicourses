<?php
/**
 * Help Panel.
 *
 * @package ai_engine
 */

$ai_engine_import_done = get_option( 'ai_engine_demo_import_done' );
$ai_engine_button_text = $ai_engine_import_done
	? __( 'View Site', 'ai-engine' )
	: __( 'Start Demo Import', 'ai-engine' );
$ai_engine_button_link = $ai_engine_import_done
	? home_url( '/' )
	: admin_url( 'themes.php?page=aiengine-wizard' );
?>
<div id="help-panel" class="panel-left visible">
    <div class="panel-aside active">
        <div class="demo-content">
            <div class="demo-info">
                <h4><?php esc_html_e( 'DEMO CONTENT IMPORTER', 'ai-engine' ); ?></h4>
                <p><?php esc_html_e('The Demo Content Importer helps you quickly set up your website to look exactly like the theme demo. Instead of building pages from scratch, you can import pre-designed layouts, pages, menus, images, and basic settings in just a few clicks.','ai-engine'); ?></p>
                <a class="button button-primary first-color" style="text-transform: capitalize" href="<?php echo esc_url( $ai_engine_button_link ); ?>" title="<?php echo esc_attr( $ai_engine_button_text ); ?>"
                    <?php echo $ai_engine_import_done ? 'target="_blank"' : ''; ?>>
                    <?php echo esc_html( $ai_engine_button_text ); ?>
                </a>
            </div>
            <div class="demo-img">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri()) .'/screenshot.png'; ?>" alt="<?php echo esc_attr( 'screenshot', 'ai-engine'); ?>"/>
            </div>
        </div>
    </div>

    <div class="panel-aside" >
        <h4><?php esc_html_e( 'USEFUL LINKS', 'ai-engine' ); ?></h4>
        <p><?php esc_html_e( 'Find everything you need to set up, customize, and manage your website with ease. These helpful resources are designed to guide you at every step, from installation to advanced customization.', 'ai-engine' ); ?></p>
        <div class="useful-links">
            <a class="button button-primary second-color" href="<?php echo esc_url( AI_ENGINE_DEMO_URL ); ?>" title="<?php esc_attr_e( 'Live Demo', 'ai-engine' ); ?>" target="_blank">
                <?php esc_html_e( 'Live Demo', 'ai-engine' ); ?>
            </a>
            <a class="button button-primary first-color" href="<?php echo esc_url( AI_ENGINE_FREE_DOC_URL ); ?>" title="<?php esc_attr_e( 'Documentation', 'ai-engine' ); ?>" target="_blank">
                <?php esc_html_e( 'Documentation', 'ai-engine' ); ?>
            </a>
            <a class="button button-primary second-color" href="<?php echo esc_url( AI_ENGINE_URL ); ?>" title="<?php esc_attr_e( 'Get Premium', 'ai-engine' ); ?>" target="_blank">
                <?php esc_html_e( 'Get Premium', 'ai-engine' ); ?>
            </a>
            <a class="button button-primary first-color" href="<?php echo esc_url( AI_ENGINE_BUNDLE_URL ); ?>" title="<?php esc_attr_e( 'Get Bundle - 60+ Themes', 'ai-engine' ); ?>" target="_blank">
                <?php esc_html_e( 'Get Bundle - 60+ Themes', 'ai-engine' ); ?>
            </a>
        </div>
    </div>

    <div class="panel-aside" >
        <h4><?php esc_html_e( 'REVIEW', 'ai-engine' ); ?></h4>
        <p><?php esc_html_e( 'If you have a moment, please consider leaving a rating and short review. It only takes a minute, and your support means a lot to us.', 'ai-engine' ); ?></p>
        <a class="button button-primary first-color" href="<?php echo esc_url( AI_ENGINE_REVIEW_URL ); ?>" title="<?php esc_attr_e( 'Visit the Review', 'ai-engine' ); ?>" target="_blank">
            <?php esc_html_e( 'Leave a Review', 'ai-engine' ); ?>
        </a>
    </div>
    
    <div class="panel-aside">
        <h4><?php esc_html_e( 'CONTACT SUPPORT', 'ai-engine' ); ?></h4>
        <p>
            <?php esc_html_e( 'Thank you for choosing AI Engine! We appreciate your interest in our theme and are here to assist you with any support you may need.', 'ai-engine' ); ?></p>
        <a class="button button-primary first-color" href="<?php echo esc_url( AI_ENGINE_SUPPORT_URL ); ?>" title="<?php esc_attr_e( 'Visit the Support', 'ai-engine' ); ?>" target="_blank">
            <?php esc_html_e( 'Contact Support', 'ai-engine' ); ?>
        </a>
    </div>
</div>