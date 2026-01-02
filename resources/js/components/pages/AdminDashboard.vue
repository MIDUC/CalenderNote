<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm text-gray-500 mb-1">Tổng số User</h3>
        <p class="text-2xl font-bold">{{ stats.total_users || 0 }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm text-gray-500 mb-1">Tổng số Task</h3>
        <p class="text-2xl font-bold">{{ stats.total_tasks || 0 }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm text-gray-500 mb-1">User hoạt động (7 ngày)</h3>
        <p class="text-2xl font-bold">{{ stats.active_users || 0 }}</p>
      </div>
    </div>
    
    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow mb-4">
      <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'px-4 py-2 text-sm font-medium border-b-2 transition',
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>
    </div>
    
    <!-- Tab Content -->
    <div class="bg-white rounded-lg shadow p-4">
      <!-- Users Tab -->
      <div v-if="activeTab === 'users'">
        <div class="mb-4">
          <input
            v-model="userSearch"
            @input="fetchUsers"
            type="text"
            placeholder="Tìm kiếm user..."
            class="w-full px-4 py-2 border rounded-lg"
          />
        </div>
        <div v-if="usersLoading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        </div>
        <div v-else>
          <table class="w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left p-2">ID</th>
                <th class="text-left p-2">Tên</th>
                <th class="text-left p-2">Email</th>
                <th class="text-left p-2">Role</th>
                <th class="text-left p-2">Level</th>
                <th class="text-left p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users.data" :key="user.id" class="border-b">
                <td class="p-2">{{ user.id }}</td>
                <td class="p-2">{{ user.name }}</td>
                <td class="p-2">{{ user.email }}</td>
                <td class="p-2">
                  <span :class="user.role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'" class="px-2 py-1 rounded text-xs">
                    {{ user.role }}
                  </span>
                </td>
                <td class="p-2">{{ user.level || 1 }}</td>
                <td class="p-2">
                  <button @click="viewUser(user.id)" class="text-blue-500 hover:text-blue-700 mr-2">Xem</button>
                  <button @click="editUser(user)" class="text-green-500 hover:text-green-700 mr-2">Sửa</button>
                  <button v-if="user.role !== 'admin'" @click="deleteUser(user.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="mt-4 flex justify-between">
            <button @click="fetchUsers(users.current_page - 1)" :disabled="users.current_page === 1" class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
              Trước
            </button>
            <span>Trang {{ users.current_page }} / {{ users.last_page }}</span>
            <button @click="fetchUsers(users.current_page + 1)" :disabled="users.current_page === users.last_page" class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
              Sau
            </button>
          </div>
        </div>
      </div>
      
      <!-- Levels Tab -->
      <div v-if="activeTab === 'levels'">
        <div class="mb-4 flex gap-2 border-b">
          <button
            @click="levelSubTab = 'names'"
            :class="['px-4 py-2', levelSubTab === 'names' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500']"
          >
            Level Names
          </button>
          <button
            @click="levelSubTab = 'xp'"
            :class="['px-4 py-2', levelSubTab === 'xp' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500']"
          >
            XP Requirements
          </button>
        </div>
        
        <!-- Level Names -->
        <div v-if="levelSubTab === 'names'">
          <button @click="addNewLevel" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            + Thêm Level Name
          </button>
          <table class="w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left p-2">Level Min</th>
                <th class="text-left p-2">Level Max</th>
                <th class="text-left p-2">Tên</th>
                <th class="text-left p-2">Mô tả</th>
                <th class="text-left p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="level in levels" :key="level.id" class="border-b">
                <td class="p-2">{{ level.level_min }}</td>
                <td class="p-2">{{ level.level_max }}</td>
                <td class="p-2">{{ level.name }}</td>
                <td class="p-2">{{ level.description || '-' }}</td>
                <td class="p-2">
                  <button @click="editLevel(level)" class="text-green-500 hover:text-green-700 mr-2">Sửa</button>
                  <button @click="deleteLevel(level.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- XP Requirements -->
        <div v-if="levelSubTab === 'xp'">
          <button @click="addNewXpRequirement" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            + Thêm XP Requirement
          </button>
          <table class="w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left p-2">Level</th>
                <th class="text-left p-2">XP Required</th>
                <th class="text-left p-2">Mô tả</th>
                <th class="text-left p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="req in xpRequirements" :key="req.id" class="border-b">
                <td class="p-2">{{ req.level }}</td>
                <td class="p-2">{{ req.xp_required }}</td>
                <td class="p-2">{{ req.description || '-' }}</td>
                <td class="p-2">
                  <button @click="editXpRequirement(req)" class="text-green-500 hover:text-green-700 mr-2">Sửa</button>
                  <button @click="deleteXpRequirement(req.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Items Tab -->
      <div v-if="activeTab === 'items'">
        <button @click="addNewItem" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
          + Thêm Item
        </button>
        <div class="mb-4">
          <input
            v-model="itemSearch"
            @input="fetchItems"
            type="text"
            placeholder="Tìm kiếm item..."
            class="w-full px-4 py-2 border rounded-lg"
          />
        </div>
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">ID</th>
              <th class="text-left p-2">Tên</th>
              <th class="text-left p-2">Type</th>
              <th class="text-left p-2">Price</th>
              <th class="text-left p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items.data" :key="item.id" class="border-b">
              <td class="p-2">{{ item.id }}</td>
              <td class="p-2">{{ item.name }}</td>
              <td class="p-2">{{ item.type }}</td>
              <td class="p-2">{{ item.price }}</td>
              <td class="p-2">
                <button @click="editItem(item)" class="text-green-500 hover:text-green-700 mr-2">Sửa</button>
                <button @click="deleteItem(item.id)" class="text-red-500 hover:text-red-700">Xóa</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Monsters Tab -->
      <div v-if="activeTab === 'monsters'">
        <button @click="addNewMonster" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
          + Thêm Monster
        </button>
        <div v-if="monstersLoading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        </div>
        <div v-else>
          <table class="w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left p-2">ID</th>
                <th class="text-left p-2">Icon</th>
                <th class="text-left p-2">Tên</th>
                <th class="text-left p-2">Level</th>
                <th class="text-left p-2">HP</th>
                <th class="text-left p-2">Attack</th>
                <th class="text-left p-2">Defense</th>
                <th class="text-left p-2">Boss</th>
                <th class="text-left p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="monster in monsters" :key="monster.id" class="border-b">
                <td class="p-2">{{ monster.id }}</td>
                <td class="p-2 text-2xl">{{ monster.icon || '👹' }}</td>
                <td class="p-2">{{ monster.name }}</td>
                <td class="p-2">{{ monster.level }}</td>
                <td class="p-2">{{ monster.hp }}/{{ monster.max_hp }}</td>
                <td class="p-2">{{ monster.attack }}</td>
                <td class="p-2">{{ monster.defense }}</td>
                <td class="p-2">
                  <span v-if="monster.is_boss" class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Boss</span>
                  <span v-else class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Normal</span>
                </td>
                <td class="p-2">
                  <button @click="editMonster(monster)" class="text-green-500 hover:text-green-700 mr-2">Sửa</button>
                  <button @click="deleteMonster(monster.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- NPCs Tab -->
      <div v-if="activeTab === 'npcs'">
        <button @click="addNewNPC" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
          + Thêm NPC
        </button>
        <div v-if="npcsLoading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        </div>
        <div v-else>
          <table class="w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left p-2">ID</th>
                <th class="text-left p-2">Icon</th>
                <th class="text-left p-2">Tên</th>
                <th class="text-left p-2">Type</th>
                <th class="text-left p-2">Level Required</th>
                <th class="text-left p-2">Active</th>
                <th class="text-left p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="npc in npcs" :key="npc.id" class="border-b">
                <td class="p-2">{{ npc.id }}</td>
                <td class="p-2 text-2xl">{{ npc.icon || '👤' }}</td>
                <td class="p-2">{{ npc.name }}</td>
                <td class="p-2">
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ npc.type }}</span>
                </td>
                <td class="p-2">{{ npc.level_required }}</td>
                <td class="p-2">
                  <span v-if="npc.is_active" class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>
                  <span v-else class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Inactive</span>
                </td>
                <td class="p-2">
                  <button @click="editNPC(npc)" class="text-green-500 hover:text-green-700 mr-2">Sửa</button>
                  <button @click="deleteNPC(npc.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Tasks Tab -->
      <div v-if="activeTab === 'tasks'">
        <div class="mb-4 flex gap-2">
          <input
            v-model="taskSearch"
            @input="fetchTasks(1)"
            type="text"
            placeholder="Tìm kiếm task..."
            class="flex-1 px-4 py-2 border rounded-lg"
          />
          <select v-model="selectedUserId" @change="fetchTasks(1)" class="px-4 py-2 border rounded-lg">
            <option value="">Tất cả users</option>
            <option v-for="user in allUsers" :key="user.id" :value="user.id">{{ user.name }}</option>
          </select>
          <select v-model="taskStatus" @change="fetchTasks(1)" class="px-4 py-2 border rounded-lg">
            <option value="">Tất cả status</option>
            <option value="pending">Pending</option>
            <option value="done">Done</option>
            <option value="failed">Failed</option>
          </select>
        </div>
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">ID</th>
              <th class="text-left p-2">User</th>
              <th class="text-left p-2">Title</th>
              <th class="text-left p-2">Status</th>
              <th class="text-left p-2">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in tasks.data" :key="task.id" class="border-b">
              <td class="p-2">{{ task.id }}</td>
              <td class="p-2">{{ task.user?.name || '-' }}</td>
              <td class="p-2">{{ task.title }}</td>
              <td class="p-2">{{ task.status }}</td>
              <td class="p-2">{{ task.task_date }}</td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4 flex justify-between">
          <button @click="fetchTasks(tasks.current_page - 1)" :disabled="tasks.current_page === 1" class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
            Trước
          </button>
          <span>Trang {{ tasks.current_page }} / {{ tasks.last_page }}</span>
          <button @click="fetchTasks(tasks.current_page + 1)" :disabled="tasks.current_page === tasks.last_page" class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
            Sau
          </button>
        </div>
      </div>
      
      <!-- Schedules Tab -->
      <div v-if="activeTab === 'schedules'">
        <div class="mb-4 flex gap-2">
          <input
            v-model="scheduleSearch"
            @input="fetchSchedules(1)"
            type="text"
            placeholder="Tìm kiếm schedule..."
            class="flex-1 px-4 py-2 border rounded-lg"
          />
          <select v-model="selectedScheduleUserId" @change="fetchSchedules(1)" class="px-4 py-2 border rounded-lg">
            <option value="">Tất cả users</option>
            <option v-for="user in allUsers" :key="user.id" :value="user.id">{{ user.name }}</option>
          </select>
        </div>
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">ID</th>
              <th class="text-left p-2">User</th>
              <th class="text-left p-2">Title</th>
              <th class="text-left p-2">Start Date</th>
              <th class="text-left p-2">End Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="schedule in schedules.data" :key="schedule.id" class="border-b">
              <td class="p-2">{{ schedule.id }}</td>
              <td class="p-2">{{ schedule.user?.name || '-' }}</td>
              <td class="p-2">{{ schedule.title }}</td>
              <td class="p-2">{{ schedule.start_date }}</td>
              <td class="p-2">{{ schedule.end_date || '-' }}</td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4 flex justify-between">
          <button @click="fetchSchedules(schedules.current_page - 1)" :disabled="schedules.current_page === 1" class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
            Trước
          </button>
          <span>Trang {{ schedules.current_page }} / {{ schedules.last_page }}</span>
          <button @click="fetchSchedules(schedules.current_page + 1)" :disabled="schedules.current_page === schedules.last_page" class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
            Sau
          </button>
        </div>
      </div>
    </div>
    
    <!-- Edit Level Modal -->
    <div v-if="showLevelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">{{ editingLevel?.id ? 'Sửa' : 'Thêm' }} Level Name</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Level Min</label>
            <input v-model.number="levelForm.level_min" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Level Max</label>
            <input v-model.number="levelForm.level_max" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Tên</label>
            <input v-model="levelForm.name" type="text" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Mô tả</label>
            <textarea v-model="levelForm.description" class="w-full px-4 py-2 border rounded-lg" rows="3"></textarea>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="showLevelModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Hủy</button>
          <button @click="saveLevel" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Lưu</button>
        </div>
      </div>
    </div>
    
    <!-- Edit XP Requirement Modal -->
    <div v-if="showXpRequirementModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">{{ editingXpRequirement?.id ? 'Sửa' : 'Thêm' }} XP Requirement</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Level</label>
            <input v-model.number="xpRequirementForm.level" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">XP Required</label>
            <input v-model.number="xpRequirementForm.xp_required" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
            <p class="text-xs text-gray-500 mt-1">XP cần để lên từ level {{ xpRequirementForm.level - 1 }} lên level {{ xpRequirementForm.level }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Mô tả</label>
            <textarea v-model="xpRequirementForm.description" class="w-full px-4 py-2 border rounded-lg" rows="3"></textarea>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="showXpRequirementModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Hủy</button>
          <button @click="saveXpRequirement" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Lưu</button>
        </div>
      </div>
    </div>
    
    <!-- Edit User Modal -->
    <div v-if="showUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Sửa User: {{ editingUser?.name }}</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Tên</label>
            <input v-model="userForm.name" type="text" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input v-model="userForm.email" type="email" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Role</label>
            <select v-model="userForm.role" class="w-full px-4 py-2 border rounded-lg">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Level</label>
              <input v-model.number="userForm.level" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">XP</label>
              <input v-model.number="userForm.exp" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Currency</label>
            <input v-model.number="userForm.currency" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">HP</label>
              <input v-model.number="userForm.hp" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Max HP</label>
              <input v-model.number="userForm.max_hp" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Attack</label>
              <input v-model.number="userForm.attack" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Defense</label>
              <input v-model.number="userForm.defense" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Online EXP mỗi 5s</label>
            <input v-model.number="userForm.online_exp_per_5s" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            <p class="text-xs text-gray-500 mt-1">Số điểm kinh nghiệm tự động cộng mỗi 5 giây khi user online</p>
          </div>
          <div class="border-t pt-4">
            <h3 class="font-semibold mb-2">Đổi mật khẩu</h3>
            <div>
              <label class="block text-sm font-medium mb-1">Mật khẩu mới</label>
              <input v-model="userForm.newPassword" type="password" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Xác nhận mật khẩu</label>
              <input v-model="userForm.newPasswordConfirmation" type="password" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="showUserModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Hủy</button>
          <button @click="saveUser" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Lưu</button>
        </div>
      </div>
    </div>
    
    <!-- Edit Item Modal -->
    <div v-if="showItemModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">{{ editingItem?.id ? 'Sửa' : 'Thêm' }} Item: {{ editingItem?.name || 'Mới' }}</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Tên</label>
            <input v-model="itemForm.name" type="text" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Mô tả</label>
            <textarea v-model="itemForm.description" class="w-full px-4 py-2 border rounded-lg" rows="3"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Type</label>
              <input v-model="itemForm.type" type="text" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Price</label>
              <input v-model.number="itemForm.price" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Rarity</label>
              <input v-model="itemForm.rarity" type="text" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Grade</label>
              <input v-model.number="itemForm.grade" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Slot</label>
            <select v-model="itemForm.slot" class="w-full px-4 py-2 border rounded-lg">
              <option value="">Không có</option>
              <option value="weapon">Weapon</option>
              <option value="armor">Armor</option>
              <option value="accessory">Accessory</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Effects</label>
            <div class="border rounded-lg p-4">
              <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                  <label class="block text-xs text-gray-600 mb-1">Attack</label>
                  <input v-model.number="itemForm.effects.attack" type="number" min="0" class="w-full px-3 py-2 border rounded" placeholder="0" />
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">Defense</label>
                  <input v-model.number="itemForm.effects.defense" type="number" min="0" class="w-full px-3 py-2 border rounded" placeholder="0" />
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">HP</label>
                  <input v-model.number="itemForm.effects.hp" type="number" min="0" class="w-full px-3 py-2 border rounded" placeholder="0" />
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">Max HP</label>
                  <input v-model.number="itemForm.effects.max_hp" type="number" min="0" class="w-full px-3 py-2 border rounded" placeholder="0" />
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">XP Bonus (%)</label>
                  <input v-model.number="itemForm.effects.xp_bonus" type="number" min="0" class="w-full px-3 py-2 border rounded" placeholder="0" />
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">Currency Bonus (%)</label>
                  <input v-model.number="itemForm.effects.currency_bonus" type="number" min="0" class="w-full px-3 py-2 border rounded" placeholder="0" />
                </div>
              </div>
              <button @click="clearEffects" type="button" class="text-xs text-red-500 hover:text-red-700">Xóa tất cả</button>
            </div>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="showItemModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Hủy</button>
          <button @click="saveItem" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Lưu</button>
        </div>
      </div>
    </div>
    
    <!-- Edit Monster Modal -->
    <div v-if="showMonsterModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">{{ editingMonster?.id ? 'Sửa' : 'Thêm' }} Monster: {{ editingMonster?.name || 'Mới' }}</h2>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Tên</label>
              <input v-model="monsterForm.name" type="text" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Icon (Emoji)</label>
              <input v-model="monsterForm.icon" type="text" class="w-full px-4 py-2 border rounded-lg" placeholder="👹" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Mô tả</label>
            <textarea v-model="monsterForm.description" class="w-full px-4 py-2 border rounded-lg" rows="2"></textarea>
          </div>
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Level</label>
              <input v-model.number="monsterForm.level" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">HP</label>
              <input v-model.number="monsterForm.hp" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Max HP</label>
              <input v-model.number="monsterForm.max_hp" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Attack</label>
              <input v-model.number="monsterForm.attack" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Defense</label>
              <input v-model.number="monsterForm.defense" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">XP Reward</label>
              <input v-model.number="monsterForm.xp_reward" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Currency Reward</label>
              <input v-model.number="monsterForm.currency_reward" type="number" min="0" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Rarity (1-5)</label>
              <input v-model.number="monsterForm.rarity" type="number" min="1" max="5" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">
                <input v-model="monsterForm.is_boss" type="checkbox" class="mr-2" />
                Is Boss
              </label>
            </div>
          </div>
          <div class="border-t pt-4">
            <h3 class="font-semibold mb-2">Tỉ lệ</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-gray-600 mb-1">Critical Rate (0-1)</label>
                <input v-model.number="monsterForm.critical_rate" type="number" step="0.01" min="0" max="1" class="w-full px-3 py-2 border rounded" />
              </div>
              <div>
                <label class="block text-xs text-gray-600 mb-1">Critical Damage Multiplier</label>
                <input v-model.number="monsterForm.critical_damage_multiplier" type="number" step="0.1" min="1" max="10" class="w-full px-3 py-2 border rounded" />
              </div>
              <div>
                <label class="block text-xs text-gray-600 mb-1">Dodge Rate (0-1)</label>
                <input v-model.number="monsterForm.dodge_rate" type="number" step="0.01" min="0" max="1" class="w-full px-3 py-2 border rounded" />
              </div>
              <div>
                <label class="block text-xs text-gray-600 mb-1">Drop Rate Multiplier</label>
                <input v-model.number="monsterForm.drop_rate_multiplier" type="number" step="0.1" min="0" max="10" class="w-full px-3 py-2 border rounded" />
              </div>
              <div>
                <label class="block text-xs text-gray-600 mb-1">Rare Drop Rate (0-1)</label>
                <input v-model.number="monsterForm.rare_drop_rate" type="number" step="0.01" min="0" max="1" class="w-full px-3 py-2 border rounded" />
              </div>
              <div>
                <label class="block text-xs text-gray-600 mb-1">Encounter Rate (0-1)</label>
                <input v-model.number="monsterForm.encounter_rate" type="number" step="0.01" min="0" max="1" class="w-full px-3 py-2 border rounded" />
              </div>
              <div>
                <label class="block text-xs text-gray-600 mb-1">Status Effect Resistance (0-1)</label>
                <input v-model.number="monsterForm.status_effect_resistance" type="number" step="0.01" min="0" max="1" class="w-full px-3 py-2 border rounded" />
              </div>
            </div>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="showMonsterModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Hủy</button>
          <button @click="saveMonster" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Lưu</button>
        </div>
      </div>
    </div>
    
    <!-- Edit NPC Modal -->
    <div v-if="showNPCModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">{{ editingNPC?.id ? 'Sửa' : 'Thêm' }} NPC: {{ editingNPC?.name || 'Mới' }}</h2>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Tên</label>
              <input v-model="npcForm.name" type="text" class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Icon (Emoji)</label>
              <input v-model="npcForm.icon" type="text" class="w-full px-4 py-2 border rounded-lg" placeholder="👤" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Mô tả</label>
            <textarea v-model="npcForm.description" class="w-full px-4 py-2 border rounded-lg" rows="2"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Type</label>
              <select v-model="npcForm.type" class="w-full px-4 py-2 border rounded-lg">
                <option value="merchant">Merchant</option>
                <option value="quest_giver">Quest Giver</option>
                <option value="trainer">Trainer</option>
                <option value="guard">Guard</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Level Required</label>
              <input v-model.number="npcForm.level_required" type="number" min="1" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">
              <input v-model="npcForm.is_active" type="checkbox" class="mr-2" />
              Is Active
            </label>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Dialogue (JSON Array)</label>
            <textarea v-model="dialogueText" class="w-full px-4 py-2 border rounded-lg font-mono text-sm" rows="4" placeholder='["Hello!", "How can I help you?"]'></textarea>
            <p class="text-xs text-gray-500 mt-1">Nhập mảng JSON các câu thoại</p>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="showNPCModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Hủy</button>
          <button @click="saveNPC" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Lưu</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useToast } from 'vue-toastification';

