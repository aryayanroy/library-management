<?php
    session_start();
    session_destroy();
    echo "Successfully logged out";
    header("refresh:2; URL=/library-management");
?>