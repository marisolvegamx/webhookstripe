<?php 
require_once '../secrets_1.php';
include "webhookcontroller.php";

\Stripe\Stripe::setApiKey($stripeSecretKey);
WebhookController::index($endpoint_secret);

