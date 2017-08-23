/**
 * lamChange 2.0
 * upgrade by lamson 2016-08-23
 * 作者：林坤源  2016-08-23
 * http://www.lamson.cc
 * E-mail:171004297@qq.com
 */
;(function($){
	$.fn.extend({
		"lamChange": function(o, lang){

			o= $.extend({
				wrapObj:null,//容器对象，默认为调用对象的父容器 add by lamson
				thumbObj:null,//导航对象
				btnPrev:null,//按钮上一个
				btnNext:null,//按钮下一个
				changeType:'fade',//切换方式，可选：fade,slide,horizontal，默认为fade
				offsetPos:0,//changeType为horizontal时的定位偏移量 add by lamson
				thumbNowClass:'now',//导航对象当前的class,默认为now
				thumbOverEvent:true,//鼠标经过thumbObj时是否切换对象，默认为true
				thumbClickEvent:true,//鼠标点击thumbObj时是否切换对象，默认为true
				slideTime:1000,//平滑过渡时间，默认为1000ms，为0或负值时，忽略changeType方式，切换效果为直接显示隐藏
				autoChange:true,//是否自动切换，默认为true
				clickFalse:true,//导航对象点击是否链接无效，默认是return false链接无效，当thumbOverEvent为false时，此项必须为true，否则鼠标点击事件冲突
				overStop:true,//鼠标经过切换对象时，是否停止切换，并于鼠标离开后重启自动切换，前提是已开启自动切换
				changeTime:5000,//自动切换时间
				delayTime:300,//鼠标经过时对象切换迟滞时间，推荐值为300ms
				initFun:null, //对象初始化后的回调函数 add by lamson
				finishFun:null, //切换后的回调函数 add by lamson
				//语言包，可自由扩展
				lang : {
					"zh-cn":{
						prev:"上一个",
						next:'下一个'
					},
					"zh-tw":{
						prev:"上一個",
						next:'下一個'
					},
					"en-us":{
						prev:"prev",
						next:'next'
					},
					"ko-kr":{
						prev:"이전",
						next:'다음'
					},
					"km-km":{
						prev:"មុន",
						next:'បន្ទាប់'
					}
				}
			}, o || {});

			var $self = $(this);
			var $wrapObj = o.wrapObj ? $(o.wrapObj) : $self.parent().parent();
			var thumbObj;
			var size = $self.size();
			var nowIndex =0; //定义全局指针
			var index;//定义全局指针
			var startRun;//预定义自动运行参数
			var delayRun;//预定义延迟运行参数
			var obt = 'next';//add by lamson

			//初始化
			var $iniObj = $self.hide().eq(0);
			var width = $iniObj.show().width();
			var left = $iniObj.css('left');
			$self.parent().height($iniObj.height());
			var $thumbObj, $btnPrev, $btnNext;
			
			//语言包
			var _lang = o.lang[typeof(lang)!='undefined' ? lang : (typeof (LamSon) == 'object' ? LamSon.lang : 'zh-cn')];
			
			if( ! o.thumbObj)
			{
				var html = '<ul class="thumbBox">';
				for(var i=0; i<size; i++)
				{
					html += '<li>' + (i+1) + '</li>';
				}
				html += '</ul>';
				$wrapObj.append(html);
				html = null;
				$thumbObj = $wrapObj.find('.thumbBox li');
			}else
			{
				$thumbObj = $(o.thumbObj);
			}
			
			if( ! o.btnPrev || ! o.btnNext)
			{
				$wrapObj.append('<div class="btnBox"><a href="javascript:void(0);" class="btnPrev" title="' + _lang.prev + '">' + _lang.prev + '</a><a href="javascript:void(0);" class="btnNext" title="' + _lang.next + '">' + _lang.next + '</a></div>');
				$btnPrev = $wrapObj.find('.btnPrev');
				$btnNext = $wrapObj.find('.btnNext');
			}
			else
			{
				$btnPrev = $(o.btnPrev);
				$btnNext = $(o.btnNext);
			}
			
			//点击任一图片
			if ($thumbObj.length) {		
				//初始化thumbObj
				$thumbObj.removeClass(o.thumbNowClass).eq(0).addClass(o.thumbNowClass);
				if (o.thumbClickEvent) {
					$thumbObj.click(function () {
						index = $thumbObj.index($(this));
						fadeAB();
						if (o.clickFalse) {return false;}
					});
				}
				if (o.thumbOverEvent) {
					$thumbObj.hover(function () {//去除jquery1.2.6不支持的mouseenter方法
						index = $thumbObj.index($(this));
						delayRun = setTimeout(fadeAB, o.delayTime);
					},function () {
						clearTimeout(delayRun);
					});
				}
			}

			//点击下一个
			if ($btnNext.length) {
				$btnNext.click(function () {
					if($self.queue().length<1){runNext();}
					return false;
				});
			}

			//点击上一个
			if ($btnPrev.length) {
				$btnPrev.click(function () {
					obt = 'prev';
					if($self.queue().length<1){
						index = (nowIndex+size-1)%size;
						fadeAB();
					}
					return false;
				});
			}
			
			if(typeof(o.initFun) == 'function')
			{
				o.initFun(o, $iniObj);	
			}

			//自动运行
			if (o.autoChange) {
				startRun = setInterval(runNext,o.changeTime);
				if (o.overStop) {
					$wrapObj.mouseenter(function (){
						clearInterval(startRun);//暂停自动切换函数
					});
					
					$wrapObj.mouseleave(function () {
						startRun = setInterval(runNext,o.changeTime);//重启用自动切换函数
					});
				}
			}
			
			// 支持触屏 add by lamson
			if("ontouchend" in document && typeof(LamTouch)=='function')
			{
				LamTouch($wrapObj, {left:function(){$btnNext.click();}, right:function(){$btnPrev.click();}});
			}
			
			//主切换函数
			function fadeAB () {
				
				// add by lamson
				/*try{
					clearInterval(startRun);
				}catch(e){}*/
				
				if (nowIndex != index) {
					$self.eq(nowIndex).stop();
					$self.eq(index).stop();
						
					if ($thumbObj.length) {
						$thumbObj.removeClass(o.thumbNowClass).eq(index).addClass(o.thumbNowClass);
					}
					if (o.slideTime <= 0) {
						$self.eq(nowIndex).hide();
						$self.eq(index).show();
					}else if(o.changeType=='fade'){
						$self.eq(nowIndex).fadeOut(o.slideTime);
						$self.eq(index).fadeIn(o.slideTime);
					}
					//add by lamson
					else if(o.changeType == 'horizontal')
					{
						if(obt == 'next' || (obt=='' && nowIndex<index))
						{
							var _lft = -width - (o.offsetPos || parseFloat(left));
							var _intpos = width;
						}
						else if(obt == 'prev' || (obt=='' && nowIndex>index))
						{
							var _lft = width + 2*(o.offsetPos || parseFloat(left));
							var _intpos = -width;
						}
						
						$self.eq(nowIndex).animate({ 
							left:_lft
						  }, o.slideTime );

						$self.eq(index).css('left', _intpos).show().animate({ 
							left:left
						  }, o.slideTime );
						
						obt = '';
					}
					else{
						$self.eq(nowIndex).slideUp(o.slideTime, 'swing');
						$self.eq(index).slideDown(o.slideTime, 'swing');
					}
					nowIndex = index;
					
					if(typeof(o.finishFun) == 'function')
					{
						o.finishFun(o, index);	
					}
					
					/*if(o.autoChange)
					{
						startRun = setInterval(runNext,o.changeTime);
					}*/
				}
			}

			//切换到下一个
			function runNext() {
				obt = 'next';
				index =  (nowIndex+1)%size;
				fadeAB();
			}
		}
	})

})(jQuery);