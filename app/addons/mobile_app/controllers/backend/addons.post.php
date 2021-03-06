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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$is_mobile_app_addon = !empty($_REQUEST['addon']) && $_REQUEST['addon'] == 'mobile_app';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update') {

        if ($mode == 'update'
            && $is_mobile_app_addon
            && !empty($_REQUEST['setting_id'])
            && !empty($_REQUEST['m_settings'])
        ) {
            $schema = fn_get_schema('mobile_app', 'app_settings');

            foreach ($schema['images'] as $data) {
                fn_attach_image_pairs($data['name'], $data['type']);
            }

            fn_mobile_app_update_settings($_REQUEST['setting_id'], $_REQUEST['m_settings']);
        }

    }

    return array(CONTROLLER_STATUS_OK);
}

if ($mode == 'update') {
    if ($is_mobile_app_addon) {
        $options = (array) Tygh::$app['view']->getTemplateVars('options');
        $colors = array();

        list($setting_id, $settings) = fn_mobile_app_extract_settings_from_options($options);

        $images = fn_mobile_app_get_mobile_app_images();
        $schema = fn_get_schema('mobile_app', 'app_settings');

        Tygh::$app['view']->assign(array(
            'setting_id'  => $setting_id,
            'config_data' => $settings,
            'app_images'  => $images,
            'image_types' => $schema['images'],
        ));

        if (!empty($settings['app_appearance']['colors'])) {

            // write colors from setting to array for less
            foreach ($settings['app_appearance']['colors'] as $key => $type) {
                foreach ($type as $variable => $value) {
                    $colors[$variable] = $value;
                }
            }
        }

        if ($action == 'rebuild') {
            $colors = array_merge($colors, $_REQUEST['colors']);
        }

        $mobile_app_styles = fn_mobile_app_compile_app_styles($colors);
        Tygh::$app['view']->assign('mobile_app_styles', $mobile_app_styles);
    }
}
