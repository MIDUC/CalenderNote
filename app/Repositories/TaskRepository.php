<?php

namespace App\Repositories;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as Log;


class TaskRepository extends BaseRepository
{
    /**
     * TaskRepository constructor.
     *
     * @param Task $model
     */
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public static function generateTasksForSchedule($schedule)
    {
        Log::debug('Generating tasks for schedule ID: ' . $schedule->id);
        // ⭐ 1. KIỂM TRA TASK TƯƠNG LAI HIỆN CÓ
        // Nếu tồn tại bất kỳ task nào cho schedule này có due_date lớn hơn thời điểm hiện tại, thì không sinh task mới.
        $futureTaskExists = Task::where('schedule_id', $schedule->id)
            // Sử dụng '>' để tìm task có ngày đến hạn lớn hơn thời điểm hiện tại (now())
            ->where('task_date', '>', now())
            ->exists();
        Log::debug("Kiểm tra tồn tại task tương lai: " . ($futureTaskExists ? "Có" : "Không"));
        if ($futureTaskExists) {
            Log::debug("Tồn tại task của lịch này");
            // Nếu có task tương lai, dừng quá trình sinh task.
            return false;
        }
        // Lấy kiểu lặp lại
        $repeatType = $schedule->repeat_type;
        Log::debug("Kiểu lặp lại: " . $repeatType);
        // Nếu không lặp lại ('none') HOẶC lịch trình bị vô hiệu hóa, thoát ngay
        if ($repeatType === 'none') {
            Log::debug('Lịch trình không lặp lại hoặc bị vô hiệu hóa.');
            return false;
        }

        // Chuyển đổi ngày bắt đầu thành đối tượng Carbon
        $startDate = Carbon::parse($schedule->start_date);
        if( $startDate->lessThan(Carbon::now())) {
            $startDate = Carbon::now();
        }
        // 1. Xử lý End Date (Nếu null, lấy ngày cuối tháng của Start Date)
        if (empty($schedule->end_date)) {
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $endDate = Carbon::parse($schedule->end_date);
        }

        // Nếu ngày kết thúc nhỏ hơn ngày bắt đầu, thoát 
        if ($endDate->lessThan($startDate)) {
            Log::debug("Ngày kết thúc nhỏ hơn ngày bắt đầu");
            return false;
        }
        Log::debug("Bắt đầu sinh task từ " . $startDate->toDateString() . " đến " . $endDate->toDateString());
        // Lấy và parse mảng ngày lặp lại (đảm bảo chúng là mảng, ví dụ: [1, 3, 5])
        $daysOfWeek = $schedule->days_of_week ? json_decode($schedule->days_of_week, true) : [];
        $daysOfMonth = $schedule->days_of_month ? json_decode($schedule->days_of_month, true) : [];

        // Đảm bảo $daysOfWeek là mảng số nguyên (chỉ cần cho mục đích so sánh sau này)
        if (is_array($daysOfWeek)) {
            $daysOfWeek = array_map('intval', $daysOfWeek);
        }
        // Đảm bảo $daysOfMonth là mảng số nguyên
        if (is_array($daysOfMonth)) {
            $daysOfMonth = array_map('intval', $daysOfMonth);
        }

        DB::beginTransaction();
        try {
            Log::debug("Bắt đầu sinh task từ " . $startDate->toDateString() . " đến " . $endDate->toDateString());
            $currentDate = $startDate->copy();

            while ($currentDate->lessThanOrEqualTo($endDate)) {
                $shouldCreateTask = false;

                // ⭐ 2. LOGIC KIỂM TRA ĐIỀU KIỆN LẶP LẠI

                if ($repeatType === 'daily') {
                    // Hàng ngày: Luôn tạo
                    $shouldCreateTask = true;
                } else if ($repeatType === 'weekly' && !empty($daysOfWeek)) {
                    // Hàng tuần: Kiểm tra ngày trong tuần (1 = T2, 7 = CN)
                    // Carbon: $currentDate->dayOfWeekIso() trả về 1 (T2) đến 7 (CN)
                    $dayOfWeek = (int) $currentDate->format('N');
                    if (in_array($dayOfWeek, $daysOfWeek)) {
                        $shouldCreateTask = true;
                    }
                } else if ($repeatType === 'monthly' && !empty($daysOfMonth)) {
                    // Hàng tháng: Kiểm tra ngày trong tháng (1 đến 31)
                    $dayOfMonth = $currentDate->day; // Carbon day property
                    if (in_array($dayOfMonth, $daysOfMonth)) {
                        $shouldCreateTask = true;
                    }
                }

                // ⭐ 3. TẠO TASK NẾU ĐIỀU KIỆN ĐƯỢC THỎA MÃN
                if ($shouldCreateTask) {
                    Log::debug('Tạo task cho ngày: ' . $currentDate->toDateString());
                    $dueDate = $currentDate->copy();
                    $task = new Task();
                    $task->schedule_id = $schedule->id;
                    $task->title = $schedule->title . " ngày " . $dueDate->toDateString();
                    $task->description = $schedule->description;

                    // Đặt due_date
                    
                    if (!empty($schedule->fixed_time)) {
                        $dueDate->setTimeFromTimeString($schedule->fixed_time);
                    }
                    $task->task_date = $dueDate;
                    $task->user_id = $schedule->user_id;
                    $task->require_checkin = $schedule->require_checkin;
                    $task->fixed_time = $schedule->fixed_time;
                    $task->status = 'pending';
                    $task->save();
                }

                // Di chuyển sang ngày tiếp theo
                $currentDate->addDay();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return true;
    }
}