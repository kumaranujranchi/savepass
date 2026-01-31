<?php
session_start();
$_SESSION = array();
session_destroy();
?>
<script>
    sessionStorage.clear();
    window.location.href = "login.php";
</script>