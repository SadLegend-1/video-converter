<?php
session_start();
$lang_code = $_GET['lang'] ?? $_SESSION['lang'] ?? 'ar';
$_SESSION['lang'] = $lang_code;

$lang_file = "lang/{$lang_code}.php";
$lang = file_exists($lang_file) ? include $lang_file : include "lang/ar.php";
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>" dir="<?php echo $lang['dir']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --bg-color: #F8FAFC;
            --card-bg: #FFFFFF;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --info-bg: #EFF6FF;
            --info-border: #BFDBFE;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Tajawal', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }

        header {
            background: var(--card-bg);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 15px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo { font-size: 22px; font-weight: 800; color: var(--primary); text-decoration: none; }
        .nav-right { display: flex; align-items: center; gap: 15px; }
        .lang-select { padding: 6px 10px; border-radius: 6px; border: 1px solid var(--border-color); font-weight: 700; color: var(--primary); outline: none; }

        main { flex: 1; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .hero-text { text-align: center; margin-bottom: 30px; }
        .hero-text h1 { font-size: 32px; font-weight: 800; }
        .hero-text p { color: var(--text-muted); max-width: 650px; margin-top: 8px; }

        .form-container {
            background: var(--card-bg);
            width: 100%;
            max-width: 900px;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
        }

        .form-section { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color); }
        .form-section-title { font-size: 19px; font-weight: 800; margin-bottom: 20px; color: var(--primary); }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 700; margin-bottom: 8px; font-size: 15px; }

        select, input[type="file"], input[type="range"] {
            width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; background: #F8FAFC; outline: none;
        }

        .field-desc {
            background: var(--info-bg);
            border: 1px solid var(--info-border);
            color: #1E40AF;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-top: 8px;
            font-weight: 500;
        }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        button[type="submit"] {
            background: var(--primary);
            color: white; border: none; padding: 16px; font-size: 18px; font-weight: 800;
            border-radius: 10px; cursor: pointer; width: 100%; margin-top: 20px; transition: 0.2s;
        }
        button[type="submit"]:hover { background: var(--primary-hover); }

        /* شريط التقدم */
        #progressContainer {
            display: none;
            margin-top: 25px;
            background: #F1F5F9;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .progress-bar-bg {
            width: 100%; height: 20px; background: #E2E8F0; border-radius: 10px; overflow: hidden; margin: 15px 0;
        }

        .progress-bar-fill {
            height: 100%; width: 0%; background: linear-gradient(90deg, #4F46E5, #818CF8); transition: width 0.3s ease;
        }

        #downloadArea { display: none; margin-top: 25px; text-align: center; }
        .download-btn {
            display: inline-block; background: #10B981; color: white; padding: 15px 30px;
            border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 18px;
        }
        .download-btn:hover { background: #059669; }

        footer { text-align: center; padding: 20px; color: var(--text-muted); font-size: 14px; margin-top: auto; }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo">🎬 MediaTools Pro</a>
        <div class="nav-right">
            <select class="lang-select" onchange="location = '?lang=' + this.value;">
                <option value="ar" <?php if($lang_code=='ar') echo 'selected'; ?>>🇸🇦 العربية</option>
                <option value="en" <?php if($lang_code=='en') echo 'selected'; ?>>🇺🇸 English</option>
                <option value="es" <?php if($lang_code=='es') echo 'selected'; ?>>🇪🇸 Español</option>
                <option value="ko" <?php if($lang_code=='ko') echo 'selected'; ?>>🇰🇷 한국어</option>
                <option value="zh" <?php if($lang_code=='zh') echo 'selected'; ?>>🇨🇳 中文</option>
            </select>
        </div>
    </header>

    <main>
        <div class="hero-text">
            <h1><?php echo $lang['hero_title']; ?></h1>
            <p><?php echo $lang['hero_desc']; ?></p>
        </div>

        <div class="form-container">
            <form id="convertForm" enctype="multipart/form-data">
                
                <!-- 1. الأساسيات والصيغ -->
                <div class="form-section">
                    <div class="form-section-title"><?php echo $lang['sec_basic']; ?></div>
                    <div class="form-group">
                        <label><?php echo $lang['lbl_file']; ?></label>
                        <input type="file" name="video_file" accept="video/*" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo $lang['lbl_format']; ?></label>
                        <select name="target_format" required>
                            <option value="mp4">MP4 (Standard Video)</option>
                            <option value="mkv">MKV (Matroska High Quality)</option>
                            <option value="webm">WEBM (Web Optimized)</option>
                            <option value="mov">MOV (Apple QuickTime)</option>
                            <option value="avi">AVI (Audio Video Interleave)</option>
                            <option value="wmv">WMV (Windows Media Video)</option>
                            <option value="flv">FLV (Flash Video)</option>
                            <option value="3gp">3GP (Mobile Format)</option>
                            <option value="ogv">OGV (Ogg Video)</option>
                            <option value="gif">GIF (Animated Image)</option>
                            <option value="mp3">MP3 (Audio Only)</option>
                        </select>
                    </div>
                </div>

                <!-- 2. خيارات الفيديو والتشفير -->
                <div class="form-section">
                    <div class="form-section-title"><?php echo $lang['sec_advanced']; ?></div>
                    
                    <div class="form-group">
                        <label><?php echo $lang['lbl_codec']; ?></label>
                        <select name="video_codec">
                            <option value="default">Default Auto Codec</option>
                            <option value="libx264">H.264 / AVC</option>
                            <option value="libx265">H.265 / HEVC</option>
                            <option value="libvpx-vp9">VP9 (Open Web)</option>
                        </select>
                        <div class="field-desc"><?php echo $lang['desc_codec']; ?></div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><?php echo $lang['lbl_crf']; ?> <span id="crfVal">23</span></label>
                            <input type="range" name="crf_value" min="0" max="51" value="23" oninput="document.getElementById('crfVal').innerText = this.value">
                            <div class="field-desc"><?php echo $lang['desc_crf']; ?></div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $lang['lbl_preset']; ?></label>
                            <select name="encoding_preset">
                                <option value="medium">Medium (Default Balance)</option>
                                <option value="ultrafast">Ultrafast (Super Fast, Bigger Size)</option>
                                <option value="fast">Fast</option>
                                <option value="slow">Slow (Better Compression)</option>
                                <option value="veryslow">Very Slow (Max Compression)</option>
                            </select>
                            <div class="field-desc"><?php echo $lang['desc_preset']; ?></div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><?php echo $lang['lbl_res']; ?></label>
                            <select name="video_resolution">
                                <option value="original"><?php echo $lang['opt_res_orig']; ?></option>
                                <option value="3840x2160">4K Ultra HD</option>
                                <option value="1920x1080">1080p Full HD</option>
                                <option value="1280x720">720p HD</option>
                                <option value="640x360">360p Low</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang['lbl_fps']; ?></label>
                            <select name="video_fps">
                                <option value="original"><?php echo $lang['opt_fps_orig']; ?></option>
                                <option value="60">60 FPS</option>
                                <option value="30">30 FPS</option>
                                <option value="24">24 FPS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 3. إعدادات الصوت -->
                <div class="form-section">
                    <div class="form-section-title"><?php echo $lang['sec_audio']; ?></div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label><?php echo $lang['lbl_acodec']; ?></label>
                            <select name="audio_codec">
                                <option value="copy">Copy Original Audio</option>
                                <option value="aac">AAC (Standard)</option>
                                <option value="libmp3lame">MP3</option>
                                <option value="libopus">Opus (High Efficiency)</option>
                            </select>
                            <div class="field-desc"><?php echo $lang['desc_acodec']; ?></div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $lang['lbl_abitrate']; ?></label>
                            <select name="audio_bitrate">
                                <option value="320k">320 kbps (Studio)</option>
                                <option value="1920k">192 kbps (High)</option>
                                <option value="128k" selected>128 kbps (Standard)</option>
                                <option value="96k">96 kbps (Low)</option>
                            </select>
                            <div class="field-desc"><?php echo $lang['desc_abitrate']; ?></div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="submitBtn"><?php echo $lang['btn_submit']; ?></button>
            </form>

            <!-- شريط التقدم بـ AJAX بدون مغادرة الصفحة -->
            <div id="progressContainer">
                <h3><?php echo $lang['progress_title']; ?></h3>
                <p><?php echo $lang['progress_sub']; ?></p>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" id="progressBar"></div>
                </div>
                <h4 id="progressPercent">0%</h4>
            </div>

            <!-- زر التنزيل عند الانتظار -->
            <div id="downloadArea">
                <a href="#" id="downloadLink" class="download-btn"><?php echo $lang['download_btn']; ?></a>
            </div>
        </div>
    </main>

    <script>
document.getElementById('convertForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    let xhr = new XMLHttpRequest();

    document.getElementById('submitBtn').style.display = 'none';
    document.getElementById('progressContainer').style.display = 'block';
    document.getElementById('downloadArea').style.display = 'none';

    // الاستعلام عن نسبة التقدم الحقيقية كل ثانية من ملف progress.php
    let progressInterval = setInterval(function() {
        fetch('progress.php')
            .then(response => response.json())
            .then(data => {
                if (data.percent > 0) {
                    document.getElementById('progressBar').style.width = data.percent + '%';
                    document.getElementById('progressPercent').innerText = data.percent + '%';
                }
            });
    }, 1000);

    xhr.onload = function() {
        clearInterval(progressInterval); // إيقاف التكرار عند الانتهاء
        if (xhr.status === 200) {
            try {
                let res = JSON.parse(xhr.responseText);
                if(res.status === 'success') {
                    document.getElementById('progressBar').style.width = '100%';
                    document.getElementById('progressPercent').innerText = '100%';
                    document.getElementById('downloadLink').href = 'download.php?file=' + res.file;
                    document.getElementById('downloadArea').style.display = 'block';
                } else {
                    alert('حدث خطأ: ' + res.message);
                    document.getElementById('submitBtn').style.display = 'block';
                }
            } catch(err) {
                alert('حدث خطأ أثناء المعالجة.');
                document.getElementById('submitBtn').style.display = 'block';
            }
        }
    };

    xhr.open('POST', 'convert.php', true);
    xhr.send(formData);
});
    </script>

    <footer><?php echo $lang['footer']; ?></footer>
</body>
</html>