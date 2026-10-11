<?php
/**
 * Hero artwork for the three technology brand pages.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Milesight hero illustration.
 */
function site_blocks_tech_brand_visual_milesight(): void {
	?>
	<div class="sg-tech__frame" role="img" aria-label="<?php esc_attr_e( 'A ceiling camera covering a commercial entrance, with a person, a vehicle, and connected sensors.', 'site-blocks' ); ?>">
		<svg viewBox="0 0 520 420" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
			<defs>
				<pattern id="sg-ms-grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M40 0H0V40" fill="none" stroke="rgb(255 255 255 / 7%)"/></pattern>
				<pattern id="sg-ms-win" width="16" height="22" patternUnits="userSpaceOnUse"><rect x="3" y="4" width="9" height="13" fill="rgb(255 255 255 / 7%)"/></pattern>
				<linearGradient id="sg-ms-sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0c2d44"/><stop offset="1" stop-color="#071e2e"/></linearGradient>
				<linearGradient id="sg-ms-cone" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1769a1" stop-opacity=".35"/><stop offset="1" stop-color="#1769a1" stop-opacity="0"/></linearGradient>
				<radialGradient id="sg-ms-dome" cx="35%" cy="30%" r="80%"><stop offset="0" stop-color="#6a737e"/><stop offset=".5" stop-color="#262b33"/><stop offset="1" stop-color="#0c0f12"/></radialGradient>
			</defs>
			<rect width="520" height="420" fill="url(#sg-ms-sky)"/>
			<rect width="520" height="420" fill="url(#sg-ms-grid)"/>
			<g fill="#12263a" stroke="rgb(255 255 255 / 10%)"><rect x="28" y="196" width="112" height="170"/><rect x="152" y="142" width="92" height="224"/><rect x="256" y="224" width="128" height="142"/></g>
			<g fill="url(#sg-ms-win)"><rect x="28" y="196" width="112" height="170"/><rect x="152" y="142" width="92" height="224"/><rect x="256" y="224" width="128" height="142"/></g>
			<rect y="364" width="520" height="56" fill="#071e2e"/>
			<path d="M0 364H520M260 364L120 420M260 364V420M260 364L400 420" fill="none" stroke="rgb(255 255 255 / 14%)"/>
			<path d="M430 74L150 364H410Z" fill="url(#sg-ms-cone)"/>
			<path d="M430 74L318 104L96 118M430 74L470 262" fill="none" stroke="#1769a1" stroke-dasharray="4 6" opacity=".7"/>
			<g fill="#1769a1">
				<circle cx="96" cy="118" r="5"/><circle cx="318" cy="104" r="5"/><circle cx="470" cy="262" r="5"/>
			</g>
			<text x="108" y="140" class="sg-txt-soft">SENSOR</text>
			<text x="330" y="126" class="sg-txt-soft">SENSOR</text>
			<text x="406" y="288" class="sg-txt-soft">SENSOR</text>
			<path d="M430 0V36" stroke="#3a414a" stroke-width="10"/>
			<rect x="414" y="30" width="32" height="12" rx="3" fill="#2b3139"/>
			<circle cx="430" cy="74" r="40" fill="url(#sg-ms-dome)" stroke="rgb(255 255 255 / 18%)"/>
			<circle cx="430" cy="74" r="24" fill="#07090b" stroke="rgb(255 255 255 / 15%)"/>
			<circle cx="430" cy="74" r="12" fill="#10161d" class="sg-acc"/>
			<rect x="168" y="270" width="40" height="90" rx="3" class="sg-acc" stroke-width="1.5"/>
			<rect x="168" y="252" width="60" height="18" class="sg-brand-fill"/><text x="174" y="265" class="sg-txt">PERSON</text>
			<rect x="262" y="314" width="118" height="46" rx="3" class="sg-acc" stroke-width="1.5"/>
			<rect x="262" y="296" width="70" height="18" class="sg-brand-fill"/><text x="268" y="309" class="sg-txt">VEHICLE</text>
			<rect x="24" y="24" width="138" height="26" rx="13" class="sg-acc" stroke-width="1.2"/>
			<circle cx="40" cy="37" r="3.5" class="sg-acc-fill"/>
			<text x="52" y="41" class="sg-txt-soft" fill="#1769a1">EDGE ANALYTICS</text>
		</svg>
	</div>
	<?php
}

/**
 * Hanwha Vision hero illustration.
 */
