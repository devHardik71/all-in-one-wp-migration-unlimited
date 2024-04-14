<div class="error">
	<p>
		<?php
		printf(
			__(
				'All in One WP Migration is not able to create <strong>%s</strong> folder. ' .
				'You will need to create this folder and grant it read/write/execute permissions (0777) ' .
				'for the All in One WP Migration plugin to function properly.',
				AI1WM_PLUGIN_NAME
			),
			AI1WM_BACKUPS_PATH
		)
		?>
	</p>
</div>
