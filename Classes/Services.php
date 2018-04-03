<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 16-Sep-17
 * Time: 17:38
 */

include_once "vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php";

class Credential
{
    const CREDENTIALS_FILES_ABSOLUTE_PATH = \BugOrderSystem\Constant::SYSTEM_LOCAL_ABSOLUTE_PATH."/CredentialFiles";

    private $fileName;
    private $username;
    private $password;

    public function __construct($fileName, $username, $password) {
        $this->fileName = $fileName;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function GetFileName() {
        return $this->fileName;
    }

    /**
     * @return mixed
     */
    public function GetUsername() {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function GetPassword() {
        return $this->password;
    }

    /**
     * @param $Name
     * @param string $credentialFileLoc
     * @return bool|Credential
     */
    public static function GetCredential($Name, string $credentialFileLoc = self::CREDENTIALS_FILES_ABSOLUTE_PATH)
    {
        try {
            $CredentialData = self::getCredentialFile($Name, $credentialFileLoc);
            if ($CredentialData !== false)
            {
                $credential = new Credential($Name, $CredentialData->username, $CredentialData->password);
                return $credential;
            }
        }
        catch (Throwable $e) {
            Services::dump($e->getMessage());
        }
        return False;
    }

    /**
     * @param $filename
     * @param string $credentialFileLoc
     * @return SimpleXMLElement
     * @throws Exception
     */
    private static function getCredentialFile($filename, $credentialFileLoc = self::CREDENTIALS_FILES_ABSOLUTE_PATH)
    {
        $approvedExtFunction = array("xml" => "simplexml_load_file");

        // Check if approved extention //
        unset($fileExtention);
        if (strpos($filename, ".") !== false) {
            $file_parts = pathinfo($filename);
            $fileExtention = $file_parts['extension'];
            if (!array_key_exists($fileExtention, $approvedExtFunction)) {
                throw new \Exception("un-supported credential file extention (".$file_parts['extension'].")!");
            }
        }
        else {
            $fileExtention = "xml";
            $filename .= ".".$fileExtention;
        }

        //Get and read file Data
        unset($ret);
        $loginFile = $credentialFileLoc.'/'.$filename;
        if (is_file($loginFile) && file_exists($loginFile) && is_readable($loginFile)) {
            $ret = call_user_func($approvedExtFunction[$fileExtention], $loginFile);

            if (isset($ret) && !empty($ret))
                return $ret;
        }

        throw new \Exception("unable to resolve filename '".$filename."' data!");
    }
}

class Services
{
    /**
     * @param $var
     * @param bool $echo
     * @return string
     */
    public static function dump($var, $echo = TRUE) {
        ob_start();
        var_dump($var);

        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", ob_get_clean());

        if(PHP_SAPI == "cli") {
            $output = PHP_EOL . PHP_EOL . $output . PHP_EOL;
        }
        else {
            $output = "<pre dir='ltr'>" . htmlspecialchars($output, ENT_QUOTES, "utf-8") . "</pre>";
        }

        if($echo) echo($output);

        return $output;
    }

    /**
     * @return bool
     */
    public static function isMobile() {
        if (class_exists("Mobile_Detect")) {
            //using mobile detect class
            $mobileDetect = new Mobile_Detect();
            return $mobileDetect->isMobile();
        }
        else {
            //inner check
            if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]))
                return true;
        }
        return false;
    }

    /**
     * @param $array
     * @param $column
     * @return array
     */
    public static function ArrayColumn($array, $column) {
        if(!function_exists("array_column")) {
            return array_map(function($element) use($column){return $element[$column];}, $array);
        }
        else {
            return array_column($array, $column);
        }
    }

    /**
     * @param $total
     * @param bool $ArrayImplode
     * @return array|bool|string
     */
    public static function RandChars($total, $ArrayImplode=true) {
        if (is_numeric($total) && $total > 0) {
            $ret=Array();
            for($i=0; $i<$total; $i++) {
                $rand_int=rand(35,126);
                $ret[]=chr($rand_int);
            }

            if (is_array($ret) && count($ret)>0) {
                $ArrayImplode ? $ret=implode('',$ret) : false;
                return $ret;
            }
        }

        return false;
    }

    /**
     * @param string|null $multi
     * @param string $delimiter
     * @return array
     */
    public static function MultiToArray(string $multi = null, $delimiter = ",") {
        if (empty($multi) || $multi == "")
            return array();

        return @explode($delimiter, $multi);
    }

    /**
     * @param array $array
     * @param string $delimiter
     * @return string
     */
    public static function ArrayToMulti(array $array, $delimiter = ",") {
        if(empty($array) || count($array) == 0)
            return "";

        return @implode($delimiter,$array);
    }

    /**
     * @param \DateTime $date1
     * @param string|\DateTime $date2
     * @param string $format
     * @return string
     */
    public static function DateDiff(DateTime $date1, $date2 = "now", string $format = "%y שנים ו-%m חודשים") {
        if (!($date2 instanceof \DateTime))
            $date2 = new \DateTime($date2);

        if (empty($format))
            $format = "%y שנים ו-%m חודשים";

        return $date1->diff($date2)->format($format);
    }

    /**
     * @param string $format
     * @return string
     */
    public static function DateNow($format = "DATE_W3C") {
        $now = new \DateTime();
        return $now->format($format);
    }

    /**
     * @param $str
     * @param $name
     * @param $value
     */
    public static function setPlaceHolder(&$str, $name, $value) {
        $str = str_replace("{".$name."}", $value, $str);
    }

    /**
     * @param string $Directory
     * @return mixed
     */
    public static function GetBaseDir($Directory='[a-z]+.php')
    {
        //var_dump($_SERVER);
        $Directory=str_replace('.','\.',$Directory);
        $Directory=str_replace('/','\/',$Directory);
        $pattern = '/'.$Directory.'[a-z0-9\/\.]*/i';
        $url=$_SERVER['SCRIPT_FILENAME'];
        $baseDir=@preg_replace($pattern,'',$url) . $Directory . '/';
        //var_dump($baseDir);

        return $baseDir;
    }

    /**
     * @param $dataToCheck
     * @return bool
     */
    public static function is_serialized($dataToCheck) {
        $data = @unserialize($dataToCheck);
        if ($dataToCheck === 'b:0;' || $data !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $string
     * @param string $AllowPattern
     * @return string
     */
    public static function StripString(string $string, string $AllowPattern = "/[^a-z_.0-9:#א-ת\(\)\,\/\"\'\+\-\&\`\-\s]/i") {
        //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace($AllowPattern, '', $string); // Removes special chars.

        return trim(preg_replace('/\t+/', '', $string)); // Replaces multiple spaces with single one.
    }
}