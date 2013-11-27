<?php

class App55_Gateway {
	public function __construct($environment, $apiKey, $apiSecret) {
		$this->environment = $environment;
		$this->apiKey = $apiKey;
		$this->apiSecret = $apiSecret;
	}

	public function createCard($user, $card) {
		return new App55_CardCreateRequest($this, array(
			'user' => $user,
			'card' => $card
		));
	}
	public function deleteCard($user, $card) {
		return new App55_CardDeleteRequest($this, array(
			'user' => $user,
			'card' => $card
		));
	}
	public function listCards($user) {
		return new App55_CardListRequest($this, array(
			'user' => $user
		));
	}

	public function createTransaction($user, $card, $transaction) {
		return new App55_TransactionCreateRequest($this, array(
			'user' => $user,
			'card' => $card,
			'transaction' => $transaction
		));
	}
	public function commitTransaction($transaction) {
		return new App55_TransactionCommitRequest($this, array(
			'transaction' => $transaction
		));
	}

	public function createUser($user) {
		return new App55_UserCreateRequest($this, array(
			'user' => $user
		));
	}
	public function authenticateUser($user) {
		return new App55_UserAuthenticateRequest($this, array(
			'user' => $user
		));
	}
	public function updateUser($user) {
		return new App55_UserUpdateRequest($this, array(
			'user' => $user
		));
	}
	
	public function createSchedule($schedule, $user, $card, $transaction) {
		return new App55_ScheduleCreateRequest($this, array(
			'schedule' => $schedule,
			'user' => $user,
			'card' => $card,
			'transaction' => $transaction
		));
	}
	
	public function response($qs) {
		return new App55_Response($this, $qs);
	}
}

?>
