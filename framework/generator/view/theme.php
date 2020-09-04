<div class="wrap">

	<h2>Raiser Theme Generator</h2>

	<?php Raiser_Theme_Generator::display_notice(); ?>

	<?php Raiser_Theme_Generator::tabs(); ?>

	<h3>Start a new blank theme</h3>
	<p>This option will create a new starter theme. To use the theme you need to activate the theme from Apperance > Themes</p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">	

		<input type="hidden" name="action" value="raiser_command_new_theme">
		<?php wp_nonce_field( 'raiser_command_new_theme', 'new_theme' ); ?>

		<table class="form-table">
			<tbody>		
				<tr>
					<th scope="row">Theme Name</th>
					<td>
						<input type="text" name="theme_name">
					</td>
				</tr>
				<tr>
					<th scope="row">Include Raiser-WP config files?</th>
					<td>
						<label><input type="radio" name="flags[config]" value='true' checked> Yes</label>&nbsp;&nbsp;&nbsp;<label><input type="radio"  name="flags[config]" value='false'> No</label>
					</td>
				</tr>					
				<tr>
					<th scope="row">Include sass and js compilation via npm?</th>
					<td>
						<label><input type="radio" name="flags[npm]" value='true'> Yes</label>&nbsp;&nbsp;&nbsp;<label><input type="radio" checked name="flags[npm]" value='false'> No</label>
					</td>
				</tr>					
				<tr>
					<th colspan="2"><input type="submit" name="submit" id="submit" class="button button-primary" value="Create"></th>
				</tr>
			</tbody>
		</table>	

	</form>

</div>