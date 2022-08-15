# PHPBitTorrentLib
BitTorrent library for encoding and decoding torrents in PHP language.

This library been tested and works on PHP 7.4+

## Bdecoder example.

```
<?php

include('BitTorrent/Encoding/Bdecoder.php');

$bdecoder = new BitTorrent\Encoding\Bdecoder();
$torrent  = file_get_contents("/path/to/torrent.torrent");
$bdecoded = $bdecoder->decode($torrent);
```

## Bencoder example.

```
<?php

include('BitTorrent/Encoding/Bencoder.php');

$bencoder = new BitTorrent\Encoding\Bencoder();
$bencoded = $bencoder->encode([
    'apples' => 1,
    'pears'  => 2
]);
```

UnitTests can be located in tests directory.