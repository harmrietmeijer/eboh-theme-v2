<?php
/**
 * Sportlink Club.Dataservice API Integration
 *
 * Haalt programma, uitslagen en standen op via de Sportlink Data API.
 * Vervangt de oude KNVB Voetbal Datacentre koppeling.
 * Documentatie: https://sportlinkservices.freshdesk.com/nl/support/solutions/folders/9000228070
 *
 * @package EBOH
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =====================================================================
// 1. ADMIN SETTINGS PAGE
// =====================================================================

add_action( 'admin_menu', 'eboh_sportlink_admin_menu' );

function eboh_sportlink_admin_menu() {
	add_menu_page(
		'Sportlink API Instellingen',
		'Sportlink API',
		'edit_posts',
		'eboh-sportlink-settings',
		'eboh_sportlink_settings_page',
		'dashicons-shield',
		81
	);
}

add_action( 'admin_init', 'eboh_sportlink_register_settings' );

function eboh_sportlink_register_settings() {
	register_setting( 'eboh_sportlink_options', 'eboh_sportlink_client_id', 'sanitize_text_field' );
	register_setting( 'eboh_sportlink_options', 'eboh_sportlink_cache_minutes', 'absint' );
	// Allow editors to save these settings (default requires manage_options)
	add_filter( 'option_page_capability_eboh_sportlink_options', function() { return 'edit_posts'; } );
}

function eboh_sportlink_settings_page() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	// Manual cache clear
	if ( isset( $_POST['eboh_sportlink_clear_cache'] ) && check_admin_referer( 'eboh_sportlink_actions_nonce' ) ) {
		eboh_sportlink_clear_cache();
		echo '<div class="notice notice-success"><p>Sportlink cache gewist.</p></div>';
	}

	// Test connection
	$test_result = '';
	if ( isset( $_POST['eboh_sportlink_test'] ) && check_admin_referer( 'eboh_sportlink_actions_nonce' ) ) {
		$api   = new EBOH_Sportlink_API();
		$teams = $api->get_teams();
		if ( is_wp_error( $teams ) ) {
			$test_result = '<div class="notice notice-error"><p>Fout: ' . esc_html( $teams->get_error_message() ) . '</p></div>';
		} else {
			$count       = is_array( $teams ) ? count( $teams ) : 0;
			$test_result = '<div class="notice notice-success"><p>Verbinding succesvol! ' . $count . ' team(s) gevonden.</p></div>';

			// Show raw team data for debugging
			if ( $count > 0 ) {
				$test_result .= '<div class="notice notice-info"><p><strong>Teams gevonden:</strong></p><ul style="margin-left:20px;list-style:disc;">';
				foreach ( $teams as $team ) {
					$naam = isset( $team['teamnaam'] ) ? $team['teamnaam'] : ( isset( $team['naam'] ) ? $team['naam'] : json_encode( $team ) );
					$code = isset( $team['teamcode'] ) ? ' (code: ' . $team['teamcode'] . ')' : '';
					$test_result .= '<li>' . esc_html( $naam . $code ) . '</li>';
				}
				$test_result .= '</ul></div>';
			}
		}
	}

	$client_id     = get_option( 'eboh_sportlink_client_id', '' );
	$cache_minutes = get_option( 'eboh_sportlink_cache_minutes', 30 );
	?>
	<div class="wrap">
		<h1>Sportlink Club.Dataservice — API Instellingen</h1>
		<p>Configureer hier de koppeling met de <a href="https://data.sportlink.com" target="_blank">Sportlink Club.Dataservice API</a>. Je hebt een Client ID nodig die je via Sportlink Club kunt aanvragen.</p>

		<?php echo $test_result; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'eboh_sportlink_options' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="eboh_sportlink_client_id">Client ID</label></th>
					<td>
						<input type="text" id="eboh_sportlink_client_id" name="eboh_sportlink_client_id" value="<?php echo esc_attr( $client_id ); ?>" class="regular-text" placeholder="bijv. i4LQjMjLoJ">
						<p class="description">De unieke Client ID van je club voor de Sportlink Data API. Verkrijgbaar via Sportlink Club.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="eboh_sportlink_cache_minutes">Cache duur (minuten)</label></th>
					<td>
						<input type="number" id="eboh_sportlink_cache_minutes" name="eboh_sportlink_cache_minutes" value="<?php echo esc_attr( $cache_minutes ); ?>" class="small-text" min="5" max="1440">
						<p class="description">Hoe lang API-responses worden gecacht. Aanbevolen: 30 minuten. Minimum: 5 minuten.</p>
					</td>
				</tr>
			</table>
			<?php submit_button( 'Instellingen opslaan' ); ?>
		</form>

		<hr>
		<h2>Acties</h2>
		<form method="post">
			<?php wp_nonce_field( 'eboh_sportlink_actions_nonce' ); ?>
			<p>
				<button type="submit" name="eboh_sportlink_test" class="button button-secondary">Verbinding testen</button>
				<button type="submit" name="eboh_sportlink_clear_cache" class="button button-secondary">Cache wissen</button>
			</p>
		</form>

		<hr>
		<h2>Beschikbare shortcodes</h2>
		<table class="widefat striped" style="max-width:900px;">
			<thead><tr><th>Shortcode</th><th>Beschrijving</th><th>Parameters</th></tr></thead>
			<tbody>
				<tr>
					<td><code>[eboh_programma]</code></td>
					<td>Wedstrijdprogramma</td>
					<td><code>team</code> (teamcode), <code>dagen</code> (max 365), <code>spelsoort</code> (V=voetbal), <code>competitiesoort</code> (R=regulier, B=beker)</td>
				</tr>
				<tr>
					<td><code>[eboh_uitslagen]</code></td>
					<td>Uitslagen</td>
					<td><code>team</code> (teamcode), <code>dagen</code> (max 365), <code>aantalregels</code> (max resultaten)</td>
				</tr>
				<tr>
					<td><code>[eboh_stand]</code></td>
					<td>Poulestand</td>
					<td><code>team</code> (teamcode) — poule wordt automatisch opgezocht</td>
				</tr>
				<tr>
					<td><code>[eboh_teams]</code></td>
					<td>Overzicht van alle teams</td>
					<td><code>teamsoort</code> (bijv. S=senioren, J=junioren), <code>spelsoort</code> (V=voetbal)</td>
				</tr>
				<tr>
					<td><code>[eboh_overzicht]</code></td>
					<td>Compleet overzicht: stand + programma + uitslagen</td>
					<td><code>team</code> (teamcode)</td>
				</tr>
			</tbody>
		</table>

		<hr>
		<h2>Backward-compatible shortcodes</h2>
		<p>De oude <code>[eboh_knvb_*]</code> shortcodes werken nog steeds als alias. Ze roepen intern de nieuwe Sportlink shortcodes aan.</p>
	</div>
	<?php
}


// =====================================================================
// 2. API CLASS
// =====================================================================

class EBOH_Sportlink_API {

	private $base_url  = 'https://data.sportlink.com';
	private $client_id = '';
	private $cache_ttl = 1800; // 30 minutes default

	public function __construct() {
		$this->client_id = get_option( 'eboh_sportlink_client_id', '' );
		$cache_minutes   = get_option( 'eboh_sportlink_cache_minutes', 30 );
		$this->cache_ttl = max( 300, absint( $cache_minutes ) * 60 );
	}

	/**
	 * Check if API is configured.
	 */
	public function is_configured() {
		return ! empty( $this->client_id );
	}

	/**
	 * Execute an API call with caching.
	 *
	 * Sportlink API is simple: base_url/article?client_id=XXX&param=value
	 * No session management or hashing needed.
	 */
	public function api_call( $article, $params = array(), $cache_key = '' ) {
		if ( ! $this->is_configured() ) {
			return new WP_Error( 'sportlink_not_configured', 'Sportlink API is niet geconfigureerd. Ga naar Instellingen → Sportlink API.' );
		}

		// Check cache first
		if ( $cache_key ) {
			$cached = get_transient( $cache_key );
			if ( false !== $cached ) {
				return $cached;
			}
		}

		// Build URL: https://data.sportlink.com/{article}?client_id=XXX&params
		$url_params = array_merge( array( 'client_id' => $this->client_id ), $params );
		$url        = $this->base_url . '/' . ltrim( $article, '/' ) . '?' . http_build_query( $url_params );

		$response = wp_remote_get( $url, array(
			'timeout' => 15,
			'headers' => array(
				'Accept' => 'application/json',
			),
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( $code !== 200 ) {
			return new WP_Error( 'sportlink_http_error', 'Sportlink API HTTP fout: ' . $code . ' — ' . substr( $body, 0, 200 ) );
		}

		$data = json_decode( $body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error( 'sportlink_parse_error', 'Kon het Sportlink API-antwoord niet verwerken (JSON fout).' );
		}

		// Sportlink returns arrays directly (no wrapper object)
		if ( ! is_array( $data ) ) {
			$data = array();
		}

		// Cache the result
		if ( $cache_key ) {
			set_transient( $cache_key, $data, $this->cache_ttl );
		}

		return $data;
	}

	/**
	 * Get all teams for this club.
	 * Endpoint: teams
	 */
	public function get_teams( $teamsoort = '', $spelsoort = 'V' ) {
		$params = array();
		if ( $spelsoort ) {
			$params['spelsoort'] = $spelsoort;
		}
		if ( $teamsoort ) {
			$params['teamsoort'] = $teamsoort;
		}
		$cache_key = 'eboh_sl_teams_' . md5( serialize( $params ) );
		return $this->api_call( 'teams', $params, $cache_key );
	}

	/**
	 * Get schedule (programma) for a team.
	 * Endpoint: programma
	 */
	public function get_programma( $teamcode = '', $dagen = 30, $spelsoort = 'V', $competitiesoort = '' ) {
		$params = array(
			'aantaldagen' => min( 365, max( 1, intval( $dagen ) ) ),
		);
		if ( $teamcode ) {
			$params['teamcode'] = $teamcode;
		}
		if ( $spelsoort ) {
			$params['spelsoort'] = $spelsoort;
		}
		if ( $competitiesoort ) {
			$params['competitiesoort'] = $competitiesoort;
		}

		$cache_key = 'eboh_sl_prog_' . md5( serialize( $params ) );
		return $this->api_call( 'programma', $params, $cache_key );
	}

	/**
	 * Get results (uitslagen) for a team.
	 * Endpoint: uitslagen
	 */
	public function get_uitslagen( $teamcode = '', $dagen = 90, $aantalregels = '' ) {
		$params = array(
			'aantaldagen' => min( 365, max( 1, intval( $dagen ) ) ),
		);
		if ( $teamcode ) {
			$params['teamcode'] = $teamcode;
		}
		if ( $aantalregels ) {
			$params['aantalregels'] = intval( $aantalregels );
		}

		$cache_key = 'eboh_sl_uits_' . md5( serialize( $params ) );
		return $this->api_call( 'uitslagen', $params, $cache_key );
	}

	/**
	 * Get poule list for a team (to find poulecode for standings).
	 * Endpoint: poulelijst
	 */
	public function get_poulelijst( $teamcode = '' ) {
		$params = array();
		if ( $teamcode ) {
			$params['teamcode'] = $teamcode;
		}
		$cache_key = 'eboh_sl_poule_' . md5( serialize( $params ) );
		return $this->api_call( 'poulelijst', $params, $cache_key );
	}

	/**
	 * Get standings (poulestand).
	 * Endpoint: poulestand
	 */
	public function get_poulestand( $poulecode ) {
		$params = array(
			'poulecode' => $poulecode,
		);
		$cache_key = 'eboh_sl_stand_' . md5( $poulecode );
		return $this->api_call( 'poulestand', $params, $cache_key );
	}

	/**
	 * Get standings for a team by first resolving the poulecode.
	 */
	public function get_stand_for_team( $teamcode ) {
		$poules = $this->get_poulelijst( $teamcode );
		if ( is_wp_error( $poules ) ) {
			return $poules;
		}

		if ( empty( $poules ) ) {
			return new WP_Error( 'sportlink_no_poule', 'Geen competitie (poule) gevonden voor dit team.' );
		}

		// Use the first poule (usually the main competition)
		$poulecode = isset( $poules[0]['poulecode'] ) ? $poules[0]['poulecode'] : '';
		if ( empty( $poulecode ) ) {
			return new WP_Error( 'sportlink_no_poulecode', 'Geen poulecode gevonden in de poule-gegevens.' );
		}

		return $this->get_poulestand( $poulecode );
	}

	/**
	 * Get cancelled matches (afgelastingen).
	 * Endpoint: afgelastingen
	 */
	public function get_afgelastingen( $dagen = 7 ) {
		$params = array(
			'aantaldagen' => min( 365, max( 1, intval( $dagen ) ) ),
		);
		$cache_key = 'eboh_sl_afg_' . md5( serialize( $params ) );
		return $this->api_call( 'afgelastingen', $params, $cache_key );
	}

	/**
	 * Resolve team name to teamcode. Searches through the teams list.
	 */
	public function resolve_team( $team_name_or_code ) {
		// If it looks like a code (alphanumeric, no spaces), return as-is
		if ( preg_match( '/^[A-Za-z0-9]+$/', $team_name_or_code ) && strlen( $team_name_or_code ) <= 10 ) {
			return $team_name_or_code;
		}

		$teams = $this->get_teams();
		if ( is_wp_error( $teams ) ) {
			return $teams;
		}

		$search = strtolower( trim( $team_name_or_code ) );

		// Exact match on teamnaam
		foreach ( $teams as $team ) {
			$naam = isset( $team['teamnaam'] ) ? $team['teamnaam'] : '';
			if ( strtolower( $naam ) === $search ) {
				return isset( $team['teamcode'] ) ? $team['teamcode'] : '';
			}
		}

		// Partial match
		foreach ( $teams as $team ) {
			$naam = isset( $team['teamnaam'] ) ? $team['teamnaam'] : '';
			if ( strpos( strtolower( $naam ), $search ) !== false ) {
				return isset( $team['teamcode'] ) ? $team['teamcode'] : '';
			}
		}

		return new WP_Error( 'sportlink_team_not_found', 'Team "' . $team_name_or_code . '" niet gevonden.' );
	}
}


// =====================================================================
// 3. CACHE HELPER
// =====================================================================

function eboh_sportlink_clear_cache() {
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_eboh_sl_%' OR option_name LIKE '_transient_timeout_eboh_sl_%'" );
	// Also clear old KNVB cache
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_eboh_knvb_%' OR option_name LIKE '_transient_timeout_eboh_knvb_%'" );
}


// =====================================================================
// 4. SHORTCODES
// =====================================================================

/**
 * [eboh_programma team="" dagen="30" spelsoort="V" competitiesoort=""]
 */
add_shortcode( 'eboh_programma', 'eboh_programma_shortcode' );

function eboh_programma_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'team'             => '',
		'dagen'            => 30,
		'spelsoort'        => 'V',
		'competitiesoort'  => '',
	), $atts, 'eboh_programma' );

	$api = new EBOH_Sportlink_API();
	if ( ! $api->is_configured() ) {
		return eboh_sportlink_notice( 'Sportlink API is niet geconfigureerd.' );
	}

	$teamcode = '';
	if ( ! empty( $atts['team'] ) ) {
		$teamcode = $api->resolve_team( $atts['team'] );
		if ( is_wp_error( $teamcode ) ) {
			return eboh_sportlink_notice( $teamcode->get_error_message() );
		}
	}

	$programma = $api->get_programma( $teamcode, $atts['dagen'], $atts['spelsoort'], $atts['competitiesoort'] );
	if ( is_wp_error( $programma ) ) {
		return eboh_sportlink_notice( $programma->get_error_message() );
	}

	if ( empty( $programma ) ) {
		return eboh_sportlink_notice( 'Geen wedstrijden gevonden in het programma.' );
	}

	ob_start();
	?>
	<div class="sportlink-table-wrap">
		<table class="sportlink-table sportlink-table--programma">
			<thead>
				<tr>
					<th>Datum</th>
					<th>Tijd</th>
					<th>Thuis</th>
					<th></th>
					<th>Uit</th>
					<th class="sportlink-table__extra">Comp.</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $programma as $match ) :
					$datum = eboh_sportlink_format_date( $match );
					$tijd  = isset( $match['aanvangstijd'] ) ? $match['aanvangstijd'] : '';
					$thuis = isset( $match['thuisteam'] ) ? $match['thuisteam'] : '';
					$uit   = isset( $match['uitteam'] ) ? $match['uitteam'] : '';
					$comp  = isset( $match['competitienaam'] ) ? $match['competitienaam'] : '';

					// Check for cancellation
					$afgelast = false;
					if ( isset( $match['wedstrijdstatus'] ) && strtolower( $match['wedstrijdstatus'] ) === 'afgelast' ) {
						$afgelast = true;
					}
					?>
					<tr<?php echo $afgelast ? ' class="sportlink-table__row--status"' : ''; ?>>
						<td data-label="Datum"><?php echo esc_html( $datum ); ?></td>
						<td data-label="Tijd">
							<?php if ( $afgelast ) : ?>
								<span class="sportlink-status">AFG</span>
							<?php else : ?>
								<?php echo esc_html( $tijd ); ?>
							<?php endif; ?>
						</td>
						<td data-label="Thuis" class="sportlink-table__team"><?php echo eboh_v2_render_team_cell( $thuis ); ?></td>
						<td class="sportlink-table__vs">–</td>
						<td data-label="Uit" class="sportlink-table__team"><?php echo eboh_v2_render_team_cell( $uit ); ?></td>
						<td data-label="Comp." class="sportlink-table__extra"><?php echo esc_html( $comp ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
	return ob_get_clean();
}


