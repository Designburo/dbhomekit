<?php
/**
 * Created by  : Designburo.nl
 * Project     : DBHomekit
 * Filename    : classes/io.class.php
 * Description : Class handling HomeWizard communications
 * Date        : 02/09/2018
 * Time        : 22:06
 * Source      : https://github.com/Designburo/dbhomekit
 */

namespace db\home;


class io {

	private $email     = "";
	private $pass      = "";
	private $login_url = 'https://cloud.homewizard.com/account/login';
	private $plugs_url = 'https://plug.homewizard.com/plugs';


	public function __construct($user, $pass) {

		$this->email = $user;
		$this->pass = $pass;

	}

	private function getEmail() {
		return $this->email;
	}

	private function getPass() {
		return sha1( $this->pass );
	}

	public function getLoginUrl() {
		return $this->login_url;
	}

	public function getPlugsUrl() {
		return $this->plugs_url;
	}

	public function createResponse( $status, $msg ) {
		$ret = array();
		$ret['status'] = $status;
		$ret['value'] = $msg;
		return $ret;
	}

	public function setCurlOptions($url) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_USERPWD, $this->getEmail() . ":" . $this->getPass() );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );

		return $ch;
	}

	public function getLoginToken() {
		$ch     = $this->setCurlOptions( $this->getLoginUrl() );
		$output = curl_exec( $ch );
		curl_close( $ch );
		if (!$output) {
			return $this->createResponse( false, curl_error( $ch ) );
		}
		$output = json_decode( $output, true );
		if ( $output['status'] === "failed" ) {
			return $this->createResponse( false, "Incorrect HomeWizard credentials. Answer : " . $output['errorMessage'] );
		}
		return $this->createResponse( true, $output['session'] );
	}

	public function setHeaders( $token ) {
		$headers = array(
			"Accept: */*",
			"Content-Type: application/json; charset=utf-8",
			"X-Session-Token: " . $token
		);

		return $headers;
	}

	public function getAllDevices($controller=false) {
		$token = $this->getLoginToken();
		if($token['status']) {
			$token = $token['value'];
		} else return $token;
		$ch      = $this->setCurlOptions( $this->getPlugsUrl() );
		$headers = $this->setHeaders( $token );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		$output = curl_exec( $ch );
		curl_close( $ch );
		if ( ! $output ) {
			$this->createResponse( false, curl_error( $ch ) );
		}
		$all = json_decode( $output, true );
		if($controller) {
			$controller = array();
			$controller['id'] = $all[0]['id'];
			$controller['identifier'] = $all[0]['identifier'];
			$controller['name'] = $all[0]['name'];
			return $this->createResponse( true, $controller );
		}
		$all = $all[0]['devices'];
		return $this->createResponse( true, $all );
		/*
		$c = array();

		$c['numberOfDevices'] = count($all);
		$i=1;
		foreach ($all as $single) {
			$c[$i.'_id'] = $single['id'];
			$c[$i.'_typeName'] = $single['typeName'];
			$c[$i.'_name'] = $single['name'];
			$c[$i.'_code'] = $single['code'];
			$c[$i.'_iconUrl'] = $single['iconUrl'];
			$i++;
		}
		return $this->createResponse( true, $c );
		*/
	}

	public function getPlugInfo( $token, $smartswitch, $apparaat, $list = false ) {
		$ch      = $this->setCurlOptions( $this->getPlugsUrl() );
		$headers = $this->setHeaders( $token );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		$output = curl_exec( $ch );
		curl_close( $ch );
		if ( ! $output ) {
			$this->createOutput( false, curl_error( $ch ) );
		}
		$output = json_decode( $output, true );
		foreach ( $output as $sp ) {
			if ( $sp['name'] == $smartswitch ) {
				$plug = $sp['id'];
			}
		}
		if ( $list ) {
			return $output['0']['devices'];
		}
		foreach ( $output['0']['devices'] as $sd ) {
			if ( $sd['name'] == $apparaat ) {
				$device = $sd['id'];
			}
		}
		$ret              = array();
		$ret['device_id'] = $device;
		$ret['plug_id']   = $plug;

		return $ret;
	}

	public function doAction( $url, $token, $action  ) {
		$headers = $this->setHeaders($token);
		$data        = array( "action" => $action );
		$data_string = json_encode( $data );
		//echo $data_string;
		$ch         = curl_init();
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		$output = curl_exec( $ch );
		$cinfo   = curl_getinfo( $ch );

		if ( ! $output ) {
			$this->createResponse( false, curl_error( $ch ) );
		}
		curl_close( $ch );
		return $this->createResponse( true, $output );
	}
}