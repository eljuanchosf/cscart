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

use Tygh\BlockManager\Exim;
use Tygh\BlockManager\Layout;
use Tygh\Exceptions\DeveloperException;
use Tygh\Languages\Languages;
use Tygh\Less;
use Tygh\Registry;
use Tygh\Settings;
use Tygh\Storage;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * Saves mobile application config
 *
 * @param int   $setting_id Setting identifier
 * @param array $settings   List of colors and parameters from form
 *
 * @return void
 */
function fn_mobile_app_update_settings($setting_id, array $settings)
{
    $setting_id = (int) $setting_id;

    if ($setting_id) {
        $settings = json_encode((array) $settings);
        Settings::instance()->updateValueById((int) $setting_id, $settings);
    }
}

/**
 * Fetches application settings from saved settings array
 *
 * @param array $options Array containing add-ons' settings
 *
 * @return array
 */
function fn_mobile_app_extract_settings_from_options(array $options)
{
    $settings = array();
    $setting_id = 0;

    if (!empty($options['service'])) {

        foreach ($options['service'] as $id => $service) {

            if ($service['name'] === 'config_data') {
                $settings = json_decode($service['value'], true);
                $setting_id = $id;
                break;
            }
        }
    }

    if (isset($settings['app_settings']['utility']['shopName'])
        && trim($settings['app_settings']['utility']['shopName']) === ''
    ) {
        $settings['app_settings']['utility']['shopName'] = Registry::get('settings.Company.company_name');
    }

    $settings['app_settings']['utility']['pushNotifications'] = true;

    return array($setting_id, $settings);
}

/**
 * Fetches saved mobile application settings along with additional that required for application config
 *
 * @return array
 */
function fn_mobile_app_get_mobile_app_settings()
{
    $section = Settings::instance()->getSectionByName('mobile_app', Settings::ADDON_SECTION);
    $options = Settings::instance()->getList($section['section_id']);
    list(, $settings) = fn_mobile_app_extract_settings_from_options($options);

    $root_user_email = db_get_field('SELECT email FROM ?:users WHERE is_root = ?s AND user_type = ?s LIMIT 1', 'Y', 'A');
    $settings['app_settings']['utility']['username'] = $root_user_email;
    $settings['app_settings']['utility']['apiKey'] = Registry::get('addons.storefront_rest_api.access_key');

    list($storefront_url, $api_url) = fn_mobile_app_get_store_urls();
    $settings['app_settings']['utility']['siteUrl'] = $storefront_url;
    $settings['app_settings']['utility']['baseUrl'] = $api_url;

    $layouts_id = db_get_field('SELECT layout_id FROM ?:bm_layouts WHERE name = ?s', 'MobileAppLayout');
    $settings['app_settings']['utility']['layoutId'] = $layouts_id;

    $settings['app_settings']['utility']['version'] = fn_allowed_for('MULTIVENDOR') ? 'MVE' : 'ULT';
    $settings['app_settings']['utility']['languages'] = fn_mobile_app_get_application_translations();

    return $settings;
}

/**
 * Fetches translations that are used in application for all available store front languages
 *
 * @return array
 */
function fn_mobile_app_get_application_translations()
{
    $languages = array();
    $variables = array(
        'Cart' => 'cart',
        'Total' => 'total',
        'Checkout' => 'checkout',
    );

    $avail_languages = Languages::getAvailable('C', true);

    foreach ($avail_languages as $lang) {
        $code = $lang['lang_code'];

        foreach ($variables as $name => $langvar) {
            $languages[$code][$name] = __($langvar, array(), $code);
        }
    }

    return $languages;
}

/**
 * Fetches store urls that required for application
 *
 * @return array
 */
function fn_mobile_app_get_store_urls()
{
    $url = fn_url('', 'C');
    return array($url, $url . 'api/4.0/');
}

/**
 * Fetches mobile app images data
 *
 * @return array
 */
function fn_mobile_app_get_mobile_app_images()
{
    $images = array();
    $schema = fn_get_schema('mobile_app', 'app_settings');

    foreach ($schema['images'] as $data) {
        $type = $data['type'];
        $images[$type] = fn_get_image_pairs(0, $type, 'M');
    }

    return $images;
}

/**
 * Compiles provided styles array
 *
 * @param array $styles Array of styles
 *
 * @return bool|int|string
 */
