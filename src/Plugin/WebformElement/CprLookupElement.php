<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\ElementInfoManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\os2forms_cpr_lookup\Service\CprServiceInterface;
use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementBase;
use Drupal\os2web_nemlogin\Service\AuthProviderService;
use Drupal\webform\Plugin\WebformElementManagerInterface;
use Drupal\webform\WebformLibrariesManagerInterface;
use Drupal\webform\WebformTokenManagerInterface;
use ItkDev\Serviceplatformen\Service\Exception\ServiceException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an abstract CprLookupElement alement.
 *
 * Implements the prepopulate logic.
 *
 * @see \Drupal\webform\Plugin\WebformElementBase
 * @see \Drupal\webform\Plugin\WebformElementInterface
 * @see \Drupal\webform\Annotation\WebformElement
 */
abstract class CprLookupElement extends NemidElementBase {
  protected const FORM_STATE_DATA = 'CprLookupElement';

  /**
   * The auth provider service.
   *
   * @var \Drupal\os2web_nemlogin\Service\AuthProviderService
   */
  private $authProviderService;

  /**
   * The CPR service.
   *
   * @var \Drupal\os2forms_cpr_lookup\Service\CprServiceInterface
   */
  private $cprService;

  /**
   * Constructor.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    LoggerInterface $logger,
    ConfigFactoryInterface $config_factory,
    AccountInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager,
    ElementInfoManagerInterface $element_info,
    WebformElementManagerInterface $element_manager,
    WebformTokenManagerInterface $token_manager,
    WebformLibrariesManagerInterface $libraries_manager,
    AuthProviderService $authProviderService,
    CprServiceInterface $cprService
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger, $config_factory, $current_user,
      $entity_type_manager, $element_info, $element_manager, $token_manager, $libraries_manager);
    $this->authProviderService = $authProviderService;
    $this->cprService = $cprService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')->get('webform'),
      $container->get('config.factory'),
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.element_info'),
      $container->get('plugin.manager.webform.element'),
      $container->get('webform.token_manager'),
      $container->get('webform.libraries_manager'),
      $container->get('os2web_nemlogin.auth_provider'),
      $container->get('os2forms_cpr_lookup.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function handleElementPrepopulate(array &$element, FormStateInterface &$form_state) {
    $prepopulateKey = $this->getPrepopulateFieldFieldKey();

    // Fetch value from cpr lookup.
    $data = NULL;

    if ($form_state->has(static::FORM_STATE_DATA)) {
      $data = $form_state->get(static::FORM_STATE_DATA);
    }
    else {
      // Making the request to the plugin, and storing the information on the
      // form, so that it's available on the next element within the same
      // webform render.
      $plugin = $this->authProviderService->getActivePlugin();

      if ($plugin->isAuthenticated()) {
        try {
          $cpr = $plugin->fetchValue('cpr');
          if ($cpr) {
            $result = $this->cprService->search($cpr);
            $data = $result->toArray();
            $form_state->set(static::FORM_STATE_DATA, $data);
          }
        }
        catch (ServiceException $serviceException) {
          // @todo Log this?
        }
      }
    }

    if (!empty($data)) {
      if (isset($data[$prepopulateKey])) {
        $value = $data[$prepopulateKey];
        $element['#value'] = $value;
      }
    }
  }

}
