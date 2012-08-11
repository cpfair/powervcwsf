<?php
include "common.php";
include "query_build.php";

$qb=new ProjectsQueryBuilder();
$qb->Prepare($_REQUEST);
$query=$qb->Compile(false);

$preQueryTime=microtime();

$res=$DB->query($query);
if (!$res) die($DB->error);

//json_encode sux
function nullable_utf8_encode($txt){
	if (!$txt) return null;
	return utf8_encode($txt);;
}

$rows=array();
while ($row=$res->fetch_assoc()){
	$rows[]=$qb->PostProcessRow(array_map('nullable_utf8_encode', $row));
}
$totalCt=$DB->query("SELECT FOUND_ROWS()");
$totalCt=$totalCt->fetch_assoc();
$totalCt=$totalCt["FOUND_ROWS()"];

//$totalCt=$totalCt->ct;
$result=new stdClass();
$result->rows=$rows;
$result->totalCount=$totalCt;
$result->queryTime=round((microtime()-$preQueryTime)*1000);
//var_dump($rows);
echo json_encode($result);