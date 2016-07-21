<?php

drupal_add_css(drupal_get_path('theme', 'masthead') . '/css/style1.css', array('group' => CSS_THEME, 'weight' => 100, 'type' => 'file'));


drupal_add_css(drupal_get_path('theme', 'masthead') . '/css/responsive.css', array('group' => CSS_THEME, 'weight' => 101, 'type' => 'file'));

/* First and Last Classes on Teasers */

function masthead_preprocess_page(&$variables) {
  $nodes = $variables['page']['content']['system_main']['nodes'];
  $i = 1;
  $len = count($nodes);
  foreach (array_keys($nodes) as $nid) {
    if ($i == 1) {
      $variables['page']['content']['system_main']['nodes'][$nid]['#node']->classes_array = array('first');
    }
    if ($i == $len - 1) {
      $variables['page']['content']['system_main']['nodes'][$nid]['#node']->classes_array = array('last');
    }
    $i++;
    /* So I don't get "Warning: Cannot use a scalar value as an array" */
    unset($nodes,$nid);
  }

  $path = current_path();
  if ($path == 'frontpage') {
    $title = 'Home' . ' | ';
  } else if (isset($variables['node'])) {
    $title = $variables['node']->title . ' | ';
  }
  $site_name = variable_get('site_name', "San Marcos Record");
  drupal_set_title($title . $site_name);
  global $user;
  if ($user->uid == 1) var_dump($variables);
}

function masthead_preprocess_node(&$variables) {
  $node = $variables['node'];
  if (!empty($node->classes_array)) {
    $variables['classes_array'] = array_merge($variables['classes_array'], $node->classes_array);
  }
}

/* Breadcrumbs */

function masthead_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  if (!empty($breadcrumb)) {
   
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.

    $output = '<div class="breadcrumb"><div class="breadcrumb-inner">' . implode(' / ', $breadcrumb) . '</div></div>';
    return $output;
  }
}

/* Span Tag on Links */

function masthead_link($variables) {
  return '<a href="' . check_plain(url($variables['path'], $variables['options'])) . '"' . drupal_attributes($variables['options']['attributes']) . '><span>' . ($variables['options']['html'] ? $variables['text'] : check_plain($variables['text'])) . '</span></a>';
}

/* Some text in ye old Search Form */

function masthead_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {

// Add extra attributes to the text box
    $form['search_block_form']['#attributes']['onblur'] = "if (this.value == '') {this.value = 'Search';}";
    $form['search_block_form']['#attributes']['onfocus'] = "if (this.value == 'Search') {this.value = '';}";
    // Prevent user from searching the default text
    $form['#attributes']['onsubmit'] = "if(this.search_block_form.value=='Search'){ alert('Please enter a search'); return false; }";
  }
} 

/* Add Page Body Class */

function masthead_preprocess_html(&$vars) {
  $path = drupal_get_path_alias($_GET['q']);
  $aliases = explode('/', $path);

  foreach($aliases as $alias) {
    $vars['classes_array'][] = drupal_clean_css_identifier($alias);
  }
}
