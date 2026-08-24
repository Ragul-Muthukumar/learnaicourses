<?php
/**
 * Widget Social Links
 *
 * @package ai_engine
 */

// register AI_Engine_Social_Links widget 
function ai_engine_register_social_links_widget() {
    register_widget( 'AI_Engine_Social_Links' );
}
add_action( 'widgets_init', 'ai_engine_register_social_links_widget' );

if( ! class_exists( 'AI_Engine_Social_Links' ) ): 
 /**
 * Adds AI_Engine_Social_Links widget.
 */
class AI_Engine_Social_Links extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'ai_engine_social_links', // Base ID
			esc_html__( 'TI: Social Links', 'ai-engine' ), // Name
			array( 'description' => esc_html__( 'A Social Links Widget', 'ai-engine' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	   
        $ai_engine_title      = ! empty( $instance['title'] ) ? $instance['title'] : '';		
        $ai_engine_facebook   = ! empty( $instance['facebook'] ) ? $instance['facebook'] : '' ;
        $ai_engine_instagram  = ! empty( $instance['instagram'] ) ? $instance['instagram'] : '' ;
        $ai_engine_twitter    = ! empty( $instance['twitter'] ) ? $instance['twitter'] : '' ;
        $ai_engine_pinterest  = ! empty( $instance['pinterest'] ) ? $instance['pinterest'] : '' ;
        $ai_engine_linkedin   = ! empty( $instance['linkedin'] ) ? $instance['linkedin'] : '' ;
        $ai_engine_youtube    = ! empty( $instance['youtube'] ) ? $instance['youtube'] : '' ;
        $ai_engine_tiktok    = ! empty( $instance['tiktok'] ) ? $instance['tiktok'] : '' ;
        
        if( $ai_engine_facebook || $ai_engine_instagram || $ai_engine_twitter || $ai_engine_pinterest || $ai_engine_linkedin || $ai_engine_youtube || $ai_engine_tiktok ){ 
        echo $args['before_widget'];
        if($ai_engine_title)
        echo $args['before_title'] . apply_filters( 'widget_title', $ai_engine_title, $instance, $this->id_base ) . $args['after_title'];
        ?>
            <ul class="social-networks">
				<?php if( $ai_engine_facebook ){ ?>
                <li><a href="<?php echo esc_url( $ai_engine_facebook ); ?>" title="<?php esc_attr_e( 'Facebook', 'ai-engine' ); ?>" ><i class="fa fa-facebook"></i></a></li>
				<?php } if( $ai_engine_instagram ){ ?>
                <li><a href="<?php echo esc_url( $ai_engine_instagram ); ?>" title="<?php esc_attr_e( 'Instagram', 'ai-engine' ); ?>"><i class="fa fa-instagram"></i></a></li>
                <?php } if( $ai_engine_twitter ){ ?>
                <li><a href="<?php echo esc_url( $ai_engine_twitter ); ?>" title="<?php esc_attr_e( 'Twitter', 'ai-engine' ); ?>"><i class="fa fa-twitter"></i></a></li>
				<?php } if( $ai_engine_pinterest ){ ?>
                <li><a href="<?php echo esc_url( $ai_engine_pinterest ); ?>"  title="<?php esc_attr_e( 'Pinterest', 'ai-engine' ); ?>"><i class="fa fa-pinterest-p"></i></a></li>
				<?php } if( $ai_engine_linkedin ){ ?>
                <li><a href="<?php echo esc_url( $ai_engine_linkedin ); ?>" title="<?php esc_attr_e( 'Linkedin', 'ai-engine' ); ?>"><i class="fa fa-linkedin"></i></a></li>
				<?php } if( $ai_engine_youtube ){ ?>
                <li><a href="<?php echo esc_url( $ai_engine_youtube ); ?>" title="<?php esc_attr_e( 'YouTube', 'ai-engine' ); ?>"><i class="fa fa-youtube"></i></a></li>
                <?php } if( $ai_engine_tiktok ){ ?>
                <li><a href="<?php echo esc_url( $ai_engine_tiktok ); ?>" title="<?php esc_attr_e( 'Tiktok', 'ai-engine' ); ?>"><i class="fab fa-tiktok"></i></a></li>
                <?php } ?>
			</ul>
        <?php
        echo $args['after_widget'];
        }
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
        
        $ai_engine_title      = ! empty( $instance['title'] ) ? $instance['title'] : '';		
        $ai_engine_facebook   = ! empty( $instance['facebook'] ) ? $instance['facebook'] : '' ;
        $ai_engine_instagram  = ! empty( $instance['instagram'] ) ? $instance['instagram'] : '' ;
        $ai_engine_twitter    = ! empty( $instance['twitter'] ) ? $instance['twitter'] : '' ;
        $ai_engine_pinterest  = ! empty( $instance['pinterest'] ) ? $instance['pinterest'] : '' ;
        $ai_engine_linkedin   = ! empty( $instance['linkedin'] ) ? $instance['linkedin'] : '' ;
        $ai_engine_youtube    = ! empty( $instance['youtube'] ) ? $instance['youtube'] : '' ;
        $ai_engine_tiktok    = ! empty( $instance['tiktok'] ) ? $instance['tiktok'] : '' ;
        
        ?>
		
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'ai-engine' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $ai_engine_title ); ?>" />
		</p>
        
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>"><?php esc_html_e( 'Facebook', 'ai-engine' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'facebook' ) ); ?>" type="text" value="<?php echo esc_attr( $ai_engine_facebook ); ?>" />
		</p>
        
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>"><?php esc_html_e( 'Instagram', 'ai-engine' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'instagram' ) ); ?>" type="text" value="<?php echo esc_attr( $ai_engine_instagram ); ?>" />
		</p>
                
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>"><?php esc_html_e( 'Twitter', 'ai-engine' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter' ) ); ?>" type="text" value="<?php echo esc_attr( $ai_engine_twitter ); ?>" />
		</p>
        
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>"><?php esc_html_e( 'Pinterest', 'ai-engine' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pinterest' ) ); ?>" type="text" value="<?php echo esc_attr( $ai_engine_pinterest ); ?>" />
		</p>
        
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>"><?php esc_html_e( 'LinkedIn', 'ai-engine' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linkedin' ) ); ?>" type="text" value="<?php echo esc_attr( $ai_engine_linkedin ); ?>" />
		</p>
        
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>"><?php esc_html_e( 'YouTube', 'ai-engine' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'youtube' ) ); ?>" type="text" value="<?php echo esc_attr( $ai_engine_youtube ); ?>" />
		</p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'tiktok' ) ); ?>"><?php esc_html_e( 'Tiktok', 'ai-engine' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tiktok' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tiktok' ) ); ?>" type="text" value="<?php echo esc_attr( $ai_engine_tiktok ); ?>" />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		
        $instance = array();
		
        $instance['title']     = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) :'';
        $instance['facebook']  = ! empty( $new_instance['facebook'] ) ? esc_url_raw( $new_instance['facebook'] ) : '';
        $instance['instagram'] = ! empty( $new_instance['instagram'] ) ? esc_url_raw( $new_instance['instagram'] ) : '';
        $instance['twitter']   = ! empty( $new_instance['twitter'] ) ? esc_url_raw( $new_instance['twitter'] ) : '';
        $instance['pinterest'] = ! empty( $new_instance['pinterest'] ) ? esc_url_raw( $new_instance['pinterest'] ) : '';
        $instance['linkedin']  = ! empty( $new_instance['linkedin'] ) ? esc_url_raw( $new_instance['linkedin'] ) : '';
        $instance['youtube']   = ! empty( $new_instance['youtube'] ) ? esc_url_raw( $new_instance['youtube'] ) : '';
        $instance['tiktok']    = ! empty( $new_instance['tiktok'] ) ? esc_url_raw( $new_instance['tiktok'] ) : '';
		
        return $instance;
                
	}

} // class AI_Engine_Social_Links 
endif;