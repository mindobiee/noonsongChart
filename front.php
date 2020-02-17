<!DOCTYPE html>
<html lang ="ko">
<head>
    <meta charset="UTF-8">
    <title>눈송차트</title>
    <style type="text/css">
        table#head {
            width : 100%;
        }
        table#chart{
           width : 100%;
        }
        table#chart th{
            height : 30px;
            text-align: left;
        }
        table#chart th, td{
            padding : 15px;
            border-bottom : 1px solid #ddd;
        }
        table#chart tr:hover{background-color:#f5f5f5;}

        body{
            background-color:lightblue;
        }

        h2{
            color: white;
            text-align:center;
        }
        nav{
            background-color:skyblue;
            float:left;
            margin:5px;
        }
        div#side{
            left :0px;
            height :300px;
        }

        div#contentbody{
            width: 1000px;
            height : 7000px;
            background-color:white;
            margin:5px;
            float :left;
            text-align : center;
        }
        p{
            font-family:verdana;
            font-size:15px;
        }
        footer{
            background-color: #777;
            padding :10px;
            text-align:center;
            color :white;
        }
    </style>
</head>
<body>
<header>
    <table id ="head">
        <tr>
            <td> <img src="calligraphy_01.gif" width = "200" height="100"></td>
            <td> <h2 width="200">눈송차트</h2></td>
            <td>
                <input type="text" name="search">
                <input type="submit" value="검색"><br>
            </td>
        </tr>
    </table>
</header>
<hr noshade>
<section>
    <nav>
        <div id="side">
            <!-- <span id="gnbHandleBtn" class="btnMenuFolding open" style status ="open">MENU</span>-->
            <div id = "gnbBody" class="jspscrollable" tabindex ="0" tabindex ="0">
                <div class="links">
                    <ul>
                        <li><a href="https://www.melon.com/chart/index.htm" target="box">
                                <img src="melon.jpg"  alt="멜론" width ="100" height="50"></a></li>
                        <li><a href="https://music.bugs.co.kr/chart" target="box">
                                <img src="bugs.jpg" alt="벅스" width ="100" height="50"></a></li>
                        <li><a href="https://www.genie.co.kr/chart/top200" target="box">
                                <img src="genie.jpg" alt="지니" width ="100" height="50"></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <article id = "container">
        <div id = "contentbody">
            <table class = "realchart" id = "chart">
                <thead>
                <tr>
                    <th scope="col" class="ranking"><span>순위</span></th>
                    <th scope="col" class="img_url"></th>
                    <th scope="col" class="title"><span>곡</span></th>
                    <th scope="col" class="artist"><span>가수</span></th>
                    <th scope="col" class="album"><span>앨범</span></th>
                    <th scope="col" class="like_sum"><span>좋아요</span></th>
                    <th scope="col" class="like_cnt"><span>좋아요 증가수</span></th>
                    <th scope="col" class="comments_sum"><span>댓글 수</span></th>
                    <th scope="col" class="comments_cnt"><span>댓글 증가수</span></th>
                    <th scope="col" class="likes"><span>감정 지수</span></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $conn = mysqli_connect('localhost','root','960919','gradualDB');
                if (!$conn) die(mysqli_get_warnings());
                mysqli_query($conn, "set session character_set_connection=utf8;");
                mysqli_query($conn,"set session character_set_results=utf8;");
                mysqli_query($conn,"set session character_set_client=utf8;");

                $query = 'select * from musicList_bugs order by ranking asc';
                $result = mysqli_query($conn,$query);
                while($row = mysqli_fetch_array($result)){
                    echo'<tr>';
                    echo'<td align="center"><strong>'.$row['ranking'].'</strong></td>';
                    echo'<td align="center"><a href ='.$row['img_url'].'></a></td>';
                    echo'<td align="center">'.$row['title'].'</td>';
                    echo'<td align="center">'.$row['artist'].'</td>';
                    echo'<td align="center">'.$row['album_title'].'</td>';
                    echo'<td align="center">'.$row['like_sum'].'</td>';
                    echo'<td align="center">'.$row['like_cnt'].'</td>';
                    echo'<td align="center">'.$row['comments_sum'].'</td>';
                    echo'<td align="center">'.$row['comments_cnt'].'</td>';
                    echo'<td align="center">10</td>';
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
<!--<footer>
    <p>footer</p>
</footer>
-->
</body>
</html>
