<?php
// NEEDS TESTING
/**
 * @file
 * getlocations_fields_handler_argument_postcode.inc
 * @author Bob Hutchinson http://drupal.org/user/52366
 * @copyright GNU GPL
 *
 * Location postal code argument handler.
 */

/**
 * Argument handler to accept post code
 */
class getlocations_fields_handler_argument_postcode extends views_handler_argument {
  function option_definition() {
    $options = parent::option_definition();
    $options['type'] = array('default' => 'postal_code');
    $options['operator'] = array('default' => 'equal');

    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['type'] = array(
      '#type' => 'value',
      '#value' => 'postal_code'
    );

    $form['operator'] = array(
      '#type' => 'select',
      '#title' => t('Operator'),
      '#options' => array(
        'equal' => t('Equals'),
        'not_equal' => t('Does not Equal'),
        'begin_with' => t('Begins with'),
        'not_begin_with' => t('Does not Begin with'),
        'end_with' => t('Ends with'),
        'not_end_with' => t('Does not End with'),
        'contain' => t('Contains'),
        'not_contain' => t('Does not Contain'),
      ),
      '#default_value' => $this->options['operator'],
    );

  }

  /**
   * Set up the query for this argument.
   *
   * The argument sent may be found at $this->argument.
   */
  function query($group_by = FALSE) {
    $value = FALSE;

    if ($this->options['type'] == 'postal_code' && isset($this->argument)) {
      $value = getlocations_apoclean($this->argument);
    }

    if ($value) {
      $alias = ($this->table_alias ? $this->table_alias : $this->table);
      $field = $alias . '.' . $this->real_field;

      if ($this->options['operator'] == 'equal') {
        $this->query->add_where($group_by, $field, $value, 'LIKE');
      }
      elseif ($this->options['operator'] == 'not_equal') {
        $this->query->add_where($group_by, $field, $value, 'NOT LIKE');
      }
      elseif ($this->options['operator'] == 'begin_with') {
        $this->query->add_where($group_by, $field, db_like($value) . '%', 'LIKE');
      }
      elseif ($this->options['operator'] == 'not_begin_with') {
        $this->query->add_where($group_by, $field, db_like($value) . '%', 'NOT LIKE');
      }
      elseif ($this->options['operator'] == 'end_with') {
        $this->query->add_where($group_by, $field, '%' . db_like($value) , 'LIKE');
      }
      elseif ($this->options['operator'] == 'not_end_with') {
        $this->query->add_where($group_by, $field, '%' . db_like($value) , 'NOT LIKE');
      }
      elseif ($this->options['operator'] == 'contain') {
        $this->query->add_where($group_by, $field, '%' . db_like($value) . '%' , 'LIKE');
      }
      elseif ($this->options['operator'] == 'not_contain') {
        $this->query->add_where($group_by, $field, '%' . db_like($value) . '%' , 'NOT LIKE');
      }
    }
  }

}
