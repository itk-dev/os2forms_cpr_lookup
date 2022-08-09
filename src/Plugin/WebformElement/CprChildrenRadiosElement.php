<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\ElementInfoManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\os2forms_cpr_lookup\Service\CprServiceInterface;
use Drupal\os2forms_cpr_lookup\Service\FormHelper;
use Drupal\os2forms_nemid\Plugin\WebformElement\NemidElementPersonalInterface;
use Drupal\os2web_nemlogin\Service\AuthProviderService;
use Drupal\webform\Plugin\WebformElement\Radios;
use Drupal\webform\Plugin\WebformElementManagerInterface;
use Drupal\webform\WebformLibrariesManagerInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\WebformTokenManagerInterface;
use ItkDev\Serviceplatformen\Service\Exception\ServiceException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CPR Children Radios element.
 *
 * @WebformElement(
 *   id = "cpr_children_radios_element",
 *   label = @Translation("CPR Children Radios Element"),
 *   description = @Translation("CPR Children Radios Element description"),
 *   category = @Translation("CPR elements")
 * )
 *
 * @todo combine the two children elements into one.
 */
class CprChildrenRadiosElement extends Radios implements NemidElementPersonalInterface {
  protected const FORM_STATE_DATA = 'CprChildrenRadiosElement';

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
  private CprServiceInterface $cprService;

  /**
   * The form helper service.
   *
   * @var FormHelper
   */
  private FormHelper $formHelper;

  /**
   * Constructor.
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param LoggerInterface $logger
   * @param ConfigFactoryInterface $config_factory
   * @param AccountInterface $current_user
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param ElementInfoManagerInterface $element_info
   * @param WebformElementManagerInterface $element_manager
   * @param WebformTokenManagerInterface $token_manager
   * @param WebformLibrariesManagerInterface $libraries_manager
   * @param AuthProviderService $authProviderService
   * @param CprServiceInterface $cprService
   * @param FormHelper $form_helper
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
    CprServiceInterface $cprService,
    FormHelper $form_helper
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
    $this->formHelper = $form_helper;
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
      $container->get('os2forms_cpr_lookup.service'),
      $container->get('os2forms_cpr_lookup.form_service')
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

  public function prepare(array &$element, WebformSubmissionInterface $webform_submission = NULL) {
  }

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$element, array &$form, FormStateInterface $form_state)
  {
    if ('cpr_children_radios_element' === $element['#type']) {
      // Define form state data.
      // Making the request to the plugin, and storing the information on the
      // form, so that it's available on the next element within the same
      // webform render.
      if (!$form_state->has(static::FORM_STATE_DATA)) {
        $plugin = $this->authProviderService->getActivePlugin();
        $data = $this->formHelper->prepareFormStateCprData($plugin);
        if ($data) {
          $form_state->set(static::FORM_STATE_DATA, $data);
        }
      }
      $cprData = $form_state->get(static::FORM_STATE_DATA);

      $form['elements'][$element['#webform_key']]['#options'] = $cprData ? $this->formHelper->setChildSelectOptions($cprData, $element) : [];

      // Remove form element form edit form and add as form item instead.
      if (substr_compare($form_state->getBuildInfo()['form_id'], 'edit_form', -strlen('edit_form')) === 0) {
        $form['elements'][$element['#webform_key']]['#access'] = FALSE;
        $submissionValue = $form['information']['#webform_submission']->getElementData($element['#webform_key']);
        $form[$element['#webform_key'] . '_value'] = [
          '#type' => 'item',
          '#title' => $element['#webform_key'],
          '#markup' => '<div>' . $submissionValue . '</div>',
        ];
      }
    }
  }
}
