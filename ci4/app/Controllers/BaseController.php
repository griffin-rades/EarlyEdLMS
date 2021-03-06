<?php
namespace App\Controllers;

/**
* Class BaseController
*
* BaseController provides a convenient place for loading components
* and performing functions that are needed by all your controllers.
* Extend this class in any new controllers:
*     class Home extends BaseController
*
* For security be sure to declare any new methods as protected or private.
*
* @package CodeIgniter
*/

use CodeIgniter\Controller;

class BaseController extends Controller
{

	/**
	* An array of helpers to be loaded automatically upon
	* class instantiation. These helpers will be available
	* to all other controllers that extend BaseController.
	 *
	* AAuth Library
	* @var \App\Libraries\Aauth $aauth
	*/

	protected $aauth;
	protected $helpers = [];

	/**
	 * declare session variable
	 *
	 * @var \CodeIgniter\Session\SessionInterface $session
	 */
	protected $session = null;

	/**
	 * declare request variable
	 *
	 * @var \CodeIgniter\HTTP\Request $request
	 */
	protected $request;

	/**
	 * declare db variable
	 *
	 * @var \CodeIgniter\Database\BaseBuilder $db
	 */
	protected $db;

	/**
	 * declare vars for the models
	 *
	 * @var \App\Models\StudentModel $studentModel
	 * @var \App\Models\AssignmentModel $assignmentModel
	 * @var \App\Models\GradeAssignmentModel $gradeModel
	 * @var \App\Models\parentModel $parentModel
	 * @var \App\Models\parentStudentModel $parentStudentModel
	 */
	protected $studentModel;
	protected $assignmentModel;
	protected $gradeModel;
	protected $parentModel;
	protected $parentStudentModel;

	/**
	* Constructor.
	*/
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:

		$this->session = \Config\Services::session();
		$this->aauth = new \App\Libraries\Aauth();
		$this->request = \Config\Services::request();
		$this->db = \Config\Database::connect();
		$this->studentModel = new \App\Models\StudentModel();
		$this->assignmentModel = new \App\Models\AssignmentModel();
		$this->gradeModel = new \App\Models\GradeAssignmentModel();
		$this->parentModel = new \App\Models\parentModel();
		$this->parentStudentModel = new \App\Models\parentStudentModel();
	}

}
