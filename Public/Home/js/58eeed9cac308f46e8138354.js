
    function addFavorite2() {
           var url = window.location;
           var title = document.title;
           var ua = navigator.userAgent.toLowerCase();
           if (ua.indexOf("360se") > -1) {
               alert("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！");
           } else if (ua.indexOf("msie 8") > -1) {
               try {
                    window.external.AddToFavoritesBar(url, title); //IE8
               } catch (e) {
                    alert("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！");
               }
           } else if (document.all) {
               try {
                   window.external.addFavorite(url, title);
               } catch (e) {
                   alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
               }
           } else if (window.sidebar) {
               window.sidebar.addPanel(title, url, "");
           } else {
               alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
           }
       }  


        $(".banner").slide({
            mainCell: ".bd",
            autoPlay: true,
            effect: "fold"
        });
    

    function search() {
        var key = document.getElementById('key').value;
        location.href = "search.php?key=" + key;
    }


        $(".profen").slide({
            titOnClassName: 'cur',
            titCell: ".proleft li",
            mainCell: ".fentab",
            autoPlay: false,
            effect: "fold"
        });
    

               $(".caselist").slide({ titOnClassName: 'cur', titCell: "ul.caselistpp li", mainCell: ".fal", autoPlay: false, effect: "fold" });
             
		

        $(".zxcolumns").slide({
            titOnClassName: 'cur',
            titCell: ".ulzx li",
            mainCell: ".bdxx",
            autoPlay: false,
            effect: "fold"
        });
    

    $(".xcshow").slide({
        titOnClassName: 'cur',
        titCell: ".qs li",
        mainCell: ".dy1",
        autoPlay: false,
        effect: "fold"
    });


        $(function() {
            $('#intentionalOrderFormId').nsw({
                btnCell: '.submit',
                row: '.findrow li',
                hasId: true, 
                errorModal:true,
                lyFields: JSON.parse($('#fieldsJson').html()),
                formInfo: JSON.parse($('#formInfo').html())
            });
        })
    

        $(function() {
            var time;
            //var winHeight = top.window.document.body.clientHeight || $(window.parent).height();
            $('.client-2').css({
                'marginTop': -($('.client-2').height() / 2)
            });
            $('#client-2 li').on({
                'mouseenter': function() {
                    var scope = this;
                    time = setTimeout(function() {
                        var divDom = $(scope).children('div');
                        var maxWidth = divDom.width();
                        $(scope).stop().animate({
                            marginLeft: -maxWidth + 77,
                            width: maxWidth
                        }, 'normal', function() {
                            var pic = $(scope).find('.my-kefu-weixin-pic');
                            if (pic.length > 0) {
                                pic.show();
                            }
                        });
                    }, 100) 
},
                'mouseleave': function() {
                    var pic = $(this).find('.my-kefu-weixin-pic');
                    if (pic.length > 0) {
                        pic.hide();
                    }
                    clearTimeout(time);
                    var divDom = $(this).children('div');
                    $(this).stop().animate({
                        marginLeft: 0,
                        width: 77
                    }, "normal", function() { 
});
                }
            });
            //返回顶部
            $(window).scroll(function() {
                var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;
                var eltop = $("#client-2").find(".my-kefu-ftop");
                if (scrollTop > 0) {
                    eltop.show();
                } else {
                    eltop.hide();
                }
            });
            $("#client-2").find(".my-kefu-ftop").click(function() {
                var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;
                if (scrollTop > 0) {
                    $("html,body").animate({
                        scrollTop: 0
                    }, "slow");
                }
            }); 
});
    

      
      $(".menu").find("li").eq(0).addClass("cur");
  
