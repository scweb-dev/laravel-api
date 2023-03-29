<?php

namespace App\Models\Soundblock\Reports;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiskspaceMonthlyReport extends BaseModel {
    use HasFactory;

    protected $table = "soundblock_reports_diskspace_monthly";

    protected $primaryKey = "row_id";
}
