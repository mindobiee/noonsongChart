<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <title>charTrue >compare page</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <link rel ="stylesheet" media="(max-width:320px)" href="mobile.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src= "includeHTML.js"></script>
    <style>
        .comparing{
            color: #0c2d83;
            font-weight:bold;
        }
        
        table#low th{
            background-color: darkblue;  
        }
        table#high th{
            background-color: darkred;
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


$sql = 'SELECT * FROM ranking_now ';
$result3 = mysqli_query($conn,$sql);

$CNT=0;
echo'<h5 style=" color : black ;text-align: left; margin-left: 100px;">※ 비교 차트는 기존 음원사이트의 차트와 chaTrue 차트와 20위 이상 차이가 나는 곡들을 보여드립니다.</h5>';
echo'<h3 style=" color : darkred ;text-align: left; margin-left: 100px;">[charTrue보다 높은 차트]</h3>';    
echo'<table class = "chart" id="high" >';
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

    $cha1=0;$cha2=0;$cha3=0;
    if($row_m['ranking']!=null)
        $cha1=$row['rank']-$row_m['ranking'];
    if($row_b['ranking']!=null)
        $cha2=$row['rank']-$row_b['ranking'];
    if($row_g['ranking']!=null)
        $cha3=$row['rank']-$row_g['ranking'];    
    $cha= max($cha1,$cha2,$cha3);
        
if($cha>20){
    
    echo'<tr>';
    echo'<td align="center"><strong>'.$row['rank'].'</strong></td>';
    //차트 비교에 따라 랭킹에 색깔 입히기 
    if($cha1>20) 
        echo'<td style="color:red"><strong>'.$row_m['ranking'].'</strong></td>';
    else 
        echo'<td><strong>'.$row_m['ranking'].'</strong></td>';
    if($cha3>20) 
        echo'<td style="color:red"><strong>'.$row_g['ranking'].'</strong></td>';
    else 
        echo'<td><strong>'.$row_g['ranking'].'</strong></td>';
     if($cha2>20) 
        echo'<td style="color:red"><strong>'.$row_b['ranking'].'</strong></td>';
    else 
        echo'<td><strong>'.$row_b['ranking'].'</strong></td>';
  
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
}

echo'</table>';
    
echo'<h3 style=" color : darkblue ;text-align: left; margin-left: 100px;">[charTrue보다 낮은 차트]</h3>';    
echo'<table class = "chart" id= "low">';
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
$sql = 'SELECT * FROM ranking_now ';
$result3 = mysqli_query($conn,$sql);
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



    $cha1=0;$cha2=0;$cha3=0;
    if($row_m['ranking']!=null)
        $cha1=$row['rank']-$row_m['ranking'];
    if($row_b['ranking']!=null)
        $cha2=$row['rank']-$row_b['ranking'];
    if($row_g['ranking']!=null)
        $cha3=$row['rank']-$row_g['ranking'];    
    $cha= min($cha1,$cha2,$cha3);
    
    if($cha<-20){
    
    echo'<tr>';
    echo'<td align="center"><strong>'.$row['rank'].'</strong></td>';
    if($cha1<-20) 
        echo'<td style="color:blue"><strong>'.$row_m['ranking'].'</strong></td>';
    else 
        echo'<td><strong>'.$row_m['ranking'].'</strong></td>';
    if($cha3<-20) 
        echo'<td style="color:blue"><strong>'.$row_g['ranking'].'</strong></td>';
    else 
        echo'<td><strong>'.$row_g['ranking'].'</strong></td>';
     if($cha2<-20) 
        echo'<td style="color:blue"><strong>'.$row_b['ranking'].'</strong></td>';
    else 
        echo'<td><strong>'.$row_b['ranking'].'</strong></td>';    
//    echo'<td>'.$row_m['ranking'].'</td>';
//    echo'<td>'.$row_g['ranking'].'</td>';
//    echo'<td>'.$row_b['ranking'].'</td>';
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
}

echo'</table>'; 

?>
</body>
</html>