/**
 * [eboh_uitslagen team="" dagen="90" aantalregels=""]
 */
add_shortcode( 'eboh_uitslagen', 'eboh_uitslagen_shortcode' );

function eboh_uitslagen_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'team'         => '',
		'dagen'        => 90,
		'aantalregels' => '',
	), $atts, 'eboh_uitslagen' );

	$api = new EBOH_Sportlink_API();
	if ( ! $api->is_configured() ) {
		return eboh_sportlink_notice( 'Sportlink API is niet geconfigureerd.' );
	}

	$teamcode = '';
	if ( ! empty( $atts['team'] ) ) {
		$teamcode = $api->resolve_team( $atts['team'] );
		if ( is_wp_error( $teamcode ) ) {
			return eboh_sportlink_notice( $teamcode->get_error_message() );
		}
	}

	$uitslagen = $api->get_uitslagen( $teamcode, $atts['dagen'], $atts['aantalregels'] );
	if ( is_wp_error( $uitslagen ) ) {
		return eboh_sportlink_notice( $uitslagen->get_error_message() );
	}

	if ( empty( $uitslagen ) ) {
		return eboh_sportlink_notice( 'Geen uitslagen gevonden.' );
	}

	ob_start();
	?>
	<div class="sportlink-table-wrap">
		<table class="sportlink-table sportlink-table--uitslagen">
			<thead>
				<tr>
					<th>Datum</th>
					<th>Thuis</th>
					<th class="sportlink-table__score-header">Uitslag</th>
					<th>Uit</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $uitslagen as $match ) :
					$datum  = eboh_sportlink_format_date( $match );
					$thuis  = isset( $match['thuisteam'] ) ? $match['thuisteam'] : '';
					$uit    = isset( $match['uitteam'] ) ? $match['uitteam'] : '';
					$score_thuis = isset( $match['uitslag'] ) ? $match['uitslag'] : '';

					// Sportlink often returns "uitslag" as "2 - 1" or separate fields
					if ( empty( $score_thuis ) ) {
						$st = isset( $match['doelpuntenthuisteam'] ) ? $match['doelpuntenthuisteam'] : '';
						$su = isset( $match['doelpuntenuitteam'] ) ? $match['doelpuntenuitteam'] : '';
						$score_thuis = ( $st !== '' && $su !== '' ) ? $st . ' – ' . $su : '-';
					}
					?>
					<tr>
						<td data-label="Datum"><?php echo esc_html( $datum ); ?></td>
						<td data-label="Thuis" class="sportlink-table__team"><?php echo eboh_v2_render_team_cell( $thuis ); ?></td>
						<td data-label="Uitslag" class="sportlink-table__score"><?php echo esc_html( $score_thuis ); ?></td>
						<td data-label="Uit" class="sportlink-table__team"><?php echo eboh_v2_render_team_cell( $uit ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
	return ob_get_clean();
}


