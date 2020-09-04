<div class="wrap">

	<h2>Raiser Theme Generator</h2>

	<?php Raiser_Theme_Generator::display_notice(); ?>

	<?php Raiser_Theme_Generator::tabs(); ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">	

		<input type="hidden" name="action" value="raiser_command_config">
		<?php wp_nonce_field( 'raiser_command_config', 'config' ); ?>

		<table class="form-table">
			<tbody>		
				<tr>
					<th scope="row">Create Config Files<br><small style="font-weight:normal;">Will not overwrite exsting config files</small></th>
					<td>
						<label><input type="checkbox" name="configs[]" value="admin" checked> Admin</label><br>
						<label><input type="checkbox" name="configs[]" value="theme" checked> Theme</label><br>
						<label><input type="checkbox" name="configs[]" value="gutenberg" checked> Gutenberg</label><br>
					</td>
				</tr>	
				<tr>
					<th colspan="2"><input type="submit" name="submit" id="submit" class="button button-primary" value="Create"></th>
				</tr>
			</tbody>
		</table>	

	</form>

	<a href="https://raiserweb.github.io/Raiser-WP-Docs/docs/configuration" target="_blank">Documentation <span class="dashicons-before dashicons-external"></span></a>

</div>