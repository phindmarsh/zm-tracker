<?php 

define ('NOW_PLAYING_URL', 'http://zmonline.com/PlNowPlayingData/');
define ('DEBUG', true);

require_once 'simplehtmldom_1_5/simple_html_dom.php';
require_once 'db.inc.php';

file_put_contents('lastupdate', time());

// get the latest five songs and store them
$feed = file_get_html(NOW_PLAYING_URL);
$items = $feed->find('li');
$count_back = count($items);

if(isset($_GET['cron']))
	write_log('Checking '.$count_back.' songs via cron');
else {
include 'header.php'; ?>

<div class="pull-right">
	<a href="index.php" class="btn btn-primary"><i class="icon-download-alt icon-white"></i> List</a>
</div>
<h2>Updating recently played</h2>
<pre class="well" style="margin-top:2em;">

<?php
}

$play_history = get_recent_plays($count_back + 5);
$updated_songs = array();

foreach($items as $container){
	$title = $container->find('div.songTitle', 0)->plaintext;
	$artist = $container->find('div.songArtist', 0)->plaintext;
	
	$song = load_song($title, $artist);
	if(!isset($updated_songs[$song['song_id']]) && !isset($play_history[$song['song_id']])){
		$updated_songs[$song['song_id']] = $song;
		create_play($song);
	}

}


function load_song($title, $artist){
	write_log("Loading song $title by $artist", true);
	$result = DB::select('SELECT * FROM song WHERE title = "'.DB::escape($title).'" AND artist = "'.DB::escape($artist).'"');
	
	if(empty($result)){
		write_log("Creating $title by $artist");
		DB::query('INSERT INTO song (title, artist) VALUES ("'.DB::escape($title).'", "'.DB::escape($artist).'")');
		$result = array('song_id' => DB::last_insert_id(),
						'title'   => $title,
						'artist'  => $artist);
	}
	else {
		$result = array_pop($result);
	}
	
	return $result;
	
}

function create_play($song, $time = null){
	if($time === null) $time = time();
	
	$timestamp = date('Y-m-d H:i:00', $time);
	if(empty($play)){
		write_log("Creating play for {$song['title']} at $timestamp");
		DB::query('INSERT INTO play (song_id, timestamp) VALUES ('.$song['song_id'].', "'.$timestamp.'")');
	}
}

function write_log($message, $verbose = false){
	if(!$verbose || isset($_GET['verbose'])){
		DB::query('INSERT INTO log (message) VALUES ("'.DB::escape($message).'")');
		if(DEBUG) echo "$message\n";
	}
}

function get_recent_plays($limit = 6){
	$plays = DB::select('SELECT * FROM play ORDER BY timestamp DESC LIMIT '.$limit);
	
	$results = array();
	foreach($plays as $play)
		$results[$play['song_id']] = $play;
	
	return $results;
}


if(isset($_GET['cron'])) exit();
?>
</pre>
<?php include 'footer.php'; ?>
