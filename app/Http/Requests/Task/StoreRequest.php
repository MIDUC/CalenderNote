<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\Base\StoreRequest as BaseStoreRequest;

class StoreRequest extends BaseStoreRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // 1. Tiêu đề bắt buộc, tối đa 255 ký tự
            'title' => [
                'required',
                'string',
                'max:255'
            ],

            // 2. Ngày thực hiện bắt buộc, phải đúng định dạng ngày (Y-m-d)
            'task_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            // 3. Quan trọng: schedule_id có thể NULL (Task tự do)
            // Nếu có giá trị thì phải tồn tại trong bảng schedules
            'schedule_id' => [
                'nullable',
                'integer',
                'exists:schedules,id'
            ],

            // 4. Giờ cố định: Có thể null, nếu có phải đúng định dạng giờ phút (H:i)
            'fixed_time' => [
                'nullable',
                'date_format:H:i', 
            ],

            // 5. Các trường phụ khác
            'description' => [
                'nullable',
                'string'
            ],

            'status' => [
                'nullable',
                'string',
                'in:pending,done,failed,blocked' // Chỉ chấp nhận các trạng thái này
            ],

            'require_checkin' => [
                'nullable',
                'boolean' // Chấp nhận true, false, 1, 0, "1", "0"
            ],
            
            'note' => [
                'nullable', 
                'string'
            ],
        ];
    }

    // Tùy chọn: Việt hóa thông báo lỗi
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tên công việc.',
            'title.max' => 'Tên công việc không được quá 255 ký tự.',
            'task_date.required' => 'Vui lòng chọn ngày thực hiện.',
            'task_date.date_format' => 'Định dạng ngày không hợp lệ (năm-tháng-ngày).',
            'schedule_id.exists' => 'Lịch trình liên kết không tồn tại.',
            'fixed_time.date_format' => 'Định dạng giờ không hợp lệ (Giờ:Phút).',
            'status.in' => 'Trạng thái công việc không hợp lệ.',
        ];
    }
}