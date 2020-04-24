<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <title>hottest chart</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <link rel ="stylesheet" media="(max-width:320px)" href="mobile.css" type ="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src="includeHTML.js"></script>
    <script language="JavaScript">
    </script>
    <style>
        
        table#chart th{
            background-color: indianred;
            height : 10px;
            text-align: center;
            color : white;
            text-align: center;
            margin: 0;
        }
        .hottest_chart{
            color: indianred;
            font-weight:bold;
        }
        
        
    </style>
</head>
<body>
<header include-html="header.html"></header>
<script>
        includeHTML();
</script>
<?php
echo '<h2 class="clock" style=" color : black">'.date("Y.m.d", time()).'
    <span style = "color : indianred ;">'.date("H:00", time()).'</span></h2>';
//echo'<h5 style="text-align: right; margin-right: 50px;">※ 급상승 차트루 차트는 시간 대비 순위가 높게 상승한 차트순으로 보여드립니다.</h5>';
echo'
    <section>
    <article id = "container">
        <div id = "contentbody">
           <table class = "realchart" id = "chart">
                <thead >
                <tr>
                    <th scope="col" class="ranking"><strong>순위</strong></th>
                    <th scope="col" class="rank_change"></th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="title"><strong>곡 정보</strong></th>
                    <th scope="col" class="album"><strong>앨범</strong></th>
                    <th scope="col" class="like"><strong>좋아요</strong></th>
                    <th scope="col" width="50px"><strong>댓글수</strong></th>
                    <th scope="col"><strong>점수</strong></th>
                    
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

$ranking_db ='ranking_now';
$query = 'select * from '.$ranking_db.' order by rank_change desc';
$result = mysqli_query($conn,$query);

$i=0;
while($row = mysqli_fetch_array($result)) {
    if($row['rank_change']<10)
        break;
    if ($row['rank_change'] > 0) {

        $query_bugs = 'select * from musicList_bugs where id = "' . $row['id'] . '"';
        $result_bugs = mysqli_query($conn, $query_bugs);
        $rowb = mysqli_fetch_array($result_bugs);

        $query_melon = 'select * from musicList_melon where id = "' . $row['id'] . '"';
        $result_melon = mysqli_query($conn, $query_melon);
        $rowm = mysqli_fetch_array($result_melon);

        $query_genie = 'select * from musicList_genie where id = "' . $row['id'] . '"';
        $result_genie = mysqli_query($conn, $query_genie);
        $rowg = mysqli_fetch_array($result_genie);

        $title_rank = 'melon:' . $rowm['ranking'] . '위, genie:' . $rowg['ranking'] . '위, bugs:' . $rowb['ranking'] . '위';

        echo '<tr>';
        echo '<td ><strong>' . $row['rank'] . '</strong></td>';
        if ($row['rank_change'] > 0)
            echo '<td ><h4 style="color : orange"><img src ="up.jpg" width="10" height="10" ><strong>' . $row['rank_change'] . '</strong></h4></td>';
        elseif ($row['rank_change'] < 0)
            echo '<td ><h4 style="color : cornflowerblue"><img src ="down.jpg" width="10" height="10" ><strong>' . abs($row['rank_change']) . '</strong></h4></td>';
        else
            echo '<td ><img src="https://img.icons8.com/officexs/16/000000/horizontal-line.png" width="20" height="10"></td>';
        echo '<td ><img src =' . $row['img_url'] . ' width = "50" height = "50"></td>';
        echo'<td title="' . $title_rank . '" ><div><strong>'.$row['title'].'</strong></div><br>';
        echo'<div class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
        <a href="artist.php?page=hotartist&artist='.$row['artist'].'"style=" color= black; text-decoration : none;">
        '.$row['artist'].'</a></div></td>';
        echo '<td >' . $row['album_title'] . '</td>';
        echo '<td ><strong><img src="heart2.jpg" width="10" height="10">' . $row['like_sum'] . '</strong></td>';
        echo '<td ><strong>' . $row['comments_sum'] . '</strong></td>';
        echo '<td ><strong>' . $row['score_sum'] . '</strong></td>';
        echo '</tr>';
        $i += 1;
    }
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
