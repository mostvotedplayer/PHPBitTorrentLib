<?php

use PHPUnit\Framework\TestCase;
use BitTorrent\Protocol\BEP7;

class BEP7Test extends TestCase
{
    public function testCompactEmptyPeerlist()
    {
        $peerlist = [];
        $expect = '';
        $actual = (new BEP7())->toCompactV6($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompactMissingIP()
    {
        $peerlist = [['port' => 80]];
        $expect = null;
        $actual = (new BEP7())->toCompactV6($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompactMissingPort()
    {
        $peerlist = [['ip' => '::1']];
        $expect = null;
        $actual = (new BEP7())->toCompactV6($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompactInvalidIPv6()
    {
        $peerlist = [['ip' => '127.0.0.1', 'port' => 7777]];
        $expect = null;
        $actual = (new BEP7())->toCompactV6($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompactInvalidPort()
    {
        $peerlist = [['ip' => '::1', 'port' => 0.5]];
        $expect = null;
        $actual = (new BEP7())->toCompactV6($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompact()
    {
        $peerlist = [['ip' => '::1', 'port' => 8080]];
        $expect = inet_pton('::1') . pack('n', 8080);
        $actual = (new BEP7())->toCompactV6($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testFromCompactEmptyPeerlist()
    { 
        $peerlist = '';
        $expect = [];
        $actual = (new BEP7())->fromCompactV6($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testFromCompactInvalidLength()
    {
        $peerlist = "\x00\x00\x00\x00\x00";
        $expect = null;
        $actual = (new BEP7())->fromCompactV6($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testFromCompact()
    {
        $peerlist = inet_pton('::1') . pack('n', 8080);
        $expect = [['ip' => '::1', 'port' => 8080]];
        $actual = (new BEP7())->fromCompactV6($peerlist);
        $this->assertEquals($expect, $actual);
    }
}
