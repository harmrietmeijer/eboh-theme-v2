<?php
/**
 * Custom Post Types Registration
 *
 * @package EBOH
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Team Custom Post Type
 */
function eboh_register_team_cpt() {
	$labels = array(
		'name'                  => _x( 'Teams', 'Post Type General Name', 'eboh' ),
		'singular_name'         => _x( 'Team', 'Post Type Singular Name', 'eboh' ),
		'menu_name'             => __( 'Teams', 'eboh' ),
		'name_admin_bar'        => __( 'Team', 'eboh' ),
		'archives'              => __( 'Team Archives', 'eboh' ),
		'attributes'            => __( 'Team Attributes', 'eboh' ),
		'parent_item_colon'     => __( 'Parent Team:', 'eboh' ),
		'all_items'             => __( 'All Teams', 'eboh' ),
		'add_new_item'          => __( 'Add New Team', 'eboh' ),
		'add_new'               => __( 'Add New', 'eboh' ),
		'new_item'              => __( 'New Team', 'eboh' ),
		'edit_item'             => __( 'Edit Team', 'eboh' ),
		'update_item'           => __( 'Update Team', 'eboh' ),
		'view_item'             => __( 'View Team', 'eboh' ),
		'view_items'            => __( 'View Teams', 'eboh' ),
		'search_items'          => __( 'Search Team', 'eboh' ),
		'not_found'             => __( 'Not found', 'eboh' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'eboh' ),
		'featured_image'        => __( 'Featured Image', 'eboh' ),
		'set_featured_image'    => __( 'Set featured image', 'eboh' ),
		'remove_featured_image' => __( 'Remove featured image', 'eboh' ),
		'use_featured_image'    => __( 'Use as featured image', 'eboh' ),
		'insert_into_item'      => __( 'Insert into team', 'eboh' ),
		'uploaded_to_this_item' => __( 'Uploaded to this team', 'eboh' ),
		'items_list'            => __( 'Teams list', 'eboh' ),
		'items_list_navigation' => __( 'Teams list navigation', 'eboh' ),
		'filter_items_list'     => __( 'Filter teams list', 'eboh' ),
	);

	$args = array(
		'label'                 => __( 'Team', 'eboh' ),
		'description'           => __( 'Post Type for Teams', 'eboh' ),
		'labels'                => $labels,
		'supports'              => array(
			'title',
			'editor',
			'thumbnail',
			'excerpt',
			'author',
			'custom-fields',
		),
		'taxonomies'            => array( 'team_category' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-groups',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => 'teams',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'               => array(
			'slug'       => 'team',
			'with_front' => true,
		),
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'rest_base'             => 'teams',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	);

	register_post_type( 'team', $args );
}

add_action( 'init', 'eboh_register_team_cpt', 0 );

/**
 * Register Team Category Taxonomy
 */
function eboh_register_team_category() {
	$labels = array(
		'name'              => _x( 'Team Categories', 'Taxonomy General Name', 'eboh' ),
		'singular_name'     => _x( 'Team Category', 'Taxonomy Singular Name', 'eboh' ),
		'menu_name'         => __( 'Team Categories', 'eboh' ),
		'all_items'         => __( 'All Team Categories', 'eboh' ),
		'parent_item'       => __( 'Parent Team Category', 'eboh' ),
		'parent_item_colon' => __( 'Parent Team Category:', 'eboh' ),
		'new_item_name'     => __( 'New Team Category Name', 'eboh' ),
		'add_new_item'      => __( 'Add New Team Category', 'eboh' ),
		'edit_item'         => __( 'Edit Team Category', 'eboh' ),
		'update_item'       => __( 'Update Team Category', 'eboh' ),
		'view_item'         => __( 'View Team Category', 'eboh' ),
		'separate_items_with_commas' => __( 'Separate team categories with commas', 'eboh' ),
		'add_or_remove_items' => __( 'Add or remove team categories', 'eboh' ),
		'choose_from_most_used' => __( 'Choose from the most used team categories', 'eboh' ),
		'popular_items'     => __( 'Popular Team Categories', 'eboh' ),
		'search_items'      => __( 'Search Team Categories', 'eboh' ),
		'not_found'         => __( 'Team Category Not found', 'eboh' ),
		'not_found_in_trash' => __( 'Team Category not found in Trash', 'eboh' ),
		'no_terms'          => __( 'No team categories', 'eboh' ),
		'items_list_navigation' => __( 'Team Categories list navigation', 'eboh' ),
		'items_list'        => __( 'Team Categories list', 'eboh' ),
		'back_to_items'     => __( 'Back to Team Categories', 'eboh' ),
	);

	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest'               => true,
		'rest_base'                  => 'team_categories',
		'rest_controller_class'      => 'WP_REST_Terms_Controller',
		'rewrite'                    => array(
			'slug'         => 'team-category',
			'with_front'   => true,
			'hierarchical' => false,
		),
	);

	register_taxonomy( 'team_category', array( 'team' ), $args );
}