function fn_mobile_app_compile_app_styles(array $styles)
{
    $path_to_mobile_app_skin_less = fn_get_theme_path('[relative]/css/addons/mobile_app/variables.less');
    $less = file_get_contents($path_to_mobile_app_skin_less);

    $less_compiler = new Less();
    $config = $less_compiler::arrayToLessVars($styles);

    return $less_compiler->parse($config . $less);
}

/**
 * Installs mobile app layout for the current theme.
 */
function fn_mobile_app_install_layout()
{
    if (fn_allowed_for('MULTIVENDOR')) {
        $companies_list = array(0);
        $layout_path = Registry::get('config.dir.addons') . 'mobile_app/resources/layouts_mve.xml';
    } else {
        $companies_list = fn_get_all_companies_ids(true);
        $layout_path = Registry::get('config.dir.addons') . 'mobile_app/resources/layouts.xml';
    }

    foreach ($companies_list as $company_id) {
        $theme_name = Settings::instance($company_id)->getValue('theme_name', '', $company_id);
        $company_condition = '';

        if (fn_allowed_for('ULTIMATE')) {
            $company_condition = db_quote(' AND company_id = ?s', $company_id);
        }

        $company_condition = fn_get_company_condition('company_id', true, $company_id);

        $layout_exists = db_get_field(
            'SELECT layout_id FROM ?:bm_layouts WHERE name = ?s AND theme_name = ?s ?p',
            'MobileAppLayout',
            $theme_name,
            $company_condition
        );

        if ($layout_exists) {
            continue;
        }

        $layout_id = Exim::instance($company_id, 0, $theme_name)->importFromFile($layout_path, array(
            'override_by_dispatch' => true,
            'clean_up' => true,
            'import_style' => 'create',
        ));

        if (!$layout_id) {
            continue;
        }

        $layout_data = Layout::instance()->get($layout_id);

        fn_create_theme_logos_by_layout_id($theme_name, $layout_id, $company_id, false, $layout_data['style_id']);
    }
}

/**
 * Fetches working dir for archiving application settings
 *
 * @return string
 */
function fn_mobile_app_get_working_dir()
{
    return implode(DIRECTORY_SEPARATOR, array('sess_data', 'mobile_app'));
}

/**
 * Resizes images for android
 *
 * @param array $image_types_schema Existent image types
 * @param array $image_sizes_schema Android phone image sizes
 * @param array $pair_data          Image pair
 *
 * @return void
 */
function fn_mobile_app_resize_android_images(array $image_types_schema, array $image_sizes_schema, array $pair_data)
{
    if (empty($pair_data['detailed']['absolute_path'])) {
        return;
    }

    $working_dir = fn_mobile_app_get_working_dir();
    $file_name = isset($image_sizes_schema['file_name']) ? $image_sizes_schema['file_name'] : '';
    $resized_image_paths = array();

    if (!empty($image_types_schema['image_params']['orientation'])) {
        $resized_image_orientation = $image_types_schema['image_params']['orientation'];
        $resized_image_paths = isset($image_sizes_schema['paths'][$resized_image_orientation]['path'])
            ? $image_sizes_schema['paths'][$resized_image_orientation]['path']
            : $resized_image_paths;
    } elseif (isset($image_sizes_schema['paths'])) {
        $resized_image_paths = reset($image_sizes_schema['paths']);
    }

    /** @var \Tygh\Backend\Storage\File $storage */
    $storage = Storage::instance('custom_files');

    // add resized images
    foreach ($image_sizes_schema['resolutions'] as $resolution_code => $resolution) {
        $resolution = isset($resized_image_orientation)
            ? $resolution[$resized_image_orientation]
            : $resolution;

        list($contents, $extension) = fn_resize_image(
            $pair_data['detailed']['absolute_path'],
            $resolution['width'],
            $resolution['height'],
            '',
            array('convert_to' => 'png')
        );

        $resized_file_path = $resized_image_paths['path'];

        if ($resolution_code == 'original') {
            $resized_file_path = $resized_image_paths['original_path'];
        }

        if (!empty($resized_image_paths['variables'])) {

            foreach ($resized_image_paths['variables'] as $variable) {

                if (isset(${$variable})) {
                    $resized_file_path = str_replace(implode('', array('%', $variable, '%')), ${$variable}, $resized_file_path);
                }
            }
        }

        $resized_file_name = implode('.', array($file_name, $extension));

        $params = array(
            'contents' => $contents,
            'overwrite' => true,
        );

        $resized_file_full_path = implode(DIRECTORY_SEPARATOR, array($working_dir, $resized_file_path, $resized_file_name));
        $storage->put($resized_file_full_path, $params);
    }
}

