import cv2
import sys
import time
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer

cap = None

class CamHandler(BaseHTTPRequestHandler):
    def log_message(self, format, *args):
        # منع طباعة سجلات الطلبات المزعجة في التيرمينال
        return

    def do_GET(self):
        self.send_response(200)
        self.send_header('Content-Type', 'multipart/x-mixed-replace; boundary=frame')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Cache-Control', 'no-cache, private')
        self.end_headers()
        
        while cap and cap.isOpened():
            ret, frame = cap.read()
            if not ret:
                time.sleep(0.05)
                continue
            
            # تقليص الحجم قليلاً لضمان سرعة فائقة وجودة ممتازة
            ret, jpeg = cv2.imencode('.jpg', frame, [int(cv2.IMWRITE_JPEG_QUALITY), 80])
            if not ret:
                continue
                
            try:
                self.wfile.write(b'--frame\r\n')
                self.send_header('Content-Type', 'image/jpeg')
                self.send_header('Content-Length', str(len(jpeg)))
                self.end_headers()
                self.wfile.write(jpeg.tobytes())
                self.wfile.write(b'\r\n')
                time.sleep(0.03) # ~30 FPS
            except (ConnectionResetError, BrokenPipeError):
                break

def main():
    global cap
    print("=" * 60)
    print("  Graduate Training System - Laptop Camera Streamer")
    print("=" * 60)
    print("Opening webcam...")
    
    # محاولة فتح الكاميرا
    cap = cv2.VideoCapture(0, cv2.CAP_DSHOW)
    if not cap.isOpened():
        cap = cv2.VideoCapture(0)
        
    if not cap.isOpened():
        print("\n[!] خطأ: تعذر فتح الكاميرا!")
        print("تأكد من إغلاق أي برنامج آخر يستخدم الكاميرا حالياً (مثل برنامج VLC المفتوح).")
        input("\nاضغط Enter للخروج...")
        sys.exit(1)
        
    cap.set(cv2.CAP_PROP_FRAME_WIDTH, 1280)
    cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 720)
    
    port = 8090
    server_address = ('0.0.0.0', port)
    
    try:
        httpd = ThreadingHTTPServer(server_address, CamHandler)
        print("\n[OK] Webcam Stream is LIVE!")
        print(f"URL: http://127.0.0.1:{port}/video")
        print("Running server on port 8090...")
        print("=" * 60)
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\nتم إيقاف البث.")
    finally:
        if cap:
            cap.release()

if __name__ == '__main__':
    main()
