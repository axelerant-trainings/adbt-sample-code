<?php

namespace Drupal\adbt_caching\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates CachingOperationForm form.
 */
class CachingOperationForm extends FormBase {

  /**
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * Constructs a CachingOperationForm.
   *
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cache_tags_invalidator
   *   The cache tags invalidator.
   */
  public function __construct(CacheTagsInvalidatorInterface $cache_tags_invalidator) {
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('cache_tags.invalidator'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adbt_caching_operation_from';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['invalidate'] = [
      '#type' => 'details',
      '#title' => t('Invalidate Tags'),
      '#open' => TRUE,
    ];

    $form['invalidate']['invalidate_adbt_info'] = [
      '#type' => 'submit',
      '#value' => 'adbt_info',
      '#name' => 'adbt_info',
    ];
    $form['invalidate']['invalidate_adbt_new'] = [
      '#type' => 'submit',
      '#value' => 'adbt_new',
      '#name' => 'adbt_new',
    ];
    $form['invalidate']['invalidate_adbt_block'] = [
      '#type' => 'submit',
      '#value' => 'adbt_block',
      '#name' => 'adbt_block',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $tags = [$triggering_element['#name']];

    $this->cacheTagsInvalidator->invalidateTags($tags);

    $msg = $this->t('Tags @tags got invalidated successfully.', [
      '@tags' => implode(', ', $tags),
    ]);
    $this->messenger()->addMessage($msg);
  }

}
