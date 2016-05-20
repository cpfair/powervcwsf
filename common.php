<?php
mb_internal_encoding("UTF-8");
$DB=new mysqli("cwsf-db.cpfx.ca","pvcwsf_public","public","powervcwsf");
$DB->set_charset("latin1"); // just so we're clear...
$DB->set_charset("utf8"); // just so we're clear...
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
$FairYears[2014]="Windsor";
$FairYears[2015]="Fredericton";
$FairYears[2016]="Montréal";

$UpToDateYear=2016; //could pull from DB, could also just update it every year

define ("CAT_CHALLENGE",1);
define ("CAT_DIVISION",2);
$CategoryTypes=array(
	'computinginformationtechnology'=>CAT_DIVISION,
	'engineering'=>CAT_DIVISION,
	'automotive'=>CAT_DIVISION,
	'environmentalinnovation'=>CAT_DIVISION,
	'biotechnology'=>CAT_DIVISION,
	'earthenvironmentalsciences'=>CAT_DIVISION,
	'engineeringcomputingsciences'=>CAT_DIVISION,
	'healthsciences'=>CAT_DIVISION,
	'lifesciences'=>CAT_DIVISION,
	'physicalmathematicalsciences'=>CAT_DIVISION,
	'international'=>CAT_CHALLENGE /* ??? */,
	'discovery'=>CAT_CHALLENGE,
	'energy'=>CAT_CHALLENGE,
	'environment'=>CAT_CHALLENGE,
	'health'=>CAT_CHALLENGE,
	'information'=>CAT_CHALLENGE,
	'innovation'=>CAT_CHALLENGE,
	'resources'=>CAT_CHALLENGE
	);

$CategoryShortNames = array(
	"computinginformationtechnology" => "Computing & I.T.",
	"engineeringcomputingsciences" => "Engineering & Comp. Sci.",
	"earthenvironmentalsciences" => "Earth and Enviro. Sci.",
	"physicalmathematicalsciences" => "Physical & Math Sci."
	);
