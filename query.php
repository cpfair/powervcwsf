<?php
header("Access-Control-Allow-Origin: *");

include "common.php";
include "query_build.php";

$qb=new ProjectsQueryBuilder();
$qb->Prepare($_REQUEST);
$query=$qb->Compile(false);

$preQueryTime=microtime();

$res=$DB->query($query);
if (!$res) die($DB->error);

$rows=array();
while ($row=$res->fetch_assoc()){
	$rows[]=$qb->PostProcessRow($row);
}

$totalCt=$DB->query("SELECT FOUND_ROWS()");
$totalCt=$totalCt->fetch_assoc();
$totalCt=$totalCt["FOUND_ROWS()"];


$result=new stdClass();
$result->rows=$rows;
$result->totalCount=$totalCt;
$result->queryTime=round((microtime()-$preQueryTime)*1000);
echo json_encode($result);