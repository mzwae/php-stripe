<?php

require_once('vendor\autoload.php');
require_once('config\db.php');
require_once('lib\pdo_db.php');
require_once('models\Customer.php');
require_once('models\Transaction.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$STRIPE_KEY = getenv('STRIPE_SECRET');

// echo $STRIPE_KEY;

\Stripe\Stripe::setApiKey($STRIPE_KEY);

/**
 * 
 * Sanitize Post array
 */

$POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);


$first_name = $POST['first_name'];
$last_name = $POST['last_name'];
$email = $POST['email'];
$token = $POST['stripeToken'];

// echo $token;


/**
 *  Create Customer In Stripe
 */
$customer = \Stripe\Customer::create(array(
    "email" => $email,
    "source" => $token
));

/**
 * Charge Customer
 */

$charge = \Stripe\Charge::create(array(
    "amount" => 5000, // equal to $50.00
    "currency" => "usd",
    "description" => "Intro to REACT Course",
    "customer" => $customer->id
));

// print_r($charge);

// Store Customer data
$customerData = [
    'id' => $charge->customer,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email
];


/**
 * Create a new customer
 */
$customer = new Customer();

// Add Customer to DB
$customer->addCustomer($customerData);



// Store Transaction data
$transactionData = [
    'id' => $charge->id,
    'customer_id' => $charge->customer,
    'product' => $charge->description,
    'amount' => $charge->amount,
    'currency' => $charge->currency,
    'status' => $charge->status
];


/**
 * Create a new transaction
 */
$transaction = new Transaction();

// Add Transaction to DB
$transaction->addTransaction($transactionData);

/**
 * Redirect to success page
 */
$charge_id = $charge->id;
$charge_desc = $charge->description;
header('Location: success.php?tid='.$charge_id.'&product='.$charge_desc);