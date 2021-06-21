<?php

namespace Drupal\os2forms_cpr_lookup\CPR;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Encapsulates a result from the CPR service.
 */
class CprServiceResult {

  /**
   * The original response from the CPR Service.
   *
   * @var array
   */
  private $response;

  /**
   * PropertyAccessor used for accessing the original response.
   *
   * @var \Symfony\Component\PropertyAccess\PropertyAccessor
   */
  private $propertyAccessor;

  /**
   * ServiceplatformenCprServiceResult constructor.
   *
   * @param object $response
   *   Original response from the CPR service.
   */
  public function __construct(object $response) {
    $this->response = $response;

    $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
  }

  /**
   * Get first name.
   *
   * @return string
   *   The first name.
   */
  public function getFirstName(): string {
    return $this->getProperty('persondata.navn.fornavn');
  }

  /**
   * Get middle name.
   *
   * @return string|null
   *   The middle name.
   */
  public function getMiddleName(): ?string {
    return $this->propertyAccessor->isReadable($this->response, 'persondata.navn.mellemnavn')
      ? $this->propertyAccessor->getValue($this->response, 'persondata.navn.mellemnavn')
      : NULL;
  }

  /**
   * Get last name.
   *
   * @return string
   *   The last name.
   */
  public function getLastName(): string {
    return $this->getProperty('persondata.navn.efternavn');
  }

  /**
   * Get street name.
   *
   * @return string
   *   The street name.
   */
  public function getStreetName(): string {
    return $this->getProperty('adresse.aktuelAdresse.vejnavn');
  }

  /**
   * Get house number.
   *
   * @return string
   *   The house number.
   */
  public function getHouseNumber(): ?string {
    return $this->getProperty('adresse.aktuelAdresse.husnummer');
  }

  /**
   * Get floor.
   *
   * @return string|null
   *   The floor.
   */
  public function getFloor(): ?string {
    return $this->propertyAccessor->isReadable($this->response, 'adresse.aktuelAdresse.etage')
      ? $this->propertyAccessor->getValue($this->response, 'adresse.aktuelAdresse.etage')
      : NULL;
  }

  /**
   * Get side.
   *
   * @return string|null
   *   The side.
   */
  public function getSide(): ?string {
    return $this->propertyAccessor->isReadable($this->response, 'adresse.aktuelAdresse.sidedoer')
      ? $this->propertyAccessor->getValue($this->response, 'adresse.aktuelAdresse.sidedoer')
      : NULL;
  }

  /**
   * Get postal code.
   *
   * @return string
   *   The postal code.
   */
  public function getPostalCode(): string {
    return $this->getProperty('adresse.aktuelAdresse.postnummer');
  }

  /**
   * Get city.
   *
   * @return string|null
   *   The city.
   */
  public function getCity(): string {
    return $this->getProperty('adresse.aktuelAdresse.postdistrikt');
  }

  /**
   * Get full person name.
   *
   * @return string
   */
  public function getName(): string {
    return implode(' ', array_filter([
      $this->getFirstName(),
      $this->getMiddleName(),
      $this->getLastName(),
    ]));
  }

  /**
   * Get full address (one line).
   *
   * @return string
   */
  public function getAddress(): string {
    $address = $this->getStreetName();

    $address .= NULL !== $this->getHouseNumber()
      ? ' ' . $this->getHouseNumber()
      : '';

    $address .= NULL !== $this->getFloor()
      ? ' ' . $this->getFloor()
      : '';

    $address .= NULL !== $this->getSide()
      ? ' ' . $this->getSide()
      : '';

    $address .= ', '
      . $this->getPostalCode()
      . ' '
      . $this->getCity();

    return $address;
  }

  /**
   * Get all values in an associative array.
   *
   * @return array
   */
  public function toArray(): array {
    return [
      'first_name' => $this->getFirstName(),
      'middle_name' => $this->getMiddleName(),
      'last_name' => $this->getLastName(),
      'city' => $this->getCity(),
      'postal_code' => $this->getPostalCode(),
      'floor' => $this->getFloor(),
      'house_number' => $this->getHouseNumber(),
      'side' => $this->getSide(),
      'street_name' => $this->getStreetName(),
      'address' => $this->getAddress(),
      'name' => $this->getName(),
    ];
  }

  /**
   * Returns the value of the property.
   *
   * @param string $property
   *   Name of property.
   *
   * @return string
   *   The value of the property. Empty if property does not exist.
   */
  private function getProperty(string $property): string {
    return $this->propertyAccessor->isReadable($this->response, $property)
      ? $this->propertyAccessor->getValue($this->response, $property)
      : '';
  }

}
