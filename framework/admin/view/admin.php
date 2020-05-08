<div class="wrap">

	<h2>Theme Status</h2>
	<hr>

	<h3>Content Setup</h3>

	<h4>Custom Post Types:</h4>

	<?php $post_types = get_post_types(['_builtin'=>false],'objects');?>

	<table class="wc_status_table widefat" cellspacing="0" >
		<thead>
			<tr>
				<th>Post Type</th>
				<th>Slug</th>
				<th>Taxonomies</th>
			</tr>
		</thead>
		<tbody>

		<?php foreach($post_types as $post_type){?>
			<tr>
				<td><?php echo $post_type->label;?></td>
				<td><?php echo $post_type->name;?></td>
				<td><?php echo implode(',',$post_type->taxonomies);?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<h4>Custom Taxonomies:</h4>

	<?php $taxonomies = get_taxonomies(['_builtin'=>false],'objects');?>

	<table class="wc_status_table widefat" cellspacing="0" >
		<thead>
			<tr>
				<th>Name</th>
				<th>Slug</th>
			</tr>
		</thead>
		<tbody>

		<?php foreach($taxonomies as $taxonomy){?>
			<tr>
				<td><?php echo $taxonomy->label;?></td>
				<td><?php echo $taxonomy->name;?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<h4>Blocks:</h4>

	<?php
	$blocks = CMB2_Boxes::get_all();
	?>
	<table class="wc_status_table widefat" cellspacing="0" >
		<thead>
			<tr>
				<th>Name</th>
				<th>Slug</th>
				<th>Object Types</th>
			</tr>
		</thead>
		<tbody>

		<?php foreach($blocks as $name=>$block){?>
			<tr>
				<td><?php echo $block->prop('title');?></td>
				<td><?php echo $name;?></td>
				<td><?php echo implode(',',$block->prop('object_types'));?></td>	
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<h4>PHP info:</h4>

	<h4>Plugin info:</h4>

	<h4>Wordpress Version info:</h4>

	<h4>Database Version info:</h4>
	
</div>
