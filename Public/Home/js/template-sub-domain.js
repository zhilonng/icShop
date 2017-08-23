(function (f) {
    var a = " .webcompatibleCon{ " +
            "   position: fixed; left:0; top:0; z-index: 8000; " +
            '   width: 100%; height: 100%; font-family: "Microsoft YaHei", "微软雅黑", "黑体"; ' +
            "    color: #747474; } " +
            ".webcompatibleCon *{ padding: 0; margin:0;} " +
            ".webcompatibleCon .webcompatibleBG{ " +
            "    position: absolute; left:0; top:0; width: 100%; height: 100%; _height:600px; *height:600px; " +
            "    opacity: 0.5; filter:alpha(opacity=50); background:#000; } " +
            ".webcompatibleCon .webcompatibleMain{ " +
            "    position: relative; background: #fff; " +
            "    width: 600px; height: 300px; " +
            "    border-radius: 3px;  font-size: 14px; " +
            "    top:200px; margin: 0 auto; } " +
            ".webcompatibleCon .colorBlack{color:#000; } " +
            ".webcompatibleCon .webcompatiblePa{padding:0 30px; } " +
            ".webcompatibleCon .webcompatibleTitle{ " +
            "    font-size: 14px; height:45px; line-height:45px; " +
            "    border-bottom:1px solid #DEDEDE; margin-bottom: 10px; } " +
            ".webcompatibleCon .tipClose{ " +
            "    position: absolute; right: 10px; top:8px; " +
            '    width: 26px; height: 26px; background:url("http://zgh07.nswyun.com/js/BrowserTip.png") no-repeat -7px -195px; } ' +
            ".webcompatibleCon .tipClose:hover{cursor: pointer;} " +
            ".webcompatibleCon .tip-part1{line-height: 40px; font-size: 16px;} " +
            ".webcompatibleCon .tip-part1 span{font-weight: bold;} " +
            ".webcompatibleCon .tip-part1 .redspan{color: #DE2626;} " +
            ".webcompatibleCon .tip-part2{text-align: center; margin-top: 40px; padding:0 15px;} " +
            ".webcompatibleCon ul li{list-style-type: none;float:left; width: 16%;} " +
            ".webcompatibleCon ul li p{color: #747474;} " +
            ".webcompatibleCon a{text-decoration:none;}" +
            '.webcompatibleCon .browser{ width: 56px; height: 58px; display: inline-block; margin:0 auto; background-image:url("http://zgh07.nswyun.com/js/BrowserTip.png"); background-repeat:no-repeat; }' +
            ".webcompatibleCon .browser-google{ background-position: -5px -20px; } " +
            ".webcompatibleCon .browser-firefox{ background-position: -99px -19px; }" +
            ".webcompatibleCon .browser-360{ background-position: -200px -22px; } " +
            ".webcompatibleCon .browser-3602{ background-position: -4px -104px; } " +
            ".webcompatibleCon .browser-sougou{ background-position: -102px -106px; } " +
            ".webcompatibleCon .browser-ie{ background-position: -199px -102px; } ";
    
    var j = '<div class="webcompatibleCon">' + '<div class="webcompatibleBG"></div>' + '<div class="webcompatibleMain">' + '<div class="webcompatibleTitle">' + '<p class="webcompatiblePa colorBlack">升级提示</p>' + '<div class="tipClose"></div>' + "</div>" + '<div class="webcompatiblePa colorBlack tip-part1">您正在使用的<span>IE版本太低!</span>' + '<span class = "redspan">请立即升级至IE9及以上内核</span></div>' + '<div class="webcompatiblePa browserup">本站采用html5，css3等新技术开发，已不再支持老旧的IE。我们建议您使用以下浏览器访问：</div>' + '<div class="tip-part2 webcompatiblePa">' + "<ul>" + '<li><a href="http://www.google.cn/chrome/browser/desktop/" target="_blank"><div class="browser browser-google"></div><p>谷歌浏览器</p></a></li>' + '<li><a href="http://www.firefox.com.cn/" target="_blank"><div class="browser browser-firefox"></div><p>火狐浏览器</p></a></li>' + '<li style="width:17.5%;"><a href="http://chrome.360.cn/" target="_blank"><div class="browser browser-360"></div><p>360极速浏览器</p></a></li>' + '<li style="width:17.5%;"><a href="http://se.360.cn/" target="_blank"><div class="browser browser-3602"></div><p>360安全浏览器</p></a></li>' + '<li><a href="http://ie.sogou.com/index.html" target="_blank"><div class="browser browser-sougou"></div><p>搜狗浏览器</p></a></li>' + '<li><a href="https://www.microsoft.com/zh-cn/download/" target="_blank"><div class="browser browser-ie"></div><p>IE浏览器</p></a></li>' + "</ul>" + "</div>" + "</div>" + "</div>";
    var l = window.document.head || window.document.getElementsByTagName("head")[0];
    var m = {
        isAddEventListener: typeof window.document.addEventListener != "undefined",
        isAttachEvent: typeof window.document.attachEvent != "undefined",
        isInnerText: "innerText" in l,
        isInnerHTML: "innerHTML" in l,
        addEvent: function (q, p, o) {
            if (m.isAddEventListener) {
                q.addEventListener(p, o, false)
            } else {
                if (m.isAttachEvent) {
                    q.attachEvent("on" + p, o)
                }
            }
        },
        removeEvent: function (q, p, o) {
            if (m.isAddEventListener) {
                q.removeEventListener(p, o, false)
            } else {
                if (m.isAttachEvent) {
                    q.detachEvent("on" + p, o)
                }
            }
        }
    };
    var n = [], k = false, c = false;

    function i(p) {
        var o = p.shift();
        while (o) {
            o();
            o = p.shift()
        }
    }

    function b() {
        if (m.isAddEventListener) {
            window.document.removeEventListener("DOMContentLoaded", b);
            k = true;
            i(n)
        }
        if (m.isAttachEvent) {
            if (window.document.readyState === "complete") {
                window.document.detachEvent("onreadystatechange", b);
                k = true;
                i(n)
            }
        }
    }

    var e = f || function (o) {
            if (typeof o !== "function") {
                return
            }
            n.push(o);
            if (k) {
                i(n)
            } else {
                if (!c) {
                    if (m.isAddEventListener) {
                        window.document.addEventListener("DOMContentLoaded", b)
                    } else {
                        if (m.isAttachEvent) {
                            window.document.attachEvent("onreadystatechange", b)
                        }
                    }
                    c = true
                }
            }
        };

    function h() {
        var o = null;
        if (document.createStyleSheet) {
            o = document.createStyleSheet();
            o.cssText = a
        } else {
            o = document.createElement("style");
            if (o.setAttribute) {
                o.setAttribute("type", "text/css")
            } else {
                o.attributes.type = "text/css"
            }
            o.innerHTML = o.innerText = a;
            l.appendChild(o)
        }
    }

    function g() {
        var p = window.document.createElement("div");
        p.innerHTML = j;
        var o = window.document.body;
        o.insertBefore(p, o.firstChild)
    }

    function d() {
        var q = document.body.children || document.body.childNodes;
        var p = 0, o = q.length, t, r;
        for (p; p < o; p++) {
            t = q[p];
            if (t.tagName.toUpperCase() == "DIV") {
                r = t.firstChild;
                if (r && r.tagName.toUpperCase() === "DIV" && /webcompatibleCon/ig.test(r.className)) {
                    break
                }
            }
        }
        r = r.lastChild.firstChild;
        var s = r.lastChild;
        m.addEvent(s, "click", function () {
            document.body.removeChild(t)
        })
    }
    e(function () {
        var q = window.navigator.userAgent;
        var o = /msie|trident/ig;
        var p = o.test(q);
        var t = /msie 6/i.test(q);
        var s = /msie 7/i.test(q);
        var r = /msie 8/i.test(q);
        if (r || t || s) {
            h();
            g();
            d()
        }
    })
})(window.$ || window.jQuery);

