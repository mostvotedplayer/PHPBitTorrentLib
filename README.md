# PHPBitTorrentLib
BitTorrent library for encoding and decoding torrents in PHP language.

## Bdecoder example.

```
<?php

include('Bdecoder.php');

$bdecoder = new Bdecoder();
$torrent  = file_get_contents("/path/to/torrent.torrent");
$bdecoded = $bdecoder->decode($torrent);
```

## Bencoder example.

```
<?php

include('Bencoder.php');

$bencoder = new Bencoder();
$bencoded = $bencoder->encode([
    'apples' => 1,
    'pears'  => 2
]);
```
