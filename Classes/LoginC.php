<?php
namespace BugOrderSystem;

use Log\ELogLevel;

class LoginC {

    const COOKIE_NAME = "BugOrderSystemCookie";
    const COOKIE_EXPIRY = 86400;

    private $username;
    private $password;
    private $remember;
    private $connected = False;
    private $loginTimestamp;

    /**
     * LoginC constructor.
     * @param string $username
     * @param string $password
     * @param bool $remember
     * @throws \Exception
     */
    public function __construct(string $username, string $password, bool $remember = false){
        if (empty($username) || empty($password))
            throw new \Exception("unable to login without proper credentials!");

        $this->username = $username;
        $this->password = $password;
        $this->remember = $remember;
        $this->connect();
    }


    /**
     * @return bool
     */
    public function isConnected() {
        return $this->connected;
    }

    /**
     * @param $Session
     * @throws \Exception
     */
    public static function Disconnect(&$Session) {
        $type = (@explode("|@|", Cookie::Get(self::COOKIE_NAME)))[1];
        $connectedAs = self::ConnectedAs();
        $userId = &$Session[$connectedAs];
        $userObject = call_user_func(ucfirst(strtolower(substr($connectedAs, 0, strlen($connectedAs)-2))).'::GetById('.$userId.')');
        if (!empty($type))
            BugOrderSystem::GetDB()->where("UserId", $userId)->delete("cookies",1);

        Cookie::Delete(self::COOKIE_NAME);
        unset($Session);
        session_destroy();

        $logText = "המשתמש {$userId} התנתק מהמערכת";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array($userObject));
    }

    /**
     * @return bool|string
     */
    public static function ConnectedAs() {
        $shopId = @$_SESSION["ShopId"];
        $regionId = @$_SESSION["RegionId"];

        if(!isset($shopId) && !isset($regionId)) {
            return false;
        }
        elseif (isset($regionId)) {
            return 'RegionId';
        }
        else {
            return 'ShopId';
        }
    }

    /**
     * @throws \Exception
     */
    private function connect() {
        if ($this->isConnected())
            throw new \Exception("Login class already connected!");

        $connectedShopData = BugOrderSystem::GetDB()->where("Id", $this->username)->where("Password", $this->password)->getOne("shops");
        $regionConnectData = BugOrderSystem::GetDB()->where("Id", $this->username)->where("Password", $this->password)->getOne("regions");

        if (empty($connectedShopData) && empty($regionConnectData))
            throw new \Exception("שם המשתמש או הסיסמה אינם נכונים");

        $this->connected = True;
        $this->loginTimestamp = new \DateTime();

        if($connectedShopData) {
            $_SESSION["ShopId"] = $connectedShopData["Id"];
            $type = "shop";

            //Log
            $shopClass = &Shop::GetById($connectedShopData["Id"]);
            $logText = "סניף {$shopClass} התחבר";
            BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array($connectedShopData["Id"]));
        }
        else {
            $_SESSION["RegionId"] = $regionConnectData["Id"];
            $logText = "מנהל אזור ".$regionConnectData["Id"]." התחבר";
            BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array($regionConnectData["Id"]));
            $type = "region";
        }

        if ($this->remember) {
            $hash = md5(uniqid());
            $hashCheck = BugOrderSystem::GetDB()->where("UserId", $this->username)->getOne("cookies", "Hash");
            $hashCheck = $hashCheck["Hash"];

            if (count($hashCheck) == 0)
                BugOrderSystem::GetDB()->insert("cookies", array("UserId" => $this->username, "Hash" => $hash));
            else
                $hash = $hashCheck;

            Cookie::Put(self::COOKIE_NAME, $hash."|@|".$type, self::COOKIE_EXPIRY);
        }

    }

    /**
     * @param $Session
     * @param string $redirectHeader
     * @return bool|void
     * @throws Exception
     * @throws \Exception
     */
    public static function Reconnect(&$Session, string $redirectHeader = "index.php") {
        if(Cookie::Exists(self::COOKIE_NAME) && empty($Session)) {
            $hashArray = @explode("|@|", Cookie::Get(self::COOKIE_NAME));
            $hash = $hashArray[0];
            $type = $hashArray[1];

            $hashCheck = BugOrderSystem::GetDB()->where("Hash",$hash)->getOne("cookies");
            if(count($hashCheck) > 0){
                switch ($type) {
                    case 'region': $Object = &Region::GetById($hashCheck["UserId"]);
                                   $name = "RegionId";
                        break;

                    default:
                        $Object = &Shop::GetById($hashCheck["UserId"]);
                                 $name = "ShopId";
                }
                $Session[$name] = $Object->GetId();

                //Log
                $shopClass = Shop::GetById($Object->GetId());
                $timeNow = new \DateTime( "now", new \DateTimeZone("Asia/Jerusalem"));
                $logFile = fopen("logs/EnterLog.php", "a");
                $logText = "סניף {$shopClass} התחבר מחדש אוטומטית";
                fwrite($logFile, "\n" . "<br>" . "{$timeNow->format("Y/m/d H:i:s")} {$logText}.");
                fclose($logFile);
                BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array($Session[$name]));

                return header("Location: ".$redirectHeader);
            }
        }
        return False;
    }

}
?>