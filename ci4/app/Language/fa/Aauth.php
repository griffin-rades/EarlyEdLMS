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
 * Language Farsi/Persian
 *
 * @package CodeIgniter-Aauth
 *
 * @codeCoverageIgnore
 */
return [
   'subjectVerification'    => 'تایید حساب کاربری',
   'subjectReset'           => 'بازنشانی کلمه عبور',
   'subjectResetSuccess'    => 'کلمه عبور با موفقیت بازنشانی شد',

   'textVerification'       => "کد تایید شما: {code}. شما همچنین میتوانید بر روی لینک زیر کلیک کنید و یا آن را در نوار آدرس مرورگر وارد کنید\n\n {link}",
   'textReset'              => "برای تغییر کلمه عبور خود بر روی لینک زیر کلیک کنید و یا آن را در نوار آدرس مرورگر وارد کنید\n\n {link}",
   'textResetSuccess'       => 'کلمه عبور شما با موفقیت تغییر کرد. کلمه عبور جدید شما: {password}',

   'infoCreateSuccess'      => 'Your account has successfully been created. You can now login.',
   'infoCreateVerification' => 'Your account has successfully been created. A email has been sent to your email address with verification details..',
   'infoUpdateSuccess'      => 'Your account has successfully updated.',
   'infoRemindSuccess'      => 'A email has been sent to your email address with reset instructions.',
   'infoResetSuccess'       => 'A email has been sent to your email address with your new password has been sent.',
   'infoVerification'       => 'Your account has been verified successfully, you can now login.',

   'noAccess'               => 'متاسفانه شما به منبع درخواست شده دسترسی ندارید.',
   'notVerified'            => 'حساب کاربری شما تایید نشده است. لطفا ایمیل خود را برای تایید حسا کاربری بررسی کنید.',

   'loginFailedEmail'       => 'ایمیل و کلمه عبور همخوانی ندارند.',
   'loginFailedUsername'    => 'نام کاربری و کلمه عبور همخوانی ندارند.',
   'loginFailedAll'         => 'ایمیل یا نام کاربری با کلمه عبور همخوانی ندارد.',
   'loginAttemptsExceeded'  => 'شما بیش از حد مجاز برای ورود به سایت تلاش کردید. حساب کاربری شما موقتا غیر فعال شد.',

   'invalidUserBanned'      => 'This user is banned, please contact the system administrator.',
   'invalidEmail'           => 'آدرس ایمیل نامعتبر است',
   'invalidPassword'        => 'کلمه عبور نامعتبر است',
   'invalidUsername'        => 'نام کاربری نامعتبر است',
   'invalidVerficationCode' => 'Invalid Verification Code',
   'invalidCaptcha'         => 'کد کپتچا به درستی وارد نشده.',
   'invalidTOTPCode'        => 'کد احراز هویت نامعتبر است',

   'requiredUsername'       => 'ورود نام کاربری الزامی است',
   'requiredGroupName'      => 'Group name required',
   'requiredPermName'       => 'Perm name required',
   'requiredTOTPCode'       => 'ورود کد احراز هویت الزامی است',

   'existsAlreadyEmail'     => 'آدرس ایمیل در سیستم موجود است. در صورتی که کلمه عبور خود را فراموش کردید، میتوانید بر روی لینک زیر کلیک کنید.',
   'existsAlreadyUsername'  => 'نام کاربری وارد شده در سیستم موجود هست. لطفا یک نام کاربری دیگر انتخاب کنید، و یا اگر کلمه عبور خود را فراموش کرده اید بر روی لینک زیر کلیک کنید.',
   'existsAlreadyGroup'     => 'نام گروه از قبل موجود است',
   'existsAlreadyPerm'      => 'سطح دسترسی از قبل موجود است',

   'notFoundUser'           => 'نام کاربری وجود ندارد',
   'notFoundGroup'          => 'گروه موجود نیست',
   'notFoundSubgroup'       => 'زیرگروه موجود نیست',

   'alreadyMemberGroup'     => 'کاربر از قبل عضو این گروه می باشد',
   'alreadyMemberSubgroup'  => 'زیرگروه از قبل شامل این گروه می باشد',
];
