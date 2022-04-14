<?php

namespace Drupal\tinyurl\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new url entity.
 *
 * @DataProducer(
 *   id = "create_url",
 *   name = @Translation("Create url"),
 *   description = @Translation("Creates a new url."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("url")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("url data")
 *     )
 *   }
 * )
 */
class CreateUrl extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  /**
   * CreateUrl constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * Creates an url.
   *
   * @param array $data
   *   The Title of the job.
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
   *   The newly created url.
   *
   * @throws \Exception
   */
  public function resolve(array $data) {
    if ($this->currentUser->hasPermission("create url content")) {
      $values = [
        'type' => 'url',
        'field_slug' => $data['slug'],
        'field_url' => [
          'uri'=> $data['url']
        ]
      ];
      $node = Node::create($values);
      $node->save();
      return $node;
    }
    return NULL;
  }

}