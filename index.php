<?php
/**
 * Created by  : Designburo.nl
 * Project     : DBHomekit
 * Filename    : index.php
 * Description : Core functionality
 * Date        : 02/09/2018
 * Time        : 22:06
 * Source      : https://github.com/Designburo/dbhomekit
 */

namespace db\home;
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
use db\home as home;

require_once('classes/config.class.php');
require_once('classes/io.class.php');
require_once('classes/display.class.php');

function logToFile($log) {
	if ( ! file_exists( 'logs.txt' ) ) {
		$myfile = file_put_contents( 'logs.txt', $log . PHP_EOL, LOCK_EX );
	} else 	$myfile = file_put_contents( 'logs.txt', $log . PHP_EOL, FILE_APPEND | LOCK_EX );
}


/**
 * @param $name
 *
 * @return bool|string|array
 */
function getPost($name) {
	if( isset( $_POST[$name]) && $_POST[$name] !== "") {
		if(is_array($_POST[$name])) {
			return $_POST[$name];
		} else return trim($_POST[$name]);
	} else return false;
}

function getGet($name) {
	if( isset( $_GET[$name]) && $_GET[$name] !== "") {
		return $_GET[$name];
	} else return false;
}

$config = new home\config();
$display = new home\display($config->loggedIn);

if(isset($_GET['display'])) {
	$page = $_GET['display'];
	$page = strtoupper($page);
} else $page = 'DASHBOARD';


if ($ifttt = getPost('ifttt') || $ifttt = getGet('ifttt')) {
	include "handle-IFTT.php";
	if(!$config->loggedIn) {
		die();
	}
}

if ( $action = getPost('action' ) ) {
	include "handle-action.php";
}

if(!$config->config) {
	$display->prepare('install');
	$display->present();
}
if(!$config->loggedIn) {
	$display->prepare('login' );
	$display->present();
}

// At this point we have logged in ..
$io = new home\io( $config->getValue('HomeWizard', 'username' ), $config->getValue('HomeWizard', 'password' ) );

$reload = getGet('reload');

if(!$config->getSection('devices') || $reload === 'true') {
	$devices = $io->getAllDevices();
	if($devices['status']) {
		$config->config['devices'] = $devices['value'];
		$config->saveConfig();
	} else {
		$display->setMessage( $devices['value'] );
		$display->prepare('HW-credentials' );
		$display->present();
	}
}
if(!$config->getSection('controller')) {
	echo "no controller";
	$controller = $io->getAllDevices(true);
	if ($controller['status'] === true) {
		$config->config['controller'] = $controller['value'];
		echo $config->saveConfig();
	} else {
		$display->setMessage( $controller['value'] );
		$display->prepare('HW-credentials' );
		$display->present();
	}
}