/**
 * Resizes images for ios
 *
 * @param array $image_types_schema Existent image types
 * @param array $image_sizes_schema iOS phone image sizes
 * @param array $pair_data          Image pair
 *
 * @return void
 */
function fn_mobile_app_resize_ios_images(array $image_types_schema, array $image_sizes_schema, array $pair_data)
{
    $working_dir = fn_mobile_app_get_working_dir();
    $resized_image_path = isset($image_sizes_schema['path']) ? $image_sizes_schema['path'] : '';
    $resized_image_orientation = isset($image_types_schema['image_params']['orientation'])
        ? $image_types_schema['image_params']['orientation'] : '';

    if (!isset($image_sizes_schema['resolutions'][$resized_image_orientation])) {
        return;
    }

    /** @var \Tygh\Backend\Storage\File $storage */
    $storage = Storage::instance('custom_files');

    // add resized images
    foreach ($image_sizes_schema['resolutions'][$resized_image_orientation] as $resolution) {
        list($contents, $extension) = fn_resize_image(
            $pair_data['detailed']['absolute_path'],
            $resolution['width'],
            $resolution['height'],
            '',
            array('convert_to' => 'png')
        );

        $resized_file_name = implode('.', array($resolution['name'], $extension));

        $params = array(
            'contents' => $contents,
            'overwrite' => true,
        );

        $resized_file_full_path = implode(DIRECTORY_SEPARATOR, array($working_dir, $resized_image_path, $resized_file_name));
        $storage->put($resized_file_full_path, $params);
    }
}

/**
 * Resizes icons for ios
 *
 * @param array $image_types_schema Existent image types
 * @param array $image_sizes_schema iOS phone icon sizes
 * @param array $pair_data          Image pair
 *
 * @return void
 */
function fn_mobile_app_resize_ios_icons(array $image_types_schema, array $image_sizes_schema, array $pair_data)
{
    $working_dir = fn_mobile_app_get_working_dir();
    $file_path = isset($image_sizes_schema['path']) ? $image_sizes_schema['path'] : '';

    $common_file_name_pattern = isset($image_sizes_schema['name']['file_name'])
        ? $image_sizes_schema['name']['file_name'] : '';
    $common_file_name_pattern_variables = isset($image_sizes_schema['name']['variables'])
        ? $image_sizes_schema['name']['variables'] : array();

    /** @var \Tygh\Backend\Storage\File $storage */
    $storage = Storage::instance('custom_files');

    // add resized images
    foreach ($image_sizes_schema['resolutions'] as $resolution) {
        $width = $resolution['width'];
        $height = $resolution['height'];

        $resized_file_name_pattern = isset($resolution['name']['file_name']) ? $resolution['name']['file_name'] : $common_file_name_pattern;
        $file_name_pattern_variables = isset($resolution['name']['variables']) ? $resolution['name']['variables'] : $common_file_name_pattern_variables;

        foreach ($resolution['scales'] as $scale => $idioms) {
            $resized_file_name = $resized_file_name_pattern;

            foreach ($file_name_pattern_variables as $variable) {

                if (isset(${$variable})) {
                    $resized_file_name = str_replace(implode('', array('%', $variable, '%')), ${$variable}, $resized_file_name);
                }
            }

            list($contents, $extension) = fn_resize_image(
                $pair_data['detailed']['absolute_path'],
                $width * $scale,
                $height * $scale,
                '',
                array('convert_to' => 'png')
            );

            $resized_file_name = implode('.', array($resized_file_name, $extension));

            $params = array(
                'contents' => $contents,
                'overwrite' => true,
            );

            $resized_file_full_path = implode(DIRECTORY_SEPARATOR, array($working_dir, $file_path, $resized_file_name));
            $storage->put($resized_file_full_path, $params);
        }
    }
}

/**
 * Removes mobile app layouts upon add-on removal.
 */
function fn_mobile_app_uninstall_layout()
{
    $layouts_list = db_get_fields('SELECT layout_id FROM ?:bm_layouts WHERE name = ?s', 'MobileAppLayout');

    foreach ($layouts_list as $layout_id) {
        Layout::instance()->delete($layout_id);
    }
}

