# Hướng dẫn thiết lập Laravel Scheduler

## Command không cần Queue Worker

Command `tasks:process-overdue` chạy trực tiếp qua Laravel Scheduler, **KHÔNG CẦN** bật queue worker.

## Cách thiết lập

### 1. Trên Linux/Unix (Production)

Thêm vào crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Hoặc chỉnh sửa crontab:
```bash
crontab -e
```

Thêm dòng:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Trên Windows (Development)

Sử dụng Task Scheduler:
1. Mở Task Scheduler
2. Tạo task mới
3. Trigger: Mỗi phút
4. Action: Chạy `php artisan schedule:run` trong thư mục project

Hoặc sử dụng PowerShell script:
```powershell
# Chạy mỗi phút
while ($true) {
    php artisan schedule:run
    Start-Sleep -Seconds 60
}
```

### 3. Kiểm tra Schedule

Xem danh sách scheduled tasks:
```bash
php artisan schedule:list
```

Chạy thủ công để test:
```bash
php artisan tasks:process-overdue
```

### 4. Chạy Schedule Test

Test scheduler (chạy tất cả tasks đã đến giờ):
```bash
php artisan schedule:run
```

## Lưu ý

- **KHÔNG CẦN** queue worker (`php artisan queue:work`)
- **CẦN** cron job hoặc task scheduler chạy `php artisan schedule:run` mỗi phút
- Command đã được thiết lập `runInBackground()` để không block các tasks khác
- Command sẽ tự động chạy mỗi ngày lúc 00:00 (theo timezone Asia/Ho_Chi_Minh)