export default {
  name: 'AdminDashboard',
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      stats: {},
      activeTab: 'users',
      tabs: [
        { id: 'users', label: 'Users' },
        { id: 'levels', label: 'Levels' },
        { id: 'items', label: 'Items' },
        { id: 'monsters', label: 'Monsters' },
        { id: 'npcs', label: 'NPCs' },
        { id: 'story-rules', label: 'Story Rules' },
        { id: 'tasks', label: 'Tasks' },
        { id: 'schedules', label: 'Schedules' },
      ],
      users: { data: [], current_page: 1, last_page: 1 },
      usersLoading: false,
      userSearch: '',
      levels: [],
      items: { data: [], current_page: 1, last_page: 1 },
      itemSearch: '',
      tasks: { data: [], current_page: 1, last_page: 1 },
      selectedUserId: '',
      taskStatus: '',
      taskSearch: '',
      schedules: { data: [], current_page: 1, last_page: 1 },
      selectedScheduleUserId: '',
      scheduleSearch: '',
      allUsers: [],
      levelSubTab: 'names',
      xpRequirements: [],
      showLevelModal: false,
      showXpRequirementModal: false,
      showUserModal: false,
      showItemModal: false,
      editingUser: null,
      editingItem: null,
      editingLevel: null,
      editingXpRequirement: null,
      userForm: {
        name: '',
        email: '',
        role: 'user',
        level: 1,
        exp: 0,
        currency: 0,
        hp: 100,
        max_hp: 100,
        attack: 10,
        defense: 10,
        online_exp_per_5s: 1,
        newPassword: '',
        newPasswordConfirmation: '',
      },
      itemForm: {
        name: '',
        description: '',
        type: '',
        price: 0,
        rarity: '',
        grade: 1,
        slot: '',
        effects: {
          attack: 0,
          defense: 0,
          hp: 0,
          max_hp: 0,
          xp_bonus: 0,
          currency_bonus: 0,
        },
      },
      levelForm: {
        level_min: 1,
        level_max: 10,
        name: '',
        description: '',
      },
      xpRequirementForm: {
        level: 1,
        xp_required: 100,
        description: '',
      },
      monsters: [],
      monstersLoading: false,
      showMonsterModal: false,
      editingMonster: null,
      monsterForm: {
        name: '',
        description: '',
        icon: '',
        level: 1,
        hp: 100,
        max_hp: 100,
        attack: 10,
        defense: 5,
        xp_reward: 10,
        currency_reward: 5,
        drop_items: null,
        rarity: 1,
        is_boss: false,
        critical_rate: 0.05,
        critical_damage_multiplier: 2.0,
        dodge_rate: 0.05,
        drop_rate_multiplier: 1.0,
        rare_drop_rate: 0.01,
        encounter_rate: 1.0,
        status_effect_resistance: 0.0,
      },
      npcs: [],
      npcsLoading: false,
      showNPCModal: false,
      editingNPC: null,
      npcForm: {
        name: '',
        description: '',
        icon: '',
        type: 'quest_giver',
        dialogue: [],
        level_required: 1,
        is_active: true,
      },
      storyRules: [],
      storyRulesLoading: false,
      showStoryRuleModal: false,
      editingStoryRule: null,
      storyRuleForm: {
        story_id: null,
        name: '',
        description: '',
        trigger_type: 'kill_monster',
        trigger_conditions: {},
        required_count: 1,
        target_character_id: null,
        relationship_value_change: 0,
        relationship_type_change: null,
        relationship_threshold: 100,
        has_penalty: false,
        penalty_effects: {},
        penalty_due_days: null,
        is_cumulative: true,
        reset_on_complete: false,
        is_active: true,
        order: 0,
      },
      allStories: [],
      allCharacters: [],
    };
  },
  computed: {
    dialogueText: {
      get() {
        return JSON.stringify(this.npcForm.dialogue || [], null, 2);
      },
      set(value) {
        try {
          this.npcForm.dialogue = JSON.parse(value);
        } catch (e) {
          // Invalid JSON, keep current value
        }
      },
    },
  },
  mounted() {
    this.fetchDashboard();
    this.fetchUsers();
    this.fetchLevels();
    this.fetchXpRequirements();
    this.fetchItems();
    this.fetchAllUsers();
    this.fetchMonsters();
    this.fetchNPCs();
    this.fetchStoryRules();
    this.fetchStories();
    this.fetchCharacters();
  },
  methods: {
    async fetchDashboard() {
      try {
        const response = await axios.get('/api/admin/dashboard');
        this.stats = response.data.data;
      } catch (error) {
        this.toast.error('Không thể tải dashboard.');
        console.error('Error fetching dashboard:', error);
      }
    },
    async fetchUsers(page = 1) {
      try {
        this.usersLoading = true;
        const response = await axios.get('/api/admin/users', {
          params: { page, search: this.userSearch, per_page: 15 },
        });
        this.users = response.data.data;
      } catch (error) {
        this.toast.error('Không thể tải danh sách users.');
        console.error('Error fetching users:', error);
      } finally {
        this.usersLoading = false;
      }
    },
    async fetchLevels() {
      try {
        const response = await axios.get('/api/admin/levels');
        this.levels = response.data.data;
      } catch (error) {
        this.toast.error('Không thể tải danh sách levels.');
        console.error('Error fetching levels:', error);
      }
    },
    async fetchXpRequirements() {
      try {
        const response = await axios.get('/api/admin/level-xp-requirements');
        this.xpRequirements = response.data.data;
      } catch (error) {
        this.toast.error('Không thể tải danh sách XP requirements.');
        console.error('Error fetching XP requirements:', error);
      }
    },
    async fetchItems(page = 1) {
      try {
        const response = await axios.get('/api/admin/items', {
          params: { page, search: this.itemSearch, per_page: 15 },
        });
        this.items = response.data.data;
      } catch (error) {
        this.toast.error('Không thể tải danh sách items.');
        console.error('Error fetching items:', error);
      }
    },
    async fetchTasks(page = 1) {
      try {
        const response = await axios.get('/api/admin/tasks', {
          params: { 
            page,
            user_id: this.selectedUserId, 
            status: this.taskStatus, 
            search: this.taskSearch,
            per_page: 50 
          },
        });
        this.tasks = response.data.data;
      } catch (error) {
        this.toast.error('Không thể tải danh sách tasks.');
        console.error('Error fetching tasks:', error);
      }
    },
    async fetchSchedules(page = 1) {
      try {
        const response = await axios.get('/api/admin/schedules', {
          params: { 
            page,
            user_id: this.selectedScheduleUserId, 
            search: this.scheduleSearch,
            per_page: 50 
          },
        });
        this.schedules = response.data.data;
      } catch (error) {
        this.toast.error('Không thể tải danh sách schedules.');
        console.error('Error fetching schedules:', error);
      }
    },
    async fetchAllUsers() {
      try {
        const response = await axios.get('/api/admin/users', { params: { per_page: 100 } });
        this.allUsers = response.data.data.data || [];
      } catch (error) {
        console.error('Error fetching all users:', error);
      }
    },
    viewUser(userId) {
      this.$router.push(`/admin/users/${userId}`);
    },
    editUser(user) {
      this.editingUser = user;
      this.userForm = {
        name: user.name || '',
        email: user.email || '',
        role: user.role || 'user',
        level: user.level || 1,
        exp: user.exp || 0,
        currency: user.currency || 0,
        hp: user.hp || 100,
        max_hp: user.max_hp || 100,
        attack: user.attack || 10,
        defense: user.defense || 10,
        online_exp_per_5s: user.online_exp_per_5s || 1,
        newPassword: '',
        newPasswordConfirmation: '',
      };
      this.showUserModal = true;
    },
    async saveUser() {
      try {
        const updateData = {
          name: this.userForm.name,
          email: this.userForm.email,
          role: this.userForm.role,
          level: this.userForm.level,
          exp: this.userForm.exp,
          currency: this.userForm.currency,
          hp: this.userForm.hp,
          max_hp: this.userForm.max_hp,
          attack: this.userForm.attack,
          defense: this.userForm.defense,
        };
        
        await axios.put(`/api/admin/users/${this.editingUser.id}`, updateData);
        
        // Change password if provided
        if (this.userForm.newPassword && this.userForm.newPassword === this.userForm.newPasswordConfirmation) {
          await axios.post(`/api/admin/users/${this.editingUser.id}/change-password`, {
            password: this.userForm.newPassword,
            password_confirmation: this.userForm.newPasswordConfirmation,
          });
        } else if (this.userForm.newPassword) {
          this.toast.error('Mật khẩu xác nhận không khớp.');
          return;
        }
        
        this.toast.success('Đã cập nhật user thành công.');
        this.showUserModal = false;
        this.fetchUsers();
      } catch (error) {
        const message = error.response?.data?.message || 'Không thể cập nhật user.';
        this.toast.error(message);
        console.error('Error updating user:', error);
      }
    },
    async deleteUser(userId) {
      if (!confirm('Bạn có chắc muốn xóa user này?')) return;
      try {
        await axios.delete(`/api/admin/users/${userId}`);
        this.toast.success('Đã xóa user thành công.');
        this.fetchUsers();
      } catch (error) {
        this.toast.error('Không thể xóa user.');
        console.error('Error deleting user:', error);
      }
    },
    addNewLevel() {
      this.editingLevel = { id: null };
      this.levelForm = {
        level_min: 1,
        level_max: 10,
        name: '',
        description: '',
      };
      this.showLevelModal = true;
    },
    editLevel(level) {
      this.editingLevel = level;
      this.levelForm = {
        level_min: level.level_min || 1,
        level_max: level.level_max || 10,
        name: level.name || '',
        description: level.description || '',
      };
      this.showLevelModal = true;
    },
    async saveLevel() {
      try {
        const updateData = {
          level_min: this.levelForm.level_min,
          level_max: this.levelForm.level_max,
          name: this.levelForm.name,
          description: this.levelForm.description,
        };
        
        if (this.editingLevel.id) {
          await axios.put(`/api/admin/levels/${this.editingLevel.id}`, updateData);
        } else {
          await axios.post('/api/admin/levels', updateData);
        }
        
        this.toast.success(`Đã ${this.editingLevel.id ? 'cập nhật' : 'tạo'} level thành công.`);
        this.showLevelModal = false;
        this.fetchLevels();
      } catch (error) {
        const message = error.response?.data?.message || 'Không thể lưu level.';
        this.toast.error(message);
        console.error('Error saving level:', error);
      }
    },
    addNewXpRequirement() {
      this.editingXpRequirement = { id: null };
      this.xpRequirementForm = {
        level: 1,
        xp_required: 100,
        description: '',
      };
      this.showXpRequirementModal = true;
    },
    editXpRequirement(req) {
      this.editingXpRequirement = req;
      this.xpRequirementForm = {
        level: req.level || 1,
        xp_required: req.xp_required || 100,
        description: req.description || '',
      };
      this.showXpRequirementModal = true;
    },
    async saveXpRequirement() {
      try {
        const updateData = {
          level: this.xpRequirementForm.level,
          xp_required: this.xpRequirementForm.xp_required,
          description: this.xpRequirementForm.description,
        };
        
        if (this.editingXpRequirement.id) {
          await axios.put(`/api/admin/level-xp-requirements/${this.editingXpRequirement.id}`, updateData);
        } else {
          await axios.post('/api/admin/level-xp-requirements', updateData);
        }
        
        this.toast.success(`Đã ${this.editingXpRequirement.id ? 'cập nhật' : 'tạo'} XP requirement thành công.`);
        this.showXpRequirementModal = false;
        this.fetchXpRequirements();
      } catch (error) {
        const message = error.response?.data?.message || 'Không thể lưu XP requirement.';
        this.toast.error(message);
        console.error('Error saving XP requirement:', error);
      }
    },
    async deleteXpRequirement(id) {
      if (!confirm('Bạn có chắc muốn xóa XP requirement này?')) return;
      try {
        await axios.delete(`/api/admin/level-xp-requirements/${id}`);
        this.toast.success('Đã xóa XP requirement thành công.');
        this.fetchXpRequirements();
      } catch (error) {
        this.toast.error('Không thể xóa XP requirement.');
        console.error('Error deleting XP requirement:', error);
      }
    },
    async deleteLevel(levelId) {
      if (!confirm('Bạn có chắc muốn xóa level này?')) return;
      try {
        await axios.delete(`/api/admin/levels/${levelId}`);
        this.toast.success('Đã xóa level thành công.');
        this.fetchLevels();
      } catch (error) {
        this.toast.error('Không thể xóa level.');
        console.error('Error deleting level:', error);
      }
    },
    editItem(item) {
      this.editingItem = item;
      let effects = {};
      if (item.effects) {
        if (typeof item.effects === 'string') {
          try {
            effects = JSON.parse(item.effects);
          } catch (e) {
            effects = {};
          }
        } else {
          effects = item.effects;
        }
      }
      
      this.itemForm = {
        name: item.name || '',
        description: item.description || '',
        type: item.type || '',
        price: item.price || 0,
        rarity: item.rarity || '',
        grade: item.grade || 1,
        slot: item.slot || '',
        effects: {
          attack: effects.attack || 0,
          defense: effects.defense || 0,
          hp: effects.hp || 0,
          max_hp: effects.max_hp || 0,
          xp_bonus: effects.xp_bonus || 0,
          currency_bonus: effects.currency_bonus || 0,
        },
      };
      this.showItemModal = true;
    },
    async saveItem() {
      try {
        // Remove zero values from effects
        const effects = {};
        Object.keys(this.itemForm.effects).forEach(key => {
          if (this.itemForm.effects[key] && this.itemForm.effects[key] !== 0) {
            effects[key] = this.itemForm.effects[key];
          }
        });
        
        const updateData = {
          name: this.itemForm.name,
          description: this.itemForm.description,
          type: this.itemForm.type,
          price: this.itemForm.price,
          rarity: this.itemForm.rarity,
          grade: this.itemForm.grade,
          slot: this.itemForm.slot || null,
          effects: Object.keys(effects).length > 0 ? effects : null,
        };
        
        if (this.editingItem.id) {
          await axios.put(`/api/admin/items/${this.editingItem.id}`, updateData);
        } else {
          await axios.post('/api/admin/items', updateData);
        }
        
        this.toast.success(`Đã ${this.editingItem.id ? 'cập nhật' : 'tạo'} item thành công.`);
        this.showItemModal = false;
        this.fetchItems();
      } catch (error) {
        const message = error.response?.data?.message || 'Không thể lưu item.';
        this.toast.error(message);
        console.error('Error saving item:', error);
      }
    },
    clearEffects() {
      this.itemForm.effects = {
        attack: 0,
        defense: 0,
        hp: 0,
        max_hp: 0,
        xp_bonus: 0,
        currency_bonus: 0,
      };
    },
    async deleteItem(itemId) {
      if (!confirm('Bạn có chắc muốn xóa item này?')) return;
      try {
        await axios.delete(`/api/admin/items/${itemId}`);
        this.toast.success('Đã xóa item thành công.');
        this.fetchItems();
      } catch (error) {
        this.toast.error('Không thể xóa item.');
        console.error('Error deleting item:', error);
      }
    },
    addNewItem() {
      this.editingItem = { id: null };
      this.itemForm = {
        name: '',
        description: '',
        type: '',
        price: 0,
        rarity: '',
        grade: 1,
        slot: '',
        effects: {
          attack: 0,
          defense: 0,
          hp: 0,
          max_hp: 0,
          xp_bonus: 0,
          currency_bonus: 0,
        },
      };
      this.showItemModal = true;
    },
    async fetchMonsters() {
      try {
        this.monstersLoading = true;
        const response = await axios.get('/api/admin/monsters');
        this.monsters = response.data.data || [];
      } catch (error) {
        this.toast.error('Không thể tải danh sách monsters.');
        console.error('Error fetching monsters:', error);
      } finally {
        this.monstersLoading = false;
      }
    },
    editMonster(monster) {
      this.editingMonster = { ...monster };
      this.monsterForm = {
        name: monster.name || '',
        description: monster.description || '',
        icon: monster.icon || '',
        level: monster.level || 1,
        hp: monster.hp || 100,
        max_hp: monster.max_hp || 100,
        attack: monster.attack || 10,
        defense: monster.defense || 5,
        xp_reward: monster.xp_reward || 10,
        currency_reward: monster.currency_reward || 5,
        drop_items: monster.drop_items || null,
        rarity: monster.rarity || 1,
        is_boss: monster.is_boss || false,
        critical_rate: monster.critical_rate || 0.05,
        critical_damage_multiplier: monster.critical_damage_multiplier || 2.0,
        dodge_rate: monster.dodge_rate || 0.05,
        drop_rate_multiplier: monster.drop_rate_multiplier || 1.0,
        rare_drop_rate: monster.rare_drop_rate || 0.01,
        encounter_rate: monster.encounter_rate || 1.0,
        status_effect_resistance: monster.status_effect_resistance || 0.0,
      };
      this.showMonsterModal = true;
    },
    addNewMonster() {
      this.editingMonster = { id: null };
      this.monsterForm = {
        name: '',
        description: '',
        icon: '',
        level: 1,
        hp: 100,
        max_hp: 100,
        attack: 10,
        defense: 5,
        xp_reward: 10,
        currency_reward: 5,
        drop_items: null,
        rarity: 1,
        is_boss: false,
        critical_rate: 0.05,
        critical_damage_multiplier: 2.0,
        dodge_rate: 0.05,
        drop_rate_multiplier: 1.0,
        rare_drop_rate: 0.01,
        encounter_rate: 1.0,
        status_effect_resistance: 0.0,
      };
      this.showMonsterModal = true;
    },
    async saveMonster() {
      try {
        const updateData = { ...this.monsterForm };
        
        if (this.editingMonster.id) {
          await axios.put(`/api/admin/monsters/${this.editingMonster.id}`, updateData);
        } else {
          await axios.post('/api/admin/monsters', updateData);
        }
        
        this.toast.success(`Đã ${this.editingMonster.id ? 'cập nhật' : 'tạo'} monster thành công.`);
        this.showMonsterModal = false;
        this.fetchMonsters();
      } catch (error) {
        const message = error.response?.data?.message || 'Không thể lưu monster.';
        this.toast.error(message);
        console.error('Error saving monster:', error);
      }
    },
    async deleteMonster(id) {
      if (!confirm('Bạn có chắc muốn xóa monster này?')) return;
      
      try {
        await axios.delete(`/api/admin/monsters/${id}`);
        this.toast.success('Đã xóa monster thành công.');
        this.fetchMonsters();
      } catch (error) {
        this.toast.error('Không thể xóa monster.');
        console.error('Error deleting monster:', error);
      }
    },
    async fetchNPCs() {
      try {
        this.npcsLoading = true;
        const response = await axios.get('/api/admin/npcs');
        this.npcs = response.data.data || [];
      } catch (error) {
        this.toast.error('Không thể tải danh sách NPCs.');
        console.error('Error fetching NPCs:', error);
      } finally {
        this.npcsLoading = false;
      }
    },
    editNPC(npc) {
      this.editingNPC = { ...npc };
      this.npcForm = {
        name: npc.name || '',
        description: npc.description || '',
        icon: npc.icon || '',
        type: npc.type || 'quest_giver',
        dialogue: npc.dialogue || [],
        level_required: npc.level_required || 1,
        is_active: npc.is_active !== undefined ? npc.is_active : true,
      };
      this.showNPCModal = true;
    },
    addNewNPC() {
      this.editingNPC = { id: null };
      this.npcForm = {
        name: '',
        description: '',
        icon: '',
        type: 'quest_giver',
        dialogue: [],
        level_required: 1,
        is_active: true,
      };
      this.showNPCModal = true;
    },
    async saveNPC() {
      try {
        const updateData = {
          ...this.npcForm,
          dialogue: this.npcForm.dialogue,
        };
        
        if (this.editingNPC.id) {
          await axios.put(`/api/admin/npcs/${this.editingNPC.id}`, updateData);
        } else {
          await axios.post('/api/admin/npcs', updateData);
        }
        
        this.toast.success(`Đã ${this.editingNPC.id ? 'cập nhật' : 'tạo'} NPC thành công.`);
        this.showNPCModal = false;
        this.fetchNPCs();
      } catch (error) {
        const message = error.response?.data?.message || 'Không thể lưu NPC.';
        this.toast.error(message);
        console.error('Error saving NPC:', error);
      }
    },
    async deleteNPC(id) {
      if (!confirm('Bạn có chắc muốn xóa NPC này?')) return;
      
      try {
        await axios.delete(`/api/admin/npcs/${id}`);
        this.toast.success('Đã xóa NPC thành công.');
        this.fetchNPCs();
      } catch (error) {
        this.toast.error('Không thể xóa NPC.');
        console.error('Error deleting NPC:', error);
      }
    },
  },
  watch: {
    activeTab(newTab) {
      if (newTab === 'tasks') {
        this.fetchTasks();
      } else if (newTab === 'schedules') {
        this.fetchSchedules();
      } else if (newTab === 'levels') {
        this.fetchLevels();
        this.fetchXpRequirements();
      } else if (newTab === 'monsters') {
        this.fetchMonsters();
      } else if (newTab === 'npcs') {
        this.fetchNPCs();
      } else if (newTab === 'story-rules') {
        this.fetchStoryRules();
      }
    },
    levelSubTab(newTab) {
      if (newTab === 'xp') {
        this.fetchXpRequirements();
      }
    },
  },
};
</script>

