<?php namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model{
	protected $table      = 'assignment';
	protected $primaryKey = 'id';

	protected $returnType = 'array';
	protected $useSoftDeletes = true;

	protected $allowedFields = ['classID', 'description', 'maxPoints'];

	protected $useTimestamps = false;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}
