<?php
/* This is the script to install the Mediawiki Akismet
 * extension.  It will create the database table needed
 * for the edits that Akismet thinks are spam.
 *
 * Started on: 2007.12.18
 *
 * Copyright 2007 Adam Goforth
 *
 * This file is part of Mediawiki-Akismet.
 *
 * Mediawiki-Akismet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mediawiki-Akismet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mediawiki-Akismet.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once("config.php");

$userSuccessMsg = "";
$command = $_GET['do'];

if (isset($command)){
    connectToDB($DBHost, $DBUser, $DBPass, $DBName);

    if ($command == "createtable"){
        dropDBTable();
        createDBTable();
        $userSuccessMsg = "Successfully created the table";
    }

    closeDBConn();
}


function connectToDB($host, $user, $password, $database){
    $link = mysql_connect($host, $user, $password)
        or die('Could not connect: ' . mysql_error());
    mysql_select_db($database) or die('Could not select database');
}

function closeDBConn(){
    mysql_close();
}

function dropDBTable(){
    $dropQuery = file_get_contents("drop_table.sql");
    mysql_query($dropQuery) or die('Query failed: ' . mysql_error());
}

function createDBTable(){
    $createQuery = file_get_contents("create_table.sql");
    mysql_query($createQuery) or die('Query failed: ' . mysql_error());
}
?>
<html>
<head>
    <title>Install Mediawiki Akismet Extension</title>
</head>
<body>
    <div style="color: green;"><h3><?=$userSuccessMsg ?></h3></div>
    <p>Click the link to install: <a href="?do=createtable">Recreate the table</a><br /><span style="color: red">Warning: This will delete all Akismet data from the database, if it exists.</span></p>
</body>
</html>
