<?php
require_once('vendor\autoload.php');

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


