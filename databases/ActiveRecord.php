<?php
/**
 * 进阶处理mysql 操作基础封装
 * 连贯性支持查询
 * 加入like,in,between,not in 等等
 * @author jokechat
 * @2016年8月16日
 * @上午11:45:36
 * @email jokechat@qq.com
 */
class ActiveRecordModel
{

	protected $db = null;
	/**
	 * 实例化数据库连接
	 */
	public function __construct()
	{

		$host 	= "localhost";
		$user 	= "jokechat";
		$pass 	= "zhangkui";
		$db 		= "maoxian";
		require "MysqliDb2.php";
		if(empty($this->db)){
			$this->db = new MysqliDb2($host, $user, $pass, $db);
		}
	}


	/**
	 * 使用原始的预编译查询
	 * 例如
	 * 	$query 		= "select * from userinfo where nickname = ? or nickname = ?";
	 *	$bindParams = ['jokechat','坏孩子'];
	 * @param string $query
	 * @param array $bindParams
	 * @return
	 */
	public function rawQuery($query, $bindParams = null)
	{
		$result 	= $this->db->rawQuery($query, $bindParams);
		return $result;
	}


	/**
	 * 一种执行选择查询的方法
	 * @param string $query 查询语句
	 * @param array | number $numRows
	 * @return
	 */
	public function query($query, $numRows = null)
	{
		$result 	= $this->db->query($query,$numRows);
		return $result;
	}


