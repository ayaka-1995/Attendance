<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Application;

class ApplicationBreak extends Model//申請中の休憩案、「申請時点でこう直したい」という休憩データ
{
    protected $table = 'application_breaks';
    protected $fillable = ['application_id', 'break_in', 'break_out'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
