$('.dropdown-toggle').dropdown();

function test(){
}
$('#myDropdown').on('show.bs.dropdown', function () {
    alert("mydropdown pressed!");

})

function unfold_table() {
    var x = document.getElementsByClassName("hidden");
    var i;
    for (i = 0; i < x.length; i++){
        if (x[i].style.visibility === 'visible')
            x[i].style.visibility = 'hidden';
        else
            x[i].style.visibility = 'visible';
    }
}
var hour;

/*$(document).ready(function () {
    createCoockie("hour");
});*/

function time_change(obj) {
    //let x = document.getElementsByClassName("dropdown-item");
    //var id_by_class = $('.dropdown-item').attr('id');
    var y;
    var id_by_this= obj.id; //$(obj).attr('id');
    for(var i = 0; i< 24; i++) {
        if(i < 10){
            if ('0'+toString(i) === toString(id_by_this)) {
                y = 'ranking_' + id_by_this;
                break;
            }
        }else {
            if (toString(i) === toString(id_by_this)) {
                y = 'ranking_' + id_by_this;
                break;
            }
        }
    }
    hour=y;
    //alert('ranking_db= '+ y+', hour='+hour);
    //alert(id_by_this+':00 시간 랭킹을 확인하겠습니다.');
    document.cookie = "hour ="+ y +";";
    return y;
    // 출력하는 앨범명 : ranking_now -> ranking_00 바꿔주기!
}

