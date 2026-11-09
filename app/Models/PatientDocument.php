<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientDocument extends Model
{
    protected $fillable = ['original_name', 'path', 'mime_type', 'size', 'uploaded_by'];
}
