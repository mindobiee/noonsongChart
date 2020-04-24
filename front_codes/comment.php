<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <meta name="viewpoint" content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>charTrue >comment page</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <link rel ="stylesheet" media="(max-width:320px)" href="mobile.css">
    <script src ="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src ="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src= "includeHTML.js"></script>
    <script language="JavaScript">
    </script>
    <style>
        table#chart th{
            background-color: #0c2d83;
            height : 10px;
            text-align: center;
            color : white;
        }

    </style>
</head>
<body>
<header include-html="header.html"></header>
<script>
        includeHTML();
</script>

<?php

$conn = mysqli_connect('localhost','root','Song123~','gradproj');
if (!$conn) {
    die(mysqli_get_warnings());
}
mysqli_query($conn, "set session character_set_connection=utf8;");
mysqli_query($conn,"set session character_set_results=utf8;");
mysqli_query($conn,"set session character_set_client=utf8;");

$page=$_GET['page'];
$title=$_GET['title'];


if($page=="hotartist"){
    $db = "comments";
    $db2 = "ranking_now";
    $page= "전체";
} 
elseif($page=="bugs") {
    $db = "comments_bugs";
    $db2 = "musicList_bugs";
}
elseif ($page=="melon") {
    $db = "comments_melon";
    $db2 = "musicList_melon";
}
elseif($page=="genie") {
    $db = "comments_genie";
    $db2 = "musicList_genie";
}
$query0 = 'select * from '.$db2.' where title ="'.$title.'"limit 1';
$result0 = mysqli_query($conn,$query0);
$row0 = mysqli_fetch_array($result0);
$id = $row0['id'];
$artist = $row0['artist'];

$query = 'select * from '.$db.' where id ="'.$id.'"order by time_of_crawl desc';
$result = mysqli_query($conn,$query);

echo'<h2 style="text-align: left; margin-left: 100px;">댓글 페이지 ['.$page.']</h2>
<h3 style="text-align: left; margin-left: 100px;">['.$title.'/'.$artist.']</h3>';

$i=1;
echo'
<table id="chart">
    <tr>
    <th class="ranking">번호</th>
    <th>아이디명</th>
    <th>댓글 내용 </th>
   
    <th>크롤링 시각</th>
</tr>';
// <th class="ranking">점수</th>
    
while($row = mysqli_fetch_array($result)){
    echo'<tr><td><strong>'.$i.'</strong></td>';
    echo '<td><strong>'.$row['writerId'].'</strong></td>';
    echo '<td><strong>'.$row['comment'].'</strong></td>';
    //echo'<td><strong>'.round($row['sen_score'],2).' </strong></td>';
    echo'<td><strong>'.$row['time_of_crawl'].'</strong></td></tr>';
    $i=$i+1;
}
echo'</table>';


