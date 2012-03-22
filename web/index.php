<?php include 'header.php'; ?>

		<div class="pull-right">
			<a href="update.php?verbose=1" class="btn btn-warning"><i class="icon-refresh icon-white"></i> Update</a>
		</div>
		<h2>Play counts for today</h2>
		
		<?php
		$today = strtotime('today');
		$timestamp = date('Y-m-d H:i:s', $today);
		$plays = DB::select('SELECT * FROM play 
							 INNER JOIN song ON play.song_id = song.song_id
							 WHERE timestamp > "'.$timestamp.'"');
		
		$top_songs = DB::select('SELECT song.*, COUNT(play.play_id) AS plays FROM song
								 INNER JOIN play ON play.song_id = song.song_id
								 WHERE play.timestamp > "'.$timestamp.'"
								 GROUP BY song.song_id
								 ORDER BY plays DESC, play.timestamp DESC');
		
		?>

		<h4>Most Played Today</h4>
		<ul class="top-songs">
			<?php foreach($top_songs as $song): ?>
			<li>
				<span class="count"><?php echo $song['plays']; ?></span>
				<?php echo $song['title']; ?> <span class="artist">by <?php echo $song['artist']; ?></span> 
			</li>
			<?php endforeach; ?>
		</ul>
		
		
		
<?php include 'footer.php'; ?>