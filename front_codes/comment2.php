<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <meta name="viewpoint" content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>charTrue >comment page</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nanum+Gothic&display=swap" rel="stylesheet">
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
            color : white ;
        }
        table#chart td{
             text-align: center;
        }
        table#chart{
            width : 80%;
        }
        #pages{
            border-color: rgba(0,0,0,0.5) ;
            border-style:ridge;
            color :black;
            text-align: right;
            padding : 20px 100px 20px 0;
            margin : 0 auto;
            width : 70%;

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

$id=$_GET['id']; 
$title=$_GET['title'];    
    
$query = 'select * from comments where id ="'.$id.'" order by sen_score desc';
$result = mysqli_query($conn,$query);

$query_s = 'select * from songs where id ="'.$id.'"';
$result_s = mysqli_query($conn,$query_s);
$row_s = mysqli_fetch_array($result_s);

$query_p = 'select sen_sum_score from preRanking where id ="'.$id.'"';
$result_p = mysqli_query($conn,$query_p);
$row_p = mysqli_fetch_array($result_p);

echo'<h2 style="text-align: left; margin-left: 100px;">전체 댓글 페이지</h2>
<h3 style="text-align: left; margin-left: 100px;">['.$title.'/'.$row_s['artist'].']';
echo'</h3>';
echo'<h4 class="artist_link" id="pages">';
echo'※ 총 댓글 갯수 : '.$row_s['cmts_sum'].', &emsp; 감정분석 총 점수 : '.round($row_s['sen_score'],2).
', &emsp; 감정분석 환산 점수 : '.round($row_p['sen_sum_score'],2).'&emsp;&emsp;&emsp;&emsp;&emsp;';
if(strcmp($row_s['song_url_bugs'],"0"))
    echo'<a href ="comment.php?page=bugs&title='.$row_s['title'].'" style="color: #fb4233">벅스</a>';
if(strcmp($row_s['song_url_genie'],"0"))
    echo'&nbsp;<a href ="comment.php?page=genie&title='.$row_s['title'].'"  style="color:#11a6e3;">지니</a>';
if(strcmp($row_s['song_url_melon'],"0"))
    echo'&nbsp;<a href ="comment.php?page=melon&title='.$row_s['title'].'"  style="color: #01cc3b;">멜론</a>';
echo'</h4>';

echo'<h4 style=" color : gray ;text-align: left; margin-left: 130px;">
※ 이 페이지에서는 댓글별 감정분석 점수를 높은 순서대로 보여드립니다.</h4>';

$i=1;
echo'
<table id="chart">
    <tr>
    <th class="ranking" >번호</th>
    <th>댓글 내용 </th>
   
</tr>';
// <th class="ranking">점수</th>
while($row = mysqli_fetch_array($result)){
    echo'<tr><td><strong>'.$i.'</strong></td>';
    echo '<td><strong>'.$row['comment'].'</strong></td>';
    //echo'<td><strong>'.round($row['sen_score'],2).'</strong></td></tr>';
    $i=$i+1;
}
echo'</table>';
    