/**
 * [eboh_stand team=""]
 * Looks up poulecode automatically via poulelijst, then fetches poulestand.
 */
add_shortcode( 'eboh_stand', 'eboh_stand_shortcode' );

function eboh_stand_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'team' => '',
	), $atts, 'eboh_stand' );

	$api = new EBOH_Sportlink_API();
	if ( ! $api->is_configured() ) {
		return eboh_sportlink_notice( 'Sportlink API is niet geconfigureerd.' );
	}

	$teamcode = '';
	if ( ! empty( $atts['team'] ) ) {
		$teamcode = $api->resolve_team( $atts['team'] );
		if ( is_wp_error( $teamcode ) ) {
			return eboh_sportlink_notice( $teamcode->get_error_message() );
		}
	}

	if ( empty( $teamcode ) ) {
		// Try to get first team
		$teams = $api->get_teams();
		if ( is_wp_error( $teams ) || empty( $teams ) ) {
			return eboh_sportlink_notice( 'Geen teams gevonden. Geef een teamcode op.' );
		}
		$teamcode = isset( $teams[0]['teamcode'] ) ? $teams[0]['teamcode'] : '';
	}

	$ranking = $api->get_stand_for_team( $teamcode );
	if ( is_wp_error( $ranking ) ) {
		return eboh_sportlink_notice( $ranking->get_error_message() );
	}

	if ( empty( $ranking ) ) {
		return eboh_sportlink_notice( 'Geen stand gevonden.' );
	}

	ob_start();
	?>
	<div class="sportlink-table-wrap">
		<table class="sportlink-table sportlink-table--stand">
			<thead>
				<tr>
					<th class="sportlink-table__pos">#</th>
					<th>Team</th>
					<th>GS</th>
					<th>W</th>
					<th>G</th>
					<th>V</th>
					<th>DV</th>
					<th>DT</th>
					<th class="sportlink-table__pts">Pnt</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$pos = 0;
				foreach ( $ranking as $row ) :
					$pos++;
					$team_naam  = isset( $row['teamnaam'] ) ? $row['teamnaam'] : ( isset( $row['naam'] ) ? $row['naam'] : '' );
					$gespeeld   = isset( $row['gespeeldewedstrijden'] ) ? $row['gespeeldewedstrijden'] : ( isset( $row['aantalgespeeld'] ) ? $row['aantalgespeeld'] : '0' );
					$gewonnen  = isset( $row['gewonnen'] ) ? $row['gewonnen'] : ( isset( $row['aantalgewonnen'] ) ? $row['aantalgewonnen'] : 0 );
					$gelijk    = isset( $row['gelijk'] ) ? $row['gelijk'] : ( isset( $row['aantalgelijk'] ) ? $row['aantalgelijk'] : 0 );
					$verloren  = isset( $row['verloren'] ) ? $row['verloren'] : ( isset( $row['aantalverloren'] ) ? $row['aantalverloren'] : 0 );
					$dv         = isset( $row['doelpuntenvoor'] ) ? $row['doelpuntenvoor'] : '0';
					$dt         = isset( $row['doelpuntentegen'] ) ? $row['doelpuntentegen'] : '0';
					$punten     = isset( $row['totaalpunten'] ) ? $row['totaalpunten'] : ( isset( $row['punten'] ) ? $row['punten'] : '0' );
					$positie    = isset( $row['positie'] ) ? $row['positie'] : $pos;
					$is_eboh    = ( stripos( $team_naam, 'EBOH' ) !== false );
					?>
					<tr<?php echo $is_eboh ? ' class="sportlink-table__row--highlight"' : ''; ?>>
						<td data-label="#" class="sportlink-table__pos"><?php echo esc_html( $positie ); ?></td>
						<td data-label="Team" class="sportlink-table__team"><?php echo eboh_v2_render_team_cell( $team_naam ); ?></td>
						<td data-label="GS"><?php echo esc_html( $gespeeld ); ?></td>
						<td data-label="W"><?php echo esc_html( $gewonnen ); ?></td>
						<td data-label="G"><?php echo esc_html( $gelijk ); ?></td>
						<td data-label="V"><?php echo esc_html( $verloren ); ?></td>
						<td data-label="DV"><?php echo esc_html( $dv ); ?></td>
						<td data-label="DT"><?php echo esc_html( $dt ); ?></td>
						<td data-label="Pnt" class="sportlink-table__pts"><?php echo esc_html( $punten ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
	return ob_get_clean();
}


