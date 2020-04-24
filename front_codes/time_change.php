<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <title>chartrue</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src="includeHTML.js"></script>
    <script language="JavaScript">
    </script>
    <style>
        .realtime_chart{
            color: #0c2d83;
            font-weight:bold;
        }
        
    </style>
</head>
<body>
<header include-html="header.html"></header>
<script>
        includeHTML();
</script>

<h5 style=" text-align:center; margin : 5px auto;">
    ※ 현재 페이지는 선택하신 시간대의 차트루 랭킹을 보여드립니다.</h5>

<?php
$H = $_COOKIE["hour"];
$Hour = substr($H,8,2);
echo '<h2 class="clock" style="color : black">'.date("Y.m.d", time()).'
    <span style = "color : #0c2d83 ;">'.$Hour.':00
    <span class ="dropdown">
    <button type ="button" 
    id="dropdownMenuButton" data-toggle="dropdown" 
    aria-haspopup="true" aria-expanded="false"> ▼ </button>';
echo'<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
$present_time= (int)date("H");
for($var=$present_time-1;$var>=0;$var--) {
    if($var<2 || $var>6) {
        if ($var < 10)
            echo '<li><a href="time_change.php" class="dropdown-item" onclick="time_change(this);" id ="0' . $var . '" >0' . $var . ':00</a></li>';
        else
            echo '<li><a href="time_change.php" class="dropdown-item" onclick="time_change(this);" id ="' . $var . '" >' . $var . ':00</a></li>';
    }
}
echo' </ul></span></span></h2>';
//style="visibility:collapse;" 제외
echo'
    <section>
    <article id = "container">
        <div id = "contentbody">
            
            <table class = "realchart" id = "chart">
                <thead>
                <tr>
                    <th scope="col" class="ranking"><strong>순위</strong></th>
                    <th scope="col" class="rank_change"></th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="title"><strong>곡 정보</strong></th>
                    <th scope="col" class="album"><strong>앨범</strong></th>
                    <th scope="col" class="listen"><strong>듣기</strong></th>
                    <th scope="col" class="hidden" ><strong>좋아요</strong></th>
                    <th scope="col" class="hidden" ><strong>댓글수</strong></th>
                    <th scope="col" class="hidden" ><strong>점수</strong></th>
                    
                </tr>
                </thead>
                <tbody>';

$conn = mysqli_connect('localhost','root','Song123~','gradproj');
if (!$conn) die(mysqli_get_warnings());
mysqli_query($conn, "set session character_set_connection=utf8;");
mysqli_query($conn,"set session character_set_results=utf8;");
mysqli_query($conn,"set session character_set_client=utf8;");

$ranking_db = $_COOKIE["hour"];
$query = 'select * from '.$ranking_db.' order by rank asc';
$result = mysqli_query($conn,$query);
$rank_temp=0;
while($row = mysqli_fetch_array($result)){
//
//    $query_bugs = 'select * from musicList_bugs where id = "'.$row['id'].'"';
//    $result_bugs = mysqli_query($conn,$query_bugs);
//    $rowb = mysqli_fetch_array($result_bugs);
//
//    $query_melon = 'select * from musicList_melon where id = "'.$row['id'].'"';
//    $result_melon = mysqli_query($conn,$query_melon);
//    $rowm = mysqli_fetch_array($result_melon);
//
//    $query_genie = 'select * from musicList_genie where id = "'.$row['id'].'"';
//    $result_genie = mysqli_query($conn,$query_genie);
//    $rowg = mysqli_fetch_array($result_genie);
    while($row['rank']==$rank_temp)//순위가 같은 랭킹이 나오면 다음으로 건너뛰기!
    {
        $rank_temp = $row['rank'];
        $row = mysqli_fetch_array($result); 
    }
    if($row==null)
        break;

    echo'<tr>';
    echo'<td ><strong>'.$row['rank'].'</strong></td>';
    $rank_temp=$row['rank'];
    if($row['rank_change']>0)
        echo'<td><h4 style="color : orange"><img src ="up.jpg" width="10" height="10" ><strong>'.$row['rank_change'].'</strong></h4></td>';
    elseif($row['rank_change']<0)
        echo'<td><h4 style="color : cornflowerblue"><img src ="down.jpg" width="10" height="10" ><strong>'.abs($row['rank_change']).'</strong></h4></td>';
    else
        echo'<td><img src="https://img.icons8.com/officexs/16/000000/horizontal-line.png" width="20" height="10"></td>';
    echo'<td ><img src ='.$row['img_url'].' width = "50" height = "50"></td>';
    echo'<td ><div><strong>'.$row['title'].'</strong></div><br>';
    echo'<div>'.$row['artist'].'</div></td>';
    echo'<td ><strong>'.$row['album_title'].'</strong></td>';
    echo'<td >';
    if($row['song_url_melon']!=NULL)
        echo'<a href ='.$row['song_url_melon'].'><img src = "melonb.jpg" width = "30px" height="30px"></a>';
    if($row['song_url_genie']!=NULL)
        echo'<a href ='.$row['song_url_genie'].'><img src = "genieb.jpg" width = "30px" height="30px"></a>';
    if($row['song_url_bugs']!=NULL)
        echo'<a href ='.$row['song_url_bugs'].'><img src = "bugsb.jpg" width = "30px" height="30px"></a></td>';
    echo'<td class="hidden" ><strong>'.$row['like_sum'].'</strong></td>';
    echo'<td class="hidden" ><strong>'.$row['comments_sum'].'</strong></td>';
    echo'<td class="hidden" ><strong>'.$row['score_sum'].'</strong></td>';
    echo '</tr>';
}
mysqli_close($conn);
?>
</tbody>
</table>
</div>
</article>
</section>
</body>
</html>


