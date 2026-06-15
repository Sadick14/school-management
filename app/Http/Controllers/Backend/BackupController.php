<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function backup()
    {
        try {
            Artisan::call('backup:database');
            return response()->json(['success' => true, 'message' => 'Database backed up successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Backup failed: ' . $e->getMessage()], 500);
        }
    }
}
