<?php
/**
 * Created by PhpStorm.
 * User: Ohad Shalev
 * Date: 19/11/2017
 * Time: 09:28
 */

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

require __DIR__ . '/vendor/autoload.php';

class MysqliDBHandler extends AbstractProcessingHandler
{
    const DEFAULT_DB_TABLE_NAME = "monolog";

    private $dbTable;
    private $initialized = false;
    private $mysqliDb;

    public function __construct(MysqliDb $mysqliDb, $level = Logger::DEBUG, string $DbTable = "", bool $bubble = true) {
        $this->mysqliDb = $mysqliDb;

        if (empty($DbTable))
            $DbTable = self::DEFAULT_DB_TABLE_NAME;
        $this->dbTable = $DbTable;

        parent::__construct($level, $bubble);
    }

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
        /*
        if(!$Success)
            throw new \Exception("Unable to Add new DB Log");
        */
    }

    private function initialize() {

        $tableCreateQuery = 'CREATE TABLE IF NOT EXISTS '.$this->dbTable.' (channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED)';
        $this->mysqliDb->rawQueryOne($tableCreateQuery);

        $this->initialized = true;

        /*
        $this->mysqliDb->exec(
            'CREATE TABLE IF NOT EXISTS monolog '
            .'(channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED)'
        );
        $this->statement = $this->mysqliDb->prepare(
            'INSERT INTO monolog (channel, level, message, time) VALUES (:channel, :level, :message, :time)'
        );
        */
    }
}