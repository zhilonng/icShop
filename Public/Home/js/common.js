$(function(){

	// 获取登录状态
	//state();

	$logBox = $('#toolbar .loginBox');
	$toolbarBox = $('#toolbarBox');
	$cartLayer = $('#cartLayer');
	
	// 头部购物车
	$cartLayer.mouseleave(function(e) {
		$(this).removeClass('hover');
	}).children('a').mouseenter(function(e) {
		$cartLayer.addClass('hover');
		ajax_cart_list();
	});

	// 选项卡效果
	$('.tabsNav').each(function(index, element) {
		$(this).find('li').mouseover(function(){
			$(this).addClass('cur').siblings().removeClass('cur');
			$(element).next().children().eq($(this).index()).addClass('cur').siblings().removeClass('cur');
		}).eq(0).mouseover();
	});
	

});

// 获取登录状态
/*
function state()
{
	$.get(USER_URL + '/Public/state', function(data){
		$('#stateBox').html(data);
	});

}
*/

// 显示登录框
function show_login()
{
	$logBox.show();
}
// 异步登录处理
function ajax_login(obj)
{
	if(Validator.Validate(obj, 3))
	{
		$.post(USER_URL + '/Public/login', $(obj).serialize(), function(data){
			if(data.id)
			{
				//state();
				// 替换头像
				$('#toolbarState').addClass('loged')
					.find('img').attr('src', data.thumb ? data.thumb : (_HOME_ + '/images/default_user.gif'));
				$logBox.hide();
				$('.ts').html('');
			}else
			{
				$('.ts').html(data.info);
				$('#yzimg').click(); // 更换验证码
			}
		}, 'json');
	}
	return false;
}

// 异步退出处理
function ajax_logout(obj)
{
	$.get(USER_URL + '/Public/logout', function(data){
		QC.Login.signOut();
		state();
		$('#toolbarState').removeClass('loged')
			.find('img').attr('src', _HOME_ + '/images/default_user.gif');	
	});
}

// 显示右边工具栏的购物车列表
function show_toolbarCart()
{
	if ($toolbarBox.css('right') == '-210px')
	{
		ajax_cart_list();
		$toolbarBox.animate({right : '35px' });
	}else
	{
		hide_toolbarCart();
	}
}

function hide_toolbarCart()
{
	$toolbarBox.animate({right : '-210px'});
}

//菜单列表的伸缩切换
function lam_shtree(obj)
{
	var $par = $(obj).parent();
	var lv = $par.attr('lv');
	
	var $objs =	$par.nextUntil('[lv='+lv+']').filter(function(){return $(this).attr('lv')>lv});
	if($objs.filter(':hidden').length)
	{
		$objs.show().find('i').html('-');
		$(obj).removeClass().html('-');
	}else
	{
		$objs.hide().find('i').html('+');
		$(obj).addClass('sma').html('+');
	}
}

// ajax加载购物车列表
function ajax_cart_list()
{
	$.get(HOME_URL + '/Cart/index', function(data){
		$('#toolbarCartList, #cartList section').html(data);
		$('.cartNumber').html($('.cgCnt').html()).show();
	});
}

// 移除某项购物车记录
/*
function remove_cart_item(obj, key)
{
	layer.confirm('您确定要移除该记录吗', {title:'询问', icon:3}, function(index){
		$.post(HOME_URL + '/Cart/del/key/' + key, function(data){
			$(obj).parent().parent().remove();
			layer.close(index);
		});	
	})	
}*/
