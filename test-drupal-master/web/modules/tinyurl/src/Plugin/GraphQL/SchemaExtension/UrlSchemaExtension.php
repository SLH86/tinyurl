<?php

namespace Drupal\tinyurl\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
/**
 * @SchemaExtension(
 *   id = "url_extension",
 *   name = "Tinyurl extension",
 *   description = "A simple url shortener node.",
 *   schema = "composable_extension"
 * )
 */
class urlSchemaExtension extends SdlSchemaExtensionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry)
  {
    $builder = New ResolverBuilder();

    // First we add our top level Query based fields to the Schema.
    $this->addQueryFields($registry, $builder);
    // Next we add the url entity specific fields to the schema.
    $this->addurlFields($registry, $builder);

    $registry->addFieldResolver('Mutation', 'CreateUrl',
    $builder->produce('create_url')
      ->map('data', $builder->fromArgument('data'))
    );
  }

  /**
   * @param \Drupal\graphql\GraphQL\ResolverRegistryInterface $registry
   * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
   */
  protected function addurlFields(ResolverRegistryInterface $registry, ResolverBuilder $builder) {
    $registry->addFieldResolver('url', 'id',
      $builder->produce('entity_id')
      ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('url', 'slug',
      $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_slug.value'))
    );

    $registry->addFieldResolver('url', 'url',
      $builder->produce('property_path')
      ->map('type', $builder->fromValue('entity:node'))
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('field_url.uri'))
    );
  }

  /**
   * @param ResolverRegistryInterface $registry
   * @param ResolverBuilder $builder
   */
  protected function addQueryFields(ResolverRegistryInterface $registry, ResolverBuilder $builder) {
    $registry->addFieldResolver('Query', 'url',
      $builder->produce('entity_load')
      ->map('type', $builder->fromValue('node'))
      ->map('bundles', $builder->fromValue(['url']))
      ->map('id', $builder->fromArgument('id'))
    );
  }
}
