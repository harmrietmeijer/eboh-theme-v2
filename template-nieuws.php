<?php
/**
 * Template Name: Nieuws
 * Template Post Type: page
 *
 * @package EBOH
 * @since 2.0.0
 */

get_header();
$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
?>

<section class="page-hero<?php echo $hero_image ? ' page-hero--image' : ''; ?>"
	<?php if ( $hero_image ) : ?>style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"<?php endif; ?>>
	<div class="page-hero__container">
		<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?></p>
		<span class="page-hero__eyebrow"><?php esc_html_e( 'Van de club', 'eboh' ); ?></span>
		<h1 class="page-hero__title"><?php the_title(); ?></h1>
		<?php if ( get_the_excerpt() ) : ?>
			<p class="page-hero__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</section>

<main id="main" class="page-shell page-shell--muted">
	<div class="page-container">

		<?php
		// Category filter tabs based on actual categories.
		$cats        = get_categories( array( 'hide_empty' => true ) );
		$active_cat  = isset( $_GET['cat_slug'] ) ? sanitize_key( $_GET['cat_slug'] ) : '';
		?>
		<div class="tabs" style="margin-bottom:40px;">
			<a class="tabs__item <?php echo $active_cat === '' ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Alle', 'eboh' ); ?></a>
			<?php foreach ( $cats as $cat ) : ?>
				<a class="tabs__item <?php echo $active_cat === $cat->slug ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'cat_slug', $cat->slug, get_permalink() ) ); ?>">
					<?php echo esc_html( $cat->name ); ?>
				</a>
			<?php endforeach; ?>
		</div>

		<?php
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$args  = array(
			'post_type'      => 'post',
			'posts_per_page' => 9,
			'paged'          => $paged,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		if ( $active_cat ) {
			$args['category_name'] = $active_cat;
		}
		$news_query = new WP_Query( $args );

		if ( $news_query->have_posts() ) :
			?>
			<div class="news-grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr));">
				<?php while ( $news_query->have_posts() ) : $news_query->the_post(); get_template_part( 'parts/news-card' ); endwhile; ?>
			</div>

			<?php if ( $news_query->max_num_pages > 1 ) : ?>
				<div style="display:flex;justify-content:center;margin-top:48px;gap:10px;">
					<?php
					echo wp_kses_post( paginate_links( array(
						'total'   => $news_query->max_num_pages,
						'current' => $paged,
						'prev_text' => '← Vorige',
						'next_text' => 'Volgende →',
					) ) );
					?>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<p style="text-align:center;padding:60px 20px;font-size:18px;opacity:0.7;"><?php esc_html_e( 'Er zijn momenteel geen nieuwsberichten beschikbaar.', 'eboh' ); ?></p>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</main>

<?php get_footer();
