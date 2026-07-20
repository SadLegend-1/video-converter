<?php
header('Content-Type: application/json');

$log_file = 'uploads/progress_log.txt';
$dur_file = 'uploads/duration.txt';

if (file_exists($log_file) && file_exists($dur_file)) {
    $total_duration = floatval(file_get_contents($dur_file));
    $content = file_get_contents($log_file);

    // البحث عن آخر وقت ميكروثانية تم الوصول إليه من FFmpeg (out_time_ms)
    if ($total_duration > 0 && preg_match_all('/out_time_us=(\d+)/', $content, $matches)) {
        $last_us = end($matches[1]);
        $current_seconds = $last_us / 1000000;
        
        $percent = min(99, round(($current_seconds / $total_duration) * 100));
        echo json_encode(['percent' => max(1, $percent)]);
        exit;
    }
}

echo json_encode(['percent' => 0]);
?>