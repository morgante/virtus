<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	
	<title>Virtus</title>
		
	<?php Stack::out( 'virtus_header_css', '<link rel="stylesheet" type="text/css" href="%s" media="all" />' . "\n" ); ?>

	<script type="text/javascript">
		url = '<?php echo $url; ?>';
	</script>
	
	<?php Stack::out( 'virtus_header_javascript', '<script src="%s" type="text/javascript"></script>' . "\n" ); ?>
	
</head>
<body>
	
	<div id="page">
		
		<header>
			
			<hgroup>
				<h1><?php echo $month->format('F Y'); ?></h1>
				<h2><?php echo $month_points; ?> points</h2>
			</hgroup>
			
			<section id="records">
				<table>
					<tbody>
						<tr id="dayrecord">
							<th scope="row">Daily Record:</th>
							<td><?php echo $record_day['record']; ?> on <?php echo $record_day['day']->format('F j, Y'); ?></td>
						</tr>
						<tr id="weekrecord">
							<th scope="row">Weekly Record:</th>
							<td><?php echo $record_week['record']; ?> in <?php echo $record_week['month']->format('F Y'); ?></td>
						</tr>
						<tr id="monthrecord">
							<th scope="row">Monthly Record:</th>
							<td><?php echo $record_month['record']; ?> in <?php echo $record_month['month']->format('F Y'); ?></td>
						</tr>
					</tbody>
				</table>
			</section>
		
		</header>
		
		<section id="calendar">
			<table>
				<thead>
					<tr>
						<th id="weekcolumn" scope="col">Week</th>
						<th id="daycolumn" scope="col">Day</th>
						<?php foreach( $virtues as $key => $virtue ): ?>
						<th id="<?php echo $key; ?>column" class="<?php echo $key; ?> virtue" scope="col" colspan="<?php echo $virtue['parts']; ?>"><?php echo $virtue['name']; ?></th>
						<?php endforeach; ?>
						<th id="commentcolumn" scope="col">Comments</th>
					</tr>
				</thead>
				<?php foreach( $weeks as $week ): ?>
				<tbody id="week<?php echo $week['number']; ?>">
					<?php
					$i = 1;
					$status = "false";
					foreach( $week['days'] as $day ):
					?>
					<tr id="day<?php echo $day['date']->format('d'); ?>" date="<?php echo $day['date']->format('Y-m-d'); ?>">
						<?php if( $i == 1 ): ?>
						<td class="week number"><?php echo $week['number']; ?></td>
						<?php elseif( $i == 2): ?>
						<td class="week points"><?php echo $week['score']; ?></td>
						<?php else: ?>
						<td class="week blank">&nbsp;</td>
						<?php endif; ?>
						
						<th class="day" scope="row"><abbr title="<?php echo $day['date']->format('l, F j, Y'); ?>"><?php echo substr( $day['date']->format('D'), 0, 1 ); ?></abbr></th>
						
						<?php
						foreach( $virtues as $key => $virtue ):
							$status = "false";
							for( $k = 1; $k <= $virtue['parts']; $k++ ):
								if( $day['virtues'][$key] >= $k):
									$status = "true";
								else:
									$status = "false";
								endif;
						?>
						<td class="virtue <?php echo $key; ?> part<?php echo $k; ?> <?php echo $status; ?> parts<?php echo $virtue['parts']; ?>" virtue="<?php echo $key; ?>"><a href="#" title="<?php echo $virtue['name']; ?>"><?php echo $status; ?></a></td>
						<?php
							endfor;
						endforeach; ?>
						
						<td class="comment"><input type="text" value="<?php echo $day['comment']; ?>" /></td>
					</tr>
					<?php
					$i++;
					endforeach;
					?>
				</tbody>
				<?php endforeach; ?>
			</table>
			
			<footer>
				<p>Made by MasterMade</p>
			</footer>
	
	</div>
	
</body>
</html>