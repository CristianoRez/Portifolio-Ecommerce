<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'admin_id'
    ];
    public function direct_message(){
        return $this->hasmany(Direct_message::class, 'chat_id', 'id');
    }
    public function last_direct_message(){
        return $this->hasone(Direct_message::class, 'chat_id', 'id')->latest();
    }
    public function user_id(){
        return $this->hasone(User::class, 'id', 'user_id');
    }
    public function admin_id(){
        return $this->hasone(User::class, 'id', 'admin_id');
    }
}
