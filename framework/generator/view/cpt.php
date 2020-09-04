<div class="wrap">

	<h2>Raiser Theme Generator</h2>

	<?php Raiser_Theme_Generator::display_notice(); ?>

	<?php Raiser_Theme_Generator::tabs(); ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">	

		<input type="hidden" name="action" value="raiser_command_cpt">
		<?php wp_nonce_field( 'raiser_command_cpt', 'cpt' ); ?>

		<table class="form-table">
			<tbody>		
				<tr>
					<th scope="row">CPT Name</th>
					<td>
						<input type="text" name="post_type_name">
					</td>
				</tr>
				<tr>
					<th scope="row">Setup template files?</th>
					<td>
						<label><input type="radio" name="flags[templates]" value='true'> Yes</label>&nbsp;&nbsp;&nbsp;<label><input type="radio" checked name="flags[templates]" value='false'> No</label>
					</td>
				</tr>		
				<tr>
					<th colspan="2"><input type="submit" name="submit" id="submit" class="button button-primary" value="Create"></th>
				</tr>
			</tbody>
		</table>	

	</form>

	<a href="https://raiserweb.github.io/Raiser-WP-Docs/docs/cpt" target="_blank">Documentation <span class="dashicons-before dashicons-external"></span></a>

</div>