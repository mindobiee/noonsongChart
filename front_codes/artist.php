<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <title>charTrue >artist page</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <script type="text/javascript" src="chartuse.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src = "chartuse.js"></script>
    <script src= "includeHTML.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <style> 
        .hot_artist{
           color: #0c2d83;
           font-weight:bold;
        }
        
        .album{
            width : 200px;
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
$artist=$_GET['artist'];
    
$page =$_GET['page'];
    
    
$sql = "SELECT artist_like FROM ranking_now where artist = '$artist'";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result);    
    
echo'<h2 style="text-align: left; margin-left: 100px;">아티스트 채널</h2>';
echo'<h3 style="text-align: left; margin-left: 100px;">['.$artist.']
 <img src="heart2.jpg" width="10" height="10">'.$row['artist_like'].'</h3>';
echo'<h5 style=" color : black ;text-align: left; margin-left: 100px;">※ 아티스트 페이지에서는 각 사이트별 순위와 팬수, 상위 3개 곡의 순위 변화 추이를 보여드립니다. <br>순위 안에 3개 미만의 곡을 보유할 경우가 있음을 유의바랍니다.</h5>';     
echo'
<div id="chart-container" style="width: 800px ; margin: 0 auto;">
    <canvas id="myChart"></canvas>
</div>';
echo'
 <section>
    <article id = "container">
        <div id = "contentbody">
        <table class = "realchart" id = "chart">
        <thead>
            <tr>
              <th scope="col" class="rank_change"><strong>순위</strong></th>
              <th scope="col" class="rank_change"><span>멜론</span></th>
              <th scope="col" class="rank_change"><span>지니</span></th>
              <th scope="col" class="rank_change"><span>벅스</span></th>
              <th scope="col" class="img_url"></th>
              <th scope="col" class="title"><strong>곡 정보</strong></th>
              <th scope="col" class="album"><strong>앨범</strong></th>
              <th scope="col" class="like"><strong>좋아요</strong></th>
              <th scope="col" class="hidden"><strong>댓글수</strong></th>
              <th class="like" ><strong>감정분석</strong></th>
            </tr>
           </thead>
         <tbody>';

$sql_s = "SELECT * FROM ranking_now where artist = '$artist' ";
$result_s = mysqli_query($conn,$sql_s);
    
$i=0;
$rank_temp=0;
$idList=[];
while($row_s = mysqli_fetch_array($result_s)){
//    
//$sql = "SELECT * FROM ranking_now where id = "'.$row_s['id'].'"';
//$result = mysqli_query($conn,$sql);
//$row = mysqli_fetch_array($result);     
    
$sql_p = 'SELECT * FROM preRanking where id = "'.$row_s['id'].'"';
$result_p = mysqli_query($conn,$sql_p);
$row_p = mysqli_fetch_array($result_p);    
    
$sql_m = 'SELECT * FROM musicList_melon where id = "'.$row_s['id'].'"';
$result_m = mysqli_query($conn,$sql_m);
$row_m = mysqli_fetch_array($result_m);

$sql_g = 'SELECT * FROM musicList_genie where id = "'.$row_s['id'].'"';
$result_g = mysqli_query($conn,$sql_g);
$row_g = mysqli_fetch_array($result_g);

$sql_b = 'SELECT * FROM musicList_bugs where id = "'.$row_s['id'].'"';
$result_b = mysqli_query($conn,$sql_b);
$row_b = mysqli_fetch_array($result_b);   

    echo'<tr>';
    echo'<td>'.$row_s['rank'].'</td>';
    echo'<td>'.$row_m['ranking'].'</td>';
    echo'<td>'.$row_g['ranking'].'</td>';
    echo'<td>'.$row_b['ranking'].'</td>';
    echo'<td  ><img src ='.$row_s['img_url'].' width = "50" height = "50"></td>';
    //echo'<td ><strong>'.$row_s['title'].'</strong><br>';
    echo'<td class="artist_link" title ="해당 곡의 페이지로 넘어갑니다." >
    <a href="title.php?page=hotartist&title='.$row_s['title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row_s['title'].'</strong></a></td>';
    echo'<td class="artist_link" title="해당 앨범의 페이지로 넘어갑니다." >
    <a href="album.php?&album='.$row_s['album_title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row_s['album_title'].'</strong></td>';
    echo'<td ><img src="heart2.jpg" width="10" height="10">'.$row_s['like_sum'].'</td>';
    echo'<td >'.$row_s['comments_sum'].'</td>';
     echo'<td >'.round($row_p['sen_sum_score'],2).'</td>';
    echo '</tr>';
    $idList[] = $row_s['title'];
    $i+=1;
}
echo'</tbody>
</table>
</div>
</article>
</section>';

