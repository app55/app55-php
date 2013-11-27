<?php

class NonAbstract extends App55_Message {
	public function toArray($apiKey = null, $apiSecret = null){
		return parent::toArray($apiKey, $apiSecret);
	}
	
	public function toDotted($array, $path = null){
		return parent::toDotted($array, $path);
	}
}

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testToArrayKeyAndSecretSuccessful() {
	    $apiKey = "uiiWxnjBadFGuVDsK1LJHhLCcu8sbLb1";
    	$apiSecret = "uiiWxnjBadFGuVDsK1LJHhLCcu8sbLb2";
    	
    	$args = array();
    	
    	$obj = new NonAbstract($args);
    	$result = $obj->toArray($apiKey, $apiSecret);
    	
    	$this->assertArrayHasKey('api_key', $result);
    	$this->assertArrayHasKey('ts', $result);
    	$this->assertArrayHasKey('sig', $result);
    }
    
    public function testToArrayNoKeySuccessful() {
    	$apiKey = null;
    	$apiSecret = "uiiWxnjBadFGuVDsK1LJHhLCcu8sbLb2";
    	
    	$args = array();
    	
    	$obj = new NonAbstract($args);
    	$result = $obj->toArray($apiKey, $apiSecret);
    	
    	$this->assertArrayNotHasKey('api_key', $result);
    	$this->assertArrayHasKey('ts', $result);
    	$this->assertArrayHasKey('sig', $result);
    }
    
    public function testToArrayKeySuccessful() {
	    $apiKey = "uiiWxnjBadFGuVDsK1LJHhLCcu8sbLb1";
	    $apiSecret = null;
    	
    	$args = array();
    	
    	$obj = new NonAbstract($args);
    	$result = $obj->toArray($apiKey, $apiSecret);
    	
    	$this->assertArrayHasKey('api_key', $result);
    	$this->assertArrayNotHasKey('ts', $result);
    	$this->assertArrayNotHasKey('sig', $result);
    }
    
    public function testToDottedSuccessful() {
	    $testArray = array(
	    	'key1' => array(
	    		'2key1' => '2value1',
	    		'2key2' => '2value2'
	    	),
	    	'key2' => 'value2'
	    );
	    $expectedArray = array(
	    	'key1.2key1' => '2value1',
	    	'key1.2key2' => '2value2',
		    'key2' => 'value2'
	    );
    	
    	$args = array();
    	
    	$obj = new NonAbstract($args);
    	$result = $obj->toDotted($testArray);
    	
    	$this->assertEquals($expectedArray, $result);
    }
    
    public function testToString() {
	    $args = array(
	    	'arg1' => 'value1',
	    	'arg2' => 'value2'
	    );
	    
	    $expectedString = 'NonAbstract(arg1 => value1, arg2 => value2)';
	    
	    $obj = new NonAbstract($args);
	    $result = $obj->__toString();
	    
	    $this->assertEquals($expectedString, $result);
    }
}
?>