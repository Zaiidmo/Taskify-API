<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'status','user_id'];

    public function Done(){
        $this->status = 'Done';
        $this->save();
    }
    public function Doing(){
        $this->status = 'Doing';
        $this->save();
    }
    public function ToDo(){
        $this->status = 'To Do';
        $this->save();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
