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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update') {
        if (!empty($_REQUEST['posts']) && fn_discussion_check_update_posts_permission($_REQUEST['posts'], $auth)) {
            fn_update_discussion_posts($_REQUEST['posts']);
        }
    }

    return;
}

if ($mode == 'update') {

    $discussion = fn_get_discussion($_REQUEST['category_id'], 'C', true, $_REQUEST);
    if (!empty($discussion) && $discussion['type'] != 'D') {
        if (fn_allowed_for('MULTIVENDOR') || fn_allowed_for('ULTIMATE') && Registry::get('runtime.company_id')) {
            Registry::set('navigation.tabs.discussion', array (
                'title' => __('discussion_title_category'),
                'js' => true
            ));

            Tygh::$app['view']->assign('discussion', $discussion);
        }
    }

} elseif ($mode == 'm_update') {
    $selected_fields = Tygh::$app['session']['selected_fields'];

    if (!empty($selected_fields['extra']) && in_array('discussion_type', $selected_fields['extra'])) {

        $field_names = Tygh::$app['view']->getTemplateVars('field_names');
        $fields2update = Tygh::$app['view']->getTemplateVars('fields2update');

        $field_names['discussion_type'] = __('discussion_title_category');
        $fields2update[] = 'discussion_type';

        Tygh::$app['view']->assign('field_names', $field_names);
        Tygh::$app['view']->assign('fields2update', $fields2update);
    }
}
