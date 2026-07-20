<?php
// إعطاء مهلة كافية للمعالجة
set_time_limit(3600);
ini_set('max_execution_time', 3600);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['video_file'])) {

    $upload_dir = 'uploads/';
    $converted_dir = 'converted/';

    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    if (!is_dir($converted_dir)) mkdir($converted_dir, 0777, true);

    $file_tmp = $_FILES['video_file']['tmp_name'];
    $file_name = $_FILES['video_file']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $unique_name = uniqid('vid_') . '.' . $file_ext;
    $target_upload = $upload_dir . $unique_name;

    if (move_uploaded_file($file_tmp, $target_upload)) {

        // 1. إنشاء/تصفير ملف التقدّم ومعرفة المدة الكلية للفيديو
        $progress_log = $upload_dir . 'progress_log.txt';
        $progress_percent = $upload_dir . 'progress.txt';
        
        file_put_contents($progress_log, '');
        file_put_contents($progress_percent, '0');

        $dur_cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($target_upload);
        $total_duration = floatval(shell_exec($dur_cmd));
        
        // حفظ المدة الإجمالية لطلبها من progress.php
        file_put_contents($upload_dir . 'duration.txt', $total_duration);

        $target_format = $_POST['target_format'] ?? 'mp4';
        $video_codec = $_POST['video_codec'] ?? 'default';
        $crf_value = intval($_POST['crf_value'] ?? 23);
        $encoding_preset = $_POST['encoding_preset'] ?? 'medium';
        $video_resolution = $_POST['video_resolution'] ?? 'original';
        $video_fps = $_POST['video_fps'] ?? 'original';
        $audio_codec = $_POST['audio_codec'] ?? 'copy';
        $audio_bitrate = $_POST['audio_bitrate'] ?? '128k';

        $output_filename = uniqid('converted_') . '.' . $target_format;
        $output_file_path = $converted_dir . $output_filename;

        // 2. بناء أمر FFmpeg المباشر مع كتابة ملف Progress تلقائياً
        $cmd = "ffmpeg -y -progress " . escapeshellarg($progress_log) . " -i " . escapeshellarg($target_upload);

        if ($video_codec !== 'default') $cmd .= " -c:v " . escapeshellarg($video_codec);
        $cmd .= " -crf " . $crf_value . " -preset " . escapeshellarg($encoding_preset);

        if ($target_format === 'mp3') {
            $cmd .= " -vn -c:a libmp3lame -b:a " . escapeshellarg($audio_bitrate);
        } else {
            $cmd .= " -c:a " . escapeshellarg($audio_codec);
            if ($audio_codec !== 'copy') $cmd .= " -b:a " . escapeshellarg($audio_bitrate);
        }

        if ($video_resolution !== 'original') $cmd .= " -vf scale=" . escapeshellarg($video_resolution);
        if ($video_fps !== 'original') $cmd .= " -r " . intval($video_fps);

        $cmd .= " " . escapeshellarg($output_file_path);

        // تنفيذ الأمر المباشر
        exec($cmd, $output, $return_code);

        if ($return_code === 0 && file_exists($output_file_path)) {
            file_put_contents($progress_percent, '100');
            echo json_encode([
                'status' => 'success',
                'file' => $output_filename
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء معالجة الفيديو بـ FFmpeg.'
            ]);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل رفع الملف.']);
    }
}
?>