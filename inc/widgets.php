<?php
/**
 * Custom Widgets for EBOH Theme
 *
 * @package EBOH
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EBOH Upcoming Matches Widget
 */
class EBOH_Upcoming_Matches_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'eboh_upcoming_matches',
			__( 'EBOH: Upcoming Matches', 'eboh' ),
			array( 'description' => __( 'Display upcoming matches for EBOH', 'eboh' ) )
		);
	}

	public function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );
		?>
		<h3 class="widget-title">
			<?php
			if ( ! empty( $instance['title'] ) ) {
				echo wp_kses_post( apply_filters( 'widget_title', $instance['title'] ) );
			} else {
				esc_html_e( 'Komende Wedstrijden', 'eboh' );
			}
			?>
		</h3>

		<div class="widget-upcoming-matches">
			<div class="match-item">
				<strong class="match-date"><?php esc_html_e( 'Zaterdag 19 maart', 'eboh' ); ?></strong><br>
				<?php esc_html_e( 'vv EBOH U17 vs SC Cambuur', 'eboh' ); ?><br>
				<small class="match-time"><?php esc_html_e( '15:00 uur', 'eboh' ); ?></small>
			</div>
			<div class="match-item">
				<strong class="match-date"><?php esc_html_e( 'Zaterdag 19 maart', 'eboh' ); ?></strong><br>
				<?php esc_html_e( 'Dordrecht City vs vv EBOH', 'eboh' ); ?><br>
				<small class="match-time"><?php esc_html_e( '16:30 uur', 'eboh' ); ?></small>
			</div>
		</div>
		<?php
		echo wp_kses_post( $args['after_widget'] );
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Komende Wedstrijden', 'eboh' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_attr_e( 'Title:', 'eboh' ); ?>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
	}
}

/**
 * EBOH Sponsors Widget
 */
class EBOH_Sponsors_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'eboh_sponsors',
			__( 'EBOH: Sponsors', 'eboh' ),
			array( 'description' => __( 'Display EBOH sponsors', 'eboh' ) )
		);
	}

	public function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );
		?>
		<h3 class="widget-title">
			<?php
			if ( ! empty( $instance['title'] ) ) {
				echo wp_kses_post( apply_filters( 'widget_title', $instance['title'] ) );
			} else {
				esc_html_e( 'Onze Sponsors', 'eboh' );
			}
			?>
		</h3>

		<div class="widget-sponsors">
			<div class="sponsor-item-widget">
				<small><?php esc_html_e( 'Sponsor', 'eboh' ); ?></small>
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/sponsor-placeholder.png' ); ?>"
					 alt="<?php esc_attr_e( 'Sponsor', 'eboh' ); ?>">
			</div>
		</div>
		<?php
		echo wp_kses_post( $args['after_widget'] );
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Onze Sponsors', 'eboh' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_attr_e( 'Title:', 'eboh' ); ?>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
	}
}

/**
 * Register Widgets
 */
function eboh_register_widgets() {
	register_widget( 'EBOH_Upcoming_Matches_Widget' );
	register_widget( 'EBOH_Sponsors_Widget' );
}

add_action( 'widgets_init', 'eboh_register_widgets' );
