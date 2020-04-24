<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <title>charTrue >album page</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <link rel ="stylesheet" media="(max-width:320px)" href="mobile.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src= "includeHTML.js"></script>
    <style>
        
        table#melon th{
            background-color : #01cc3b;
        }
        table#genie th{
            background-color : #11a6e3;
        }
        table#bugs th{
            background-color : #fb4233;
        }
        table.chart{
        width : 85%;
        margin: 0 auto;
        border-radius: 1px;
        border : 5px solid white;
        }
        table.chart th{
        background-color: #0c2d83;
        height : 8px;
        text-align: center;
        color : white;
        }
        table.chart th, td{
        font-family: 'Asap','Nanum Gothic',sans-serif ;
        padding : 15px;
        border-bottom : 1px solid #ddd;
        height: 8px;
        font-size: 10pt;
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
if (!$conn) die(mysqli_get_warnings());
mysqli_query($conn, "set session character_set_connection=utf8;");
mysqli_query($conn,"set session character_set_results=utf8;");
mysqli_query($conn,"set session character_set_client=utf8;");


$album=$_GET['album'];
$sql3 = 'SELECT * FROM ranking_now where album_title = "'.$album.'"';
$result3 = mysqli_query($conn,$sql3);

$CNT=0;
echo'<h2 style="text-align: left; margin-left: 100px;"> 앨범 페이지 </h2>';
echo'<h3 style="text-align: left; margin-left: 100px;">['.$album.']</h3>';
echo'<h3 style=" color : #0c2d83 ;text-align: left; margin-left: 100px;">[chatrue]</h3>';
echo'<h5 style=" color : black ;text-align: left; margin-left: 100px;">※ 앨범 페이지에서는 선택하신 앨범의 해당 곡들을 보여드립니다.</h5>';
    
echo'<table id = chart>';
echo'<tr>';
echo'
                    <th scope="col" class="ranking"><span>순위</span></th>
                    <th scope="col" class="ranking"><span>멜론</span></th>
                    <th scope="col" class="ranking"><span>지니</span></th>
                    <th scope="col" class="ranking"><span>벅스</span></th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="title"><span>곡 정보</span></th>
                    <th scope="col" class="like"><span>좋아요</span></th>
                    <th scope="col" class="hidden"><span>댓글 수</span></th>
                    <th scope="col" class="score_sum"><span>점수</span></th>
                    <th scope="col" class="like"><span>감정분석</span></th>
';
echo'</tr>';

$rank_temp=0;
while( $row = mysqli_fetch_array($result3)){ // title
    
     while($row['rank']==$rank_temp)//순위가 같은 랭킹이 나오면 다음으로 건너뛰기!
        {
            $rank_temp = $row['rank'];
            $row = mysqli_fetch_array($result); 
        }
        if($row==null) break;
 $sql_p = 'SELECT * FROM preRanking where id = "'.$row['id'].'"';
 $result_p = mysqli_query($conn,$sql_p);
 $row_p = mysqli_fetch_array($result_p);

$sql_m = 'SELECT * FROM musicList_melon where id = "'.$row['id'].'"';
$result_m = mysqli_query($conn,$sql_m);
$row_m = mysqli_fetch_array($result_m);

$sql_g = 'SELECT * FROM musicList_genie where id = "'.$row['id'].'"';
$result_g = mysqli_query($conn,$sql_g);
$row_g = mysqli_fetch_array($result_g);

$sql_b = 'SELECT * FROM musicList_bugs where id = "'.$row['id'].'"';
$result_b = mysqli_query($conn,$sql_b);
$row_b = mysqli_fetch_array($result_b);

    echo'<tr>';
    echo'<td align="center"><strong>'.$row['rank'].'</strong></td>';
    echo'<td>'.$row_m['ranking'].'</td>';
    echo'<td>'.$row_g['ranking'].'</td>';
    echo'<td>'.$row_b['ranking'].'</td>';
    echo'<td align="center"><img src ='.$row['img_url'].' width = "50" height = "50"></td>';;
    echo'<td>
    <div class="artist_link" title ="해당 곡의 페이지로 넘어갑니다." >
    <a href="title.php?page=hotartist&title='.$row['title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row['title'].'</strong></a></div><br>';
    echo'<div class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=hotartist&artist='.$row['artist'].'"style=" color= black; text-decoration : none;">
    '.$row['artist'].'</a></div></td>';
    echo'<td ><img src="heart2.jpg" width="10" height="10"><strong>'.$row['like_sum'].'</strong></td>';
    echo'<td ><strong>'.$row['comments_sum'].'</strong></td>';
    echo'<td ><strong>'.$row['score_sum'].'</strong></td>';
    echo'<td >'.round($row_p['sen_sum_score'],2).'</td>';
    echo '</tr>';
    $CNT+=1;
    $rank_temp=$row['rank'];

}

echo'</table>';
    

$sql3 = 'SELECT * FROM musicList_melon where album_title = "'.$album.'"';
$result3 = mysqli_query($conn,$sql3);
if($row = mysqli_fetch_array($result3)){
echo'<br>';
echo'<h3 style=" color : #01cc3b ;text-align: left; margin-left: 100px;">[멜론]</h3>';    
echo'<table id = "melon" class="chart" >';
echo'<tr>';
echo'
                    <th scope="col" class="ranking"><span>순위</span></th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="title"><span>곡 정보</span></th>
                    <th scope="col" class="album"><span>앨범</span></th>
                    <th scope="col" class="like"><span>좋아요</span></th>
                    <th scope="col" class="hidden"><span>댓글 수</span></th>
                    <th scope="col" class="hidden"><span>듣기</span></th>
';
echo'</tr>';

while($row){
    echo'<tr>';
    echo'<td align="center"><strong>'.$row['ranking'].'</strong></td>';
    echo'<td align="center"><img src ='.$row['img_url'].' width = "50" height = "50"></td>';
//    echo'<td ><div><strong>'.$row['title'].'</strong></div><br>';
//    echo'<div>'.$row['artist'].'</div></td>';
    echo'<td>
    <div class="artist_link" title ="해당 곡의 페이지로 넘어갑니다." >
    <a href="title.php?page=melon&title='.$row['title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row['title'].'</strong></a></div><br>';
    echo'<div class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=melon&artist='.$row['artist'].'"style=" color= black; text-decoration : none;">
    '.$row['artist'].'</a></div></td>';
    echo'<td ><strong>'.$row['album_title'].'</strong></td>';
    echo'<td ><img src="heart2.jpg" width="10" height="10"><strong>'.$row['like_sum'].'</strong></td>';
    echo'<td ><strong>'.$row['comments_sum'].'</strong></td>';
//    echo'<td ><strong>'.$row['artist_like'].'</strong></td>';
    echo'<td ><a href ='.$row['song_url'].'><img src = "melonb.jpg" width = "30px" height="30px"></a>
        </td>';
    echo '</tr>';
    $CNT+=1;
    $row = mysqli_fetch_array($result3);
}
echo'</table>';
}

$sql3 = 'SELECT * FROM musicList_genie where album_title = "'.$album.'"';
$result3 = mysqli_query($conn,$sql3);
if($row = mysqli_fetch_array($result3)){
echo'<br>';
echo'<h3 style=" color : #11a6e3 ;text-align: left; margin-left: 100px;">[지니]</h3>';    
    
echo'<table id = "genie" class="chart" >';
echo'<tr style="background-color : #11a6e3">';
echo'
                    <th scope="col" class="ranking"><span>순위</span></th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="title"><span>곡 정보</span></th>
                    <th scope="col" class="album"><span>앨범</span></th>
                    <th scope="col" class="like"><span>좋아요</span></th>
                    <th scope="col" class="hidden"><span>댓글 수</span></th>
                    
                    <th scope="col" class="hidden"><span>듣기</span></th>
';
echo'</tr>';

while($row){
    echo'<tr>';
    echo'<td align="center"><strong>'.$row['ranking'].'</strong></td>';
    echo'<td align="center"><img src ='.$row['img_url'].' width = "50" height = "50"></td>';
//    echo'<td ><div><strong>'.$row['title'].'</strong></div><br>';
//    echo'<div>'.$row['artist'].'</div></td>';
    echo'<td>
    <div class="artist_link" title ="해당 곡의 페이지로 넘어갑니다." >
    <a href="title.php?page=genie&title='.$row['title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row['title'].'</strong></a></div><br>';
    echo'<div class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=genie&artist='.$row['artist'].'"style=" color= black; text-decoration : none;">
    '.$row['artist'].'</a></div></td>';
    
    echo'<td ><strong>'.$row['album_title'].'</strong></td>';
    echo'<td ><img src="heart2.jpg" width="10" height="10"><strong>'.$row['like_sum'].'</strong></td>';
    echo'<td ><strong>'.$row['comments_sum'].'</strong></td>';
//    echo'<td ><strong>'.$row['artist_like'].'</strong></td>';
    echo'<td ><a href ='.$row['song_url'].'><img src = "genieb.jpg" width = "30px" height="30px"></a>
        </td>';
    echo '</tr>';
    $CNT+=1;
    $row = mysqli_fetch_array($result3);
}

echo'</table>';
}

$sql3 = 'SELECT * FROM musicList_bugs where album_title = "'.$album.'"';
$result3 = mysqli_query($conn,$sql3);
if($row = mysqli_fetch_array($result3)){    
echo'<br>';    
echo'<h3 style=" color : #fb4233 ;text-align: left; margin-left: 100px;">[벅스]</h3>';    
    
echo'<table id = "bugs" class="chart">';
echo'<tr style="background-color : #fb4233">';
echo'
                    <th scope="col" class="ranking"><span>순위</span></th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="title"><span>곡 정보</span></th>
                    <th scope="col" class="album"><span>앨범</span></th>
                    <th scope="col" class="like"><span>좋아요</span></th>
                    <th scope="col" class="hidden"><span>댓글 수</span></th>       
                    <th scope="col" class="hidden"><span>듣기</span></th>
';
echo'</tr>';
while( $row ){
    echo'<tr>';
    echo'<td align="center"><strong>'.$row['ranking'].'</strong></td>';
    echo'<td align="center"><img src ='.$row['img_url'].' width = "50" height = "50"></td>';
//    echo'<td ><div><strong>'.$row['title'].'</strong></div><br>';
//    echo'<div>'.$row['artist'].'</div></td>';
    echo'<td>
    <div class="artist_link" title ="해당 곡의 페이지로 넘어갑니다." >
    <a href="title.php?page=bugs&title='.$row['title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row['title'].'</strong></a></div><br>';
    echo'<div class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=bugs&artist='.$row['artist'].'"style=" color= black; text-decoration : none;">
    '.$row['artist'].'</a></div></td>';
    
    echo'<td ><strong>'.$row['album_title'].'</strong></td>';
    echo'<td ><img src="heart2.jpg" width="10" height="10"><strong>'.$row['like_sum'].'</strong></td>';
    echo'<td ><strong>'.$row['comments_sum'].'</strong></td>';
//    echo'<td ><strong>'.$row['artist_like'].'</strong></td>';
    echo'<td ><a href ='.$row['song_url'].'><img src = "bugsb.jpg" width = "30px" height="30px"></a>
        </td>';
    echo '</tr>';
    $CNT+=1;
    $row = mysqli_fetch_array($result3);

}
echo'</table>';
}
?>
</body>
</html>
