<?php
 
namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
 
class TaskModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_task';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_task';

     /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id_user', 
        'title',
        'description',
        'deadline',
        'done_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function addTask($taskData) {

        try {
            DB::table($this->table)->insert([
                'id_user' => $taskData['id_user'],
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'deadline' => $taskData['deadline'],
                'created_at' => date('y-m-d H:i:s')
            ]);
            return true;
        } catch (Exception $error) {
            return false;
        }

    }

    public function getAllTask($userId) {

        return DB::table($this->table)->where('id_user','=',$userId)->where('deleted_at','=',null)
        ->get()->first();

    }

    public function getTaskById($id_task) {

        return DB::table($this->table)
                ->select('tb_user.username','tb_task.id_task','tb_task.title','tb_task.description','tb_task.deadline','tb_task.done_date','tb_task.created_at')
                ->join('tb_user','tb_task.id_user','=','tb_user.id_user')
                ->where('tb_task.id_task','=',$id_task)->get()->first();

    }

    public function deleteTask($id_task,$id_user) {

        $result = DB::table($this->table)->where('id_task','=',$id_task)->where('id_user','=',$id_user)->update(['deleted_at' => date('y-m-d H:i:s')]);

        if ($result !== 0) {
            return true;
        }

        return false;

    }

    public function updateTask($data) {

        $result = DB::table($this->table)->where('id_task','=',$data['id_task'])->where('id_user','=',$data['id_user'])->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'updated_at' => date('y-m-d H:i:s'),
        ]);

        if ($result !== 0) {
            return true;
        }

        return false;

    }

    public function setTaskDone($id_user, $id_task) {

        $result = DB::table($this->table)->where('id_task','=',$id_task)->where('id_user','=',$id_user)->update([
            'done_date' => date('y-m-d H:i:s'),
        ]);

        if ($result !== 0) {
            return true;
        }

        return false;

    }

}