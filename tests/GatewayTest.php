<?php

require_once('tests/TestConfiguration.php');

date_default_timezone_set('UTC');

class GatewayTest extends PHPUnit_Framework_TestCase
{
    public function testSuccessfulGateway(){
    	$api_key = TestConfiguration::getApiKey();
    	$api_secret = TestConfiguration::getApiSecret();
		$gateway = new App55_Gateway(App55_Environment::$sandbox, $api_key, $api_secret);
		
		$this->assertEquals($api_key, $gateway->apiKey);
		$this->assertEquals($api_secret, $gateway->apiSecret);
		
		return $gateway;
	}
	
	/**
     * @depends testSuccessfulGateway
     */
	public function testUserSuccessful($gateway) {
		$email = 'example.' . gmdate('YmdHis') . '@example.com';
		$phone = '0123 456 7890';
		$password = 'pa55word';
		$password_confirm = 'pa55word';
	
		$response = $gateway->createUser(new App55_User(array(
			'email' => $email,
			'phone' => $phone,
			'password' => $password,
			'password_confirm' => $password_confirm
		)));
		
		$args = $this->readAttribute($response, 'args');
		$args = $this->readAttribute($args['user'], 'args');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertEquals($email, $args['email']);
		$this->assertEquals($phone, $args['phone']);
		$this->assertEquals($password, $args['password']);
		$this->assertEquals($password_confirm, $args['password_confirm']);
	}
    
    /**
     * @depends testSuccessfulGateway
     */
    public function testCreateCardSuccessful($gateway) {
    	$response = $gateway->createCard(
	        new App55_User(array(
	            'id' => '1134'
	        )),
	        new App55_Card(array(
	            'address' => new App55_Address(array(
	                'street' => '8 Exchange Quay',
	                'city' => 'Manchester',
	                'postal_code' => 'M5 3EJ',
	                'country' => 'GB'
	            )),
	            'holder_name' => 'Robin Crorie',
	            'number' => '4111111111111111',
	            'expiry' => '07/2014',
	            'security_code' => '240'
	        ))
	    );
	    
	    $args = $this->readAttribute($response, 'args');
		$user_response = $this->readAttribute($args['user'], 'args');
		$card_response = $this->readAttribute($args['card'], 'args');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertEquals('1134', $user_response['id']);
		$this->assertEquals('Robin Crorie', $card_response['holder_name']);
		$this->assertEquals('4111111111111111', $card_response['number']);
    }
        
    /**
     * @depends testSuccessfulGateway
     */
    public function testListCardsSuccessful($gateway) {
	    $response = $gateway->listCards(
			new App55_User(array(
				'id' => '1134'
			))
		);
		
	    $args = $this->readAttribute($response, 'args');
		$user_response = $this->readAttribute($args['user'], 'args');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertEquals('1134', $user_response['id']);
    }
    
    /**
     * @depends testSuccessfulGateway
     */
    public function testDeleteCardSuccessful($gateway) {
    	
	    $response = $gateway->deleteCard(
			new App55_User(array(
				'id' => '1134'
			)),
			new App55_Card(array(
				'token' => '22f'
			))
		);
	    
	    $args = $this->readAttribute($response, 'args');
		$user_response = $this->readAttribute($args['user'], 'args');
		$token = $this->readAttribute($response, 'token');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertEquals('1134', $user_response['id']);
		$this->assertEquals('22f', $token);
    }
        
    /**
     * @depends testSuccessfulGateway
     */
    public function testCreateTransactionSuccessful($gateway) {
	    
	    $response = $gateway->createTransaction(
			new App55_User(array(
				'id' => '1134'
			)),
			new App55_Card(array(
				'token' => '22f'
			)),
			new App55_Transaction(array(
				'amount' => '0.10',
				'currency' => 'GBP'
			))
		);
		
		$args = $this->readAttribute($response, 'args');
		$user_response = $this->readAttribute($args['user'], 'args');
		$card_response = $this->readAttribute($args['card'], 'args');
		$transaction_response = $this->readAttribute($args['transaction'], 'args');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertEquals('1134', $user_response['id']);
		$this->assertEquals('22f', $card_response['token']);
		$this->assertEquals('0.10', $transaction_response['amount']);
		$this->assertEquals('GBP', $transaction_response['currency']);
    }
    
    /**
     * @depends testSuccessfulGateway
     */
    public function testTransactionCommitSuccessful($gateway) {
	    $response = $gateway->commitTransaction(
			new App55_Transaction(array(
				'id' => '1134'
			))
		);
		
		$args = $this->readAttribute($response, 'args');
		$transaction_response = $this->readAttribute($args['transaction'], 'args');
		$id_response = $this->readAttribute($response, 'id');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertObjectHasAttribute('id', $response);
		$this->assertEquals('1134', $id_response);
    }
    
