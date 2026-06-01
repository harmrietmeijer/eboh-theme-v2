<?php
/**
 * Generic Page Template for EBOH
 *
 * @package EBOH
 */

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$has_thumb = has_post_thumbnail();
		?>

		<section class="page-hero<?php echo $has_thumb ? ' page-hero--image' : ''; ?>"
			<?php if ( $has_thumb ) : ?>style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( null, 'full' ) ); ?>');"<?php endif; ?>>
			<div class="page-hero__container">
				<?php
				$parent_id = wp_get_post_parent_id( get_the_ID() );
				if ( $parent_id ) {
					echo '<p class="page-hero__breadcrumbs"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a> / <a href="' . esc_url( get_permalink( $parent_id ) ) . '">' . esc_html( get_the_title( $parent_id ) ) . '</a> / ' . esc_html( get_the_title() ) . '</p>';
				} else {
					echo '<p class="page-hero__breadcrumbs"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a> / ' . esc_html( get_the_title() ) . '</p>';
				}
				?>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
				<?php
				$subtitle = get_post_meta( get_the_ID(), '_eboh_subtitle', true );
				if ( $subtitle ) {
					echo '<p class="page-hero__subtitle">' . esc_html( $subtitle ) . '</p>';
				}
				?>
			</div>
		</section>

		<main id="main" class="page-shell">
			<div class="page-container page-container--narrow">
				<div class="page-content">
					<?php the_content(); ?>
				</div>
			</div>
		</main>

		<?php
	}
}

get_footer();
