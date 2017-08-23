var $header = $('header'), $aside = $('aside'), $main = $("#main"), $menuBox = $("#menuBox"), $mainWin = $('#mainWin'), $dt, $dfn = $('dfn'), $nu = $('nav ul'), $swiBox = $('#swiBox'), $breadCrumb = $('#breadCrumb'), $more = $('#more');

$swiBox.mouseleave(function(){
	$(this).hide('slow');
});

//模拟事件的发生
$(window).resize(function(){
	$nu.width(document.documentElement.clientWidth - $('.logo').outerWidth(true));
	var wlen = $nu.width() - 120;
	$mainWin
		.height(document.documentElement.clientHeight - $header.outerHeight() - $breadCrumb.outerHeight() - LamClient.adjust.num.y);
	$aside.height( document.documentElement.clientHeight - $header.outerHeight() );
	$swiBox.hide();
	
	//自适应导航栏
	$more.prependTo($swiBox);
	$swiBox.children(':gt(0)').appendTo($nu);
	while( sum_length() >= wlen )
	{
		$nu.children(':last').prependTo($swiBox);
	}
	if( $swiBox.children().length>1 )
	{
		$more.appendTo( $nu );
	}
});

$(function(){
	//引起二级菜单的伸缩
	$(document).on('click', '#menuBox dt', function(){
		$(this).next().slideToggle('fast');
	});
	
	//设置顶级菜单第一项处于选中状态
	$nu.children("li:first").addClass('current').children('a').click();
	
	$dfn.attr('title','隐藏左菜单').click(function(){
		$aside.stop(true);
		$main.stop(true);
		if(this.title=='显示左菜单'){show_aside();}
		else{hide_aside();}
		return false;
	});
	
	$(window).resize();
	//iframe的onload事件
	$mainWin.load(set_my_tit);
});

//计算$nu里面的li的长度之和
function sum_length()
{
	var len = 0;
	$nu.children().each(function(index, element) {
		len += $(element).outerWidth(true);
	});
	return len;
}

function show_more(obj)
{
	$swiBox.css('left', $('#more').offset().left-24).show('slow');
}

function set_my_tit()
{
	try{
		//注意一定要第一个i标签
		var tit = frames['mainWin'].document.getElementsByTagName('i')[0].innerHTML;
		if(tit){$('#curpstion').html(tit);}
	}catch(e){}
}

//显示左边栏
function show_aside()
{
	$main.animate({'margin-left':212}, 260);
	$aside.animate({width:212}, 300, function(){
		$menuBox.add('.actnav').fadeIn(320);
		$dfn.removeClass().attr('title','隐藏左菜单');
	});
}

//收起左边栏
function hide_aside()
{
	$menuBox.add('.actnav').fadeOut(150);
	$aside.animate({width:30}, 320); 
	$dfn.addClass('dfn').attr('title','显示左菜单');
	$main.animate({'margin-left':30}, 420);
}

//获取左边的菜单
function get_left_menu(pid, myobj)
{
	$dfn.hasClass('dfn') && $dfn.trigger('click');
	$(myobj).parent().addClass('current').siblings().removeClass('current');
	show_loading($menuBox, '', IMG + 'loading_spin.gif');
	window.setTimeout(function(){
		$menuBox.load(U('Node/ajaxLeft', 'pid='+pid+'&'+Math.random()));
	}, 800);
}