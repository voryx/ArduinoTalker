<?php


namespace ArduinoTalker;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use React\EventLoop\LoopInterface;
use React\Stream\Stream;

/**
 * Class ArduinoTalker
 */
class ArduinoTalker implements EventEmitterInterface
{
    use EventEmitterTrait;
    /**
     * @var Stream
     */
    private $stream;

    /**
     * @var string
     */
    private $deviceName;

    /**
     * @var
     */
    private $reconnectTimer;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @param $deviceName
     * @param LoopInterface $loop
     */
    function __construct($deviceName, LoopInterface $loop)
    {
        $this->deviceName = $deviceName;
        $this->loop       = $loop;

        $this->open();

        // set a 2 second timeout before ready because this will
        // probably reset the arduino
        $this->loop->addTimer(2, [$this, 'ready']);
    }

    /**
     *
     */
    protected function open()
    {
        $fp = fopen($this->deviceName, "w+");

        if (!$fp) {
            echo "Couldn't open serial port\n";
            $this->reconnect();
            return;
        }

        echo "Connected\n";

        //Cancel timers that are already running
        if ($this->reconnectTimer) {
            $this->loop->cancelTimer($this->reconnectTimer);
        }

        $stream       = new Stream($fp, $this->loop);
        $this->stream = $stream;

        //Register Handlers
        $stream->on("data", [$this, 'onData']);
        $stream->on('close', [$this, 'onClose']);
        $stream->on('error', [$this, 'onError']);
    }

    /**
     * Handles reconnecting
     * @param int $time
     */
    private function reconnect($time = 10)
    {
        //Cancel timers that are already running
        if ($this->reconnectTimer) {
            $this->loop->cancelTimer($this->reconnectTimer);
        }

        $this->reconnectTimer = $this->loop->addPeriodicTimer($time, function () {
            echo "Attempting to reconnect\n";
            $this->open();
        });
    }

    /**
     *
     */
    public function ready()
    {

    }

    /**
     * @param $data
     */
    public function onData($data)
    {

    }

    /**
     * @param $data
     */
    public function write($data)
    {
        $this->stream->write($data);
    }

    /**
     * @return mixed
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @param mixed $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    /**
     * @param $stream
     */
    public function onClose($stream)
    {
        echo "closed\n";
        $this->reconnect(5);
    }

    /**
     * @param $stream
     */
    public function onError($stream)
    {
        echo "error\n";
        $this->reconnect(25);
    }
}
