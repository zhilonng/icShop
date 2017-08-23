<?php
namespace Admin\Controller;
use Think\Controller;
class ComputeController extends CommonController{
	public function gethz(){
			$sex = "男";
			$sex = "男";
			$money = "5万元";
			$money2 = "2.5万元";
			$goage = "至60周岁";
			$json_str = '"{"productId":1644,"productPlanId":1934,"genes":[{"sort":0,"protectItemId":756,"key":"","value":"'.$money.'"},{"sort":-1,"protectItemId":767,"key":"","value":"'.$money2.'"},{"sort":1,"protectItemId":"","key":"paymentType","value":"年交"},{"sort":2,"protectItemId":"","key":"insurantDateLimit","value":"'.$goage.'"},{"sort":3,"protectItemId":"","key":"insureAgeLimit","value":"3年"},{"sort":3,"protectItemId":"","key":"sex","value":"'.$age.'"},{"sort":5,"protectItemId":"","key":"insurantDate","value":"2017-03-15"},{"sort":6,"protectItemId":"","key":"premiumExemption","value":"含"},{"sort":7,"protectItemId":"","key":"additionalRiskPeriod","value":"2年"},{"sort":8,"protectItemId":"","key":"insurantJob","value":"1-4类"}],"optGeneOldValue":{"sort":3,"protectItemId":"","key":"sex","value":"男"}}"';
			$url='http://cps.hzins.com/v2/product/tryTrial?uid=556319&prodId=1644&planId=1934&restrictGeneParams='.urlencode($json_str).'&_=1492013323282';
			echo $url;
			$html = file_get_contents($url);
			echo $html;
			$this->display();
		
	}

	
}

?>