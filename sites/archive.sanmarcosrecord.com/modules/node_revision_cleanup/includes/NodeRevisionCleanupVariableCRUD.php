<?php
/**
 * @file
 * Abstracted cache CRUD methods.
 */

/**
 * A wrapper class for caching mechanisms.
 */
class NodeRevisionCleanupVariableCRUD {

  /**
   * Constructor.
   */
  public function __construct() {
    $this->memcacheExists = module_exists('memcache');
    if ($this->memcacheExists) {
      $this->mem = new MemCacheDrupal('nrc_cache');
    }
  }

  /**
   * Method for performing variable lookups in the defined caching mechanism.
   *
   * @param string $key
   *   The key name for which a value will be returned.
   * @param mixed $default
   *   The default value to be returned, if $key is not defined.
   *
   * @return mixed
   *   The stored value for the key searched, or default if that is not defined.
   */
  public function vget($key, $default) {
    if ($this->memcacheExists) {
      $return = $this->mem->get($key);
      if ($return) {
        return $return->data;
      }
      return $default;
    }
    return variable_get($key, $default);
  }

  /**
   * Method for defining a key's value in the caching mechanism.
   *
   * @param string $key
   *   The unique name of the value to be stored. If not unique, the existing
   *   value will be replaced.
   * @param mixed $value
   *   The serializable value to be stored under the $key provided.
   *
   * @return NULL
   *   Returns after corresponding set functionality finishes.
   */
  public function vset($key, $value) {
    if ($this->memcacheExists) {
      $this->mem->set($key, $value);
      return;
    }
    variable_set($key, $value);
  }

  /**
   * Method for deleting a key and its value from the caching mechanism.
   *
   * @param string $key
   *   The key that will be deleted, along with its value.
   *
   * @return NULL
   *   Returns after corresponding delete functionality finishes.
   */
  public function vdel($key) {
    if ($this->memcacheExists) {
      $this->mem->clear($key);
      return;
    }
    variable_del($key);
  }

}
