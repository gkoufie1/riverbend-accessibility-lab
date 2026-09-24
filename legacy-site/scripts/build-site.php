<?php
/**
 * Builds the "inherited legacy" Riverbend demo site with barriers B01-B13 planted.
 * Run via: wp eval-file /scripts/build-site.php <hero_id> <carousel_id_1> ... <carousel_id_4>
 *
 * Barrier IDs match audit-lab/README.md. Do NOT fix anything here; fixing is the lab.
 */

wp_set_current_user( 1 );

$hero_id      = (int) $args[0];
$carousel_ids = array_map( 'intval', array_slice( $args, 1, 4 ) );

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function rb_id() {
	return substr( md5( uniqid( '', true ) ), 0, 7 );
}

function rb_widget( $type, $settings ) {
	return array(
		'id'         => rb_id(),
		'elType'     => 'widget',
		'widgetType' => $type,
		'settings'   => $settings,
		'elements'   => array(),
	);
}

function rb_container( $widgets ) {
	return array(
		'id'       => rb_id(),
		'elType'   => 'container',
		'isInner'  => false,
		'settings' => array( 'content_width' => 'boxed', 'flex_direction' => 'column' ),
		'elements' => $widgets,
	);
}

function rb_page( $title, $content = '' ) {
	return wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_content' => $content,
		)
	);
}

// Clear out the default content.
foreach ( array( 'Hello world!', 'Sample Page' ) as $title ) {
	$old = get_posts( array( 'title' => $title, 'post_type' => array( 'post', 'page' ), 'post_status' => 'any' ) );
	foreach ( $old as $p ) {
		wp_delete_post( $p->ID, true );
	}
}

update_option( 'permalink_structure', '/%postname%/' );
$GLOBALS['wp_rewrite']->init();

// ---------------------------------------------------------------------------
// Pages
// ---------------------------------------------------------------------------

$home_id     = rb_page( 'Home' );
$about_id    = rb_page( 'About' );
$meetings_id = rb_page( 'Meetings' );
$comment_id  = rb_page( 'Public Comment' );
$news_id     = rb_page( 'News' );

// Pages must exist before permalinks resolve.
flush_rewrite_rules( false );
$comment_url  = get_permalink( $comment_id );
$meetings_url = get_permalink( $meetings_id );

// ---------------------------------------------------------------------------
// Home: Elementor (B01-B05, B07 in the intro link)
// ---------------------------------------------------------------------------

$carousel = array();
foreach ( $carousel_ids as $cid ) {
	$carousel[] = array( 'id' => $cid, 'url' => wp_get_attachment_url( $cid ) );
}

$social = array();
foreach ( array( 'facebook', 'x-twitter', 'instagram', 'youtube' ) as $net ) {
	$social[] = array(
		'_id'         => rb_id(),
		'social_icon' => array( 'value' => "fab fa-$net", 'library' => 'fa-brands' ),
		'link'        => array( 'url' => "https://example.com/riverbend-$net", 'is_external' => '', 'nofollow' => '' ),
	);
}

$elementor_data = array(
	// B01: event details exist only inside the image.
	rb_container(
		array(
			rb_widget(
				'image',
				array(
					'image'      => array( 'id' => $hero_id, 'url' => wp_get_attachment_url( $hero_id ) ),
					'image_size' => 'full',
				)
			),
		)
	),
	rb_container(
		array(
			rb_widget( 'heading', array( 'title' => 'Welcome to the Riverbend Regional Commission', 'header_size' => 'h2' ) ),
			rb_widget(
				'text-editor',
				array(
					'editor' => '<p>The Riverbend Regional Commission plans for transportation, water, aging services and growth across our seven-county region. '
						. 'Residents are encouraged to attend <a href="' . esc_url( $meetings_url ) . '">upcoming board meetings</a> and share their views on the Regional Transportation Plan 2050.</p>'
						. '<p><em>This is a demo site built for accessibility training. Riverbend Regional Commission is not a real agency.</em></p>',
				)
			),
			// B03: clickable div, no role, not focusable.
			rb_widget(
				'html',
				array(
					'html' => '<div class="fake-btn" onclick="location.href=\'' . esc_url( $comment_url ) . '\'" style="background:#0a6;color:#fff;padding:12px 20px;display:inline-block;cursor:pointer;border-radius:4px">Submit a Comment</div>',
				)
			),
		)
	),
	rb_container(
		array(
			rb_widget( 'heading', array( 'title' => 'Around the Region', 'header_size' => 'h2' ) ),
			// B02: autoplay with no pause, no navigation.
			rb_widget(
				'image-carousel',
				array(
					'carousel'             => $carousel,
					'thumbnail_size'       => 'full',
					'slides_to_show'       => '1',
					'navigation'           => 'none',
					'autoplay'             => 'yes',
					'autoplay_speed'       => 2000,
					'pause_on_hover'       => 'no',
					'pause_on_interaction' => 'no',
					'infinite'             => 'yes',
					'image_stretch'        => 'yes',
				)
			),
		)
	),
	rb_container(
		array(
			rb_widget( 'heading', array( 'title' => 'Where We Meet', 'header_size' => 'h2' ) ),
			// B05: iframe without a title.
			rb_widget(
				'html',
				array(
					'html' => '<iframe src="https://www.openstreetmap.org/export/embed.html?bbox=-84.6%2C33.6%2C-84.2%2C33.9&amp;layer=mapnik" width="600" height="400" style="border:0;max-width:100%"></iframe>',
				)
			),
		)
	),
	rb_container(
		array(
			rb_widget( 'text-editor', array( 'editor' => '<p>Follow the Commission</p>' ) ),
			// B04: 10px icons with no spacing.
			rb_widget(
				'social-icons',
				array(
					'social_icon_list' => $social,
					'icon_size'        => array( 'unit' => 'px', 'size' => 10 ),
					'icon_padding'     => array( 'unit' => 'em', 'size' => 0.1 ),
					'icon_spacing'     => array( 'unit' => 'px', 'size' => 0 ),
					'align'            => 'left',
				)
			),
		)
	),
);

