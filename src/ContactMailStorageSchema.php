<?php

namespace Drupal\contact_mail;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;
use Drupal\Core\Field\FieldStorageDefinitionInterface;


class ContactMailStorageSchema extends SqlContentEntityStorageSchema
{

  const NOT_NULLS = [
    'name',
    'name_kana',
    'email',
    'status'
  ];
  
  /**
   * {@inheritdoc}
   */
  protected function getSharedTableFieldSchema(FieldStorageDefinitionInterface $storage_definition, $table_name, array $column_mapping)
  {
    $schema = parent::getSharedTableFieldSchema($storage_definition, $table_name, $column_mapping);
    $field_name = $storage_definition->getName();

    if ($table_name === 'contact_mails' && in_array($field_name, self::NOT_NULLS)) {
      $schema['fields'][$field_name]['not null'] = true;
    }

    if($table_name === 'contact_mails' && $field_name === "status"){
      $schema['fields'][$field_name]['default'] = 'pending';
    }

    return $schema;
  }
}
