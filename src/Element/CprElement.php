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
        $form_state->setError($element, t('%name field is not a valid CPR.', ['%name' => $element['#title']]));
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
      $command = new MessageCommand(t('Not a valid CPR number.'), NULL, ['type' => 'error']);
      $response->addCommand($command);
      return $response;
    }

    /** @var \Drupal\os2forms_cpr_lookup\Service\CprService $cprService */
    $cprService = \Drupal::service('os2forms_cpr_lookup.service');

    try {
      $result = $cprService->search($cpr);
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

    return $response;
  }

  /**
   * Get name invoke command.
   */
  private function getNameInvokeCommand($result) {
    $selector = '.cpr-name';
    $method = 'val';
    $arguments = [$this->generateNameString($result)];

    return new InvokeCommand($selector, $method, $arguments);
  }

  /**
   * Get address invoke command.
   */
  private function getAddressInvokeCommand($result) {
    $selector = '.cpr-address';
    $method = 'val';
    $arguments = [$this->generateAddressString($result)];

    return new InvokeCommand($selector, $method, $arguments);
  }

  /**
   * Generates name string.
   */
  private function generateNameString(CprServiceResult $result): string {
    $name = $result->getFirstName();
    if (NULL !== $result->getMiddleName()) {
      $name .= ' ' . $result->getMiddleName();
    }
    $name .= ' ' . $result->getLastName();

    return $name;
  }

  /**
   * Generates address string.
   */
  private function generateAddressString(CprServiceResult $result): string {
    $address = $result->getStreetName();

    $address .= NULL !== $result->getHouseNumber()
      ? ' ' . $result->getHouseNumber()
      : '';

    $address .= NULL !== $result->getFloor()
      ? ' ' . $result->getFloor()
      : '';

    $address .= NULL !== $result->getSide()
      ? ' ' . $result->getSide()
      : '';

    $address .= ', '
      . $result->getPostalCode()
      . ' '
      . $result->getCity();

    return $address;
  }

  private function getStreetInvokeCommand(CprServiceResult $result) {
    $selector = '.cpr-street';
    $method = 'val';
    $arguments = [$result->getStreetName()];

    return new InvokeCommand($selector, $method, $arguments);
  }

  private function getHouseNumberInvokeCommand(CprServiceResult $result) {
    $selector = '.cpr-house-number';
    $method = 'val';

    $houseNumber = NULL !== $result->getHouseNumber()
      ? ' ' . $result->getHouseNumber()
      : '';

    $arguments = [$houseNumber];

    return new InvokeCommand($selector, $method, $arguments);
  }

  private function getFloorInvokeCommand(CprServiceResult $result) {
    $selector = '.cpr-floor';
    $method = 'val';

    $floor = NULL !== $result->getFloor()
      ? ' ' . $result->getFloor()
      : '';

    $arguments = [$floor];

    return new InvokeCommand($selector, $method, $arguments);
  }

  private function getSideInvokeCommand(CprServiceResult $result) {
    $selector = '.cpr-side';
    $method = 'val';

    $side = NULL !== $result->getSide()
      ? ' ' . $result->getSide()
      : '';

    $arguments = [$side];

    return new InvokeCommand($selector, $method, $arguments);
  }
}
