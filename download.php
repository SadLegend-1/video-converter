<?php
// إيقاف إظهار الأخطاء النصية داخل الملف لعدم إتلاف الفيديو
error_reporting(0);

if (isset($_GET['file'])) {
    
    // تنظيف اسم الملف للأمان
    $file_name = basename($_GET['file']);
    $file_path = 'converted/' . $file_name;

    // التأكد من وجود الملف وحجمه
    if (file_exists($file_path) && is_file($file_path)) {
        
        // مسح أي بفر أو مساحات فارغة في الذاكرة لمنع تلف الفيديو
        while (ob_get_level()) {
            ob_end_clean();
        }

        // تحديد نوع الملف تلقائياً ليفهمه مشغل الفيديو في جهازك
        $mime_type = mime_content_type($file_path);
        if (!$mime_type) {
            $mime_type = 'application/octet-stream';
        }

        // إرسال الهيدرز الصحيحة التي تضمن فتح الفيديو بسلاسة
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . rawurlencode($file_name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // قراءة الملف وإرساله بالكامل للجهاز
        readfile($file_path);
        exit;
    } else {
        echo "عذراً، الملف غير موجود أو تعذر الوصول إليه.";
    }
} else {
    echo "طلب غير صحيح.";
}
?>