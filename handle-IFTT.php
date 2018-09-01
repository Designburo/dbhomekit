<?php
/**
 * Created by PhpStorm.
 * User: Charlot
 * Date: 27/08/2018
 * Time: 14:47
 */

namespace db\home;

$username = getPost('username');
$password = getPost('password');
$action = trim(getPost('action'));
$room = getPost('room');
$type = getPost('type');
$device = getPost('device');
$controller = $config->getController();
$dt = date('d-m-Y-H-i-s');
$log = "$dt : Username :$username - Password:$password - Action:$action - Room:$room - Type:$type - Device:$device - Controller$controller";

if($action === "on") {
	$action = "On";
}
if($action === "off") {
	$action = "Off";
}

use db\home as home;

$io = new home\io( $config->getValue('HomeWizard', 'username' ), $config->getValue('HomeWizard', 'password' ) );

if($username !== $config->config['login']['username'] || $password !== $config->config['login']['password'] ) {
	die('wrong credentials');
}

// Request per device

if( $device !== false && $action !== false && $controller !== false) {
	if(is_array($device)) {
		$token = $io->getLoginToken();
		if($token['status']===true) {
			$token=$token['value'];
			$listOfDevices = '(';
			foreach ($device as $single) {
				$url = $url = $io->getPlugsUrl() . '/';
				$deviceId = $config->getDeviceIdByName($single);
				$listOfDevices .= ' '.$single.' ';
				$url .= $controller . "/devices/". $deviceId . '/action';
				$result = $io->doAction($url,$token,$action);
				if(!$result['status']) {
					$log .= " - ERROR:".$result['value'];
					logToFile($log);
					die ($result['value']);
				}
			}
			$log .= " - SUCCESS:".$listOfDevices .') have been turned ';
			logToFile($log);
			$display->setMessage( $listOfDevices .') have been turned ' . $action, 'success' );
		} else {
			$log .= " - ERROR:".$token['value'];
			logToFile($log);
			die($token['value']);
		}
	} else {
		$deviceId = $config->getDeviceIdByName($device);
		if(!$deviceId) {
			$log .= " - ERROR:Unkown device ID";
			logToFile($log);
			die(' Unkown device ID');
		} else {
			$token = $io->getLoginToken();
			if($token['status']===true) {
				$token=$token['value'];
				$url = $io->getPlugsUrl() . '/' . $controller . "/devices/". $deviceId . '/action';
				$result = $io->doAction($url,$token,$action);
				if(!$result['status']) {
					$log .= " - ERROR single request:".$result['value'];
					logToFile($log);
					die ($result['value']);
				}
				$log .= " - SUCCESS: ". $device .' turned ' . $action;
				logToFile($log);
				$display->setMessage( $device .' turned ' . $action, 'success' );

			} else {
				$log .= " - ERROR single request token:".$token['value'];
				logToFile($log);
				die($token['value']);
			}
		}
	}
}

// Request per room

if( $device === false && $room !== false && $type !==false && $action !== false) {
	if(trim($room)==='All') {
		$devices = $config->getDevicesByType($type);
	} else $devices = $config->getDevicesByRoomAndType($room, $type);
	if($devices !== false) {
		$token = $io->getLoginToken();
		if($token['status']===true) {
			$token=$token['value'];
			foreach ($devices as $single) {
				$url = $url = $io->getPlugsUrl() . '/';
				$url .= $controller . "/devices/". $single . '/action';
				//echo $url;
				$result = $io->doAction($url,$token,$action);
				if(!$result['status']) {
					$log .= " - ERROR Room request :: " . $result['value'];
					logToFile($log);
					die ($result['value']);
				}
			}
			$log .= " - SUCCESS: Turning " . $action;
			logToFile($log);
			die('success');
		} else {
			$log .= " - ERROR Room request token:".$token['value'];
			logToFile($log);
			die($token['value']);
		}
	} else {
		$log .= " - ERROR Room request no devices found that match.";
		logToFile($log);
		die ('no devices found that match.');
	}
}