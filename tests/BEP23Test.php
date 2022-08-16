<?php

use PHPUnit\Framework\TestCase;
use BitTorrent\Protocol\BEP23;

class BEP23Test extends TestCase
{
    public function testCompactEmptyPeerlist()
    {
        $peerlist = [];
        $expect = '';
        $actual = (new BEP23())->toCompactV4($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompactMissingIP()
    {
        $peerlist = [['port' => 80]];
        $expect = null;
        $actual = (new BEP23())->toCompactV4($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompactMissingPort()
    {
        $peerlist = [['ip' => '127.0.0.1']];
        $expect = null;
        $actual = (new BEP23())->toCompactV4($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompactInvalidIPv4()
    {
        $peerlist = [['ip' => '::1', 'port' => 7777]];
        $expect = null;
        $actual = (new BEP23())->toCompactV4($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompactInvalidPort()
    {
        $peerlist = [['ip' => '127.0.0.1', 'port' => 0.5]];
        $expect = null;
        $actual = (new BEP23())->toCompactV4($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testCompact()
    {
        $peerlist = [['ip' => '127.0.0.1', 'port' => 8080]];
        $expect = inet_pton('127.0.0.1') . pack('n', 8080);
        $actual = (new BEP23())->toCompactV4($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testFromCompactEmptyPeerlist()
    { 
        $peerlist = '';
        $expect = [];
        $actual = (new BEP23())->fromCompactV4($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testFromCompactInvalidLength()
    {
        $peerlist = "\x00\x00\x00\x00\x00";
        $expect = null;
        $actual = (new BEP23())->fromCompactV4($peerlist);
        $this->assertEquals($expect, $actual);
    }

    public function testFromCompact()
    {
        $peerlist = inet_pton('127.0.0.1') . pack('n', 8080);
        $expect = [['ip' => '127.0.0.1', 'port' => 8080]];
        $actual = (new BEP23())->fromCompactV4($peerlist);
        $this->assertEquals($expect, $actual);
    }
}