/**
 * [eboh_teams teamsoort="" spelsoort="V"]
 */
add_shortcode( 'eboh_teams', 'eboh_teams_shortcode' );

function eboh_teams_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'teamsoort' => '',
		'spelsoort' => 'V',
	), $atts, 'eboh_teams' );

	$api = new EBOH_Sportlink_API();
	if ( ! $api->is_configured() ) {
		return eboh_sportlink_notice( 'Sportlink API is niet geconfigureerd.' );
	}

	$teams = $api->get_teams( $atts['teamsoort'], $atts['spelsoort'] );
	if ( is_wp_error( $teams ) ) {
		return eboh_sportlink_notice( $teams->get_error_message() );
	}

	if ( empty( $teams ) ) {
		return eboh_sportlink_notice( 'Geen teams gevonden.' );
	}

	// Group by teamsoort
	$grouped = array();
	foreach ( $teams as $team ) {
		$soort = isset( $team['teamsoort'] ) ? $team['teamsoort'] : 'Overig';
		// Map codes to readable names
		$soort_labels = array(
			'S' => 'Senioren',
			'J' => 'Junioren',
			'P' => 'Pupillen',
			'M' => 'Mini\'s',
		);
		$label = isset( $soort_labels[ $soort ] ) ? $soort_labels[ $soort ] : $soort;
		$grouped[ $label ][] = $team;
	}

	ob_start();
	foreach ( $grouped as $cat_name => $cat_teams ) :
		?>
		<div class="sportlink-team-group">
			<h3 class="sportlink-team-group__title"><?php echo esc_html( $cat_name ); ?></h3>
			<div class="sportlink-team-grid">
				<?php foreach ( $cat_teams as $team ) :
					$naam = isset( $team['teamnaam'] ) ? $team['teamnaam'] : '';
					$code = isset( $team['teamcode'] ) ? $team['teamcode'] : '';
					?>
					<div class="sportlink-team-card">
						<span class="sportlink-team-card__name"><?php echo esc_html( $naam ); ?></span>
						<span class="sportlink-team-card__info"><?php echo esc_html( $code ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach;

	return ob_get_clean();
}


