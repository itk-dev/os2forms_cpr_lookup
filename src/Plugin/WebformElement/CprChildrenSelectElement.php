<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\ElementInfoManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\os2forms_cpr_lookup\Service\CprServiceInterface;
use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;
use Drupal\os2web_nemlogin\Service\AuthProviderService;
use Drupal\webform\Plugin\WebformElement\Radios;
use Drupal\webform\Plugin\WebformElementManagerInterface;
use Drupal\webform\WebformLibrariesManagerInterface;
use Drupal\webform\WebformTokenManagerInterface;
use ItkDev\Serviceplatformen\Service\Exception\ServiceException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CPR Children Select element.
 *
 * @WebformElement(
 *   id = "cpr_children_select_element",
 *   label = @Translation("CPR Children Select Element"),
 *   description = @Translation("CPR Children Select Element description"),
 *   category = @Translation("CPR elements")
 * )
 */
class CprChildrenSelectElement extends Radios implements NemidElementPersonalInterface {
  protected const FORM_STATE_DATA = 'CprLookupElement';

  /**
   * The auth provider service.
   *
   * @var \Drupal\os2web_nemlogin\Service\AuthProviderService
   */
  private AuthProviderService $authProviderService;

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
    // PluginBase::__construct() accepts only three arguments.
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->logger = $logger;
    $this->configFactory = $config_factory;
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->elementInfo = $element_info;
    $this->elementManager = $element_manager;
    $this->tokenManager = $token_manager;
    $this->librariesManager = $libraries_manager;
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
  protected function defineDefaultProperties() {
    $properties = [
        'cpr_output_type' => '',
        'options' => [],
      ] + parent::defineDefaultProperties();
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state)
  {
    $form = parent::form($form, $form_state);
    $form['options']['options']['#required'] = FALSE;
    $form['options']['#access'] = FALSE;

    $form['element']['cpr_output_type'] = array(
      '#type' => 'radios',
      '#options'=> ['cpr' => $this->t('CPR'), 'name' => $this->t('Name')],
      '#title' => $this
        ->t('CPR output type'),
      '#required' => TRUE,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getElementSelectorOptions(array $element) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getElementSelectorSourceValues(array $element) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$element, array &$form, FormStateInterface $form_state)
  {
    // Check if data is already set by CPR Look up element.
    if ('cpr_children_select_element' === $element['#type']) {
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
              // Add data for the CPR value element.
              $data['cpr'] = $cpr;
              // Merge in some values from the NemID login provider.
              $data += array_filter(
                array_map(
                  [$plugin, 'fetchValue'],
                  [
                    'pid' => 'pid',
                    // Will replace PID in the future https://migrering.nemlog-in.dk/nemlog-in-broker/privat-tjenesteudbyder/opslagstjenester/erstatning-til-pid-rid-uuid/
                    'uuid' => 'uuid',
                  ]
                )
              );

              $form_state->set(static::FORM_STATE_DATA, $data);
            }
          }
          catch (ServiceException $serviceException) {
            // @todo Log this?
          }
        }
      }
      $options = [];
      $cpr_data = $form_state->get(static::FORM_STATE_DATA);
      if(!empty($cpr_data['children'])) {
        $childrenArr = explode(' ', $cpr_data['children']);

        switch ($element['#cpr_output_type']) {
          case 'name':
            foreach ($childrenArr as $childCpr) {
              $child = $this->cprService->search($childCpr);
              $data = $child->toArray();
              $options[$data['name']] = $data['name'];
            }
            break;
          case 'cpr':
            foreach ($childrenArr as $childCpr) {
              $child = $this->cprService->search($childCpr);
              $data = $child->toArray();
              $options[$childCpr] = $data['name'];
            }
            break;
          default:
        }
      }

      $form['elements'][$element['#webform_key']]['#options'] = $options;
    }
  }
}