add_action( 'init', 'eboh_register_team_category', 0 );

/**
 * Register Team Meta Fields for REST API and admin
 */
function eboh_register_team_meta() {
	$meta_fields = array(
		'klasse'  => __( 'Klasse', 'eboh' ),
		'regio'   => __( 'Regio', 'eboh' ),
		'trainer' => __( 'Trainer(s)', 'eboh' ),
	);

	foreach ( $meta_fields as $key => $label ) {
		register_post_meta( 'team', $key, array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
}

add_action( 'init', 'eboh_register_team_meta' );

/**
 * Add Team Details Meta Box
 */
function eboh_add_team_meta_boxes() {
	add_meta_box(
		'eboh_team_details',
		__( 'Team Details', 'eboh' ),
		'eboh_team_details_callback',
		'team',
		'normal',
		'high'
	);
}

add_action( 'add_meta_boxes', 'eboh_add_team_meta_boxes' );

/**
 * Render Team Details Meta Box
 */
function eboh_team_details_callback( $post ) {
	wp_nonce_field( 'eboh_team_details_nonce', 'eboh_team_details_nonce_field' );

	$klasse  = get_post_meta( $post->ID, 'klasse', true );
	$regio   = get_post_meta( $post->ID, 'regio', true );
	$trainer = get_post_meta( $post->ID, 'trainer', true );
	?>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row">
				<label for="eboh_klasse"><?php _e( 'Klasse', 'eboh' ); ?></label>
			</th>
			<td>
				<input type="text" id="eboh_klasse" name="eboh_klasse" value="<?php echo esc_attr( $klasse ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Bijv. 2e klasse E', 'eboh' ); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="eboh_regio"><?php _e( 'Regio', 'eboh' ); ?></label>
			</th>
			<td>
				<input type="text" id="eboh_regio" name="eboh_regio" value="<?php echo esc_attr( $regio ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Bijv. Zuid 1', 'eboh' ); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="eboh_trainer"><?php _e( 'Trainer(s)', 'eboh' ); ?></label>
			</th>
			<td>
				<input type="text" id="eboh_trainer" name="eboh_trainer" value="<?php echo esc_attr( $trainer ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Naam van de trainer(s)', 'eboh' ); ?>" />
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Save Team Details Meta Box Data
 */
function eboh_save_team_details( $post_id ) {
	if ( ! isset( $_POST['eboh_team_details_nonce_field'] ) ||
	     ! wp_verify_nonce( $_POST['eboh_team_details_nonce_field'], 'eboh_team_details_nonce' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array( 'klasse', 'regio', 'trainer' );
	foreach ( $fields as $field ) {
		$key = 'eboh_' . $field;
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $key ] ) );
		}
	}
}

add_action( 'save_post_team', 'eboh_save_team_details' );

/**
 * Add custom columns to Teams admin list
 */
function eboh_team_admin_columns( $columns ) {
	$new_columns = array();
	foreach ( $columns as $key => $value ) {
		$new_columns[ $key ] = $value;
		if ( 'title' === $key ) {
			$new_columns['klasse']  = __( 'Klasse', 'eboh' );
			$new_columns['regio']   = __( 'Regio', 'eboh' );
			$new_columns['trainer'] = __( 'Trainer', 'eboh' );
		}
	}
	return $new_columns;
}

add_filter( 'manage_team_posts_columns', 'eboh_team_admin_columns' );

/**
 * Display custom column content for Teams
 */
function eboh_team_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'klasse':
			echo esc_html( get_post_meta( $post_id, 'klasse', true ) );
			break;
		case 'regio':
			echo esc_html( get_post_meta( $post_id, 'regio', true ) );
			break;
		case 'trainer':
			echo esc_html( get_post_meta( $post_id, 'trainer', true ) );
			break;
	}
}

add_action( 'manage_team_posts_custom_column', 'eboh_team_admin_column_content', 10, 2 );

/**
 * Make custom columns sortable
 */
function eboh_team_sortable_columns( $columns ) {
	$columns['klasse']  = 'klasse';
	$columns['regio']   = 'regio';
	$columns['trainer'] = 'trainer';
	return $columns;
}

add_filter( 'manage_edit-team_sortable_columns', 'eboh_team_sortable_columns' );

/**
 * Handle sorting by custom columns
 */
function eboh_team_orderby( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( in_array( $orderby, array( 'klasse', 'regio', 'trainer' ), true ) ) {
		$query->set( 'meta_key', $orderby );
		$query->set( 'orderby', 'meta_value' );
	}
}

add_action( 'pre_get_posts', 'eboh_team_orderby' );
