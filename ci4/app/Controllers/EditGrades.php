<?php namespace App\Controllers;

use App\Libraries\Aauth;

class EditGrades extends BaseController{

	/**
	 * index
	 *
	 * This is called when the user access the grades page from the navigation
	 *
	 * @return string, array
	 */
	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;
		$data['db'] = $this->db;

		$assignmentList = $this->db->query('SELECT title, description, maxPoints, id, maxPoints FROM assignment WHERE classID = ' . $this->aauth->getUserVar('classID'));
		$list = $assignmentList->getResult();

		$data['assignList'] = $list;

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id, lms_students.info FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		return view('grades', $data);
	}

	/**
	 * gradeStudent
	 *
	 * This is called when the teacher is grading a student on an assignment
	 *
	 * @return string, array
	 */
	function gradeStudent(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;
		$data['db'] = $this->db;

		$studentID = $this->request->getVar('studentInfo');
		$assignID = $this->request->getVar('assign');
		$grade = $this->request->getVar('pointSlider2');

		$checkID = $this->db->query('SELECT id FROM assignGrade WHERE studentID = ' . "'" . $studentID . "'" . 'AND assignmentID = '."'".$assignID."'");
		$id = $checkID->getResult();

		foreach ($id as $row) {
			 $pKey = $row->id;
		}

		//if the student already has a grade for that assigment we want to edit that grade instead of making a new grade entry.
		if($id){
			$gradeData = [
				'id' => $pKey,
				'studentID' => $studentID,
				'classID' => $this->aauth->getUserVar('classID'),
				'assignmentID' => $assignID,
				'points' => $grade
			];

			$this->gradeModel->save($gradeData);
		}else{
			$gradeData = [
				'studentID' => $studentID,
				'classID' => $this->aauth->getUserVar('classID'),
				'assignmentID' => $assignID,
				'points' => $grade
			];

			$this->gradeModel->save($gradeData);
		}

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id, lms_students.info FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		$assignmentList = $this->db->query('SELECT title, description, maxPoints, id FROM assignment WHERE classID = ' . $this->aauth->getUserVar('classID'));
		$list = $assignmentList->getResult();

		$data['assignList'] = $list;

		return view('grades', $data);

	}
}
