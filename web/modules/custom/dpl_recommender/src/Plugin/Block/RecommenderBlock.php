<?php

namespace Drupal\dpl_recommender\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\dpl_react\DplReactConfigInterface;
use Drupal\dpl_react_apps\Controller\DplReactAppsController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a "recommender" component.
 *
 * @Block(
 *   id = "dpl_recommender_block",
 *   admin_label = "Recommender"
 * )
 */
class RecommenderBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * RecommenderBlock constructor.
   *
   * @param mixed[] $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Drupal config factory to get FBS and Publizon settings.
   * @param \Drupal\dpl_react\DplReactConfigInterface $recommenderSettings
   *   Recommender settings.
   */
  public function __construct(
      array $configuration,
      $plugin_id,
      $plugin_definition,
      protected ConfigFactoryInterface $configFactory,
      protected DplReactConfigInterface $recommenderSettings
    ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configuration = $configuration;
  }

  /**
   * {@inheritDoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @param mixed[] $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param int $plugin_definition
   *   The plugin implementation definition.
   *
   * @return \Drupal\dpl_recommender\Plugin\Block\RecommenderBlock|static
   *   Recommender.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      \Drupal::service('recommender.settings')
    );
  }

  /**
   * {@inheritDoc}
   *
   * @return mixed[]
   *   The app render array.
   */
  public function build() {
    $recommenderSettings = $this->recommenderSettings->getConfig();

    $data = [
      // Urls.
      'dpl-cms-base-url' => DplReactAppsController::dplCmsBaseUrl(),
      'material-url' => DplReactAppsController::materialUrl(),

      // Texts.
      'add-to-favorites-aria-label-text' => $this->t("Add element to favorites list", [], ['context' => 'Recommender (Aria)']),
      'empty-recommender-search-config' => $recommenderSettings['searchText'],
      'material-and-author-text' => $this->t("and", [], ['context' => 'Recommender']),
      'material-by-author-text' => $this->t("By", [], ['context' => 'Recommender']),
      'recommender-title-inspiration-text' => $this->t("For your inspiration", [], ['context' => 'Recommender']),
      'recommender-title-loans-text' => $this->t("Because you have borrowed @title you may also like", [], ['context' => 'Recommender']),
      'recommender-title-reservations-text' => $this->t("Because you have reserved @title you may also like", [], ['context' => 'Recommender']),
      'remove-from-favorites-aria-label-text' => $this->t("Remove element from favorites list", [], ['context' => 'Recommender (Aria)']),
    ] + DplReactAppsController::externalApiBaseUrls();

    return [
      '#theme' => 'dpl_react_app',
      "#name" => 'recommender',
      '#data' => $data,
    ];
  }

}
