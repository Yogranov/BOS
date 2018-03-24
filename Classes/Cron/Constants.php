<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 3/17/2018
 * Time: 10:58 AM
 */

namespace CronJob;


class Constants {
    const DEBUG = false;
    const SYSTEM_TIMEZONE = "Asia/Jerusalem";
    const EMAIL_SEND = true;
    const ALLOWED_IPS = array(
        "LocalServerIp" => "88.99.28.98",
        "OhadServer" => "62.75.151.35",
        //"YogevIp" => "",
        "OhadIP" => "84.108.156.186"
    );
    const LOG_TEXT_PREPEND = "BOS CronJob";
}