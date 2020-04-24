<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <meta name="viewpoint" content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>sentimental chart</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <link rel ="stylesheet" media="(max-width:320px)" href="mobile.css" type ="text/css">
    <script src ="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src ="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src = "chartuse.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>   
    <script language="JavaScript">
    </script>
    <script src="includeHTML.js"></script>
    <style>

        .link a:link{
             color : white;
             text-decoration: none;
         }
        .link a:visited{
            color : white;
            text-decoration: none;
        }
        .link a:hover{
            color: white;
            text-decoration: none;
        }
        .senti_chart{
            font-weight:bold;
            color : #0c2d83;
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

//$myID = findID();
//echo("<script language=javascript> aa(\"$myID\"); </script>");
$sql = "SELECT title FROM preRanking order by sen_sum_score desc limit 5";
$result = mysqli_query($conn,$sql);
$idList=[];
while($row = mysqli_fetch_array($result))
    $idList[] = $row['title'];

$time_array=[];
$data=[];
//echo'<h3 style="text-align: left; margin-left: 100px;">[ 랭킹 변화 추이 ]</h3>';
for( $k=0; $k <5 ; $k++) {
    $data[$k] = [];
    $temp=0;
    //echo '<div style="margin-left: 100px; color : #737373 ">';
    //echo '<h4>' . $idList[$k] . '</h4>';
    //$data= [];
    $time = ltrim(date('Ymd', strtotime('-1 day', time())), '2020');
    $present_time = (int)date("H");
    if ($present_time != 23) {
        for ($i = $present_time + 1; $i < 24; $i += 1) {
            if ($i < 2 || $i > 6) {
                if ($i < 10)
                    $sqlQuery = 'select * from ranking_200' . $time . '_0' . $i . ' where title = "' . $idList[$k] . '"';
                else
                    $sqlQuery = 'select * from ranking_200' . $time . '_' . $i . ' where title = "' . $idList[$k] . '"';
                if ($k == 0)
                    array_push($time_array, $i);

                try {
                    if (!$result = mysqli_query($conn, $sqlQuery))
                        ;//echo mysqli_error($conn);
                    if ($row = mysqli_fetch_array($result)) { //if($row['rank']!=null)
                        array_push($data[$k], $row['rank']);
                    } else { //fetch할 게 없을 때
                        if($data[$k][$temp-1]) //이전 index의 값이 있으면 그 값을 넣어줌.
                            array_push($data[$k], $data[$k][$temp-1]);
                        else
                            array_push($data[$k], 100);
                    }
                    //echo '<p>' . $sqlQuery . '</p>';
                    //echo '<span>[' . $i . ']' . $data[$k][$i] . ' - </span>';

                } catch (Exception $exception) {
                    $s = $exception->getMessage();
                    //echo $s;
                }
                $temp+=1;
            }
        }
    }
    $time = ltrim(date('Ymd'), '2020');
    for ($i = 0; $i <= $present_time; $i += 1) {
        if ($i < 2 || $i > 6) {
            if ($i < 10)
                $sqlQuery = 'select * from ranking_200' . $time . '_0' . $i . ' where title = "' . $idList[$k] . '"';
            else
                $sqlQuery = 'select * from ranking_200' . $time . '_' . $i . ' where title = "' . $idList[$k] . '"';
            if ($k == 0)
                array_push($time_array, $i);
            try {
                if (!$result = mysqli_query($conn, $sqlQuery))
                    ;//echo mysqli_error($conn);
                if ($row = mysqli_fetch_array($result)) {
                    array_push($data[$k], $row['rank']);
                } else {
                    if($data[$k][$temp-1])
                        array_push($data[$k], $data[$k][$temp-1]);
                    else
                        array_push($data[$k], 100);
                }
                //echo '<span>[' . $i . ']' . $data[$k][$i] . ' - </span>';
            } catch (Exception $exception) {
                $s = $exception->getMessage();
                //echo $s;
            }
            $temp+=1;
        }
        //echo '</div>';
    }
}
for($i=0;$i<5;$i++){
    switch ($i){
        case 0:
            $array1 = $data[0];
            break;
        case 1:
            $array2 = $data[1];
            break;
        case 2:
            $array3 = $data[2];
            break;
        case 3:
            $array4 = $data[3];
            break;
        case 4:
            $array5 = $data[4];
            break;
        default:
            break;
    }
}
echo'<h5 style="text-align: center;">※ 감정분석 페이지에서는 각 곡 별 댓글의 감정분석의 총 환산 점수 순서대로 보여드립니다.</h5>';
echo '<h2 class="clock" style="color : black">'.date("Y.m.d", time()).'
    <span style = "color : #0c2d83 ;">'.date("H:00", time()).'</span></h2>';

echo'
<h1>
    <div id="chart-container" style="width: 600px; margin: 0 auto;">
        <canvas id="myChart"></canvas>
    </div>
</h1>';
echo'<h5 style="text-align: center; color:black">☞ 다음은 charTrue의 감정분석 top 5의 실시간 순위의 변화 추이를 보여드립니다.</h5>';
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
                    <th scope="col" class="title"><strong></strong></th>
                    <th scope="col" class="title"><strong>곡 정보</strong></th>
                    <th scope="col" class="album"><strong>앨범</strong></th>
                    <th scope="col" class="like" ><strong>좋아요</strong></th>
                    <th scope="col" class="hidden"><strong>댓글 수</strong></th>
                    <th scope="col" class="like"><strong>감정분석</strong></th>
                    <th scope="col" class="hidden"><strong>총 점수</strong></th>
                </tr>
                </thead>
                <tbody>';

    
$ranking_db ='preRanking';
$query = 'select * from '.$ranking_db.' order by sen_sum_score desc';
$result = mysqli_query($conn,$query);

$i=1;
$rank_temp=0; //임시 랭킹 생성(bigint)

while($row = mysqli_fetch_array($result)){
    
    $query_now = 'select * from ranking_now where id = "'.$row['id'].'"';
    $result_now = mysqli_query($conn,$query_now);
    $row_now = mysqli_fetch_array($result_now);

    
    $query_bugs = 'select * from musicList_bugs where id = "'.$row['id'].'"';
    $result_bugs = mysqli_query($conn,$query_bugs);
    $rowb = mysqli_fetch_array($result_bugs);

    $query_melon = 'select * from musicList_melon where id = "'.$row['id'].'"';
    $result_melon = mysqli_query($conn,$query_melon);
    $rowm = mysqli_fetch_array($result_melon);

    $query_genie = 'select * from musicList_genie where id = "'.$row['id'].'"';
    $result_genie = mysqli_query($conn,$query_genie);
    $rowg = mysqli_fetch_array($result_genie);

    $title_rank='melon:'.$rowm['ranking'].'위, genie:'.$rowg['ranking'].'위, bugs:'.$rowb['ranking'].'위';
    $title= 'melon:'.$rowm['like_sum'].', genie:'.$rowg['like_sum'].', bugs:'.$rowb['like_sum'];
    $title2= 'melon:'.$rowm['comments_sum'].', genie:'.$rowg['comments_sum'].', bugs:'.$rowb['comments_sum'];
    
//    if($i==1)
//        echo'<tr style="background-color : rgba(255, 99, 132, 0.2)">';
//    else if($i==2)
//        echo'<tr style="background-color : rgba(54, 162, 235, 0.2)">';
//    else if($i==3)
//        echo'<tr style="background-color : rgba(255, 206, 86, 0.2)">';
//    else if($i==4)
//        echo'<tr style="background-color : rgba(153, 102, 255, 0.2)">';
//    else if($i==5)
//        echo'<tr style="background-color : rgba(255, 159, 64, 0.2)">';
//    else{
//        if($row_now['rank_change']>20)
//            echo'<tr style="background-color : rgba(249,191,253,0.3);">';    
//        else if($row_now['rank_change']<-20)
//            echo'<tr style="background-color : aliceblue;">';
//        else 
//            echo'<tr>';
//    }
//    echo'<td ><strong>'.$i.'</strong></td>';
    if($row_now['rank_change']>20)
        echo'<tr style="background-color : rgba(249,191,253,0.3);">';    
    elseif($row_now['rank_change']<-20)
        echo'<tr style="background-color : aliceblue;">';
    else 
        echo'<tr>';
    if($i==1)
        echo'<td style="color : rgba(255, 99, 132, 1)"><strong>'.$i.'</strong></td>';
    else if($i==2)
         echo'<td style="color : rgba(54, 162, 235, 1)"><strong>'.$i.'</strong></td>';
    else if($i==3)
        echo'<td style="color : rgba(255, 206, 86, 1)"><strong>'.$i.'</strong></td>';
    else if($i==4)
        echo'<td style="color : rgba(153, 102, 255, 1)"><strong>'.$i.'</strong></td>';
    else if($i==5)
        echo'<td style="color : rgba(255, 159, 64, 1)"><strong>'.$i.'</strong></td>';
    else
        echo'<td ><strong>'.$i.'</strong></td>';  
    
    $rank_temp = $row_now['rank'];
    if($row_now['rank_change']>0)
        echo'<td ><h4 style="color : orange"><img src ="up.jpg" width="10" height="10" ><strong>'.$row_now['rank_change'].'</strong></h4></td>';
    elseif($row_now['rank_change']<0)
        echo'<td ><h4 style="color : cornflowerblue"><img src ="down.jpg" width="10" height="10" ><strong>'.abs($row_now['rank_change']).'</strong></h4></td>';
    else
        echo'<td ><img src="https://img.icons8.com/officexs/16/000000/horizontal-line.png" width="20" height="10"></td>';
    echo'<td ><img src ='.$row['img_url'].' width = "50" height = "50"></td>';
    echo'<td title="해당 곡의 댓글 페이지로 넘어갑니다.">
    <a  href="comment2.php?id='.$row['id'].'&title='.$row['title'].'">
    <img src="comment.png" width="20px" height="20px"></a></td>';
    echo'<td title="'.$title_rank.'" >
    <div class="artist_link" title ="해당 곡의 페이지로 넘어갑니다." >
    <a href="title.php?page=hotartist&title='.$row['title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row['title'].'</strong></a></div><br>';
    echo'<div class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=hotartist&artist='.$row['artist'].'"style=" color= black; text-decoration : none;">
    '.$row['artist'].'</a></div></td>';
    echo'<td class="artist_link" title="해당 앨범의 페이지로 넘어갑니다." >
    <a href="album.php?&album='.$row['album_title'].'"style=" color= black; text-decoration : none;"><strong>
    '.$row['album_title'].'</strong></td>';
    echo'<td title ="'.$title.'"><img src="heart2.jpg" width="10" height="10"><strong>
    '.$row['like_sum'].'</strong></td>';
    echo'<td class="hidden"  title="'.$title2.'">'.$row['cmts_sum'].'</td>';
    echo'<td class="hidden"  ><strong>'.round($row['sen_sum_score'],2).'</strong></td>';
    echo'<td class="hidden"  ><strong>'.$row_now['score_sum'].'</strong></td>';
//    echo'<td class="ranking" ><strong>'.$row_now['rank'].'</strong></td>';
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


</body>
<script>
    var ctx = document.getElementById("myChart").getContext('2d');

    var time = [<?php echo '"'.implode('","',  $time_array ).'"' ?>]; // 활용 가능!!!
    var data0 = [<?php echo '"'.implode('","',  $array1 ).'"' ?>];
    var data1 = [<?php echo '"'.implode('","',  $array2 ).'"' ?>];
    var data2 = [<?php echo '"'.implode('","',  $array3 ).'"' ?>];
    var data3 = [<?php echo '"'.implode('","',  $array4 ).'"' ?>];
    var data4 = [<?php echo '"'.implode('","',  $array5 ).'"' ?>];
    var id = [<?php echo '"'.implode('","',  $idList ).'"' ?>];
    var list=[]; //일차원 배열 생성
    var k=0;

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: time,  //배열을 넣으면 분명히 작동은 된다!!!
            datasets: [{
                label: id[0],
                data: data0,
                borderColor:
                    'rgba(255,99,132,1)',
                backgroundColor:
                    'rgba(255, 99, 132, 0.2)',
                fill : false,
                borderWidth: 2
            },{
                label: id[1],
                data: data1,
                borderColor:
                    'rgba(54, 162, 235, 1)',
                backgroundColor:
                    'rgba(54, 162, 235, 0.2)',
                fill : false,
                borderWidth: 2
            },{
                label: id[2],
                data: data2,
                borderColor:
                    'rgba(255, 206, 86, 1)',
                backgroundColor:
                    'rgba(255, 206, 86, 0.2)',
                fill : false,
                borderWidth: 2
            },{
                label: id[3],
                data: data3,
                borderColor:
                     'rgba(153, 102, 255, 1)',
                backgroundColor:
                   'rgba(153, 102, 255, 0.2)',
                fill : false,
                borderWidth: 2
            },{
                label: id[4],
                data: data4,
                borderColor:
                     'rgba(255, 159, 64, 1)',
                backgroundColor:
                    'rgba(255, 159, 64, 0.2)',
                fill : false,
                borderWidth: 2
            }
                      ]

        },
        options: {
            maintainAspectRatio: true, // default value. false일 경우 포함된 div의 크기에 맞춰서 그려짐.
            scales: {
                yAxes: [{
                    display : false,
                    ticks: {
                        reverse : true,
                        //beginAtZero:true,
                        min : 0
                        //max : 50
                    },
                    gridLines:{
						color: 'rgba(255,255,255,1)',
						lineWidth: 1
					}
                }]
            }
        }
    });
</script>
</html>
