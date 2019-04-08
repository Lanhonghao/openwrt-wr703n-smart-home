////////////////////////////////////////////////////////////////////////////////////////辅助方法//////////////////////////////////////////////////////////////////
function NewGuid() {
    var guid = "";
    for (var i = 1; i <= 32; i++) {
        guid += Math.floor(Math.random() * 16.0).toString(16);
        if ((i == 8) || (i == 12) || (i == 16) || (i == 20))
            guid += "-";
    }
    return guid;
}

function Toast(msg) {
    var tdiv = $("div[toast='jyeoo']");
    if (tdiv.size() < 1) {
        tdiv = $("<div toast='jyeoo' toe='0' style='font-size:1em;font-style:normal;font-weight:normal;position:absolute;line-height:20px;background-color:rgba(0, 0, 0, 0.6);padding:5px 10px;border-radius:5px;display:none;z-index:100000;box-shadow:1px 4px 10px #BEBEBE;max-width:300px;white-space:normal;word-break:break-all;color:#fff'></div>");
        tdiv.appendTo($(document.body));
    }
    window.clearTimeout(parseInt(tdiv.attr("toe")));
    tdiv.text(msg);
    tdiv.css("left", (document.documentElement.clientWidth - tdiv.width()) / 2 + "px");
    tdiv.css("top", document.documentElement.clientHeight - tdiv.height() - 50 + "px");
    tdiv.fadeIn("normal", function () {
        var toe = window.setTimeout(function () {
            tdiv.fadeOut("slow");
        }, 2000);
        tdiv.attr("toe", toe);
    });
}

function InputHint(obj, colorE, colorN) {
    if (obj == null || obj.getAttribute("hint") == null || $.trim(obj.getAttribute("hint")).length < 1) {
        return;
    }

    if (typeof (colorE) != "string" || $.trim(colorE).length < 1) {
        colorE = "#bfbebe";
    }

    if (typeof (colorN) != "string" || $.trim(colorN).length < 1) {
        colorE = "#000";
    }

    var input = $(obj);
    if (obj.type == "password") {
        input.attr("pw", "1");
    }
    else {
        input.attr("pw", "0");
    }
    var hint = input.attr("hint");
    if ($.trim(input.val()).length < 1) {
        input.val(hint);
        if (input.attr("pw") == "1") {
            obj.type = "text";
        }
    }
    input.css("color", colorE);
   
    input.focus(function () {
        var cbinput = $(this);
        if (cbinput.attr("hint") == $.trim(cbinput.val())) {
            cbinput.val("");
            cbinput.css("color", colorN);
            if (cbinput.attr("pw") == "1") {
                this.type = "password";
            }
        }
    });

    input.blur(function () {
        var cbinput = $(this);
        if (this.value.length < 1) {
            cbinput.val(cbinput.attr("hint"));
            cbinput.css("color", colorE);
            if (cbinput.attr("pw") == "1") {
                this.type = "text";
            }
        }
    });
}

///////////////////////////////////////////////////////////////////////////下面是和页面有关///////////////////////////////////////////////////////////////////////////

function CheckSearch() {
    try {
        var v = $("#rqSearchBox").val();
        if (!/[0-9a-z\u4E00-\u9FA5]/ig.test(v)) {
            Toast("请输入有效的查询内容！"); return false;
        }
        if (v.length > 100)
        { $("#q").val(v.substr(0, 100)); }
    } catch (e) { }
    return true;
}

function LayoutOptions() {
    var list;
    var mobj;
    var marr;
    var cw;
    var mroot = $(".measureRoot");
    var mrlength = mroot.size();
    var wsize = {
        "w": window.screen.width,
        "h": window.screen.height
    };
    var maxW = wsize.w - 50;
    for (var ml = 0; ml < mrlength; ml++) {
        cw = 0;
        marr = [];
        list = $(".whmeasure", mroot.eq(ml));
        mroot.removeClass("measureRoot");

        if (list.size() < 2) {
            continue;
        }

        for (var i = 0; i < list.size() ; i++) {
            mobj = { "p": list.eq(i).parent().parent(), "w": list.eq(i).width() };
            cw += mobj.w;
            marr.push(mobj);
            if (i > 0 && (marr[i].w + marr[i - 1].w) > maxW) {
                marr = [];
                break;
            }
        }
        //一行能显示完的
        //if (cw < maxW) {
        //    for (var i = 1; i < marr.length; i++) {
        //        marr[0].p.append(marr[i].p.children());
        //        marr[i].p.remove();
        //    }
        //    continue;
        //}

        //每一行显示2个
        for (var i = 1; i < marr.length; i++) {
            marr[i].p.children().appendTo(marr[i - 1].p);
            marr[i].p.remove();
            i++;
        }
    }
}

//显示试题
function ShowQues(id, s) {
    window.location.href = "./" + s + "/ques/detail/" + id;
}

$(function () {
    try {
        MathJye.LayOut(document.body);
        LayoutOptions();
    } catch (e) {alert(e.message) }
});