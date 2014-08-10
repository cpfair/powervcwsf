<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include "common.php";
include "query_build.php";


if (@$_REQUEST["projects_ProvTerr"] == "YK") $_REQUEST["projects_ProvTerr"]  = "YT";

$qb=new ProjectsQueryBuilder();
$qb->Prepare($_REQUEST);
$query=$qb->Compile(false);

$preQueryTime=microtime();

$res=$DB->query($query);
if (!$res) die($DB->error);

$rows=array();
$idx= $qb->StartIndex;
while ($row=$res->fetch_assoc()){
	$newRow = $qb->PostProcessRow($row);
	$newRow["Index"] = $idx++;
	$rows[]=$newRow;
}

$totalCt=$DB->query("SELECT FOUND_ROWS()");
$totalCt=$totalCt->fetch_assoc();
$totalCt=$totalCt["FOUND_ROWS()"];


$result=new stdClass();
$result->rows=$rows;
$result->totalCount=intval($totalCt);
$result->pageSize = 50;
$result->pages = ceil($result->totalCount / $result->pageSize);
$result->queryTime=round((microtime()-$preQueryTime)*1000);
echo json_encode($result);