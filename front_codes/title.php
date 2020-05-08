<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8" name = "viewpoint" content="width=device-width, initial-scale=1">
    <title>charTrue > song page</title>
    <link href="https://fonts.googleapis.com/css?family=Asap|Permanent+Marker&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:500&display=swap&subset=korean" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Nanum+Gothic&display=swap' rel="stylesheet">
    <link rel ="stylesheet" href="style2.css" type ="text/css">
    <script type="text/javascript" src="chartuse.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src = "Java.js"></script>
    <script src = "chartuse.js"></script>
    <script src="includeHTML.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>

    <style>
       
        .realtime_chart{
            color: #0c2d83;
            font-weight:bold;
        }
        
       
        .all-info{
            width : 80%;
            margin: 0 auto;
            height: 200px;
            border-color: rgba(0,0,0,0.5) ;
            border-left-color: white;
            border-style:ridge;
            
        }
         #image{
            display: inline-block;
            float:left;       
        }
        #com{
            position: absolute;
            display: inline-block;
            float:left;
            margin-top: 20px;  
            color :rgba(0,0,0,0.7) ;
            font-family:'Nanum Gothic', sans-serif;
        }
        
        #info{
            display : inline-block;
            position: relative;
            padding: 20px 0 0 70px;
            color :rgba(0,0,0,0.7) ;
            font-family:'Nanum Gothic', sans-serif;
            height: 160px;
            width: 35%;
        }
        
        .charts{
            display: block;
            position: relative; 
            width: 600px; 
            margin: 0 auto;
        }
        table#chart{
            width: 80%;
        }
        table#chart td{
            text-align: center;
        }
        #combu{
            width: 60px;
            height: 30px;
        }
        
    </style>
</head>
<body>
<header include-html="header.html"></header>
<script>
        includeHTML();
</script>

<section>   
<?php
$conn = mysqli_connect('localhost','root','Song123~','gradproj');
if (!$conn) die(mysqli_get_warnings());
mysqli_query($conn, "set session character_set_connection=utf8;");
mysqli_query($conn,"set session character_set_results=utf8;");
mysqli_query($conn,"set session character_set_client=utf8;");

//song page <- 전 페이지에서 클릭한 title 값 가져오기	
$title=$_GET['title'];
$sql_n = 'SELECT * FROM ranking_now where title = "'.$title.'"';
$result_n = mysqli_query($conn,$sql_n);
$row_n = mysqli_fetch_array($result_n);
          
    
echo'<h2 style="text-align: left; margin-left: 130px;">'.$row_n['rank'].'.'.$title.'</h2>';
echo'<h5 style=" color : gray ;text-align: left; margin-left: 130px;">※ 곡 페이지에서는 해당 곡의 기본 정보 및 점수 환산 점수와 순위, 좋아요, 댓글수의 실시간 변화 추이를 보여드립니다.</h5>'; 
//좋아요와 댓글수의 역전은 각 음원사이트에서 순위 밖에 있어 해당 항목이 차감되었기 때문임을 알려드립니다.    

$sql = 'SELECT * FROM songs where title = "'.$title.'"';
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result);


    
$sql_p = 'SELECT * FROM preRanking where title = "'.$title.'"';
$result_p = mysqli_query($conn,$sql_p);
$row_p = mysqli_fetch_array($result_p);
    
$sql_m = 'SELECT * FROM musicList_melon_pre where id = "'.$row['id'].'"';
$result_m = mysqli_query($conn,$sql_m);
$row_m = mysqli_fetch_array($result_m);
        
$sql_g = 'SELECT * FROM musicList_genie_pre where id = "'.$row['id'].'"';
$result_g = mysqli_query($conn,$sql_g);
$row_g = mysqli_fetch_array($result_g);
        
$sql_b = 'SELECT * FROM musicList_bugs_pre where id = "'.$row['id'].'"';
$result_b = mysqli_query($conn,$sql_b);
$row_b = mysqli_fetch_array($result_b);
   
echo'<div class="all-info"><div id="image"><img src ='.$row['img_url'].' width = "200" height = "200"></div>';
echo'<div id="info">';//.$row['title'];
    
