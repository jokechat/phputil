<?php
/**
 * 封装常规的对redis的操作
 * @notice 该脚本所运营的环境必须安装php_redis扩展
 * @author jokechat
 * @2016年6月7日
 * @下午3:09:07
 * @email jokechat@qq.com
 * @v1.1 增加服务器异常检测操作
 * @v2.0 增加队列封装 2016年8月26日15:24:02
 */
class RedisUtil
{

	private static $host 	= "ip/domain";
	private static $port 	= 6379;
	private static $passwd 	= "password";

	protected  $redis 	= null;

	// php-redis 对redis做了处理,key类型为int类型  by jokechat
	private static $redisType 	= [0=>'none',1=>'string',2=>'set',3=>'list',4=>'zset',5=>'hash'];

	public function __construct()
	{
		$this->redis = new Redis();
		try {
			$this->redis->connect(self::$host,self::$port);
			$this->redis->auth(self::$passwd);
		}catch (RedisException $e)
		{
// 			$e->getMessage();
			return false;
		}
	}


	/**
	 * 返回ping 结果
	 */
	public function ping()
	{
		try {
			return 	$this->redis->ping();
		}catch (RedisException $e)
		{
			return false;
		}

	}

	/**
	 * 设置缓存key 可以直接存储数据  进行json_encode
	 * 设置失败返回false
	 * @param string $key
	 * @param string | 其他 $value
	 */
	public function set($key,$value)
	{

		$ping 		= $this->ping();
		if (!$ping)
		{
			return $ping;
		}

		if (is_array($value))
		{
			$value 	= json_encode($value,JSON_UNESCAPED_UNICODE);
		}

		return $this->redis->set($key,$value);
	}


	/**
	 * 设置带有过期时间的缓存  可以直接存储数据
	 * 设置失败返回false
	 * @param string $key
	 * @param string|array $value
	 * @param unknown $expire_time
	 */
	public function setex($key,$value,$expire_time=3600)
	{

		$ping 		= $this->ping();
		if (!$ping)
		{
			return $ping;
		}

		if (is_array($value))
		{
			$value 	= json_encode($value,JSON_UNESCAPED_UNICODE);
		}

		return  $this->redis->setex($key,$expire_time,$value);
	}


	/**
	 * 获取redis 缓存 如果存在 返回数据 不存在返回 false
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{

		$ping 		= $this->ping();
		if (!$ping)
		{
			return $ping;
		}

		$result 	=  $this->redis->get($key);
		$deRet 		= json_decode($result,true);
		if(!is_null($deRet))
		{
			$result 	= $deRet;
		}

		return $result;
	}

	/**
	 * @desc 删除指定key的值
	 * @param string $key
	 */
	public function del($key)
	{
		return $this->redis->del($key);
	}

	/**
	 * @desc 返回redis当前数据库的记录总数
	 * @return number
	 */
	public function dbSize()
	{
		$result = $this->redis->dbSize();
		return $result;
	}

	/**
	 * @desc 获取给定模式的 key 列表
	 * @param string $key
	 * @return Ambigous <boolean, array>
	 */
	public function keys($key)
	{
		$result 	= $this->redis->keys($key);
		$result 	= !empty($result) ? $result : false;
		return $result;
	}

	/**
	 * @desc 返回 key 所储存的值的类型
	 * @param string $key
	 * @return multitype:string
	 */
	public function keyType($key)
	{
		$result 	= $this->redis->type($key);
		$result 	= self::$redisType[$result];
		return $result;
	}

	/****************************
	 * 以下为list 操作 2016年8月26日13:59:17 by jokechat
	 ***************************/

	/**
	 * @desc 返回指定key的list长度
	 * @param string $key key
	 * @return number
	 * 返回指定key长度,key不存在时返回0
	 */
	public function llength($key)
	{
		$result 	= $this->redis->lLen($key);
		return $result;
	}

