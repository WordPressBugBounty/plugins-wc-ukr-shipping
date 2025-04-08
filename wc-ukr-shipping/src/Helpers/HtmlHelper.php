<?php

namespace kirillbdev\WCUkrShipping\Helpers;

if ( ! defined('ABSPATH')) {
    exit;
}

class HtmlHelper
{
	/**
	 * @param string $id
	 * @param array $options
	 */
	public static function selectField($id, $options = [])
	{
	  $class = '';

	  if ( ! empty($options['class'])) {
	    $class = implode(' ', $options['class']);
    }

    $attributes = '';

	  if ( ! empty($options['attributes'])) {
	    foreach ($options['attributes'] as $key => $value) {
	      $attributes .= $key . '="' . $value . '"';
      }
    }

		$html = sprintf(
		  '<select name="%s" id="%s" class="%s"%s>',
      $id,
      $id,
      $class,
      $attributes
    );

	  if ( ! empty($options['options'])) {
      foreach ($options['options'] as $key => $value) {
        $html .= sprintf(
          '<option value="%s"%s>%s</option>',
          esc_attr($key),
          isset($options['value']) && $options['value'] === $key ? ' selected' : '',
          esc_attr($value)
        );
      }
    }

		$html .= '</select>';

		echo $html;
	}

    public static function switcherField(string $name, string $label, bool $checked): void
    {
    ?>
        <div class="wcus-form-group wcus-form-group--horizontal">
            <label class="wcus-switcher">
                <input type="hidden" name="<?php echo esc_attr($name); ?>" value="0">
                <input type="checkbox"
                       name="<?php echo esc_attr($name); ?>"
                       value="1" <?php echo $checked ? 'checked' : ''; ?>>
                <span class="wcus-switcher__control"></span>
            </label>
            <div class="wcus-control-label"><?php echo esc_html($label); ?></div>
        </div>
    <?php
    }
}
