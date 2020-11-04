/*global variables*/
// const DEBUG = false; // set to false to disable debugging
const DEBUG = true; // set to false to disable debugging
// const old_console_log = console.log;
const IS_SF = navigator.userAgent.indexOf('Safari') !== -1 && navigator.userAgent.indexOf('Chrome') === -1;
// const IS_FF = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
/*****************************
//console.log
*****************************/
// console.log = function() {
//     if (DEBUG) {
//         old_console_log.apply(this, arguments);
//     }
// };

const isQAsite = /qa/.exec(location.host) || /localhost/.exec(location.host);
const APIHOST= isQAsite ? 'https://qa.astra.cloud/' : 'https://astra.cloud/';
const PAGE = {
    annMainList:  '/admin/announcement',
    annHisList:  '/customer/announcement/history',
    annUpdate: '/admin/announcement/update',
    annDelete: '/customer/announcement/manage/delete/{ann_id}',
    annCreate:  '/admin/announcement/create',
    custManage: '/organization/customer',
    custDetail: '/organization/location?customer_id={customer_id}',
    issueDetail: '/dashboard/issue/detail/{issue_id}',
    scheduleUpdate: '/system/deploy/schedule/update/{schedule_id}',
    scheduleAdd: '/system/deploy/schedule/add',
    scheduleDelete: '/system/deploy/schedule/delete/{schedule_id}',
    peopleCount: '/customer/people_counting/did/',
    orgManage: '/organization/manage',
    adminAccount: '/admin/account'
};
//*******************************
//get lan json file
//*******************************
const fetchLAN = () => {
    var result;
    var langArr = ['en-US', 'ja-JP'];
    var defaultLocale = 'ja-JP';
    var _lang = getCookie('use_lang') ? getCookie('use_lang') : defaultLocale ;
    _lang = langArr.join(',').indexOf(_lang) !== -1 ? _lang : defaultLocale ;
    const path = location.hostname === 'localhost' ? '/assets/lang/src/' : '/assets/lang/js/';
    const ver = localStorage.getItem('lanVer') ? localStorage.getItem('lanVer') : new Date().getTime();
    $.ajax({
        type: 'GET',
        url: path + _lang + '.json?ver=' + ver,
        dataType: 'json',
        async:false,
        success:function(data){
            localStorage.setItem('lanVer', ver);
            result = data;
        }
    });
    return $.extend(result, {lang: _lang});
}
const LAN = fetchLAN();
//*******************************
//get dataTable lan json file
//*******************************
LAN.dataTable = (function () {
    let result;
    $.ajax(
        {
            type: 'GET',
            url: '/assets/json/' + LAN.lang + '_dataTable.json',
            dataType: 'json',
            async:false,
            success:function(data) {
                result = data;
            }
        }
    );
    return result;
})();
// LAN.err = (function () {
//     let result = {};
//     $.getJSON('/dist/json/error_code', function (data) {
//         for (var i in data) {
//             result[i] = data[i][LAN.lang];
//         }
//     });
//     return result;
// })();
//*******************************
//get parsley lan json file
//*******************************
LAN.parsley = (function () {
    let result;
    console.log('LAN.parsley');
    console.log(LAN.lang);
    $.ajax(
        {
            type: 'GET',
            url: '/assets/json/' + LAN.lang + '_parsley.json',
            dataType: 'json',
            async:false,
            success:function(data) {
                result = data;
            }
        }
    );
    console.log('parsley');
    console.log(result);
    return result;
})();
//********************************
//$.fn.replaceElString
//The method searches a string for a specified value
//{object}, target element,  row
//{object}, target data,  row data
//********************************
$.fn.replaceElString = function (target, value) {
    //console.log("replaceElStrig is executed.");
    $.each(value, function ( i , item) {
        if (item === null) {
            return;
        }
        var reg = new RegExp("{" + i + "}", "g");
        //console.log(reg.exec(target));
        //console.log("replace param:" + reg + ", value:" + item);
        target = target.replace(reg, item);
    });
        //console.log("replace finished target:" + target);
        return target;
};
//*******************************
//SupperXHR
//ajax request supper class
//You can use it by Object.create
//example: 
//var xhr = Object.create(SupperXHR);
//xhr.url = {url};
//xhr.data = {data};
//xhr.context= {context};
//xhr.getXHR();
//xhr.done(functin () {....});
//*******************************
var SupperXHR = {
    getXHR: function() {
        var ajaxSettings = {
            url: this.url,
            type: "POST",
            dataType: "JSON",
            data: this.data,
            context: this.context
        };
        ajaxSettings = $.extend(ajaxSettings, this.ajaxSettings);
        this.xhr = $.ajax(ajaxSettings);
        this.done = this.xhr.done;
        return this.xhr;
    },
    getData: function() {
        return this.data;
    },
    getContext: function() {
        return this.context;
    },
    setXHR: function(settings) {
        this.ajaxSettings = settings;
    }

};
//*********************
// setCookie
//*********************
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
//*********************
// getCookie
//*********************
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
$(function () {
    // **lang drop menu init
    $('.langMenu a').click(function (e) {
        var lang = $(this).data().lang;
        setCookie('use_lang', lang, 30);
        localStorage.removeItem('lanVer');
        location.reload();
    });
    if ($.fn.datepicker) {
        $.fn.datepicker.dates.ja = {
            days: ['日曜', '月曜', '火曜', '水曜', '木曜', '金曜', '土曜'],
            daysShort: ['日', '月', '火', '水', '木', '金', '土'],
            daysMin: ['日', '月', '火', '水', '木', '金', '土'],
            months: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
            monthsShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
            today: '今日',
            format: 'yyyy/mm/dd',
            titleFormat: 'yyyy年mm月',
            clear: 'クリア'
        };
        $.fn.datepicker.defaults.language = LAN.lang.substring(0, 2);
    }
    console.log('doc');
    //********************************
    // set parsley lang
    //********************************
    // Validation errors messages for Parsley
    // Load this after Parsley

    if (window.Parsley) {
        const parsleyLAN = LAN.lang.substr(0, 2);
        Parsley.addMessages(parsleyLAN, LAN.parsley);
        Parsley.setLocale(parsleyLAN);
    }
    //*****************************
    // init side menu
    // === following js will activate the menu in left side bar based on url ====
    //*****************************
    $("#sidebar-menu a").each(function() {
        if (this.href == window.location.href || window.location.href.indexOf(this.href) != -1) {  //Betty add
            $(this).addClass("active");
            $(this).parent().addClass("active"); // add active to li of the current link
            $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
            $(this).parent().parent().prev().click(); // click the item to make it drop
        }
    });
});
