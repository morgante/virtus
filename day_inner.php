<header>
	
	<h1 id="date" date="<?php echo $day['date']->format('Y-m-d'); ?>">Day</h1>

</header>

<?php // Utils::debug( $day ); ?>

<section id="virtues">
	
	<ul>
		
		<?php
		foreach( $virtues as $key => $virtue ):
		?>
		<li id="<?php echo $key; ?>" class="virtue <?php echo $key; ?>" virtue="<?php echo $key; ?>">
			
			<?php
			
			$status = "false";
			for( $k = 1; $k <= $virtue['parts']; $k++ ):
				if( $day['virtues'][$key] >= $k):
					$status = "true";
				else:
					$status = "false";
				endif;
			
			?>
			<span class="virtue <?php echo $key; ?> part part<?php echo $k; ?> <?php echo $status; ?> parts<?php echo $virtue['parts']; ?>"><?php echo $status; ?></span>
			
			<?php endfor; ?>
			
			<a href="#" title="<?php echo $virtue['name']; ?>"><?php echo $virtue['name']; ?></a>
		
		</li>
			
		<?php endforeach; ?>
	</ul>
	
</section>
	
<footer>
	<p>Made by MasterMade</p>
</footer>