<?php
 
namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
 
class UserModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_user';

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
        'email', 
        'username',
        'first_name',
        'last_name',
        'password',
        'is_verified',
        'jwt_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function addUserData($userData) {

        try {
            DB::table($this->table)->insert([
                'email' => $userData['email'],
                'username' => $userData['username'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'password' => password_hash($userData['password'],PASSWORD_DEFAULT),
                'created_at' => date('y-m-d H:i:s')
            ]);
            return true;
        } catch (Exception $error) {
            return false;
        }

    }

    public function getUserData($username) {

        return DB::table($this->table)->where('username','=',$username)->orWhere('email','=',$username)
        ->get()->first();

    }

}