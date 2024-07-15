<?php

namespace Drupal\contact_mail\Controller;

use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\contact_mail\Entity\ContactMail;

class ContactMailController extends ControllerBase
{

  public function csv()
  {
    $header = [
      'ID',
      '名前',
      'フリガナ',
      'メールアドレス',
      '電話番号',
      '会社名',
      'お問い合わせ内容詳細',
      'ステータス',
      'メモ',
      '作成日時',
      '更新日時',
    ];

    $rows = $this->getContactMailData();

    $filename = 'contact_mails_' . date('Y-m-d') . '.csv';

    $csvContent = $this->arrayToCsv($header, $rows);

    $response = new Response($csvContent);
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

    return $response;
  }


  private function getContactMailData()
  {
    $query = Database::getConnection()->select('contact_mails', 'cm')->fields('cm')->orderBy('id','DESC');
    $result = $query->execute();

    $rows = [];

    if (empty($result)) return $rows;

   $status_field = ContactMail::create([])->getFieldDefinition('status');
   $allowed_values = $status_field->getSetting('allowed_values'); // Status allowed valuesを取得


    foreach ($result as $record) {
      $rows[] = [
        $record->id,
        $record->name,
        $record->name_kana,
        $record->email,
        $record->tel,
        $record->company_name,
        $record->detail,
        isset($allowed_values[$record->status]) ? $allowed_values[$record->status] : $record->status,
        $record->memo,
        !is_null($record->created_at) ? date('Y-m-d H:i:s', $record->created_at) : "",
        !is_null($record->updated_at) ? date('Y-m-d H:i:s', $record->updated_at) : "",
      ];
    }

    return $rows;
  }

  private function arrayToCsv(array $header, array $rows)
  {
    $output = fopen('php://temp', 'w'); //1.一時的なファイルポインタを作成

    fputcsv($output, $header);

    foreach ($rows as $row) {
      fputcsv($output, $row); //2.ファイルポインタに書き込む
    }

    rewind($output); //3.ファイルポインタの位置を先頭に戻す

    $data = stream_get_contents($output);
    fclose($output);
    
    $data = mb_convert_encoding($data, 'SJIS-win', 'UTF-8');

    return $data;
  }
}
