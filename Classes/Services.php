<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 16-Sep-17
 * Time: 17:38
 */

class Services
{
    /**
     * @param $var
     * @param bool $echo
     * @return mixed|string
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
}