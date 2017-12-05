<?php
/**
 * Created by PhpStorm.
 * User: Ohad Shalev
 * Date: 19/11/2017
 * Time: 09:28
 */

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

require_once "ILogRead.php";
require_once __DIR__ . '/Message.php';
require __DIR__ . '/vendor/autoload.php';

class MysqliDBHandler extends AbstractProcessingHandler implements ILogRead
{
    const DEFAULT_DB_TABLE_NAME = "monolog";

    private $dbTable;
    private $initialized = false;
    private $mysqliDb;

    /**
     * MysqliDBHandler constructor.
     * @param MysqliDb $mysqliDb
     * @param $level
     * @param string $DbTable
     * @param bool $bubble
     */
    public function __construct(MysqliDb $mysqliDb, $level = Logger::DEBUG, string $DbTable = self::DEFAULT_DB_TABLE_NAME, bool $bubble = true) {
        $this->mysqliDb = $mysqliDb;

        if (empty($DbTable))
            $DbTable = self::DEFAULT_DB_TABLE_NAME;
        $this->dbTable = $DbTable;

        parent::__construct($level, $bubble);
    }

    /**
     * @param array $record
     */
    protected function write(array $record) {
        if (!$this->initialized) {
            $this->initialize();
        }

        $data = array(
            'channel' => $record['channel'],
            'level' => $record['level'],
            'message' => $record['formatted'],
            'time' => $record['datetime']->format('U'),
        );

        $Success = $this->mysqliDb->insert($this->dbTable, $data);
    }

    /**
     * @param int $rows
     * @param DateTime|null $TimeFrom
     * @param DateTime|null $TimeTo
     * @return array
     * @throws Exception
     */
    public function Read(int $rows = 0, DateTime $TimeFrom = null, DateTime $TimeTo = null) {
        // TODO: Implement Message Class to hold the message object

        if ($TimeFrom !== null)
            $this->mysqliDb->where("time", $TimeFrom->format("U"), ">=");
        if ($TimeTo !== null)
            $this->mysqliDb->where("time", $TimeTo->format("U"), "<=");

        $this->mysqliDb->orderBy("time", "Desc");

        if ($rows == 0)
            $rows = null;

        $content = array();
        $export = $this->mysqliDb->get($this->dbTable, $rows);
        foreach ($export as $dbLogLine) {
            if (!empty($dbLogLine)) {
                //Services::dump($dbLogLine);
                $messageObject = new \Log\Message($dbLogLine["message"]);
                $messageObject->SetChannel($dbLogLine["channel"]);
                $messageObject->SetLevel(\Log\ELogLevel::search($dbLogLine["level"]));
                $messageObject->SetTime(new DateTime($dbLogLine["time"]));
                array_push($content, $messageObject);
            }
        }

        return $content;
    }

    /**
     *
     */
    private function initialize() {

        $tableCreateQuery = 'CREATE TABLE IF NOT EXISTS '.$this->dbTable.' (channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED)';
        $this->mysqliDb->rawQueryOne($tableCreateQuery);

        $this->initialized = true;
    }
}