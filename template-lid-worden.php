<?php
/**
 * Template Name: Lid Worden
 * Template Post Type: page
 *
 * @package EBOH
 * @since 2.0.0
 *
 * Renders the digital membership signup form. The form posts to admin-post.php
 * and is handled by eboh_handle_membership_form() in functions.php.
 */

get_header();
$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$submitted  = isset( $_GET['eboh_signup'] ) && $_GET['eboh_signup'] === 'success';
$failed     = isset( $_GET['eboh_signup'] ) && $_GET['eboh_signup'] === 'error';
?>

<section class="page-hero<?php echo $hero_image ? ' page-hero--image' : ''; ?>"
	<?php if ( $hero_image ) : ?>style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"<?php endif; ?>>
	<div class="page-hero__container">
		<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?></p>
		<span class="page-hero__eyebrow"><?php esc_html_e( 'Word deel van de club', 'eboh' ); ?></span>
		<h1 class="page-hero__title"><?php the_title(); ?></h1>
		<?php if ( get_the_excerpt() ) : ?>
			<p class="page-hero__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php else : ?>
			<p class="page-hero__subtitle"><?php esc_html_e( 'Van mini-pupil tot veteraan: bij EBOH is iedereen welkom. Meld je online aan.', 'eboh' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<main id="main" class="page-shell">
	<div class="page-container">

		<?php if ( $submitted ) : ?>
			<div class="info-card" style="margin-bottom:32px;background:#eafbe7;border-top-color:#2ea04a;">
				<h3 class="info-card__title" style="color:#2ea04a;"><?php esc_html_e( 'Bedankt voor je aanmelding!', 'eboh' ); ?></h3>
				<p style="margin:0;"><?php esc_html_e( 'We hebben je aanmelding ontvangen en de ledenadministratie neemt binnen een week contact met je op.', 'eboh' ); ?></p>
			</div>
		<?php elseif ( $failed ) : ?>
			<div class="info-card" style="margin-bottom:32px;border-top-color:#E80808;">
				<h3 class="info-card__title"><?php esc_html_e( 'Er ging iets mis', 'eboh' ); ?></h3>
				<p style="margin:0;"><?php esc_html_e( 'Controleer of alle verplichte velden zijn ingevuld en probeer opnieuw.', 'eboh' ); ?></p>
			</div>
		<?php endif; ?>

		<?php if ( get_the_content() ) : ?>
			<div class="page-content" style="margin-bottom:48px;"><?php the_content(); ?></div>
		<?php endif; ?>

		<div class="two-col">
			<div>
				<h2 class="page-section__title page-section__title--bar"><?php esc_html_e( 'Digitaal aanmeldformulier', 'eboh' ); ?></h2>
				<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="eboh_membership_signup">
					<?php wp_nonce_field( 'eboh_signup', 'eboh_signup_nonce' ); ?>

					<h3 style="font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:1rem;letter-spacing:0.08em;margin:0;color:#343B41;"><?php esc_html_e( 'Persoonlijke gegevens', 'eboh' ); ?></h3>
					<div class="form__row">
						<div class="form__field"><label for="m-first"><?php esc_html_e( 'Voornaam', 'eboh' ); ?></label><input type="text" id="m-first" name="first_name" required></div>
						<div class="form__field"><label for="m-last"><?php esc_html_e( 'Achternaam', 'eboh' ); ?></label><input type="text" id="m-last" name="last_name" required></div>
					</div>
					<div class="form__row">
						<div class="form__field"><label for="m-dob"><?php esc_html_e( 'Geboortedatum', 'eboh' ); ?></label><input type="date" id="m-dob" name="dob" required></div>
						<div class="form__field"><label for="m-gender"><?php esc_html_e( 'Geslacht', 'eboh' ); ?></label>
							<select id="m-gender" name="gender">
								<option><?php esc_html_e( 'Man', 'eboh' ); ?></option>
								<option><?php esc_html_e( 'Vrouw', 'eboh' ); ?></option>
								<option><?php esc_html_e( 'Anders / wil ik niet zeggen', 'eboh' ); ?></option>
							</select>
						</div>
					</div>
					<div class="form__field"><label for="m-street"><?php esc_html_e( 'Adres (straat + nr)', 'eboh' ); ?></label><input type="text" id="m-street" name="street"></div>
					<div class="form__row">
						<div class="form__field"><label for="m-zip"><?php esc_html_e( 'Postcode', 'eboh' ); ?></label><input type="text" id="m-zip" name="zip"></div>
						<div class="form__field"><label for="m-city"><?php esc_html_e( 'Woonplaats', 'eboh' ); ?></label><input type="text" id="m-city" name="city"></div>
					</div>
					<div class="form__row">
						<div class="form__field"><label for="m-email"><?php esc_html_e( 'E-mailadres', 'eboh' ); ?></label><input type="email" id="m-email" name="email" required></div>
						<div class="form__field"><label for="m-phone"><?php esc_html_e( 'Telefoonnummer', 'eboh' ); ?></label><input type="tel" id="m-phone" name="phone"></div>
					</div>

					<h3 style="font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:1rem;letter-spacing:0.08em;margin:20px 0 0;color:#343B41;"><?php esc_html_e( 'Voetbalvoorkeur', 'eboh' ); ?></h3>
					<div class="form__row">
						<div class="form__field"><label for="m-cat"><?php esc_html_e( 'Categorie', 'eboh' ); ?></label>
							<select id="m-cat" name="category">
								<option>Mini (4-5 jaar)</option>
								<option>JO8-JO13 jeugd</option>
								<option>JO15-JO19 jeugd</option>
								<option>Senioren zaterdag</option>
								<option>Dames</option>
								<option>Veteranen</option>
								<option>Niet-spelend lid</option>
							</select>
						</div>
						<div class="form__field"><label for="m-exp"><?php esc_html_e( 'Ervaring', 'eboh' ); ?></label>
							<select id="m-exp" name="experience">
								<option><?php esc_html_e( 'Nieuw — nog nooit gevoetbald', 'eboh' ); ?></option>
								<option><?php esc_html_e( 'Overschrijving van andere club', 'eboh' ); ?></option>
								<option><?php esc_html_e( 'Heeft eerder gevoetbald', 'eboh' ); ?></option>
							</select>
						</div>
					</div>
					<div class="form__field"><label for="m-prev"><?php esc_html_e( 'Vorige club (indien van toepassing)', 'eboh' ); ?></label><input type="text" id="m-prev" name="previous_club"></div>

					<h3 style="font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:1rem;letter-spacing:0.08em;margin:20px 0 0;color:#343B41;"><?php esc_html_e( 'Ouder / verzorger (bij minderjarigheid)', 'eboh' ); ?></h3>
					<div class="form__row">
						<div class="form__field"><label for="m-parent"><?php esc_html_e( 'Naam ouder/verzorger', 'eboh' ); ?></label><input type="text" id="m-parent" name="parent_name"></div>
						<div class="form__field"><label for="m-pphone"><?php esc_html_e( 'Telefoon ouder/verzorger', 'eboh' ); ?></label><input type="tel" id="m-pphone" name="parent_phone"></div>
					</div>

					<h3 style="font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:1rem;letter-spacing:0.08em;margin:20px 0 0;color:#343B41;"><?php esc_html_e( 'Betaling & akkoord', 'eboh' ); ?></h3>
					<div class="form__field"><label for="m-iban">IBAN</label><input type="text" id="m-iban" name="iban" placeholder="NL.. ABNA .. .. .."></div>
					<div class="form__field">
						<label style="display:flex;gap:10px;align-items:flex-start;text-transform:none;letter-spacing:0;font-family:'Work Sans',sans-serif;font-weight:400;font-size:14px;line-height:1.5;">
							<input type="checkbox" name="agree_rules" required style="width:auto;margin-top:3px;">
							<?php esc_html_e( 'Ik ga akkoord met statuten, huishoudelijk reglement en gedragscode.', 'eboh' ); ?>
						</label>
					</div>
					<div class="form__field">
						<label style="display:flex;gap:10px;align-items:flex-start;text-transform:none;letter-spacing:0;font-family:'Work Sans',sans-serif;font-weight:400;font-size:14px;line-height:1.5;">
							<input type="checkbox" name="agree_photos" style="width:auto;margin-top:3px;">
							<?php esc_html_e( 'Ik geef toestemming voor gebruik van foto’s op website en socials.', 'eboh' ); ?>
						</label>
					</div>

					<div class="form__actions">
						<button type="submit" class="btn filled"><?php esc_html_e( 'Aanmelding versturen', 'eboh' ); ?></button>
					</div>
					<p class="form__note"><?php esc_html_e( 'Na je aanmelding neemt de ledenadministratie binnen een week contact met je op.', 'eboh' ); ?></p>
				</form>
			</div>

			<aside>
				<div class="info-card" style="margin-bottom:20px;">
					<h3 class="info-card__title"><?php esc_html_e( 'Contributie 2025/2026', 'eboh' ); ?></h3>
					<ul class="info-card__list">
						<li><strong>Mini's</strong><span>€ 69 / jaar</span></li>
						<li><strong>JO8 – JO13</strong><span>€ 139 / jaar</span></li>
						<li><strong>JO15 – JO19</strong><span>€ 169 / jaar</span></li>
						<li><strong>Senioren</strong><span>€ 219 / jaar</span></li>
						<li><strong>Veteranen</strong><span>€ 179 / jaar</span></li>
						<li><strong>Niet-spelend</strong><span>€ 49 / jaar</span></li>
					</ul>
				</div>
				<div class="info-card">
					<h3 class="info-card__title"><?php esc_html_e( 'Vragen?', 'eboh' ); ?></h3>
					<p style="font-size:14px;line-height:1.7;margin:0 0 10px;"><?php esc_html_e( 'De ledenadministratie helpt je graag verder.', 'eboh' ); ?></p>
					<p class="person-card__contact" style="margin:0;">leden@eboh.nl</p>
				</div>
			</aside>
		</div>
	</div>
</main>

<?php get_footer();
