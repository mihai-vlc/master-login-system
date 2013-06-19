<?php
include "inc/init.php";

$user->logout();

header("Location: $set->url");