@echo off
chcp 65001 >nul
title تشغيل بث كاميرا الحاسوب - VLC Live Stream
echo ==========================================================
echo        منظومة تأهيل الخريجين - بث كاميرا الحاسوب
echo ==========================================================
echo.
echo  كاميرا اللابتوب المتصلة: HP TrueVision HD Camera
echo  رابط البث في المنظومة: http://127.0.0.1:8090/stream.ogg
echo.
echo  * اترك هذه النافذة مفتوحة أثناء البث المباشر.
echo  * لإيقاف البث: أغلق هذه النافذة.
echo ==========================================================
echo.

"D:\VLC\vlc.exe" -I dummy dshow:// :dshow-vdev="HP TrueVision HD Camera" :dshow-adev=none :live-caching=300 --sout=#transcode{vcodec=theo,vb=1500,scale=Auto,acodec=none}:http{mux=ogg,dst=:8090/stream.ogg}

pause
