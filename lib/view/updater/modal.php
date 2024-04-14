<div id="ai1wm-modal-dialog-<?php echo $modal; ?>" class="ai1wm-modal-dialog">
	<div class="ai1wm-modal-container">
		<h2><?php _e( 'Enter your Purchase ID', AI1WM_PLUGIN_NAME ); ?></h2>
		<p><?php _e( 'To update your plugin/extension to the latest version, please fill your Purchase ID below.', AI1WM_PLUGIN_NAME ); ?></p>
		<p class="ai1wm-modal-error"></p>
		<p>
			<input type="text" class="ai1wm-purchase-id" placeholder="<?php _e( 'Purchase ID', AI1WM_PLUGIN_NAME ); ?>" />
			<input type="hidden" class="ai1wm-update-link" value="<?php echo $url; ?>" />
		</p>
		<p>
			<?php _e( "Don't have a Purchase ID? You can find your Purchase ID", AI1WM_PLUGIN_NAME ); ?>
			<a href="https://servmask.com/lost-purchase" target="_blank" class="ai1wm-help-link"><?php _e( 'here', AI1WM_PLUGIN_NAME ); ?></a>
		</p>
		<p class="ai1wm-modal-buttons submitbox">
			<button type="button" class="ai1wm-purchase-add ai1wm-button-green">
				<?php _e( 'Save', AI1WM_PLUGIN_NAME ); ?>
			</button>
			<a href="#" class="submitdelete ai1wm-purchase-discard"><?php _e( 'Discard', AI1WM_PLUGIN_NAME ); ?></a>
		</p>
	</div>
</div>

<span id="ai1wm-update-section-<?php echo $modal; ?>">
	<span class="ai1wm-icon-update"></span>
	<?php _e( 'There is an update available. To update, you must enter your', AI1WM_PLUGIN_NAME ); ?>
	<a href="#ai1wm-modal-dialog-<?php echo $modal; ?>"><?php _e( 'Purchase ID', AI1WM_PLUGIN_NAME ); ?></a>.
</span>
