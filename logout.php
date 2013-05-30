<?php
include "inc/init.php";

session_unset('user');
$path_info = parse_url($set->url);
setcookie("user", 0, time() - 3600 * 24 * 30, $path_info['path']); // delete
setcookie("pass", 0, time() - 3600 * 24 * 30, $path_info['path']); // delete

header("Location: $set->url");