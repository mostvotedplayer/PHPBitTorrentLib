<?php
/**
* MIT License
*
* Copyright (c) 2022 Lee Howarth
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/
namespace BitTorrent;
use \BitTorrent\Encoding\Bdecoder;
use \BitTorrent\Encoding\Bencoder;
use \Exception;
use \stdClass;

/**
 * TorrentValidator.
 */
class TorrentValidator
{
    /**
     * Bdecoder.
     *
     * @var Bdecoder $bdecoder An instance of Bdecoder class.
     */
    protected $bdecoder;

    /**
     * Bencoder.
     *
     * @var Bencoder $bencoder An instance of Bencoder class.
     */
    protected $bencoder;

    /**
     * Init.
     *
     * @param  Bdecoder $bdecoder An instance of Bdecoder class.
     * @param  Bencoder $bencoder An instance of Bencoder class.
     * @return void
     */
    public function __construct(Bdecoder $bdecoder, Bencoder $bencoder)
    {
        $this->bdecoder = $bdecoder;
        $this->bencoder = $bencoder;
    }

    /**
     * Parse Torrent.
     *
     * @param  string   $data The contents of a .torrent file.
     * @return stdClass       
     */
    public function parseTorrent(string $data) : stdClass
    {
        $result = new stdClass();
        try {
            $decoded = $this->bdecoder->decode($data);
            if (! is_array($decoded)) {
                throw new Exception('Unable to decode data.');
            }
            $length = array_shift($decoded);
            if ($length !== strlen($data)) {
                throw new Exception('Decoded string length doesn\'t match original length.');
            }
            $buffer = array_shift($decoded);
            if (! isset($buffer['info'])) {
                throw new Exception('No info dictionary found.');
            } elseif (! is_array($buffer['info'])) {
                throw new Exception('Info dictionary is not an array.');
            } else {
                $info = $buffer['info'];
            }
            $encoded = $this->bencoder->encodeArr($buffer);
            if ($encoded === null) {
                throw new Exception('Unable to bencode info dictionary.');                                                                             
            } 
            if (false === strpos($data, $encoded)) {
                throw new Exception('Invalid bencoding of info dictionary detected.');
            }   
            $result->status = 'success';
        } catch (Exception $e) {
            $result->status = 'failure';
            $result->reason = $e->getMessage();
        }
        return $result;
    }
}

