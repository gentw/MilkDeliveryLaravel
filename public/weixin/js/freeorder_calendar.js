/**
 * Created by Administrator on 3/22/17.
 */

var mydate=new Date();
var thisyear=mydate.getFullYear();//年
var thismonth=mydate.getMonth()+1;//月
var thisday=mydate.getDate();//日
var mydate1=new Date();
var thisyear1=mydate1.getFullYear();
var thismonth1=mydate1.getMonth()+1;
var thisday1=mydate1.getDate();
var selectday=thisday;

var garyBottle = [];
// 获取订购天数函数
var gfnOrderDayCount;

function initdata(){
    //日期初始填充
    jQuery(".selectdate").val(thisyear+"年"+thismonth+"月");
    var days=getdaysinonemonth(thisyear,thismonth);
    var weekday=getfirstday(thisyear,thismonth);
    setcalender(days,weekday,thismonth);
    markdate(thisyear,thismonth,selectday);
    orderabledate(thisyear,thismonth,thisday);
}

function orderabledate(thisyear,thismonth,thisday){
    //能可以选择的日期
    if(thisyear<thisyear1){
        jQuery(".data_table tbody td").addClass("orderdate");
        jQuery(".data_table tbody td").removeClass("usedate");
    }else if(thisyear==thisyear1){
        if(thismonth<thismonth1){
            jQuery(".data_table tbody td").addClass("orderdate");
            jQuery(".data_table tbody td").removeClass("usedate");
        }else if(thismonth==thismonth1){
            for(var j=0;j<6;j++){
                for(var i=0;i<7;i++){
                    var tdhtml=jQuery(".data_table tbody tr").eq(j).find("td").eq(i).find("p").eq(0).html();
                    if(tdhtml<thisday){
                        jQuery(".data_table tbody tr").eq(j).find("td").eq(i).addClass("orderdate");
                        jQuery(".data_table tbody tr").eq(j).find("td").eq(i).removeClass("usedate");
                    }else{
                        jQuery(".data_table tbody tr").eq(j).find("td").eq(i).removeClass("orderdate");
                    }
                }
            }
        }else{
            jQuery(".data_table tbody td").removeClass("orderdate");
        }
    }else{
        jQuery(".data_table tbody td").removeClass("orderdate");
    }
}
function markdate(thisyear,thismonth,thisday){
    //标记当前日期
    var datetxt=thisyear+"年"+thismonth+"月";
    var thisdatetxt=thisyear1+"年"+thismonth1+"月";
    jQuery(".data_table td").removeClass("tdselect");
    if(datetxt==thisdatetxt){
        for(var j=0;j<6;j++){
            for(var i=0;i<7;i++){
                var tdhtml=jQuery(".data_table tbody tr").eq(j).find("td").eq(i).find("span").find("p").html();
                if(tdhtml==thisday){
                    jQuery(".data_table tbody tr").eq(j).find("td").eq(i).addClass("tdselect");
                }
            }
        }
    }
}
function getdaysinonemonth(year,month){
    //算某个月的总天数
    month=parseInt(month,10);
    var d=new Date(year,month,0);
    return d.getDate();
}
function getfirstday(year,month){
    //算某个月的第一天是星期几
    month=month-1;
    var d=new Date(year,month,1);
    return d.getDay();
}
function setcalender(days,weekday,thismonth){
    //往日历中填入日期
    var a=1;
    for(var j=0;j<6;j++){
        for(var i=0;i<7;i++){
            if(j==0&&i<weekday){
                jQuery(".data_table tbody tr").eq(0).find("td").eq(i).html("");
                jQuery(".data_table tbody tr").eq(0).find("td").eq(i).removeClass("usedate");
            }else{
                if(a<=days){
                    //
                    // 查询该日期的数量
                    //
                    var nIndex = isSelectedDate(thisyear, thismonth, a);
                    var strHtml = "<p>"+a+"</p><div class='bottlecount'>";

                    //
                    // 初始化数量
                    //
                    if (nIndex < 0) {
                        strHtml += "<span id='date" + thismonth + "_" + a + "' style='display:none; font-size:20px;'>0</span>" +
                            "<p style='display:none;'>";
                    }
                    else {
                        strHtml += "<span id='date" + thismonth + "_" + a + "' style='font-size:20px;'>" + garyBottle[nIndex].num + "</span>" +
                            "<p>";
                    }

                    strHtml += "<img src='"+errimg+"'>" +
                        "</p>" +
                        "</div>";

                    jQuery(".data_table tbody tr").eq(j).find("td").eq(i).html(strHtml);
                    jQuery(".data_table tbody tr").eq(j).find("td").eq(i).addClass("usedate");
                    a++;
                }else{
                    jQuery(".data_table tbody tr").eq(j).find("td").eq(i).html("");
                    jQuery(".data_table tbody tr").eq(j).find("td").eq(i).removeClass("usedate");
                    a=days+1;
                }
            }
        }
    }
}

