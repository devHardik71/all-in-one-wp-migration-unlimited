<ul id="ai1wm-queries">
	<li class="ai1wm-query ai1wm-expandable">
		<p>
			<span>
				<strong><?php _e( 'Find', AI1WM_PLUGIN_NAME ); ?></strong>
				<small class="ai1wm-query-find-text ai1wm-tooltip" title="Search the database for this text"><?php echo esc_html( __( '<text>', AI1WM_PLUGIN_NAME ) ); ?></small>
				<strong><?php _e( 'Replace with', AI1WM_PLUGIN_NAME ); ?></strong>
				<small class="ai1wm-query-replace-text ai1wm-tooltip" title="Replace the database with this text"><?php echo esc_html( __( '<another-text>', AI1WM_PLUGIN_NAME ) ); ?></small>
				<strong><?php _e( 'in the database', AI1WM_PLUGIN_NAME ); ?></strong>
			</span>
			<span class="ai1wm-query-arrow ai1wm-icon-chevron-right"></span>
		</p>
		<div>
			<input class="ai1wm-query-find-input" type="text" placeholder="<?php _e( 'Find', AI1WM_PLUGIN_NAME ); ?>" name="options[replace][old_value][]" />
			<input class="ai1wm-query-replace-input" type="text" placeholder="<?php _e( 'Replace with', AI1WM_PLUGIN_NAME ); ?>" name="options[replace][new_value][]" />
		</div>
	</li>
</ul>

<button type="button" class="ai1wm-button-gray" id="ai1wm-add-new-replace-button">
	<i class="ai1wm-icon-plus2"></i><?php _e( 'Add', AI1WM_PLUGIN_NAME ); ?>
</button>
