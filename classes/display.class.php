<?php
/**
 * Created by PhpStorm.
 * User: Charlot
 * Date: 24/08/2018
 * Time: 13:42
 */

namespace db\home;


class display {

	private $content = '';
	private $vars =  array();

	public function __construct($loggedIn) {
		$this->setKey('title', 'DB Homekit' );
		$this->setKey('message', '' );
		if($loggedIn) {
			$this->setKey( 'nav', $this->nav( 'DASHBOARD' ) );
		} else $this->setKey( 'nav', '' );
	}


	public function setMessage($msg, $type="warning") {
		$add = 'class="uk-alert-'.$type.'"';
		$val='<div '.$add.' uk-alert><a class="uk-alert-close" uk-close></a>'.$msg.'</div>';
		$this->setKey('message', $val );
	}

	public function setKey($k, $v) {
		$this->vars['%%'.$k.'%%']=$v;
	}

	public function getKeys() {
		return array_keys($this->vars);
	}

	public function nav($active) {
		$nav = array();
		$menu = '<nav class="uk-navbar-container uk-margin" uk-navbar><div class="uk-navbar-left"><a class="uk-navbar-item uk-logo" href="index.php">DB HomeKit</a><ul class="uk-navbar-nav">';
		$nav[0]['DASHBOARD']='index.php';
		$nav[1]['ROOMS']='index.php?display=rooms';
		$nav[2]['TYPES']='index.php?display=types';
		$nav[3]['IFTTT']='index.php?display=ifttt';
		$nav[4]['REFETCH DATABASE']='index.php?reload=true';
		foreach ($nav as $item) {
			$k = key($item);
			if($k === $active) {
				$class = ' class="uk-active"';
			} else $class="";
			if ($k === 'REFETCH DATABASE') {
				$menu .= '<li'.$class.'><a onClick="alert(\'Wait for the page re-load, do not repeatedly press refetch database !\');" href="'.$item[$k].'">'.$k.'</a></li>';
			} else $menu .= '<li'.$class.'><a href="'.$item[$k].'">'.$k.'</a></li>';
		}
		$menu .= '</ul></div></nav>';
		return $menu;
	}

	public function getValues() {
		return array_values($this->vars);
	}

	public function build($template,$k,$v) {
		foreach($k as &$value) {
			$value = '%%'.$value.'%%';
		}
		unset ($value);
		if( file_exists('templates/'.$template.'.tpl')) {
			$template = 'templates/'.$template.'.tpl';
		} else {
			$template = 'templates/notfound.tpl';
		}
		$template = file_get_contents($template);
		$body = str_replace( $k, $v, $template );
		return $body;
	}

	public function prepare( $screen ) {
		$body = file_get_contents('templates/index.tpl');
		if( file_exists('templates/'.$screen.'.tpl')) {
			$screen = 'templates/'.$screen.'.tpl';
		} else {
			$screen = 'templates/notfound.tpl';
		}
		$template = file_get_contents($screen);
		$body = str_replace('%%body%%',$template,$body);
		$body = str_replace($this->getKeys(),$this->getValues(),$body);
		$this->content = $body;
	}

	public function present() {
		echo $this->content;
		die();
	}


}