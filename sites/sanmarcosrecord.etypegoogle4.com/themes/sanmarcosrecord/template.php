<?php

/* First and Last Classes on Teasers */
function cni_preprocess_page(&$variables) {

    $grid_info = get_grid_info();

    // Create page variables
    $variables['grid_size'] = 'container_' . $grid_info['grid_size'];
    $variables['grid_full_width'] = 'grid_' . $grid_info['grid_size'];
    $variables['sidebar_first_grid_width'] = 'grid_' . $grid_info['sidebar_first_width'];
    $variables['sidebar_second_grid_width'] = 'grid_' . $grid_info['sidebar_second_width'];
    $variables['twitter'] = theme_get_setting('twitter');
    $variables['facebook'] = theme_get_setting('facebook');

    for ($region_count = 1; $region_count <= 4; $region_count++) {
        $variables['preface_' . $region_count . '_grid_width'] = 'grid_' . $grid_info['preface_' . $region_count . '_grid_width'];
        $variables['postscript_' . $region_count . '_grid_width'] = 'grid_' . $grid_info['postscript_' . $region_count . '_grid_width'];
    }

    if (empty($variables['page']['sidebar_first']) && empty($variables['page']['sidebar_second'])) {
        $variables['main_content_grid_width'] = 'grid_' . $grid_info['grid_size'];
    }
    else {
        if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
            $variables['main_content_grid_width'] = 'grid_' . ($grid_info['grid_size'] - ($grid_info['sidebar_first_width'] + $grid_info['sidebar_second_width']));
        }
        else {
            if (empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
                $variables['main_content_grid_width'] = 'grid_' . ($grid_info['grid_size'] - $grid_info['sidebar_second_width']);
            }
            else {
                if (!empty($variables['page']['sidebar_first']) && empty($variables['page']['sidebar_second'])) {
                    $variables['main_content_grid_width'] = 'grid_' . ($grid_info['grid_size'] - $grid_info['sidebar_first_width']);
                }
            }
        }
    }

    if (isset ($variables['page']['content']['system_main']['nodes'])) {
        $nodes = $variables['page']['content']['system_main']['nodes'];
        $i = 1;
        $len = count($nodes);
        if ($len > 0) {
            foreach (array_keys($nodes) as $nid) {
                if ($i == 1) {
                    $variables['page']['content']['system_main']['nodes'][$nid]['#node']->classes_array = array('first');
                }
                if ($i == $len - 1) {
                    $variables['page']['content']['system_main']['nodes'][$nid]['#node']->classes_array = array('last');
                }
                $i++;
                /* Defeat "Warning: Cannot use a scalar value as an array" */
                unset($nodes, $nid);
            }
        }
    }

}

function cni_preprocess_node(&$variables) {

    $node = $variables['node'];
    if (!empty($node->classes_array)) {
        $variables['classes_array'] = array_merge($variables['classes_array'], $node->classes_array);
    }

    /* add things to pages, ie, not teasers */

    if (node_is_page($node) !== FALSE) {

        /* add this script */
        drupal_add_js('//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-56e774978692f861', 'external');

        /* add message to "free" stories on pages */
        if (module_exists('premium')) {
            if (isset($node->premium_level['level_name'])) {
                $level = $node->premium_level['level_name'];
                $check = user_is_logged_in();
                if ($level === 'free' && $check != '1') {
                    $site_name = variable_get('site_name');
                    if (substr($site_name, 0) !== 'The') {
                        $site_name = 'the ' . $site_name;
                    }
                    $e_edition = theme_get_setting('e_edition');;
                    $variables['free_message'] = "<a href=\"https://etypeservices.com/$e_edition\">Subscribe and see the e-Edition of $site_name for the complete story.</a>";
                }
            }
        }

    }

}

/* Breadcrumbs */

function cni_breadcrumb($variables) {
    $breadcrumb = $variables['breadcrumb'];
    if (!empty($breadcrumb)) {

        // Provide a navigational heading to give context for breadcrumb links to
        // screen-reader users. Make the heading invisible with .element-invisible.

        $output = '<div class="breadcrumb"><div class="breadcrumb-inner">' . implode(' / ', $breadcrumb) . '</div></div>';
        return $output;
    }
}

/**
 * @param $variables
 * @return string
 * Span Tag on Links
 */
function cni_link($variables) {
    return '<a href="' . check_plain(url($variables['path'], $variables['options'])) . '"' . drupal_attributes($variables['options']['attributes']) . '><span>' . ($variables['options']['html'] ? $variables['text'] : check_plain($variables['text'])) . '</span></a>';
}

