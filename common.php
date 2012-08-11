<?php

$DB=new mysqli("localhost","root","","cpfxca_pvcwsf");
define("MODE_PROJECTS",0);
define("MODE_PARTICIPANTS",1);
if (isset($_REQUEST["mode"]) && $_REQUEST["mode"]=="participants"){
	
	$MODE=MODE_PARTICIPANTS;
} else {
	$MODE=MODE_PROJECTS;
}

$ROOT="http://127.0.0.1/powervcwsf/";