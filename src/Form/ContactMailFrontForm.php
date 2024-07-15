<?php

namespace Drupal\contact_mail\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\contact_mail\Entity\ContactMail;
use Drupal\Core\Url;
use Symfony\Component\Validator\Validation;
use Drupal\contact_mail\Plugin\Validation\Constraint\KanaConstraint;
use Drupal\contact_mail\Plugin\Validation\Constraint\TelConstraint;
// use Symfony\Component\HttpFoundation\RedirectResponse;

class ContactMailFrontForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'contact_mail_front_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $step = $form_state->get('step');
        if ($step == 'confirm') {
            // 確認画面のビルド
            $form = $this->buildConfirmForm($form, $form_state);
        } else {
            // 入力フォームのビルド
            $form = $this->buildInputForm($form, $form_state);
        }
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $validator = Validation::createValidator();

        $values = $form_state->getValues();


        $violations = $validator->validate($values["name_kana"], [
            new KanaConstraint(),
        ]);

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $form_state->setErrorByName('name_kana', $violation->getMessage());
            }
        }


        //dump($values["tel"]);exit;
        if ($values["tel"] !== "") {
            // 電話番号のバリデーション
            $violations = $validator->validate($values["tel"], [
                new TelConstraint(),
            ]);

            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $form_state->setErrorByName('tel', $violation->getMessage());
                }
            }
        }
    }

    /**
     * 入力フォームのビルド.
     */
    protected function buildInputForm(array $form, FormStateInterface $form_state)
    {
        //$form['#action'] = Url::fromRoute('contact_mail.form_confirm')->toString();

        $form['name'] = [
            '#type' => 'textfield',
            '#title' => 'お名前',
            '#required' => true,
        ];

        $form['name_kana'] = [
            '#type' => 'textfield',
            '#title' => 'フリガナ',
            '#required' => true,

        ];

        $form['email'] = [
            '#type' => 'email',
            '#title' => 'メールアドレス',
            '#required' => true,
        ];

        $form['tel'] = [
            '#type' => 'textfield',
            '#title' => '電話番号',
        ];

        $form['company_name'] = [
            '#type' => 'textfield',
            '#title' => '会社名',
        ];

        $form['detail'] = [
            '#type' => 'textarea',
            '#title' => 'お問い合わせ内容詳細',
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => '送信',
            '#button_type' => 'primary',
        ];

        return $form;
    }

    /**
     * 確認画面のビルド.
     */
    protected function buildConfirmForm(array $form, FormStateInterface $form_state)
    {
        $form_values = $form_state->getValues();
        $form['info'] = [
            '#type' => 'container',
            '#attributes' => ['class' => ['contact-mail-confirm']],
        ];

        $form_definitions = $this->buildInputForm([], $form_state);

        foreach ($form_values as $key => $value) {
            if (!isset($form_definitions[$key]['#title']) || $key == "submit") continue;

            $title = $form_definitions[$key]['#title'];

            $form['info'][$key] = [
                '#markup' => "<div><p>{$title}</p><p>{$value}</p></div>", //xss対策されているので、そのまま渡す
            ];
        }

        $form['name'] = [
            '#type' => 'hidden',
            '#required' => true,
        ];

        $form['name_kana'] = [
            '#type' => 'hidden',
            '#required' => true,
        ];

        $form['email'] = [
            '#type' => 'email', //メールアドレスチェックをしたいので、hiddenではなくemail
            '#required' => true,
            '#attributes' => [
                'style' => 'display: none;',
            ],
        ];

        $form['tel'] = [
            '#type' => 'hidden',
        ];

        $form['company_name'] = [
            '#type' => 'hidden',
            '#title' => '会社名',
        ];

        $form['detail'] = [
            '#type' => 'hidden',
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => '送信',
            '#button_type' => 'primary',
        ];

        $form['back'] = [
            '#type' => 'submit',
            '#value' => '戻る',
            '#attributes' => [
                'onclick' => 'history.back(); return false;',
            ],
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => '送信',
            '#button_type' => 'primary',
        ];

        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
       
        if ($form_state->get('step') == 'confirm') {
            // 新しい ContactMail エンティティを作成
         
            try{
                $contact_mail = ContactMail::create([
                    'name' => $form_state->getValue('name'),
                    'name_kana' => $form_state->getValue('name_kana'),
                    'email' => $form_state->getValue('email'),
                    'tel' => $form_state->getValue('tel'),
                    'company_name' => $form_state->getValue('company_name'),
                    'detail' => $form_state->getValue('detail'),
                    'status' => 'pending'
                ]);
                $contact_mail->save();
            }catch(\Exception $e){
                \Drupal::messenger()->addMessage($e->getMessage(), 'error');
                return $form_state->setRedirect('contact_mail.form');
            }
            

            $module = 'contact_mail';
            $mailManager = \Drupal::service('plugin.manager.mail');
            $key = 'mail_send';
            $to = \Drupal::config('system.site')->get('mail');

            $params['title'] = \Drupal::config('system.site')->get('name') . "にお問い合わせがありました";

            $params['message'] = "-------入力内容-------\n";
            $params['message'] .= "お名前：" . $form_state->getValue('name') . "\n";
            $params['message'] .= "フリガナ：" . $form_state->getValue('name_kana') . "\n";
            $params['message'] .= "メールアドレス：" . $form_state->getValue('email') . "\n";
            $params['message'] .= "電話番号：" . $form_state->getValue('tel') . "\n";
            $params['message'] .= "会社名：" . $form_state->getValue('company_name') . "\n";
            $params['message'] .= "お問い合わせ内容詳細：" . $form_state->getValue('detail') . "\n";
            $lang_code = \Drupal::config('system.site')->get('langcode');
            $send = true;

            $result = $mailManager->mail($module, $key, $to, $lang_code, $params, null, $send);


            // ユーザー宛のメール
            $user_params['title'] = \Drupal::config('system.site')->get('name') . "へのお問い合わせありがとうございます";
            $user_params['message'] = "お問い合わせいただきありがとうございます。\n";
            $user_params['message'] .= "以下の内容でお問い合わせを受け付けました。\n\n";
            $user_params['message'] .= "-------入力内容-------\n";
            $user_params['message'] .= "お名前：" . $form_state->getValue('name') . "\n";
            $user_params['message'] .= "フリガナ：" . $form_state->getValue('name_kana') . "\n";
            $user_params['message'] .= "メールアドレス：" . $form_state->getValue('email') . "\n";
            $user_params['message'] .= "電話番号：" . $form_state->getValue('tel') . "\n";
            $user_params['message'] .= "会社名：" . $form_state->getValue('company_name') . "\n";
            $user_params['message'] .= "お問い合わせ内容詳細：" . $form_state->getValue('detail') . "\n";

            $result_user = $mailManager->mail($module, $key, $form_state->getValue('email'), $lang_code, $user_params, null, $send);


            if ($result['result'] !== true || $result_user['result'] !== true) {
                \Drupal::messenger()->addMessage('メールの送信に失敗しました。', 'error');
            } else {
                \Drupal::messenger()->addMessage('お問い合わせを受け付けました。');
            }

            return $form_state->setRedirect('contact_mail.form');
        } else {
            // 確認画面に遷移
            $form_state->set('step', 'confirm');
            $form_state->setRebuild(true);
        }
    }
}
