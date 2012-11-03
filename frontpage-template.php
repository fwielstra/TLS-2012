
<?php
/*
Template Name: This is the Frontpage
*/
?>

<?php get_header(); ?>

	<div class="frontpage-primary">
		<div class="frontpage-content">
			<?php include('frontpage-previews.php'); ?>
		</div>
	</div>

	<!-- bottom section, highlights, two columns -->
	<div class="frontpage-highlights">
		<div class="frontpage-primary">
			<div class="tls_border">
				<h3>More News</h3>
			</div>

			<div class="frontpage-content">
				<div class="frontpage-content-left">
					<?php echo echo_posts_for_smaller_preview(); ?>
				</div>
				<div class="frontpage-clear"></div>
				<a href="news">Find all our News &amp; Updates here</a>
			</div>
		</div>

		<div class="frontpage-primary">
			<div class="tls_border">
				<h3>Latest from the Forums</h3>
			</div>

			<div class="frontpage-content">
				<div class="frontpage-content-right">
					<?php forum_get_latest_posts() ; ?>
				</div>
				<div class="frontpage-clear"></div>
				<a href="forums">Read more on the forums</a>
			</div>
		</div>
	</div>
<?php get_footer(); ?>
