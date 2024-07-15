<?php

namespace Drupal\contact_mail\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
// use Drupal\contact_mail\Plugin\Validation\Constraint\KanaConstraint;
// use Drupal\contact_mail\Plugin\Validation\Constraint\TelConstraint;


/**
 * Defines the ContactMail entity.
 *
 * @ContentEntityType(
 *   id = "contact_mail",
 *   label = @Translation("お問い合わせ"),
 *   label_collection = @Translation("お問い合わせ"),
 *   base_table = "contact_mails",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name"
 *   },
 *   handlers = {
 *     "storage_schema" = "Drupal\contact_mail\ContactMailStorageSchema",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\contact_mail\ContactMailListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 * 
 *     "form" = {
 *       "default" = "Drupal\contact_mail\Form\ContactMailForm",
 *       "add" = "Drupal\contact_mail\Form\ContactMailForm",
 *       "edit" = "Drupal\contact_mail\Form\ContactMailForm",
 *       "delete" = "Drupal\contact_mail\Form\ContactMailDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   admin_permission = "administer site configuration",
 *   links = {
 *     "canonical" = "/admin/contact_mail/{contact_mail}",
 *     "edit-form" = "/admin/contact_mail/{contact_mail}/edit",
 *     "delete-form" = "/admin/contact_mail/{contact_mail}/delete",
 *     "collection" = "/admin/contact_mail",
 *     "add-form" = "/admin/contact_mail/add"
 *   },
 * )
 */
class ContactMail extends ContentEntityBase
{

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        // https://gist.github.com/cesarmiquel/48404d99c8f7d9f274705b7a601c5554
        // https://chatdeoshiete.com/node/3526

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel('お名前')
            ->setRequired(true)
            ->setSettings([
                'max_length' => 255,
            ])
            ->setDisplayOptions('view', [
                'label' => 'hidden',
                'type' => 'string',
                'weight' => 1,
            ])
            ->setDisplayOptions('form', [
                'type' => 'string',
                'weight' => 0,
            ]);

        $fields['name_kana'] = BaseFieldDefinition::create('string')
            ->setLabel('フリガナ')
            ->setRequired(true)
            ->setSettings([
                'max_length' => 255,
            ])
            ->addConstraint('KanaConstraint')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'string',
                'weight' => 1,
            ])
            ->setDisplayOptions('form', [
                'type' => 'string',
                'weight' => 0,
            ]);

        $fields['email'] = BaseFieldDefinition::create('email')
            ->setLabel('メールアドレス')
            ->setRequired(true)
            ->setSettings([
                'max_length' => 255,
            ])
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'email',
                'weight' => 1,
            ])
            ->setDisplayOptions('form', [
                'type' => 'email',
                'weight' => 0,
            ]);


        $fields['tel'] = BaseFieldDefinition::create('string')
            ->setLabel('電話番号')
            ->setSettings([
                'max_length' => 255,
            ])
            ->addConstraint('TelConstraint')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'string',
                'weight' => 1,
            ])
            ->setDisplayOptions('form', [
                'type' => 'string',
                'weight' => 0,
            ]);


        $fields['company_name'] = BaseFieldDefinition::create('string')
            ->setLabel('会社名')
            ->setSettings([
                'max_length' => 255,
            ])
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'string',
                'weight' => 1,
            ])
            ->setDisplayOptions('form', [
                'type' => 'string',
                'weight' => 0,
            ]);


        $fields['detail'] = BaseFieldDefinition::create('string_long')
            ->setLabel('お問い合わせ内容詳細')
            ->setDisplayOptions('view', [
                'label' => 'above',
                'type' => 'text',
                'weight' => 1,
            ])
            ->setDisplayOptions('form', [
                'type' => 'text',
                'weight' => 0,
            ]);

        $fields['status'] = BaseFieldDefinition::create('list_string')
            ->setLabel('ステータス')
            ->setRequired(true)
            ->setSettings([
                'allowed_values' => [
                    'pending' => '未対応',
                    'completed' => '完了',
                ],
            ])
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'list_default',
                'weight' => 1,
            ])
            ->setDisplayOptions('form', [
                'type' => 'options_select',
                'weight' => 0,
            ]);


        $fields['memo'] = BaseFieldDefinition::create('string_long')
            ->setLabel('管理者メモ')
            ->setDisplayOptions('view', [
                'label' => 'above',
                'type' => 'text_long',
                'weight' => 1,
            ])
            ->setDisplayOptions('form', [
                'type' => 'text_textarea',
                'weight' => 0,
            ]);

        $fields['created_at'] = BaseFieldDefinition::create('created')
            ->setLabel("作成日時")
            ->setRequired(true);

        $fields['updated_at'] = BaseFieldDefinition::create('changed')
            ->setLabel("更新日時")
            ->setRequired(true);

        return $fields;
    }
}
