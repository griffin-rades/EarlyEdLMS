<?php
/**
 * CodeIgniter-Aauth
 *
 * Aauth is a User Authorization Library for CodeIgniter 4.x, which aims to make
 * easy some essential jobs such as login, permissions and access operations.
 * Despite ease of use, it has also very advanced features like grouping,
 * access management, public access etc..
 *
 * @package   CodeIgniter-Aauth
 * @author    Emre Akay
 * @author    Raphael "REJack" Jackstadt
 * @copyright 2014-2019 Emre Akay
 * @license   https://opensource.org/licenses/MIT   MIT License
 * @link      https://github.com/emreakay/CodeIgniter-Aauth
 */

/**
 * Aauth language strings.
 *
 * Language Russian
 *
 * @package CodeIgniter-Aauth
 *
 * @codeCoverageIgnore
 */
return [
   'subjectVerification'    => 'Подтверждение аккаунта',
   'subjectReset'           => 'Сброс пароля',
   'subjectResetSuccess'    => 'Сброс пароля выполнен',

   'textVerification'       => "Ваш код подтверждения: {code}. Так же вы можете нажать (или скопировать/вставить в адресную строку браузера) следующую ссылку\n\n {link}",
   'textReset'              => "Для сброса пароля нажмите (или скопируйте/вставьте в адресную строку браузера) ссылку:\n\n {link}",
   'textResetSuccess'       => 'Ваш пароль сброшен. Ваш новый пароль: {password}',

   'infoCreateSuccess'      => 'Your account has successfully been created. You can now login.',
   'infoCreateVerification' => 'Your account has successfully been created. A email has been sent to your email address with verification details..',
   'infoUpdateSuccess'      => 'Your account has successfully updated.',
   'infoRemindSuccess'      => 'A email has been sent to your email address with reset instructions.',
   'infoResetSuccess'       => 'A email has been sent to your email address with your new password has been sent.',
   'infoVerification'       => 'Your account has been verified successfully, you can now login.',

   'noAccess'               => 'Извините, у вас нет доступа к запрашиваемому ресурсу.',
   'notVerified'            => 'Ваш акккаунт не подтвержден. Проверьте ваш ящик e-mail и подтвердите аккаунт.',

   'loginFailedEmail'       => 'Неверный email или пароль.',
   'loginFailedUsername'    => 'Неверное имя пользователя или пароль.',
   'loginFailedAll'         => 'Неверный E-mail, имя пользователя или пароль.',
   'loginAttemptsExceeded'  => 'Количество попыток входа превышено, ваш аккаунт временно заблокирован.',

   'invalidUserBanned'      => 'This user is banned, please contact the system administrator.',
   'invalidEmail'           => 'Некорректный адрес e-mail',
   'invalidPassword'        => 'Некорректный пароль',
   'invalidUsername'        => 'Некорректное имя пользователя',
   'invalidVerficationCode' => 'Invalid Verification Code',
   'invalidCaptcha'         => 'Извините, текст с CAPTCHA введен неверно.',
   'invalidTOTPCode'        => 'Неверный код аутентификации',

   'requiredUsername'       => 'Логин обязателен',
   'requiredGroupName'      => 'Group name required',
   'requiredPermName'       => 'Perm name required',
   'requiredTOTPCode'       => 'Требуется код аутентификации',

   'existsAlreadyEmail'     => 'Email уже зарегистрирован в системе. Если вы забыли ваш пароль, нажмите на ссылку ниже.',
   'existsAlreadyUsername'  => 'Аккаунт с этим именен пользователя уже есть в системе.  Введите другое имя пользователя, или если вы забыли ваш пароль, нажмите на ссылку ниже.',
   'existsAlreadyGroup'     => 'Такое имя группы уже есть',
   'existsAlreadyPerm'      => 'Такое имя разрешений уже есть',

   'notFoundUser'           => 'Пользователь не существует',
   'notFoundGroup'          => 'Группа не существует',
   'notFoundSubgroup'       => 'Подгруппа не существует',

   'alreadyMemberGroup'     => 'Пользователь уже состоит в группе',
   'alreadyMemberSubgroup'  => 'Подгруппа состоит в группе',
];
