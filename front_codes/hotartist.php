<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <meta name="viewpoint" content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>hotartist</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src= "includeHTML.js"></script>
    <script src = "Java.js"></script>
    <style>

        #header{
        width : 100%;
        position: relative;
        z-index: 10001;
        }

        #section{
        position: relative;
        z-index: 1;
        }
        
        .hot_artist{
            color : lightblue ;
            font-weight:bold;
        }
        
        table#chart{
            width : 70%;
            border-radius: 1px;
            border : 5px solid white;
            margin : 0 auto;

        }
        table#chart th{
            background-color: lightsteelblue;
        }
        table#chart td{
            text-align: center;
        }
    </style>
</head>
<body>
<header include-html="header.html"></header>
<script>
        includeHTML();
</script>

<?php
echo'<h5 style="text-align: left;margin-left:200px">※ 핫 아티스트는 현재 순위 안에 곡이 많은 순으로 보여드립니다. 곡이 2개인 아티스트는 좋아요가 많은 순으로 보여드립니다.</h5>';
echo '<h2 class="clock" style="color : black">'.date("Y.m.d", time()).'
    <span style = "color : lightsteelblue ;">'.date("H:00", time()).'
    </span></h2>';

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
while($row = mysqli_fetch_array($result)){
    
    if($row['art']==2){
        break;     
    }
    $query1 = 'select artist_like from '.$ranking_db.' where artist = "'.$row['artist'].'"';
    $result1 = mysqli_query($conn,$query1);
    $row1 = mysqli_fetch_array($result1);
        
    echo'<tr>';
    echo'<td ><strong>'.$i.'</strong></td>';
    echo'<td class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=hotartist&artist='.$row['artist'].'" style=" color= black; text-decoration :   none;"><strong>'.$row['artist'].'</strong></a></td>';
    echo'<td><strong>'.$row['art'].'</strong></td>';
    echo'<td><strong>'.$row1['artist_like'].'</strong></td>';
    echo'<td><img src="heart2.jpg" width="10" height="10"><strong>'.$row['sum(like_sum)'].'</strong></td>';
    echo'<td><strong>'.$row['sum(comments_sum)'].'</strong></td>';
    echo '</tr>';
    
    $i+=1;

}

$ranking_db ='ranking_now';
$query = 'select artist, count(artist) as art, sum(like_sum) as sum, sum(comments_sum) from '.$ranking_db.' group by artist having art =2 order by sum desc';
$result = mysqli_query($conn,$query);
    
while($row = mysqli_fetch_array($result)){ 
    
        $query1 = 'select artist_like from '.$ranking_db.' where artist = "'.$row['artist'].'"';
        $result1 = mysqli_query($conn,$query1);
        $row1 = mysqli_fetch_array($result1);
    
        echo'<tr>';
        echo'<td ><strong>'.$i.'</strong></td>';
        echo'<td class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
        <a href="artist.php?page=hotartist&artist='.$row['artist'].'" style=" color= black; text-decoration :   none;"><strong>'.$row['artist'].'</strong></a></td>';
        echo'<td><strong>'.$row['art'].'</strong></td>';
        echo'<td><strong>'.$row1['artist_like'].'</strong></td>';
        echo'<td><img src="heart2.jpg" width="10" height="10"><strong>'.$row['sum'].'</strong></td>';
        echo'<td><strong>'.$row['sum(comments_sum)'].'</strong></td>';
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
<br>
</body>
</html>