// multidemesion of array needed
//for( $k=0; $k < count($idList); $k++)
  //  $data=['time'=>[],'ranking'=>[]];
 //array( array(0),array(1));
$time_array=[];
//$array=[];
$data=[];
//echo'<h3 style="text-align: left; margin-left: 100px;">[ 랭킹 변화 추이 ]</h3>';
for( $k=0; $k < count($idList); $k++) {
    //echo'<div style="margin-left: 100px; color : #737373 ">';
    //echo'<h4>'.$idList[$k].'</h4>';
    $data[$k]= [];
    $temp=0;
    $time = ltrim(date('Ymd',strtotime('-1 day',time())), '2020');
    $present_time= (int)date("H");
    if($present_time!=23) {
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
                    } else {
                        if($data[$k][$temp-1])
                            array_push($data[$k], $data[$k][$temp-1]);
                        else
                            array_push($data[$k], 100);
                        //array_push($data[$k], 100);
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
    //today's db
    $time = ltrim(date('Ymd'), '2020');
    for ($i = 0; $i <= $present_time ; $i += 1) {
        if($i< 2 || $i>6) {
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
                } else {
                    if($data[$k][$temp-1])
                        array_push($data[$k], $data[$k][$temp-1]);
                    else
                        array_push($data[$k], 100);
                    //array_push($data[$k], 0);
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
    //$array[]=$data;
    //echo'</div>';
}
/*$list=[];
for($i=0; count($idList); $i++) {
    $column=[];
    for($j=0; $j < count($time_array); $j++) {
        $column[$j]=$data[$k++];
    }
    $list[$i]=$column;
}*/
for($i=0;$i<count($idList);$i++){
    switch ($i){
        case 0:
            $array1 = $data[$i];
            break;
        case 1:
            $array2 = $data[1];
            break;
        case 2:
            $array3 = $data[2];
            break;
        default:
            break;
    }
}

//json_encode($idList);
//json_encode($time_array);
//json_encode($array);

//var_dump($array); //변수의 내용을 출력하는 내용.
mysqli_close($conn);

?>

<script>
var ctx = document.getElementById("myChart").getContext('2d');

var time = [<?php echo '"'.implode('","',  $time_array ).'"' ?>]; // 활용 가능!!!
var data = [<?php echo '"'.implode('","',  $data ).'"' ?>];
var data0 = [<?php echo '"'.implode('","',  $array1 ).'"' ?>];
var data1 = [<?php echo '"'.implode('","',  $array2 ).'"' ?>];
var data2 = [<?php echo '"'.implode('","',  $array3 ).'"' ?>];
var id = [<?php echo '"'.implode('","',  $idList ).'"' ?>];
//var list=[]; //일차원 배열 생성
//var k=0;
/*
for(var i=0; id.length; i++) {
    //list[i]= []; // 다중 배열 생성
    var column=[];
    for(j=0; j < time.length; j++) {
        //list[i][j]= data[k];
        column[j]=data[k];
        k=k+1;
    }
    list[i]=column;
}
var chartlist=list[0];
*/
//차트의 동적 형성이 가능한지 확인해볼 것!
//var data=[6,52,57,65];
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: time,  //배열을 넣으면 분명히 작동은 된다!!!
        datasets: [{
            label: id[0],
            data: data0,
            borderColor:
                'rgba(255,99,132,1)',
            hoverRadius : 4,
            fill : false,
            backgroundColor:'rgba(255,99,132,1)',
                 //'rgba(255, 99, 132, 0.2)',
            borderWidth: 2
        },{
            label: id[1],
            data: data1,
            borderColor:
                'rgba(54, 162, 235, 1)',
            fill : false,
            hoverRadius : 4,
            backgroundColor:'rgba(54, 162, 235, 1)',
                //'rgba(54, 162, 235, 0.2)',
            borderWidth: 2
            },{
                label: id[2],
                data: data2,
                borderColor:
                    'rgba(255, 206, 86, 1)',
                fill : false,
                hoverRadius : 4,
                backgroundColor: 'rgba(255, 206, 86, 1)',
                    //'rgba(255, 206, 86, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
//            tooltips:
//            {
//                bodyfontcolor: 'black';
//            },
            "hover": {
            "animationDuration": 0
            },
            "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                //ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                //ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.fillStyle='rgba(0,0,0,0.5)';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];                            
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
            },
            maintainAspectRatio: true, // default value. false일 경우 포함된 div의 크기에 맞춰서 그려짐.
            scales: {
            yAxes: [{
                display: false,
                ticks: {
                    reverse: true,
                    //beginAtZero:true,
                    min: 0,
                    
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
</body>
</html>