<?php
/**
 * Created by PhpStorm.
 * User: Ohad Shalev
 * Date: 05/12/2017
 * Time: 17:24
 */

namespace Log;

//require_once __DIR__ . "/Address.php";
require_once __DIR__. '/ELogLevel.php';

class Message
{
    const DEFAULT_MESSAGE_FORMAT = "(%context.ip%_%context.username%) %message%";
    const DEFAULT_FORMAT = "[%datetime%] %channel%.%level_name%: " . self::DEFAULT_MESSAGE_FORMAT . " %context% %extra%\r\n";
    const DEFAULT_DATETIME = "Y-m-d H:i:s";
    private $message;
    private $time;
    private $level;
    private $channel;
    private $ip;
    private $context;
    private $user;

    /**
     * Message constructor.
     * @param string $message
     * TODO: need to finalize the constructor - match the message with the format map
     */
    public function __construct(string $message) {
        //\Services::dump($message);

        $format = str_replace("%", "", self::DEFAULT_FORMAT);
        //\Services::dump($format);

        $formatMap = $this->getFormatMap();
        //\Services::dump($formatMap);
    }

    /**
     * @return array
     */
    private function getFormatMap() {
        $formatMapArray = array();
        $FormatMapData = explode(" ", self::DEFAULT_FORMAT);

        foreach ($FormatMapData as $var) {
            $string = "";
            $stringFound = false;
            foreach (str_split($var) as $char) {
                if (ctype_alpha($char) || ($stringFound && $char == ".")) {
                    $string .= $char;
                    $stringFound = true;
                }
                else if ($stringFound) {
                    array_push($formatMapArray, $string);
                    $string = "";
                    $stringFound = false;
                }
            }
        }

        return $formatMapArray;
    }

    /**
     * @param \DateTime $time
     */
    public function SetTime(\DateTime $time) {
        $this->time = $time;
    }

    /**
     * @param ELogLevel $level
     */
    public function SetLevel(ELogLevel $level) {
        $this->level = $level;
    }

    /**
     * @param string $channel
     */
    public function SetChannel(string $channel) {
        $this->channel = $channel;
    }


    /**
     * @return string
     */
    public function GetMessage() {
        return $this->message;
    }

    /**
     * @return \DateTime
     */
    public function GetTime() {
        return $this->time;
    }

    /**
     * @return ELogLevel
     */
    public function GetLevel() {
        return $this->level;
    }

    /**
     * @return string
     */
    public function GetChannel() {
        return $this->channel;
    }

    /**
     * @return Address
     */
    public function GetIp() {
        return $this->ip;
    }

    /**
     * @return array
     */
    public function GetContext() {
        return $this->context;
    }

    /**
     * @return string
     */
    public function GetUser() {
        return $this->user;
    }
}