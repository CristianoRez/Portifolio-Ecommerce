<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direct_message extends Model
{
    use HasFactory;
    protected $fillable = [
        'message',
        'chat_id',
        'sender'        
    ];
    public function user(){
        return $this->hasone(User::class, 'id', 'user_id');
    }
}