	/**
	 * 支持连贯性where查询
	 * 如果$where为数组,后面三个参数作废,不生效 使用形式 $where 		= ['belong'=>'gh_59fea3718abf','nickname'=>'jokechat'];
	 * 高阶使用形式如下,可以连贯性条件使用
	 * 如果$where为string  使用形式 $model->where('nickname','jokechat','=','AND')->where('nickname','%坏孩子%','like','OR')->get("userinfo");
	 * @param array | string $where
	 * @param string $whereValue
	 * @param string $operator
	 * @param string $cond
	 * @return ActiveRecordModel
	 */
	public function where($where, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
	{
		if (is_array($where))
		{
			foreach ($where as $k=>$v)
			{
				$this->db->where($k,$v);
			}
		}else
		{
			$this->db->where($where,$whereValue,$operator,$cond);
		}
		return $this;
	}

	/**
	 * orWhere 条件
	 * @param string $whereProp
	 * @param string $whereValue
	 * @param string $operator
	 * @return MysqliDb
	 */
	public function orWhere($whereProp, $whereValue = 'DBNULL', $operator = '=')
	{
		$result 		= $this->db->orWhere($whereProp, $whereValue , $operator);
		return $this;
	}

	/**
	 * 查询获取多条记录
	 * @param string $tableName 表名称
	 * @param  array | number $numRows 查询行数记录,如 $numRows = 0,or $numRows = [0,10];
	 * @param string $columns 需要查询的字段 默认*,形如  "id,nickname"
	 * @return
	 */
	public function get($tableName, $numRows = null, $columns = '*')
	{
		$result 		= $this->db->get($tableName,$numRows,$columns );
		return $result;
	}


	/**
	 * 获取单条记录
	 * @param string $tableName
	 * @param string $columns
	 * @return 查询成功  返回数据,否则返回null
	 */
	public function getOne($tableName, $columns = '*')
	{
		$result 		= $this->db->getOne($tableName,$columns);
		return $result;
	}

	/**
	 * 从一行获得一个单列值
	 * @param unknown $tableName 表名
	 * @param unknown $column 字段名  仅可一个字段!!!
	 * @param number $limit 查询行数
	 * @return mixed
	 */
	public function getValue($tableName, $column, $limit = 1)
	{
		$result 		= $this->db->getValue($tableName, $column,$limit);
		return $result;
	}

	/**
	 * 插入一条数据
	 * @param string $tableName
	 * @param array $insertData
	 * @return 插入成功  返回受影响的行数
	 */
	public function insert($tableName, $insertData)
	{
		$result 		= $this->db->insert($tableName, $insertData);
		return $result;
	}

	/**
	 * 替换函数
	 * 如果插入数据含有表主键,更新数据,否则新增一条数据
	 * @param string $tableName
	 * @param array $insertData
	 * @return 返回受影响的行数
	 */
	public function replace($tableName, $insertData)
	{
		$result 		= $this->db->replace($tableName, $insertData);
		return $result;
	}

	/**
	 * 检测表中是否存在记录
	 * @param string $tableName
	 * @return 如果表中含有记录  返回true,否则返回false
	 */
	public function has($tableName)
	{
		$result 		= $this->db->has($tableName);
		return $result;
	}

	/**
	 * 根据条件 更新表记录 更新前必须使用where条件限定更新的数据!
	 * @param string $tableName
	 * @param array $tableData
	 * @param string $numRows
	 * @return Ambigous <boolean, void, string>
	 */
	public function update($tableName, $tableData, $numRows = null)
	{
		$result 		= $this->db->update($tableName, $tableData,$numRows);
		return $result;
	}

	/**
	 * 根据条件 删除表记录 更新前必须使用where条件限定更新的数据!
	 * @param string $tableName
	 * @param int|array $numRows
	 * @return unknown
	 */
	public function delete($tableName, $numRows = null)
	{
		$result 		= $this->db->delete($tableName,$numRows);
		return $result;
	}

	/**
	 * join 查询
	 * 例如
	 * $result 	= $arModel->join('userinfo uinfo', "uinfo.userid=umoney.userid","LEFT")
	 *					  ->where('umoney.userid','11467#luhwcv')
	 *						  ->get('user_money_total umoney',null,"uinfo.nickname,uinfo.userid,umoney.*");
	 * @param string $joinTable
	 * @param string $joinCondition
	 * @param string $joinType
	 * @return ActiveRecordModel
	 */
	public function join($joinTable, $joinCondition, $joinType = '')
	{
		$result 		= $this->db->join($joinTable, $joinCondition, $joinType);
		return $this;
	}


	/**
	 * orderBy 排序查询
	 * 例如使用 orderBy('id', 'desc')->orderBy('name', 'desc');
	 * @param string $orderByField
	 * @param string $orderbyDirection  排序规则  ASC | DESC
	 * @param string $customFields
	 * @return ActiveRecordModel
	 */
	public function orderBy($orderByField, $orderbyDirection = "DESC", $customFields = null)
	{
		$result 		= $this->db->orderBy($orderByField, $orderbyDirection, $customFields);
		return $this;
	}

	/**
	 * groupBy 根据表中字段 分组查询
	 * @param string $groupByField
	 * @return MysqliDb
	 */
	public function groupBy($groupByField)
	{
		$result 		= $this->db->groupBy($groupByField);
		return $result;
	}

	/**
	 * having 根据表中字段 分组查询
	 * @param string $havingProp
	 * @param string $havingValue
	 * @param string $operator
	 * @param string $cond
	 * @return ActiveRecordModel
	 */
	public function having($havingProp, $havingValue = 'DBNULL', $operator = '=', $cond = 'AND')
	{
		$result 		= $this->db->having($havingProp, $havingValue , $operator, $cond);
		return $this;
	}

	/**
	 * 获取最后一次查询语句
	 * @return string
	 */
	public function getLastQuery()
	{
		return $this->db->getLastQuery();
	}

	/**
	 * 获取最后一次查询错误
	 * @return number
	 */
	public function getLastError()
	{
		return $this->db->getLastErrno();
	}


	/**
	 * 开启事物 操作
	 */
	public function startTransaction()
	{
		return $this->db->startTransaction();
	}

	/**
	 * 提交事务 操作
	 */
	public function commit()
	{
		return $this->db->commit();
	}

	/**
	 * 回滚操作
	 */
	public function rollback()
	{
		return $this->db->rollback();
	}

	/**
	 * 开启调试模式
	 * @param string $enabled true | false
	 * @param string $stripPrefix
	 * @return MysqliDb
	 */
	public function setTrace($enabled, $stripPrefix = null)
	{
		return $this->db->setTrace($enabled, $stripPrefix);
	}

	/**
	 * 获取调试信息
	 * @return multitype:
	 */
	public function getTrack()
	{
		return $this->db->trace;
	}



	/********************************************
	 * 以下为特殊用处函数  money=money+10 等,日期计算等
	 * by jokechat 2016年8月16日17:02:51
	 * jokechat@qq.com
	 * ******************************************/

	/**
	 * 字段值相加某个数值  无需带操作符
	 * @param number $num 1
	 * @return 操作成功返回true 失败false
	 */
	public function inc($num = 1)
	{
		return $this->db->inc($num);
	}

	/**
	 * 字段值相减某个数值 无需带操作符
	 * @param number $num 1
	 * @return 操作成功返回true 失败false
	 */
	public function dec($num = 1)
	{
		return $this->db->dec($num);
	}
}
?>
