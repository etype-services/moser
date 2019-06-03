<?php

function sanmarcosrecord_preprocess_html(&$variables) {
  if ($node = menu_get_object()) {
    if ($node->type = 'article') {
      $node_wrapper = entity_metadata_wrapper('node', $node);
      $val = $node_wrapper->field_section->value();
      if (!empty($val)) {
        $matches = theme_get_setting('mailchimp_sections');
        foreach ($val as $item) {
          if (strpos($matches,$item->tid) !== false) {
            $variables['mailchimp_js'] = <<<EOT
<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/50048306554a918deb227946d/cef39c16feb89f564a0f77673.js");</script>
EOT;
            break;
          }
        }
      }
    }
  }
}