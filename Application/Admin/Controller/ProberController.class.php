<?php
/**
 * 后台-探针控制器
 *
 * @author 林坤源
 * @link http://www.lamson.cc
 */
namespace Admin\Controller
{
class ProberController extends AuthController
{
	/**
	 * 默认要实例化的模型的名字，为空时系统会实例化与控制器同名的模型， 为null时表示不需要实例化模型
	 *
	 * @var string|null
	 * @access public
	 */
	public $modelName = null;
	
	protected $_uckAction = 'index';
	
	/**
	 * 初始框架页
	 */
	public function index()
	{
		define('YES', '<span class="resYes">YES</span>');
		define('NO', '<span class="resNo">NO</span>');
		define('ICON', '<span class="icon">2</span>&nbsp;');
		
		// 系统参数
		switch (PHP_OS)
		{
			case 'Linux':
				$sysre = (($sysinfo = $this->_sysLinux()) !== false) ? 'show' : 'none';
			break;
			case 'FreeBSD':
				$sysre = (($sysinfo = $this->_sysFreebsd()) !== false) ? 'show' : 'none';
			break;
			default:
			break;
		}
		
		$this->assign('sysre', $sysre);
		$this->assign('mysql', false !== function_exists("$dbconn->execute") ? '' : ' disabled');
		$this->assign('mail', false !== function_exists('mail') ? '' : ' disabled');
		$this->assign('gdversion', $this->_gdVersion());
		$this->assign('mysqlversion', $this->_mysqlVersion());
		$this->assign('os', explode(' ', php_uname()));
		$this->assign('disfuns', get_cfg_var('disable_functions'));
		$this->display();
	}
	
	/**
	 * 获得GD的版本
	 */
	protected function _gdVersion()
	{
		static $gd_version_number = null;
		if ($gd_version_number === null)
		{
			ob_start();
			phpinfo(8);
			$module_info = ob_get_contents();
			ob_end_clean();
			if (preg_match('/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i', $module_info, $matches))
			{
				$version = $matches[1];
			}
			else
			{
				$version = 0;
			}
		}
		return $version;
	}
	
	/**
	 * 获得mysql的版本<td class='v'>5.0.51a </td>
	 */
	protected function _mysqlVersion()
	{
		static $mysql_version_number = null;
		if ($mysql_version_number === null)
		{
			ob_start();
			phpinfo(8);
			$module_info = ob_get_contents();
			ob_end_clean();
			if (preg_match('#Client API version </td><td class="v">([^\-]*)#i', $module_info, $matches))
			{
				$version = $matches[1];
			}
			else
			{
				$version = 0;
			}
		}
		return $version;
	}

	/**
	 * 系统参数探测 LINUX
	 */
	protected function _sysLinux()
	{
		// CPU
		if (false === ($str = @file('/proc/cpuinfo'))){return false;}
		$str = implode('', $str);
		@preg_match_all('/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(.]+)[\r\n]+/', $str, $model);
		@preg_match_all('/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/', $str, $cache);
		if (false !== is_array($model[1]))
		{
			$res['cpu']['num'] = sizeof($model[1]);
			for ($i = 0; $i < $res['cpu']['num']; $i ++)
			{
				$res['cpu']['detail'][] = '类型：' . $model[1][$i] . ' 缓存：' . $cache[1][$i];
			}
			if (false !== is_array($res['cpu']['detail'])){$res['cpu']['detail'] = implode('<br />', $res['cpu']['detail']);}
		}
		
		// UPTIME
		if (false === ($str = @file('/proc/uptime'))){return false;}
		$str = explode(' ', implode('', $str));
		$str = trim($str[0]);
		$min = $str / 60;
		$hours = $min / 60;
		$days = floor($hours / 24);
		$hours = floor($hours - ($days * 24));
		$min = floor($min - ($days * 60 * 24) - ($hours * 60));
		if ($days !== 0){$res['uptime'] = $days . '天';}
		if ($hours !== 0){$res['uptime'] .= $hours . '小时';}
		$res['uptime'] .= $min . '分钟';
		
		// MEMORY
		if (false === ($str = @file('/proc/meminfo'))){return false;}
		$str = implode('', $str);
		preg_match_all('/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s', $str, $buf);
		
		$res['memTotal'] = round($buf[1][0] / 1024, 2);
		$res['memFree'] = round($buf[2][0] / 1024, 2);
		$res['memUsed'] = ($res['memTotal'] - $res['memFree']);
		$res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;
		
		$res['swapTotal'] = round($buf[3][0] / 1024, 2);
		$res['swapFree'] = round($buf[4][0] / 1024, 2);
		$res['swapUsed'] = ($res['swapTotal'] - $res['swapFree']);
		$res['swapPercent'] = (floatval($res['swapTotal']) != 0) ? round($res['swapUsed'] / $res['swapTotal'] * 100, 2) : 0;
		
		// LOAD AVG
		if (false === ($str = @file('/proc/loadavg'))){return false;}
		$str = explode(' ', implode('', $str));
		$str = array_chunk($str, 3);
		$res['loadAvg'] = implode(' ', $str[0]);
		
		return $res;
	}