/**
 * [eboh_overzicht team=""]
 * Complete overview: stand + programma + uitslagen
 */
add_shortcode( 'eboh_overzicht', 'eboh_overzicht_shortcode' );

function eboh_overzicht_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'team' => '',
	), $atts, 'eboh_overzicht' );

	$team = esc_attr( $atts['team'] );

	ob_start();
	?>
	<div class="sportlink-overzicht">
		<div class="sportlink-overzicht__section">
			<h3 class="sportlink-overzicht__title">Competitiestand</h3>
			<?php echo do_shortcode( '[eboh_stand team="' . $team . '"]' ); ?>
		</div>
		<div class="sportlink-overzicht__section">
			<h3 class="sportlink-overzicht__title">Komende wedstrijden</h3>
			<?php echo do_shortcode( '[eboh_programma team="' . $team . '" dagen="30"]' ); ?>
		</div>
		<div class="sportlink-overzicht__section">
			<h3 class="sportlink-overzicht__title">Uitslagen</h3>
			<?php echo do_shortcode( '[eboh_uitslagen team="' . $team . '" dagen="90"]' ); ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}


// =====================================================================
// 5. BACKWARD-COMPATIBLE SHORTCODE ALIASES
// =====================================================================

/**
 * Map the old [eboh_knvb_*] shortcodes to the new Sportlink versions.
 * This ensures existing pages continue to work without edits.
 */
add_shortcode( 'eboh_knvb_programma', 'eboh_knvb_programma_compat' );
function eboh_knvb_programma_compat( $atts ) {
	$atts = shortcode_atts( array(
		'team'       => '',
		'weeknummer' => 'A',
		'comptype'   => '',
	), $atts );

	return eboh_programma_shortcode( array(
		'team'  => $atts['team'],
		'dagen' => 30,
	) );
}

add_shortcode( 'eboh_knvb_uitslagen', 'eboh_knvb_uitslagen_compat' );
function eboh_knvb_uitslagen_compat( $atts ) {
	$atts = shortcode_atts( array(
		'team'       => '',
		'weeknummer' => 'A',
		'comptype'   => '',
	), $atts );

	return eboh_uitslagen_shortcode( array(
		'team'  => $atts['team'],
		'dagen' => 90,
	) );
}

