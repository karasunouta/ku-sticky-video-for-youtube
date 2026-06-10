<?php
/**
 * Settings Page Template (Free Version)
 *
 * @package KU_Sticky_Video_For_YouTube
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap ku-sticky-video-for-youtube-admin-wrap">
	<h1 class="screen-reader-text"><?php esc_html_e( 'KU Sticky Video for YouTube Settings', 'ku-sticky-video-for-youtube' ); ?></h1>

	<div class="ku-sticky-video-for-youtube-header">
		<div class="ku-sticky-video-for-youtube-title">KU Sticky Video for YouTube <span><?php echo esc_html( KU_Sticky_Video_For_YouTube::VERSION ); ?></span></div>
		<p><?php esc_html_e( 'Configure options for your floating YouTube video player.', 'ku-sticky-video-for-youtube' ); ?></p>
	</div>

	<?php
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) {
		echo '<div class="success-badge"><svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20" style="margin-right: 4px;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' . esc_html__( 'Settings saved successfully.', 'ku-sticky-video-for-youtube' ) . '</div>';
	}
	?>

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
					<?php esc_html_e( 'If this class is assigned to a YouTube block or its parent container, that video will not follow the scroll position.', 'ku-sticky-video-for-youtube' ); ?><br>
					<?php
					/* translators: %s: default class name */
					printf( esc_html__( 'Default: %s', 'ku-sticky-video-for-youtube' ), '<code>no-sticky</code>' );
					?>
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
</div>
