<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 07-Nov-17
 * Time: 22:44
 */

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\NativeMailerHandler;
use MySQLHandler\MySQLHandler;
use Tylercd100\Monolog\Handler\PlivoHandler;
use Tylercd100\Monolog\Handler\TwilioHandler;
use Tylercd100\Monolog\Handler\ClickatellHandler;

require __DIR__. '/ELogLevel.php';
require __DIR__ . '/vendor/autoload.php';

class Log
{
    /**
     * @var Logger[]
     */
    private $loggerObject;
    private $name;
    private $hasHandler = array();

    /**
     * Log constructor.
     * @param string $name
     * @throws Exception
     */
    public function __construct(string $name) {
        if (empty($name))
            throw new \Exception("Unable to create log object without log name");

        $this->loggerObject = new Logger($name);
        $this->name = $name;
    }

    /**
     * @param string $logText
     * @param null $level
     * @param array $context
     * @param bool $showIp
     * @throws Exception
     */
    public function Write(string $logText, $level = null, array $context = [], bool $showIp = true) {
        if (empty($logText))
            throw new \Exception("Unable to write log without log text!");

        if (empty($level))
            $level = \Log\ELogLevel::DEBUG();

        if ($this->isLevelHandlerAssigned($level))
            throw new \Exception("no Handlers has been assigned to handle the requested Log minimum level!");

        //adding Ips to log
        if ($showIp) {
            $ipText = $_SERVER['REMOTE_ADDR'];
            if ($_SERVER['REMOTE_ADDR'] !== $_SERVER['HTTP_X_FORWARDED_FOR'])
                $ipText .= " - ".$_SERVER['HTTP_X_FORWARDED_FOR'];

            $logText = "(".$ipText.") ".$logText;
        }

        $this->loggerObject->log($level->getValue(), $logText, $context);
    }

    /**
     * @param \Log\ELogLevel $level
     * @return bool
     */
    private function isLevelHandlerAssigned(\Log\ELogLevel $level) {
        if (!is_array($this->hasHandler))
            return False;

        foreach (\Log\ELogLevel::toArray() as $name => $value) {
            if (@isset($this->hasHandler[$value]))
                return True;
            else if ($value >= $level->getValue())
                break;
        }

        return False;
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param string $fileLocation
     * @param string|null $fileName
     * @param int $maxFiles
     * @throws \Exception
     */
    public function AddFileHandler(\Log\ELogLevel $minlevel, string $fileLocation, string $fileName = null, int $maxFiles = 10) {
        if (empty($fileLocation))
            throw new \Exception("Invalid file location! (Empty)");

        if (empty($fileName) || is_null($fileName))
            $fileName = $this->name;
        else
            $fileName = $this->name.'_'.$fileName;

        $handler = new RotatingFileHandler($fileLocation.$fileName.'.log', $maxFiles, $minlevel->getValue());
        $handler->setFilenameFormat($fileName.'_{date}', RotatingFileHandler::FILE_PER_MONTH);
        $this->loggerObject->pushHandler($handler);
        $this->hasHandler[$minlevel->getValue()] = True;
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param string $toEmail
     * @param string $fromEmail
     * @throws \Exception
     */
    public function AddEmailHandler(\Log\ELogLevel $minlevel, string $toEmail, string $fromEmail = "") {
        if (empty($toEmail))
            throw new \Exception("Unable to add email handler without proper destination Email!");

        if (empty($fromEmail))
            $fromEmail = $_SERVER["HTTP_HOST"].' Logger';

        $handler = new NativeMailerHandler($toEmail, $this->name, $fromEmail, $minlevel->getValue());
        $this->loggerObject->pushHandler($handler);
        $this->hasHandler[$minlevel->getValue()] = True;
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param $dbinstance
     * @param string $DbTable
     * @param array $DbColumns
     */
    public function AddDbHandler(\Log\ELogLevel $minlevel, $dbinstance, string $DbTable, array $DbColumns) {
        $handler = new MySQLHandler($dbinstance, $DbTable, $DbColumns, $minlevel->getValue());
        $this->loggerObject->pushHandler($handler);
        $this->hasHandler[$minlevel->getValue()] = True;
    }

    /**
     * @param string $Token
     * @param string $AuthId
     * @param string $fromPhoneNumber
     * @param string $toPhoneNumber
     * @throws \Exception
     */
    public function AddSmsHandlerPLIVO(string $Token, string $AuthId,  string $toPhoneNumber, string $fromPhoneNumber) {
        if (empty($Token))
            throw new \Exception("Unable to set sms handler without provider Token!");

        if (empty($AuthId))
            throw new \Exception("Unable to set sms handler without provider AuthId!");

        if (empty($fromPhoneNumber))
            throw new \Exception("Unable to set sms handler without provider origin PhoneNumber!");

        if (empty($toPhoneNumber))
            throw new \Exception("Unable to set sms handler without provider destination PhoneNumber!");

        $handler = new PlivoHandler($Token, $AuthId, $fromPhoneNumber, $toPhoneNumber);
        $this->loggerObject->pushHandler($handler);
        $this->hasHandler[\Log\ELogLevel::DEBUG] = True;
    }

    /**
     * @param string $Token
     * @param string $AuthId
     * @param string $fromPhoneNumber
     * @param string $toPhoneNumber
     * @throws Exception
     */
    public function AddSmsHandlerTWILIO(string $Token, string $AuthId, string $toPhoneNumber, string $fromPhoneNumber) {
        if (empty($Token))
            throw new Exception("Unable to set sms handler without provider Token!");

        if (empty($AuthId))
            throw new Exception("Unable to set sms handler without provider AuthId!");

        if (empty($fromPhoneNumber))
            throw new Exception("Unable to set sms handler without provider origin PhoneNumber!");

        if (empty($toPhoneNumber))
            throw new Exception("Unable to set sms handler without provider destination PhoneNumber!");

        $handler = new TwilioHandler($Token, $AuthId, $fromPhoneNumber, $toPhoneNumber);
        $this->loggerObject->pushHandler($handler);
        $this->hasHandler[\Log\ELogLevel::DEBUG] = True;
    }

    /**
     * @param string $Token
     * @param string $toPhoneNumber
     * @param string|null $fromPhoneNumber
     * @throws Exception
     */
    public function AddSmsHandlerCLICKATELL(string $Token, string $toPhoneNumber, string $fromPhoneNumber = null) {
        if (empty($Token))
            throw new Exception("Unable to set sms handler without provider Token!");

        if (empty($toPhoneNumber))
            throw new Exception("Unable to set sms handler without provider destination PhoneNumber!");

        $handler = new ClickatellHandler($Token, $fromPhoneNumber, $toPhoneNumber);
        $this->loggerObject->pushHandler($handler);
        $this->hasHandler[\Log\ELogLevel::DEBUG] = True;
    }
}