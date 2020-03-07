<?php namespace App\Controllers;

use App\Libraries\Aauth;

class SendEmail extends BaseController{

	/**
	 * index
	 *
	 * log the user out
	 *
	 *
	 * @return string, array
	 */
	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$aauthUser = $this->db->query('SELECT email FROM aauth_users WHERE id = ' . "'" . $this->aauth->getUserID() . "'");
		$aauthUserResult = $aauthUser->getResult();

		foreach ($aauthUserResult as $row){
			$sender = $row->email;
		}

		$recipient = $this->request->getVar('recipient');
		$subject = $this->request->getVar('subject');
		$senderEmail = $sender;
		$message = $this->request->getVar('messageBody');

		mail("'" . $recipient . "'", "'" . $subject . "'", "'" . $message . "'", "From: ". "'" . $senderEmail . "'");

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		$parentNamesList = $this->db->query('SELECT lms_students.firstName AS firstNameS, lms_students.lastName AS lastNameS, parent.firstName AS firstNameP, parent.lastName AS lastNameP, email FROM parentStudent JOIN lms_students ON parentStudent.studentID = lms_students.id JOIN parent ON parentStudent.parentID = parent.id WHERE parentStudent.classID = ' . $this->aauth->getUserVar('classID'));
		$parentInfo = $parentNamesList->getResult();

		$data['parentList'] = $parentInfo;
		$data['sent'] = true;

		return view('parents', $data);
	}
}
