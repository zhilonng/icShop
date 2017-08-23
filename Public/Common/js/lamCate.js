/**
 * 无极分类
 *
 * @author 林坤源
 * @version 5.5.7 最后修改时间 2015年11月06日
 * @link http://www.lamsonphp.com
 * @param String box 下拉菜单所在的容器
 * @param String url 读取分类的服务端url
 * @param Number cacheid 数据缓存ID
 * @param String fname 域名的名称
 * @example
 * 需要依赖的资源：
	对象：
		jQuery, selectbox(可选)
	函数：
		U()
 * 注意：COM_IMG 目录下要有名为loading_spin.gif的图片
 *
 */
function LamCate(box, url, cacheid, fname)
{
	this.fname = fname || 'cid[]';	//域的名称
	this.opnArr = new Array();
	
	this.cacheid = !cacheid ? 0 : cacheid;	//cacheid: 数据缓存ID
	this.$box = $('#'+(box || 'catebox'));	//select所在的容器
	
	this.url = url || 'childOption';

	this.noid = 0;	//不取的ID
	this.fsted = 0; //是否已首次调用(通过该属性可控制在编辑页是否显示当前默认项，0为不显示，1为显示)
	
	this.urlArgs = {};	//其他的URL参数
	
	this.$fobj = this.$box.parents('form');//所在的表单
	this.$sbm = $(":submit", this.$fobj);//提交按钮
	
	this.afterCreate = this.afterEdit = null;	//全部编辑完成后和全部添加完成后的外调函数
	this.beauty = true;	//如果有selectobx美化组件，是否自动启用
	
	this.icon = typeof(IS_MOBILE)=='undefined' || IS_MOBILE ? '' : '--'; // 请选择前后的符号
}

/**
 * 添加页面的调用分类
 * @param object/number obj 当前动作的分类
 * @param number limit 级别限制
 * @param function chgfunc 下拉菜单onchang时的扩展响应函数
 */	
LamCate.prototype.create = function (obj, limit, chgfunc)
{
	var _this = this;	//在方法内部的回调函数内部还可以调用到本对象
	limit = limit || 0;
	pid = typeof(obj)=='object' ? obj.value : obj;
	if(pid==='')	//如果点了"请选择"，则把后面的select全部移除
	{
		$(obj).nextAll('select').remove();//移除后面的select	

		if(typeof($.selectbox)=='object')
		{
			$(obj).next('div').nextAll('.sbHolder').remove();
		}

		return false;	
	}else if(pid==0)	//首次调用
	{
		$('select', this.$box).remove();
	}
	
	this.setSbm(true);//设置提交按钮状态为不可用，防止加载过程中提交
	
	pid = pid || 0;	//	上级分类的ID
	var para = {obj:obj, pid:pid, limit:limit, chgfunc:chgfunc}
	//数组声明
	if(!this.opnArr[this.cacheid])
	{
		this.opnArr[this.cacheid] = [];
	}

	if(this.opnArr[this.cacheid][pid] === undefined)
	{
		var args = 'pid=' + pid + '&myid=' + this.noid + '&random=' + Math.random()  + '&level=' + (typeof(obj)=='object' ? $(obj).attr('level') : 0);
		
		for(var k in this.urlArgs)
		{
			args += '&' + k + '=' + this.urlArgs[k];
		}
		
		$.get(U(this.url, args), function(data){_this.backCreate(data, para);});
	}else
	{
		this.backCreate('', para);
	}
}

