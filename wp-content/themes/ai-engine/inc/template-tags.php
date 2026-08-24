<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package ai_engine
 */

if ( ! function_exists( 'ai_engine_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function ai_engine_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$ai_engine_posted_on = '<a href="' . esc_url( get_day_link( get_the_time('Y'), get_the_time('m'), get_the_time('d') ) ) . '">' . $time_string . '</a>';

	$byline = '<span class="author vcard" itemprop="author"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>';
    
	echo '<span class="posted-on">' . $ai_engine_posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function ai_engine_categorized_blog() {
	if ( false === ( $ai_engine_all_the_cool_cats = get_transient( 'ai_engine_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$ai_engine_all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$ai_engine_all_the_cool_cats = count( $ai_engine_all_the_cool_cats );

		set_transient( 'ai_engine_categories', $ai_engine_all_the_cool_cats );
	}

	if ( $ai_engine_all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so ai_engine_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so ai_engine_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in ai_engine_categorized_blog.
 */
function ai_engine_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'ai_engine_categories' );
}
add_action( 'edit_category', 'ai_engine_category_transient_flusher' );
add_action( 'save_post',     'ai_engine_category_transient_flusher' );


if ( ! function_exists( 'ai_engine_category_list' ) ) :
/**
 * Prints Categories lists
*/
function ai_engine_category_list(){
    // Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$ai_engine_categories_list = get_the_category_list( esc_html__( ', ', 'ai-engine' ) );
		if ( $ai_engine_categories_list && ai_engine_categorized_blog() ) {
			echo '<span class="category">' . $ai_engine_categories_list . '</span>';
		}
	}
}
endif;