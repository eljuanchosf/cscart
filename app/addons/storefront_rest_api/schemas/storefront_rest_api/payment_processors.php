<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Enum\Addons\StorefrontRestApi\PaymentTypes;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/**
 * This schema describes payment processors that can be used to perform the order settlement via Storefront REST API.
 *
 * Structure:
 *
 * [
 *     payment_processor_script => [
 *       'redirection_details_provider' => FQDN of the class to generate redirection details.
 *                                         Must implement \Tygh\Addons\StorefrontRestApi\Payments\IRedirectionPayment
 *
 *       OR
 *
 *       'perform_payment_callback'     => FQDN of the class to directly perform payment.
 *                                         Must implement \Tygh\Addons\StorefrontRestApi\Payments\IDirectPayment
 *     ]
 * ]
 */
$schema = array();

if (Registry::get('addons.paypal.status') == 'A') {
    $schema['paypal_express.php'] = array(
        'type'  => PaymentTypes::REDIRECTION,
        'class' => '\Tygh\Addons\StorefrontRestApi\Payments\PaypalExpress',
    );
}

return $schema;