echo'<br><br><div title="각 음원사이트의 곡 페이지로 넘어갑니다."><strong>곡페이지</strong>&emsp;'; 
if(strcmp($row['song_url_melon'],"0"))
    echo'<a href ='.$row['song_url_melon'].'><img src = "melonb.jpg" width = "23px" height="23px"></a>';
if(strcmp($row['song_url_genie'],"0"))
    echo'<a href ='.$row['song_url_genie'].'><img src = "genieb.jpg" width = "23px" height="23px"></a>';
if(strcmp($row['song_url_bugs'],"0"))
    echo'<a href ='.$row['song_url_bugs'].'><img src = "bugsb.jpg" width = "23px" height="23px"></a>';
echo'</div>';    
    
echo'<br><div class="artist_link"><strong> 아티스트&emsp;
<a class="artist_link" title="해당 아티스트의 페이지로 넘어갑니다." href="artist.php?page=hotartist&artist='.$row['artist'].'"style=" color: black; text-decoration : none;">'.$row['artist'].'</a></strong></div><br>';    
    
echo'<div class="artist_link"><strong> 앨범&emsp;
<a class="artist_link" title="해당 앨범의 페이지로 넘어갑니다." href="album.php?&album='.$row['album_title'].'" style=" color= black; text-decoration : none;">'.$row['album_title'].'</strong></a></div></div>';
echo'<div id="com"><br><br>';
  
echo'<div class="artist_link" title="댓글을 보시려면 눌러주시기 바랍니다."><strong>댓글&emsp;'.$row['cmts_sum'].'개 &nbsp;</strong>';    
	
echo'<a  href="comment2.php?id='.$row['id'].'&title='.$title.'">&emsp;<img src="comicon.png" id="combu"></a></div>'; 
    
echo'<br><div><strong>감정 분석&emsp; '.round($row['sen_score'],2).'점</strong></div>';
    
echo'<br><div><strong>좋아요&emsp;   <img src="heart2.jpg" width="10" height="10">'.$row['like_sum'].'</strong></div></div></div>';
  
    
echo'
    <article id = "container">
        <div id = "contentbody">
        <table class = "realchart" id = "chart">
        <thead>
            <tr>
              <th scope="col" ><strong>항목</strong></th>
              <th scope="col" ><strong>멜론</strong></th>
              <th scope="col" ><strong>지니</strong></th>
              <th scope="col" ><strong>벅스</strong></th>
              <th scope="col" ><strong>총 점수</strong></th>
              <th scope="col" ><strong>차트루</strong></th>
             
            </tr>
           </thead>
         <tbody>';

        echo'<tr><td><strong>순위</strong></td>';
       
        echo'<td><strong>'.$row_m['ranking'].'</strong></td>';
        echo'<td><strong>'.$row_g['ranking'].'</strong></td>';
        echo'<td><strong>'.$row_b['ranking'].'</strong></td>';
        echo'<td><strong>'.round($row_p['ranking'],2).'</strong></td>';
        echo'<td><strong>'.$row_n['rank'].'</strong></td></tr>';
    
        echo'<tr><td><strong>좋아요</strong></td>';

        echo'<td><strong>'.$row_m['like_sum'].'</strong></td>';
        echo'<td><strong>'.$row_g['like_sum'].'</strong></td>';
        echo'<td><strong>'.$row_b['like_sum'].'</strong></td>';
        echo'<td><strong>'.$row_p['like_sum_score'].'</strong></td>';
        echo'<td><strong>'.$row['like_sum'].'</strong></td></tr>';    
        
        echo'<tr><td><strong>댓글 수</strong></td>';
       
        echo'<td><strong>'.$row_m['comments_sum'].'</strong></td>';
        echo'<td><strong>'.$row_g['comments_sum'].'</strong></td>';
        echo'<td><strong>'.$row_b['comments_sum'].'</strong></td>';
        echo'<td><strong>'.$row_p['comments_sum_score'].'</strong></td>';
        echo'<td><strong>'.$row['cmts_sum'].'</strong></td></tr>';
    
        echo'<tr><td><strong>아티스트팬</strong></td>';
       
        echo'<td><strong>'.$row_m['artist_like'].'</strong></td>';
        echo'<td><strong>'.$row_g['artist_like'].'</strong></td>';
        echo'<td><strong>'.$row_b['artist_like'].'</strong></td>';
        echo'<td><strong>*</strong></td>';
        echo'<td><strong>'.$row['artist_like'].'</strong></td></tr>';
    
        echo'<tr><td><strong>좋아요 증가</strong></td>';
       
        echo'<td><strong>'.$row_m['like_cnt'].'</strong></td>';
        echo'<td><strong>'.$row_g['like_cnt'].'</strong></td>';
        echo'<td><strong>'.$row_b['like_cnt'].'</strong></td>';
        echo'<td><strong>'.$row_p['like_cnt_score'].'</strong></td>';
        echo'<td><strong>'.$row['like_cnt'].'</strong></td></tr>';
    
        echo'<tr><td><strong>댓글 증가</strong></td>';
        echo'<td><strong>'.$row_m['comments_cnt'].'</strong></td>';
        echo'<td><strong>'.$row_g['comments_cnt'].'</strong></td>';
        echo'<td><strong>'.$row_b['comments_cnt'].'</strong></td>';
        echo'<td><strong>'.$row_p['comments_cnt_score'].'</strong></td>';
        echo'<td><strong>'.$row['comments_cnt'].'</strong></td></tr>';
    
        echo'<tr><td><strong>감정 분석</strong></td>';
        echo'<td colspan="3"><strong>'.round($row['sen_score'],2).'</strong></td>';
        echo'<td colspan="2"><strong>'.round($row_p['sen_sum_score'],2).'</strong></td></tr>';
    
        echo'<tr><td><strong>총 점수</strong></td>';
        echo'<td colspan="5"><strong>'.$row_n['score_sum'].'점</strong></td>';
    
        $id = $row['id'];
        
