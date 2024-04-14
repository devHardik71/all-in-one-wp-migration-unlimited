<div class="ai1wm-report-problem">
	<button type="button" id="ai1wm-report-problem-button" class="ai1wm-button-red">
		<i class="ai1wm-icon-notification"></i> <?php _e( 'Report issue', AI1WM_PLUGIN_NAME ); ?>
	</button>
	<div class="ai1wm-report-problem-dialog">
		<div class="ai1wm-field">
			<input placeholder="<?php _e( 'Enter your email address..', AI1WM_PLUGIN_NAME ); ?>" type="text" id="ai1wm-report-email" class="ai1wm-report-email" />
		</div>
		<div class="ai1wm-field">
			<textarea rows="3" id="ai1wm-report-message" class="ai1wm-report-message" placeholder="<?php _e( 'Please describe your problem here..', AI1WM_PLUGIN_NAME ); ?>"></textarea>
		</div>
		<div class="ai1wm-field ai1wm-report-terms-segment">
			<label for="ai1wm-report-terms">
				<input type="checkbox" class="ai1wm-report-terms" id="ai1wm-report-terms" />
				<?php _e( 'I agree to send my email address, comments and error logs to a ServMask server.', AI1WM_PLUGIN_NAME ); ?>
			</label>
		</div>
		<div class="ai1wm-field">
			<div class="ai1wm-buttons">
				<a href="#" id="ai1wm-report-cancel" class="ai1wm-report-cancel"><?php _e( 'Cancel', AI1WM_PLUGIN_NAME ); ?></a>

				<button type="submit" id="ai1wm-report-submit" class="ai1wm-button-blue ai1wm-form-submit">
					<i class="ai1wm-icon-paperplane"></i>
					<?php _e( 'Send', AI1WM_PLUGIN_NAME ); ?>
				</button>
				<span class="spinner"></span>
				<div class="ai1wm-clear"></div>
			</div>
		</div>
	</div>
</div>