/**
 * Created by jiangw on 2017/2/20.
 * 分站信息替换（发布后的页面生效）
 * subName - 分站名称
 * subDomain - 分站地址
 * fullCustomerName - 公司名称
 * fixedPhoneNum - 固定电话
 * phone400 - 400电话
 * fax - 传真
 * address - 地址
 * contactName - 联系人姓名
 * contactPhoneNumber - 联系人电话
 * contactEmail - 联系电子邮件
 * qq - QQ号码
 */
(function () {
    "use strict";
    var subDomain = {};
    subDomain.init = function init(type) {
        var scope = this;
        if (/(\/pccms\/js\/template)/.test(top.window.location.pathname)) {
            return;
        }

        if (!window.$ && !window.jQuery) {
            scope.parallelLoadScripts("//cdn.bootcss.com/jquery/1.11.3/jquery.min.js", function () {
                scope.loadTypeParams(type);
            });
        } else {
            scope.loadTypeParams(type);
        }
    };
    /**
     * 用于区分当前访问的设备类型
     * @param type - 固定类型(可选)
     */
    subDomain.loadTypeParams = function loadTypeParams(type) {
        var scope = this,
            params = {};

        var baseUrl = $('[data-base]').attr('data-base') || '/';
        params.url = 'http://' + window.location.host + baseUrl + 'subDomain.js';

        params.type = type ? String(type) : '' || scope.isPhone() ? '5' : '4';
        switch (params.type) {
            case "4":
                params.subBefore = new RegExp('^(http:\/\/)*(' + location.host + ')\/*$');
                params.subAfter = "subDomain";
                break;
            case "5":
                params.subBefore = new RegExp('^(' + scope.getStorage() + ')$');
                params.subAfter = "contactPhoneNumber";
                break;
        }

        scope.filterData(params.type, params.url, params.subBefore, params.subAfter);
    };
    /**
     * 请求并筛选主站和分站信息
     * @param type - 设备类型
     * @param url - subDomain.js的url地址
     * @param subBefore - 判断是否分站的依据
     * @param subAfter - 判断是否分站的依据
     */
    subDomain.filterData = function filterData(type, url, subBefore, subAfter) {
        var scope = this;
        var ajax = $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            cache: false
        });
        ajax.done(function (data) {
            var domainArray = [{}, []];
            $.each(data, function (i, item) {
                if (item.isMain) {
                    domainArray[0] = item;
                }
                if (item.projType === type) {
                    domainArray[1].push(item);
                }
            });
            // 主站匹配
            // scope.fillContent(domainArray[0]);
            scope.filterDomain(domainArray[1], subBefore, subAfter);
        });
        ajax.fail(function () {
            console.log('当前项目没有分站信息');
        });
    };
    /**
     * 筛选满足的分站信息并启动替换
     * @param data - 分站列表
     * @param subBefore - 判断是否分站的依据
     * @param subAfter - 判断是否分站的依据
     */
    subDomain.filterDomain = function filterDomain(data, subBefore, subAfter) {
        var scope = this;
        $.each(data, function (i, item) {
            if (subBefore.test(item[subAfter])) {
                scope.fillContent(item);
            }
        });
    };
    /**
     * 获取页面对应对象进行替换
     * @param domainData - 分站信息
     */
    subDomain.fillContent = function fillContent(domainData) {
        var scope = this;
        if (domainData) {
            var attr = [
                "subName",
                "subDomain",
                "fullCustomerName",
                "fixedPhoneNum",
                "phone400",
                "fax",
                "address",
                "contactName",
                "contactPhoneNumber",
                "contactEmail",
                "qq"
            ];
            $.each(attr, function (index, value) {
                var el = $("[" + value + "]");
                $.each(el, function (indexChild, elChild) {
                    if (!$(elChild).attr(value)) {
                        scope.autoSetContent($(elChild), value, domainData[value]);
                    } else {
                        scope.userDefinedContent($(elChild), value, domainData[value]);
                    }
                });
            });
        } else {
            localStorage.removeItem("nswtel");
        }
    };
    subDomain.autoSetContent = function autoSetContent(el, key, content) {
        switch (key) {
            case "contactPhoneNumber":
                el.attr("href", "tel:" + content);
                break;
            case "contactEmail":
                el.attr("href", "mailto:" + content);
                break;
            case "qq":
                el.attr("href", "http://wpa.qq.com/msgrd?v=3&uin=" + content + "&site=qq&menu=yes");
                break;
            case "phone400":
                el.attr("href", "tel:" + content);
                break;
        }
        this.setDomText(el, content);
    };
    /**
     * 判断文本内容并替换
     * @param el - dom对象
     * @param text - 内容
     */
    subDomain.setDomText = function setDomText(el, text) {
        if (el[0].children.length > 0) {
            return;
        }
        $.map(el[0].childNodes, function (item) {
            if (item.nodeType === 3) {
                el.text(text);
            }
        });
    };
    /**
     * 自定义规则内容替换
     * @param el - dom对象
     * @param key - 分站索引字段
     * @param content - 分站内容
     */
    subDomain.userDefinedContent = function userDefinedContent(el, key, content) {
        var attrNames = el.attr(key).split(',');
        $.each(attrNames, function (index, attrName) {
            var attrContent = (el.attr('sd-' + attrName) || '').replace(/(\$\$)/g, content) || content;
            if (attrName === 'text') {
                el.text(attrContent);
            } else {
                el.attr(attrName, attrContent);
            }
            el.removeAttr('sd-' + attrName);
        });
    };
    /**
     * 获取localStorage的tel数据
     */
    subDomain.getStorage = function getStorage() {
        var param = this.parseUrl(location.href);
        if (param.tel) {
            param = param.tel;
            localStorage.nswtel = param;
        } else {
            if (localStorage.nswtel) {
                param = localStorage.nswtel;
            } else {
                param = "";
            }
        }
        return param;
    };
    /**
     * 判断当前是否移动设备
     * @returns {boolean}
     */
    subDomain.isPhone = function isPhone() {
        var isPhone = false;
        var ua = navigator.userAgent;
        var ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
            isIphone = !ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
            isAndroid = ua.match(/(Android)\s+([\d.]+)/),
            isMobile = isIphone || isAndroid;
        if (isMobile) {
            isPhone = true;
        }
        return isPhone;
    };
    /**
     * 转换网页地址的参数为JSON
     * @param url - 网页地址
     * @returns {{}} - 参数的JSON对象
     */
    subDomain.parseUrl = function parseUrl(url) {
        var reg_url = /^[^\?]+\?([\w\W]+)$/,
            reg_para = /([^&=]+)=([\w\W]*?)(&|$|#)/g,
            arr_url = reg_url.exec(url),
            ret = {};
        if (arr_url && arr_url[1]) {
            var str_para = arr_url[1],
                result;
            while ((result = reg_para.exec(str_para)) !== null) {
                ret[result[1]] = result[2];
            }
        }
        return ret;
    };
    /**
     * 动态添加js文件
     * @param scripts - js地址列表
     * @param callback - 回调方法
     */
    subDomain.parallelLoadScripts = function parallelLoadScripts(scripts, callback) {
        if (typeof(scripts) !== "object") {
            scripts = [scripts];
        }
        var HEAD = document.getElementsByTagName("head").item(0) || document.documentElement,
            s = [],
            loaded = 0;
        for (var i = 0; i < scripts.length; i++) {
            s[i] = document.createElement("script");
            s[i].setAttribute("type", "text/javascript");
            s[i].onload = s[i].onreadystatechange = function () {
                if (!/*@cc_on!@*/0 || this.readyState === "loaded" || this.readyState === "complete") {
                    loaded++;
                    this.onload = this.onreadystatechange = null;
                    this.parentNode.removeChild(this);
                    if (loaded === scripts.length && typeof(callback) === "function") {
                        callback();
                    }
                }
            };
            s[i].setAttribute("src", scripts[i]);
            HEAD.appendChild(s[i]);
        }
    };
    subDomain.init();
})();