update_post_meta( $home_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $home_id, '_elementor_template_type', 'wp-page' );
update_post_meta( $home_id, '_elementor_version', ELEMENTOR_VERSION );
update_post_meta( $home_id, '_wp_page_template', 'elementor_header_footer' );
update_post_meta( $home_id, '_elementor_data', wp_slash( wp_json_encode( $elementor_data ) ) );

// ---------------------------------------------------------------------------
// About: Beaver Builder (B10 fake/skipped headings, B11 fixed width)
// ---------------------------------------------------------------------------

// Outside the builder UI, Beaver Builder writes nodes straight to the published layout.
FLBuilderModel::set_post_id( $about_id );
$row    = FLBuilderModel::add_row( '1-col' );
$groups = FLBuilderModel::get_nodes( 'column-group', $row );
$cols   = FLBuilderModel::get_nodes( 'column', array_shift( $groups ) );
$col    = array_shift( $cols )->node;

FLBuilderModel::add_module( 'heading', (object) array( 'heading' => 'About the Commission', 'tag' => 'h2' ), $col );
FLBuilderModel::add_module(
	'rich-text',
	(object) array(
		'text' => '<p>Founded in 1971, the Riverbend Regional Commission brings seven counties and 42 cities together to plan for the region\'s future. We coordinate transportation funding, protect regional water supplies, support older adults and help local governments plan for growth.</p>',
	),
	$col
);
// B10: H2 jumps straight to H4.
FLBuilderModel::add_module( 'heading', (object) array( 'heading' => 'Leadership', 'tag' => 'h4' ), $col );
FLBuilderModel::add_module(
	'rich-text',
	(object) array(
		'text' => '<p>Board Chair: Dana Whitfield<br>Executive Director: Marcus Ellery<br>Director of Transportation Planning: Priya Raman</p>',
	),
	$col
);
// B10: bold paragraphs pretending to be headings.
FLBuilderModel::add_module(
	'rich-text',
	(object) array(
		'text' => '<p><strong>Our History</strong></p><p>The Commission began as a small planning office serving three counties. Today it is the designated metropolitan planning organization for the Riverbend region.</p>'
			. '<p><strong>Member Counties</strong></p><p>Ashford, Belmont, Cedar, Dunmore, Elkhart, Fairview and Glenwood counties.</p>',
	),
	$col
);
// B11: fixed 900px width forces sideways scrolling on small screens.
FLBuilderModel::add_module(
	'html',
	(object) array(
		'html' => '<div style="width:900px;border:1px solid #ccc;padding:10px">Notice: The Riverbend Regional Commission board will meet on October 30 at 6 PM at the Riverbend Civic Center. The meeting includes a public hearing on the Regional Transportation Plan 2050. Written comments received by October 28 will be shared with board members before the vote.</div>',
	),
	$col
);
update_post_meta( $about_id, '_fl_builder_enabled', true );

// ---------------------------------------------------------------------------
// Public Comment form: Contact Form 7 (B13 placeholder-only fields)
// ---------------------------------------------------------------------------

$form = WPCF7_ContactForm::get_template( array( 'title' => 'Public Comment' ) );
$form->set_properties(
	array(
		'form'                => '[text* your-name placeholder "Name"]' . "\n\n"
			. '[email* your-email placeholder "Email"]' . "\n\n"
			. '[textarea* your-message placeholder "Your comment"]' . "\n\n"
			. '[submit "Send"]',
		// Local demo: accept submissions without trying to send mail.
		'additional_settings' => 'demo_mode: on',
	)
);
$form->save();
$form_id = $form->id();

