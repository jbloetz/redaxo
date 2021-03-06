<?php

/**
 * Class for var casting
 *
 * @author gharlan
 */
class rex_type
{
  /**
   * Casts the variable $var to $vartype
   *
   * Possible types:
   *  - 'bool' (or 'boolean')
   *  - 'int' (or 'integer')
   *  - 'double'
   *  - 'string'
   *  - 'float'
   *  - 'real'
   *  - 'object'
   *  - 'array'
   *  - 'array[<type>]', e.g. 'array[int]'
   *  - '' (don't cast)
   *  - a callable
   *  - array(
   *      array(<key>, <vartype>, <default>),
   *      array(<key>, <vartype>, <default>),
   *      ...
   *    )
   *
   * @param mixed $var     Variable to cast
   * @param mixed $vartype Variable type
   *
   * @return mixed Castet value
   */
  static public function cast($var, $vartype)
  {
    if (is_string($vartype)) {
      $casted = true;
      switch ($vartype) {
        // ---------------- PHP types
        case 'bool'   :
        case 'boolean':
          $var = (boolean) $var;
          break;
        case 'int'    :
        case 'integer':
          $var = (int)     $var;
          break;
        case 'double' :
          $var = (double)  $var;
          break;
        case 'float'  :
        case 'real'   :
          $var = (float)   $var;
          break;
        case 'string' :
          $var = (string)  $var;
          break;
        case 'object' :
          $var = (object)  $var;
          break;
        case 'array'  :
          if (empty($var))
            $var = array();
          else
            $var = (array) $var;
          break;

          // kein Cast, nichts tun
        case ''       : break;

        default:
          // check for array with generic type
          if (strpos($vartype, 'array[') === 0) {
            if (empty($var))
              $var = array();
            else
              $var = (array) $var;

            // check if every element in the array is from the generic type
            $matches = array();
            if (preg_match('@array\[([^\]]*)\]@', $vartype, $matches)) {
              foreach ($var as $key => $value) {
                try {
                  $var[$key] = self::cast($value, $matches[1]);
                } catch (rex_exception $e) {
                  // Evtl Typo im vartype, mit urspr. typ als fehler melden
                  throw new rex_exception('Unexpected vartype "' . $vartype . '" in cast()!');
                }
              }
            } else {
              throw new rex_exception('Unexpected vartype "' . $vartype . '" in cast()!');
            }
          } else {
            $casted = false;
          }
      }
      if ($casted) {
        return $var;
      }
    }

    if (is_callable($vartype)) {
      $var = call_user_func($vartype, $var);
    } elseif (is_array($vartype)) {
      $var = self::cast($var, 'array');
      $newVar = array();
      foreach ($vartype as $cast) {
        if (!is_array($cast) || !isset($cast[0])) {
          throw new rex_exception('Unexpected vartype in cast()!');
        }
        $key = $cast[0];
        $innerVartype = isset($cast[1]) ? $cast[1] : '';
        if (array_key_exists($key, $var)) {
          $newVar[$key] = self::cast($var[$key], $innerVartype);
        } elseif (!isset($cast[2])) {
          $newVar[$key] = self::cast('', $innerVartype);
        } else {
          $newVar[$key] = $cast[2];
        }
      }
      $var = $newVar;
    } elseif (is_string($vartype)) {
      throw new rex_exception('Unexpected vartype "' . $vartype . '" in cast()!');
    } else {
      throw new rex_exception('Unexpected vartype in cast()!');
    }

    return $var;
  }
}
