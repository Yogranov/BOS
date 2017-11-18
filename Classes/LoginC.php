<?php
namespace BugOrderSystem;

class LoginC {

    const COOKIE_NAME = "BugOrderSystemCookie";
    const COOKIE_EXPIRY = 86400;

    private $username;
    private $password;
    private $remember;
    private $connected = False;
    private $loginTimestamp;

    public function __construct(string $username, string $password, bool $remember = false){
        if (empty($username) || empty($password))
            throw new \Exception("unable to login without proper credentials!");

        $this->username = $username;
        $this->password = $password;
        $this->remember = $remember;
        $this->connect();
    }


    public function isConnected() {
        return $this->connected;
    }

    public static function Disconnect(&$Session) {
        $type = (@explode("|@|", Cookie::Get(self::COOKIE_NAME)))[1];
        BugOrderSystem::GetDB()->where("UserId", $Session[ucfirst($type)."Id"])->delete("cookies",1);
        Cookie::Delete(self::COOKIE_NAME);
        unset($Session);
        session_destroy();
    }


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
            $shopClass = Shop::GetById($connectedShopData["Id"]);
            $timeNow = new \DateTime( "now", new \DateTimeZone("Asia/Jerusalem"));
            $logFile = fopen("logs/EnterLog.php", "a");
            fwrite($logFile, "\n" . "<br>" . "{$timeNow->format("Y/m/d H:i:s")} - סניף <b>{$shopClass->GetShopName()}</b> - התחבר.");
            fclose($logFile);

        }
        else {
            $_SESSION["RegionId"] = $regionConnectData["Id"];
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
     * @param string $redirectHeader
     * @param $Session
     * @return bool
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
                fwrite($logFile, "\n" . "<br>" . "{$timeNow->format("Y/m/d H:i:s")} - סניף <b>{$shopClass->GetShopName()}</b> - התחבר מחדש אוטומטית.");
                fclose($logFile);
                //
                return header("Location: ".$redirectHeader);
            }
        }
        return False;
    }

}
?>