<?php

namespace App\Services;

class CsvHandler
{
    public static function read($filePath)
    {
        $handle = fopen($filePath, "r");
        $headers = fgetcsv($handle);
        $data = [];
        
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = array_combine($headers, $row);
        }
        
        fclose($handle);
        return $data;
    }
    
    public static function write($data, $headers = null)
    {
        $output = fopen("php://temp", "r+");
        
        if ($headers) {
            fputcsv($output, $headers);
        }
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}