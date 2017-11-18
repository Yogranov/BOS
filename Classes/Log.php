<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 07-Nov-17
 * Time: 22:44
 */

namespace BugOrderSystem;

//psr's
require_once("Log/LoggerInterface.php");

require_once("ELogHandler.php");
require_once("ELogLevel.php");

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\NativeMailerHandler;


class Log
{
    /**
     * @var Logger[]
     */
    private static $log = array();

    /**
     * @param string $logName
     * @return Logger
     * @throws Exception
     */
    private static function &getLogByName(string $logName) {
        if (empty($logName))
            throw new Exception("Unable to get log Object!", $logName);

        $log = @self::$log[$logName];
        if (empty($log)) {
            self::$log[$logName] = new Logger(Constant::SYSTEM_NAME."_".$logName."_Logger");
            $log = self::$log[$logName];
        }

        return $log;
    }

    /**
     * @param $logText
     * @param ELogHandler[]|ELogHandler $Type
     * @param ELogLevel $logLevel
     * @param string $loggerName
     * @throws Exception
     */
    public static function Write($logText, $Type, ELogLevel $logLevel, string $loggerName = Constant::DEFAULT_LOG_NAME) {
        if (empty($logText))
            throw new Exception("unable to write log without a log text!");

        $logObject = &self::getLogByName($loggerName);
        $logName = Constant::DEFAULT_LOG_DIRECTORY.''.$loggerName.'.log';
        foreach ($Type as $index => $innerType) {
            switch ($innerType) {
                case ELogHandler::Email(): $logObject->pushHandler(new NativeMailerHandler(Constant::WEBMASTER_EMAIL, $loggerName, Constant::SYSTEM_NAME, $logLevel->getValue()));
                    break;
                case ELogHandler::DB():
                case ELogHandler::File():
                default: $logObject->pushHandler(new RotatingFileHandler($logName, Constant::DEFAULT_MAX_FILE, $logLevel->getValue()));
            }
        }

        try {
            $logObject->log($logLevel->getKey(), $logText);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    public static function Get() {

    }
}