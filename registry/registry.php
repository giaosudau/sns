<?php
class Registry{
	private $objects;
	private $settings;
	public function __contruct(){
		
	}
	public function createAndStoreObject($object, $key){
		require_once($object . '.class.php');
		$this->objects[$key] = new $object($this);
	}
	public function storeAndSetting($setting, $key){
		$this->settings[$key] = $setting;
	}
	public function getSetting($key){
		return $this->settings[$key];
	}
}
?>
