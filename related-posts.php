<?php
/**
 * similar posts display
 */

function show_similar_posts() {
	ob_start();
	$postID = get_the_ID();
	$cats = wp_get_post_categories($postID);
	if(!empty($cats)) {
		foreach($cats as $cat) {
			$c = get_category( $cat );
			$cs[] = $c->term_id;
		}
	}else {
		$cs = null;
	}
	$ptype = get_post_type($postID);
	$ptype_labels = get_post_type_object($ptype);
	$ptype_label = strtoupper($ptype_labels->label);
	$heading = get_post_meta($postID, 'similar_posts_heading', true);
	$select_posts = get_post_meta($postID, 'similar_posts_select_posts', true);
	if($select_posts === '1') {
		$selected_posts = get_post_meta($postID, 'similar_posts_posts', true);
	}
    $ptype_title = '<span>MORE: </span>'.$ptype_label.' OF IMPACT';
	if(!empty($heading)) {
		$ptype_title = $heading;
	}
	$not_post = array($postID);
	$num_posts = get_post_meta($postID, 'similar_posts_number_of_posts_to_show', true);
	if(!isset($selected_posts)) {
		$args = array(
			'post_type'			=> $ptype,
			'posts_per_page'	=> $num_posts,
			'post__not_in' => $not_post, 
			'post_status'		=> 'publish',
			'order' 			=>  'DESC',
			'category__in'		=> $cs,
		);
		if($ptype === 'event') {
			$taxes = get_the_terms($postID, 'event_category');
			if(!empty($taxes)) {
				foreach($taxes as $tax) {
					$tx[] = $tax->term_id;
				}
				$tax_query = array(  
					array(
					'taxonomy' => 'event_category',
					'field' => 'term_id',                 
					'terms' => $tx,
					),
				);
				$args['tax_query'] = $tax_query;
			}
		}
		// var_dump($args);
		$recents = new WP_Query($args);
		if( $recents->have_posts() ) : ?>
			<section class="grid story-grid more_posts">
				<div class="container container-lg">
					<?php echo '<div class="more_of_type">'.$ptype_title.'</div>'; ?>
					<div class="outer-wrapper post-wrap">
						<div class="inner-wrapper flex row vc">
						<?php
						while( $recents->have_posts() ) :
							$recents->the_post();
							$ID = get_the_ID();
							$posttitle = get_the_title();
							$thumb = get_the_post_thumbnail('bc-custom-thumbnail-size');
							$url = get_the_permalink();
							$ptype = get_post_type($ID);
							if(has_excerpt()) {
								$excerpt = get_the_excerpt();
							}else {
								$excerpt = get_excerpt();   
							}
							$date = get_the_date('n.j.Y');
							?>
							<div class="post_1_<?php echo $num_posts; ?> post item item_1_<?php echo $num_posts; ?> flex vc col <?php echo $ptype; ?>">
								<div class="post_wrap">
									<figure class="inner_post flex row vc nolink" style="display:block;text-decoration:none;">
										<?php the_post_thumbnail('bc-custom-thumbnail-size', array('class'=>'nolink')); ?>
									</figure>
									<div class="post_content clearfix">
										<div class="eqheight">
											<h4><a href="<?php echo $url; ?>" class="nolink"><?php echo $posttitle; ?></a></h4>
											<div class="excerpt"><?php echo $excerpt; ?></div>
										</div><!-- /.eqheight -->
										<div class="flex row meta">
											<div><i>Published <?php echo $date; ?></i></div>
											<div><a href="<?php echo $url; ?>"><i>Read the Story</i></a></div>
										</div>
									</div><!-- /.post_content -->
								</div><!-- /.post_wrap -->
							</div><!-- /.post -->
							<?php
						endwhile; ?>
						</div><!-- /.inner-wrapper-->
					</div><!-- /.outer-wrapper-->
				</div><!-- /.container-->
			</section><!-- /.story-grid -->
			<?php
		endif;
	}
	if(isset($selected_posts)) {
		?>
		<section class="grid story-grid more_posts">
			<div class="container container-lg">
				<?php echo '<div class="more_of_type">'.$ptype_title.'</div>'; ?>
				<div class="outer-wrapper post-wrap">
					<div class="inner-wrapper flex row vc">
					<?php
					foreach($selected_posts as $selected_post) {
						$ID = $selected_post;
						$posttitle = get_the_title($ID);
						$thumb = get_the_post_thumbnail($ID, 'bc-custom-thumbnail-size');
						$url = get_the_permalink($ID);
						$ptype = get_post_type($ID);
						if(has_excerpt($ID)) {
							$excerpt = get_the_excerpt($ID);
						}else {
							$excerpt = get_excerpt($ID);   
						}
						$date = get_the_date('n.j.Y', $ID);
						?>
						<div class="post_1_<?php echo $num_posts; ?> post item item_1_<?php echo $num_posts; ?> flex vc col <?php echo $ptype; ?>">
							<div class="post_wrap">
								<figure class="inner_post flex row vc nolink" style="display:block;text-decoration:none;">
									<?php echo get_the_post_thumbnail($ID, 'bc-custom-thumbnail-size', array('class'=>'nolink')); ?>
								</figure>
								<div class="post_content clearfix">
									<div class="eqheight">
										<h4><a href="<?php echo $url; ?>" class="nolink"><?php echo $posttitle; ?></a></h4>
										<div class="excerpt"><?php echo $excerpt; ?></div>
									</div><!-- /.eqheight -->
									<div class="flex row meta">
										<div><i>Published <?php echo $date; ?></i></div>
										<div><a href="<?php echo $url; ?>"><i>Read the Story</i></a></div>
									</div>
								</div><!-- /.post_content -->
							</div><!-- /.post_wrap -->
						</div><!-- /.post -->
					<?php
					}
					?>
					</div><!-- /.inner-wrapper-->
				</div><!-- /.outer-wrapper-->
			</div><!-- /.container-->
		</section><!-- /.story-grid -->
	<?php
	}
	echo ob_get_clean();
 }