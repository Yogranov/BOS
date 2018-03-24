<?php
/**
 * Created by PhpStorm.
 * User: Ohad Shalev
 * Date: 05/12/2017
 * Time: 17:24
 */

namespace Log;

require_once __DIR__ . '/Address.php';
require_once __DIR__. '/ELogLevel.php';

class Message
{
    const DEFAULT_DATETIME = "Y-m-d H:i:s";
    const DEFAULT_INTERNAL_SPACER = "*";
    const DEFAULT_SPACER = "\x9"; //ascii for TAB
    const DEFAULT_LINE_END = "\xA"; //ascii for new line

    const MESSAGE_REGEX_PATTERN = "/[–@a-z_.0-9:#\sא-ת\(\)\,\/\"\'\+\-\&\`]+/i";
    private static $defaultFormat = "[%datetime%] %channel%*%level_name% %context.ip%*%context.username% %message% %context% %extra%" . self::DEFAULT_LINE_END;

    /*
     * TODO: need to make conversion Map array in order to be more robust and replace the switch statement at lines 79-115
     *
    private $conversionMap = array(
        "datetime" => array("time", "new \DateTime({value})"),
        "level_name" => array("level", "SetLevel(ELogLevel::searchByKey({value}))"),
        "context.ip" => array("ip", "new \DateTime({value})"),
    );
    */

    private $message;
    private $time;
    private $microseconds;
    private $level;
    private $channel;
    private $ip;
    private $context;
    //private $extra;
    private $username;

    /**
     * Message constructor.
     * @param string $message
     * @throws \Exception
     */
    public function __construct(string $message) {
        $replace = array( "%" => "", self::DEFAULT_LINE_END => "");

        $formatMap = $this->getFormatMap();

        $message = str_replace(array_keys($replace), $replace, $message);
        $splitedLine = explode(self::DEFAULT_SPACER, $message);

        $MessageData = array();
        for ($i = 0; $i < count($splitedLine); $i++) {
            preg_replace("/^(\()|(\))$/i", "", $splitedLine[$i]);
            preg_match_all(self::MESSAGE_REGEX_PATTERN, $splitedLine[$i], $innerData);
            $innerData = $innerData[0];
            $map = $formatMap[$i];

            if (is_array($map)) {
                foreach ($map as $num => $innerMap) {
                    if (empty($innerData[$num]))
                        $innerData[$num] = null;
                    $MessageData[$innerMap] = $innerData[$num];
                }
            }
            else {
                if (empty($innerData[0]))
                    $innerData[0] = null;
                $MessageData[$map] = $innerData[0];
            }
        }

        //todo: need to redo - need to do with more style!
        foreach ($MessageData as $propertyName => $propertyValue) {
            switch ($propertyName) {
                case "datetime":
                    $this->time = new \DateTime($propertyValue);
                    break;

                case "level_name":
                    $this->SetLevel(ELogLevel::searchByKey($propertyValue));
                    break;

                case "context.ip":
                    $this->ip = new Address($propertyValue);
                    break;

                case "context.username":
                    $this->username = $propertyValue;
                    break;

                case "context":
                    if (!empty($propertyValue) && $propertyValue != "null") {
                        $propertyExplode = @explode(",", $propertyValue);
                        $contextDataArray = array();
                        foreach ($propertyExplode as $contextData) {
                            $contextData = str_replace('"', '', $contextData);
                            $innerContextDataArray = @explode(":", $contextData);
                            if (count($innerContextDataArray) > 1)
                                $contextDataArray[$innerContextDataArray[0]] = $innerContextDataArray[1];
                            else
                                $contextDataArray[0] = $innerContextDataArray[0];
                        }
                        $this->context = $contextDataArray;
                    }
                    break;

                default:
                    if (property_exists($this, $propertyName))
                        $this->$propertyName = $propertyValue;
            }
        }
    }

    /**
     * @return mixed
     */
    public static function GetDefaultFormat() {
        $format = str_replace(" ", self::DEFAULT_SPACER, self::$defaultFormat);
        return $format;
    }

    /**
     * @return array
     */
    private function getFormatMap() {
        $formatMapArray = array();
        $FormatMapData = explode(self::DEFAULT_SPACER, self::GetDefaultFormat());

        foreach ($FormatMapData as $var) {
            unset($data);
            preg_match_all("/%[a-z_.]+%/", $var, $matches);

            if (count($matches[0]) > 1)
                $data = $matches[0];
            else
                $data = $matches[0][0];

            $replace = array( "%" => "", self::DEFAULT_LINE_END => "", '"' => "'");
            $data = str_replace(array_keys($replace), $replace, $data);
            array_push($formatMapArray, $data);
        }

        return $formatMapArray;
    }

    /**
     * @param \DateTime $time
     */
    public function SetTime(\DateTime $time, int $microseconds = 0) {
        $this->microseconds = $microseconds;
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
        return $this->username;
    }

    /**
     * @param \ILogRead $handler
     * @param array $SearchMessageArray
     * @param \DateTime|null $fromDate
     * @param \DateTime|null $toDate
     * @return Message[]
     */
    public static function SearchMessage(\ILogRead $handler, array $SearchMessageArray, \DateTime $fromDate = null, \DateTime $toDate = null) {
        $messageObjects = $handler->Read(0, $fromDate, $toDate);
        $matchesMessages = array();
        foreach ($messageObjects as $message) {
            $meetTheRequirment = true;
            foreach ($SearchMessageArray as $searchMessage) {
                $match = @preg_match("/{$searchMessage}/i", $message->GetMessage());
                if ($match == 0 || $match == false) {
                    $meetTheRequirment = false;
                    break;
                }
            }

            if ($meetTheRequirment)
                array_push($matchesMessages, $message);
        }

        return array_reverse($matchesMessages);
    }
}