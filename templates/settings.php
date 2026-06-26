<?php
/**
 * Settings Page Template (Free Version)
 *
 * @package KU_Sticky_Video_For_YouTube
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $options ) ) {
	$options = get_option( 'ku-sticky-video-for-youtube-options', array() );
}

$exclude_class                  = isset( $options['exclude_class'] ) ? $options['exclude_class'] : 'no-sticky';
$position                       = isset( $options['position'] ) ? $options['position'] : 'bottom-right';
$disable_narrow_viewport        = isset( $options['disable_narrow_viewport'] ) ? $options['disable_narrow_viewport'] : '1';
$sticky_trigger_mode            = isset( $options['sticky_trigger_mode'] ) ? $options['sticky_trigger_mode'] : 'playing';
$sticky_hide_above              = isset( $options['sticky_hide_above'] ) ? $options['sticky_hide_above'] : '1';
$sticky_width_unit              = isset( $options['sticky_width_unit'] ) ? $options['sticky_width_unit'] : '%';
$sticky_width_val_px            = isset( $options['sticky_width_val_px'] ) ? $options['sticky_width_val_px'] : 400;
$sticky_width_val_pct           = isset( $options['sticky_width_val_pct'] ) ? $options['sticky_width_val_pct'] : 25;
$sticky_width_max_original      = isset( $options['sticky_width_max_original'] ) ? $options['sticky_width_max_original'] : '1';
$sticky_width_max_custom_active = isset( $options['sticky_width_max_custom_active'] ) ? $options['sticky_width_max_custom_active'] : '0';
$sticky_width_max_custom_val    = isset( $options['sticky_width_max_custom_val'] ) ? $options['sticky_width_max_custom_val'] : 450;
$sticky_z_index                 = isset( $options['sticky_z_index'] ) ? $options['sticky_z_index'] : 9999;
?>
<div class="wrap ku-sticky-video-for-youtube-admin-wrap">
	<h1 class="screen-reader-text"><?php esc_html_e( 'KU Sticky Video for YouTube Settings', 'ku-sticky-video-for-youtube' ); ?></h1>

	<div class="ku-sticky-video-for-youtube-header">
		<div class="ku-sticky-video-for-youtube-title">KU Sticky Video for YouTube <span><?php echo esc_html( KU_Sticky_Video_For_YouTube::VERSION ); ?></span></div>
		<p><?php esc_html_e( 'Configure options for your floating YouTube video player.', 'ku-sticky-video-for-youtube' ); ?></p>
	</div>

	<form method="post" action="options.php">
		<?php settings_fields( 'ku-sticky-video-for-youtube-options-group' ); ?>

		<div class="ku-sticky-video-for-youtube-card">
			<h2 class="ku-sticky-video-for-youtube-card-title">
				<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
				</svg>
				<?php esc_html_e( 'General Settings', 'ku-sticky-video-for-youtube' ); ?>
			</h2>

			<div class="form-group">
				<label for="exclude_class"><?php esc_html_e( 'Exclude CSS Class Name', 'ku-sticky-video-for-youtube' ); ?></label>
				<div class="input-wrapper">
					<span class="prefix-dot">.</span>
					<input type="text" id="exclude_class" name="ku-sticky-video-for-youtube-options[exclude_class]" value="<?php echo esc_attr( $exclude_class ); ?>" class="input-field" placeholder="no-sticky" />
				</div>
				<p class="field-description">
					<?php esc_html_e( 'If this class is assigned to a YouTube block or its parent container, that video will not follow the scroll position. In this free version, only the first (topmost) video on the page that does not have this class will be targeted for the sticky effect.', 'ku-sticky-video-for-youtube' ); ?><br>
					<?php
					/* translators: %s: default class name */
					printf( esc_html__( 'Default: %s', 'ku-sticky-video-for-youtube' ), '<code>no-sticky</code>' );
					?>
				</p>
			</div>

			<div class="form-group" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
				<label><?php esc_html_e( 'Trigger Settings', 'ku-sticky-video-for-youtube' ); ?></label>
				<div style="display: flex; flex-direction: column; gap: 10px; margin-top: 8px;">
					<label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 8px;">
						<input type="radio" name="ku-sticky-video-for-youtube-options[sticky_trigger_mode]" value="playing" <?php checked( $sticky_trigger_mode, 'playing' ); ?> />
						<?php esc_html_e( 'Only when playing', 'ku-sticky-video-for-youtube' ); ?>
					</label>
					<label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 8px;">
						<input type="radio" name="ku-sticky-video-for-youtube-options[sticky_trigger_mode]" value="always" <?php checked( $sticky_trigger_mode, 'always' ); ?> />
						<?php esc_html_e( 'Always (Scroll Follow)', 'ku-sticky-video-for-youtube' ); ?>
					</label>
				</div>
				<p class="field-description">
					<?php esc_html_e( 'Only when playing: Enable sticky video only when the video is actually playing.', 'ku-sticky-video-for-youtube' ); ?><br>
					<?php esc_html_e( 'Always: Enable sticky video when scrolling, regardless of playback state.', 'ku-sticky-video-for-youtube' ); ?>
					<abbr title="<?php echo esc_attr__( 'In this free version, only the first (topmost) eligible video is targeted in both modes. To have multiple videos each become sticky while playing, upgrade to the Pro version\'s "Only when playing" mode.', 'ku-sticky-video-for-youtube' ); ?>" class="ku-sticky-note">*</abbr>
				</p>

				<?php
				$is_playing_mode = ( 'playing' === $sticky_trigger_mode );
				$hide_above_container_style = $is_playing_mode ? 'opacity: 0.5; pointer-events: none;' : 'opacity: 1; transition: opacity 0.3s ease;';
				?>
				<div id="hide_above_settings_container" style="margin-top: 12px; padding-left: 24px; <?php echo esc_attr( $hide_above_container_style ); ?>">
					<label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 8px;">
						<input type="checkbox" id="sticky_hide_above" name="ku-sticky-video-for-youtube-options[sticky_hide_above]" value="1" <?php checked( $sticky_hide_above, '1' ); ?> <?php disabled( $is_playing_mode, true ); ?> />
						<?php esc_html_e( 'Hide when scrolling above', 'ku-sticky-video-for-youtube' ); ?>
					</label>
					<p class="field-description" style="margin-top: 4px;">
						<?php esc_html_e( 'Prevents the sticky video from appearing when scrolling above the original video position. This only applies to the "Always" trigger mode.', 'ku-sticky-video-for-youtube' ); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- Layout Settings Card -->
		<div class="ku-sticky-video-for-youtube-card">
			<h2 class="ku-sticky-video-for-youtube-card-title">
				<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
				</svg>
				<?php esc_html_e( 'Layout Settings', 'ku-sticky-video-for-youtube' ); ?>
			</h2>

			<div class="form-group" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
				<label for="position" style="font-weight: bold;"><?php esc_html_e( 'Display Position', 'ku-sticky-video-for-youtube' ); ?></label>
				<div style="margin-top: 8px;">
					<select id="position" name="ku-sticky-video-for-youtube-options[position]" class="select-field" style="padding: 0 8px; height: 35px; border-radius: 6px; border: 1px solid #ddd; background-color: #fff; min-width: 150px;">
						<option value="bottom-right" <?php selected( $position, 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'ku-sticky-video-for-youtube' ); ?></option>
						<option value="bottom-left" <?php selected( $position, 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'ku-sticky-video-for-youtube' ); ?></option>
					</select>
				</div>
				<p class="field-description">
					<?php esc_html_e( 'Choose which corner the floating video player should stick to.', 'ku-sticky-video-for-youtube' ); ?>
				</p>
			</div>

			<div class="form-group" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
				<label for="sticky_z_index" style="font-weight: bold;"><?php esc_html_e( 'Z-index (Stack Order)', 'ku-sticky-video-for-youtube' ); ?></label>
				<div style="margin-top: 8px;">
					<input type="number" id="sticky_z_index" name="ku-sticky-video-for-youtube-options[sticky_z_index]" value="<?php echo esc_attr( $sticky_z_index ); ?>" class="input-field" min="1" max="2147483647" style="width: 150px;" />
				</div>
				<p class="field-description">
					<?php esc_html_e( 'Adjust the Z-index value to prevent the sticky video player from overlapping with theme elements like header menus or chat widgets. Default is 9999.', 'ku-sticky-video-for-youtube' ); ?>
				</p>
			</div>

			<div class="form-group">
				<label><?php esc_html_e( 'Sticky Video Width', 'ku-sticky-video-for-youtube' ); ?></label>
				<div style="display: flex; align-items: center; gap: 10px; margin-top: 8px;">
					<!-- Unit selector -->
					<select name="ku-sticky-video-for-youtube-options[sticky_width_unit]" id="sticky_width_unit" class="select-field" style="padding: 0 8px; height: 35px; border-radius: 6px; border: 1px solid #ddd; background-color: #fff; min-width: 95px;">
						<option value="%" <?php selected( $sticky_width_unit, '%' ); ?>>% (vw)</option>
						<option value="px" <?php selected( $sticky_width_unit, 'px' ); ?>>px</option>
					</select>

					<!-- % input wrapper -->
					<div id="width_input_pct_container" class="input-wrapper" style="align-items: center; gap: 5px; <?php echo '%' === $sticky_width_unit ? 'display: inline-flex;' : 'display: none;'; ?>">
						<input type="number" id="sticky_width_val_pct" name="ku-sticky-video-for-youtube-options[sticky_width_val_pct]" value="<?php echo esc_attr( $sticky_width_val_pct ); ?>" class="input-field" min="5" max="100" style="width: 100px;" />
						<span style="font-weight: bold; color: #666;">%</span>
					</div>

					<!-- px input wrapper -->
					<div id="width_input_px_container" class="input-wrapper" style="align-items: center; gap: 5px; <?php echo 'px' === $sticky_width_unit ? 'display: inline-flex;' : 'display: none;'; ?>">
						<input type="number" id="sticky_width_val_px" name="ku-sticky-video-for-youtube-options[sticky_width_val_px]" value="<?php echo esc_attr( $sticky_width_val_px ); ?>" class="input-field" min="100" max="2000" style="width: 100px;" />
						<span style="font-weight: bold; color: #666;">px</span>
					</div>
				</div>
				<p class="field-description">
					<?php esc_html_e( 'Configure the width of the video player when it is in sticky mode. Switching units will preserve both pixel and percentage settings.', 'ku-sticky-video-for-youtube' ); ?>
				</p>
			</div>

			<!-- Max Width Settings (Only for % unit) -->
			<?php
			$is_px_unit          = ( 'px' === $sticky_width_unit );
			$max_container_style = $is_px_unit ? 'opacity: 0.5; pointer-events: none;' : 'opacity: 1; transition: opacity 0.3s ease;';
			?>
			<div id="width_max_settings_container" class="form-group" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; <?php echo esc_attr( $max_container_style ); ?>">
				<label><?php esc_html_e( 'Maximum Width Settings', 'ku-sticky-video-for-youtube' ); ?></label>
				<div style="display: flex; flex-direction: column; gap: 10px; margin-top: 8px;">
					<label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 8px;">
						<input type="checkbox" id="sticky_width_max_original" name="ku-sticky-video-for-youtube-options[sticky_width_max_original]" value="1" <?php checked( $sticky_width_max_original, '1' ); ?> <?php disabled( $is_px_unit, true ); ?> />
						<?php esc_html_e( 'Sticky video player does not exceed the width of the original video player', 'ku-sticky-video-for-youtube' ); ?>
					</label>

					<div style="display: flex; align-items: center; gap: 8px;">
						<label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 8px;">
							<input type="checkbox" id="sticky_width_max_custom_active" name="ku-sticky-video-for-youtube-options[sticky_width_max_custom_active]" value="1" <?php checked( $sticky_width_max_custom_active, '1' ); ?> <?php disabled( $is_px_unit, true ); ?> />
							<?php esc_html_e( 'Limit the maximum width to:', 'ku-sticky-video-for-youtube' ); ?>
						</label>
						<div class="input-wrapper" style="align-items: center; gap: 5px; display: inline-flex;">
							<input type="number" id="sticky_width_max_custom_val" name="ku-sticky-video-for-youtube-options[sticky_width_max_custom_val]" value="<?php echo esc_attr( $sticky_width_max_custom_val ); ?>" class="input-field" min="100" max="2000" style="width: 80px;" <?php disabled( $is_px_unit || $sticky_width_max_custom_active !== '1', true ); ?> />
							<span style="font-weight: bold; color: #666;">px</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Sticky Video Height -->
			<div class="form-group" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
				<label><?php esc_html_e( 'Sticky Video Height', 'ku-sticky-video-for-youtube' ); ?></label>
				<p class="field-description" style="margin-top: 8px;">
					<?php esc_html_e( 'Automatically limited to 50% of the viewport height.', 'ku-sticky-video-for-youtube' ); ?>
				</p>
			</div>

			<div class="form-group" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
				<label style="font-weight: bold;"><?php esc_html_e( 'Mobile User Experience', 'ku-sticky-video-for-youtube' ); ?></label>
				<div style="margin-top: 8px;">
					<label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 8px;">
						<input type="checkbox" name="ku-sticky-video-for-youtube-options[disable_narrow_viewport]" value="1" <?php checked( $disable_narrow_viewport, '1' ); ?> />
						<?php esc_html_e( 'Disable on narrow viewports', 'ku-sticky-video-for-youtube' ); ?>
					</label>
				</div>
				<p class="field-description">
					<?php esc_html_e( 'If enabled, the sticky video player is automatically disabled when the browser width is narrower than the sticky video width + double margins (e.g., 400px + 20px×2 = 440px). This protects the mobile user experience.', 'ku-sticky-video-for-youtube' ); ?>
				</p>
			</div>

			<div class="instruction-box">
				<h3><?php esc_html_e( 'How to Exclude a YouTube Block', 'ku-sticky-video-for-youtube' ); ?></h3>
				<ol>
					<li><?php esc_html_e( 'Open the post editor and select the YouTube block you want to exclude.', 'ku-sticky-video-for-youtube' ); ?></li>
					<li><?php esc_html_e( 'In the block settings sidebar, expand the "Advanced" panel.', 'ku-sticky-video-for-youtube' ); ?></li>
					<li>
						<?php
						/* translators: %s: CSS class name */
						printf( esc_html__( 'Add the configured class name %s to the "Additional CSS class(es)" input field.', 'ku-sticky-video-for-youtube' ), '<code>' . esc_html( $exclude_class ) . '</code>' );
						?>
					</li>
				</ol>
			</div>
		</div>

		<button type="submit" class="submit-btn">
			<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
			</svg>
			<?php esc_html_e( 'Save Changes', 'ku-sticky-video-for-youtube' ); ?>
		</button>
	</form>

	<!-- Pro Version Upsell Card -->
	<div class="ku-sticky-video-for-youtube-card ku-sticky-pro-card">
		<p class="ku-sticky-pro-card-text">
			🚀 <?php esc_html_e( 'Want more? The Pro version supports multiple videos, each becoming sticky independently while playing — plus additional display positions and advanced targeting options.', 'ku-sticky-video-for-youtube' ); ?>
		</p>
		<a href="<?php echo esc_url( __( 'https://karasunouta.com/en/store/ku-sticky-video-for-youtube-pro/', 'ku-sticky-video-for-youtube' ) ); ?>"
		   target="_blank" rel="noopener noreferrer" class="ku-sticky-pro-link">
			<?php esc_html_e( 'Learn more about the Pro version →', 'ku-sticky-video-for-youtube' ); ?>
		</a>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var triggerRadios = document.querySelectorAll('input[name="ku-sticky-video-for-youtube-options[sticky_trigger_mode]"]');
	var hideAboveCheckbox = document.getElementById('sticky_hide_above');
	var hideAboveContainer = document.getElementById('hide_above_settings_container');

	function toggleHideAbove() {
		var selectedRadio = document.querySelector('input[name="ku-sticky-video-for-youtube-options[sticky_trigger_mode]"]:checked');
		if (selectedRadio && selectedRadio.value === 'playing') {
			if (hideAboveCheckbox) hideAboveCheckbox.disabled = true;
			if (hideAboveContainer) {
				hideAboveContainer.style.opacity = '0.5';
				hideAboveContainer.style.pointerEvents = 'none';
			}
		} else {
			if (hideAboveCheckbox) hideAboveCheckbox.disabled = false;
			if (hideAboveContainer) {
				hideAboveContainer.style.opacity = '1';
				hideAboveContainer.style.pointerEvents = 'auto';
			}
		}
	}

	triggerRadios.forEach(function(radio) {
		radio.addEventListener('change', toggleHideAbove);
	});

	// Initialize on load
	toggleHideAbove();
});
</script>
