<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:40
 */

namespace BugOrderSystem;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "Classes/Login/vendor/autoload.php";
require_once "Classes/BugOrderSystem.php";
use Log\ELogLevel;
use Log\Message;
use SimpleAcl\Acl;
use SimpleAcl\Resource;
use SimpleAcl\Role;
use SimpleAcl\Rule;

//$shop = &Shop::GetById(94);
//$shop->SendEmail("בדיקת אימייל המהערכת", "Test2");

// SimpleAcl - https://github.com/alexshelkov/SimpleAcl/wiki/Small-usage-guide
/*
$acl = new Acl(); // create an acl

$guest = new Role('Guest'); // same for not registered or guest user
$user = new Role('User'); // create role for our registered user or just user
$guest->addChild($user); // our user will inherits all privileges from guest
$premiumUser = new Role('PremiumUser'); // add new role
$user->addChild($premiumUser);

$post = new Resource('Post'); // our post becomes resource
$starredPost = new Resource('StarredPost'); // and new post type
$post->addChild($starredPost);

$acl->addRule($guest, $post, 'View', true); // Rule #1, allow access for guest and all of his children
$acl->addRule($user, $post, 'Create', true); // Rule #2
$acl->addRule($premiumUser, $starredPost, 'View', true); // Rule #3, allow access to StarredPost for PremiumUser
$acl->addRule($guest, $starredPost, 'View', false); // Rule #4, and deny for anybody else

var_dump($acl->isAllowed('Guest', 'StarredPost', 'View')); // false
var_dump($acl->isAllowed('User', 'StarredPost', 'View')); // false
var_dump($acl->isAllowed('PremiumUser', 'StarredPost', 'View')); // true

*/
