<?php
/**
 * Created by PhpStorm.
 * User: Charlot
 * Date: 24/08/2018
 * Time: 13:42
 */

namespace db\home;

use db\home as home;


class config {

	private $configFile     = "config.json.php";
	public $config = array();

	public $loggedIn = false;

	public function __construct() {

		if(file_exists($this->configFile)) {
			$this->config = json_decode(file_get_contents($this->configFile), true);
		}
		if(isset($_COOKIE['db_home_user'])) {
			$this->loggedIn = true;
		}

	}

	public function login(){
		$this->loggedIn = true;
		setcookie("db_home_user", "1");
	}

	public function logout(){
		$this->loggedIn = false;
		setcookie("db_home_user", "", time() - 3600);
	}

	public function getSection( $key )
	{
		if(!isset($this->config[$key])) return false;
		return $this->config[$key];
	}

	public function getController() {
		if(!isset($this->config['controller'])) return false;
		return $this->config['controller']['id'];
	}


	public function getValue( $section, $key )
	{
		if(!isset($this->config[$section])) return false;
		if(!isset($this->config[$section][$key])) return false;
		return $this->config[$section][$key];
	}

	public function getDeviceIdByName($name) {
		if(!isset($this->config['devices'])) return false;
		foreach ($this->config['devices'] as $device) {
			if ($device['name'] === $name) {
				return $device['id'];
			}
		}
		return false;
	}

	public function getDeviceDetails( $id )
	{
		if(!isset($this->config['devices'])) return false;
		foreach ($this->config['devices'] as $device) {
			if ($device['id'] === $id) {
				return $device;
			}
		}
		return false;

	}

	public function getDevicesByRoomAndType($room, $type) {
		$devices = array();
		if(!isset($this->config['rooms'][$room])) return false;
		$devicesInRoom = $this->config['rooms'][$room];
		if(!isset($this->config['types'][$type])) return false;
		$devicesbyType = $this->config['types'][$type];
		foreach($devicesInRoom as $device) {
			if( in_array(  $device , $devicesbyType ) ) {
				$devices[]=$device;
			}
		}
		return $devices;
	}

	public function getDevicesByType($type) {
		$devices = array();
		if(!isset($this->config['types'][$type])) return false;
		$devicesbyType = $this->config['types'][$type];
		foreach($devicesbyType as $device) {
				$devices[]=$device;
		}
		return $devices;
	}

	public function getDeviceDetailsFromRoom($room) {
		if(!isset($this->config['rooms'][$room])) return false;
		$devicesIds = $this->config['rooms'][$room];
		$ret = array();
		foreach($devicesIds as $deviceId) {
			$ret[]=$this->getDeviceDetails($deviceId);
		}
		return $ret;
	}

	public function getDeviceDetailsFromType($type) {
		if(!isset($this->config['types'][$type])) return false;
		$devicesIds = $this->config['types'][$type];
		$ret = array();
		foreach($devicesIds as $deviceId) {
			$ret[]=$this->getDeviceDetails($deviceId);
		}
		return $ret;
	}

	public function saveConfig() {
		$json = json_encode($this->config, 128);
		return file_put_contents($this->configFile,$json);
	}
	/*
	public function	put_ini_file($config, $has_section = false, $write_to_file = true){
		$fileContent = '';
		if(!empty($config)){
			foreach($config as $i=>$v){
				if($has_section){
					$fileContent .= "[".$i."]\n\r" . $this->put_ini_file($v, false, false);
				}
				else{
					if(is_array($v)){
						foreach($v as $t=>$m){
							$fileContent .= $i."[".$t."] = ".(is_numeric($m) ? $m : '"'.$m.'"') . "\n\r";
						}
					}
					else $fileContent .= $i . " = " . (is_numeric($v) ? $v : '"'.$v.'"') . "\n\r";
				}
			}
		}

		if($write_to_file && strlen($fileContent)) return file_put_contents($this->configFile, $fileContent, LOCK_EX);
		else return $fileContent;
	}
	*/

}