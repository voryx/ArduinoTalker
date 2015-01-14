<?php

require_once __DIR__ . '/../vendor/autoload.php';

class SimpleSketchTalker extends \ArduinoTalker\ArduinoTalker {
    public function onData($data) {
        echo "Received \"" . $data . "\"\n";
    }

    public function sendAnX() {
        $this->write("X");
        echo "Sent \"X\"... ";
    }
}

$loop = React\EventLoop\Factory::create();

$talker = new SimpleSketchTalker('/dev/cu.usbmodemfd121', $loop);

$loop->addPeriodicTimer(5, [$talker, "sendAnX"]);

$loop->nextTick([$talker, "sendAnX"]);

$loop->run();