// Ready to build the correct page
$display->setKey('nav', $display->nav($page) );
switch($page) {
	case "DASHBOARD" :
		$cards = '';
		$devices = $config->getSection('devices');


		foreach($devices as $device) {
			$deviceId = $device['id'];
			$devicetypeName = $device['typeName'];
			$devicename = $device['name'];
			$deviceIcon = $device['iconUrl'];
			if($deviceIcon==="") {
				$deviceIcon="icons/default";
			}
			if($deviceIcon==="custom") {
				$deviceIcon="icons/custom";
			}
			$deviceIcon .= '.png';
			$divid = str_replace(' ','_',$devicename);
			$controllerName = $config->getValue('controller', 'name');
			if($devicetypeName=='plug_outlet') {
				$background = 'uk-background-primary';
				$cardtype = 'primary';
			} else {
				$cardtype = 'default';
				$background = 'uk-background-muted';
			}

			$rooms = $config->getSection('rooms');
			if(!$rooms) {
				$deviceHTML = 'undefined';
			} else {
				$deviceRoom = false;
				$deviceHTML = '<select name="room[]" class="uk-select" multiple>';
				foreach ($rooms as $room=>$id) {
					if(in_array($deviceId,$id)) {
						$deviceHTML .= '<option value="' . $room . '" selected>' . $room . '</option>';
						$deviceRoom = $room;
					} else {
						$deviceHTML .= '<option value="' . $room . '">' . $room . '</option>';
					}
				}
				if ($deviceRoom === false) {
					$deviceHTML .= '<option value="undefined" selected disabled>undefined</option>';
				}
				$deviceHTML .= '</select>';

			}
			$types = $config->getSection('types');
			if(!$types) {
				$typeHTML = 'undefined';
			} else {
				$deviceType = false;
				$typeHTML = '<select name="type" class="uk-select">';
				foreach ($types as $type=>$id) {
					if(in_array($deviceId,$id)) {
						$typeHTML .= '<option value="' . $type . '" selected>' . $type . '</option>';
						$deviceType = $type;
					} else {
						$typeHTML .= '<option value="' . $type . '">' . $type . '</option>';
					}
				}
				if ($deviceType === false) {
					$typeHTML .= '<option value="undefined" selected disabled>undefined</option>';
				}
				$typeHTML .= '</select>';

			}

			$replace = array(
				'icon',
				'background',
				'deviceId',
				'divid',
				'devicetypeName',
				'devicename',
				'cardtype',
				'deviceRoom',
				'deviceType'
			);
			$with = array(
				$deviceIcon,
				$background,
				$deviceId,
				$divid,
				$devicetypeName,
				$devicename,
				$cardtype,
				$deviceHTML,
				$typeHTML
			);
			$cards .= $display->build( 'card', $replace, $with );
		}

		$display->setKey('cards', $cards);
		$display->setKey('switchname', $controllerName);
		$display->prepare('cards');
		$display->present();
		break;
	case "ROOMS":
		$rooms = $config->getSection('rooms');
		if(!$rooms) {
			$display->setMessage('There are no rooms yet, please create one first','primary');
			$display->prepare('createroom');
			$display->present();
		}
		$roomlist = '';
		$roomlist .= '<ul class="uk-list uk-list-large uk-list-divider">';
		foreach($rooms as $k=>$v) {
			$tools = "<script>
function show_alert(form) {

  if(confirm('Do you really want to do delete this room?'))
    form.submit();
  else
    return false;
}
</script>";
			$tools .= '<li>';
			$tools .= '<form id="'.$k.'" method="post" style="display:inline-block;">';
			$tools .= '<input type="hidden" name="room" value="'.$k.'">';
			$tools .= '<button class="uk-text-middle uk-margin-right uk-button uk-button-text" uk-tooltip="Edit room" type="submit" name="action" value="showeditroom"><span uk-icon="icon: file-edit"></span></button>';
			$tools .= '</form>';
			$tools .= '<form id="'.$k.'" method="post" style="display:inline-block;">';
			$tools .= '<button onClick="show_alert(this.form); return false;" class="uk-text-middle uk-margin-right uk-button uk-button-text" uk-tooltip="Delete room" type="submit" name="action" value="deleteroom"><span uk-icon="icon: trash"></span></button>';
			$tools .= '<input type="hidden" name="room" value="'.$k.'">';
			$tools .= '<input type="hidden" name="action" value="deleteroom">';
			$tools .= '</form></span>';
			$tools .= '';
			$listOfDevices = '';
			$devicesDetails = $config->getDeviceDetailsFromRoom($k);
			if($devicesDetails !== false) {
				$listOfDevices = '<span class="uk-text-middle" style="float:right;">';
				foreach ( $devicesDetails as $deviceDetail ) {
					$listOfDevices .= '"'.$deviceDetail['name'] . '" ';
				}
				$listOfDevices .= '</span>';
			}
			$roomlist .= $tools.'<span class="uk-text-large uk-text-primary uk-text-middle">'.$k.'</span>'.$listOfDevices.'</li>';
		}
		$roomlist .= '</ul>';
		$display->setKey('rooms', $roomlist);
		$display->prepare('rooms');
		$display->present();
		break;
	case "TYPES":
		$types = $config->getSection('types');
		if(!$types) {
			$display->setMessage('There are no types defined yet, please create one first','primary');
			$display->prepare('createtype');
			$display->present();
		}
		$typelist = '<ul class="uk-list uk-list-large uk-list-divider">';
		foreach($types as $k=>$v) {
			$tools = "<script>
function show_alert(form) {

  if(confirm('Do you really want to do delete this type?'))
    form.submit();
  else
    return false;
}
</script>";
			$tools .= '<li>';
			$tools .= '<form id="'.$k.'" method="post" style="display:inline-block;">';
			$tools .= '<input type="hidden" name="type" value="'.$k.'">';
			$tools .= '<button class="uk-text-middle uk-margin-right uk-button uk-button-text" uk-tooltip="Edit type" type="submit" name="action" value="showedittype"><span uk-icon="icon: file-edit"></span></button>';
			$tools .= '</form>';
			$tools .= '<form id="'.$k.'" method="post" style="display:inline-block;">';
			$tools .= '<button onClick="show_alert(this.form); return false;" class="uk-text-middle uk-margin-right uk-button uk-button-text" uk-tooltip="Delete type" type="submit" name="action" value="deletetype"><span uk-icon="icon: trash"></span></button>';
			$tools .= '<input type="hidden" name="type" value="'.$k.'">';
			$tools .= '<input type="hidden" name="action" value="deletetype">';
			$tools .= '</form></span>';
			$tools .= '';
			$listOfDevices = '';
			$devicesDetails = $config->getDeviceDetailsFromType($k);
			if($devicesDetails !== false) {
				$listOfDevices = '<span class="uk-text-middle" style="float:right;">';
				foreach ( $devicesDetails as $deviceDetail ) {
					$listOfDevices .= '"'.$deviceDetail['name'] . '" ';
				}
				$listOfDevices .= '</span>';
			}
			$typelist .= $tools.'<span class="uk-text-large uk-text-primary uk-text-middle">'.$k.'</span>'.$listOfDevices.'</li>';
		}
		$typelist .= '</ul>';
		$display->setKey('types', $typelist);
		$display->prepare('types');
		$display->present();
		break;
	case "IFTTT" : {
		$display->prepare('ifttt-index' );
		$display->present();
		break;
	}
	default:
		$display->prepare('notfound' );
		$display->present();
		break;
}

?>