wp_update_post(
	array(
		'ID'           => $comment_id,
		'post_content' => "<!-- wp:paragraph -->\n<p>Share your comments on the Regional Transportation Plan 2050. All comments become part of the public record.</p>\n<!-- /wp:paragraph -->\n\n"
			. "<!-- wp:shortcode -->\n[contact-form-7 id=\"$form_id\" title=\"Public Comment\"]\n<!-- /wp:shortcode -->",
	)
);

// ---------------------------------------------------------------------------
// Meetings: block editor (B12 table with no header cells)
// ---------------------------------------------------------------------------

$rows = array(
	array( 'Date', 'Time', 'Location', 'Topic' ),
	array( 'Oct 30, 2026', '6:00 PM', 'Riverbend Civic Center', 'Public hearing: Regional Transportation Plan 2050' ),
	array( 'Nov 18, 2026', '10:00 AM', 'Commission Offices, Room 2A', 'Board meeting' ),
	array( 'Dec 9, 2026', '10:00 AM', 'Commission Offices, Room 2A', 'Board meeting and 2027 budget' ),
	array( 'Jan 20, 2027', '10:00 AM', 'Commission Offices, Room 2A', 'Board meeting' ),
);
$tbody = '';
foreach ( $rows as $r ) {
	$tbody .= '<tr><td>' . implode( '</td><td>', array_map( 'esc_html', $r ) ) . '</td></tr>';
}

wp_update_post(
	array(
		'ID'           => $meetings_id,
		'post_content' => "<!-- wp:paragraph -->\n<p>All board meetings are open to the public. Agendas are posted one week before each meeting.</p>\n<!-- /wp:paragraph -->\n\n"
			. "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table class=\"has-fixed-layout\"><tbody>$tbody</tbody></table></figure>\n<!-- /wp:table -->\n\n"
			. "<!-- wp:paragraph -->\n<p>Need an accommodation to attend? Contact the Commission at least 72 hours before the meeting.</p>\n<!-- /wp:paragraph -->",
	)
);

// ---------------------------------------------------------------------------
// News posts (B13 "click here" links)
// ---------------------------------------------------------------------------

$posts = array(
	array(
		'Northside Trail Extension Moves to Design Phase',
		'<p>The Commission board approved funding to design a 4-mile extension of the Northside Trail, connecting three neighborhoods to the Cedar County transit center.</p><p>To see the meeting where the project was approved, <a href="' . esc_url( $meetings_url ) . '">click here</a>.</p>',
	),
	array(
		'Riverbend Transit Study: Share Your Input',
		'<p>We want to hear how you get around the region. The Riverbend Transit Study will shape bus and rail priorities for the next decade.</p><p>To submit a comment, <a href="' . esc_url( $comment_url ) . '">click here</a>.</p>',
	),
	array(
		'2027 Board Meeting Schedule Released',
		'<p>The Commission has published its board meeting calendar for the coming year. Meetings are held at the Commission offices unless noted.</p><p>For the full schedule, <a href="' . esc_url( $meetings_url ) . '">click here</a>.</p>',
	),
);
foreach ( $posts as $i => $p ) {
	wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => $p[0],
			'post_content' => "<!-- wp:html -->\n{$p[1]}\n<!-- /wp:html -->",
			'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( $i * 9 + 2 ) . ' days' ) ),
		)
	);
}

// ---------------------------------------------------------------------------
// Reading settings and menu
// ---------------------------------------------------------------------------

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
update_option( 'page_for_posts', $news_id );

$menu_id = wp_create_nav_menu( 'Main Menu' );
foreach ( array( $home_id, $about_id, $meetings_id, $comment_id, $news_id ) as $pid ) {
	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-object-id' => $pid,
			'menu-item-object'    => 'page',
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		)
	);
}
set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu_id, 'mobile_menu' => $menu_id ) );

// ---------------------------------------------------------------------------
// Theme styling: B06 low-contrast text, B07/B08/B09 in Additional CSS
// ---------------------------------------------------------------------------

$astra = get_option( 'astra-settings', array() );
if ( ! is_array( $astra ) ) {
	$astra = array();
}
$astra['text-color'] = '#999999'; // Customizer > Global > Colors > Text
update_option( 'astra-settings', $astra );

wp_update_custom_css_post(
	"/* ---- Styles from previous vendor (2019). Do not remove without testing. ---- */\n\n"
	. "/* Cleaner look: no underlines */\n"
	. "a { text-decoration: none; }\n\n"
	. "/* Client asked us to remove the blue boxes around links */\n"
	. "*:focus, *:focus-visible { outline: none !important; box-shadow: none !important; }\n\n"
	. "/* Sticky header */\n"
	. "#masthead { position: sticky; top: 0; z-index: 999; min-height: 160px; background: #fff; }\n",
	array( 'stylesheet' => 'astra' )
);

flush_rewrite_rules( false );
if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

WP_CLI::success( "Site built. Home=$home_id About=$about_id Meetings=$meetings_id Comment=$comment_id News=$news_id Form=$form_id" );
