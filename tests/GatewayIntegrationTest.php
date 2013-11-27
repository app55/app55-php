<?php

require_once('tests/TestConfiguration.php');

date_default_timezone_set('UTC');

class GatewayIntegrationTest extends PHPUnit_Framework_TestCase
{
	/**
     * @expectedException App55_AuthenticationException
     */
    public function testInvalidApiKey() {
    	$gateway = new App55_Gateway(App55_Environment::$sandbox, "Key", TestConfiguration::getApiSecret());
    	
    	$this->testUserSuccessful($gateway);
    }
    
    /**
     * @expectedException App55_AuthenticationException
     */
    public function testInvalidApiSecret() {
    	$gateway = new App55_Gateway(App55_Environment::$sandbox, TestConfiguration::getApiKey(), "Secret");
    	
    	$this->testUserSuccessful($gateway);
    }
    
    public function testSuccessfulGateway(){
		$gateway = new App55_Gateway(App55_Environment::$sandbox, TestConfiguration::getApiKey(), TestConfiguration::getApiSecret());
		
		$this->assertEquals(TestConfiguration::getApiKey(), $gateway->apiKey);
		$this->assertEquals(TestConfiguration::getApiSecret(), $gateway->apiSecret);
		
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
		)))->send();
		
		return $response;
	}
	
	/**
     * @depends testSuccessfulGateway
     * @expectedException App55_ValidationException
     */
	public function testUserPasswordNoMatch($gateway) {
		$email = 'example.' . gmdate('YmdHis') . '@example.com';
		$phone = '0123 456 7890';
		$password = 'pa55word';
		$password_confirm = 'different';
	
		$response = $gateway->createUser(new App55_User(array(
			'email' => $email,
			'phone' => $phone,
			'password' => $password,
			'password_confirm' => $password_confirm
		)))->send();
	}
	
	/**
     * @depends testSuccessfulGateway
     * @expectedException App55_ValidationException
     */
	public function testUserInvalidEmail($gateway) {
		$email = 'example.' . gmdate('YmdHis') . 'example.com';
		$phone = '01234567890';
		$password = 'pa55word';
		$password_confirm = 'pa55word';
	
		$response = $gateway->createUser(new App55_User(array(
			'email' => $email,
			'phone' => $phone,
			'password' => $password,
			'password_confirm' => $password_confirm
		)))->send();
	}
	
	/**
	 * @depends testSuccessfulGateway
     * @expectedException App55_AuthenticationException
     */
    public function testCreateCardInvalidUserId($gateway) {
    	
    	$response = $gateway->createCard(
	        new App55_User(array(
	            'id' => 3
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
	    )->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @expectedException App55_AuthenticationException
     */
    public function testCreateCardInvalidExpiry($gateway) {
    	
    	$response = $gateway->createCard(
	        new App55_User(array(
	            'id' => 3
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
	            'expiry' => '072014',
	            'security_code' => '240'
	        ))
	    )->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateCardInvalidCountry($gateway, $user_response) {
    	$response = $gateway->createCard(
	        new App55_User(array(
	            'id' => $user_response->user->id
	        )),
	        new App55_Card(array(
	            'address' => new App55_Address(array(
	                'street' => '8 Exchange Quay',
	                'city' => 'Manchester',
	                'postal_code' => 'M5 3EJ',
	                'country' => 'WRONG'
	            )),
	            'holder_name' => 'Robin Crorie',
	            'number' => '4111111111111111',
	            'expiry' => '07/2014',
	            'security_code' => '240'
	        ))
	    )->send();
	    
	    return $response;
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     */
    public function testCreateCardSuccessful($gateway, $user_response) {
    	$response = $gateway->createCard(
	        new App55_User(array(
	            'id' => $user_response->user->id
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
	    )->send();
	    
	    return $response;
    }
    
    /**
     * @depends testSuccessfulGateway
     * @expectedException App55_AuthenticationException
     */
    public function testListCardsInvalidId($gateway) {
	    $response = $gateway->listCards(
			new App55_User(array(
				'id' => ''
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     */
    public function testListCardsSuccessful($gateway, $user_response) {
	    $response = $gateway->listCards(
			new App55_User(array(
				'id' => $user_response->user->id
			))
		)->send();
		
		return $response->cards;
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testCreateCardSuccessful
     * @expectedException App55_AuthenticationException
     */
    public function testDeleteCardInvalidUserId($gateway, $card) {
    	
	    $response = $gateway->deleteCard(
			new App55_User(array(
				'id' => ''
			)),
			new App55_Card(array(
				'token' => $card->card->token
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @expectedException App55_ResourceException
     */
    public function testDeleteCardInvalidToken($gateway, $user_response) {
    	
	    $response = $gateway->deleteCard(
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => ''
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     */
    public function testDeleteCardSuccessful($gateway, $user_response) {
    	$card = $this->testCreateCardSuccessful($gateway, $user_response)->card;
    	
    	$this->assertCount(2, $this->testListCardsSuccessful($gateway, $user_response));
    	
	    $response = $gateway->deleteCard(
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->token
			))
		)->send();

		$this->assertCount(1, $this->testListCardsSuccessful($gateway, $user_response));
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testCreateCardSuccessful
     * @expectedException App55_AuthenticationException
     */
    public function testCreateTransactionInvalidUserId($gateway, $card) {
	    
	    $response = $gateway->createTransaction(
			new App55_User(array(
				'id' => ''
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => '0.10',
				'currency' => 'GBP'
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @expectedException App55_CardException
     */
    public function testCreateTransactionInvalidToken($gateway, $user_response) {
	    
	    $response = $gateway->createTransaction(
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => ''
			)),
			new App55_Transaction(array(
				'amount' => '0.10',
				'currency' => 'GBP'
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @expectedException App55_CardException
     */
    public function testCreateTransactionInvalidAmount($gateway, $user_response, $card) {
	    
	    $response = $gateway->createTransaction(
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => '0.0',
				'currency' => 'GBP'
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateTransactionInvalidCurrency($gateway, $user_response, $card) {
	    
	    $response = $gateway->createTransaction(
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => '0.10',
				'currency' => ''
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     */
    public function testCreateTransactionSuccessful($gateway, $user_response, $card) {
	    
	    $response = $gateway->createTransaction(
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => '0.10',
				'currency' => 'GBP'
			))
		)->send();
		
		$this->assertNotEmpty($response->transaction->id);
		
		return $response;
    }
    
    /**
     * @depends testSuccessfulGateway
     * @expectedException App55_ResourceException
     */
    public function testTransactionCommitInvalidId($gateway) {
	    $response = $gateway->commitTransaction(
			new App55_Transaction(array(
				'id' => ''
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testCreateTransactionSuccessful
     */
    public function testTransactionCommitSuccessful($gateway, $transaction) {
	    $response = $gateway->commitTransaction(
			new App55_Transaction(array(
				'id' => $transaction->transaction->id
			))
		)->send();
		
		$this->assertEquals('succeeded', $response->transaction->code);
		
		return $response;
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleInvalidEndDateFormat($gateway, $user_response, $card, $transaction) {
	    $startDate = date('Y-m-d');
    	$endDate = date("Y/m/d",strtotime("+1 week"));
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleInvalidStartDateFormat($gateway, $user_response, $card, $transaction) {
	    $startDate = date('Y/m/d');
    	$endDate = date("Y-m-d",strtotime("+1 week"));
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleNoUnit($gateway, $user_response, $card, $transaction) {
	    $startDate = date('Y-m-d');
    	$endDate = date("Y-m-d",strtotime("+1 week"));
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => '',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_AuthenticationException
     */
    public function testCreateScheduleNoUserId($gateway, $card, $transaction) {
	    $startDate = date('Y-m-d');
    	$endDate = date("Y-m-d",strtotime("+1 week"));
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'start' => $startDate,
			)),
			new App55_User(array(
				'id' => ''
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleNoCardToken($gateway, $user_response, $transaction) {
	    $startDate = date('Y-m-d');
    	$endDate = date("Y-m-d",strtotime("+1 week"));
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'start' => $startDate,
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => ''
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleInvalidTransactionAmount($gateway, $user_response, $card, $transaction) {
	    $startDate = date('Y-m-d');
    	$endDate = date("Y-m-d",strtotime("+1 week"));
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'start' => $startDate,
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => '0',
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ServerException
     */
    public function testCreateScheduleOnceNoStartDateSpecified($gateway, $user_response, $card, $transaction) {
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE'
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleOnceUnitsSpecified($gateway, $user_response, $card, $transaction) {
	    $startDate = date('Y-m-d');
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'units' => '1',
				'start' => $startDate,
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleOnceDaySpecified($gateway, $user_response, $card, $transaction) {
	    $startDate = date('Y-m-d');
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'day' => '1',
				'start' => $startDate,
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleOnceStartDateInPast($gateway, $user_response, $card, $transaction) {
	    $startDate = date("Y-m-d",strtotime("-1 week"));
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'start' => $startDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     */
    public function testCreateScheduleOnceSuccessful($gateway, $user_response, $card, $transaction) {
	    $startDate = date('Y-m-d');
	    
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'ONCE',
				'start' => $startDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
        
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleDailyEndDateInPast($gateway, $user_response, $card, $transaction) {
    	$startDate = date("Y-m-d");
    	$endDate = date("Y-m-d",strtotime("-1 week"));
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'DAILY',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleDailyStartDateInPast($gateway, $user_response, $card, $transaction) {
    	$startDate = date("Y-m-d",strtotime("-1 week"));
    	$endDate = date("Y-m-d",strtotime("+1 week"));
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'DAILY',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleDailyDaySpecified($gateway, $user_response, $card, $transaction) {
    	$startDate = date('Y-m-d');
    	$endDate = date("Y-m-d",strtotime("+1 week"));
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'DAILY',
				'day' => '1',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     */
    public function testCreateScheduleDailyNoEndDate($gateway, $user_response, $card, $transaction) {
    	$startDate = date('Y-m-d');
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'DAILY',
				'units' => '1',
				'start' => $startDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ServerException
     */
    public function testCreateScheduleDailyNoStartDate($gateway, $user_response, $card, $transaction) {
    	$endDate = date("Y-m-d",strtotime("+1 week"));
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'DAILY',
				'units' => '1',
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     */
    public function testCreateScheduleDailyNoUnits($gateway, $user_response, $card, $transaction) {
    	$startDate = date('Y-m-d');
    	$endDate = date("Y-m-d",strtotime("+1 week"));
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'DAILY',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     */
    public function testCreateScheduleDailySuccessful($gateway, $user_response, $card, $transaction) {
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
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleWeeklyEndDateNotOnDay($gateway, $user_response, $card, $transaction) {
    	$startDate = date("Y-m-d");
    	$endDate = date("Y-m-d",strtotime("+1 day"));
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
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleWeeklyStartDateNotOnDay($gateway, $user_response, $card, $transaction) {
    	$startDate = date("Y-m-d");
    	$endDate = date("Y-m-d",strtotime("+1 week"));
    	$day = 2 + date('w');
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'WEEKLY',
				'units' => '1',
				'day' => $day,
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleWeeklyDayNotSpecified($gateway, $user_response, $card, $transaction) {
    	$startDate = date('Y-m-d');
    	$endDate = date("Y-m-d",strtotime("+1 week"));
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'WEEKLY',
				'units' => '1',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     */
    public function testCreateScheduleWeeklySuccessful($gateway, $user_response, $card, $transaction) {
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
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleMonthlyUnitsNotSpecified($gateway, $user_response, $card, $transaction) {
    	$startDate = date('Y-m',strtotime("+1 month"))."-01";
    	$endDate = date("Y-m",strtotime("-1 month"))."-01";
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'MONTHLY',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleMonthlyEndDateNotSpecified($gateway, $user_response, $card, $transaction) {
    	$startDate = date('Y-m',strtotime("+1 month"))."-01";
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'MONTHLY',
				'units' => '1',
				'start' => $startDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleMonthlyStartDateNotSpecified($gateway, $user_response, $card, $transaction) {
    	$endDate = date("Y-m",strtotime("-1 month"))."-01";
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'MONTHLY',
				'units' => '1',
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleMonthlyDayNotSpecified($gateway, $user_response, $card, $transaction) {
    	$startDate = date('Y-m',strtotime("+1 month"))."-01";
    	$endDate = date("Y-m",strtotime("-1 month"))."-01";
    	
	    $response = $gateway->createSchedule(
			new App55_Schedule(array(
				'time_unit' => 'MONTHLY',
				'units' => '1',
				'start' => $startDate,
				'end' => $endDate
			)),
			new App55_User(array(
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleMonthlyEndDateInPast($gateway, $user_response, $card, $transaction) {
    	$startDate = date('Y-m',strtotime("+1 month"))."-01";
    	$endDate = date("Y-m",strtotime("-1 month"))."-01";
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
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     * @expectedException App55_ValidationException
     */
    public function testCreateScheduleMonthlyStartDateInPast($gateway, $user_response, $card, $transaction) {
    	$startDate = date('Y-m',strtotime("-1 month"))."-01";
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
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
    
    /**
     * @depends testSuccessfulGateway
     * @depends testUserSuccessful
     * @depends testCreateCardSuccessful
     * @depends testCreateTransactionSuccessful
     */
    public function testCreateScheduleMonthlySuccessful($gateway, $user_response, $card, $transaction) {
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
				'id' => $user_response->user->id
			)),
			new App55_Card(array(
				'token' => $card->card->token
			)),
			new App55_Transaction(array(
				'amount' => $transaction->transaction->amount,
				'currency' => $transaction->transaction->currency
			))
		)->send();
    }
}
?>