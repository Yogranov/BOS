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
     * @throws Exception
     */
    public function Read(int $rows = 0, DateTime $TimeFrom = null, DateTime $TimeTo = null) {
        $contents = array();

        $dir = Services::MultiToArray($this->getGlobPattern(), "/");

        //find Regex Word according to filename format
        $searchRegex = "/" . str_replace("*", "[0-9_-]+", $dir[count($dir)-1]) . "/i";

        //find log files location
        unset($dir[count($dir)-1]);
        $dir = Services::ArrayToMulti($dir, "/")."/";

        //find all files in the dir and match to the Regex Word - read if match
        foreach (scandir($dir) as $file) {
            if (preg_match($searchRegex, $file) == 1) {
                $object = new SplFileObject($dir.$file,"a+");
                while($object->valid()) {
                    $lineData = $object->fgets();
                    if (!empty($lineData))
                        array_push($contents, new \Log\Message($lineData));
                }
            }
        }

        //SORT Array
        usort($contents, function(\Log\Message $a, \Log\Message $b) {
            if( $a->GetTime() ==  $b->GetTime() )
                return 0 ;

            return ($a->GetTime() < $b->GetTime()) ? 1 : -1;
        });

        return $contents;
    }
}