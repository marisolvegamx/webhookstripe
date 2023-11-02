<?php 
require_once '../secrets_2.php';
include "webhookcontroller.php";


\Stripe\Stripe::setApiKey($stripeSecretKey);
WebhookController::index($endpoint_secret);