function site_blocks_tech_brand_visual_hanwha(): void {
	?>
	<div class="sg-tech__frame" role="img" aria-label="<?php esc_attr_e( 'A camera lens in a viewfinder, marked for distance and light.', 'site-blocks' ); ?>">
		<svg viewBox="0 0 520 500" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
			<defs>
				<linearGradient id="sg-hw-bg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0c2d44"/><stop offset="1" stop-color="#071e2e"/></linearGradient>
				<radialGradient id="sg-hw-glow" cx="50%" cy="50%" r="55%"><stop offset="0" stop-color="#1769a1" stop-opacity=".28"/><stop offset="1" stop-color="#1769a1" stop-opacity="0"/></radialGradient>
				<radialGradient id="sg-hw-glass" cx="38%" cy="32%" r="80%"><stop offset="0" stop-color="#2d3a44"/><stop offset=".55" stop-color="#141c24"/><stop offset="1" stop-color="#071018"/></radialGradient>
			</defs>
			<rect width="520" height="500" fill="url(#sg-hw-bg)"/>
			<rect width="520" height="500" fill="url(#sg-hw-glow)"/>
			<path d="M173 0V500M347 0V500M0 167H520M0 333H520" stroke="rgb(255 255 255 / 8%)" stroke-dasharray="3 7"/>
			<path d="M28 72V28H72M448 28H492V72M28 428V472H72M448 472H492V428" class="sg-acc" stroke-width="2"/>
			<circle cx="260" cy="250" r="168" fill="#0e1820" stroke="rgb(255 255 255 / 12%)"/>
			<circle cx="260" cy="250" r="122" fill="#080f14" stroke="rgb(255 255 255 / 16%)"/>
			<circle cx="260" cy="250" r="96" fill="url(#sg-hw-glass)"/>
			<circle cx="260" cy="250" r="96" class="sg-acc" stroke-width="2" opacity=".7"/>
			<circle cx="260" cy="250" r="36" fill="#05080c"/>
			<circle cx="260" cy="250" r="14" fill="#000"/>
			<ellipse cx="222" cy="212" rx="32" ry="13" fill="rgb(255 255 255 / 16%)" transform="rotate(-35 222 212)"/>
			<path d="M92 452H428" fill="none" stroke="rgb(255 255 255 / 35%)"/>
			<text x="260" y="444" text-anchor="middle" class="sg-txt-soft">DISTANCE</text>
			<text x="48" y="56" class="sg-txt-soft" fill="#1769a1">FRAME</text>
		</svg>
	</div>
	<?php
}

/**
 * NX Witness interface diagram.
 */
function site_blocks_tech_brand_visual_nx(): void {
	$tiles = array(
		__( 'Camera 01 · Entrance', 'site-blocks' ),
		__( 'Camera 02 · Lobby', 'site-blocks' ),
		__( 'Camera 03 · Car park', 'site-blocks' ),
		__( 'Camera 04 · Corridor', 'site-blocks' ),
		__( 'Camera 05 · Perimeter', 'site-blocks' ),
		__( 'Camera 06 · Warehouse', 'site-blocks' ),
	);
	?>
	<div class="sg-tech__frame sg-tech__nx" role="img" aria-label="<?php esc_attr_e( 'A video-management screen: sites, six camera tiles, events, and a recording timeline.', 'site-blocks' ); ?>">
		<div class="sg-tech__nx-top">
			<span class="sg-tech__nx-tabs"><span class="is-on"><?php esc_html_e( 'Live', 'site-blocks' ); ?></span><span><?php esc_html_e( 'Review', 'site-blocks' ); ?></span><span><?php esc_html_e( 'Events', 'site-blocks' ); ?></span><span><?php esc_html_e( 'Map', 'site-blocks' ); ?></span></span>
		</div>
		<div class="sg-tech__nx-body">
			<div class="sg-tech__nx-side">
				<h3><?php esc_html_e( 'Sites', 'site-blocks' ); ?></h3>
				<ul><li class="is-on"><?php esc_html_e( 'Site A', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Site B', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Site C', 'site-blocks' ); ?></li></ul>
				<div class="sg-tech__nx-map" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
			</div>
			<div class="sg-tech__nx-grid">
				<?php foreach ( $tiles as $index => $label ) : ?>
					<div class="sg-tech__nx-tile<?php echo 1 === $index ? ' sg-tech__nx-tile--hit' : ''; ?>"><b><?php esc_html_e( 'LIVE', 'site-blocks' ); ?></b><span><?php echo esc_html( $label ); ?></span></div>
				<?php endforeach; ?>
			</div>
			<div class="sg-tech__nx-events">
				<h3><?php esc_html_e( 'Events', 'site-blocks' ); ?></h3>
				<ul>
					<li><span><?php esc_html_e( 'Motion · Camera 02', 'site-blocks' ); ?></span><small>09:42</small></li>
					<li><span><?php esc_html_e( 'Analytics event · Camera 05', 'site-blocks' ); ?></span><small>09:38</small></li>
					<li><span><?php esc_html_e( 'Rule triggered · Camera 03', 'site-blocks' ); ?></span><small>09:21</small></li>
					<li><span><?php esc_html_e( 'User sign-in', 'site-blocks' ); ?></span><small>09:05</small></li>
				</ul>
			</div>
		</div>
		<div class="sg-tech__nx-time">
			<div class="sg-tech__nx-track">
				<i style="left:2%;width:30%"></i><i style="left:36%;width:22%"></i><i style="left:62%;width:30%"></i>
				<em style="left:18%"></em><em style="left:47%"></em><em style="left:71%"></em>
				<span class="sg-tech__nx-play"></span>
			</div>
			<div class="sg-tech__nx-hours"><span>06:00</span><span>09:00</span><span>12:00</span><span>15:00</span><span>18:00</span></div>
		</div>
	</div>
	<?php
}