/**
 * @param $form
 * @param $form_state
 * @param $form_id
 * Some text in the main Search Form
 */
function cni_form_alter(&$form, &$form_state, $form_id) {
    if ($form_id == 'search_block_form') {
        // Prevent user from searching the default text
        $form['#attributes']['onsubmit'] = "if(this.search_block_form.value=='Search'){ alert('Please enter a search'); return false; }";
    }
}

/**
 * @param $variables
 */
function cni_preprocess_html(&$variables) {

    // Add body class for sidebar layout
    $variables['classes_array'][] = theme_get_setting('sidebar_layout');

    /* add body classes */
    $path = drupal_get_path_alias($_GET['q']);
    $aliases = explode('/', $path);
    foreach ($aliases as $alias) {
        $variables['classes_array'][] = drupal_clean_css_identifier($alias);
    }

    /* add site-specific css */
    $base_path = base_path();
    $conf_path = conf_path();
    $site_css = $base_path . $conf_path . '/local.css';

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $site_css)) {
        drupal_add_css(
            $site_css,
            array(
                'type' => 'file',
                'media' => 'all',
                'preprocess' => FALSE,
                'every_page' => TRUE,
                'weight' => 999,
                'group' => CSS_THEME
            )
        );
    }

    /* add site setting css */
    $nav_color = theme_get_setting('nav_color');
    if (!empty($nav_color)) {
        drupal_add_css(
            '#block-superfish-1, .sf-menu.sf-style-space li, .sf-menu.sf-style-space li li, .sf-menu.sf-style-space li li li, .sf-menu.sf-style-space.sf-navbar {background: ' . $nav_color . ' !important;}',
            array(
                'group' => CSS_THEME,
                'type' => 'inline',
                'media' => 'screen',
                'preprocess' => FALSE,
                'weight' => '9999',
            )
        );
    }

    /* not using media to get smaller desktop browsers as well */
    $max_nav_width = theme_get_setting('max_nav_width');
    if (!empty($max_nav_width)) {
        drupal_add_css(
            '#main-menu ul.menu {max-width: ' . $max_nav_width . ' !important;}',
            array(
                'group' => CSS_THEME,
                'type' => 'inline',
                'media' => 'screen',
                'preprocess' => FALSE,
                'weight' => '9999',
            )
        );
    }

    /* not using media to get smaller desktop browsers as well */
    $max_user_nav_width  = theme_get_setting('max_user_nav_width');
    if (!empty($max_user_nav_width)) {
        drupal_add_css(
            '#main-menu #block-system-user-menu ul.menu {max-width: ' . $max_user_nav_width . ' !important;}',
            array(
                'group' => CSS_THEME,
                'type' => 'inline',
                'media' => 'screen',
                'preprocess' => FALSE,
                'weight' => '9999',
            )
        );
    }

    $body_background = theme_get_setting('body_background');
    if (!empty($body_background)) {
        drupal_add_css(
            'body {background: ' . $body_background . ' !important;}',
            array(
                'group' => CSS_THEME,
                'type' => 'inline',
                'media' => 'screen',
                'preprocess' => FALSE,
                'weight' => '9999',
            )
        );
    }

    $logo_width = theme_get_setting('logo_width');
    if (!empty($logo_width)) {
        drupal_add_css(
            '.site-logo img {max-width: ' . $logo_width . ' !important;}',
            array(
                'group' => CSS_THEME,
                'type' => 'inline',
                'media' => 'screen',
                'preprocess' => FALSE,
                'weight' => '9999',
            )
        );
    }

    /* add favicons */
    $icon_path = $base_path . $conf_path . '/files/favicons/';

    $theme_color = array(
        '#type' => 'html_tag',
        '#tag' => 'meta',
        '#attributes' => array(
            'name' => 'theme-color',
            'content' => '#ffffff',
        )
    );
    drupal_add_html_head($theme_color, 'theme_color');

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $icon_path . 'safari-pinned-tab.svg')) {
        $mask_icon = array(
            '#type' => 'html_tag',
            '#tag' => 'link',
            '#attributes' => array(
                'rel' => 'mask-icon',
                'href' => $icon_path . 'safari-pinned-tab.svg',
                'color' => '#5bbad5',
            )
        );
        drupal_add_html_head($mask_icon, 'mask_icon');

    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $icon_path . 'manifest.json')) {
        $manifest = array(
            '#type' => 'html_tag',
            '#tag' => 'link',
            '#attributes' => array(
                'rel' => 'manifest',
                'href' => $icon_path . 'manifest.json',
            )
        );
        drupal_add_html_head($manifest, 'manifest');
    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $icon_path . 'favicon-16x16.png')) {
        $icon16 = array(
            '#type' => 'html_tag',
            '#tag' => 'link',
            '#attributes' => array(
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '16x16',
                'href' => $icon_path . 'favicon-16x16.png',
            )
        );
        drupal_add_html_head($icon16, 'icon16');
    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $icon_path . 'favicon-32x32.png')) {
        $icon32 = array(
            '#type' => 'html_tag',
            '#tag' => 'link',
            '#attributes' => array(
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '32x32',
                'href' => $icon_path . 'favicon-32x32.png',
            )
        );
        drupal_add_html_head($icon32, 'icon32');
    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $icon_path . 'apple-touch-icon.png')) {
        $appletouchicon = array(
            '#type' => 'html_tag',
            '#tag' => 'link',
            '#attributes' => array(
                'rel' => 'apple-touch-icon',
                'sizes' => '180x180',
                'href' => $icon_path . 'apple-touch-icon.png',
            )
        );
        drupal_add_html_head($appletouchicon, 'apple-touch-icon');
    }

    /* extra scripts */
    $variables['head_script'] = variable_get('etype_head_script');
    $variables['body_script'] = empty(variable_get('etype_body_script'))? theme_get_setting('adscript'): variable_get('etype_body_script');

    /* Menu break variable -- this segment loads different css files depending
    on the setting, and passes a variable that script.js uses to move menus
    around */
    $tmp = theme_get_setting('menu_break_point');
    $menu_break_point = empty($tmp)? '767': $tmp;
    drupal_add_js(array('cni' => array('menu_break_point' => $menu_break_point)), array('type' => 'setting'));
    switch ($menu_break_point) {
        case '958':
            drupal_add_css(
                drupal_get_path('theme', 'cni') . '/css/menu_break_958.css',
                array(
                    'type' => 'file',
                    'media' => 'all',
                    'preprocess' => FALSE,
                    'every_page' => TRUE,
                    'weight' => 999,
                    'group' => CSS_THEME
                )
            );
            break;

        default:
            drupal_add_css(
                drupal_get_path('theme', 'cni') . '/css/menu_break_767.css',
                array(
                    'type' => 'file',
                    'media' => 'all',
                    'preprocess' => FALSE,
                    'every_page' => TRUE,
                    'weight' => 999,
                    'group' => CSS_THEME
                )
            );
    }

}

