<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 07-Nov-17
 * Time: 22:44
 */

use Monolog\Logger;

use Monolog\Processor\PsrLogMessageProcessor;

use \Monolog\Formatter\LineFormatter;

use Monolog\Handler\DeduplicationHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\LogglyHandler;
use MySQLHandler\MySQLHandler;
use Tylercd100\Monolog\Handler\PlivoHandler;
use Tylercd100\Monolog\Handler\TwilioHandler;
use Tylercd100\Monolog\Handler\ClickatellHandler;
use Log\Message;

require __DIR__. '/ELogLevel.php';
require __DIR__. '/MysqliDBHandler.php';
require __DIR__. '/FileHandler.php';
require __DIR__ . '/vendor/autoload.php';

class Log
{
    const READ_INTERFACE = "ILogRead";
    private $loggerObject;
    private $name;
    private $userName;
    private $formatter;

    /**
     * Log constructor.
     * @param string $name
     * @param string $userName
     * @param DateTimeZone|null $timeZone
     * @throws Exception
     */
    public function __construct(string $name, string $userName = "", DateTimeZone $timeZone = null) {
        if (empty($name))
            throw new \Exception("Unable to create log object without log name");

        if (!empty($userName))
            $this->userName = $userName;

        if (empty($timeZone))
            $timeZone = new DateTimeZone("GMT");

        $this->loggerObject = new Logger($name);
        $this->loggerObject->pushProcessor(new PsrLogMessageProcessor);
        $this->loggerObject->setTimezone($timeZone);

        $this->formatter = new LineFormatter(Message::GetDefaultFormat(), Message::DEFAULT_DATETIME, false, true);

        $this->name = $name;
    }

