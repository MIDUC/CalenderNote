import { ref } from 'vue';
import axios from 'axios';
import { useToast } from "vue-toastification";

export function useSchedules() {
    const schedules = ref([]);
    const loading = ref(false);
    const pagination = ref({ page: 1, itemPerPage: 10, total: 0, lastPage: 1 });
    const toast = useToast();
    const token = localStorage.getItem("token");

    // Header chung
    const authHeader = { headers: { Accept: "application/json", Authorization: `Bearer ${token}` } };

    // 1. Lấy danh sách
    const fetchSchedules = async (filters = {}, sort = {}) => {
        loading.value = true;
        try {
            const res = await axios.post(
                `/api/schedule/listing`,
                {
                    filters,
                    sort_by: [sort.sortBy || 'created_at'],
                    sort_direction: [sort.sortDirection || 'desc'],
                    page: pagination.value.page,
                    item_per_page: pagination.value.itemPerPage,
                },
                authHeader
            );
            const data = res.data?.data;
            schedules.value = data?.data || [];
            pagination.value.total = data?.total || 0;
            pagination.value.lastPage = data?.last_page || 1;
        } catch (err) {
            console.error(err);
            toast.error("Không thể tải danh sách lịch.");
        } finally {
            loading.value = false;
        }
    };

    // 2. Thêm mới
    const createSchedule = async (payload) => {
        try {
            await axios.post(`/api/schedule/store`, payload, authHeader);
            toast.success("Thêm mới thành công!");
            return true;
        } catch (error) {
            const msg = error.response?.data?.message || "Lỗi khi thêm mới.";
            toast.error(msg);
            return false;
        }
    };

    // 3. Cập nhật
    const updateSchedule = async (id, payload) => {
        try {
            await axios.put(`/api/schedule/update/${id}`, payload, authHeader);
            toast.success("Cập nhật thành công!");
            return true;
        } catch (error) {
            toast.error("Lỗi khi cập nhật.");
            return false;
        }
    };

    // 4. Xóa / Dừng / Chạy
    const changeStatus = async (id, action) => {
        // action: 'delete', 'pause', 'play'
        // mapping endpoint tương ứng
        let endpoint = action; 
        let method = 'post';
        if (action === 'delete') { method = 'delete'; }
        
        try {
            await axios({
                method: method,
                url: `/api/schedule/${endpoint}/${id}`,
                headers: authHeader.headers
            });
            toast.success("Thao tác thành công!");
            return true;
        } catch (error) {
            toast.error("Có lỗi xảy ra.");
            return false;
        }
    };

    return {
        schedules,
        loading,
        pagination,
        fetchSchedules,
        createSchedule,
        updateSchedule,
        changeStatus
    };
}