/**
 * Creates or updates a notification subscription.
 *
 * @param int    $user_id   User identifier
 * @param string $device_id Device UUID
 * @param string $platform  Device OS
 * @param string $locale    Device locale
 * @param string $token     Firebase Cloud Messaging token
 *
 * @return int Subscription ID
 */
function fn_mobile_app_update_notification_subscription($user_id, $device_id, $platform, $locale, $token)
{
    $params = array(
        'user_id'   => $user_id,
        'device_id' => $device_id,
        'platform'  => $platform,
        'locale'    => $locale,
        'token'     => $token,
    );

    db_replace_into('mobile_app_notification_subscriptions', $params);

    $subscription = fn_mobile_app_get_notification_subscriptions(array(
        'user_id'   => $user_id,
        'device_id' => $device_id,
        'platform'  => $platform,
    ));

    $subscription = reset($subscription);

    return (int)$subscription['subscription_id'];
}

/**
 * Gets the list of notifications subscriptions.
 *
 * @param array $conditions Conditions that subscriptions must meet to be returned
 *                          @see \Tygh\Database\Connection::process for ?w placeholder
 *
 * @return array
 */
function fn_mobile_app_get_notification_subscriptions($conditions)
{
    $subscriptions = db_get_array('SELECT * FROM ?:mobile_app_notification_subscriptions WHERE ?w', $conditions);

    return $subscriptions;
}

/**
 * Removes notifications subscriptions.
 *
 * @param array $conditions Conditions that subscriptions must meet to be removed
 *                          @see \Tygh\Database\Connection::process for ?w placeholder
 *
 * @return int Number of removed subscriptions
 */
function fn_mobile_app_remove_notification_subscriptions($conditions)
{
    return db_query('DELETE FROM ?:mobile_app_notification_subscriptions WHERE ?w', $conditions);
}

/**
 * Sends push notification to the connected user devices.
 *
 * @param int    $user_id       User identifier
 * @param string $title         Notification title
 * @param string $message       Notification text
 * @param string $target_screen Target screen to open in the mobile app
 *
 * @return bool Whether notification have been sent
 */
function fn_mobile_app_notify_user($user_id, $title, $message, $target_screen = '')
{
    $connected_devices = fn_mobile_app_get_notification_subscriptions(array('user_id' => $user_id));
    if (!$connected_devices) {
        return false;
    }

    /** @var \Tygh\Addons\MobileApp\Notifications\Factory $notifications_factory */
    $notifications_factory = Tygh::$app['addons.mobile_app.notifications.factory'];
    /** @var \Tygh\Addons\MobileApp\Notifications\Sender $notifications_sender */
    $notifications_sender = Tygh::$app['addons.mobile_app.notifications.sender'];

    if (!$notifications_sender->isSetUp()) {
        return false;
    }

    foreach ($connected_devices as $device_info) {
        try {
            $notification = $notifications_factory->get($device_info['platform'], $title, $message, $target_screen);
            $notifications_sender->addNotification($device_info['token'], $notification);
        } catch (DeveloperException $e) {
            // silently ignore
        }
    }

    return $notifications_sender->send();
}

/**
 * Hook handler: sends push notification on order status change.
 */
function fn_mobile_app_change_order_status(
    $status_to,
    $status_from,
    $order_info,
    $force_notification,
    $order_statuses,
    $place_order
) {
    $status_params = $order_statuses[$status_to]['params'];

    if ($place_order
        || $force_notification === false
        || $force_notification['C'] === false
        || empty($status_params['notify'])
        || $status_params['notify'] != 'Y'
        || empty($order_info['user_id'])
        || $status_to == STATUS_INCOMPLETED_ORDER
        || $order_info['status'] == STATUS_PARENT_ORDER
    ) {
        return;
    }

    $order_id = $order_info['order_id'];
    $user_id = $order_info['user_id'];
    $notification_language = isset($order_info['lang_code'])
        ? $order_info['lang_code']
        : CART_LANGUAGE;

    $title = __('change_order_status_default_subj', array(
        '[order]' => $order_id,
    ), $notification_language);
    $message = __('change_order_status_default_text', array(
        '[status]'   => $order_statuses[$status_to]['description'],
    ), $notification_language);

    fn_mobile_app_notify_user(
        $user_id,
        $title,
        $message,
        'orders/' . $order_id
    );
}