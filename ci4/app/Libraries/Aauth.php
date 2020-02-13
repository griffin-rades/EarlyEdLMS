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
 * @version   3.0.0-rc2
 */

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Aauth Library
 *
 * @package CodeIgniter-Aauth
 */
class Aauth
{
	/**
	 * Variable for loading the config array into
	 *
	 * @var \Config\Aauth
	 */
	protected $config;

	/**
	 * Variable for loading the app config array into
	 *
	 * @var \Config\App
	 */
	protected $configApp;

	/**
	 * Variable for loading the session service into
	 *
	 * @var \CodeIgniter\Session\Session
	 */
	protected $session;

	/**
	 * Array with modules
	 *
	 * @var array
	 */
	protected $modules = [];

	/**
	 * Array to store error messages
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Local temporary storage for current flash errors
	 *
	 * Used to update current flash data list since flash data is only available on the next page refresh
	 *
	 * @var array
	 */
	protected $flashErrors = [];

	/**
	 * Array to store info messages
	 *
	 * @var array
	 */
	protected $infos = [];

	/**
	 * Local temporary storage for current flash infos
	 *
	 * Used to update current flash data list since flash data is only available on the next page refresh
	 *
	 * @var array
	 */
	protected $flashInfos = [];

	/**
	 * Array to cache permission-ids.
	 *
	 * @var array
	 */
	protected $cachePermIds;

	/**
	 * Array to cache group-ids.
	 *
	 * @var array
	 */
	protected $cacheGroupIds;

	/**
	 * Constructor
	 *
	 * Prepares config & session variable.
	 *
	 * @param \Config\Aauth                $config  Config Object
	 * @param \CodeIgniter\Session\Session $session Session Class
	 *
	 * @return void
	 */
	public function __construct(\Config\Aauth $config = null, \CodeIgniter\Session\Session $session = null)
	{
		if (! $config)
		{
			$config = new \Config\Aauth();
		}

		if (! $session)
		{
			$session = \Config\Services::session();
		}

		$this->configApp = new \Config\App();
		$this->config    = $config;
		$this->session   = $session;
		$this->modules   = $this->config->modules;

		if ($this->config->captchaEnabled)
		{
			$this->modules = array_merge($this->modules, ['CAPTCHA']);
		}

		if ($this->config->totpEnabled)
		{
			$this->modules = array_merge($this->modules, ['TOTP']);
		}

		if ($this->config->socialEnabled)
		{
			$this->modules = array_merge($this->modules, ['Social']);
		}

		$this->cachePermIds  = [];
		$this->cacheGroupIds = [];

		$this->precachePerms();
		$this->precacheGroups();
	}

	//--------------------------------------------------------------------
	// Caching Functions
	//--------------------------------------------------------------------

	/**
	 * PreCache Perms
	 *
	 * Caches all permission IDs for later use.
	 *
	 * @return void
	 */
	private function precachePerms()
	{
		$permModel = $this->getModel('Perm');

		foreach ($permModel->asArray()->findAll() as $perm)
		{
			$key                      = str_replace(' ', '', trim(strtolower($perm['name'])));
			$this->cachePermIds[$key] = $perm['id'];
		}
	}

	/**
	 * PreCache Groups
	 *
	 * Caches all group IDs for later use.
	 *
	 * @return void
	 */
	private function precacheGroups()
	{
		$groupModel = $this->getModel('Group');

		foreach ($groupModel->asArray()->findAll() as $group)
		{
			$key                       = str_replace(' ', '', trim(strtolower($group['name'])));
			$this->cacheGroupIds[$key] = $group['id'];
		}
	}

	//--------------------------------------------------------------------
	// Login Functions
	//--------------------------------------------------------------------

	/**
	 * Login user
	 *
	 * Check provided details against the database. Add items to error array on fail
	 *
	 * @param string  $identifier Identifier
	 * @param string  $password   Password
	 * @param boolean $remember   Whether to remember login
	 * @param string  $totpCode   TOTP Code
	 *
	 * @return boolean
	 */
	public function login(string $identifier, string $password, bool $remember = null, string $totpCode = null)
	{
		helper('cookie');
		delete_cookie($this->config->loginRememberCookie);

		$userModel         = $this->getModel('User');
		$loginAttemptModel = $this->getModel('LoginAttempt');
		$userVariableModel = $this->getModel('UserVariable');

		if ($this->config->loginProtection && ! $loginAttemptModel->save())
		{
			$this->error(lang('Aauth.loginAttemptsExceeded'));

			return false;
		}

		if ($this->config->loginProtection && $this->config->captchaEnabled && $this->isCaptchaRequired())
		{
			$request = \Config\Services::request();

			if ($this->config->captchaType === 'recaptcha')
			{
				$response = $request->getPostGet('g-recaptcha-response');
			}
			else if ($this->config->captchaType === 'hcaptcha')
			{
				$response = $request->getPostGet('h-captcha-response');
			}

			if (! $this->verifyCaptchaResponse((string) $response)['success'])
			{
				$this->error(lang('Aauth.invalidCaptcha'));

				return false;
			}
		}

		if ($this->config->loginUseUsername)
		{
			if (! $identifier || strlen($password) < $this->config->passwordMin || strlen($password) > $this->config->passwordMax)
			{
				$this->error(lang('Aauth.loginFailedUsername'));

				return false;
			}

			if (! $user = $userModel->where('username', $identifier)->asArray()->first())
			{
				$this->error(lang('Aauth.notFoundUser'));

				return false;
			}
		}
		else
		{
			$validation = \Config\Services::validation();

			if (! $validation->check($identifier, 'valid_email') || strlen($password) < $this->config->passwordMin || strlen($password) > $this->config->passwordMax)
			{
				$this->error(lang('Aauth.loginFailedEmail'));

				return false;
			}

			if (! $user = $userModel->where('email', $identifier)->asArray()->first())
			{
				$this->error(lang('Aauth.notFoundUser'));

				return false;
			}
		}

		if (! empty($userVariableModel->find($user['id'], 'verification_code', true)))
		{
			$this->error(lang('Aauth.notVerified'));
			return false;
		}
		else if ($user['banned'])
		{
			$this->error(lang('Aauth.invalidUserBanned'));
			return false;
		}

		if ($this->config->totpEnabled)
		{
			$totpSecret = $userVariableModel->find($user['id'], 'totp_secret', true);
			$request    = \Config\Services::request();

			if ($this->config->totpLogin)
			{
				if (! $this->config->totpOnIpChange)
				{
					if (! empty($totpSecret) && ! $totpCode)
					{
						$this->error(lang('Aauth.requiredTOTPCode'));

						return false;
					}
					else if (! $this->verifyUserTotpCode($totpCode, $user['id']))
					{
						$this->error(lang('Aauth.invalidTOTPCode'));

						return false;
					}
				}
				else if ($this->config->totpOnIpChange)
				{
					if ($request->getIPAddress() !== $user['last_ip_address'])
					{
						if (! empty($totpSecret) && ! $totpCode)
						{
							$this->error(lang('Aauth.requiredTOTPCode'));

							return false;
						}
						else if (! $this->verifyUserTotpCode($totpCode, $user['id']))
						{
							$this->error(lang('Aauth.invalidTOTPCode'));

							return false;
						}
					}
				}
			}
			else if (! $this->config->totpLogin)
			{
				if (! $this->config->totpOnIpChange)
				{
					$this->session->set('totp_required', true);
				}
				else if ($this->config->totpOnIpChange)
				{
					if ($request->getIPAddress() !== $user['last_ip_address'])
					{
						$this->session->set('totp_required', true);
					}
				}
			}
		}

		if (password_verify($password, $user['password']))
		{
			$loginTokenModel = $this->getModel('LoginToken');

			if ($this->config->loginSingleMode)
			{
				$loginTokenModel->deleteAll($user['id']);
				$userSessionModel = $this->getModel('UserSession');

				foreach ($userSessionModel->findAll() as $userSessionRow)
				{
					$result      = $matches = [];
					$sessionData = ';' . $userSessionRow['data'];
					$keyreg      = '/;([^|{}"]+)\|/';

					preg_match_all($keyreg, $sessionData, $matches);

					if (isset($matches[1]))
					{
						$keys   = $matches[1];
						$values = preg_split($keyreg, $sessionData);

						if (count($values) > 1)
						{
							array_shift($values);
						}

						$result      = array_combine($keys, $values);
						$userSession = unserialize($result['user']);

						if ($userSession['id'] === $user['id'])
						{
							$userSessionModel->delete($userSessionRow['id']);
						}
					}
				}
			}

			$data['id']       = $user['id'];
			$data['username'] = $user['username'];
			$data['email']    = $user['email'];
			$data['loggedIn'] = true;
			$this->session->set('user', $data);

			if ($remember)
			{
				$this->generateRemember($user['id']);
			}

			$userModel->updateLastLogin($user['id']);

			if ($this->config->loginAttemptRemoveSuccessful)
			{
				$loginAttemptModel->delete();
			}

			return true;
		}
		else
		{
			if ($this->config->loginAccurateErrors)
			{
				if ($this->config->loginUseUsername)
				{
					$this->error(lang('Aauth.loginFailedUsername'));
				}
				else
				{
					$this->error(lang('Aauth.loginFailedEmail'));
				}
			}
			else
			{
				$this->error(lang('Aauth.loginFailedAll'));
			}

			return false;
		}
	}

