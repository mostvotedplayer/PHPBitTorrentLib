# PHPBitTorrentLib
BitTorrent library for PHP.

This library been tested and works on PHP 7.4+, it originally was just a project to handle the process of encoding/decoding torrents but going forward it will include various other pieces of code.

## Bdecoder

##### Decoding a torrent example.

At present there isn't a validator in this project to validate the contents of a torrent although in the future it will do.

```
<?php

include('BitTorrent/Encoding/Bdecoder.php');
$bdecoder = new BitTorrent\Encoding\Bdecoder();
$torrent  = file_get_contents("/path/to/torrent.torrent");
$bdecoded = $bdecoder->decode($torrent);
print_r($bdecoded);
```

##### Decoding manually.

If you are not sure what type of data your dealing with then it's best to just pass the bencoded data to the decode method and it will determine what the data is, alternatively you can manually ask the decoder to try decoding a specific type as per:

###### List
```
$bdecoded = $bdecoder->decodeList('li1ei2ei3ei4ei5ee', 0);
print_r($bdecoded);
```

###### Dictionary
```
$bdecoded = $bdecoder->decodeDict('de', 0);
print_r($bdecoded);
```

###### Integer
```
$bdecoded = $bdecoder->decodeInt('i1024e', 0);
print_r($bdecoded);
```

###### String
```
$bdecoded = $bdecoder->decodeStr('3:lee', 0);
print_r($bdecoded);
```

As you probably noticed each of the calls above used a second parameter which is the starting offset, in the decode method the offset by default starts at 0 so there isn't any need to provide a starting offset.

If your dealing with a very large torrent in theory you could parse elements of the torrent by using something like strpos and passing the offset to a particular method.

Each of the methods return an array, the first element is the length of the decoded data and second is the value of the decoded data, an example of this can be seen by using a empty dictionary:

```
de
```

would yield a result such as:

```
array(
    [0] => 2, // The size of the encoded data is 2
    [1] = []  // The decoded data is an empty dictionary
)
```

another example would be:

```
li1024ei2048ee
```

would yield a result such as:

```
array(
   [0] => 14,
   [1] => array(
          [0] => 1024,
          [1] => 2048
       )
)
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
