<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function index()
    {
        // legacy used is_view check on backup, navigation uses database_backup
        abort_unless(get_permission('database_backup','is_view') || get_permission('backup','is_view'), 403, translate('access_denied'));
        $files = [];
        $dir = public_path('uploads/db_backup');
        if (is_dir($dir)) {
            foreach (glob($dir.'/*.zip') as $f) {
                $files[] = ['name'=>basename($f),'size'=>filesize($f),'mtime'=>filemtime($f)];
            }
            usort($files, fn($a,$b)=> $b['mtime'] <=> $a['mtime']);
        }
        return view('backup.index', ['title'=>translate('database_backup'),'files'=>$files]);
    }

    public function create()
    {
        abort_unless(get_permission('database_backup','is_view') || get_permission('backup','is_add'), 403, translate('access_denied'));
        $dir = public_path('uploads/db_backup');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $filename = 'DB-backup_'.date('Y-m-d_H-i').'.zip';
        $path = $dir.'/'.$filename;
        // Use mysqldump if available else create placeholder
        try {
            $db = config('database.connections.mysql.database');
            $user = config('database.connections.mysql.username');
            $pass = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port') ?? 3306;
            $mysqldump = trim(shell_exec('which mysqldump'));
            if ($mysqldump) {
                $cmd = sprintf('mysqldump -h %s -P %s -u %s %s %s | zip > %s',
                    escapeshellarg($host), escapeshellarg($port), escapeshellarg($user),
                    $pass ? '-p'.escapeshellarg($pass) : '',
                    escapeshellarg($db), escapeshellarg($path));
                shell_exec($cmd);
            }
            if (!file_exists($path) || filesize($path) < 100) {
                // fallback: create zip with SQL dump via PHP
                $zip = new \ZipArchive();
                if ($zip->open($path, \ZipArchive::CREATE)===true) {
                    $tables = DB::select('SHOW TABLES');
                    $sql = '';
                    foreach ($tables as $t) {
                        $table = array_values((array)$t)[0];
                        $create = DB::select("SHOW CREATE TABLE `{$table}`")[0];
                        $createSql = array_values((array)$create)[1] ?? '';
                        $sql .= $createSql.";\n\n";
                        $rows = DB::table($table)->get();
                        foreach ($rows as $row) {
                            $vals = array_map(fn($v)=> is_null($v) ? 'NULL' : DB::getPdo()->quote($v), (array)$row);
                            $sql .= "INSERT INTO `{$table}` VALUES (".implode(',', $vals).");\n";
                        }
                        $sql .= "\n";
                    }
                    $zip->addFromString('backup.sql', $sql);
                    $zip->close();
                }
            }
            set_alert('success', translate('database_backup_completed'));
        } catch (\Throwable $e) {
            set_alert('error', translate('database_backup_failed').': '.$e->getMessage());
        }
        return redirect()->route('backup.index');
    }

    public function download(Request $request)
    {
        $file = $request->query('file');
        $path = public_path('uploads/db_backup/'.$file);
        abort_unless(file_exists($path), 404);
        return response()->download($path);
    }

    public function deleteFile(string $file)
    {
        abort_unless(get_permission('database_backup','is_delete') || get_permission('backup','is_delete'), 403, translate('access_denied'));
        $path = public_path('uploads/db_backup/'.$file);
        if (file_exists($path)) unlink($path);
        if (request()->expectsJson() || request()->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('backup.index');
    }
}
