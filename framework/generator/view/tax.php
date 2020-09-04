<div class="wrap">

	<h2>Raiser Theme Generator</h2>

	<?php Raiser_Theme_Generator::display_notice(); ?>

	<?php Raiser_Theme_Generator::tabs(); ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">	

		<input type="hidden" name="action" value="raiser_command_tax">
		<?php wp_nonce_field( 'raiser_command_tax', 'tax' ); ?>

		<table class="form-table">
			<tbody>		
				<tr>
					<th scope="row">Taxonomy Name</th>
					<td>
						<input type="text" name="tax_name">
					</td>
				</tr>
				<tr>
					<th scope="row">Attach to objects (existing)</th>
					<td>
						<?php $post_types = get_post_types([],'objects'); ?>
						<select name="object_types[]" multiple style="height:140px;">
							<?php foreach( $post_types as $post_type ){?>
								<option value="<?php echo $post_type->name;?>"><?php echo $post_type->label;?></option>
							<?php } ?>
						</select>
					</td>
				</tr>		
				<tr>
					<th scope="row">Attach to objects (custom)<br><small style="font-weight:400;">insert object slugs seperated by commas, eg projects,staff</small></th>
					<td>
						<input type="text" name="object_type_custom">
					</td>
				</tr>					
				<tr>
					<th colspan="2"><input type="submit" name="submit" id="submit" class="button button-primary" value="Create"></th>
				</tr>
			</tbody>
		</table>	

	</form>

	<a href="https://raiserweb.github.io/Raiser-WP-Docs/docs/tax" target="_blank">Documentation <span class="dashicons-before dashicons-external"></span></a>

</div>