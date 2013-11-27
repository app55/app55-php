<?php

class DAOTest extends PHPUnit_Framework_TestCase
{
	public function testSetUpDao() {
		$test_args = array(
			'arg1' => 'value1',
			'arg2' => 'value2',
			'arg3' => 'value3',
			'arg4' => 'value4'
		);
		$obj = new App55_DAO($test_args);
		return $obj;
	}
	
	/**
	 * @depends testSetUpDao
	 */
	public function testGetSuccessful($obj) {
		$result = $obj->__get('arg1');
		$this->assertEquals('value1', $result);
	}
	
	/**
	 * @depends testSetUpDao
	 */
	public function testIssetSuccessful($obj) {
		$result = $obj->__isset('arg1');
		$this->assertEquals(true, $result);
	}
	
	/**
	 * @depends testSetUpDao
	 */
	public function testSetSuccessful($obj) {
		$result = $obj->__get('arg1');
		$this->assertEquals('value1', $result);
		$obj->__set('arg1', 'newValue1');
		$result = $obj->__get('arg1');
		$this->assertEquals('newValue1', $result);
	}
	
	/**
	 * @depends testSetUpDao
	 */
	public function testUnsetSuccessful($obj) {
		$result = $obj->__get('arg2');
		$this->assertEquals('value2', $result);
		$obj->__unset('arg2');
		$result = $obj->__isset('arg2');
		$this->assertEquals(false, $result);
	}
	
	/**
	 * @depends testSetUpDao
	 */
	public function testToStringSuccessful($obj) {
		$expectedString = 'App55_DAO(arg1 => newValue1, arg3 => value3, arg4 => value4)';
		$result = $obj->__toString();
		$this->assertEquals($expectedString, $result);
	}
	
	/**
	 * @depends testSetUpDao
	 */
	public function testToArraySuccessful($obj) {
		$expectedArray = array(
			'arg1' => 'newValue1',
			'arg3' => 'value3',
			'arg4' => 'value4'
		);
		$result = $obj->toArray();
		$this->assertEquals($expectedArray, $result);
	}
}
?>