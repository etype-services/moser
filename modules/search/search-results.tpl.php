<?php

/**
 * @file
 * Default theme implementation for displaying search results.
 *
 * This template collects each invocation of theme_search_result(). This and
 * the child template are dependent to one another sharing the markup for
 * definition lists.
 *
 * Note that modules may implement their own search type and theme function
 * completely bypassing this template.
 *
 * Available variables:
 * - $search_results: All results as it is rendered through
 *   search-result.tpl.php
 * - $module: The machine-readable name of the module (tab) being searched, such
 *   as "node" or "user".
 *
 *
 * @see template_preprocess_search_results()
 *
 * @ingroup themeable
 */
?>
<?php if ($search_results): ?>
  <h2><?php print t('Search results');?></h2>
  <ol class="search-results <?php print $module; ?>-results">
    <?php print $search_results; ?>
  </ol>
  <?php print $pager; ?>
<?php else : ?>
  <h2><?php print t('Your search yielded no results');?></h2>
    <ul>
        <li>Check if your spelling is correct. Remove quotes around phrases to search for each word individually. For example, bike shed will often show more results than “bike shed.” Also, consider loosening your query with “OR”  as bike OR shed will often show more results than bike shed. If you think your article was posted before February 6th, 2017, check out our digital archive records from our <a href="http://archive.sanmarcosrecord.com">old website</a> on the button below. If you still can’t find what you're looking for there, feel free to contact us and we’ll do our best to find what it is you seek.</li>
        <li>Thank you for your support, the San Marcos Daily Record.</li>
    </ul>
<?php endif; ?>