//添加页面的回调函数
LamCate.prototype.backCreate = function (rs, para)
{
	var _this = this;	//在方法内部的回调函数内部还可以调用到本对象
	//注意类的私有成员里面不能访问公有成员
	$(para.obj).nextAll('select').remove();//移除后面的select	
	
	if(typeof($.selectbox)=='object')
	{
		$(para.obj).next('div').nextAll('.sbHolder').remove();
	}
	if(rs)
	{
		this.opnArr[this.cacheid][para.pid] = rs;
	}
	
	if(this.opnArr[this.cacheid][para.pid])
	{
		var $s = $('<select name="' + this.fname + '"></select>');
		$s.attr('level', this.$box.children('select').length).html('<option value="">' + this.icon + '请选择' + this.icon + '</option>' + this.opnArr[this.cacheid][para.pid]).appendTo(this.$box);
		if(para.limit==0 || $s.prevAll('select').length<para.limit-1)
		{
			$s.change(function(){
				_this.create(this, para.limit, para.chgfunc);	
				if(typeof(para.chgfunc)=='function')
				{
					para.chgfunc(this);
				}
			});
		}else
		{
			this.endRec($s, true);
		}
	}
	typeof(this.afterCreate) == 'function' && this.afterCreate();
	
	typeof($.selectbox)=='object' && $s && this.beauty && $s.selectbox();
	
	this.setSbm(false);	
}

/**
 * 编辑页面的调用分类
 * @param number pid 上一级分类的ID
 * @param number myid 本身的ID
 * @param number limit 级别限制
 * @param function chgfunc 下拉菜单onchang时的扩展响应函数
 */
LamCate.prototype.edit = function (pid, myid, limit, chgfunc)
{
	var _this = this;	//在方法内部的回调函数内部还可以调用到本对象
	myid = ! myid ? '' : parseInt(myid);
	limit = ! limit ? 0 : limit;
	var args = 'isedit=1&pid='+pid;
	if(! this.fsted)
	{
		args += '&myid='+myid;//如果为首次执行，把myid传给PHP	
		this.noid = myid;	//并把不取的ID记下来
		this.fsted++;
	}
	
	for(var k in this.urlArgs)
	{
		args += '&' + k + '=' + this.urlArgs[k];
	}
	
	this.setSbm(true);
	//创建一个loading图像,并插在box里
	var $img = $('<img src="' + COM_IMG + 'loading_spin.gif" />');
	this.$box.prepend($img);
	var para = {pid:pid, myid:myid, $img:$img, limit:limit, chgfunc:chgfunc}
	window.setTimeout(
		function()
		{
			$.getJSON(U(_this.url, args), function(data){_this.backEdit(data, para);});
		},
		300
	);
}

//编辑页面的ajax的回调函数
LamCate.prototype.backEdit = function (json, para){
	var _this = this;	//在方法内部的回调函数内部还可以调用到本对象
	var $s;
	if(json)
	{
		this.$box.prepend($s = $('<select name="' + this.fname + '"><option value="">' + this.icon + '请选择' + this.icon + '</option>' + json.opn + '</select>'));
		$s.change(function(){
			_this.create( this, para.limit, para.chgfunc);
		});
		try{
			$s.find('[value='+ para.myid +']').prop('selected', true);
		}catch(e)
		{}
		if(typeof(json.ppid) != 'object')
		{
			 this.edit(json.ppid, para.pid, para.limit, para.chgfunc);	//递归
		}else
		{
			this.setSbm(false);
			this.noid && this.endRec($s = this.$box.children('select').last(), para.limit);
			typeof($.selectbox)=='object' && this.beauty && this.$box.children('select').selectbox();
		}
		if(json.ppid=='')
		{
			this.setSbm(false);
			typeof(this.afterEdit) == 'function' && this.afterEdit();
		}
	}
	para.$img.remove();
}
	
//结束递归
LamCate.prototype.endRec = function($obj, limit){
	try{
		if($obj.index()>=limit && limit){$obj.data('disabled', true).css('color', '#999').unbind('change').change(function (){this.selectedIndex = 0;});}
	}catch(e){}
}

//域的名称
LamCate.prototype.setFname = function (fname){
	fname && (this.fname = fname); return this;
}

LamCate.prototype.setAfterEdit = function (func){
	this.afterEdit = func; return this;
}

LamCate.prototype.setAfterCreate = function (func){
	this.afterCreate = func; return this;
}

//设置提交按钮的状态
LamCate.prototype.setSbm = function (val)
{
	this.$sbm.prop('disabled', val);
	return this;
}