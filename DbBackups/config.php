<?php

use BugOrderSystem\Constant;
$SqlCredential = \Credential::GetCredential('sql_' . Constant::MYSQL_SERVER . '_' . Constant::MYSQL_SERVER_PORT . '_' . Constant::MYSQL_DATABASE);

return array(
    'backup_dir'    => Constant::DB_BACKUP_DIR,
    'keep_files'    => Constant::DB_BACKUP_FILES,
    'compression'   => Constant::DB_BACKUP_COMPRESSION,
    'db_host'       => Constant::MYSQL_SERVER,
    'db_port'       => Constant::MYSQL_SERVER_PORT,
    'db_protocol'   => Constant::MYSQL_PROTOCOL,
    'db_user'       => $SqlCredential->GetUsername(),
    'db_passwd'     => $SqlCredential->GetPassword(),
    'db_names'      => Constant::DB_BACKUP_DBS
);
