<?php
mb_internal_encoding("UTF-8"); 
$DB=new mysqli("cwsf.cpfx.ca","pvcwsf_public","public","powervcwsf");
define("MODE_PROJECTS",0);
define("MODE_PARTICIPANTS",1);
if (isset($_REQUEST["mode"]) && $_REQUEST["mode"]=="participants"){
	
	$MODE=MODE_PARTICIPANTS;
} else {
	$MODE=MODE_PROJECTS;
}

$FairYears=array();
$FairYears[2005]="Vancouver";
$FairYears[2006]="Saguenay";
$FairYears[2007]="Truro";
$FairYears[2008]="Ottawa";
$FairYears[2009]="Winnipeg";
$FairYears[2010]="Peterborough";
$FairYears[2011]="Toronto";
$FairYears[2012]="Charlottetown";
$FairYears[2013]="Lethbridge";

$UpToDateYear=2012; //could pull from DB, could also just update it every year