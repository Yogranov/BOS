<?php

namespace Log;

class Address
{
	private $ip = "";
	private $port = 0;
	private $query = 0;

    /**
     * Address constructor.
     * @param $ip
     * @param $port
     * @param int $query
     * @throws \Exception
     */
    function __construct($ip, $port, $query=0) {
        if(($this->setIp($ip))!== False) {
            $ret=$this->setPort($port);
            $ret2=$this->setQuery($query);

            if (!$ret && !$ret2)
                unset($this->ip,$this->port,$this->query);
            else
                return True;
        }
        throw new \Exception("Error Creating Address Object");
    }

    /**
     * @param $ip
     * @return bool
     */
    private function setIp($ip)
    {
		if ($ip == 0 || $ip == '')
		    $ip='0.0.0.0';
		if (self::ValidateIp($ip))
        {
			$this->ip=$ip;
			return True;
        }
        else
        {
			//$this->error=$ip." is not a valid IP address!";
			return False;
        }
    }

    /**
     * @param $port
     * @return bool
     */
    private function setPort($port)
    {
        if (is_numeric($port) && $port > 0)
        {
			$this->port=$port;
			return true;
        }
        else
        {
			return false;
        }
    }

    /**
     * @param $query
     * @return bool
     */
    private function setQuery($query)
    {
        if (is_numeric($query) && $query > 0)
        {
            $this->query=$query;
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @param string $format
     * @return bool|mixed
     */
    public function FormatAddress($format='{ip}:{port} {query}')
    {
		if ($this->ip != "" && $this->port > 0)
        {
			$what_array=array('{ip}','{port}','{query}');
			$replacedValues=array($this->ip,$this->port,$this->query);
			$addr=str_replace($what_array,$replacedValues,$format);

		    return $addr;
        }
        else
        {
		    return False;
        }
    }

    /**
     * @param $ip
     * @return bool
     */
    public static function ValidateIp($ip)
    {
		if (filter_var($ip, FILTER_VALIDATE_IP) !== false)
        {
			return True;
        }
        else
        {
			return False;
        }
    }

}
?>