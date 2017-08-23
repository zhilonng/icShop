$(function(){
	$('.addProduct').each(function(i){
		$(this).click(function(){
			$.post(reportUrl,{'rid':$(this).attr('data-rid')},function(data){
				$('.product-form').eq(i).append(data);
			},'html');
		})
	})
})

function delProduct(obj, id){
	if(id == 0){
		$(obj).parents('.r_product').remove();
	}else{
		layer.confirm('您确定要删除这个产品吗', {icon: 0, title:'提示'}, function(){
			$.post(delProductUrl,{'id':id},function(data){
				if(data.status){
					layer.msg('删除成功', {icon: 1});
					$(obj).parents('.r_product').remove();
				}else{
					layer.msg('删除失败', {icon: 2});
				}
			},'json')
		},function(index){
			layer.close(index);
		});		
	}
}
//function changeAll(op){
//    var id={
//        id:op.value
//    };
//    $.ajax({
//        url:changeAllUrl,
//        data:id,
//        success:function(data){
//            if(data.status==1){
//                $("#fam-conf").val(data.family);
//                $("#f-conf").val(data.father);
//                $("#m-conf").val(data.mother);
//                $("#b-conf").val(data.baby);
//                $(".r_product").remove();
//                var f_p=JSON.parse(data.f_p);
//                alert(f_p[0]);
//            }
//        }
//    })
//}