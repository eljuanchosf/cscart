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
 ***************************************************************************/

use Tygh\Addons\Gdpr\CookiesPolicyManager;

defined('BOOTSTRAP') or die('Access denied');

/** @var CookiesPolicyManager $cookies_policy_manager */
$cookies_policy_manager = Tygh::$app['addons.gdpr.cookies_policy_manager'];

$auth = Tygh::$app['session']['auth'];
$has_user_agreement = $cookies_policy_manager->hasUserAgreement($auth);

$cookies_accepted = isset($_REQUEST[CookiesPolicyManager::REQUEST_ACCEPTANCE_FLAG])
    && $_REQUEST[CookiesPolicyManager::REQUEST_ACCEPTANCE_FLAG] === 'Y';

if ($cookies_accepted) {
    // user accepted cookies policy
    if (!$has_user_agreement) {
        $cookies_policy_manager->saveAgreement($auth['user_id']);
    }

} elseif (!$has_user_agreement) {
    $cookies_policy_manager->applyCookiePolicy($_REQUEST);
}
