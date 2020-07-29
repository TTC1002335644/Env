<?php
namespace bang\env;

class Env {

	/**
	 * 载入.env文件.
	 * @param string $file
	 * @throws \Exception
	 */
	public static function load(string $file) {
		if (is_file($file)) {
			$env = parse_ini_file($file, true);
			self::set($env);
		} else {
			throw new \Exception($file . " cannot found");
		}
	}

	/**
	 * 设置env
	 * @param $env
	 * @param null $value
	 */
	public static function set($env, $value = null) {
		if (is_array($env)) {
			$env = array_change_key_case($env, CASE_UPPER); //设置成大写
			foreach ($env as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $key => $val) {
						$temp_name = $key . '_' . strtoupper($key);
						putenv("$temp_name=$val");
						unset($temp_name);
					}
				} else {
					putenv(ENV_PREFIX . "$k=$v");
				}
			}
		} else {
			putenv(ENV_PREFIX . "$env=$value");
		}
	}

	/**
	 * 获取env数据
	 * @param null $name
	 * @param null $default
	 * @param string $prefix
	 * @return array|bool|false|string|null
	 */
	public static function get($name = null, $default = null, $prefix = 'PHP_') {
		$name = strtoupper(str_replace('.', '_', $name));
		return self::getEnv($name, $default, $prefix);
	}

	/**
	 * 获取公共方法并处理数据
	 * @param $name
	 * @param null $default
	 * @param string $prefix
	 * @return array|bool|false|string|null
	 */
	private static function getEnv($name, $default = null, $prefix = 'PHP_') {
		$name = $prefix . $name;

		$result = getenv($name);

		if (false === $result) {
			return $default;
		}

		if ('false' === $result) {
			$result = false;
		} elseif ('true' === $result) {
			$result = true;
		}

		return $result;
	}

}
