<?php
/**
 * Plugin Name: Riverbend Design
 * Description: Government-portal look for the Riverbend demo: seal header, navy menu bar, breadcrumbs, content card and a Quick Links / Upcoming Events sidebar. Riverbend is fictional.
 */

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'riverbend-fonts', 'https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap', array(), null );
		$file = __DIR__ . '/riverbend-design/design.css';
		wp_enqueue_style( 'riverbend-design', WPMU_PLUGIN_URL . '/riverbend-design/design.css', array(), filemtime( $file ) );
	},
	100
);

/**
 * Page URL by title, so links survive a site rebuild.
 */
function rb_page_url( $title ) {
	$page = get_page_by_path( sanitize_title( $title ) );
	return $page ? get_permalink( $page ) : home_url( '/' );
}

// ---------------------------------------------------------------------------
// Header top section: seal, name, board officers, utility links, search.
// ---------------------------------------------------------------------------

add_action(
	'astra_masthead_top',
	function () {
		$seal = WPMU_PLUGIN_URL . '/riverbend-design/seal.svg';
		?>
		<div class="rb-topbar">
			<div class="rb-topbar-inner">
				<div class="rb-brand">
					<a class="rb-brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img class="rb-seal" src="<?php echo esc_url( $seal ); ?>" alt="" width="112" height="112">
						<span class="rb-brand-text">
							<span class="rb-kicker">Serving the seven-county region</span>
							<span class="rb-name">Riverbend Regional Commission</span>
						</span>
					</a>
					<p class="rb-chair">Dana Whitfield, Board Chair <span class="rb-demo">Demo site</span></p>
				</div>

				<div class="rb-officers">
					<p class="rb-officers-title">Board Officers</p>
					<ul>
						<li>Luis Ortega, Vice Chair</li>
						<li>Janelle Brooks, Secretary</li>
						<li>Raymond Cole, Treasurer</li>
						<li>Marcus Ellery, Executive Director</li>
					</ul>
				</div>

				<div class="rb-utility">
					<nav aria-label="Utility">
						<ul>
							<li><a href="<?php echo esc_url( wp_login_url() ); ?>">Login</a></li>
							<li><a href="<?php echo esc_url( rb_page_url( 'Meetings' ) ); ?>">Meetings</a></li>
							<li><a href="<?php echo esc_url( rb_page_url( 'Public Comment' ) ); ?>">Contact</a></li>
						</ul>
					</nav>
					<form class="rb-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label for="rb-search-input" class="screen-reader-text">Search this site</label>
						<input id="rb-search-input" type="search" name="s" placeholder="Search" value="<?php echo esc_attr( get_search_query() ); ?>">
						<button type="submit">
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><circle cx="10" cy="10" r="6.5" fill="none" stroke="currentColor" stroke-width="2.6"/><line x1="15" y1="15" x2="21" y2="21" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/></svg>
							<span class="screen-reader-text">Search</span>
						</button>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
);

// ---------------------------------------------------------------------------
// Breadcrumbs above the content.
// ---------------------------------------------------------------------------

add_action(
	'astra_content_before',
	function () {
		if ( is_front_page() ) {
			return;
		}

		$crumbs = array( array( 'Home', home_url( '/' ) ) );
		if ( is_singular( 'post' ) ) {
			$crumbs[] = array( 'News', get_permalink( get_option( 'page_for_posts' ) ) );
			$current  = get_the_title();
		} elseif ( is_home() ) {
			$current = 'News';
		} elseif ( is_search() ) {
			$current = 'Search results';
		} elseif ( is_singular() ) {
			$current = get_the_title();
		} else {
			$current = wp_get_document_title();
		}
		?>
		<div class="rb-breadcrumb-bar">
			<nav class="rb-breadcrumb" aria-label="Breadcrumb">
				<ol>
					<?php foreach ( $crumbs as $crumb ) : ?>
						<li><a href="<?php echo esc_url( $crumb[1] ); ?>"><?php echo esc_html( $crumb[0] ); ?></a></li>
					<?php endforeach; ?>
					<li aria-current="page"><?php echo esc_html( $current ); ?></li>
				</ol>
			</nav>
		</div>
		<?php
	}
);

// ---------------------------------------------------------------------------
// Sidebar: Quick Links and Upcoming Events.
// ---------------------------------------------------------------------------

/**
 * Sidebar on regular pages, posts and the news list. Not on the Elementor home page,
 * and not on Beaver Builder pages (their wide legacy layouts need the full width).
 */
function rb_wants_sidebar() {
	if ( is_front_page() ) {
		return false;
	}
	if ( is_singular() && get_post_meta( get_queried_object_id(), '_fl_builder_enabled', true ) ) {
		return false;
	}
	return true;
}

add_filter(
	'astra_page_layout',
	function ( $layout ) {
		return rb_wants_sidebar() ? 'right-sidebar' : 'no-sidebar';
	},
	99
);

// News and posts use the same plain layout as pages, so every page gets the same card.
add_filter(
	'astra_get_content_layout',
	function ( $layout ) {
		return ( is_singular( 'post' ) || is_home() || is_archive() || is_search() ) ? 'plain-container' : $layout;
	},
	99
);

foreach ( array( 'site-content-style', 'single-post-content-style', 'archive-post-content-style', 'site-sidebar-style', 'single-post-sidebar-style', 'archive-post-sidebar-style' ) as $rb_option ) {
	add_filter(
		"astra_get_option_{$rb_option}",
		function () {
			return 'unboxed';
		}
	);
}

// Our panel replaces WordPress's default sidebar widgets (Search, Recent Posts, ...).
add_filter(
	'sidebars_widgets',
	function ( $widgets ) {
		if ( ! is_admin() ) {
			$widgets['sidebar-1'] = array();
		}
		return $widgets;
	}
);

add_action(
	'astra_sidebars_before',
	function () {
		$events = array(
			array( '2026-10-30T18:00', 'Oct', '30', 'October 30', '6:00 pm', 'Public hearing: Regional Transportation Plan 2050' ),
			array( '2026-11-18T10:00', 'Nov', '18', 'November 18', '10:00 am', 'Board meeting' ),
			array( '2026-12-09T10:00', 'Dec', '9', 'December 9', '10:00 am', 'Board meeting and 2027 budget' ),
		);
		?>
		<section class="rb-panel" aria-labelledby="rb-quick-title">
			<h2 id="rb-quick-title" class="rb-panel-title">Quick Links</h2>
			<ul class="rb-quick">
				<li><a href="<?php echo esc_url( rb_page_url( 'Public Comment' ) ); ?>">Submit a public comment</a></li>
				<li><a href="<?php echo esc_url( rb_page_url( 'Meetings' ) ); ?>">Board meeting schedule</a></li>
				<li><a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">Latest news</a></li>
				<li><a href="<?php echo esc_url( rb_page_url( 'About' ) ); ?>">About the Commission</a></li>
			</ul>

			<div class="rb-events">
				<h2 class="rb-panel-title">Upcoming Events</h2>
				<ul>
					<?php foreach ( $events as $e ) : ?>
						<li>
							<span class="rb-date" aria-hidden="true"><span><?php echo esc_html( $e[1] ); ?></span><span><?php echo esc_html( $e[2] ); ?></span></span>
							<span class="rb-event-text">
								<time datetime="<?php echo esc_attr( $e[0] ); ?>"><span class="screen-reader-text"><?php echo esc_html( $e[3] ); ?>, </span><?php echo esc_html( $e[4] ); ?></time>
								<span><?php echo esc_html( $e[5] ); ?></span>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php
	}
);