echo'</tbody>
</table>
</div>
</article>';
    
echo'
<div style="text-align: left; margin-left: 130px;"><h3>[시간대별 변화추이]</h3></div>
<div class="charts" id="chart-container">
    <canvas id="myChart"></canvas>
</div>
<div class="charts" id="chart-container">
    <canvas id="myChart2"></canvas>
</div>
<div class="charts" id="chart-container">
    <canvas id="myChart3"></canvas>
</div>';
 
   
$time_array=[];
$data=[];
$like=[];
$comment=[];
$time = ltrim(date('Ymd',strtotime('-1 day',time())), '2020');
$present_time= (int)date("H");
$temp=0;
if($present_time!=23) {
        for ($i = $present_time + 1; $i < 24; $i += 1) {
            if ($i < 2 || $i > 6) {
                if ($i < 10)
                    $sqlQuery = 'select * from ranking_200' . $time . '_0' . $i . ' where id = "' . $id . '"';
                else
                    $sqlQuery = 'select * from ranking_200' . $time . '_' . $i . ' where id = "' . $id. '"';

                array_push($time_array, $i);
                try {
                    if (!$result = mysqli_query($conn, $sqlQuery))
                        ;//echo mysqli_error($conn);
                    if ($row = mysqli_fetch_array($result)) { //if($row['rank']!=null)
                        array_push($data, $row['rank']);
                        array_push($like, $row['like_sum']);
                        array_push($comment, $row['comments_sum']);

                    } else {
                        if($data[$temp-1]){ //이전 index의 값이 있으면 그 값을 넣어줌.
                            array_push($data, $data[$temp-1]);
                            array_push($like, $like[$temp-1]);
                            array_push($comment, $comment[$temp-1]);
                        }
                        else{
                            array_push($data, 100);
                            array_push($like, 0);
                            array_push($comment, 0);
                        }
                    }
                   $temp+=1;

                } catch (Exception $exception) {
                    $s = $exception->getMessage();
                    //echo $s;
                }
            }
        }
}
    //today's db
