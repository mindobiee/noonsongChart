<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <meta name="viewpoint" content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>hotartist</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link rel ="stylesheet" href="style1.css" type ="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <style>
        .controller{
            display: inline-block;
        }
        
        #logo2{
            display: inline;
        }
        #searcher{
            position : static;
            float: left;
            margin: 15px 0 0 11px;
            display : inline;
        }
        .tb{
            position: relative;
            width: 940px;
            margin: 0 auto; /*left, right auto*/
            font-family: 'Noto Sans KR', sans-serif;
        }
        /* Style the tb links */
        .tb a {
            color: #27282d;
            font-size: 18px;
        }

        /* Change color on hover */
        .tb a:hover {
            background-color: white;
            color: #0c2d83;
            font-weight: bold;
        }
        table#chart{
            width : 70%;
            border-radius: 1px;
            border : 5px solid white;
            margin : 0 auto;

        }
        table#chart th{
            background-color: lightblue;
        }
    </style>
</head>
<body>
<header>
     <a href =info.html> <h4 style= "text-align: right"> 차트루란? </h4></a>
    <div class="controller">
        <h1 id ="logo2" style="float : left; margin-left:50px;" >
            <a href = "http://www.chartrue.site/main.php">
            <img src="logo2.jpg" class="logo" width = "150" height="62"></a>
        </h1>
        <div id ="searcher">
        <form action="search_server.php">
            <span class= "window" method="get">
                <input type="text" class="input_text" name="search_word" placeholder="차트루 랭킹이 궁금한 노래를 검색해주세요" autofocus>
                <button type="submit" class='sch_smit'><img src = "dod.png" height="33px" width="33px" ></button>
            </span>
        </form>
        </div>
    </div>
    <div class="tb">
        <a href="main.php" >실시간</a>
        <a href="hottest_chart.php" >급상승</a>
        <a href="daily_chart.php" >일간</a>
        <a href="melon_chart.php" target="box">멜론</a>
        <a href="bugs_chart.php" target="box">벅스</a>
        <a href="genie_chart.php" target="box">지니</a>
        <a href="hotartist.php" style = "color : lightblue ;"><strong>핫아티스트</strong></a>
    </div>
 <hr noshade>
</header>
<?php
echo '<h2 class="clock" style="color : black">'.date("Y.m.d", time()).'
    <span style = "color : lightblue ;">'.date("H:00", time()).'
    </span></h2>';
echo'<h5 style="text-align: center;">※ 핫 아티스트는 현재 순위 안에 곡이 많은 순, 팬이 많은 순으로 보여드립니다.</h5>';
echo'
    <section>
    <article id = "container">
        <div id = "contentbody">
            <table class = "realchart" id = "chart">
                <thead >
                <tr>
                    <th scope="col" class="ranking"><strong>순위</strong></th>
                    <th scope="col" class="artist"><strong>아티스트명</strong></th>
                    <th scope="col" class="title_num"><strong>곡 수</strong></th>
                    <th scope="col" class="hidden"><strong>fan</strong></th>
                    <th scope="col" class="like"><strong>좋아요</strong></th>
                    <th scope="col" class="hidden"><strong>댓글수</strong></th>                 
                </tr>
                </thead>
                <tbody>';

$conn = mysqli_connect('localhost','root','Song123~','gradproj');
if (!$conn) die(mysqli_get_warnings());
mysqli_query($conn, "set session character_set_connection=utf8;");
mysqli_query($conn,"set session character_set_results=utf8;");
mysqli_query($conn,"set session character_set_client=utf8;");

$ranking_db ='ranking_now';
$query = 'select artist, count(artist) as art, sum(like_sum),sum(comments_sum) from '.$ranking_db.' group by artist order by art desc';
$result = mysqli_query($conn,$query);

$i=1;
//$temp_art=0;
    
while($row = mysqli_fetch_array($result)){
    
    if($row['art']==1){
        break;     
    }
    $query1 = 'select artist_like from '.$ranking_db.' where artist = "'.$row['artist'].'"';
    $result1 = mysqli_query($conn,$query1);
    $row1 = mysqli_fetch_array($result1);
        
    echo'<tr>';
    echo'<td ><strong>'.$i.'</strong></td>';
    echo'<td class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=hotartist&artist='.$row['artist'].'" style=" color= black; text-decoration : none;">'.$row['artist'].'</a></td>';
    echo'<td>'.$row['art'].'</td>';
    echo'<td>'.$row1['artist_like'].'</td>';
    echo'<td><img src="heart2.jpg" width="10" height="10"><strong>'.$row['sum(like_sum)'].'</strong></td>';
    echo'<td><strong>'.$row['sum(comments_sum)'].'</strong></td>';
    echo '</tr>';
    
    $i+=1;
    //temp_art =$row['art'];
}
       
//$query2 = 'select * from ranking_now order by artist_like desc';
//$result2 = mysqli_query($conn,$query2);
//  
$ranking_db ='ranking_now';
$query = 'select artist, count(artist) as art, sum(like_sum) as sum,sum(comments_sum) from '.$ranking_db.' group by artist 
having art =1 order by sum desc';
$result = mysqli_query($conn,$query);
    
while($row = mysqli_fetch_array($result)){ 
        
//        $query3 = 'select count(artist) as art, sum(like_sum),sum(comments_sum) from ranking_now where artist = "'.$row2['artist'].'"';
//        $result3 = mysqli_query($conn,$query3);
//        $row3 = mysqli_fetch_array($result3);
//        
//        while($row3['art']!=1){ 
//            $row2 = mysqli_fetch_array($result2);  
//            $query3 = 'select count(artist) as art, sum(like_sum),sum(comments_sum) from ranking_now where artist = "'.$row2['artist'].'"';
//            $result3 = mysqli_query($conn,$query3);
//            $row3 = mysqli_fetch_array($result3);
//        }
        $query1 = 'select artist_like from '.$ranking_db.' where artist = "'.$row['artist'].'"';
        $result1 = mysqli_query($conn,$query1);
        $row1 = mysqli_fetch_array($result1);
         echo'<tr>';
    echo'<td ><strong>'.$i.'</strong></td>';
    echo'<td class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=hotartist&artist='.$row['artist'].'" style=" color= black; text-decoration : none;">'.$row['artist'].'</a></td>';
    echo'<td>'.$row['art'].'</td>';
    echo'<td>'.$row1['artist_like'].'</td>';
    echo'<td><img src="heart2.jpg" width="10" height="10"><strong>'.$row['sum(like_sum)'].'</strong></td>';
    echo'<td><strong>'.$row['sum(comments_sum)'].'</strong></td>';
    echo '</tr>';
    
        
//        echo'<tr>';
//        echo'<td ><strong>'.$i.'</strong></td>';
//        echo'<td class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
//        <a href="artist.php?page=hotartist&artist='.$row2['artist'].'" style=" color= black; text-decoration : none;">'.$row2['artist'].'</a></td>';
//        echo'<td>'.$row3['art'].'</td>';
//        echo'<td>'.$row2['artist_like'].'</td>';
//        echo'<td><img src="heart2.jpg" width="10" height="10"><strong>'.$row3['sum(like_sum)'].'</strong></td>';
//        echo'<td><strong>'.$row3['sum(comments_sum)'].'</strong></td>';
//        echo '</tr>';
//        $i+=1;
    }
    


mysqli_close($conn);
?>
</tbody>
</table>
</div>
</article>
</section>
<br>
</body>
</html>