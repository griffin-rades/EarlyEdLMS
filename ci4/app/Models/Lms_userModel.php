<?php namespace App\Models;

use CodeIgniter\Model;

class Lms_userModel extends Model{
    protected $table      = 'lms_teacher';
    protected $primaryKey = 'teacherNumber';

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['firstName', 'lastName'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;
}
