<?php
/**
 * Blog Listing Template (gebruikt door WordPress als Posts page, bv. /nieuws)
 *
 * @package EBOH
 */

get_header();

$posts_page_id = (int) get_option( 'page_for_posts' );
$hero_image    = $posts_page_id ? get_the_post_thumbnail_url( $posts_page_id, 'full' ) : '';
$hero_title    = $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Nieuws', 'eboh-v2' );
$hero_excerpt  = $posts_page_id ? get_post_field( 'post_excerpt', $posts_page_id ) : '';

// Categorie-filter via ?cat_slug=... (gekozen via tabs hieronder).
$active_slug = isset( $_GET['cat_slug'] ) ? sanitize_key( wp_unslash( $_GET['cat_slug'] ) ) : '';

// Bouw lijst met filter-tabs. Slugs MOETEN overeenkomen met de categorie-slugs
// die door eboh_ensure_news_categories() in functions.php worden aangemaakt.
$filter_tabs = array(
	''                => __( 'Alle', 'eboh-v2' ),
	'de-club'         => __( 'De club', 'eboh-v2' ),
	'eboh-1'          => __( 'EBOH 1', 'eboh-v2' ),
	'jeugd'           => __( 'Jeugd', 'eboh-v2' ),
	'maatschappelijk' => __( 'Maatschappelijk', 'eboh-v2' ),
);
?>

<section class="page-hero<?php echo $hero_image ? ' page-hero--image' : ''; ?>"
	<?php if ( $hero_image ) : ?>style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"<?php endif; ?>>
	<div class="page-hero__container">
		<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php echo esc_html( $hero_title ); ?></p>
		<span class="page-hero__eyebrow"><?php esc_html_e( 'Van de club', 'eboh-v2' ); ?></span>
		<h1 class="page-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
		<?php if ( $hero_excerpt ) : ?>
			<p class="page-hero__subtitle"><?php echo esc_html( $hero_excerpt ); ?></p>
		<?php endif; ?>
	</div>
</section>

<main id="main" class="page-shell page-shell--muted">
	<div class="page-container">

		<nav class="news-filters" aria-label="<?php esc_attr_e( 'Filter op categorie', 'eboh-v2' ); ?>">
			<?php
			$base_url = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/nieuws/' );
			foreach ( $filter_tabs as $slug => $label ) {
				$href = $slug === ''
					? esc_url( $base_url )
					: esc_url( add_query_arg( 'cat_slug', $slug, $base_url ) );
				$is_active = ( $active_slug === $slug ) ? ' is-active' : '';
				echo '<a class="news-filters__item' . $is_active . '" href="' . $href . '">' . esc_html( $label ) . '</a>';
			}
			?>
		</nav>

		<?php
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$args  = array(
			'post_type'           => 'post',
			'posts_per_page'      => 12,
			'paged'               => $paged,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
		);
		if ( $active_slug !== '' ) {
			$args['category_name'] = $active_slug;
		}
		$news_query = new WP_Query( $args );
		?>

		<?php if ( $news_query->have_posts() ) : ?>
			<div class="news-grid">
				<?php while ( $news_query->have_posts() ) : $news_query->the_post(); ?>
					<?php get_template_part( 'parts/news-card' ); ?>
				<?php endwhile; ?>
			</div>

			<?php
			echo paginate_links( array(
				'total'     => $news_query->max_num_pages,
				'current'   => max( 1, $paged ),
				'prev_text' => esc_html__( '← Vorige', 'eboh-v2' ),
				'next_text' => esc_html__( 'Volgende →', 'eboh-v2' ),
			) );
			wp_reset_postdata();
			?>
		<?php else : ?>
			<p style="margin-top:32px;"><?php esc_html_e( 'Geen nieuwsberichten gevonden in deze categorie.', 'eboh-v2' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer();
