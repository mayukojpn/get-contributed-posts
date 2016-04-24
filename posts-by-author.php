<?php
/**
 * Plugin Name: Posts by Author
 * Version: 0.1-alpha
 * Description: Show post list of specific author from any WordPress site by using WP REST API.
 * Author: Mayo Moriyama
 * Author URI: http://mayuko.me
 * Text Domain: posts-by-author
 * Domain Path: /languages
 * @package posts-by-author
 */

 class Posts_By_Author_Widget extends WP_Widget {
 	/**
 	 * Sets up a new Posts by Author widget instance.
 	 *
 	 * @since 2.8.0
 	 * @access public
 	 */
 	public function __construct() {
 		$widget_ops = array('classname' => 'widget-posts-by-author', 'description' => __( "Recent posts by author.") );
 		parent::__construct('author-posts', __('Posts by Author'), $widget_ops);
 		$this->alt_option_name = 'widget-posts-by-author';
 	}
 	/**
 	 * Outputs the content for the current Posts by Author widget instance.
 	 *
 	 * @since 2.8.0
 	 * @access public
 	 *
 	 * @param array $args     Display arguments including 'before_title', 'after_title',
 	 *                        'before_widget', and 'after_widget'.
 	 * @param array $instance Settings for the current Posts by Author widget instance.
 	 */
 	public function widget( $args, $instance ) {
 		if ( ! isset( $args['widget_id'] ) ) {
 			$args['widget_id'] = $this->id;
 		}
 		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
 		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
 		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    $site_url = ( ! empty( $instance['site_url'] ) ) ? $instance['site_url'] : '';
    $end_point = ( ! empty( $instance['end_point'] ) ) ? $instance['end_point'] : 'wp-json/wp/v2';
    $author_id = ( ! empty( $instance['author_id'] ) ) ? absint( $instance['author_id'] ) : 1;
 		if ( ! $author_id )
 			$author_id = 1;
 		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
 		if ( ! $number )
 			$number = 5;
 		$version_one = isset( $instance['version_one'] ) ? $instance['version_one'] : false;
 		/**
 		 * Filter the arguments for the Posts by Author widget.
 		 *
 		 * @since 3.4.0
 		 *
 		 * @see WP_Query::get_posts()
 		 *
 		 * @param array $args An array of arguments used to retrieve the recent posts.
 		 */
     /*
 		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
 			'posts_per_page'      => $number,
      'author'              => $author_id,
 			'no_found_rows'       => true,
 			'post_status'         => 'publish',
 			'ignore_sticky_posts' => true
 		) ) );
 		if ($r->have_posts()) :
    */
 		?>
 		<?php echo $args['before_widget']; ?>
 		<?php if ( $title ) {
 			echo $args['before_title'] . $title . $args['after_title'];
 		} ?>
    <div id="wpapi"></div>

 		<?php
    /*
    ?>
 		<ul>
 		<?php
    while ( $r->have_posts() ) : $r->the_post(); ?>
 			<li>
 				<a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
 			<?php if ( $show_date ) : ?>
 				<span class="post-date"><?php echo get_the_date(); ?></span>
 			<?php endif; ?>
 			</li>
 		<?php endwhile;
    ?>
 		</ul>
 		<?php
    */
    echo $args['after_widget']; ?>
    <script type="text/javascript">
    jQuery(function($) {
      <?php echo "var url = '".$site_url."/wp-json/wp/v2/';"; ?>
      $.ajax({
        url: url + 'posts',
        type:'GET',
        dataType: 'json',
        data : {
          filter: {
            author: <?php echo $author_id; ?>,
            posts_per_page: <?php echo $number; ?>
          }
        },
        timeout:10000,
      }).done(function(datas) {

        var ul = $('<ul></ul>');

        for (var i = 0; i < datas.length; i++) {
          var post_title = datas[i]['title']['rendered'],
              post_url  = datas[i]['link'],
              post_time = new Date(datas[i]['date']);
          var post_link = $('<a>' + post_title + '</a>').attr('href',post_url);
          var post_list = $('<li></li>')
              .append($('<time>'+post_time.getFullYear()+'年'+post_time.getMonth()+'月'+post_time.getDate()+'日</time>'))
              .append(post_link);
          $(ul).append(post_list);
        }
        $('#wpapi').append(ul);

      }).fail(function(datas) {
        $('#wpapi').append('');
      });

    });
    </script>
 		<?php
    /*
 		// Reset the global $the_post as this query will have stomped on it
 		wp_reset_postdata();
 		endif;
    */
 	}
 	/**
 	 * Handles updating the settings for the current Posts by Author widget instance.
 	 *
 	 * @since 2.8.0
 	 * @access public
 	 *
 	 * @param array $new_instance New settings for this instance as input by the user via
 	 *                            WP_Widget::form().
 	 * @param array $old_instance Old settings for this instance.
 	 * @return array Updated settings to save.
 	 */
 	public function update( $new_instance, $old_instance ) {
 		$instance = $old_instance;
    $instance['title'] = sanitize_text_field( $new_instance['title'] );
    $instance['site_url'] = sanitize_text_field( $new_instance['site_url'] );
    $instance['end_point'] = sanitize_text_field( $new_instance['end_point'] );
    $instance['author_id'] = (int) $new_instance['author_id'];
 		$instance['number'] = (int) $new_instance['number'];
 		$instance['version_one'] = isset( $new_instance['version_one'] ) ? (bool) $new_instance['version_one'] : false;
 		return $instance;
 	}
 	/**
 	 * Outputs the settings form for the Posts by Author widget.
 	 *
 	 * @since 2.8.0
 	 * @access public
 	 *
 	 * @param array $instance Current settings.
 	 */
 	public function form( $instance ) {
    $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
    $site_url  = isset( $instance['site_url'] ) ? esc_attr( $instance['site_url'] ) : '';
 		$end_point = isset( $instance['end_point'] ) ? esc_attr( $instance['end_point'] ) : 'wp-json';
    $author_id = isset( $instance['author_id'] ) ? absint( $instance['author_id'] ) : 1;
 		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
 		$version_one = isset( $instance['version_one'] ) ? (bool) $instance['version_one'] : false;
 ?>
 		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
 		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

    <p><label for="<?php echo $this->get_field_id( 'end_point' ); ?>"><?php _e( 'Site URL:' ); ?></label>
 		<input class="widefat" id="<?php echo $this->get_field_id( 'end_point' ); ?>" name="<?php echo $this->get_field_name( 'end_point' ); ?>" type="text" value="<?php echo $end_point; ?>" /></p>

    <p><label for="<?php echo $this->get_field_id( 'site_url' ); ?>"><?php _e( 'Site URL:' ); ?></label>
 		<input class="widefat" id="<?php echo $this->get_field_id( 'site_url' ); ?>" name="<?php echo $this->get_field_name( 'site_url' ); ?>" type="text" value="<?php echo $site_url; ?>" /></p>

    <p><label for="<?php echo $this->get_field_id( 'author_id' ); ?>"><?php _e( 'Author ID:' ); ?></label>
 		<input class="widefat" id="<?php echo $this->get_field_id( 'author_id' ); ?>" name="<?php echo $this->get_field_name( 'author_id' ); ?>" type="number" step="1" min="1" value="<?php echo $author_id; ?>" /></p>

 		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
 		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

 		<p><input class="checkbox" type="checkbox"<?php checked( $version_one ); ?> id="<?php echo $this->get_field_id( 'version_one' ); ?>" name="<?php echo $this->get_field_name( 'version_one' ); ?>" />
 		<label for="<?php echo $this->get_field_id( 'version_one' ); ?>"><?php _e( 'Check if you use wp-api v1.' ); ?></label></p>
 <?php
 	}
 }
 add_action( 'widgets_init', create_function( '', 'return register_widget( "Posts_By_Author_Widget" );' ) );
