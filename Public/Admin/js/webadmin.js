/**
 * 后台基本类(精简版)
 * 
 * @author 林坤源
 * @version 5.5.5 最后修改时间 2016年09月19日
 * @link http://www.lamson.cc
 */

$(document).ready(function(e) {

	//重置按钮
	$('button#rst').addClass('btnSec').click(function(){location.reload();});
	//调节没有表单搜索的列表页
	if($('#listBox').length && ! $('#searchForm').length){$('#listBox').addClass('listNoForm');}
	
	//表格中的选项卡
	$(document).on('click', '#opnTab caption b', function(){
		var idx = $(this).index();
		$(this).addClass('cur').siblings().removeClass('cur');
		if($('#opnTabList').length)
		{
			$('#opnTabList').children().eq(idx).show().siblings().hide();
		}else
		{
			$(this).parents('table').children('tbody').eq(idx).show().siblings('tbody').hide();	
		}
	});
	$('#opnTab caption b').eq(0).trigger('click');
	
});

//显示原来的缩略图
function show_thumb($thumbImg, del, lang)
{
	var _this = this;	//让私有方法可以调用公有属性和公有方法
	//语言包，可自由扩展
	this.lang = {
		"zh-cn":{
			dblclick:"双击可删除此图片",
			delsure:"您确定要删除此图片？"
		},
		"zh-tw":{
			dblclick:"雙擊可刪除此圖片",
			delsure:"您確定要刪除此圖片？"
		},
		"en-us":{
			dblclick:"Double-click to delete this image",
			delsure:"Are you sure you want to delete this image?"
		},
		"ko-kr":{
			dblclick:"이 이미지를 삭제하려면 두 번 클릭",
			delsure:"이 이미지를 삭제 하시겠습니까?"
		},
		"km-km":{
			dblclick:"ចុចទ្វេដងដើម្បីលុបរូបភាពនេះ",
			delsure:"តើអ្នកពិតជាចង់លុបរូបភាពនេះ?"
		}
	};
	var _lang = this.lang[typeof(lang)!='undefined' ? lang : (typeof (LamSon) == 'object' ? LamSon.lang : 'zh-cn')];
	
	var $lamFileBox = $('.lamFileBox[thumb="' + $thumbImg.attr('thumb') + '"]');
	var $_thumb = $lamFileBox.find('[type="hidden"]');
	var _rootpath = $_thumb[0].form['_rootpath'].value;

	if( $_thumb.val() && ! $thumbImg.next('.autoGrid').length )
	{
		var t = new Image();
		t.src = $_thumb.val().indexOf('http://') != -1 ? $_thumb.val() : (_rootpath + '/' +  $_thumb.val());
		$(t).css({'margin-left':10, width:$thumbImg.width(), height:$thumbImg.height(), maxHeight:$thumbImg.css('max-height')});
		if(del)
		{
			$(t).attr({title:_lang.dblclick});
			t.ondblclick = function (event){
				//删除原有的缩略图
				if(confirm(_lang.delsure))
				{
					$.get(U('delFile'),'id='+$_thumb.data('pkval') + '&thumb=' + encodeURIComponent ($_thumb.val()) + '&field=' + $lamFileBox.find('[type="file"]')[0].name + '&_rootpath=' + _rootpath, function(data){
						$_thumb.val('');
						$(t).remove();
						unmask();
					});	
				}
			}
		}
		$thumbImg.after(t);
		$(t).wrap('<span class="autoGrid"></span>');
	}
}