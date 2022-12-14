<?php

namespace Drupal\os2forms_cpr_lookup\Element;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Textfield;
use Drupal\os2forms_cpr_lookup\CPR\CprServiceResult;
use ItkDev\Serviceplatformen\Service\Exception\NoPnrFoundException;

/**
 * CPR Element.
 *
 * @FormElement("cpr_element")
 */
class CprElement extends Textfield {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $element = parent::getInfo();
    $element['#element_validate'] = [[$this, 'validate']];
    $element['#ajax'] = [
      'callback' => [$this, 'ajaxCallback'],
      'event' => 'change',
      'progress' => [
        'type' => 'throbber',
        'message' => $this->t('Looking up CPR'),
      ],
    ];

    return $element;
  }

  /**
   * Validation.
   */
  public function validate(&$element, FormStateInterface $form_state, &$complete_form) {
    if ($element['#value'] !== '') {
      if (!preg_match('{^\d{10}$}', $element['#value'])) {
        $form_state->setError($element, $this->t('%name field is not a valid CPR.', ['%name' => $element['#title']]));
      }
    }
  }

  /**
   * Call back method when performing ajax request.
   */
  public function ajaxCallback(array &$form, FormStateInterface $form_state) {

    $cprNumberElement = $form_state->getTriggeringElement();
    $cpr = $cprNumberElement['#value'];

    if ('' === $cpr) {
      $response = new AjaxResponse();
      $command = new MessageCommand($this->t('No CPR number provided.'), NULL, ['type' => 'error']);
      $response->addCommand($command);
      return $response;
    }

    if (!preg_match('{^\d{10}$}', $cpr)) {
      $response = new AjaxResponse();
      $command = new MessageCommand($this->t('Not a valid CPR number.'), NULL, ['type' => 'error']);
      $response->addCommand($command);
      return $response;
    }

    /** @var \Drupal\os2forms_cpr_lookup\Service\CprService $cprService */
    $cprService = \Drupal::service('os2forms_cpr_lookup.service');

    try {
      $result = $cprService->search($cpr);

      $log = [
        'type' => 'CPR',
        'operation' => 'Query',
        'description' => $this->t('CPR Lookup performed for CPR: %cpr by User: %user', [
          '%cpr' => $cpr,
          '%user' => \Drupal::currentUser()->getEmail(),
        ]),
      ];

      if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail')) {
        \Drupal::logger('os2forms_cpr_lookup')->error('Required module admin_audit_trail not installed.');

        $response = new AjaxResponse();
        $command = new MessageCommand($this->t('Something went wrong. Please contact an administrator for further assistance.'), NULL, ['type' => 'error']);
        $response->addCommand($command);
        return $response;
      }

      admin_audit_trail_insert($log);
    }
    catch (NoPnrFoundException $e) {
      $response = new AjaxResponse();
      $command = new MessageCommand($this->t('Not a valid CPR number.'), NULL, ['type' => 'error']);
      $response->addCommand($command);
      return $response;
    }

    $response = new AjaxResponse();

    $response->addCommand($this->getNameInvokeCommand($result));
    $response->addCommand($this->getAddressInvokeCommand($result));
    $response->addCommand($this->getStreetInvokeCommand($result));
    $response->addCommand($this->getHouseNumberInvokeCommand($result));
    $response->addCommand($this->getFloorInvokeCommand($result));
    $response->addCommand($this->getSideInvokeCommand($result));
    $response->addCommand($this->getPostalCodeInvokeCommand($result));
    $response->addCommand($this->getCityInvokeCommand($result));

    return $response;
  }

  /**
   * Get name invoke command.
   */
  private function getNameInvokeCommand(CprServiceResult $result) {
    $selector = '.cpr-name';
    $method = 'val';
    $arguments = [$result->getName()];

    return new InvokeCommand($selector, $method, $arguments);
  }

  /**
   * Get address invoke command.
   */
  private function getAddressInvokeCommand(CprServiceResult $result) {
    $selector = '.cpr-address';
    $method = 'val';
    $arguments = [$result->getAddress()];

    return new InvokeCommand($selector, $method, $arguments);
  }

  /**
   * Get Street invoke command.
   *
   * @param \Drupal\os2forms_cpr_lookup\CPR\CprServiceResult $result
   *   Result from CPR Service.
   *
   * @return \Drupal\Core\Ajax\InvokeCommand
   *   Invoke command for use in ajax response.
   */
  private function getStreetInvokeCommand(CprServiceResult $result): InvokeCommand {
    $selector = '.cpr-street';
    $method = 'val';
    $arguments = [$result->getStreetName()];

    return new InvokeCommand($selector, $method, $arguments);
  }

  /**
   * Get House Number invoke command.
   *
   * @param \Drupal\os2forms_cpr_lookup\CPR\CprServiceResult $result
   *   Result from CPR Service.
   *
   * @return \Drupal\Core\Ajax\InvokeCommand
   *   Invoke command for use in ajax response.
   */
  private function getHouseNumberInvokeCommand(CprServiceResult $result): InvokeCommand {
    $selector = '.cpr-house-number';
    $method = 'val';

    $houseNumber = NULL !== $result->getHouseNumber()
      ? ' ' . $result->getHouseNumber()
      : '';

    $arguments = [$houseNumber];

    return new InvokeCommand($selector, $method, $arguments);
  }

  /**
   * Get Floor invoke command.
   *
   * @param \Drupal\os2forms_cpr_lookup\CPR\CprServiceResult $result
   *   Result from CPR Service.
   *
   * @return \Drupal\Core\Ajax\InvokeCommand
   *   Invoke command for use in ajax response.
   */
  private function getFloorInvokeCommand(CprServiceResult $result): InvokeCommand {
    $selector = '.cpr-floor';
    $method = 'val';

    $floor = NULL !== $result->getFloor()
      ? ' ' . $result->getFloor()
      : '';

    $arguments = [$floor];

    return new InvokeCommand($selector, $method, $arguments);
  }

  /**
   * Get Side invoke command.
   *
   * @param \Drupal\os2forms_cpr_lookup\CPR\CprServiceResult $result
   *   Result from CPR Service.
   *
   * @return \Drupal\Core\Ajax\InvokeCommand
   *   Invoke command for use in ajax response.
   */
  private function getSideInvokeCommand(CprServiceResult $result): InvokeCommand {
    $selector = '.cpr-side';
    $method = 'val';

    $side = NULL !== $result->getSide()
      ? ' ' . $result->getSide()
      : '';

    $arguments = [$side];

    return new InvokeCommand($selector, $method, $arguments);
  }

  /**
   * Get Postal Code invoke command.
   *
   * @param \Drupal\os2forms_cpr_lookup\CPR\CprServiceResult $result
   *   Result from CPR Service.
   *
   * @return \Drupal\Core\Ajax\InvokeCommand
   *   Invoke command for use in ajax response.
   */
  private function getPostalCodeInvokeCommand(CprServiceResult $result): InvokeCommand {
    $selector = '.cpr-postal-code';
    $method = 'val';
    $arguments = [$result->getPostalCode()];

    return new InvokeCommand($selector, $method, $arguments);
  }

  /**
   * Get City invoke command.
   *
   * @param \Drupal\os2forms_cpr_lookup\CPR\CprServiceResult $result
   *   Result from CPR Service.
   *
   * @return \Drupal\Core\Ajax\InvokeCommand
   *   Invoke command for use in ajax response.
   */
  private function getCityInvokeCommand(CprServiceResult $result): InvokeCommand {
    $selector = '.cpr-city';
    $method = 'val';
    $arguments = [$result->getCity()];

    return new InvokeCommand($selector, $method, $arguments);
  }

}
