<?php

session_start();
session_destroy();

echo "scrPane<script>alert('Logout');
	location.reload()</script>";

?>