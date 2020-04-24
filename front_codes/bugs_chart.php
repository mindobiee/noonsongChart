<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <title>bugs_chart</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <link rel ="stylesheet" media="(max-width:320px)" href="mobile.css" type ="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src= "includeHTML.js"></script>
    <script language="JavaScript">
    </script>
    <style>
        .bugs_chart{
            color: #fb4233;
            font-weight:bold;
        }   
        
        table#chart th{
            background-color: #fb4233;
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
echo '<h2 class="clock" style="color : black">'.date("Y.m.d", time()).'
    <span style = "color: #fb4233">'.date("H:00", time()).'
    </span></h2>';
echo'<h5 style="text-align: center">※ 순위 비교는 기존 사이트 대비 chartrue의 순위와의 차이를 보여줍니다.</h5>';

echo'
    <section>
    <article id = "container">
        <div id = "contentbody">
            <div> 
            </div>
            <table class = "realchart" id = "chart">
                <thead>
                <tr>
                    <th scope="col" class="ranking"><strong>순위</strong></th>
                    <th scope="col" class="rank_change">비교</th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="comment_move"></th>
                    <th scope="col" class="title"><strong>곡 정보</strong></th>
                    <th scope="col" class="album"><strong>앨범</strong></th>
                    <th scope="col" class="hidden"><strong>좋아요</strong></th>
                    <th scope="col" class="hidden"><strong>댓글수</strong></th>
                    <th scope="col" ><strong>듣기</strong></th>
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

$query = 'select * from musicList_bugs order by ranking asc';
$result = mysqli_query($conn,$query);

$i=0;
while($row = mysqli_fetch_array($result)){
    
    $query_now = 'select * from ranking_now where id = "'.$row['id'].'"';
    $result_now = mysqli_query($conn,$query_now);
    if($rown = mysqli_fetch_array($result_now))    
        $cha =$row['ranking']-$rown['rank'];
    else
        $cha = 101;
    if($cha>20)
        echo'<tr style="background-color : rgba(249,191,253,0.3);">';    
    elseif($cha<-20)
        echo'<tr style="background-color : aliceblue;">';
    else 
        echo'<tr>';
    echo'<td class="comment_link" style="color:#fb4233"><strong>'.$row['ranking'].'</strong></td>';
    if($cha>0 && $cha <101)
        echo'<td ><h4 style="color : red"><strong>+'.$cha.'</strong></h4></td>';
    elseif($cha<0)
        echo'<td ><h4 style="color : blue"><strong>'.$cha.'</strong></h4></td>';
    elseif($cha==0)
        echo'<td ><img src="https://img.icons8.com/officexs/16/000000/horizontal-line.png" width="20" height="10"></td>';
    else
        echo'<td><h4 style="color:yellow"><strong>new</strong></h4></td>';
    
    echo'<td ><img src ='.$row['img_url'].' width = "50" height = "50"></td>';
     echo'<td title="해당 곡의 댓글 페이지로 넘어갑니다.">
    <a  href="comment.php?page=bugs&title='.$row['title'].'">
    <img src="comment.png" width="20px" height="20px"></a></td>';
    echo'<td>  <div class="artist_link" title ="해당 곡의 페이지로 넘어갑니다." >
    <a href="title.php?page=bugs&title='.$row['title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row['title'].'</strong></a></div><br>';
    echo'<div class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=bugs&artist='.$row['artist'].'"style=" color= black; text-decoration : none;">
    '.$row['artist'].'</a></div></td>';
    echo'<td class="artist_link" title="해당 앨범의 페이지로 넘어갑니다." >
    <a href="album.php?&album='.$row['album_title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row['album_title'].'</strong></td>';
    echo'<td title="'.$row['like_cnt'].' 증가"><img src="heart2.jpg" width="10" height="10">
    <strong>'.$row['like_sum'].'</strong></td>';
    echo'<td title="'.$row['comments_cnt'].' 증가"><strong>'.$row['comments_sum'].'</strong></td>';
    echo'<td title="해당 음원사이트의 곡페이지로 넘어갑니다."><a href ='.$row['song_url'].'><img src = "bugsb.jpg" width = "30px" height="30px"></a></td>';
    echo '</tr>';
    $i+=1;
   
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

