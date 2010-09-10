<?php

class rex_form_widget_media_element extends rex_form_element
{
  var $category_id = 0;
  var $args = array();
  
  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_widget_media_element($tag = '', &$table, $attributes = array())
  {
    parent::rex_form_element('', $table, $attributes);
  }

  function setCategoryId($category_id)
  {
    $this->category_id = $category_id;
  }

  function setTypes($types)
  {
    $this->args['types'] = $types;
  }
  
  function setPreview($preview = true)
  {
    $this->args['preview'] = $preview;
  }

  function formatElement()
  {
    static $widget_counter = 1;

		$html = rex_var_media::getMediaButton($widget_counter, $this->category_id, $this->args);
		$html = str_replace('REX_MEDIA['. $widget_counter .']', $this->getValue(), $html);
		$html = str_replace('MEDIA['. $widget_counter .']', $this->getAttribute('name'), $html);

    $widget_counter++;
    return $html;
  }
}