add_shortcode( 'eboh_knvb_stand', 'eboh_knvb_stand_compat' );
function eboh_knvb_stand_compat( $atts ) {
	$atts = shortcode_atts( array(
		'team'     => '',
		'comptype' => '',
		'periode'  => '',
	), $atts );

	return eboh_stand_shortcode( array(
		'team' => $atts['team'],
	) );
}

add_shortcode( 'eboh_knvb_teams', 'eboh_knvb_teams_compat' );
function eboh_knvb_teams_compat( $atts ) {
	$atts = shortcode_atts( array(
		'categorie' => '',
	), $atts );

	return eboh_teams_shortcode( array() );
}

add_shortcode( 'eboh_knvb_overzicht', 'eboh_knvb_overzicht_compat' );
function eboh_knvb_overzicht_compat( $atts ) {
	$atts = shortcode_atts( array(
		'team' => '',
	), $atts );

	return eboh_overzicht_shortcode( array(
		'team' => $atts['team'],
	) );
}


// =====================================================================
// 6. HELPER FUNCTIONS
// =====================================================================

/**
 * Format Sportlink date to Dutch format.
 * Sportlink can return dates in various fields: wedstrijddatum, datum, etc.
 */
function eboh_sportlink_format_date( $match ) {
	// Try common date field names
	$date_str = '';
	$date_fields = array( 'wedstrijddatum', 'datum', 'wedstrijddatumtijd' );
	foreach ( $date_fields as $field ) {
		if ( ! empty( $match[ $field ] ) ) {
			$date_str = $match[ $field ];
			break;
		}
	}

	if ( empty( $date_str ) ) {
		return '';
	}

	$days   = array( 'zo', 'ma', 'di', 'wo', 'do', 'vr', 'za' );
	$months = array( '', 'jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec' );

	$ts = strtotime( $date_str );
	if ( $ts === false ) {
		return $date_str; // Return as-is if we can't parse it
	}

	$day   = $days[ (int) date( 'w', $ts ) ];
	$d     = date( 'j', $ts );
	$month = $months[ (int) date( 'n', $ts ) ];

	return ucfirst( $day ) . ' ' . $d . ' ' . $month;
}

/**
 * Render a notice/fallback message.
 */
function eboh_sportlink_notice( $message ) {
	return '<div class="sportlink-notice"><p>' . esc_html( $message ) . '</p></div>';
}


/**
 * Get the next home match for the homepage widget.
 * Uses the Sportlink programma endpoint, filters for EBOH home games.
 * Returns array with match details or null if no upcoming home match.
 */