	/**
	 * 系统参数探测 FreeBSD
	 */
	protected function _sysFreebsd()
	{
		// CPU
		if (false === ($res['cpu']['num'] = $this->_getKey('hw.ncpu'))){return false;}
		$res['cpu']['detail'] = $this->_getKey('hw.model');
		
		// LOAD AVG
		if (false === ($res['loadAvg'] = $this->_getKey('vm.loadavg'))){return false;}
		$res['loadAvg'] = str_replace('{', '', $res['loadAvg']);
		$res['loadAvg'] = str_replace('}', '', $res['loadAvg']);
		
		// UPTIME
		if (false === ($buf = $this->_getKey('kern.boottime'))){return false;}
		$buf = explode(' ', $buf);
		$sys_ticks = time() - intval($buf[3]);
		$min = $sys_ticks / 60;
		$hours = $min / 60;
		$days = floor($hours / 24);
		$hours = floor($hours - ($days * 24));
		$min = floor($min - ($days * 60 * 24) - ($hours * 60));
		if ($days !== 0){$res['uptime'] = $days . '天';}
		if ($hours !== 0){$res['uptime'] .= $hours . '小时';}
		$res['uptime'] .= $min . '分钟';
		
		// MEMORY
		if (false === ($buf = $this->_getKey('hw.physmem'))){return false;}
		$res['memTotal'] = round($buf / 1024 / 1024, 2);
		$buf = explode('\n', $this->_doCommand('vmstat', ''));
		$buf = explode(' ', trim($buf[2]));
		
		$res['memFree'] = round($buf[5] / 1024, 2);
		$res['memUsed'] = ($res['memTotal'] - $res['memFree']);
		$res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;
		
		$buf = explode('\n', $this->_doCommand('swapinfo', '-k'));
		$buf = $buf[1];
		preg_match_all('/([0-9]+)\s+([0-9]+)\s+([0-9]+)/', $buf, $bufArr);
		$res['swapTotal'] = round($bufArr[1][0] / 1024, 2);
		$res['swapUsed'] = round($bufArr[2][0] / 1024, 2);
		$res['swapFree'] = round($bufArr[3][0] / 1024, 2);
		$res['swapPercent'] = (floatval($res['swapTotal']) != 0) ? round($res['swapUsed'] / $res['swapTotal'] * 100, 2) : 0;
		
		return $res;
	}

	/**
	 * 取得参数值 FreeBSD
	 */
	protected function _getKey($keyName)
	{
		return $this->_doCommand('sysctl', '-n $keyName');
	}

	/**
	 * 确定执行文件位置 FreeBSD
	 */
	protected function _findCommand($commandName)
	{
		$path = array(
			'/bin', 
			'/sbin', 
			'/usr/bin', 
			'/usr/sbin', 
			'/usr/local/bin', 
			'/usr/local/sbin'
		);
		foreach ($path as $p)
		{
			if (@is_executable("$p/$commandName")){return "$p/$commandName";}
		}
		return false;
	}

	/**
	 * 执行系统命令 FreeBSD
	 */
	protected function _doCommand($commandName, $args)
	{
		$buffer = '';
		if (false === ($command = $this->_findCommand($commandName)))
			return false;
		if ($fp = @popen("$command $args", 'r'))
		{
			while (! @feof($fp))
			{
				$buffer .= @fgets($fp, 4096);
			}
			return trim($buffer);
		}
		return false;
	}

	/**
	 * phpinfo
	 */
	public function phpinfo()
	{
		phpinfo();
	}
}

}
namespace
{
	function _getCon($var)
	{
		@ $res = ini_get($var);
		if ($res == '' || $res == '0' || $res == '1')
		{}
		else
		{
			$res = get_cfg_var($var);
		}
		switch ($res)
		{
			case 0:
				return NO;
				break;
			case 1:
				return YES;
				break;
			default:
				return $res;
				break;
		}
	}
	
	function _isFun($fun)
	{
		return (false !== function_exists($fun)) ? YES : NO;
	}
}