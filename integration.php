<?php

require_once(dirname(__FILE__) . '/lib/App55.php');
$gateway = new App55_Gateway(App55_Environment::$development, getenv('APP55_API_KEY') ? getenv('APP55_API_KEY') : 'cHvG680shFTaPWhp8RHhGCSo5QbHkWxP', getenv('APP55_API_SECRET') ? getenv('APP55_API_SECRET') : 'zMHzGPF3QAAQQzTDoTGtGz8f5WFZFjzM');

function createUser() {
	global $gateway;

	$email = 'example.' . gmdate('YmdHis') . '@example.com';
	$phone = '0123 456 7890';
	$password = 'pa55word';
	$password_confirm = 'pa55word';

	echo "Creating user $email..."; 
	$response = $gateway->createUser(new App55_User(array(
		'email' => $email,
		'phone' => $phone,
		'password' => $password,
		'password_confirm' => $password_confirm
	)))->send();
	
	echo " DONE (user-id " . $response->user->id . ")\n";
	return $response;
}

function createCard($user, $number = '4111111111111111') {
	global $gateway;

	echo "Creating card...";
	$response = $gateway->createCard(
		new App55_User(array(
			'id' => $user->id
		)),
		new App55_Card(array(
			'holder_name' => 'App55 User',
			'number' => $number,
			'expiry' => gmdate('m/Y', time() + 90 * 24 * 3600),
			'security_code' => '111',
			'address' => new App55_Address(array(
				'street' => '8 Exchange Quay',
				'city' => 'Manchester',
				'postal_code' => 'M5 3EJ',
				'country' => 'GB'
			))
		))	
	)->send();
	echo " DONE (token " . $response->card->token . ")\n";	
	return $response;
}

function listCards($user) {
	global $gateway;

	echo "Listing cards...";
	$response = $gateway->listCards(
		new App55_User(array(
			'id' => $user->id
		))
	)->send();
	echo " DONE (" . count($response->cards) . " cards)\n";
	return $response;
}

function deleteCard($user, $card) {
	global $gateway;
	
	echo "Deleting card $card->token...";
	$response = $gateway->deleteCard(
		new App55_User(array(
			'id' => $user->id
		)),
		new App55_Card(array(
			'token' => $card->token
		))
	)->send();
	echo " DONE\n";
	return $response;
}

function createTransaction($user, $card, $commit=false, $threeds=false) {
	global $gateway;

	echo "Creating transaction...";
	$response = $gateway->createTransaction(
		new App55_User(array(
			'id' => $user->id
		)),
		new App55_Card(array(
			'token' => $card->token
		)),
		new App55_Transaction(array(
			'amount' => '0.10',
			'currency' => 'GBP',
			'commit' => $commit
		)),
		$threeds
	)->send();
	echo " DONE (transaction-id " . $response->transaction->id . ")\n";
	return $response;
}

function commitTransaction($transaction) {
	global $gateway;
	
	echo "Committing transaction...";
	$response = $gateway->commitTransaction(
		new App55_Transaction(array(
			'id' => $transaction->id
		))
	)->send();
	echo " DONE\n";
	return $response;
}

function file_get_contents_json($url) {
	$context = stream_context_create(array(
		'http' => array(
			'method' => 'GET',
			'ignore_errors' => false,
			'header' => 'Accept: application/json'
		)
	));
	return file_get_contents($url, false, $context);
}

echo "App55 Sandbox - API Key <$gateway->apiKey>\n";
echo "\n";

$user = createUser()->user;

$card1 = createCard($user)->card;
$transaction = createTransaction($user, $card1)->transaction;
commitTransaction($transaction);

$card2 = createCard($user)->card;
$transaction = createTransaction($user, $card2)->transaction;
commitTransaction($transaction);

$card3 = createCard($user)->card;
$transaction = createTransaction($user, $card3)->transaction;
commitTransaction($transaction);

$card_3ds_ne = createCard($user, '4543130000001116')->card;

$transaction = createTransaction($user, $card1, false, true);
$response = file_get_contents_json($transaction->threeds . '&next=http://dev.app55.com/v1/echo');
$response = $gateway->response(null, $response);
$transaction = $gateway->commitTransaction($response->transaction)->send()->transaction;
assert($transaction->code == 'succeeded');
assert($transaction->auth_code == '06603');

$transaction = createTransaction($user, $card1, true, true);
$response = file_get_contents_json($transaction->threeds . '&next=http://dev.app55.com/v1/echo');
$response = $gateway->response(null, $response);
assert($response->transaction->code == 'succeeded');
assert($response->transaction->auth_code == '06603');

$transaction = createTransaction($user, $card_3ds_ne, false, true);
assert(!isset($transaction->threeds));
$transaction = $gateway->commitTransaction($transaction->transaction)->send()->transaction;
assert($transaction->code == 'succeeded');
assert($transaction->auth_code == '06603');

$transaction = createTransaction($user, $card_3ds_ne, true, true);
assert(!isset($transaction->threeds));
assert($transaction->transaction->code == 'succeeded');
assert($transaction->transaction->auth_code == '06603');


$cards = listCards($user)->cards;
$tokens = array();
foreach($cards as $card) {
	$tokens[] = $card->token;
}
assert(in_array($card1->token, $tokens));
assert(in_array($card2->token, $tokens));
assert(in_array($card3->token, $tokens));
assert(in_array($card_3ds_ne->token, $tokens));
assert(count($cards) == 4);

deleteCard($user, $card1);
deleteCard($user, $card2);
deleteCard($user, $card3);
deleteCard($user, $card_3ds_ne);

assert(count(listCards($user)->cards) == 0);

$email = 'example.' . gmdate('YmdHis') . '@app55.com';
echo 'Updating user...';
$gateway->updateUser(
	new App55_User(array(
		'id' => $user->id,
		'email' => $email,
		'password' => 'password01',
		'password_confirm' => 'password01'
	))
)->send();
echo " DONE\n";

echo "Authenticating user...";
$user2 = $gateway->authenticateUser(
	new App55_User(array(
		'email' => $email,
		'password' => 'password01'
	))
)->send()->user;
echo " DONE\n";

assert($user->id == $user2->id);


?>