function eboh_get_next_home_match() {
	$api = new EBOH_Sportlink_API();
	if ( ! $api->is_configured() ) {
		return null;
	}

	$programma = $api->get_programma( '', 60, 'V', '' );
	if ( is_wp_error( $programma ) || empty( $programma ) ) {
		return null;
	}

	// Filter for home matches where EBOH is thuisteam
	$home_matches = array();
	$now = time();
	foreach ( $programma as $match ) {
		$thuisteam = isset( $match['thuisteam'] ) ? $match['thuisteam'] : '';
        if ( strcasecmp( trim( $thuisteam ), 'EBOH 1' ) !== 0 ) {
            continue;
        }
		// Only future matches
		$datum_str = '';
		foreach ( array( 'wedstrijddatum', 'datum', 'wedstrijddatumtijd' ) as $f ) {
			if ( ! empty( $match[ $f ] ) ) { $datum_str = $match[ $f ]; break; }
		}
		$ts = $datum_str ? strtotime( $datum_str ) : 0;
		if ( $ts && $ts >= strtotime( 'today' ) ) {
			$match['_ts'] = $ts;
			$home_matches[] = $match;
		}
	}

	if ( empty( $home_matches ) ) {
		return null;
	}

	// Sort by date ascending
	usort( $home_matches, function( $a, $b ) {
		return $a['_ts'] - $b['_ts'];
	} );

	$match = $home_matches[0];
	$ts    = $match['_ts'];

	$days_nl   = array( 'Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag' );
	$months_nl = array( '', 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december' );

	$dag_naam   = $days_nl[ (int) date( 'w', $ts ) ];
	$dag_num    = date( 'j', $ts );
	$maand_naam = $months_nl[ (int) date( 'n', $ts ) ];
	$maand_kort = strtoupper( substr( $maand_naam, 0, 3 ) );

	$tijd = isset( $match['aanvangstijd'] ) ? $match['aanvangstijd'] : '';

	return array(
		'thuisteam'    => isset( $match['thuisteam'] ) ? $match['thuisteam'] : '',
		'uitteam'      => isset( $match['uitteam'] ) ? $match['uitteam'] : '',
		'datum'        => $dag_naam . ' ' . $dag_num . ' ' . $maand_naam,
		'datum_kort'   => strtoupper( $dag_naam ) . ' ' . $dag_num . ' ' . $maand_kort,
		'tijd'         => $tijd ? $tijd . ' UUR' : '',
		'competitie'   => isset( $match['competitienaam'] ) ? $match['competitienaam'] : '',
		'locatie'      => 'Sportpark De Bovenhoeck',
	);
}

/**
 * Get upcoming matches for a specific team.
 *
 * @param string $team_name  Team name as shown in Sportlink (e.g. 'EBOH 1').
 * @param int    $limit      Max results.
 * @return array
 */
function eboh_get_team_programma( $team_name, $limit = 5 ) {
    $api = new EBOH_Sportlink_API();
    if ( ! $api->is_configured() ) { return array(); }

    $programma = $api->get_programma( '', 60, 'V', '' );
    if ( is_wp_error( $programma ) || empty( $programma ) ) { return array(); }

    $matches = array();
    foreach ( $programma as $match ) {
        $thuis = isset( $match['thuisteam'] ) ? trim( $match['thuisteam'] ) : '';
        $uit   = isset( $match['uitteam'] )  ? trim( $match['uitteam'] )  : '';
        if ( strcasecmp( $thuis, $team_name ) !== 0 && strcasecmp( $uit, $team_name ) !== 0 ) { continue; }

        $datum_str = '';
        foreach ( array( 'wedstrijddatum', 'datum', 'wedstrijddatumtijd' ) as $f ) {
            if ( ! empty( $match[ $f ] ) ) { $datum_str = $match[ $f ]; break; }
        }
        $ts = $datum_str ? strtotime( $datum_str ) : 0;
        if ( $ts && $ts >= strtotime( 'today' ) ) {
            $match['_ts'] = $ts;
            $matches[] = $match;
        }
    }
    usort( $matches, function( $a, $b ) { return $a['_ts'] - $b['_ts']; } );
    return array_slice( $matches, 0, $limit );
}

/**
 * Get recent results for a specific team.
 *
 * @param string $team_name  Team name as shown in Sportlink.
 * @param int    $limit      Max results.
 * @return array
 */
function eboh_get_team_uitslagen( $team_name, $limit = 5 ) {
    $api = new EBOH_Sportlink_API();
    if ( ! $api->is_configured() ) { return array(); }

    $uitslagen = $api->get_uitslagen( '', 90 );
    if ( is_wp_error( $uitslagen ) || empty( $uitslagen ) ) { return array(); }

    $results = array();
    foreach ( $uitslagen as $match ) {
        $thuis = isset( $match['thuisteam'] ) ? trim( $match['thuisteam'] ) : '';
        $uit   = isset( $match['uitteam'] )  ? trim( $match['uitteam'] )  : '';
        if ( strcasecmp( $thuis, $team_name ) !== 0 && strcasecmp( $uit, $team_name ) !== 0 ) { continue; }
        $results[] = $match;
    }
    return array_slice( $results, 0, $limit );
}


/**
 * Resolve een (eventueel) logo voor een teamnaam. Geeft een URL terug of leeg.
 *
 * Voor EBOH-teams gebruiken we het eigen clublogo uit assets/. Andere clubs
 * kun je toevoegen via filter 'eboh_v2_team_logo_map' of via een per-team
 * filter 'eboh_v2_team_logo'. Wanneer er geen logo bekend is, geeft de
 * functie '' terug zodat de UI op een tekst-crest fallt.
 *
 * @param string $team_name  Bv. 'EBOH 1', 'CKC 1', 'Rijsoord JO17-1'.
 * @return string  Logo-URL of lege string.
 */
function eboh_v2_team_logo( $team_name ) {
    $team_name = trim( $team_name );
    if ( $team_name === '' ) { return ''; }

    $own_logo = get_template_directory_uri() . '/assets/images/logo-eboh.png';

    // Basis-map die je kunt uitbreiden zonder code te wijzigen.
    $map = apply_filters( 'eboh_v2_team_logo_map', array(
        'EBOH' => $own_logo,
    ) );

    // Eerst exacte teamnaam, daarna club-prefix (alles voor het laatste woord/getal).
    if ( isset( $map[ $team_name ] ) ) {
        return apply_filters( 'eboh_v2_team_logo', $map[ $team_name ], $team_name );
    }

    $clubnaam = eboh_v2_extract_clubnaam( $team_name );
    if ( $clubnaam && isset( $map[ $clubnaam ] ) ) {
        return apply_filters( 'eboh_v2_team_logo', $map[ $clubnaam ], $team_name );
    }

    return apply_filters( 'eboh_v2_team_logo', '', $team_name );
}

/**
 * Haal de clubnaam uit een teamnaam. 'EBOH JO17-2' → 'EBOH', 'Rijsoord 1' → 'Rijsoord'.
 */
function eboh_v2_extract_clubnaam( $team_name ) {
    $name = trim( $team_name );
    // Strip het laatste segment dat begint met een cijfer of JO/MA/MO/VR-code.
    $name = preg_replace( '/\s+(JO|MA|MO|VR|MU|JG)\d+(-\d+)?$/i', '', $name );
    $name = preg_replace( '/\s+\d+(-\d+)?$/', '', $name );
    return trim( $name );
}

/**
 * Render een team-cel met logo + naam. Gebruikt door programma/uitslagen/stand.
 *
 * @param string $team_name
 * @return string  HTML voor in een tabelcel.
 */
function eboh_v2_render_team_cell( $team_name ) {
    $team_name = trim( $team_name );
    if ( $team_name === '' ) { return ''; }
    $logo  = eboh_v2_team_logo( $team_name );
    $initial = mb_substr( $team_name, 0, 1 );
    ob_start();
    ?>
    <span class="sl-team">
        <span class="sl-team__crest" aria-hidden="true">
            <?php if ( $logo ) : ?>
                <img src="<?php echo esc_url( $logo ); ?>" alt="" loading="lazy">
            <?php else : ?>
                <span class="sl-team__crest-fallback"><?php echo esc_html( $initial ); ?></span>
            <?php endif; ?>
        </span>
        <span class="sl-team__name"><?php echo esc_html( $team_name ); ?></span>
    </span>
    <?php
    return ob_get_clean();
}


/**
 * Get current-season statistics for a specific team from the competition table.
 *
 * @param string $team_name  Team name as shown in Sportlink (e.g. 'EBOH 1').
 * @return array|null  Array with keys 'wedstrijden', 'gewonnen', 'gelijk', 'verloren',
 *                     'goals_voor', 'goals_tegen', 'positie', 'punten' — or null if
 *                     the team / competition cannot be resolved.
 */
function eboh_get_team_stats( $team_name ) {
    $api = new EBOH_Sportlink_API();
    if ( ! $api->is_configured() ) { return null; }

    $teamcode = $api->resolve_team( $team_name );
    if ( is_wp_error( $teamcode ) || empty( $teamcode ) ) { return null; }

    $stand = $api->get_stand_for_team( $teamcode );
    if ( is_wp_error( $stand ) || empty( $stand ) ) { return null; }

    $pos = 0;
    foreach ( $stand as $row ) {
        $pos++;
        $naam = isset( $row['teamnaam'] ) ? $row['teamnaam'] : ( isset( $row['naam'] ) ? $row['naam'] : '' );
        if ( strcasecmp( trim( $naam ), $team_name ) !== 0 ) { continue; }

        return array(
            'wedstrijden'  => isset( $row['gespeeldewedstrijden'] ) ? intval( $row['gespeeldewedstrijden'] ) : ( isset( $row['aantalgespeeld'] ) ? intval( $row['aantalgespeeld'] ) : 0 ),
            'gewonnen'     => isset( $row['gewonnen'] ) ? intval( $row['gewonnen'] ) : ( isset( $row['aantalgewonnen'] ) ? intval( $row['aantalgewonnen'] ) : 0 ),
            'gelijk'       => isset( $row['gelijk'] ) ? intval( $row['gelijk'] ) : ( isset( $row['aantalgelijk'] ) ? intval( $row['aantalgelijk'] ) : 0 ),
            'verloren'     => isset( $row['verloren'] ) ? intval( $row['verloren'] ) : ( isset( $row['aantalverloren'] ) ? intval( $row['aantalverloren'] ) : 0 ),
            'goals_voor'   => isset( $row['doelpuntenvoor'] ) ? intval( $row['doelpuntenvoor'] ) : 0,
            'goals_tegen'  => isset( $row['doelpuntentegen'] ) ? intval( $row['doelpuntentegen'] ) : 0,
            'punten'       => isset( $row['totaalpunten'] ) ? intval( $row['totaalpunten'] ) : ( isset( $row['punten'] ) ? intval( $row['punten'] ) : 0 ),
            'positie'      => isset( $row['positie'] ) ? intval( $row['positie'] ) : $pos,
        );
    }

    return null;
}


// =====================================================================
// 7. FRONTEND CSS
// =====================================================================

add_action( 'wp_head', 'eboh_sportlink_inline_styles' );

function eboh_sportlink_inline_styles() {
	?>
	<style id="eboh-sportlink-css">
		/* Sportlink Table Base */
		.sportlink-table-wrap {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
			margin: 0 0 24px;
		}
		.sportlink-table {
			width: 100%;
			border-collapse: collapse;
			font-family: 'Work Sans', sans-serif;
			font-size: 14px;
			line-height: 1.5;
		}
		.sportlink-table thead {
			background-color: var(--dark-section, #343B41);
			color: #fff;
		}
		.sportlink-table th {
			padding: 10px 12px;
			text-align: left;
			font-family: 'Oswald', sans-serif;
			font-size: 12px;
			font-weight: 500;
			text-transform: uppercase;
			letter-spacing: 0.06em;
			white-space: nowrap;
		}
		.sportlink-table td {
			padding: 10px 12px;
			border-bottom: 1px solid #e8eaed;
			vertical-align: middle;
		}
		.sportlink-table tbody tr:hover {
			background-color: #f4f5f7;
		}

		/* Team cell */
		.sportlink-table__team {
			white-space: nowrap;
		}
		.sportlink-table__vs {
			text-align: center;
			font-weight: 700;
			color: #999;
			padding: 10px 4px;
		}

		/* Score */
		.sportlink-table__score {
			text-align: center;
			font-weight: 700;
			font-family: 'Oswald', sans-serif;
			font-size: 16px;
			letter-spacing: 0.03em;
			min-width: 60px;
		}
		.sportlink-table__score-header {
			text-align: center;
		}

		/* Stand specific */
		.sportlink-table__pos {
			text-align: center;
			font-weight: 700;
			width: 32px;
		}
		.sportlink-table__pts {
			text-align: center;
			font-weight: 700;
			font-family: 'Oswald', sans-serif;
			font-size: 16px;
		}
		.sportlink-table__row--highlight {
			background-color: rgba(232, 8, 8, 0.06) !important;
			font-weight: 600;
		}
		.sportlink-table__row--highlight td {
			border-bottom-color: rgba(232, 8, 8, 0.15);
		}

		/* Status badges */
		.sportlink-status {
			display: inline-block;
			background-color: #f0ad4e;
			color: #fff;
			font-size: 10px;
			font-weight: 700;
			text-transform: uppercase;
			padding: 2px 6px;
			border-radius: 3px;
			letter-spacing: 0.05em;
		}
		.sportlink-table__row--status {
			opacity: 0.7;
		}

		/* Notice */
		.sportlink-notice {
			padding: 16px 20px;
			background: #f8f9fa;
			border-left: 4px solid var(--primary-red, #E80808);
			border-radius: 2px;
			font-size: 14px;
			color: #555;
			margin: 0 0 24px;
		}

		/* Team grid */
		.sportlink-team-group {
			margin-bottom: 32px;
		}
		.sportlink-team-group__title {
			font-family: 'Oswald', sans-serif;
			text-transform: uppercase;
			font-size: 18px;
			letter-spacing: 0.06em;
			padding-bottom: 8px;
			border-bottom: 3px solid var(--primary-red, #E80808);
			margin-bottom: 16px;
		}
		.sportlink-team-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
			gap: 10px;
		}
		.sportlink-team-card {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 10px 14px;
			background: #fff;
			border: 1px solid #e8eaed;
			border-radius: 4px;
			font-size: 14px;
		}
		.sportlink-team-card__name {
			font-weight: 600;
		}
		.sportlink-team-card__info {
			font-size: 12px;
			color: #888;
			text-transform: uppercase;
		}

		/* Overzicht sections */
		.sportlink-overzicht__section {
			margin-bottom: 40px;
		}
		.sportlink-overzicht__title {
			font-family: 'Oswald', sans-serif;
			text-transform: uppercase;
			font-size: 20px;
			letter-spacing: 0.06em;
			padding-bottom: 8px;
			border-bottom: 3px solid var(--primary-red, #E80808);
			margin-bottom: 16px;
		}

		/* Responsive */
		@media (max-width: 640px) {
			.sportlink-table__extra {
				display: none;
			}
			.sportlink-table th,
			.sportlink-table td {
				padding: 8px 6px;
				font-size: 13px;
			}
		}
	</style>
	<?php
}
