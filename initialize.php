<?php
if(session_id() == '') {
    session_start();
}
require_once 'DotMailerConnect.php';
?>