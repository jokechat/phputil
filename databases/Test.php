<?php
require "ActiveRecord.php";
 	$arModel 		= new ActiveRecordModel();

// 		$where 		= ['belong'=>'gh_59fea3718abf','nickname'=>'jokechat'];
// 		$result 	= $arModel->where($where)->get("userinfo",[10,10]);
// 		$result 	= $arModel->where('nickname','jokechat1111','=','AND')->where('nickname','%坏孩子111%','like','OR')->get("userinfo");

		//查询一条记录
// 		$result 	= $arModel->where('nickname','jokechat1234')->getOne("userinfo");


// 		//使用预编译原始sql查询
// 		$query 		= "select * from userinfo where nickname = ? or nickname = ?";
// 		$bindParams = ['jokechat','坏孩子'];
// 		$result 	= $arModel->rawQuery($query,$bindParams);

//query
// 		$query 		= "select * from userinfo ";
// 		$result 	= $arModel->query($query,[10,10]);


// 		$result 	= $arModel->getValue("userinfo", 'nickname',10);


		//插入数据
// 		$insertData = ['keyword'=>"hello",'msg'=>'测试消息'];
// 		$result 	= $arModel->insert("wechat_keyword", $insertData);

//replace
// 		$insertData = ['keyword'=>"hello",'msg'=>'测试消息454545','id'=>9];
// 		$result 	= $arModel->replace("wechat_keyword", $insertData);

		//更新数据
// 		$tableData 	= ['keyword'=>"hello",'msg'=>'测试消息454545'];
// 		$result 	= $arModel->where('id',3)->update("wechat_keyword", $tableData);

		//删除数据
// 		$result 	= $arModel->where('id',3)->delete("wechat_keyword");


		// orWhere 条件
// 		$result 	= $arModel->orWhere('nickname','jokechat')->orWhere('nickname','huaihaizi')->get('userinfo');



		//join 条件查询
// 		$result 	= $arModel->join('userinfo uinfo', "uinfo.userid=umoney.userid","LEFT")
// 							  ->where('umoney.userid','11467#luhwcv')
// 							  ->get('user_money_total umoney',null,"uinfo.nickname,uinfo.userid,umoney.*");


		//orderBy
// 		$result 	= $arModel->where('belong','gh_59fea3718abf')->orderBy('id',"DESC")->get("userinfo",[0,20]);


		//groupBy  分组查询
// 		$result 	= $arModel->groupBy("belong")->get("userinfo",null,'count(*) as count,belong');

		//having 分组查询
// 		$result 	= $arModel->having('belong','gh_7e5ce1bd594b')->get("userinfo",null,'count(*) as count,belong');

		// in,not in,between,not between,like



		//开启调试模式
		$arModel->setTrace(true);
// 		$result 	= $arModel->where('id',[1,1000],'not between')->get("userinfo");
// 		$result 	= $arModel->where('id',[1,1000],'not between')->get("userinfo");
// 		$result 	= $arModel->where('id',[100,200],'not in')->get("userinfo");
// 		$result 	= $arModel->where('id',[100,200],'in')->get("userinfo");
// 		$result 	= $arModel->where("id",10000,">=")->where("id",11000,"<=")->get("userinfo");
		$result 	= $arModel->where('nickname',"%joke%","like")->get("userinfo");



		//字段相加减
// 		$tableData 	= ['money'=>$arModel->inc(10)];
		//UPDATE user_money_total SET `money` = money+10 WHERE  userid = '1084#jzdxph'
// 		$result 	= $arModel->where('userid','1084#jzdxph')->update('user_money_total', $tableData);

// 		$tableData 	= ['money'=>$arModel->dec(10)];
		//UPDATE user_money_total SET `money` = money-10 WHERE  userid = '1084#jzdxph'
// 		$result 	= $arModel->where('userid','1084#jzdxph')->update('user_money_total', $tableData);


		//获取sql执行记录
		$trace 		= $arModel->getTrack();
?>
