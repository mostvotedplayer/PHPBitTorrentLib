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

/**
 * BEP7.
 *
 * The purpose of this class is to provide helper functions to aid in parsing and creating a compact peerlist for
 * just ipv6 addresses.
 *
 * @see http://bittorrent.org/beps/bep_0007.html
 */
class BEP7
{
    /**
     * Create a compact peer list.
     *
     * @param  array       $peers A list of dictionaries containg the ip and port.
     * @return string|null        A compact peerlist or null on failure.
     */
    public function toCompactV6(array $peers) : ?string
    {
        $result = '';
        foreach ($peers as $peer) {
            if (isset($peer['ip'])) {
                if (! filter_var($peer['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                    return null;
                }
            } else {
                return null;
            }
            if (isset($peer['port'])) {
                if ($peer['port'] < 1 || $peer['port'] > 65535) {
                    return null;
                }
            } else {
                return null;
            }
            $result .= inet_pton($peer['ip']) . pack('n', $peer['port']);
        }
        return $result;
    }
 
    /**
     * Decode a compact peer list.
     *
     * @param  string     $peers A compact peerlist.
     * @return array|null        A list of dictionaries containing the ip and port or null on failure.
     */
    public function fromCompactV6(string $peers) : ?array
    {
        $length = strlen($peers);
        if ($length % 18 !== 0) {
            return null;
        }
        $peerlist = [];
        for ($i = 0; $i < $length; $i += 18) {
            $ip = substr($peers, $i, 16);
            $ip = inet_ntop($ip);
            if ($ip === false) {
                return null;
            }
            $port = substr($peers, $i + 16, 2);
            $port = unpack('n', $port);
            if ($port === false) {
                return null;
            }
            $peerlist[] = ['ip' => $ip, 'port' => $port[1]];
        }
        return $peerlist;
    }
}
