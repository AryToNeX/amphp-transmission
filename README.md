# amphp-transmission

I don't even know why I am open sourcing this to be fairly honest.

It's so low effort that literally anybody can do it and do it better than me.

Anyway...

## Usage

First of all, open up a terminal where your project is and type

```
composer require arytonex/amphp-transmission
```

```php

<?php
require_once "vendor/autoload.php"; // assuming you're using composer, you should use it

$host = '127.0.0.1';
$port = 9091;
$endpoint = 'transmission/rpc/';

$transmission = new \AryToNeX\Transmission\Transmission($host, $port, $endpoint);
\Amp\Loop::run(function() use ($transmission){

	// EVERYTHING SHOULD BE yieldED

	$response = yield $transmission->addTorrent(["filename" => "/some/path/myawesomemusic.torrent"]);
	// it works with magnets and URLs too
	$response = yield $transmission->removeTorrent(["ids" => [1,2,3,4,/* ...array of torrent IDs */]]);
	// replace removeTorrent with startTorrent, startTorrentNow, stopTorrent, verifyTorrent, reannounceTorrent
	$response = yield $transmission->getTorrents([
	"fields" => ["array of custom fields, refer to Transmission's RPC documentation"]
				// or just leave this field blank, so you'll have ALL OF THEM!
	]);
});
?>
```