    /**
     * @depends testSuccessfulGateway
     */
    public function testCreateScheduleOnceSuccessful($gateway) {
	    $startDate = date('Y-m-d');
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'start' => $startDate
			)),
			new App55_User(array(
				'id' => '1134'
			)),
			new App55_Card(array(
				'token' => '22f'
			)),
			new App55_Transaction(array(
				'amount' => '0.10',
				'currency' => 'GBP'
			))
		);
		
		$args = $this->readAttribute($response, 'args');
		$user_response = $this->readAttribute($args['user'], 'args');
		$card_response = $this->readAttribute($args['card'], 'args');
		$transaction_response = $this->readAttribute($args['transaction'], 'args');
		$schedule_response = $this->readAttribute($args['schedule'], 'args');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertEquals('1134', $user_response['id']);
		$this->assertEquals('22f', $card_response['token']);
		$this->assertEquals('0.10', $transaction_response['amount']);
		$this->assertEquals('GBP', $transaction_response['currency']);
		$this->assertEquals('ONCE', $schedule_response['time_unit']);
		$this->assertEquals($startDate, $schedule_response['start']);
    }
        
    /**
     * @depends testSuccessfulGateway
     */
    public function testCreateScheduleDailySuccessful($gateway) {
    	$startDate = date('Y-m-d');
    	$endDate = date("Y-m-d",strtotime("+1 week"));
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'DAILY',
				'units' => '1',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => '1134'
			)),
			new App55_Card(array(
				'token' => '22f'
			)),
			new App55_Transaction(array(
				'amount' => '0.10',
				'currency' => 'GBP'
			))
		);
		
		$args = $this->readAttribute($response, 'args');
		$user_response = $this->readAttribute($args['user'], 'args');
		$card_response = $this->readAttribute($args['card'], 'args');
		$transaction_response = $this->readAttribute($args['transaction'], 'args');
		$schedule_response = $this->readAttribute($args['schedule'], 'args');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertEquals('1134', $user_response['id']);
		$this->assertEquals('22f', $card_response['token']);
		$this->assertEquals('0.10', $transaction_response['amount']);
		$this->assertEquals('GBP', $transaction_response['currency']);
		$this->assertEquals('DAILY', $schedule_response['time_unit']);
		$this->assertEquals($startDate, $schedule_response['start']);
		$this->assertEquals($endDate, $schedule_response['end']);
		$this->assertEquals('1', $schedule_response['units']);
    }
    
    /**
     * @depends testSuccessfulGateway
     */
    public function testCreateScheduleWeeklySuccessful($gateway) {
    	$startDate = date("Y-m-d");
    	$endDate = date("Y-m-d",strtotime("+1 week"));
    	$day = 1 + date('w');
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'WEEKLY',
				'units' => '1',
				'day' => $day,
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => '1134'
			)),
			new App55_Card(array(
				'token' => '22f'
			)),
			new App55_Transaction(array(
				'amount' => '0.10',
				'currency' => 'GBP'
			))
		);
		
		$args = $this->readAttribute($response, 'args');
		$user_response = $this->readAttribute($args['user'], 'args');
		$card_response = $this->readAttribute($args['card'], 'args');
		$transaction_response = $this->readAttribute($args['transaction'], 'args');
		$schedule_response = $this->readAttribute($args['schedule'], 'args');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertEquals('1134', $user_response['id']);
		$this->assertEquals('22f', $card_response['token']);
		$this->assertEquals('0.10', $transaction_response['amount']);
		$this->assertEquals('GBP', $transaction_response['currency']);
		$this->assertEquals('WEEKLY', $schedule_response['time_unit']);
		$this->assertEquals($startDate, $schedule_response['start']);
		$this->assertEquals($endDate, $schedule_response['end']);
		$this->assertEquals($day, $schedule_response['day']);
		$this->assertEquals('1', $schedule_response['units']);
    }
    
    /**
     * @depends testSuccessfulGateway
     */
    public function testCreateScheduleMonthlySuccessful($gateway) {
    	$startDate = date('Y-m',strtotime("+1 month"))."-01";
    	$endDate = date("Y-m",strtotime("+2 month"))."-01";
    	$day = 1;
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'MONTHLY',
				'units' => '1',
				'day' => $day,
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => '1134'
			)),
			new App55_Card(array(
				'token' => '22f'
			)),
			new App55_Transaction(array(
				'amount' => '0.10',
				'currency' => 'GBP'
			))
		);
		
		$args = $this->readAttribute($response, 'args');
		$user_response = $this->readAttribute($args['user'], 'args');
		$card_response = $this->readAttribute($args['card'], 'args');
		$transaction_response = $this->readAttribute($args['transaction'], 'args');
		$schedule_response = $this->readAttribute($args['schedule'], 'args');
		
		$this->assertObjectHasAttribute('gateway', $response);
		$this->assertEquals('1134', $user_response['id']);
		$this->assertEquals('22f', $card_response['token']);
		$this->assertEquals('0.10', $transaction_response['amount']);
		$this->assertEquals('GBP', $transaction_response['currency']);
		$this->assertEquals('MONTHLY', $schedule_response['time_unit']);
		$this->assertEquals($startDate, $schedule_response['start']);
		$this->assertEquals($endDate, $schedule_response['end']);
		$this->assertEquals($day, $schedule_response['day']);
		$this->assertEquals('1', $schedule_response['units']);
    }
}
?>