	/**
	 * Generate Remember
	 *
	 * @param integer        $userId User Id
	 * @param string|integer $expire Expire Date, relative Date or Timestamp
	 *
	 * @return void
	 */
	protected function generateRemember(int $userId, string $expire = null)
	{
		helper('cookie');
		helper('text');

		if (! $expire)
		{
			$expire = $this->config->loginRemember;
		}

		$userIdEncoded  = base64_encode($userId);
		$randomString   = random_string('alnum', 32);
		$selectorString = random_string('alnum', 16);

		$cookieData['name']   = $this->config->loginRememberCookie;
		$cookieData['value']  = $userIdEncoded . ';' . $randomString . ';' . $selectorString;
		$cookieData['expire'] = YEAR;

		\Config\Services::response()->setCookie($cookieData)->send();

		$tokenData['user_id']       = $userId;
		$tokenData['random_hash']   = password_hash($randomString, PASSWORD_DEFAULT);
		$tokenData['selector_hash'] = password_hash($selectorString, PASSWORD_DEFAULT);
		$tokenData['expires_at']    = date('Y-m-d H:i:s', strtotime($expire));

		$loginTokenModel = $this->getModel('LoginToken');
		$loginTokenModel->insert($tokenData);
	}

	/**
	 * Logout
	 *
	 * Deletes session and cookie
	 *
	 * @return void
	 */
	public function logout()
	{
		helper('cookie');

		$cookieData['name']   = $this->config->loginRememberCookie;
		$cookieData['value']  = '';
		$cookieData['expire'] = -YEAR;

		\Config\Services::response()->setCookie($cookieData)->send();

		$this->session->remove('user');
		@$this->session->destroy();
	}

	/**
	 * Fast login
	 *
	 * Login with just a user id
	 *
	 * @param integer $userId User id
	 *
	 * @return boolean
	 */
	private function loginFast(int $userId)
	{
		$userModel = $this->getModel('User');
		$userModel->select('id, email, username');
		$userModel->where('id', $userId);
		$userModel->where('banned', 0);

		$user = $userModel->asArray()->first();

		$this->session->set('user', [
			'id'       => $user['id'],
			'username' => $user['username'],
			'email'    => $user['email'],
			'loggedIn' => true,
		]);

		return true;
	}

	//--------------------------------------------------------------------
	// Access Functions
	//--------------------------------------------------------------------

	/**
	 * Check user login
	 *
	 * Checks if user logged in, also checks remember.
	 *
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		helper('cookie');

		if (isset($this->session->get('user')['loggedIn']))
		{
			return true;
		}
		else if ($cookie = get_cookie($this->config->loginRememberCookie))
		{
			$cookie    = explode(';', $cookie);
			$cookie[0] = base64_decode($cookie[0]);

			if (! is_numeric($cookie[0]) || strlen($cookie[1]) !== 32 || strlen($cookie[2]) !== 16)
			{
				return false;
			}
			else
			{
				$loginTokenModel = $this->getModel('LoginToken');
				$loginTokens     = $loginTokenModel->findAllByUserId($cookie[0]);

				foreach ($loginTokens as $loginToken)
				{
					if (password_verify($cookie[1], $loginToken['random_hash']) && password_verify($cookie[2], $loginToken['selector_hash']))
					{
						if (strtotime($loginToken['expires_at']) > strtotime('now'))
						{
							$loginTokenModel->update($loginToken['id']);

							if ($this->config->socialEnabled && $this->config->socialRemember)
							{
								$this->rebuildSocialStorage($loginToken['user_id']);
							}

							return $this->loginFast($loginToken['user_id']);
						}
						else
						{
							$loginTokenModel->deleteExpired($cookie[0]);
							delete_cookie($this->config->loginRememberCookie);
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * Is member
	 *
	 * @param integer|string $groupPar Group id or name to check
	 * @param integer        $userId   User id, if not given current user
	 *
	 * @return boolean
	 */
	public function isMember($groupPar, int $userId = null)
	{
		$userModel = $this->getModel('User');

		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$groupToUserModel = $this->getModel('GroupToUser');

		return $groupToUserModel->exists($groupId, $userId);
	}

	/**
	 * Is admin
	 *
	 * @param integer $userId User id to check, if it is not given checks current user
	 *
	 * @return boolean
	 */
	public function isAdmin(int $userId = null)
	{
		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		return $this->isMember($this->config->groupAdmin, $userId);
	}

