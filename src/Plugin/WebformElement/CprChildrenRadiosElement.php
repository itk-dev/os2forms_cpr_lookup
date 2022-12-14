<?php

namespace Drupal\os2forms_cpr_lookup\Plugin\WebformElement;

use Drupal\Component\Utility\NestedArray;
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
   * @var \Drupal\os2forms_cpr_lookup\Service\FormHelper
   */
  private FormHelper $formHelper;

  /**
   * Constructor.
   *
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity typ manager.
   * @param \Drupal\Core\Render\ElementInfoManagerInterface $element_info
   *   The element info.
   * @param \Drupal\webform\Plugin\WebformElementManagerInterface $element_manager
   *   The element manager.
   * @param \Drupal\webform\WebformTokenManagerInterface $token_manager
   *   The token manager.
   * @param \Drupal\webform\WebformLibrariesManagerInterface $libraries_manager
   *   The libraries manager.
   * @param \Drupal\os2web_nemlogin\Service\AuthProviderService $authProviderService
   *   The auth provider service.
   * @param \Drupal\os2forms_cpr_lookup\Service\CprServiceInterface $cprService
   *   The CPR service.
   * @param \Drupal\os2forms_cpr_lookup\Service\FormHelper $form_helper
   *   The form helper.
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
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $form['options']['options']['#required'] = FALSE;
    $form['options']['#access'] = FALSE;

    $form['element']['cpr_output_type'] = [
      '#type' => 'radios',
      '#options' => ['cpr' => $this->t('CPR'), 'name' => $this->t('Name')],
      '#title' => $this
        ->t('CPR output type'),
      '#required' => TRUE,
    ];

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
  public function prepare(array &$element, WebformSubmissionInterface $webform_submission = NULL) {
  }

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$element, array &$form, FormStateInterface $form_state) {
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

      $cprElement = &NestedArray::getValue($form['elements'], $element['#webform_parents']);
      $cprElement['#options'] = $cprData ? $this->formHelper->setChildSelectOptions($cprData, $element) : [];

      // Add form element on edit form with form item displaying value.
      if (substr_compare($form_state->getFormObject()->getFormId(), 'edit_form', -strlen('edit_form')) === 0) {
        // Hide the cpr element and insert a form item for displaying the value
        // before the cpr element.
        $cprElement['#access'] = FALSE;

        $parents = $element['#webform_parents'];
        array_pop($parents);
        $siblings = NestedArray::getValue($form['elements'], $parents);

        // Find index of cpr element in siblings list.
        $cprElementIndex = -1;
        $index = -1;
        foreach ($siblings as $sibling) {
          $index++;
          if (($sibling['#webform_key'] ?? NULL) === $cprElement['#webform_key']) {
            $cprElementIndex = $index;
            break;
          }
        }
        // Insert form item before cpr element.
        $submissionValue = $form['information']['#webform_submission']->getElementData($element['#webform_key']);
        // @see https://stackoverflow.com/a/1783125/2502647
        $siblings = array_slice($siblings, 0, $cprElementIndex, TRUE)
          + [
            $cprElement['#webform_id'] . '_value' => [
              '#type' => 'item',
              '#title' => $element['#title'],
              '#markup' => '<div>' . $submissionValue . '</div>',
            ],
          ]
          + array_slice($siblings, $cprElementIndex, NULL, TRUE);

        NestedArray::setValue($form['elements'], $parents, $siblings);
      }
    }
  }

}
