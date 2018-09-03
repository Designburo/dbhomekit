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


if ($ifttt = getPost('ifttt')) {
	include "handle-IFTT.php";
}

if ( $action = getPost('action' ) ) {
	switch ($action) {
		case "iftttwizard1" :
			$display->setKey('nav', $display->nav($page) );
			$what = getPost('what');
			if($what === false) break;
			switch ($what) {
				case "device":
					$deviceList="";
					$ifttt_devices = $config->getSection('devices');
					foreach ($ifttt_devices as $device) {
						$deviceList .= '<option value="'.$device['name'].'">'.$device['name'].'</option>';
					}
					$display->setKey('list', $deviceList );
					$display->prepare('ifttt-device-index' );
					$display->present();
					break;
				case "room":
					$typeList="";
					$roomList="";
					$ifttt_rooms = $config->getSection('rooms');
					foreach ($ifttt_rooms as $room=>$value) {
						$roomList .= '<option value="'.$room.'">'.$room.'</option>';
					}
					$display->setKey('list1', $roomList );
					$ifttt_types = $config->getSection('types');
					foreach ($ifttt_types as $type=>$value) {
						$typeList .= '<option value="'.$type.'">'.$type.'</option>';
					}
					$display->setKey('list2', $typeList );
					$display->prepare('ifttt-room-index' );
					$display->present();
					break;
				case "type":
					$typeList="";
					$ifttt_types = $config->getSection('types');
					foreach ($ifttt_types as $type=>$value) {
						$typeList .= '<option value="'.$type.'">'.$type.'</option>';
					}
					$display->setKey('list', $typeList );
					$display->prepare('ifttt-type-index' );
					$display->present();
					break;
			}
			break;
		case "iftttwizard2" :
			$display->setKey('nav', $display->nav($page) );
			$wizardType = getPost('ifttt-type');
			if($wizardType === false) break;
			switch ($wizardType) {
				case "1":
					$device = getPost('ifttt-chosen');
					if(!$device) break;
					$iftttList="";
					$iftttList.="<h3>WEB Request</h3>";
					$iftttList.="<p>After you have choosen and setup the IF from IFTHEN in a new applet, now choose THEN and a <strong>WEB</strong> request</p>";
					$iftttList.="<p>Fill in the following fields: <span class=\"uk-text-meta\">( Example below turns on the device. Use action=Off to turn it off )</span></p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">URL</span> ".(!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/'."index.php</p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">METHOD</span> POST</p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">CONTENT TYPE</span> application/x-www-form-urlencoded</p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">BODY</span> username=".$config->config['login']['username']."&password=".$config->config['login']['password']."&action=On&device=".$device."&ifttt=1</p>";
					$display->setKey('result', $iftttList );
					$display->prepare('ifttt-device-result' );
					$display->present();
					break;
				case "room":
					$room = getPost('ifttt-chosen-room');
					$type = getPost('ifttt-chosen-type');
					if(!$room || !$type) break;
					$iftttList="";
					$iftttList.="<h3>WEB Request</h3>";
					$iftttList.="<p>After you have choosen and setup the IF from IFTHEN in a new applet, now choose THEN and a <strong>WEB</strong> request</p>";
					$iftttList.="<p>Fill in the following fields: <span class=\"uk-text-meta\">( Example below turns on all $type in $room. Use action=Off to turn it off )</span></p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">URL</span> ".(!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/'."index.php</p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">METHOD</span> POST</p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">CONTENT TYPE</span> application/x-www-form-urlencoded</p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">BODY</span> username=".$config->config['login']['username']."&password=".$config->config['login']['password']."&action=On&room=".$room."&type=".$type."&ifttt=1</p>";
					$display->setKey('result', $iftttList );
					$display->prepare('ifttt-room-result' );
					$display->present();
					break;
				case "type":
					$type = getPost('ifttt-chosen-type');
					if(!$type) break;
					$iftttList="";
					$iftttList.="<h3>WEB Request</h3>";
					$iftttList.="<p>After you have choosen and setup the IF from IFTHEN in a new applet, now choose THEN and a <strong>WEB</strong> request</p>";
					$iftttList.="<p>Fill in the following fields: <span class=\"uk-text-meta\">( Example below turns on all $type. Use action=Off to turn it off )</span></p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">URL</span> ".(!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/'."index.php</p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">METHOD</span> POST</p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">CONTENT TYPE</span> application/x-www-form-urlencoded</p>";
					$iftttList.="<p><span class=\"uk-label uk-label-success\">BODY</span> username=".$config->config['login']['username']."&password=".$config->config['login']['password']."&action=On&room=All&type=".$type."&ifttt=1</p>";
					$display->setKey('result', $iftttList );
					$display->prepare('ifttt-type-result' );
					$display->present();
					break;
			}
			break;
		case "install" :
			$username = getPost('username');
			$password = getPost('password');
			$homewizardUsername = getPost('h_username');
			$homewizardPassword = getPost('h_password');
			if ($username === false || $password === false || $homewizardPassword === false || $homewizardUsername === false ) {
				$display->setMessage('Not all fields have been filled' );
				$display->prepare('install' );
				$display->present();
			}
			$config->config['login']['username'] = $username;
			$config->config['login']['password'] = $password;
			$config->config['HomeWizard']['username'] = $homewizardUsername;
			$config->config['HomeWizard']['password'] = $homewizardPassword;
			$config->saveConfig();
			break;
		case "hw_credentials" :
			$homewizardUsername = getPost('h_username');
			$homewizardPassword = getPost('h_password');
			if ($homewizardPassword === false || $homewizardUsername === false ) {
				$display->setMessage('Not all fields have been filled' );
				$display->prepare('HW-credentials' );
				$display->present();
			}
			$config->config['HomeWizard']['username'] = $homewizardUsername;
			$config->config['HomeWizard']['password'] = $homewizardPassword;
			$config->saveConfig();
			break;
		case "login" :
			$username = getPost('username');
			$password = getPost('password');
			if($username === $config->getValue('login', 'username') && $password === $config->getValue('login', 'password') ) {
				$config->login();
			} else {
				$display->setMessage( 'Username or password is incorrect' );
				$display->prepare('login' );
				$display->present();
			}
			break;
		case "createroom":
			$roomname = getPost('roomname');
			$newroom = getPost('newroom');
			if($roomname === false) {
				if(!$newroom) {
					$display->setMessage( 'Not all fields have been filled' );
				}
				$display->prepare('createroom' );
				$display->present();
			}
			if($config->getValue('rooms',$roomname) !== false) {
				$display->setMessage('A room with this name already exists' );
				$display->prepare('createroom' );
				$display->present();
			}
			$config->config['rooms'][$roomname] = array();
			$config->saveConfig();
			$display->setMessage('Room "'.$roomname.' created"', 'success' );
			break;
		case "createtype":
			$typename = getPost('typename');
			$newtype = getPost('newtype');
			if($typename === false) {
				if(!$newtype) {
					$display->setMessage( 'Not all fields have been filled' );
				}
				$display->prepare('createtype' );
				$display->present();
			}
			if($config->getValue('types',$typename) !== false) {
				$display->setMessage('A type with this name already exists' );
				$display->prepare('createtype' );
				$display->present();
			}
			$config->config['types'][$typename] = array();
			$config->saveConfig();
			$display->setMessage('Type "'.$typename.' created"', 'success' );
			break;
		case "editroom":
			$oldroom = getPost('oldroom');
			$roomname = getPost('roomname');
			if($oldroom === $roomname) {
				break;
			}
			if($roomname === false) {
				$display->setMessage('Not all fields have been filled' );
				$display->setKey('roomname', $oldroom );
				$display->prepare('editroom' );
				$display->present();
			}
			if($config->getValue('rooms',$oldroom) !== false) {
				$config->config['rooms'][$roomname] = $config->config['rooms'][$oldroom];
				unset($config->config['rooms'][$oldroom]);
				$config->saveConfig();
				$display->setMessage('Room "'.$roomname.'" saved.', 'success' );
			}
			break;
		case "deleteroom" :
			$room = getPost('room');
			if($config->getValue('rooms',$room) !== false) {
				unset($config->config['rooms'][$room]);
				$config->saveConfig();
				$display->setMessage('Room "'.$room.'" deleted.', 'success' );
			}
			break;
		case "deletetype" :
			$type = getPost('type');
			if($config->getValue('types',$type) !== false) {
				unset($config->config['types'][$type]);
				$config->saveConfig();
				$display->setMessage('Type "'.$type.'" deleted.', 'success' );
			}
			break;
		case "edittype":
			$oldtype = getPost('oldtype');
			$type = getPost('typename');
			if($oldtype === $type) {
				break;
			}
			if($type === false) {
				$display->setMessage('Not all fields have been filled' );
				$display->setKey('typename', $oldtype );
				$display->prepare('edittype' );
				$display->present();
			}
			if($config->getValue('types',$oldtype) !== false) {
				$config->config['types'][$type] = $config->config['types'][$oldtype];
				unset($config->config['types'][$oldtype]);
				$config->saveConfig();
				$display->setMessage('Type "'.$type.'" saved.', 'success' );
			} else echo"ok";
			break;
		case "showeditroom":
			$roomname = getPost('room');
			if($roomname === false) {
				break;
			}
			$display->setKey('roomname', $roomname );
			$display->prepare('editroom' );
			$display->present();

			break;
		case "showedittype":
			$type = getPost('type');
			if($type === false) {
				break;
			}
			$display->setKey('typename', $type );
			$display->prepare('edittype' );
			$display->present();

			break;
		case "savedevice":
			$deviceId = getPost('deviceid');
			$type = getPost('type');
			$room = getPost('room');
			if($deviceId !== false) {
				if($type !== false) {
					if(!in_array($deviceId, $config->config['types'][$type])) {
						$config->config['types'][ $type ][] = $deviceId;
						foreach($config->config['types'] as $k=>$v) {
							if($k!==$type) {
								if (in_array($deviceId,$v)) {
									foreach ($v as $key=>$id) {
										if($id === $deviceId) {
											unset( $config->config['types'][ $k ][ $key ] );
										}
									}
								}
							}
						}
					}
				}

				if($room !== false) {
					// first clear all entries for this device
					foreach($config->config['rooms'] as $k=>$v) {
						if (in_array($deviceId,$v)) {
							foreach ($v as $key=>$id) {
								if($id === $deviceId) {
									unset( $config->config['rooms'][ $k ][ $key ] );
								}
							}
						}
					}
					// now add to all rooms needed
					foreach ($room as $single) {
						$single = trim($single);
						$config->config['rooms'][ $single ][] = $deviceId;
					}
				}

				$config->saveConfig();
				$display->setMessage('Device updated', 'success' );
			}
			break;
	}
}

if(!$config->config) {
	$display->prepare('install');
	$display->present();
}
if(!$config->loggedIn) {
	//$display->setMessage('Username or password is incorrect' );
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
	print_r($controller);
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
				'background',
				'deviceId',
				'devicetypeName',
				'devicename',
				'cardtype',
				'deviceRoom',
				'deviceType'
			);
			$with = array(
				$background,
				$deviceId,
				$devicetypeName,
				$devicename,
				$cardtype,
				$deviceHTML,
				$typeHTML
			);
			$cards .= $display->build( 'card', $replace, $with );
		}

		//$body = "<pre>" .  print_r($config->config, true)."</pre>" ;
		//$display->setMessage('All setup. Now build pages..','success');
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


/*
$body = file_get_contents('templates/index.tpl');
$login = file_get_contents('templates/login.tpl');
$replace = array('%%title%%','%%body%%');
$with = array('DB HomeKit',$login);
$body = str_replace($replace,$with,$body);
echo $body;
*/
?>