	/**
	 * Is user allowed
	 *
	 * Check if user allowed to do specified action, admin always allowed
	 * first checks user permissions then check group permissions
	 *
	 * @param integer|string $permPar Permission id or name to check
	 * @param integer|null   $userId  User id to check, or if false checks current user
	 *
	 * @return boolean
	 */
	public function isAllowed($permPar, int $userId = null)
	{
		if ($this->config->totpEnabled && ! $this->config->totpLogin)
		{
			if ($this->isTotpRequired())
			{
				return \Config\Services::response()->redirect($this->config->totpLink);
			}
		}

		$userModel = $this->getModel('User');

		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $userModel->existsById($userId))
		{
			return false;
		}
		else if ($this->isAdmin($userId))
		{
			return true;
		}
		else
		{
			if (! $permId = $this->getPermId($permPar))
			{
				return false;
			}

			$permToUserModel = $this->getModel('PermToUser');

			if ($permToUserModel->denied($permId, $userId))
			{
				return false;
			}
			else if ($permToUserModel->allowed($permId, $userId))
			{
				return true;
			}
			else
			{
				$groupAllowed = false;
				foreach ($this->getUserGroups($userId) as $group)
				{
					if ($this->isGroupAllowed($permId, $group['group_id']))
					{
						$groupAllowed = true;
						break;
					}
				}

				return $groupAllowed;
			}
		}
	}

	/**
	 * Is Group allowed
	 *
	 * Check if group is allowed to do specified action, admin always allowed
	 *
	 * @param integer|string $permPar  Permission id or name to check
	 * @param integer|string $groupPar Group id or name to check, or if false checks all user groups
	 *
	 * @return boolean
	 */
	public function isGroupAllowed($permPar, $groupPar = null)
	{
		if (! $permId = $this->getPermId($permPar))
		{
			return false;
		}

		if ($groupPar)
		{
			if (strcasecmp($groupPar, $this->config->groupAdmin) === 0)
			{
				return true;
			}

			$permToGroupModel = $this->getModel('PermToGroup');
			$groupId          = $this->getGroupId($groupPar);
			$groupAllowed     = false;

			if ($subgroups = $this->getSubgroups($groupId))
			{
				foreach ($subgroups as $group)
				{
					if (! $groupAllowed)
					{
						if ($this->isGroupAllowed($permId, $group['subgroup_id']))
						{
							$groupAllowed = true;
						}
					}
				}
			}

			if ($permToGroupModel->denied($permId, $groupId))
			{
				return false;
			}
			else if ($permToGroupModel->allowed($permId, $groupId))
			{
				return true;
			}
			else if ($groupAllowed || $permToGroupModel->allowed($permId, $groupId))
			{
				return true;
			}
			else if (! $groupAllowed)
			{
				return false;
			}
		}
		else
		{
			if ($this->isAdmin() || $this->isGroupAllowed($permId, $this->config->groupPublic))
			{
				return true;
			}
			else if (! $this->isLoggedIn())
			{
				return false;
			}

			foreach ($this->getUserGroups() as $group)
			{
				if ($this->isGroupAllowed($permId, $group['group_id']))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Control
	 *
	 * Controls if a logged or public user has permission
	 *
	 * If user does not have permission to access page, it stops script and gives
	 * error message, unless 'no_permission' value is set in config.  If 'no_permission' is
	 * set in config it redirects user to the set url and passes the 'no_access' error message.
	 * It also updates last activity every time function called.
	 *
	 * @param string $permPar If not given just control user logged in or not
	 *
	 * @return boolean|redirect|error
	 */
	public function control(string $permPar = null)
	{
		if ($this->config->totpEnabled && $this->isTotpRequired())
		{
			$this->error(lang('Aauth.requiredTOTPCode'));
			return \Config\Services::response()->redirect($this->config->totpLink);
		}

		$this->getModel('User')->updateLastActivity($this->getUserId());

		if (! $permId = $this->getPermId($permPar))
		{
			if (! $this->isLoggedIn())
			{
				if ($this->config->linkNoPermission && $this->config->linkNoPermission !== 'error')
				{
					return \Config\Services::response()->redirect($this->config->linkNoPermission);
				}
				else if ($this->config->linkNoPermission === 'error')
				{
					return trigger_error(lang('Aauth.noAccess'), E_USER_ERROR);
				}

				return false;
			}
		}
		else if (! $this->isAllowed($permId))
		{
			if ($this->config->linkNoPermission && $this->config->linkNoPermission !== 'error')
			{
				return \Config\Services::response()->redirect($this->config->linkNoPermission);
			}
			else if ($this->config->linkNoPermission === 'error')
			{
				return trigger_error(lang('Aauth.noAccess'), E_USER_ERROR);
			}

			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------
	// User Functions
	//--------------------------------------------------------------------

	/**
	 * Create user
	 *
	 * Creates a new user
	 *
	 * @param string         $email    User's email address
	 * @param string         $password User's password
	 * @param string|boolean $username User's username
	 *
	 * @return integer|boolean
	 */
	public function createUser(string $email, string $password, string $username = null)
	{
		$userModel = $this->getModel('User');

		$data['email']    = $email;
		$data['password'] = $password;

		if (! is_null($username))
		{
			$data['username'] = $username;
		}

		if (! $userId = $userModel->insert($data))
		{
			$this->error(array_values($userModel->errors()));

			return false;
		}

		if ($this->config->groupDefault)
		{
			$this->addMember($this->config->groupDefault, $userId);
		}

		if ($this->config->userVerification)
		{
			$this->sendVerification($userId, $email);
			$this->info(lang('Aauth.infoCreateVerification'));

			return $userId;
		}

		$this->info(lang('Aauth.infoCreateSuccess'));

		return $userId;
	}

	/**
	 * Update user
	 *
	 * Updates existing user details
	 *
	 * @param integer        $userId   User id to update
	 * @param string|boolean $email    User's email address, or false if not to be updated
	 * @param string|boolean $password User's password, or false if not to be updated
	 * @param string|boolean $username User's name, or false if not to be updated
	 *
	 * @return boolean
	 */
	public function updateUser(int $userId, string $email = null, string $password = null, string $username = null)
	{
		$userModel = $this->getModel('User');

		if (! $userModel->existsById($userId))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}
		else if (is_null($email) && is_null($password) && is_null($username))
		{
			return true;
		}

		$data['id'] = $userId;

		if (! is_null($email))
		{
			$data['email'] = $email;
		}

		if (! is_null($password))
		{
			$data['password'] = $password;
		}

		if (! is_null($username))
		{
			$data['username'] = $username;
		}

		if (! $userModel->update($userId, $data))
		{
			$this->error(array_values($userModel->errors()));

			return false;
		}

		$this->info(lang('Aauth.infoUpdateSuccess'));

		return true;
	}

	/**
	 * Delete user
	 *
	 * @param integer $userId User id to delete
	 *
	 * @return boolen
	 */
	public function deleteUser(int $userId)
	{
		$userModel        = $this->getModel('User');
		$groupToUserModel = $this->getModel('GroupToUser');
		$permToUserModel  = $this->getModel('PermToUser');

		if (! $userModel->existsById($userId))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}

		$userModel->transStart();
		$groupToUserModel->deleteAllByUserId($userId);
		$permToUserModel->deleteAllByUserId($userId);
		$userModel->delete($userId);
		$userModel->transComplete();

		if ($userModel->transStatus() === false)
		{
			$userModel->transRollback();

			return false;
		}
		else
		{
			$userModel->transCommit();

			return true;
		}
	}

	/**
	 * List users
	 *
	 * Return users as an object array
	 *
	 * @param string|integer $groupPar       Specify group id to list group or null for all users
	 * @param integer        $limit          Limit of users to be returned
	 * @param integer        $offset         Offset for limited number of users
	 * @param boolean        $includeBanneds Include banned users
	 * @param string         $orderBy        Order by MYSQL string (e.g. 'name ASC', 'email DESC')
	 *
	 * @return array Array of users
	 */
	public function listUsers($groupPar = null, int $limit = 0, int $offset = 0, bool $includeBanneds = null, string $orderBy = null)
	{
		$userModel = $this->getModel('User');
		$userModel->limit($limit, $offset);

		$userModel->select('id, email, username, banned, created_at, updated_at, last_activity, last_ip_address, last_login');

		if ($groupPar && $groupId = $this->getGroupId($groupPar))
		{
			$userModel->join($this->config->dbTableGroupToUser, $this->config->dbTableGroupToUser . '.user_id = ' . $this->config->dbTableUsers . '.id');
			$userModel->where($this->config->dbTableGroupToUser . '.group_id', $groupId);
		}

		if (is_null($includeBanneds))
		{
			$userModel->where('banned', 0);
		}

		if (! is_null($orderBy))
		{
			$userModel->orderBy($orderBy);
		}

		return $userModel->findAll();
	}

	/**
	 * List users with paginate
	 *
	 * Return users as an object array
	 *
	 * @param string|integer $groupPar       Specify group id to list group or null for all users
	 * @param integer        $limit          Limit of users to be returned
	 * @param boolean        $includeBanneds Include banned users
	 * @param string         $orderBy        Order by MYSQL string (e.g. 'name ASC', 'email DESC')
	 *
	 * @return array Array of users
	 */
	public function listUsersPaginated($groupPar = null, int $limit = 10, bool $includeBanneds = null, string $orderBy = null)
	{
		$userModel = $this->getModel('User');

		$userModel->select('id, email, username, banned, created_at, updated_at, last_activity, last_ip_address, last_login');

		if ($groupPar && $groupId = $this->getGroupId($groupPar))
		{
			$userModel->join($this->config->dbTableGroupToUser, $this->config->dbTableGroupToUser . '.user_id = ' . $this->config->dbTableUsers . '.id');
			$userModel->where($this->config->dbTableGroupToUser . '.group_id', $groupId);
		}

		if (is_null($includeBanneds))
		{
			$userModel->where('banned', 0);
		}

		if (! is_null($orderBy))
		{
			$userModel->orderBy($orderBy);
		}

		return [
			'users' => $userModel->paginate($limit),
			'pager' => $userModel->pager,
		];
	}

	/**
	 * Send verification email
	 *
	 * Sends a verification email based on user id
	 *
	 * @param integer $userId User id to send verification email to
	 * @param string  $email  Email to send verification email to
	 *
	 * @return boolean
	 */
	protected function sendVerification(int $userId, string $email)
	{
		helper('text');
		$userVariableModel = $this->getModel('UserVariable');
		// $emailService      = \Config\Services::email();
		$emailService     = new PHPMailer;
		$verificationCode = sha1(strtotime('now'));

		$userVariableModel->save($userId, 'verification_code', $verificationCode, true);

		$messageData['code'] = $verificationCode;
		$messageData['link'] = site_url($this->config->linkVerification . '/' . $userId . '/' . $verificationCode);

		// phpcs:disable CodeIgniter4.NamingConventions.ValidVariableName
		if (isset($this->config->emailConfig->protocol))
		{
			if ($this->config->emailConfig->protocol === 'smtp')
			{
				$emailService->isSMTP();
				$emailService->Host       = $this->config->emailConfig->SMTPHost ? : '';
				$emailService->SMTPAuth   = true;
				$emailService->Username   = $this->config->emailConfig->SMTPUser ? : '';
				$emailService->Password   = $this->config->emailConfig->SMTPPass ? : '';
				$emailService->SMTPSecure = $this->config->emailConfig->SMTPCrypto ? : 'tls';
				$emailService->Port       = $this->config->emailConfig->SMTPPort ? : 587;
			}
			else if ($this->config->emailConfig->protocol === 'sendmail')
			{
				$emailService->isSendmail();
			}
			else if ($this->config->emailConfig->protocol === 'mail')
			{
			}
		}

		$emailService->setFrom($this->config->emailFrom, $this->config->emailFromName);
		$emailService->addAddress($email);
		$emailService->isHTML(true);
		$emailService->Subject = lang('Aauth.subjectVerification');
		$emailService->Body    = view('Aauth/Verification', $messageData);

		if (! $emailService->send())
		{
			$this->error(explode('<br />', $emailService->ErrorInfo));

			return false;
		}
		// phpcs:enable

		return true;

		// $emailService->initialize(isset($this->config->emailConfig) ? $this->config->emailConfig : []);
		// $emailService->setFrom($this->config->emailFrom, $this->config->emailFromName);
		// $emailService->setTo($email);
		// $emailService->setSubject(lang('Aauth.subjectVerification'));
		// $emailService->setMessage(view('Aauth/Verification', $messageData));

		// return $emailService->send();
	}

	/**
	 * Verify user
	 *
	 * Activates user account based on verification code
	 *
	 * @param string $verificationCode Code to validate against
	 *
	 * @return boolean Activation fails/succeeds
	 */
	public function verifyUser(string $verificationCode)
	{
		$userVariableModel = $this->getModel('UserVariable');
		$userVariable      = [
			'data_key'   => 'verification_code',
			'data_value' => $verificationCode,
			'system'     => 1,
		];

		if (! $verificationCodeStored = $userVariableModel->where($userVariable)->asArray()->first())
		{
			$this->error(lang('Aauth.invalidVerficationCode'));

			return false;
		}

		$userVariableModel->delete($verificationCodeStored['user_id'], 'verification_code', true);
		$this->info(lang('Aauth.infoVerification'));

		return true;
	}

	/**
	 * Get user
	 *
	 * Get user information
	 *
	 * @param integer|boolean $userId           User id to get or false for current user
	 * @param boolean         $includeVariables Whether to get user variables
	 * @param boolean         $systemVariables  Whether to get system user variables
	 *
	 * @return object|boolean User information or false if user not found
	 */
	public function getUser(int $userId = null, bool $includeVariables = false, bool $systemVariables = false)
	{
		$userModel         = $this->getModel('User');
		$userVariableModel = $this->getModel('UserVariable');

		$userModel->select('id, email, username, banned, created_at, updated_at, last_activity, last_ip_address, last_login');

		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $user = $userModel->find($userId))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}

		if ($includeVariables)
		{
			$userVariableModel->select('data_key, data_value');
			$variables = $userVariableModel->findAll($userId, $systemVariables);

			$user['variables'] = $variables;
		}

		return $user;
	}

	/**
	 * Get user id
	 *
	 * Get user id from email address, if par. not given, return current user's id
	 *
	 * @param string|boolean $email Email address for user,
	 *
	 * @return object|boolean User information or false if user not found
	 */
	public function getUserId(string $email = null)
	{
		$userModel = $this->getModel('User');

		if (! $email)
		{
			$where = ['id' => $this->session->user['id']];
		}
		else
		{
			$where = ['email' => $email];
		}

		if (! $user = $userModel->where($where)->asArray()->first())
		{
			return false;
		}

		return $user['id'];
	}

	/**
	 * Get active users count
	 *
	 * @return integer Count of active users
	 */
	public function getActiveUsersCount()
	{
		$userSessionModel = $this->getModel('UserSession');

		return count($userSessionModel->findAll());
	}

	/**
	 * List active users
	 *
	 * Return users as an object array
	 *
	 * @return array Array of active users
	 */
	public function listActiveUsers()
	{
		$userSessionModel = $this->getModel('UserSession');

		$usersIds = [];

		foreach ($userSessionModel->findAll() as $userSession)
		{
			$result = $matches = [];
			$data   = ';' . $userSession['data'];
			$keyreg = '/;([^|{}"]+)\|/';

			preg_match_all($keyreg, $data, $matches);

			if (isset($matches[1]))
			{
				$keys   = $matches[1];
				$values = preg_split($keyreg, $data);

				if (count($values) > 1)
				{
					array_shift($values);
				}

				$result = array_combine($keys, $values);
			}

			$user       = unserialize($result['user']);
			$usersIds[] = $user['id'];
		}

		if (count($usersIds) === 0)
		{
			return [];
		}

		$userModel = $this->getModel('User');

		$userModel->select('id, email, username, banned, created_at, updated_at, last_activity, last_ip_address, last_login');

		$userModel->whereIn('id', $usersIds);

		return $userModel->findAll();
	}

	/**
	 * Is banned
	 *
	 * @param integer $userId User id, can be null to use session user
	 *
	 * @return boolean
	 */
	public function isBanned(int $userId = null)
	{
		$userModel = $this->getModel('User');

		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $userModel->existsById($userId))
		{
			return true;
		}

		return $userModel->isBanned($userId);
	}

	/**
	 * Ban User
	 *
	 * @param integer $userId User id, can be null to use session user
	 *
	 * @return boolean
	 */
	public function banUser(int $userId = null)
	{
		$userModel = $this->getModel('User');

		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $userModel->existsById($userId))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}

		return $userModel->updateBanned($userId, 1);
	}

	/**
	 * Unban User
	 *
	 * @param integer $userId User id, can be null to use session user
	 *
	 * @return boolean
	 */
	public function unbanUser(int $userId = null)
	{
		$userModel = $this->getModel('User');

		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $userModel->existsById($userId))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}

		return $userModel->updateBanned($userId, 0);
	}

	/**
	 * Remind password
	 *
	 * Emails user with link to reset password
	 *
	 * @param string $email Email for account to remind
	 *
	 * @return boolean
	 */
	public function remindPassword(string $email)
	{
		$userModel = $this->getModel('User');

		if (! $user = $userModel->where('email', $email)->getFirstRow('array'))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}

		$userVariableModel = $this->getModel('UserVariable');
		// $emailService      = \Config\Services::email();
		$emailService = new PHPMailer;
		$resetCode    = sha1(strtotime('now'));
		$userVariableModel->save($user['id'], 'verification_code', $resetCode, true);

		$messageData['code'] = $resetCode;
		$messageData['link'] = site_url($this->config->linkResetPassword . '/' . $resetCode);

		// phpcs:disable CodeIgniter4.NamingConventions.ValidVariableName
		if (isset($this->config->emailConfig->protocol))
		{
			if ($this->config->emailConfig->protocol === 'smtp')
			{
				$emailService->isSMTP();
				$emailService->Host       = $this->config->emailConfig->SMTPHost ? : '';
				$emailService->SMTPAuth   = true;
				$emailService->Username   = $this->config->emailConfig->SMTPUser ? : '';
				$emailService->Password   = $this->config->emailConfig->SMTPPass ? : '';
				$emailService->SMTPSecure = $this->config->emailConfig->SMTPCrypto ? : 'tls';
				$emailService->Port       = $this->config->emailConfig->SMTPPort ? : 587;
			}
			else if ($this->config->emailConfig->protocol === 'sendmail')
			{
				$emailService->isSendmail();
			}
			else if ($this->config->emailConfig->protocol === 'mail')
			{
			}
		}

		$emailService->setFrom($this->config->emailFrom, $this->config->emailFromName);
		$emailService->addAddress($user['email']);
		$emailService->isHTML(true);
		$emailService->Subject = lang('Aauth.subjectReset');
		$emailService->Body    = view('Aauth/RemindPassword', $messageData);

		if (! $email = $emailService->send())
		{
			$this->error(explode('<br />', $emailService->ErrorInfo));

			return false;
		}
		// phpcs:enable

		// $emailService->initialize(isset($this->config->emailConfig) ? $this->config->emailConfig : []);
		// $emailService->setFrom($this->config->emailFrom, $this->config->emailFromName);
		// $emailService->setTo($user['email']);
		// $emailService->setSubject(lang('Aauth.subjectReset'));
		// $emailService->setMessage(view('Aauth/RemindPassword', $messageData));

		// if (! $email = $emailService->send())
		// {
		// 	$this->error(explode('<br />', $emailService->printDebugger([])));

		// 	return false;
		// }

		$this->info(lang('Aauth.infoRemindSuccess'));

		return $email;
	}

	/**
	 * Reset password
	 *
	 * Generate new password and email it to the user
	 *
	 * @param string $resetCode Verification code for account
	 *
	 * @return boolean
	 */
	public function resetPassword(string $resetCode)
	{
		$userVariableModel = $this->getModel('UserVariable');
		$variable          = [
			'data_key'   => 'verification_code',
			'data_value' => $resetCode,
			'system'     => 1,
		];

		if (! $userVariable = $userVariableModel->where($variable)->getFirstRow('array'))
		{
			$this->error(lang('Aauth.invalidVerficationCode'));

			return false;
		}

		helper('text');
		$userModel = $this->getModel('User');
		$password  = random_string('alnum', $this->config->passwordMin);

		if (! $user = $userModel->find($userVariable['user_id']))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}

		// $emailService = \Config\Services::email();
		$emailService = new PHPMailer;

		$data['id']       = $user['id'];
		$data['password'] = $password;

		$userModel->update($user['id'], $data);
		$userVariableModel->delete($user['id'], 'verification_code', true);

		if ($this->config->totpEnabled && $this->config->totpResetPassword)
		{
			$userVariableModel->delete($user['id'], 'totp_secret', true);
		}

		$messageData['password'] = $password;

		// phpcs:disable CodeIgniter4.NamingConventions.ValidVariableName
		if (isset($this->config->emailConfig->protocol))
		{
			if ($this->config->emailConfig->protocol === 'smtp')
			{
				$emailService->isSMTP();
				$emailService->Host       = $this->config->emailConfig->SMTPHost ? : '';
				$emailService->SMTPAuth   = true;
				$emailService->Username   = $this->config->emailConfig->SMTPUser ? : '';
				$emailService->Password   = $this->config->emailConfig->SMTPPass ? : '';
				$emailService->SMTPSecure = $this->config->emailConfig->SMTPCrypto ? : 'tls';
				$emailService->Port       = $this->config->emailConfig->SMTPPort ? : 587;
			}
			else if ($this->config->emailConfig->protocol === 'sendmail')
			{
				$emailService->isSendmail();
			}
			else if ($this->config->emailConfig->protocol === 'mail')
			{
			}
		}

		$emailService->setFrom($this->config->emailFrom, $this->config->emailFromName);
		$emailService->addAddress($user['email']);
		$emailService->isHTML(true);
		$emailService->Subject = lang('Aauth.subjectResetSuccess');
		$emailService->Body    = view('Aauth/ResetPassword', $messageData);

		if (! $email = $emailService->send())
		{
			$this->error(explode('<br />', $emailService->ErrorInfo));

			return false;
		}
		// phpcs:enable

		// $emailService->initialize(isset($this->config->emailConfig) ? $this->config->emailConfig : []);
		// $emailService->setFrom($this->config->emailFrom, $this->config->emailFromName);
		// $emailService->setTo($user['email']);
		// $emailService->setSubject(lang('Aauth.subjectResetSuccess'));
		// $emailService->setMessage(view('Aauth/ResetPassword', $messageData));

		// if (! $email = $emailService->send())
		// {
		// 	$this->error(explode('<br />', $emailService->printDebugger([])));

		// 	return false;
		// }

		$this->info(lang('Aauth.infoResetSuccess'));

		return $email;
	}

	/**
	 * Set User Variable as key value
	 *
	 * If variable not set before, it will be set
	 * if set, overwrites the value
	 *
	 * @param string  $key    User Variable Key
	 * @param string  $value  User Variable Value
	 * @param integer $userId User id, can be null to use session user
	 *
	 * @return boolean
	 */
	public function setUserVar(string $key, string $value, int $userId = null)
	{
		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		$userModel = $this->getModel('User');

		if (! $userModel->existsById($userId))
		{
			return false;
		}

		$userVariableModel = $this->getModel('UserVariable');

		return $userVariableModel->save($userId, $key, $value);
	}

	/**
	 * Unset User Variable as key value
	 *
	 * @param string  $key    User Variable Key
	 * @param integer $userId User id, can be null to use session user
	 *
	 * @return boolean
	 */
	public function unsetUserVar(string $key, int $userId = null)
	{
		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		$userModel = $this->getModel('User');

		if (! $userModel->existsById($userId))
		{
			return false;
		}

		$userVariableModel = $this->getModel('UserVariable');

		return $userVariableModel->delete($userId, $key);
	}

	/**
	 * Get User Variable by key
	 *
	 * @param string  $key    User Variable Key
	 * @param integer $userId User id, can be null to use session user
	 *
	 * @return boolean|string false if var is not set, the value of var if set
	 */
	public function getUserVar(string $key, int $userId = null)
	{
		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		$userModel = $this->getModel('User');

		if (! $userModel->existsById($userId))
		{
			return false;
		}

		$userVariableModel = $this->getModel('UserVariable');

		if (! $variable = $userVariableModel->find($userId, $key))
		{
			return false;
		}

		return $variable;
	}

	/**
	 * List User Variables by user id
	 *
	 * Return array with all user keys & variables
	 *
	 * @param integer $userId User id, can be null to use session user
	 *
	 * @return boolean|array , false if var is not set, the value of var if set
	 */
	public function listUserVars(int $userId = null)
	{
		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		$userModel = $this->getModel('User');

		if (! $userModel->existsById($userId))
		{
			return false;
		}

		$userVariableModel = $this->getModel('UserVariable');

		return $userVariableModel->findAll($userId);
	}

	/**
	 * Get User Variable Keys by UserId
	 *
	 * Return array of variable keys or false
	 *
	 * @param integer $userId User id, can be null to use session user
	 *
	 * @return boolean|array
	 */
	public function getUserVarKeys(int $userId = null)
	{
		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		$userModel = $this->getModel('User');

		if (! $userModel->existsById($userId))
		{
			return false;
		}

		$userVariableModel = $this->getModel('UserVariable');
		$userVariableModel->select('data_key as key');

		return $userVariableModel->findAll($userId);
	}

	//--------------------------------------------------------------------
	// Group Functions
	//--------------------------------------------------------------------

	/**
	 * Create group
	 *
	 * @param string $name       New group name
	 * @param string $definition Description of the group
	 *
	 * @return integer|boolean Group id or false on fail
	 */
	public function createGroup(string $name, string $definition = '')
	{
		$groupModel = $this->getModel('Group');

		$data['name']       = $name;
		$data['definition'] = $definition;

		if (! $groupId = $groupModel->insert($data))
		{
			$this->error(array_values($groupModel->errors()));

			return false;
		}

		$this->precacheGroups();

		return $groupId;
	}

	/**
	 * Update group
	 *
	 * @param string|integer $groupPar   Group id or name
	 * @param string         $name       New group name
	 * @param string         $definition New group definition
	 *
	 * @return boolean
	 */
	public function updateGroup($groupPar, string $name = null, string $definition = null)
	{
		$groupModel = $this->getModel('Group');

		if (is_null($name) && is_null($definition))
		{
			return true;
		}
		else if (! $groupId = $this->getGroupId($groupPar))
		{
			$this->error(lang('Aauth.notFoundGroup'));

			return false;
		}

		$data['id'] = $groupId;

		if (! is_null($name))
		{
			$data['name'] = $name;
		}

		if (! is_null($definition))
		{
			$data['definition'] = $definition;
		}

		if (! $groupModel->update($groupId, $data))
		{
			$this->error(array_values($groupModel->errors()));

			return false;
		}

		$this->precacheGroups();

		return true;
	}

	/**
	 * Delete group
	 *
	 * @param string|integer $groupPar Group id or name
	 *
	 * @return boolean
	 */
	public function deleteGroup($groupPar)
	{
		$groupModel        = $this->getModel('Group');
		$groupToGroupModel = $this->getModel('GroupToGroup');
		$groupToUserModel  = $this->getModel('GroupToUser');
		$permToGroupModel  = $this->getModel('PermToGroup');

		if (! $groupId = $this->getGroupId($groupPar))
		{
			$this->error(lang('Aauth.notFoundGroup'));

			return false;
		}

		$groupModel->transStart();
		$groupToGroupModel->deleteAllByGroupId($groupId);
		$groupToGroupModel->deleteAllBySubgroupId($groupId);
		$groupToUserModel->deleteAllByGroupId($groupId);
		$permToGroupModel->deleteAllByGroupId($groupId);
		$groupModel->delete($groupId);
		$groupModel->transComplete();

		if ($groupModel->transStatus() === false)
		{
			$groupModel->transRollback();

			return false;
		}
		else
		{
			$groupModel->transCommit();
			$this->precacheGroups();

			return true;
		}
	}

	/**
	 * Add member to group
	 *
	 * @param integer|string $groupPar Group id or name to add user to
	 * @param integer        $userId   User id to add to group
	 *
	 * @return boolean
	 */
	public function addMember($groupPar, int $userId)
	{
		$userModel        = $this->getModel('User');
		$groupToUserModel = $this->getModel('GroupToUser');

		if (! $groupId = $this->getGroupId($groupPar))
		{
			$this->error(lang('Aauth.notFoundGroup'));

			return false;
		}
		else if (! $userModel->existsById($userId))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}
		else if ($groupToUserModel->exists($groupId, $userId))
		{
			$this->info(lang('Aauth.alreadyMemberGroup'));

			return true;
		}

		return $groupToUserModel->insert($groupId, $userId);
	}

	/**
	 * Remove member from group
	 *
	 * @param integer|string $groupPar Group id or name to remove user from
	 * @param integer        $userId   User id to remove from group
	 *
	 * @return boolean
	 */
	public function removeMember($groupPar, int $userId)
	{
		$groupToUserModel = $this->getModel('GroupToUser');

		$groupId = $this->getGroupId($groupPar);

		return $groupToUserModel->delete($groupId, $userId);
	}

	/**
	 * Get User Groups
	 *
	 * @param integer $userId User id
	 *
	 * @return boolean|array
	 */
	public function getUserGroups(int $userId = null)
	{
		$userModel = $this->getModel('User');

		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $userModel->existsById($userId))
		{
			return false;
		}

		$groupToUserModel = $this->getModel('GroupToUser');

		return $groupToUserModel->findAllByUserId($userId);
	}

	/**
	 * Get User Perms
	 *
	 * @param integer|string $userId User id
	 * @param integer        $state  State
	 *
	 * @return boolean|array
	 */
	public function getUserPerms($userId, int $state = null)
	{
		$userModel = $this->getModel('User');

		if (! $userModel->existsById($userId))
		{
			return false;
		}

		$permToUserModel = $this->getModel('PermToUser');

		return $permToUserModel->findAllByUserId($userId, $state);
	}

	/**
	 * Add subgroup to group
	 *
	 * @param integer|string $groupPar    Group id
	 * @param integer|string $subgroupPar Subgroup id or name to add to group
	 *
	 * @return boolean
	 */
	public function addSubgroup($groupPar, $subgroupPar)
	{
		$groupModel        = $this->getModel('Group');
		$groupToGroupModel = $this->getModel('GroupToGroup');

		if (! $groupId = $this->getGroupId($groupPar))
		{
			$this->error(lang('Aauth.notFoundGroup'));

			return false;
		}
		else if (! $subgroupId = $this->getGroupId($subgroupPar))
		{
			$this->error(lang('Aauth.notFoundSubgroup'));

			return false;
		}
		else if ($groupId === $subgroupId)
		{
			return false;
		}

		if ($groupGroups = $groupToGroupModel->findAllByGroupId($groupId))
		{
			foreach ($groupGroups as $item)
			{
				if ($item['subgroup_id'] === $subgroupId)
				{
					return false;
				}
			}
		}

		if ($subgroupGroups = $groupToGroupModel->findAllByGroupId($subgroupId))
		{
			foreach ($subgroupGroups as $item)
			{
				if ($item['subgroup_id'] === $groupId)
				{
					return false;
				}
			}
		}

		return $groupToGroupModel->insert($groupId, $subgroupId);
	}

	/**
	 * Remove subgroup from group
	 *
	 * @param integer|string $groupPar    Group id or name to remove
	 * @param integer|string $subgroupPar Sub-Group id or name to remove
	 *
	 * @return boolean
	 */
	public function removeSubgroup($groupPar, $subgroupPar)
	{
		$groupToGroupModel = $this->getModel('GroupToGroup');
		$groupId           = $this->getGroupId($groupPar);
		$subgroupId        = $this->getGroupId($subgroupPar);

		return $groupToGroupModel->delete($groupId, $subgroupId);
	}

	/**
	 * Get subgroups
	 *
	 * @param integer|string $groupPar Group id or name to get
	 *
	 * @return boolean|array
	 */
	public function getSubgroups($groupPar)
	{
		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$groupToGroupModel = $this->getModel('GroupToGroup');

		return $groupToGroupModel->findAllByGroupId($groupId);
	}

	/**
	 * List all group subgroups
	 *
	 * @param integer|string $groupPar Group id or name to remove
	 *
	 * @return array
	 */
	public function listGroupSubgroups($groupPar)
	{
		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$groupModel = $this->getModel('Group');

		$groupModel->select('id, name, definition, IF(group_id = ' . $groupId . ', 1, 0)  as subgroup');
		$groupModel->join($this->config->dbTableGroupToGroup, '(' . $this->config->dbTableGroups . '.id = ' . $this->config->dbTableGroupToGroup . '.subgroup_id AND ' . $this->config->dbTableGroupToGroup . '.group_id = ' . $groupId . ')', 'left');

		return $groupModel->findAll();
	}

	/**
	 * List group subgroups with paginate
	 *
	 * Return groups as an object array
	 *
	 * @param integer|string $groupPar Group id or name to remove
	 * @param integer        $limit    Limit of users to be returned
	 * @param string         $orderBy  Order by MYSQL string (e.g. 'name ASC', 'email DESC')
	 *
	 * @return array
	 */
	public function listGroupSubgroupsPaginated($groupPar, int $limit = 10, string $orderBy = null)
	{
		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$groupModel = $this->getModel('Group');
		$groupModel->select('id, name, definition, IF(group_id = ' . $groupId . ', 1, 0)  as subgroup');
		$groupModel->join($this->config->dbTableGroupToGroup, '(' . $this->config->dbTableGroups . '.id = ' . $this->config->dbTableGroupToGroup . '.subgroup_id AND ' . $this->config->dbTableGroupToGroup . '.group_id = ' . $groupId . ')', 'left');

		if (! is_null($orderBy))
		{
			$groupModel->orderBy($orderBy);
		}

		return [
			'groups' => $groupModel->paginate($limit),
			'pager'  => $groupModel->pager,
		];
	}

	/**
	 * Get group perms
	 *
	 * @param integer|string $groupPar Group id or name to get
	 * @param integer        $state    State (1 = allowed, 0 = denied)
	 *
	 * @return boolean|array
	 */
	public function getGroupPerms($groupPar, int $state = null)
	{
		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$permToGroupModel = $this->getModel('PermToGroup');

		return $permToGroupModel->findAllByGroupId($groupId, $state);
	}

	/**
	 * Remove member from all groups
	 *
	 * @param integer $userId User id to remove from all groups
	 *
	 * @return boolean
	 */
	public function removeMemberFromAll(int $userId)
	{
		$groupToUserModel = $this->getModel('GroupToUser');

		return $groupToUserModel->deleteAllByUserId($userId);
	}

	/**
	 * List all groups
	 *
	 * @return array
	 */
	public function listGroups()
	{
		$groupModel = $this->getModel('Group');

		return $groupModel->findAll();
	}

	/**
	 * List groups with paginate
	 *
	 * Return groups as an object array
	 *
	 * @param integer $limit   Limit of users to be returned
	 * @param string  $orderBy Order by MYSQL string (e.g. 'name ASC', 'email DESC')
	 *
	 * @return array
	 */
	public function listGroupsPaginated(int $limit = 10, string $orderBy = null)
	{
		$groupModel = $this->getModel('Group');

		if (! is_null($orderBy))
		{
			$groupModel->orderBy($orderBy);
		}

		return [
			'groups' => $groupModel->paginate($limit),
			'pager'  => $groupModel->pager,
		];
	}

	/**
	 * Get group name
	 *
	 * @param integer $groupId Group id to get
	 *
	 * @return string
	 */
	public function getGroupName(int $groupId)
	{
		$groupModel = $this->getModel('Group');

		if (! $group = $groupModel->find($groupId))
		{
			return false;
		}

		return $group['name'];
	}

	/**
	 * Get group id
	 *
	 * @param integer|string $groupPar Group id or name to get
	 *
	 * @return integer
	 */
	public function getGroupId(string $groupPar)
	{
		if (is_numeric($groupPar))
		{
			if (array_search($groupPar, $this->cacheGroupIds))
			{
				return $groupPar;
			}
		}
		else
		{
			$groupPar = str_replace(' ', '', trim(strtolower($groupPar)));

			if (isset($this->cacheGroupIds[$groupPar]))
			{
				return $this->cacheGroupIds[$groupPar];
			}
		}

		return false;
	}

	/**
	 * Get group
	 *
	 * @param integer|string $groupPar Group id or name to get
	 *
	 * @return array
	 */
	public function getGroup($groupPar)
	{
		$groupModel = $this->getModel('Group');

		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		return $groupModel->asArray()->find($groupId);
	}

	/**
	 * List user groups
	 *
	 * @param integer|null $userId User id to get or false for current user
	 *
	 * @return boolean|array
	 */
	public function listUserGroups(int $userId = null)
	{
		$userModel = $this->getModel('User');

		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $userModel->existsById($userId))
		{
			return false;
		}

		$groupModel = $this->getModel('Group');

		$groupModel->select('id, name, definition, IF(user_id = ' . $userId . ', 1, 0) as member');
		$groupModel->join($this->config->dbTableGroupToUser, $this->config->dbTableGroups . '.id = ' . $this->config->dbTableGroupToUser . '.group_id AND ' . $this->config->dbTableGroupToUser . '.user_id = ' . $userId, 'left');

		return $groupModel->get()->getResult('array');
	}

	/**
	 * List user groups with paginate
	 *
	 * Return users as an object array
	 *
	 * @param integer|null $userId  User id to get or false for current user
	 * @param integer      $limit   Limit of users to be returned
	 * @param string       $orderBy Order by MYSQL string (e.g. 'name ASC', 'email DESC')
	 *
	 * @return boolean|array
	 */
	public function listUserGroupsPaginated(int $userId = null, int $limit = 10, string $orderBy = null)
	{
		$userModel = $this->getModel('User');

		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $userModel->existsById($userId))
		{
			return false;
		}

		$groupModel = $this->getModel('Group');

		$groupModel->select('id, name, definition, IF(user_id = ' . $userId . ', 1, 0) as member');
		$groupModel->join($this->config->dbTableGroupToUser, $this->config->dbTableGroups . '.id = ' . $this->config->dbTableGroupToUser . '.group_id AND ' . $this->config->dbTableGroupToUser . '.user_id = ' . $userId, 'left');

		if (! is_null($orderBy))
		{
			$groupModel->orderBy($orderBy);
		}

		return [
			'groups' => $groupModel->paginate($limit),
			'pager'  => $groupModel->pager,
		];
	}

	/**
	 * Set Group Variable as key value
	 *
	 * If variable not set before, it will be set
	 * if set, overwrites the value
	 *
	 * @param string         $key      Group Variable Key
	 * @param string         $value    Group Variable Value
	 * @param integer|string $groupPar Group id or name to remove
	 *
	 * @return boolean
	 */
	public function setGroupVar(string $key, string $value, $groupPar)
	{
		$groupModel = $this->getModel('Group');

		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$groupVariableModel = $this->getModel('GroupVariable');

		return $groupVariableModel->save($groupId, $key, $value);
	}

	/**
	 * Unset Group Variable as key value
	 *
	 * @param string         $key      Group Variable Key
	 * @param integer|string $groupPar Group id or name to remove
	 *
	 * @return boolean
	 */
	public function unsetGroupVar(string $key, $groupPar)
	{
		$groupModel = $this->getModel('Group');

		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$groupVariableModel = $this->getModel('GroupVariable');

		return $groupVariableModel->delete($groupId, $key);
	}

	/**
	 * Get Group Variable by key
	 *
	 * @param string $key      Variable Key
	 * @param string $groupPar Group name or id
	 *
	 * @return boolean|string
	 */
	public function getGroupVar(string $key, string $groupPar)
	{
		$groupModel = $this->getModel('Group');

		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$groupVariableModel = $this->getModel('GroupVariable');

		if (! $variable = $groupVariableModel->find($groupId, $key))
		{
			return false;
		}

		return $variable;
	}

	/**
	 * List Group Variables by group id
	 *
	 * Return array with all group keys & variables
	 *
	 * @param string $groupPar Group name or id
	 *
	 * @return array
	 */
	public function listGroupVars(string $groupPar = null)
	{
		$groupModel = $this->getModel('Group');

		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$groupVariableModel = $this->getModel('GroupVariable');

		return $groupVariableModel->findAll($groupId);
	}

	/**
	 * Get Group Variable Keys by GroupId
	 *
	 * Return array of variable keys or false
	 *
	 * @param string $groupPar Group name or id
	 *
	 * @return boolean|array
	 */
	public function getGroupVarKeys(string $groupPar = null)
	{
		$groupModel = $this->getModel('Group');

		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$groupVariableModel = $this->getModel('GroupVariable');
		$groupVariableModel->select('data_key as key');

		return $groupVariableModel->findAll($groupId);
	}

	//--------------------------------------------------------------------
	// Perm Functions
	//--------------------------------------------------------------------

	/**
	 * Create permission
	 *
	 * Creates a new permission type
	 *
	 * @param string $name       New permission name
	 * @param string $definition Permission description
	 *
	 * @return integer|boolean Permission id or false on fail
	 */
	public function createPerm(string $name, string $definition = '')
	{
		$permModel = $this->getModel('Perm');

		$data['name']       = $name;
		$data['definition'] = $definition;

		if (! $permId = $permModel->insert($data))
		{
			$this->error(array_values($permModel->errors()));

			return false;
		}

		$this->precachePerms();

		return $permId;
	}

	/**
	 * Update permission
	 *
	 * Updates permission name and description
	 *
	 * @param integer|string $permPar    Permission id or permission name
	 * @param string         $name       New permission name
	 * @param string         $definition Permission description
	 *
	 * @return boolean
	 */
	public function updatePerm($permPar, string $name = null, string $definition = null)
	{
		$permModel = $this->getModel('Perm');

		if (is_null($name) && is_null($definition))
		{
			return true;
		}
		else if (! $permId = $this->getPermId($permPar))
		{
			$this->error(lang('Aauth.notFoundPerm'));

			return false;
		}

		$data['id'] = $permId;

		if (! is_null($name))
		{
			$data['name'] = $name;
		}

		if (! is_null($definition))
		{
			$data['definition'] = $definition;
		}

		if (! $permModel->update($permId, $data))
		{
			$this->error(array_values($permModel->errors()));

			return false;
		}

		$this->precachePerms();

		return true;
	}

	/**
	 * Delete permission
	 *
	 * Delete a permission from database. WARNING Can't be undone
	 *
	 * @param integer|string $permPar Permission id or perm name
	 *
	 * @return boolean
	 */
	public function deletePerm($permPar)
	{
		$permModel        = $this->getModel('Perm');
		$permToGroupModel = $this->getModel('PermToGroup');
		$permToUserModel  = $this->getModel('PermToUser');

		if (! $permId = $this->getPermId($permPar))
		{
			$this->error(lang('Aauth.notFoundPerm'));

			return false;
		}

		$permModel->transStart();
		$permToGroupModel->deleteAllByPermId($permId);
		$permToUserModel->deleteAllByPermId($permId);
		$permModel->delete($permId);
		$permModel->transComplete();

		if ($permModel->transStatus() === false)
		{
			$permModel->transRollback();

			return false;
		}
		else
		{
			$permModel->transCommit();
			$this->precachePerms();

			return true;
		}
	}

	/**
	 * Allow User
	 *
	 * @param integer|string $permPar Permission id or perm name
	 * @param integer        $userId  User id to allow
	 *
	 * @return boolean
	 */
	public function allowUser($permPar, int $userId)
	{
		$userModel       = $this->getModel('User');
		$permToUserModel = $this->getModel('PermToUser');

		if (! $permId = $this->getPermId($permPar))
		{
			$this->error(lang('Aauth.notFoundPerm'));

			return false;
		}
		else if (! $userModel->existsById($userId))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}
		else if ($permToUserModel->allowed($permId, $userId))
		{
			return true;
		}

		return $permToUserModel->save($permId, $userId, 1);
	}

	/**
	 * Deny User
	 *
	 * @param integer|string $permPar Permission id or perm name
	 * @param integer        $userId  User id to deny
	 *
	 * @return boolean
	 */
	public function denyUser($permPar, int $userId)
	{
		$userModel       = $this->getModel('User');
		$permToUserModel = $this->getModel('PermToUser');

		if (! $permId = $this->getPermId($permPar))
		{
			$this->error(lang('Aauth.notFoundPerm'));

			return false;
		}
		else if (! $userModel->existsById($userId))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}
		else if ($permToUserModel->denied($permId, $userId))
		{
			return true;
		}

		return $permToUserModel->save($permId, $userId, 0);
	}

	/**
	 * Remove User Perm
	 *
	 * @param integer|string $permPar Permission id or perm name
	 * @param integer        $userId  User id to deny
	 *
	 * @return boolean
	 */
	public function removeUserPerm($permPar, int $userId)
	{
		$userModel       = $this->getModel('User');
		$permToUserModel = $this->getModel('PermToUser');

		if (! $permId = $this->getPermId($permPar))
		{
			$this->error(lang('Aauth.notFoundPerm'));

			return false;
		}
		else if (! $userModel->existsById($userId))
		{
			$this->error(lang('Aauth.notFoundUser'));

			return false;
		}

		return $permToUserModel->delete($permId, $userId);
	}

	/**
	 * Allow Group
	 *
	 * Add group to permission
	 *
	 * @param integer|string $permPar  Permission id or perm name
	 * @param integer|string $groupPar Group id or name to allow
	 *
	 * @return boolean
	 */
	public function allowGroup($permPar, $groupPar)
	{
		$permToGroupModel = $this->getModel('PermToGroup');

		if (! $permId = $this->getPermId($permPar))
		{
			$this->error(lang('Aauth.notFoundPerm'));

			return false;
		}
		if (! $groupId = $this->getGroupId($groupPar))
		{
			$this->error(lang('Aauth.notFoundGroup'));

			return false;
		}
		else if ($permToGroupModel->allowed($permId, $groupId))
		{
			return true;
		}

		return $permToGroupModel->save($permId, $groupId, 1);
	}

	/**
	 * Deny Group
	 *
	 * Remove group from permission
	 *
	 * @param integer|string $permPar  Permission id or perm name
	 * @param integer|string $groupPar Group id or name to deny
	 *
	 * @return boolean
	 */
	public function denyGroup($permPar, $groupPar)
	{
		$permToGroupModel = $this->getModel('PermToGroup');

		if (! $permId = $this->getPermId($permPar))
		{
			$this->error(lang('Aauth.notFoundPerm'));

			return false;
		}
		if (! $groupId = $this->getGroupId($groupPar))
		{
			$this->error(lang('Aauth.notFoundGroup'));

			return false;
		}
		else if ($permToGroupModel->denied($permId, $groupId))
		{
			return true;
		}

		return $permToGroupModel->save($permId, $groupId, 0);
	}

	/**
	 * Remove Group Perm
	 *
	 * Remove group from permission
	 *
	 * @param integer|string $permPar  Permission id or perm name
	 * @param integer|string $groupPar Group id or name to deny
	 *
	 * @return boolean
	 */
	public function removeGroupPerm($permPar, $groupPar)
	{
		$permToGroupModel = $this->getModel('PermToGroup');

		if (! $permId = $this->getPermId($permPar))
		{
			$this->error(lang('Aauth.notFoundPerm'));

			return false;
		}
		if (! $groupId = $this->getGroupId($groupPar))
		{
			$this->error(lang('Aauth.notFoundGroup'));

			return false;
		}

		return $permToGroupModel->delete($permId, $groupId);
	}

	/**
	 * List Permissions
	 *
	 * List all permissions
	 *
	 * @return array
	 */
	public function listPerms()
	{
		$permModel = $this->getModel('Perm');

		return $permModel->findAll();
	}

	/**
	 * List perms with paginate
	 *
	 * Return perms as an object array
	 *
	 * @param integer $limit   Limit of users to be returned
	 * @param string  $orderBy Order by MYSQL string (e.g. 'name ASC', 'email DESC')
	 *
	 * @return array Array of perms
	 */
	public function listPermsPaginated(int $limit = 10, string $orderBy = null)
	{
		$permModel = $this->getModel('Perm');

		if (! is_null($orderBy))
		{
			$permModel->orderBy($orderBy);
		}

		return [
			'perms' => $permModel->paginate($limit),
			'pager' => $permModel->pager,
		];
	}

	/**
	 * Get permission id
	 *
	 * @param integer|string $permPar Permission id or name to get
	 *
	 * @return boolean|integer Permission id or FALSE if perm does not exist
	 */
	public function getPermId($permPar)
	{
		if (is_numeric($permPar))
		{
			if (array_search($permPar, $this->cachePermIds))
			{
				return $permPar;
			}
		}
		else
		{
			$permPar = str_replace(' ', '', trim(strtolower($permPar)));

			if (isset($this->cachePermIds[$permPar]))
			{
				return $this->cachePermIds[$permPar];
			}
		}

		return false;
	}

	/**
	 * Get permission
	 *
	 * Get permission from permisison name or id
	 *
	 * @param integer|string $permPar Permission id or name to get
	 *
	 * @return integer Permission id or NULL if perm does not exist
	 */
	public function getPerm($permPar)
	{
		$permModel = $this->getModel('Perm');

		if (! $permId = $this->getPermId($permPar))
		{
			return false;
		}

		return $permModel->asArray()->find($permId);
	}

	/**
	 * List group permissions
	 *
	 * @param integer|string $groupPar Group id or name to get
	 *
	 * @return boolean|array
	 */
	public function listGroupPerms($groupPar)
	{
		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$permModel = $this->getModel('Perm');

		$permModel->select('id, name, definition, IF(state = 0 OR state = 1, state, -1) as state');
		$permModel->join($this->config->dbTablePermToGroup, '(' . $this->config->dbTablePerms . '.id = ' . $this->config->dbTablePermToGroup . '.perm_id AND ' . $this->config->dbTablePermToGroup . '.group_id = ' . $groupId . ')', 'left');

		return $permModel->get()->getResult('array');
	}

	/**
	 * List users with paginate
	 *
	 * Return users as an object array
	 *
	 * @param integer|string $groupPar Group id or name to get
	 * @param integer        $limit    Limit of users to be returned
	 * @param string         $orderBy  Order by MYSQL string (e.g. 'name ASC', 'email DESC')
	 *
	 * @return boolean|array
	 */
	public function listGroupPermsPaginated($groupPar, int $limit = 10, string $orderBy = null)
	{
		if (! $groupId = $this->getGroupId($groupPar))
		{
			return false;
		}

		$permModel = $this->getModel('Perm');

		$permModel->select('id, name, definition, IF(state = 0 OR state = 1, state, -1) as state');
		$permModel->join($this->config->dbTablePermToGroup, '(' . $this->config->dbTablePerms . '.id = ' . $this->config->dbTablePermToGroup . '.perm_id AND ' . $this->config->dbTablePermToGroup . '.group_id = ' . $groupId . ')', 'left');

		if (! is_null($orderBy))
		{
			$permModel->orderBy($orderBy);
		}

		return [
			'perms' => $permModel->paginate($limit),
			'pager' => $permModel->pager,
		];
	}

	/**
	 * List user permissions
	 *
	 * @param integer $userId User id
	 *
	 * @return boolean|array
	 */
	public function listUserPerms(int $userId = null)
	{
		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $this->getUser($userId))
		{
			return false;
		}

		$permModel = $this->getModel('Perm');

		$permModel->select('id, name, definition, IF(state = 0 OR state = 1, state, -1) as state');
		$permModel->join($this->config->dbTablePermToUser, '(' . $this->config->dbTablePerms . '.id = ' . $this->config->dbTablePermToUser . '.perm_id AND ' . $this->config->dbTablePermToUser . '.user_id = ' . $userId . ')', 'left');

		return $permModel->get()->getResult('array');
	}

	/**
	 * List users with paginate
	 *
	 * Return users as an object array
	 *
	 * @param integer $userId  User id
	 * @param integer $limit   Limit of users to be returned
	 * @param string  $orderBy Order by MYSQL string (e.g. 'name ASC', 'email DESC')
	 *
	 * @return boolean|array
	 */
	public function listUserPermsPaginated(int $userId = null, int $limit = 10, string $orderBy = null)
	{
		if (! $userId)
		{
			$userId = (int) @$this->session->user['id'];
		}

		if (! $this->getUser($userId))
		{
			return false;
		}

		$permModel = $this->getModel('Perm');

		$permModel->select('id, name, definition, IF(state = 0 OR state = 1, state, -1) as state');
		$permModel->join($this->config->dbTablePermToUser, '(' . $this->config->dbTablePerms . '.id = ' . $this->config->dbTablePermToUser . '.perm_id AND ' . $this->config->dbTablePermToUser . '.user_id = ' . $userId . ')', 'left');

		if (! is_null($orderBy))
		{
			$permModel->orderBy($orderBy);
		}

		return [
			'perms' => $permModel->paginate($limit),
			'pager' => $permModel->pager,
		];
	}

	//--------------------------------------------------------------------
	// Error Functions
	//--------------------------------------------------------------------

	/**
	 * Error
	 *
	 * Add message to error array and set flash data
	 *
	 * @param string|array $message   Message to add to array
	 * @param boolean      $flashdata Whether to add $message to session flashdata
	 *
	 * @return void
	 */
	public function error($message, bool $flashdata = null)
	{
		if (is_array($message))
		{
			$this->errors = array_merge($this->errors, $message);
		}
		else
		{
			$this->errors[] = $message;
		}

		if ($flashdata)
		{
			if (is_array($message))
			{
				$this->flashErrors = array_merge($this->flashErrors, $message);
			}
			else
			{
				$this->flashErrors[] = $message;
			}

			$this->session->setFlashdata('errors', $this->flashErrors);
		}
	}

	/**
	 * Keep Errors
	 *
	 * Keeps the flashdata errors for one more page refresh.  Optionally adds the default errors into the
	 * flashdata list.  This should be called last in your controller, and with care as it could continue
	 * to revive all errors and not let them expire as intended.
	 * Benefitial when using Ajax Requests
	 *
	 * @param boolean $includeNonFlash Wheter to store basic errors as flashdata
	 *
	 * @return void
	 */
	public function keepErrors(bool $includeNonFlash = null)
	{
		if ($includeNonFlash)
		{
			$flashErrorsOld    = $this->session->getFlashdata('errors');
			$this->flashErrors = array_merge((is_array($flashErrorsOld) ? $flashErrorsOld : []), $this->errors);
			$this->session->setFlashdata('errors', $this->flashErrors);
		}
		else
		{
			$this->session->keepFlashdata('errors');
		}
	}

	/**
	 * Get Errors Array
	 *
	 * Return array of errors
	 *
	 * @return array
	 */
	public function getErrorsArray()
	{
		return $this->errors;
	}

	/**
	 * Printeger Errors
	 *
	 * Prints string of errors separated by delimiter
	 *
	 * @param string  $divider Separator for error
	 * @param boolean $return  Whether to return instead of echoing
	 *
	 * @return void|string
	 */
	public function printErrors(string $divider = '<br />', bool $return = null)
	{
		$msg = implode($divider, $this->errors);

		if ($return)
		{
			return $msg;
		}

		echo $msg;
	}

	/**
	 * Clear Errors
	 *
	 * Removes errors from error list and clears all associated flashdata
	 *
	 * @return void
	 */
	public function clearErrors()
	{
		$this->errors      = [];
		$this->flashErrors = [];
		$this->session->remove('errors');
	}

	//--------------------------------------------------------------------
	// Info Functions
	//--------------------------------------------------------------------

	/**
	 * Info
	 *
	 * Add message to info array and set flash data
	 *
	 * @param string|array $message   Message to add to infos array
	 * @param boolean      $flashdata Whether add $message to CI flashdata (deflault: false)
	 *
	 * @return void
	 */
	public function info($message, bool $flashdata = null)
	{
		if (is_array($message))
		{
			$this->infos = array_merge($this->infos, $message);
		}
		else
		{
			$this->infos[] = $message;
		}

		if ($flashdata)
		{
			if (is_array($message))
			{
				$this->flashInfos = array_merge($this->flashInfos, $message);
			}
			else
			{
				$this->flashInfos[] = $message;
			}

			$this->session->setFlashdata('infos', $this->flashInfos);
		}
	}

	/**
	 * Keep Infos
	 *
	 * Keeps the flashdata infos for one more page refresh.  Optionally adds the default infos into the
	 * flashdata list.  This should be called last in your controller, and with care as it could continue
	 * to revive all infos and not let them expire as intended.
	 * Benefitial by using Ajax Requests
	 *
	 * @param boolean $includeNonFlash Wheter to store basic errors as flashdata
	 *
	 * @return void
	 */
	public function keepInfos(bool $includeNonFlash = null)
	{
		if ($includeNonFlash)
		{
			$flashInfosOld    = $this->session->getFlashdata('infos');
			$this->flashInfos = array_merge((is_array($flashInfosOld) ? $flashInfosOld : []), $this->infos);
			$this->session->setFlashdata('infos', $this->flashInfos);
		}
		else
		{
			$this->session->keepFlashdata('infos');
		}
	}

	/**
	 * Get Info Array
	 *
	 * Return array of infos
	 *
	 * @return array Array of messages, empty array if no errors
	 */
	public function getInfosArray()
	{
		return $this->infos;
	}

	/**
	 * Printeger Info
	 *
	 * Printeger string of info separated by delimiter
	 *
	 * @param string  $divider Separator for info
	 * @param boolean $return  Whether to return instead of echoing
	 *
	 * @return string|void
	 */
	public function printInfos(string $divider = '<br />', bool $return = null)
	{
		$msg = implode($divider, $this->infos);

		if ($return)
		{
			return $msg;
		}

		echo $msg;
	}

	/**
	 * Clear Info List
	 *
	 * Removes info messages from info list and clears all associated flashdata
	 *
	 * @return void
	 */
	public function clearInfos()
	{
		$this->infos      = [];
		$this->flashInfos = [];
		$this->session->remove('infos');
	}

	//--------------------------------------------------------------------
	// Utility Functions
	//--------------------------------------------------------------------

	/**
	 * Get Model
	 *
	 * @param string $model Model name
	 *
	 * @return object
	 */
	public function getModel(string $model)
	{
		if (strpos($model, '_'))
		{
			$model = str_replace('_', '', ucwords($model, '_'));
		}
		else if (strpos($model, ' '))
		{
			$model = str_replace(' ', '', ucwords($model));
		}
		if (! strpos($model, 'Model'))
		{
			$model .= 'Model';
		}

		$model = '\App\Models\Aauth\\' . $model;

		if (class_exists($model))
		{
			return new $model();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Provides direct access to method in the builder (if available)
	 * and the database connection.
	 *
	 * @param string $name   Call name
	 * @param array  $params Call params
	 *
	 * @return Model|null
	 */
	public function __call(string $name, array $params)
	{
		$config  = $this->config;
		$session = $this->session;

		foreach ($this->modules as $module)
		{
			$module      = '\\App\\Libraries\\Aauth\\' . $module;
			$moduleClass = new $module($config, $session);

			if (method_exists($moduleClass, $name))
			{
				return $moduleClass->$name(...$params);
			}
		}

		return trigger_error('Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR);
	}
}
