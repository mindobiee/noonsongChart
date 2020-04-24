<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <title>chartrue >daily chart</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src= "includeHTML.js"></script>
    <style>
       
        .daily_chart{
            color: blueviolet;
            font-weight:bold;
        }
       
        table#chart th{
            background-color: blueviolet;
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
    
    $day=$_GET['day'];
    
    if($day<10)
        echo '<h2 class="clock" style="color : black">2020.04.0'.$day;
    else
         echo '<h2 class="clock" style="color : black">2020.04.'.$day;
    
    //$day_today = date("j", time());
//    if($day<10)
//        $day_today = substr(date("Y-m-d", time()), 9,1);
//    else
//        $day_today = substr(date("Y-m-d", time()), 8,2);
//        
//    $day_cha= $day - (int)$day_today;
//    
//    $yesterday = date("D", strtotime("'.$day_cha.'day",time()));
//    
//    echo' <span style = "color : blueviolet ;">'.$day_today.'/'.$yesterday;
//    if(strcmp($yesterday,"Tue")==0)
//       echo'sday';
//    else if(strcmp($yesterday,"Wed")==0)
//        echo'nesday';
//    else if(strcmp($yesterday,"Thu")==0)
//        echo'rsday';
//    else if(strcmp($yesterday,"Sat")==0)
//        echo'urday';
//    else
//        echo'day';
    echo'<span style = "color : blueviolet ;"> ';
    if($day%7==6) echo 'Monday';
    else if($day%7==0) echo 'Tuesday';
    else if($day%7==1) echo 'Wednesday';
    else if($day%7==2) echo 'Thursday';
    else if($day%7==3) echo 'Friday';
    else if($day%7==4) echo 'Saturday';
    else  echo 'Sunday';
    
     echo'<span class ="dropdown">
    <button type ="button" id="dropdownMenuButton" data-toggle="dropdown" 
    aria-haspopup="true" aria-expanded="false"> ▼ </button>';
    echo'<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" role="listbox">';
    for($var=1;$var<=31;$var++) {
        echo '<li><a class="dropdown-item" href="daily_chart2.php?day='.$var.'" >'.$var.'일</a></li>';
    }
    echo' </ul></span>';
    echo'</span></h2>';
    echo'<h5 style="text-align: center;">※일간 차트는 한 달간의 일별 charTrue 차트를 보여드립니다.
    다른 날의 차트를 보고 싶으면 선택해주세요.</h5>';
    
echo'
    <section>
    <article id = "container">
        <div id = "contentbody">
           <table class = "realchart" id = "chart">
                <thead >
                <tr>
                    <th scope="col" class="ranking"><strong>순위</strong></th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="title"><strong>곡 정보</strong></th>
                    <th scope="col" class="album"><strong>앨범</strong></th>
                    <th scope="col" class="like"><strong>좋아요</strong></th>
                    <th scope="col" width="50px"><strong>댓글수</strong></th>
                    <th scope="col"><strong>점수</strong></th>
                    <th scope="col" class="listen"><strong>듣기</strong></th>
                    
                </tr>
                </thead>
                <tbody>';

$conn = mysqli_connect('localhost','root','Song123~','gradproj');
if (!$conn) die(mysqli_get_warnings());
mysqli_query($conn, "set session character_set_connection=utf8;");
mysqli_query($conn,"set session character_set_results=utf8;");
mysqli_query($conn,"set session character_set_client=utf8;");

//$myID = findID();
//echo("<script language=javascript> aa(\"$myID\"); </script>");

//4월달 기준 디비    
$day=$day+1;    
if($day<10)    
    $ranking_db ='ranking_yesterday_20040'.$day;
else
    $ranking_db ='ranking_yesterday_2004'.$day;
    
$query = 'select * from '.$ranking_db.' order by rank asc';
$result = mysqli_query($conn,$query);

$i=0;
while($row = mysqli_fetch_array($result)) {

        echo '<tr>';
        echo '<td ><strong>' . $row['rank'] . '</strong></td>';
        echo '<td ><img src =' . $row['img_url'] . ' width = "50" height = "50"></td>';
        echo'<td><div class="artist_link" title ="해당 곡의 페이지로 넘어갑니다." >
        <a href="title.php?page=hotartist&title='.$row['title'].'"style=" color= black; text-decoration : none;">
        <strong>'.$row['title'].'</strong></a></div><br>';
        echo'<div class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
        <a href="artist.php?page=hotartist&artist='.$row['artist'].'"style=" color= black; text-decoration : none;">
        '.$row['artist'].'</a></div></td>';
        echo'<td class="artist_link" title="해당 앨범의 페이지로 넘어갑니다." >
        <a href="album.php?&album='.$row['album_title'].'"style=" color= black; text-decoration : none;">
        <strong>'.$row['album_title'].'</strong></td>';
        echo '<td ><img src="heart2.jpg" width="10" height="10"><strong>' . $row['like_sum'] . '</strong></td>';
        echo '<td ><strong>' . $row['comments_sum'] . '</strong></td>';
        echo '<td ><strong>' . $row['score_sum'] . '</strong></td>';
        echo'<td >';
        if(strcmp($row['song_url_melon'],"0"))
            echo'<a href ='.$row['song_url_melon'].'><img src = "melonb.jpg" width = "30px" height="30px"></a>';
        if(strcmp($row['song_url_genie'],"0"))
            echo'<a href ='.$row['song_url_genie'].'><img src = "genieb.jpg" width = "30px" height="30px"></a>';
        if(strcmp($row['song_url_bugs'],"0"))
            echo'<a href ='.$row['song_url_bugs'].'><img src = "bugsb.jpg" width = "30px" height="30px"></a></td>';
        echo '</tr>';
        $i += 1;

}
mysqli_close($conn);
?>
</tbody>
</table>
</div>
</article>
</section>
<!--
<div id="right">
    <iframe name="box" width ="400" height = "780" ></iframe>
</div>
-->
<br>

</body>
</html>
