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
   * @param array $response
   *   Original response from the CPR service.
   */
  public function __construct(array $response) {
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
