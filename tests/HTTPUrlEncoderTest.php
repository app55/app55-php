<?php

class HTTPUrlEncoderTest extends PHPUnit_Framework_TestCase
{
	public function testEncodeSuccessful() {
		$test_array = array(
			'value1' => 'test1',
			'value2' => 'test2',
			'value3' => 'test3',
			'value4' => 'test4'
		);
		
		$abstractClass = $this->getMockForAbstractClass('App55_HttpUrlEncoder');
		$this->assertSame("value1=test1&value2=test2&value3=test3&value4=test4", $abstractClass->encode($test_array));
	}
	
	public function testEncodeSpaceSuccessful() {
		$test_array = array(
			'space' => 'test space'
		);
		
		$abstractClass = $this->getMockForAbstractClass('App55_HttpUrlEncoder');
		$this->assertSame("space=test%20space", $abstractClass->encode($test_array));
	}
	
	public function testEncodeExclamationSuccessful() {
		$test_array = array(
			'space' => 'test!mark'
		);
		$abstractClass = $this->getMockForAbstractClass('App55_HttpUrlEncoder');
		$this->assertSame("space=test!mark", $abstractClass->encode($test_array));
	}
	
	public function testEncodeStarSuccessful() {
		$test_array = array(
			'space' => 'test*mark'
		);
		$abstractClass = $this->getMockForAbstractClass('App55_HttpUrlEncoder');
		$this->assertSame("space=test*mark", $abstractClass->encode($test_array));
	}
	
	public function testEncodeQuoteSuccessful() {
		$test_array = array(
			'space' => "test'mark"
		);
		$abstractClass = $this->getMockForAbstractClass('App55_HttpUrlEncoder');
		$this->assertSame("space=test'mark", $abstractClass->encode($test_array));
	}
	
	public function testEncodeOpenBracketSuccessful() {
		$test_array = array(
			'space' => "test(mark"
		);
		$abstractClass = $this->getMockForAbstractClass('App55_HttpUrlEncoder');
		$this->assertSame("space=test(mark", $abstractClass->encode($test_array));
	}
	
	public function testEncodeCloseBracketSuccessful() {
		$test_array = array(
			'space' => "test)mark"
		);
		$abstractClass = $this->getMockForAbstractClass('App55_HttpUrlEncoder');
		$this->assertSame("space=test)mark", $abstractClass->encode($test_array));
	}
	
	public function testEncodeOthersSuccessful() {
		$test_array = array(
			'space' => "test@mark",
			'space1' => "£",
			'space2' => "$",
			'space3' => "%",
			'space4' => "^",
			'space5' => "&",
			'space6' => "*",
			'space7' => "_",
			'space8' => "-",
			'space9' => "=",
			'space10' => "+",
			'space11' => "{",
			'space12' => "}",
			'space13' => ":",
			'space14' => ";",
			'space15' => '"',
			'space16' => "|",
			'space17' => "~",
			'space18' => "`",
			'space19' => "?",
			'space20' => ">",
			'space21' => "<"
		);
		$expectedEncoding = "space=test%40mark&space1=%C2%A3&space2=%24&space3=%25&space4=%5E&space5=%26&space6=*&space7=_&space8=-&space9=%3D&space10=%2B&space11=%7B&space12=%7D&space13=%3A&space14=%3B&space15=%22&space16=%7C&space17=~&space18=%60&space19=%3F&space20=%3E&space21=%3C";
		
		$abstractClass = $this->getMockForAbstractClass('App55_HttpUrlEncoder');
		$this->assertSame($expectedEncoding, $abstractClass->encode($test_array));
	}
}
?>