	/**
	 * @desc 返回指定key的list长度
	 * @param string $key key
	 * @return number
	 * 返回指定key长度,key不存在时返回0
	 */
	public function lSize($key)
	{
		$result 	= $this->redis->lSize($key);
		return $result;
	}

	/**
	 * @desc 将一个或多个值插入到列表头部。 如果 key 不存在，一个空列表会被创建并执行 LPUSH 操作。
	 *  当 key 存在但不是列表类型时，返回一个错误。
	 * @param string $key
	 * @param array | string $value
	 * @return number 操作成功 返回操作后的list 长度
	 */
	public function lPush($key,$value)
	{
		if (is_array($value))
		{
			foreach ($value as $key=>$v)
			{
				$result = $this->redis->lPush($key,$value);
			}
		}else
		{
			$result 	= $this->redis->lPush($key,$value);
		}
		return $result;
	}

	/**
	 * @desc 将一个或多个值插入到已存在的列表头部。如果列表不存在，操作无效返回0
	 * @param string $key
	 * @param string $value
	 * @return number 操作成功 返回操作后的list 长度
	 */
	public function lPushX($key,$value)
	{
		if (is_array($value))
		{
			foreach ($value as $key=>$v)
			{
				$result = $this->redis->lPushx($key,$value);
			}
		}else
		{
			$result 	= $this->redis->lPushx($key,$value);
		}
		return $result;
	}

	/**
	 * @desc 将一个值或多个值添加到列表尾部,key不存在则被创建并执行RPUSH操作
	 * @param string $key
	 * @param array | string $value
	 * @return number 操作成功 返回操作后的list 长度
	 */
	public function rPush($key,$value)
	{
		if (is_array($value))
		{
			foreach ($value as $k=>$v)
			{
				$result = $this->redis->rPush($key,$v);
			}
		}else
		{
			$result 	= $this->redis->rPush($key,$value);
		}

		return $result;
	}

	/**
	 * @desc 将一个或多个值插入到已存在key的列表尾部。如果列表不存在，操作无效返回0
	 * @param string $key key
	 * @param array | string $value
	 * @return number
	 */
	public function rPushX($key,$value)
	{
		if (is_array($value))
		{
			foreach ($value as $k=>$v)
			{
				$result = $this->redis->rPushx($key,$v);
			}
		}else
		{
			$result 	= $this->redis->rPushx($key,$value);
		}
		return $result;
	}

	/**
	 * @desc 移除并返回列表的第一个元素  (可记做left pop)
	 * @param string $key
	 * @return string | false list不空,返回存储内容,否则返回false
	 */
	public function lPop($key)
	{
		$result 	= $this->redis->lPop($key);
		return $result;
	}

	/**
	 * @desc 移除并返回list最后一个元素 (right pop)
	 * @param string $key key
	 * @return string | false list非空,返回移除内容,否则返回false
	 */
	public function rPop($key)
	{
		$result 	= $this->redis->rPop($key);
		return $result;
	}

	/**
	 * @desc 通过索引获取列表中的元素。也可以使用负数下标，以 -1 表示列表的最后一个元素， -2 表示列表的倒数第二个元素，以此类推。
	 * @param string $key
	 * @param number $index
	 * @return string | bool 存在索引,返回存储内容,否则返回false;
	 */
	public function lIndex($key,$index)
	{
		$result 	= $this->redis->lindex($key,$index);
		return $result;
	}

	/**
	 * @desc 返回列表中指定区间内的元素
	 * @param string $key key
	 * @param number $start list 起始偏移量
	 * @param number $stop list 结束偏移量 以 -1 表示列表的最后一个元素， -2 表示列表的倒数第二个元素，以此类推。
	 * @return array | false
	 */
	public function lRange($key,$start,$stop)
	{
		$result 	= $this->redis->lrange($key,$start,$stop);
		$result 	= !empty($result) ? $result : false;
		return $result;
	}

}
?>