    /**
     * @param string $logText
     * @param null $level
     * @param array $context
     * @param bool $showUserName
     * @param bool $showIp
     * @throws Exception
     */
    public function Write(string $logText, $level = null, array $context = [], bool $showUserName = true, bool $showIp = true) {
        if (empty($logText))
            throw new Exception("Unable to write log without log text!");

        if (empty($level))
            $level = \Log\ELogLevel::DEBUG();

        if (!$this->loggerObject->isHandling($level->getValue()))
            throw new Exception("no Handler has been assigned to handle the requested Log minimum level!");

        $username = "";
        if ($showUserName)
            $username = $this->userName;

        $context['username'] = $username;

        $ipText = "";
        if ($showIp) {
            $ipText = $_SERVER['REMOTE_ADDR'];
            if ($_SERVER['REMOTE_ADDR'] !== $_SERVER['HTTP_X_FORWARDED_FOR'])
                $ipText .= "-".$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        $context['ip'] = $ipText;

        $this->loggerObject->log($level->getValue(), $logText, $context);
    }

    /**
     * @param Throwable $e
     * @param null $dumpVar
     * @throws Exception
     */
    public function LogException(Throwable $e, $dumpVar = null) {
        $this->Write($e->getMessage(), \Log\ELogLevel::ERROR(), array($e->getTrace(), $dumpVar));
    }

    /**
     * @param string $userName
     */
    public function SetUserName(string $userName) {
        if (!empty($userName))
            $this->userName = $userName;
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param string $fileLocation
     * @param string|null $fileName
     * @param bool $bubble
     * @param int $maxFiles
     * @return FileHandler|void
     * @throws Exception
     */
    public function AddFileHandler(\Log\ELogLevel $minlevel, string $fileLocation, string $fileName = null, bool $bubble = true, int $maxFiles = 12) {
        if (empty($fileLocation))
            throw new \Exception("Invalid file location! (Empty)");

        if (empty($fileName) || is_null($fileName))
            $fileName = $this->name;
        else
            $fileName = $this->name.'_'.$fileName;

        $handler = new FileHandler($fileLocation.$fileName.'.log', $maxFiles, $minlevel->getValue(), $bubble);
        $fileFormat = $fileName.'_{date}';
        $dateFormat = FileHandler::FILE_PER_MONTH;
        $handler->setFilenameFormat($fileFormat, $dateFormat);
        $handler->setFormatter($this->formatter);
        $this->loggerObject->pushHandler($handler);
        //$this->loggerObject->pushHandler(new DeduplicationHandler($handler, null, Logger::ERROR, 1));


        return $this->getReturnedData($handler);
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param string $toEmail
     * @param string $fromEmail
     * @param bool $bubble
     * @return object|void
     * @throws Exception
     */
    public function AddEmailHandler(\Log\ELogLevel $minlevel, string $toEmail, string $fromEmail = "", bool $bubble = true) {
        if (empty($toEmail))
            throw new \Exception("Unable to add email handler without proper destination Email!");

        if (empty($fromEmail))
            $fromEmail = $_SERVER["HTTP_HOST"].' Logger';

        $handler = new NativeMailerHandler($toEmail, $this->name, $fromEmail, $minlevel->getValue(), $bubble);
        $handler->setFormatter($this->formatter);
        $this->loggerObject->pushHandler($handler);
        //$this->loggerObject->pushHandler(new DeduplicationHandler($handler, null, Logger::ERROR, 600));

        return $this->getReturnedData($handler);
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param MysqliDb $dbinstance
     * @param string $DbTable
     * @param bool $bubble
     * @return MysqliDBHandler|void
     */
    public function AddMysqliDbHandler(\Log\ELogLevel $minlevel, MysqliDb $dbinstance, string $DbTable = "", bool $bubble = true) {
        $handler = new MysqliDBHandler($dbinstance, $minlevel->getValue(), $DbTable, $bubble);
        $handler->setFormatter($this->formatter);
        $this->loggerObject->pushHandler($handler);
        //$this->loggerObject->pushHandler(new DeduplicationHandler($handler, null, Logger::ERROR, 60));

        return $this->getReturnedData($handler);
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param mysqli $dbinstance
     * @param string $DbTable
     * @param array $DbColumns
     * @param bool $bubble
     * @return MySQLHandler|void
     */
    public function AddDbHandler(\Log\ELogLevel $minlevel, mysqli $dbinstance, string $DbTable, array $DbColumns, bool $bubble = true) {
        $handler = new MySQLHandler($dbinstance, $DbTable, $DbColumns, $minlevel->getValue(), $bubble);
        $handler->setFormatter($this->formatter);
        $this->loggerObject->pushHandler($handler);
        //$this->loggerObject->pushHandler(new DeduplicationHandler($handler, null, Logger::ERROR, 60));

        return $this->getReturnedData($handler);
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param string $Token
     * @param string $AuthId
     * @param string $toPhoneNumber
     * @param string $fromPhoneNumber
     * @param bool $bubble
     * @return object|void
     * @throws Exception
     */
    public function AddSmsHandlerPLIVO(\Log\ELogLevel $minlevel, string $Token, string $AuthId, string $toPhoneNumber, string $fromPhoneNumber, bool $bubble = true) {
        if (empty($Token))
            throw new \Exception("Unable to set sms handler without provider Token!");

        if (empty($AuthId))
            throw new \Exception("Unable to set sms handler without provider AuthId!");

        if (empty($fromPhoneNumber))
            throw new \Exception("Unable to set sms handler without provider origin PhoneNumber!");

        if (empty($toPhoneNumber))
            throw new \Exception("Unable to set sms handler without provider destination PhoneNumber!");

        $handler = new PlivoHandler($Token, $AuthId, $fromPhoneNumber, $toPhoneNumber, $minlevel->getValue(), $bubble);
        $handler->setFormatter($this->formatter);
        $this->loggerObject->pushHandler($handler);
        //$this->loggerObject->pushHandler(new DeduplicationHandler($handler, null, Logger::ERROR, 600));

        return $this->getReturnedData($handler);
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param string $Token
     * @param string $AuthId
     * @param string $toPhoneNumber
     * @param string $fromPhoneNumber
     * @param bool $bubble
     * @return object|void
     * @throws Exception
     */
    public function AddSmsHandlerTWILIO(\Log\ELogLevel $minlevel, string $Token, string $AuthId, string $toPhoneNumber, string $fromPhoneNumber, bool $bubble = true) {
        if (empty($Token))
            throw new Exception("Unable to set sms handler without provider Token!");

        if (empty($AuthId))
            throw new Exception("Unable to set sms handler without provider AuthId!");

        if (empty($fromPhoneNumber))
            throw new Exception("Unable to set sms handler without provider origin PhoneNumber!");

        if (empty($toPhoneNumber))
            throw new Exception("Unable to set sms handler without provider destination PhoneNumber!");

        $handler = new TwilioHandler($Token, $AuthId, $fromPhoneNumber, $toPhoneNumber, $minlevel->getValue(), $bubble);
        $handler->setFormatter($this->formatter);
        $this->loggerObject->pushHandler($handler);
        //$this->loggerObject->pushHandler(new DeduplicationHandler($handler, null, Logger::ERROR, 600));

        return $this->getReturnedData($handler);
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param string $Token
     * @param string $toPhoneNumber
     * @param string|null $fromPhoneNumber
     * @param bool $bubble
     * @return object|void
     * @throws Exception
     */
    public function AddSmsHandlerCLICKATELL(\Log\ELogLevel $minlevel, string $Token, string $toPhoneNumber, string $fromPhoneNumber = null, bool $bubble = true) {
        if (empty($Token))
            throw new Exception("Unable to set sms handler without provider Token!");

        if (empty($toPhoneNumber))
            throw new Exception("Unable to set sms handler without provider destination PhoneNumber!");

        $handler = new ClickatellHandler($Token, $fromPhoneNumber, $toPhoneNumber, $minlevel->getValue(), $bubble);
        $handler->setFormatter($this->formatter);
        $this->loggerObject->pushHandler($handler);
        //$this->loggerObject->pushHandler(new DeduplicationHandler($handler, null, Logger::ERROR, 600));

        return $this->getReturnedData($handler);
    }

    /**
     * @param \Log\ELogLevel $minlevel
     * @param string $Token
     * @param bool $bubble
     * @return object|void
     * @throws Exception
     */
    public function AddLogglyHandler(\Log\ELogLevel $minlevel, string $Token, $bubble = true) {
        if (empty($Token))
            throw new Exception("Unable to set sms handler without provider Token!");

        $handler = new \Monolog\Handler\LogglyHandler($Token.'/tag/monolog', $minlevel->getValue(), $bubble);
        $handler->setFormatter($this->formatter);
        $this->loggerObject->pushHandler($handler);

        return $this->getReturnedData($handler);
    }

    /**
     * @param $handler
     * @return void | object
     */
    private function getReturnedData(&$handler) {
        $reflection = new \ReflectionClass($handler);
        if ($reflection->implementsInterface(self::READ_INTERFACE))
            return $handler;
        else
            return;
    }

}