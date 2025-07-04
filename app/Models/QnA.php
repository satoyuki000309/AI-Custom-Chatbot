<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QnA extends Model
{
    protected $table = 'qna';
    protected $fillable = ['question', 'answer'];
}
