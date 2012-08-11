<?php

$DB=new mysqli("cwsf.cpfx.ca","pvcwsf_public","public","powervcwsf");
define("MODE_PROJECTS",0);
define("MODE_PARTICIPANTS",1);
if (isset($_REQUEST["mode"]) && $_REQUEST["mode"]=="participants"){
	
	$MODE=MODE_PARTICIPANTS;
} else {
	$MODE=MODE_PROJECTS;
}