$time = ltrim(date('Ymd'), '2020');
for ($i = 0; $i <= $present_time ; $i += 1) {
        if($i< 2 || $i>6) {
            if ($i < 10)
                $sqlQuery = 'select * from ranking_200' . $time . '_0' . $i . ' where id = "' . $id . '"';
            else
                $sqlQuery = 'select * from ranking_200' . $time . '_' . $i . ' where id = "' . $id . '"';

            array_push($time_array, $i);
            try {
                if (!$result = mysqli_query($conn, $sqlQuery))
                    ;//echo mysqli_error($conn);
                if ($row = mysqli_fetch_array($result)) { //if($row['rank']!=null)
                    array_push($data,$row['rank']);
                    array_push($like, $row['like_sum']);
                    array_push($comment, $row['comments_sum']);

                } else {
                        if($data[$temp-1]){ //이전 index의 값이 있으면 그 값을 넣어줌.
                            array_push($data, $data[$temp-1]);
                            array_push($like, $like[$temp-1]);
                            array_push($comment, $comment[$temp-1]);
                        }
                        else{
                            array_push($data, 100);
                            array_push($like, 0);
                            array_push($comment, 0);
                        }
                }
              
                $temp+=1;

            } catch (Exception $exception) {
                $s = $exception->getMessage();
                //echo $s;
            }
        }
    }

mysqli_close($conn);

?>

<script>
    var ctx = document.getElementById("myChart").getContext('2d');
    var ctx2 = document.getElementById("myChart2").getContext('2d');
    var ctx3 = document.getElementById("myChart3").getContext('2d');

    var time = [<?php echo '"'.implode('","',  $time_array ).'"' ?>];
    var data0 = [<?php echo '"'.implode('","',  $data ).'"' ?>];
    var data1 = [<?php echo '"'.implode('","',  $like ).'"' ?>];
    var data2 = [<?php echo '"'.implode('","',  $comment ).'"' ?>];

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: time, 
            datasets: [{
                label: "순위",
                data: data0,
                borderColor:
                    'rgba(255,99,132,1)',
                hoverRadius : 4,
                fill : false,
                backgroundColor:'rgba(255,99,132,1)',
                borderWidth: 2,
                
            }]
        },
        options: {
            
            "hover": {
            "animationDuration": 0
            },
            "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];                            
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
            maintainAspectRatio: true, 
            /*title: {
                display: true,
                text: '[ 시간대별 변화추이 ]'
            },*/
            scales: {
                yAxes: [{
                    display : false,
                    ticks: {
                        reverse: true,
                         min : 1
                        //beginAtZero:true,
                    },
                    gridLines:{
				color: 'rgba(255,255,255,1)',
				lineWidth: 1
		    }
                }]
            }
        }
    });
     var myChart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: time,
            datasets: 
            [{
                label: "좋아요",
                data: data1,
                borderColor:
                    'rgba(54, 162, 235, 1)',
                fill : false,
                hoverRadius : 4,
                backgroundColor:'rgba(54, 162, 235, 1)',
                    //'rgba(54, 162, 235, 0.2)',
                borderWidth: 2,
                

            }]
        },
        options: {
            
            "hover": {
            "animationDuration": 0
            },
            "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];                            
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
            },
            maintainAspectRatio: true, 
            /*title: {
                display: true,
                text: '[시간대별 변화추이]'
            },*/
            scales: {
                yAxes: [{
                    display : false,
                    gridLines:{
				color: 'rgba(255,255,255,1)',
				lineWidth: 1
			}
                }]
            }
        }
    });
    var myChart3 = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: time, 
            datasets: 
            [{
                label: "댓글",
                data: data2,
                borderColor:
                    'rgba(255, 206, 86, 1)',
                fill : false,
                hoverRadius : 4,
                backgroundColor:'rgba(255, 206, 86, 1)',
                    //'rgba(255, 206, 86, 0.2)',
                borderWidth: 2,
                
            }]
        },
        options: {
            
            "hover": {
            "animationDuration": 0
            },
            "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];                            
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
            },
            maintainAspectRatio: true, 
            /*title: {
                display: true,
                text: '[시간대별 변화추이]'
            },*/
            scales: {
                yAxes: [{
                    display : false,
                    gridLines:{
				color: 'rgba(255,255,255,1)',
				lineWidth: 1
			}
                }]
            }
        }
    });
</script>     
</section>
</body>
</html>