/**
 * @return array
 */
function get_grid_info() {

    $grid_info = array();

    $grid_info['grid_size'] = theme_get_setting('grid_size');
    $grid_info['sidebar_first_width'] = theme_get_setting('sidebar_first_width');
    $grid_info['sidebar_second_width'] = theme_get_setting('sidebar_second_width');

    for ($region_count = 1; $region_count <= 4; $region_count++) {
        $grid_info['preface_' . $region_count . '_grid_width'] = theme_get_setting('preface_' . $region_count . '_grid_width');
        $grid_info['postscript_' . $region_count . '_grid_width'] = theme_get_setting('postscript_' . $region_count . '_grid_width');
    }

    return $grid_info;
}

/**
 * @param $vars
 */
function cni_preprocess_views_view_row_rss(&$vars) {
    $item = $vars['row'];
    $result = $vars['view']->result;
    $id = $vars['id'];
    $node = node_load( $result[$id-1]->nid );
    $vars['title'] = trim(check_plain($item->title));
    $vars['link'] = check_url($item->link);
    $vars['description'] = check_plain($item->description);
    $vars['node'] = $node;
    $vars['item_elements'] = empty($item->elements) ? '' : format_xml_elements($item->elements);
    empty($node->field_image['und'][0]['uri'])? $vars['img'] = '': $vars['img']  = file_create_url($node->field_image['und'][0]['uri']);
}

/**
 * @param $vars
 * @return void
 */
function cni_preprocess_field(&$vars)
{

    if($vars['element']['#field_name'] == 'field_sponsor_ad_image')
    {
        $node = node_load($vars['element']['#object']->nid);
        $ad = field_get_items('node', $node, 'field_sponsor_ad_image');
        if (count($ad) > 0) {
            $url = field_get_items('node', $node, 'field_sponsor_ad_url');
            $items = [];
            foreach ($ad as $k => $v) {
                $arr = [];
                $arr['img_src'] = file_create_url($v['uri']);
                $arr['img_url'] = $url[$k]['safe_value'];
                $items[] = $arr;
            }
            $vars['field_sponsor_ad_image_items'] = $items;
        }
    }
    return;
}
