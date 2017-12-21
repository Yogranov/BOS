<?php
/**
 * Created by PhpStorm.
 * User: Ohad Shalev
 * Date: 02/12/2017
 * Time: 13:27
 */

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

require_once "ILogRead.php";
require_once __DIR__ . '/Message.php';
require __DIR__ . '/vendor/autoload.php';

class FileHandler extends RotatingFileHandler implements ILogRead
{
    const FILE_PER_DAY = 'Y-m-d';
    const FILE_PER_MONTH = 'Y-m';
    const FILE_PER_YEAR = 'Y';

    /**
     * FileHandler constructor.
     * @param $filename
     * @param int $maxFiles
     * @param $level
     * @param bool $bubble
     * @param null $filePermission
     * @param bool $useLocking
     */
    public function __construct($filename, $maxFiles = 0, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false) {
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);
    }

    /**
     * @param int $rows
     * @param DateTime|null $TimeFrom
     * @param DateTime|null $TimeTo
     * @return array
     */
    public function Read(int $rows = 0, DateTime $TimeFrom = null, DateTime $TimeTo = null) {
        $object = new SplFileObject($this->getTimedFilename(),"a+");
        $contents = array();
        while(!$object->eof()) {
            $lineData = $object->fgets();

            if (!empty($lineData)) {
                array_push($contents, new \Log\Message($lineData));
            }
        }

        usort($contents, function(\Log\Message $a, \Log\Message $b) {
            if(  $a->GetTime() ==  $b->GetTime() ){ return 0 ; }
            return ($a->GetTime() < $b->GetTime()) ? 1 : -1;
        });

        return $contents;
    }

}