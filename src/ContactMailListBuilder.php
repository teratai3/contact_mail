<?php

namespace Drupal\contact_mail;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;


class ContactMailListBuilder extends EntityListBuilder
{

  /**
   * {@inheritdoc}
   */
  public function buildHeader()
  {

    $header = [
      'id' => [
        'data' => 'ID',
        'field' => 'id',
        'specifier' => 'id',
        // 'sort' => 'desc',
      ],
      'title' => 'タイトル',
      'status' => [
        'data' => 'ステータス',
        'field' => 'status',
        'specifier' => 'status',
      ],
      'updated_at' => '送信日時',
    ];


    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity)
  {

    $row['id'] = $entity->id();
    $row['title'] = $entity->toLink($entity->label());

    // ステータスフィールドの値を取得
    $status_value = $entity->get('status')->value;

    // allowed_values 配列を取得してラベルを取得
    $allowed_values = $entity->getFieldDefinition('status')->getSetting('allowed_values');

    $row['status'] = isset($allowed_values[$status_value]) ? $allowed_values[$status_value] : $status_value;
    $row['updated_at'] = !is_null($entity->get('updated_at')->value) ? date("Y-m-d H:i:s", $entity->get('updated_at')->value) : "";

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render()
  {
    $build['add_button'] = [
      '#type' => 'link',
      '#title' => 'お問い合わせを新規追加',
      '#url' => Url::fromRoute('entity.contact_mail.add_form'),
      '#attributes' => [
        'class' => ['button', 'button--action', 'button--primary'],
        'style' => 'margin-right: 10px;',
      ],
    ];

    $build['csv_download'] = [
      '#type' => 'link',
      '#title' => 'CSVダウンロード',
      '#url' => Url::fromRoute('contact_mail.csv_download'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
    ];


    $build['table'] = parent::render();
    return $build;
  }

  public function load()
  {
    $query = $this->storage->getQuery()->accessCheck(true);

    // ソートを適用
    $header = $this->buildHeader();
    $query->tableSort($header);

    // ページングを設定
    $query->pager(50);

    // クエリを実行
    $ids = $query->execute();
    return $this->storage->loadMultiple($ids);
  }
}
