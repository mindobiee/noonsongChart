<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <title>chartrue</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link rel ="stylesheet" href="style.css" type ="text/css">
</head>
<body>
<header>
    <a href =info.html> <h4 style= "text-align: right"> 차트루란? </h4></a>
    <h1>
        <a href = "http://www.chartrue.site/main.php"><img src="logo2.jpg" class="logo" width = "280" height="125"></a>
    </h1>
    <div id ="searcher">
        <form action="search_server.php">
            <span class= "window" method="get">
                <input type="text" class="input_text" name="search_word" placeholder="차트루 랭킹이 궁금한 노래를 검색해주세요" autofocus>
                <button type="submit" class='sch_smit'><img src = "dod.png" height="33px" width="33px" ></button>
            </span>
        </form>
    </div><br><br>
    <hr noshade>

    <div id="side"><!-- <span id="gnbHandleBtn" class="btnMenuFolding open" style status ="open">MENU</span>-->
        <div id = "gnbBody" class="jspscrollable" tabindex ="0" tabindex ="0">
            <div class="links">
                <table id = "site">
                    <td><a href="https://www.melon.com/chart/index.htm" target="box">
                            <img src="melon1.jpg"  alt="멜론" width ="80" height="40"></a></td>
                    <td><a href="https://music.bugs.co.kr/chart" target="box">
                            <img src="bugs2.jpg" alt="벅스" width ="80" height="40"></a></td>
                    <td><a href="https://www.genie.co.kr/chart/top200" target="box">
                            <img src="genie1.jpg" alt="지니" width ="80" height="40"></a></td>
                </table>
            </div>
        </div>
    </div>
</header>
<?php
echo '<h1>'.date("Y.m.d H :00", time())."</h1><br/>";
echo'
    <section>
    <article id = "container">
        <div id = "contentbody">
            <table class = "realchart" id = "chart">
                <thead>
                <tr>
                    <th scope="col" class="ranking"><span>순위</span></th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="title"><span>곡 정보</span></th>
                    <th scope="col" class="album"><span>앨범</span></th>
                    <th scope="col" class="like"><span>총 좋아요</span></th>
                    <th scope="col" class="comment"><span>총 댓글 수</span></th>
                    <th scope="col" class="score_sum"><span>감정 지수</span></th>
                    <th scope="col" class="listen"><span>듣기</span></th>
                </tr>
                </thead>
                <tbody>';

$conn = mysqli_connect('localhost','root','Song123~','gradproj');
if (!$conn) die(mysqli_get_warnings());
mysqli_query($conn, "set session character_set_connection=utf8;");
mysqli_query($conn,"set session character_set_results=utf8;");
mysqli_query($conn,"set session character_set_client=utf8;");

$query = 'select * from ranking order by rank asc';
$result = mysqli_query($conn,$query);

while($row = mysqli_fetch_array($result)){
    echo'<tr>';
    echo'<td ><strong>'.$row['rank'].'</strong></td>';
    echo'<td ><img src ='.$row['img_url'].' width = "50" height = "50"></td>';
    echo'<td ><div><strong>'.$row['title'].'</strong></div><br>';
    echo'<div>'.$row['artist'].'</div></td>';
    echo'<td ><strong>'.$row['album_title'].'</strong></td>';
    echo'<td ><strong>'.$row['like_sum'].'</strong></td>';
    echo'<td ><strong>'.$row['comments_sum'].'</strong></td>';
    echo'<td ><strong>'.$row['score_sum'].'</strong></td>';
    echo'<td ><a href ='.$row['song_url_melon'].'><img src = "melonb.jpg" width = "30px" height="30px"></a>
                    <a href ='.$row['song_url_genie'].'><img src = "genieb.jpg" width = "30px" height="30px"></a>
                    <a href ='.$row['song_url_bugs'].'><img src = "bugsb.jpg" width = "30px" height="30px"></a></td>';
    echo '</tr>';

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
<footer>
</footer>

</body>
</html>