/**
 * 增加数量/表示
 * @param datet
 * @param at
 * @returns {*|jQuery}
 */
function thishtmls(datet,at){
    $(".data_table td").removeClass("tdselect");
    $("#date"+datet+"_"+at).parent().parent().addClass("tdselect")
    selectday=at;
    var number_add= $("#date"+datet+"_"+at).text();
    number_add++;
    $("#date"+datet+"_"+at).text(number_add);

    return number_add;
}

function lastmonth(){
    // 上一个月
    thismonth--;
    if(thismonth==0){
        thismonth=12;
        thisyear--;
    }
    initdata();
}

function nextmonth(){
    // 下一个月
    thismonth++;
    if(thismonth==13){
        thismonth=1;
        thisyear++;
    }
    initdata();
}

function del(datedel,adel){
    //减少数量
    var number_del=$("#date"+datedel+"_"+adel).text();
    $("#date"+datedel+"_"+adel).text('0');
}

/**
 * 显示/隐藏
 * @param show
 */
function showBottleCount(container, show) {
    container.children().each(function() {
        if (show) {
            $(this).show();
        }
        else {
            $(this).hide();
        }
    });
}

/**
 * 该日期是否已选择
 * @param year
 * @param month
 * @param date
 * @returns {number}
 */
function isSelectedDate(year, month, date) {
    var dateNew = new Date(year, month - 1, date);

    // 查看是否已存在
    var l = garyBottle.length;
    var i = 0, nIndex = -1;
    for (i = 0; i < l; i++) {
        if (Date.parse(garyBottle[i].date) - Date.parse(dateNew) == 0) {
            nIndex = i;
            break;
        }
    }

    return nIndex;
}

/**
 * 构成随心送规则数据
 * @returns {string}
 */
function getFormattedFreeOrderData() {
    var strResult = '';
    var i = 0, l = garyBottle.length;

    for (i = 0; i < l; i++) {
        strResult += garyBottle[i].getFormattedValue();

        if (i < l - 1) {
            strResult += ',';
        }
    }

    return strResult;
}

$(document).ready(function() {

    initdata();

    var strSelector = '.data_table tbody td';

    /**
     * 点击日历里面的日期
     */
    $(strSelector).on('click', '.bottlecount', function() {
        var td = $(this).parent();
        var nDate = parseInt(td.find("p").html());

        // 显示数量和取消按钮
        showBottleCount($(this), true);

        var nCount = thishtmls(thismonth, nDate);

        // 查看是否已存在
        var nIndex = isSelectedDate(thisyear, thismonth, nDate);
        var dateNew = new Date(thisyear, thismonth - 1, nDate);

        // 如果不存在，添加新的
        if (nIndex < 0) {
            garyBottle.push(new bottle(dateNew, nCount));
        }
        else {
            garyBottle[nIndex].num = nCount;
        }

        // 计算订购天数
        calcOrderDayCount();
    });

    /**
     * 点击清空按钮
     */
    $(strSelector).on('click', '.bottlecount p', function(e) {
        var td = $(this).parent().parent();
        var nDate = parseInt(td.find("p").html());

        // 隐藏数量和取消按钮
        showBottleCount($(this).parent(), false);

        del(thismonth, nDate);

        // 从数组里删除
        var nIndex = isSelectedDate(thisyear, thismonth, nDate);
        garyBottle.splice(nIndex, 1);

        // 计算订购天数
        calcOrderDayCount();

        // 防止父元素的点击事件
        return false;
    });
});

/**
 * 初始化奶瓶数量
 * @param strData 日期:数量的组合
 */
function initBottleCount(strData, fnCalcOrderDate) {
    // 转成数组
    var strArray = strData.replace(/,\s*$/, "");
    var data_array = strArray.split(',');

    for(var i =0; i< data_array.length; i++)
    {
        var day_val = data_array[i];
        var strDate = day_val.split(":")[0];
        var nCount = parseInt(day_val.split(":")[1]);

        var aryDate = strDate.split("-");
        var nYear = parseInt(aryDate[0]);
        var nMonth = parseInt(aryDate[1]);
        var nDay = parseInt(aryDate[2]);

        var dateNew = new Date(nYear, nMonth - 1, nDay);

        // 添加到主数组
        garyBottle.push(new bottle(dateNew, nCount));
    }

    initdata();

    gfnOrderDayCount = fnCalcOrderDate;
}

/**
 * 计算订购天数
 */
function calcOrderDayCount() {
    if (gfnOrderDayCount) {
        gfnOrderDayCount(garyBottle.length);
    }
}