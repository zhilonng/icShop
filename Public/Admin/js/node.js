function delNode(id){
	layer.confirm('您确定要删除这条信息吗', {icon: 0, title:'提示'}, function(){

	  	layer.msg('的确很重要', {icon: 1});
	},function(index){
		layer.close(index);
	});
}