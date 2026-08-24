<?php
/**
 * Displays footer widgets if assigned
 *
 * @package Elearning Education
 * @subpackage elearning_education
 */
?>
<?php

// Determine the number of columns dynamically for the footer (you can replace this with your logic).
$elearning_education_no_of_footer_col = get_theme_mod('elearning_education_footer_columns', 4); // Change this value as needed.

// Calculate the Bootstrap class for large screens (col-lg-X) for footer.
$elearning_education_col_lg_footer_class = 'col-lg-' . (12 / $elearning_education_no_of_footer_col);

// Calculate the Bootstrap class for medium screens (col-md-X) for footer.
$elearning_education_col_md_footer_class = 'col-md-' . (12 / $elearning_education_no_of_footer_col);
?>
<div class="container">
    <aside class="widget-area row py-2 pt-3" role="complementary" aria-label="<?php esc_attr_e( 'Footer', 'elearning-education' ); ?>">
        <?php
        $elearning_education_default_widgets = array(
            1 => 'search',
            2 => 'archives',
            3 => 'meta',
            4 => 'categories'
        );

        for ($elearning_education_i = 1; $elearning_education_i <= $elearning_education_no_of_footer_col; $elearning_education_i++) :
            $elearning_education_lg_class = esc_attr($elearning_education_col_lg_footer_class);
            $elearning_education_md_class = esc_attr($elearning_education_col_md_footer_class);
            echo '<div class="col-12 ' . $elearning_education_lg_class . ' ' . $elearning_education_md_class . '">';

            if (is_active_sidebar('footer-' . $elearning_education_i)) {
                dynamic_sidebar('footer-' . $elearning_education_i);
            } else {
                // Display default widget content if not active.
                switch ($elearning_education_default_widgets[$elearning_education_i] ?? '') {
                    case 'search':
                        ?>
                        <aside class="widget" role="complementary" aria-label="<?php esc_attr_e('Search', 'elearning-education'); ?>">
                            <h3 class="widget-title"><?php esc_html_e('Search', 'elearning-education'); ?></h3>
                            <?php get_search_form(); ?>
                        </aside>
                        <?php
                        break;

                    case 'archives':
                        ?>
                        <aside class="widget" role="complementary" aria-label="<?php esc_attr_e('Archives', 'elearning-education'); ?>">
                            <h3 class="widget-title"><?php esc_html_e('Archives', 'elearning-education'); ?></h3>
                            <ul><?php wp_get_archives(['type' => 'monthly']); ?></ul>
                        </aside>
                        <?php
                        break;

                    case 'meta':
                        ?>
                        <aside class="widget" role="complementary" aria-label="<?php esc_attr_e('Meta', 'elearning-education'); ?>">
                            <h3 class="widget-title"><?php esc_html_e('Meta', 'elearning-education'); ?></h3>
                            <ul>
                                <?php wp_register(); ?>
                                <li><?php wp_loginout(); ?></li>
                                <?php wp_meta(); ?>
                            </ul>
                        </aside>
                        <?php
                        break;

                    case 'categories':
                        ?>
                        <aside class="widget" role="complementary" aria-label="<?php esc_attr_e('Categories', 'elearning-education'); ?>">
                            <h3 class="widget-title"><?php esc_html_e('Categories', 'elearning-education'); ?></h3>
                            <ul><?php wp_list_categories(['title_li' => '']); ?></ul>
                        </aside>
                        <?php
                        break;
                }
            }

            echo '</div>';
        endfor;
        ?>
    </aside>
</div>