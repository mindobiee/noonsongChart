<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <meta name="viewpoint" content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>charTrue >realtime</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <link rel ="stylesheet" media="(max-width:320px)" href="mobile.css" type ="text/css">
    <script src ="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src ="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src = "chartuse.js"></script>
    <script src= "includeHTML.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>

    <style>
        #searcher {
            margin-left: -200px;
            margin-top: -25px;
            vertical-align: middle;
            position: absolute;
            left: 50%;
        }
    	/* 바꾼것 -> 그래야 드롭다운이 보임.*/
        .tb{
            overflow: visible;
            width: 750px;
            margin : 25px auto;
        }
        .tb a{
            padding: 20px 15px 0px 15px;
        }
        .tb ul{
            text-align: left;
            list-style: none;
        }     
	/* 음원3사 엘아이의 클래스이름 */
        .newnav{
            position: static;
        }
        .newnav ul{
            list-style: none;
            position: absolute;
            height: 100px;
            width: 100px;
            left: 480px;
            top: 50px;
        }
         .newnav ul li{
            float: none;
            margin: 0px;
            padding: 0px;
        }

        .open > .newnav ul{
            display: block;
            position: absolute;
        }


        .realtime_chart{
            color: #0c2d83;
            font-weight:bold;
        }
        
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

    </style>
</head>
<body>
<header>
    <a href =info.html> <h4 style= "text-align: right"> 차트루란? </h4></a>
    <h1>
        <a href = "http://www.chartrue.site/main.php">
            <img src="logo2.jpg" class="logo" width = "250" height="110">
        </a>
    </h1>
    
    <div id ="searcher">
        <form action="search_server.php">
            <span class= "window" method="get">
                <input type="text" class="input_text" name="search_word" placeholder="차트루 랭킹이 궁금한 노래를 검색해주세요" autofocus>
                <button type="submit" class='sch_smit'><img src = "dod.png" height="33px" width="33px" ></button>
            </span>
        </form>
    </div>
    <div class="tb">
        <ul>
            <li><a class="realtime_chart" href="main.php" >실시간</a></li>
            <li><a class="senti_chart" href="sentimental.php" >감정분석</a></li>
            <li><a class="hottest_chart" href="hottest_chart.php" >급상승</a></li>
            <li><a class="daily_chart" href="daily_chart.php" >일간</a></li>
            <li><a class="hot_artist" href="hotartist.php" >핫아티스트</a></li>
            <li class="newnav dropdown">
                <a id="thisbutton" data-toggle="dropdown" class="dropdown-toggle" href="#">음원 3사
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu" aria-labelledby="thisbutton">
                        <li><a class="melon_chart" href="melon_chart.php">멜론</a></li>
                        <li><a class="bugs_chart" href="bugs_chart.php" >벅스</a></li>
                        <li><a class="genie_chart" href="genie_chart.php">지니</a></li>
                    </ul>  
            </li>
            <li><a class="comparing" href="compare.php" >비교차트</a></li>
        </ul>
    </div>
<div>
    <br><br><br>
    <hr>
</div>     
 </header>
<?php
    
    $conn = mysqli_connect('localhost','root','Song123~','gradproj');
if (!$conn) die(mysqli_get_warnings());
mysqli_query($conn, "set session character_set_connection=utf8;");
mysqli_query($conn,"set session character_set_results=utf8;");
mysqli_query($conn,"set session character_set_client=utf8;");

$sql = "SELECT title FROM ranking_now order by rank limit 5";
$result = mysqli_query($conn,$sql);
$idList=[];
while($row = mysqli_fetch_array($result))
    $idList[] = $row['title'];

$time_array=[];
$data=[];

for( $k=0; $k <5 ; $k++) {
    $data[$k] = [];
    $temp=0;
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
                        array_push($data[$k], $row['score_sum']);
                    } else { //fetch할 게 없을 때
                        if($data[$k][$temp-1]) //이전 index의 값이 있으면 그 값을 넣어줌.
                            array_push($data[$k], $data[$k][$temp-1]);
                        else
                            array_push($data[$k], 0);
		    }

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
                    array_push($data[$k], $row['score_sum']);
                } else {
                    if($data[$k][$temp-1])
                        array_push($data[$k], $data[$k][$temp-1]);
                    else
                        array_push($data[$k], 0);
                }
             } catch (Exception $exception) {
                $s = $exception->getMessage();
                //echo $s;
            }
            $temp+=1;
        }
       
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
echo'<h5 style="text-align: center;">※ charTrue 차트는 멜론, 벅스, 지니 3개의 음원사이트 랭킹을 바탕으로
새로운 순위 산정 방식을 통하여 집계한 순위를 보여드립니다.</h5>';     
echo '<h1 class="clock" style="color : black">'.date("Y.m.d", time()).'
    <span style = "color : #0c2d83 ;">'.date("H:00", time()).'
    <span class ="dropdown">
    <button type ="button" id="dropdownMenuButton" data-toggle="dropdown" 
    aria-haspopup="true" aria-expanded="false"> ▼ </button>';
echo'<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" role="listbox">';
$present_time= (int)date("H");
for($var=$present_time-1;$var>=0;$var--) {
    if($var<2 || $var>6) {
        if ($var < 10)
            echo '<li><a class="dropdown-item" href="time_change.php" onclick="time_change(this);" id ="0' . $var . '" >0' . $var . ':00</a></li>';
        else
            echo '<li><a class="dropdown-item" href="time_change.php" onclick="time_change(this);" id ="' . $var . '" >' . $var . ':00</a></li>';
    }
}
echo' </ul></span></span></h1>';

echo'
<h1>
    <div id="chart-container" style="width: 800px; margin: 0 auto;">
        <canvas id="myChart"></canvas>
    </div>
