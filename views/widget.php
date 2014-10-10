<h5><?php echo $instance['title']; ?></h5>
<?php if(!$show_search) ?>
  <div id="binx_stolen_widget" data-norecent="true"></div>
  <script src="http://widget.bikeindex.org/include.js"></script>
<?php endif; ?>
<ul class="bike-index-listing">
	<?php $counter = 0; ?>
	<?php foreach($bikes as $bike): ?>
	<li>
		<div class="bike-details">
			<h5 class="bike-title"><a href="<?php echo $bike['url']; ?>" class="bike-title"><?php echo $bike['year']; ?> <?php echo $bike['manufacturer_name']; ?> <?php echo $bike['frame_model']; ?> </a></h5>
			<?php if(!empty($bike['thumb'])): ?>
				<a href="<?php echo $bike['url']; ?>"><img src="<?php echo $bike['thumb']; ?>"></a>
			<?php endif; ?>
			<p><?php echo $bike['description']; ?>
			<a href="<?php echo $bike['url']; ?>" class="btn">More Details</a>
		</div>
	</li>
	<?php if($counter >= $instance['max_bikes']): ?>
		<?php break; ?>
	<?php endif; ?>
	<?php $counter++; endforeach; ?>
</ul>