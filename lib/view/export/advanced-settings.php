<div class="ai1wm-field-set">
	<div class="ai1wm-accordion ai1wm-expandable">
		<h4>
			<i class="ai1wm-icon-arrow-right"></i>
			<?php _e( 'Advanced options', AI1WM_PLUGIN_NAME ); ?>
			<small><?php _e( '(click to expand)', AI1WM_PLUGIN_NAME ); ?></small>
		</h4>
		<ul>
			<li>
				<label for="ai1wm-no-spam-comments">
					<input type="checkbox" id="ai1wm-no-spam-comments" name="options[no_spam_comments]" />
					<?php _e( 'Do <strong>not</strong> export spam comments', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>
			<li>
				<label for="ai1wm-no-revisions">
					<input type="checkbox" id="ai1wm-no-revisions" name="options[no_revisions]" />
					<?php _e( 'Do <strong>not</strong> export post revisions', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>
			<li>
				<label for="ai1wm-no-media">
					<input type="checkbox" id="ai1wm-no-media" name="options[no_media]" />
					<?php _e( 'Do <strong>not</strong> export media library (files)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>
			<li>
				<label for="ai1wm-no-themes">
					<input type="checkbox" id="ai1wm-no-themes" name="options[no_themes]" />
					<?php _e( 'Do <strong>not</strong> export themes (files)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>

			<?php if ( apply_filters( 'ai1wm_max_file_size', AI1WM_MAX_FILE_SIZE ) === 0 ) : ?>
				<li>
					<label for="ai1wm-no-inactive-themes">
						<input type="checkbox" id="ai1wm-no-inactive-themes" name="options[no_inactive_themes]" />
						<?php _e( 'Do <strong>not</strong> export inactive themes (files)', AI1WM_PLUGIN_NAME ); ?>
					</label>
					<small style="color:red"><?php _e( 'new', AI1WM_PLUGIN_NAME ); ?></small>
				</li>
			<?php endif; ?>

			<li>
				<label for="ai1wm-no-muplugins">
					<input type="checkbox" id="ai1wm-no-muplugins" name="options[no_muplugins]" />
					<?php _e( 'Do <strong>not</strong> export must-use plugins (files)', AI1WM_PLUGIN_NAME ); ?>
				</label>
				<small style="color:red"><?php _e( 'new', AI1WM_PLUGIN_NAME ); ?></small>
			</li>

			<li>
				<label for="ai1wm-no-plugins">
					<input type="checkbox" id="ai1wm-no-plugins" name="options[no_plugins]" />
					<?php _e( 'Do <strong>not</strong> export plugins (files)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>

			<?php if ( apply_filters( 'ai1wm_max_file_size', AI1WM_MAX_FILE_SIZE ) === 0 ) : ?>
				<li>
					<label for="ai1wm-no-inactive-plugins">
						<input type="checkbox" id="ai1wm-no-inactive-plugins" name="options[no_inactive_plugins]" />
						<?php _e( 'Do <strong>not</strong> export inactive plugins (files)', AI1WM_PLUGIN_NAME ); ?>
					</label>
					<small style="color:red"><?php _e( 'new', AI1WM_PLUGIN_NAME ); ?></small>
				</li>
				<li>
					<label for="ai1wm-no-cache">
						<input type="checkbox" id="ai1wm-no-cache" name="options[no_cache]" />
						<?php _e( 'Do <strong>not</strong> export cache (files)', AI1WM_PLUGIN_NAME ); ?>
					</label>
					<small style="color:red"><?php _e( 'new', AI1WM_PLUGIN_NAME ); ?></small>
				</li>
			<?php endif; ?>

			<li>
				<label for="ai1wm-no-database">
					<input type="checkbox" id="ai1wm-no-database" name="options[no_database]" />
					<?php _e( 'Do <strong>not</strong> export database (sql)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>
			<li>
				<label for="ai1wm-no-email-replace">
					<input type="checkbox" id="ai1wm-no-email-replace" name="options[no_email_replace]" />
					<?php _e( 'Do <strong>not</strong> replace email domain (sql)', AI1WM_PLUGIN_NAME ); ?>
				</label>
				<small style="color:red"><?php _e( 'new', AI1WM_PLUGIN_NAME ); ?></small>
			</li>

			<?php do_action( 'ai1wm_export_advanced_settings' ); ?>
		</ul>
	</div>
</div>
