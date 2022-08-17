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
namespace BitTorrent\Protocol;
use \Exception;
use \stdClass;

/**
 * BEP3.
 *
 * @see http://bittorrent.org/beps/bep_0003.html
 */
class BEP3
{
    /**
     * Create handshake message.
     *
     * @param  string   $infohash
     * @param  string   $reserved
     * @param  string   $peerid
     * @return stdClass
     */
    public function createHandshake(string $infohash, string $reserved, string $peerid) : stdClass
    {
        $result = new stdClass();
        try {
            $pstrlen = chr(19);
            $pstr = 'BitTorrent protocol';
            if (strlen($infohash) != 20) {
                throw new Exception('infohash should be 20 bytes.');
            }
            if (strlen($reserved) != 8) {
                throw new Exception('reserved should be 8 bytes.');
            }
            if (strlen($peerid) != 20) {
                throw new Exception('peerid should be 20 bytes.');
            }
            $result->status = 'success';
            $result->handshake = $pstrlen . $pstr . $extensions . $infohash . $peerid;
        } catch (Exception $e) {
            $result->status = 'failure';
            $result->reason = $e->getMessage();
        } 
        return $result;
    }

    /**
     * Decode handshake message.
     *
     * @param  string   $buffer
     * @return stdClass
     */
    public function decodeHandshake(string $buffer) : stdClass
    {
        $result = new stdClass();
        try {
            $pstrlen = substr($buffer, 0, 1);
            if ($pstrlen !== chr(19)) {
                throw new Exception('pstrlen doesn\'t contain correct value.');
            }
            $pstr = substr($buffer, 1, 20);
            if ($pstr !== 'BitTorrent protocol') {
                throw new Exception('pstr is the incorrect value.');
            }
            $reserved = substr($buffer, 20, 8);
            if (strlen($reserved) != 8) {
                throw new Exception('reserved should be 8 bytes.');
            }
            $infohash = substr($buffer, 28, 20);
            if (strlen($infohash) != 20) {
                throw new Exception('infohash should be 20 bytes.');
            }
            $peerid = substr($buffer, 48, 20);
            if (strlen($peerid)) {
                throw new Exception('peerid should be 20 bytes.');
            }
            $result->status = 'success';
            $result->pstrlen = $pstrlen;
            $result->pstr = $pstr;
            $result->reserved = $reserved;
            $result->infohash = $infohash;
            $result->peerid = $peerid;
        } catch (Exception $e) {
            $result->status = 'failure';
            $result->reason = $e->getMessage();
        }
        return $result;
    }
}
