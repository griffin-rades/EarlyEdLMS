<?php namespace App\Models;

use CodeIgniter\Model;

class GradeAssignmentModel extends Model{
	protected $table      = 'assignGrade';
	protected $primaryKey = 'id';

	protected $returnType = 'array';
	protected $useSoftDeletes = true;

	protected $allowedFields = ['studentID', 'classID', 'assignmentID', 'points'];

	protected $useTimestamps = false;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}

