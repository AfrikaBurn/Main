<?php

namespace Drupal\business_rules\Plugin;

use Drupal\business_rules\Entity\Variable;
use Drupal\business_rules\ItemInterface;
use Drupal\business_rules\VariableObject;
use Drupal\business_rules\VariablesSet;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base Class for Business rules plugins.
 *
 * @package Drupal\business_rules\Plugin
 */
abstract class BusinessRulesItemPluginBase extends PluginBase implements BusinessRulesItemPluginInterface {

  /**
   * The business rules processor.
   *
   * @var \Drupal\business_rules\Util\BusinessRulesProcessor
   */
  protected $processor;

  /**
   * The business rules util.
   *
   * @var \Drupal\business_rules\Util\BusinessRulesUtil
   */
  protected $util;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->processor = \Drupal::service('business_rules.processor');
    $this->util      = \Drupal::service('business_rules.util');
  }

  /**
   * {@inheritdoc}
   */
  public function getGroup() {
    return $this->pluginDefinition['group'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  abstract public function getSettingsForm(array &$form, FormStateInterface $form_state, ItemInterface $item);

  /**
   * {@inheritdoc}
   */
  public function buildForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function processSettings(array $settings, ItemInterface $item) {
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getRedirectUrl(ItemInterface $item) {
    return $item->urlInfo('collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getEditUrl(ItemInterface $item) {
    return $item->urlInfo('edit-form');
  }

  /**
   * {@inheritdoc}
   */
  public function pregMatch($string) {

    if (is_string($string)) {
      preg_match_all(self::VARIABLE_REGEX, $string, $variables);

      return $variables[1];
    }
    else {
      throw new \Exception('Only strings are acceptable to pregMatch variables');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getVariables(ItemInterface $item) {
    $variableSet = new VariablesSet();

    $settings = $item->getSettings();
    foreach ($settings as $setting) {

      if (is_string($setting)) {
        $variables = $this->pregMatch($setting);

        if (count($variables)) {
          foreach ($variables as $variable_id) {
            if (stristr($variable_id, '->') && !stristr($variable_id, '[')) {
              $arr_temp    = explode('->', $variable_id);
              $variable_id = $arr_temp[0];
              $variable    = Variable::load($variable_id);
              unset($arr_temp[0]);
            }
            elseif (stristr($variable_id, '[')) {
              $arr_temp    = explode('[', $variable_id);
              $variable_id = $arr_temp[0];
              $variable    = Variable::load($variable_id);
              unset($arr_temp[0]);
            }
            else {
              $variable = Variable::load($variable_id);
            }
            if (!empty($variable)) {
              $varObject = new VariableObject($variable_id, NULL, $variable->getType());
              $variableSet->append($varObject);
            }
          }
        }
      }
    }

    return $variableSet;
  }

  /**
   * {@inheritdoc}
   */
  public function processVariables($content, VariablesSet $event_variables) {
    /** @var \Drupal\business_rules\VariableObject $variable */
    if ($event_variables->count()) {
      foreach ($event_variables->getVariables() as $variable) {
        if (is_string($variable->getValue()) || is_numeric($variable->getValue())) {
          $content = str_replace('{{' . $variable->getId() . '}}', $variable->getValue(), $content);
        }
        elseif (is_array($variable->getValue())) {
          if (preg_match_all(self::VARIABLE_REGEX, $content)) {
            if ($content == '{{' . $variable->getId() . '}}') {
              $content = $variable->getValue();
            }
            elseif (stristr($content, '{{' . $variable->getId() . '}}')) {
              $value = implode(chr(10), $variable->getValue());
              $content = str_replace('{{' . $variable->getId() . '}}', $value, $content);
            }
          }
        }
        elseif (empty($variable->getValue())) {
          $content = str_replace('{{' . $variable->getId() . '}}', 'NULL', $content);
        }
      }
    }

    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public function processTokens(ItemInterface &$item) {
    $settings = $item->getSettings();
    foreach ($settings as $key => $setting) {
      if (is_string($setting)) {
        $settings[$key] = $this->util->token->replace($setting);
      }
      elseif (is_array($setting)) {
        $settings[$key] = $this->processTokenArraySetting($setting);
      }
    }

    foreach ($settings as $key => $setting) {
      $item->setSetting($key, $setting);
    }
  }

  /**
   * Helper function to process tokens if the setting is an array.
   *
   * @param array $setting
   *   The setting array.
   */
  private function processTokenArraySetting(array &$setting) {
    if (count($setting)) {
      foreach ($setting as $key => $value) {
        if (is_string($value)) {
          $setting[$key] = $this->util->token->replace($value);
        }
        elseif (is_array($value)) {
          $this->processTokenArraySetting($value);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

}
