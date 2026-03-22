<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    public function index()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return response('Log file not found', 404);
        }
        
        // Get last 100 lines of the log file
        $lines = [];
        $file = new \SplFileObject($logPath);
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        
        $startLine = max(0, $totalLines - 100);
        $file->seek($startLine);
        
        while (!$file->eof()) {
            $line = $file->current();
            if (!empty(trim($line))) {
                $lines[] = $line;
            }
            $file->next();
        }
        
        return view('admin.logs', ['logs' => $lines]);
    }
    
    public function clear()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }
        
        return redirect()->route('logs.index')->with('success', 'Logs cleared successfully');
    }
}