</h1>';
echo'<h5 style="text-align: center; color:black">☞ 다음은 charTrue top 5의 실시간 총 점수의 변화 추이를 보여드립니다.</h5>';
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
                    <th scope="col" class="like" ><strong>좋아요</strong></th>
                    <th scope="col" class="hidden"><strong>댓글수</strong></th>
                    <th scope="col" class="listen"><strong>듣기</strong></th>
                    
                </tr>
                </thead>
                <tbody>';

    
$ranking_db ='ranking_now';
$query = 'select * from '.$ranking_db.' order by rank asc';
$result = mysqli_query($conn,$query);

$i=0;
$rank_temp=0; //임시 랭킹 생성(bigint)
while($row = mysqli_fetch_array($result)){
    
    while($row['rank']==$rank_temp)//순위가 같은 랭킹이 나오면 다음으로 건너뛰기!
    {
        $rank_temp = $row['rank'];
        $row = mysqli_fetch_array($result); 
    }

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
   
    if($row['rank_change']>20)
        echo'<tr style="background-color : rgba(249,191,253,0.3);">';    
    elseif($row['rank_change']<-20)
        echo'<tr style="background-color : aliceblue;">';
    else 
        echo'<tr>';
    if($row['rank']==1)
        echo'<td style="color : rgba(255, 99, 132, 1)"><strong>'.$row['rank'].'</strong></td>';
    else if($row['rank']==2)
         echo'<td style="color : rgba(54, 162, 235, 1)"><strong>'.$row['rank'].'</strong></td>';
    else if($row['rank']==3)
        echo'<td style="color : rgba(255, 206, 86, 1)"><strong>'.$row['rank'].'</strong></td>';
    else if($row['rank']==4)
        echo'<td style="color : rgba(153, 102, 255, 1)"><strong>'.$row['rank'].'</strong></td>';
    else if($row['rank']==5)
        echo'<td style="color : rgba(255, 159, 64, 1)"><strong>'.$row['rank'].'</strong></td>';
    else
        echo'<td ><strong>'.$row['rank'].'</strong></td>';  
    
    $rank_temp = $row['rank'];
    if($row['rank_change']>0)
        echo'<td ><h4 style="color : orange"><img src ="up.jpg" width="10" height="10" ><strong>'.$row['rank_change'].'</strong></h4></td>';
    elseif($row['rank_change']<0)
        echo'<td ><h4 style="color : cornflowerblue"><img src ="down.jpg" width="10" height="10" ><strong>'.abs($row['rank_change']).'</strong></h4></td>';
    else
        echo'<td ><img src="https://img.icons8.com/officexs/16/000000/horizontal-line.png" width="20" height="10"></td>';
    echo'<td ><img src ='.$row['img_url'].' width = "50" height = "50"></td>';
    echo'<td title="'.$title_rank.'" >
    <div class="artist_link" title ="해당 곡의 페이지로 넘어갑니다." >
    <a href="title.php?page=hotartist&title='.$row['title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row['title'].'</strong></a></div><br>';
    echo'<div class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다.">
    <a href="artist.php?page=hotartist&artist='.$row['artist'].'"style=" color= black; text-decoration : none;">
    '.$row['artist'].'</a></div></td>';
    echo'<td class="artist_link" title="해당 앨범의 페이지로 넘어갑니다." >
    <a href="album.php?&album='.$row['album_title'].'"style=" color= black; text-decoration : none;">
    <strong>'.$row['album_title'].'</strong></td>';
    echo'<td title ="'.$title.'"><img src="heart2.jpg" width="10" height="10">
    <strong>'.$row['like_sum'].'</strong></td>';
    echo'<td class="hidden"  title="'.$title2.'"><strong>'.$row['comments_sum'].'</strong></td>';
//    echo'<td class="hidden"  ><strong>'.$row['score_sum'].'</strong></td>';
    echo'<td title="각 음원사이트의 곡 페이지로 넘어갑니다.">';
    if(strcmp($row['song_url_melon'],"0"))
        echo'<a href ='.$row['song_url_melon'].'><img src = "melonb.jpg" width = "30px" height="30px"></a>';
    if(strcmp($row['song_url_genie'],"0"))
        echo'<a href ='.$row['song_url_genie'].'><img src = "genieb.jpg" width = "30px" height="30px"></a>';
    if(strcmp($row['song_url_bugs'],"0"))
        echo'<a href ='.$row['song_url_bugs'].'><img src = "bugsb.jpg" width = "30px" height="30px"></a></td>';
    echo '</tr>';
    $i+=1;
}
mysqli_close($conn);
?>
</tbody>
</table>
</div>
</article>
<h4 class= "link" style="color: white"><a href="https://icons8.com/icon/63rLOqnhhG7l/horizontal-line">horizontal-line</a></h4>
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
// 	주석처리를 제외했을 때 그 값에 해당하는 텍스트가 보여짐.		
//            "hover": {
//            "animationDuration": 0
//            },
//            "animation": {
//            "duration": 1,
//            "onComplete": function () {
//                var chartInstance = this.chart,
//                ctx = chartInstance.ctx;
//                ctx.textAlign = 'center';
//                ctx.textBaseline = 'bottom';
//                ctx.fillStyle='rgba(0,0,0,0.5)';
//
//                this.data.datasets.forEach(function (dataset, i) {
//                    var meta = chartInstance.controller.getDatasetMeta(i);
//                    meta.data.forEach(function (bar, index) {
//                        var data = dataset.data[index];
//                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
//                    });
//                });
//            }
//            },
            maintainAspectRatio: true, // default value. false일 경우 포함된 div의 크기에 맞춰서 그려짐.
            scales: {
                yAxes: [{
                    display : false,
                    ticks: {
                        reverse : false,
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
