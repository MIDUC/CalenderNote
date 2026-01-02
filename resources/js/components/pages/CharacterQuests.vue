<template>
  <div class="p-4">
    <div class="mb-4">
      <button
        @click="$router.back()"
        class="text-blue-600 hover:text-blue-800 mb-2"
      >
        ← Quay lại
      </button>
      <h1 class="text-2xl font-bold">Nhiệm Vụ - {{ characterName }}</h1>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>
    
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      {{ error }}
    </div>
    
    <div v-else class="space-y-4">
      <div
        v-for="quest in quests"
        :key="quest.id"
        class="bg-white rounded-lg shadow-md p-6"
      >
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1">
            <h3 class="text-lg font-semibold mb-2">{{ quest.title }}</h3>
            <p class="text-gray-600 text-sm mb-3">{{ quest.description }}</p>
          </div>
          <span
            v-if="quest.user_status === 'in_progress'"
            class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded"
          >
            Đang làm
          </span>
          <span
            v-else-if="quest.user_status === 'completed'"
            class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded"
          >
            Hoàn thành
          </span>
          <span
            v-else-if="quest.user_status === 'failed'"
            class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded"
          >
            Thất bại
          </span>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
          <div>
            <span class="text-gray-500">Yêu cầu cấp:</span>
            <span class="ml-2 font-semibold">{{ quest.level_required }}</span>
          </div>
          <div>
            <span class="text-gray-500">Loại:</span>
            <span class="ml-2 font-semibold">{{ getQuestTypeLabel(quest.type) }}</span>
          </div>
        </div>
        
        <div v-if="quest.user_status === 'in_progress'" class="mb-4">
          <div class="flex items-center justify-between text-sm mb-1">
            <span class="text-gray-500">Tiến độ:</span>
            <span>{{ quest.user_progress }} / {{ quest.target_count }}</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-blue-500 h-2 rounded-full transition-all"
              :style="{ width: (quest.user_progress / quest.target_count) * 100 + '%' }"
            ></div>
          </div>
          <div v-if="quest.due_date" class="mt-2 text-xs text-gray-500">
            Hạn chót: {{ formatDate(quest.due_date) }}
          </div>
        </div>
        
        <div class="bg-gray-50 p-3 rounded mb-4">
          <p class="text-sm font-semibold mb-2">Phần thưởng:</p>
          <div class="flex flex-wrap gap-2">
            <span v-if="quest.xp_reward > 0" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
              +{{ quest.xp_reward }} XP
            </span>
            <span v-if="quest.currency_reward > 0" class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">
              +{{ quest.currency_reward }} 💎
            </span>
            <span v-if="quest.item_reward" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
              {{ quest.item_reward.name }} x{{ quest.item_reward_quantity }}
            </span>
          </div>
        </div>
        
        <div v-if="quest.relationship_effects" class="bg-yellow-50 p-3 rounded mb-4">
          <p class="text-sm font-semibold mb-2">Ảnh hưởng quan hệ:</p>
          <div class="text-xs space-y-1">
            <div v-if="quest.relationship_effects.enemy_ids && quest.relationship_effects.enemy_ids.length > 0">
              <span class="text-red-600">⚠️ Sẽ trở thành kẻ thù với:</span>
              <span class="ml-1">{{ quest.relationship_effects.enemy_ids.length }} nhân vật</span>
            </div>
            <div v-if="quest.relationship_effects.ally_ids && quest.relationship_effects.ally_ids.length > 0">
              <span class="text-green-600">✓ Sẽ trở thành đồng minh với:</span>
              <span class="ml-1">{{ quest.relationship_effects.ally_ids.length }} nhân vật</span>
            </div>
          </div>
        </div>
        
        <div class="flex gap-2">
          <button
            v-if="quest.user_status === 'available'"
            @click="acceptQuest(quest.id)"
            class="flex-1 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"
          >
            Nhận Nhiệm Vụ
          </button>
          <button
            v-else-if="quest.user_status === 'in_progress'"
            @click="completeQuest(quest)"
            class="flex-1 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition"
          >
            Hoàn Thành
          </button>
          <button
            v-else
            disabled
            class="flex-1 bg-gray-300 text-gray-600 px-4 py-2 rounded cursor-not-allowed"
          >
            {{ quest.user_status === 'completed' ? 'Đã Hoàn Thành' : 'Đã Thất Bại' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CharacterQuests',
  data() {
    return {
      quests: [],
      characterName: '',
      loading: true,
      error: null,
    };
  },
  mounted() {
    const characterId = this.$route.params.characterId;
    if (characterId) {
      this.fetchQuests(characterId);
    }
  },
  methods: {
    async fetchQuests(characterId) {
      try {
        this.loading = true;
        const response = await axios.get(`/api/character-quests/character/${characterId}`);
        this.quests = response.data.data || [];
        
        if (this.quests.length > 0 && this.quests[0].character) {
          this.characterName = this.quests[0].character.name;
        }
      } catch (error) {
        this.error = 'Không thể tải danh sách nhiệm vụ.';
        console.error('Error fetching quests:', error);
      } finally {
        this.loading = false;
      }
    },
    async acceptQuest(questId) {
      try {
        const response = await axios.post(`/api/character-quests/accept/${questId}`);
        this.$toast?.success(response.data.data?.message || 'Đã nhận nhiệm vụ thành công!');
        await this.fetchQuests(this.$route.params.characterId);
      } catch (error) {
        const message = error.response?.data?.message || 'Không thể nhận nhiệm vụ.';
        this.$toast?.error(message);
        console.error('Error accepting quest:', error);
      }
    },
    async completeQuest(quest) {
      // Find user quest ID
      const userQuest = await this.getUserQuest(quest.id);
      if (!userQuest) {
        this.$toast?.error('Không tìm thấy nhiệm vụ của bạn.');
        return;
      }
      
      try {
        const response = await axios.post(`/api/character-quests/complete/${userQuest.id}`);
        this.$toast?.success(response.data.data?.message || 'Hoàn thành nhiệm vụ thành công!');
        await this.fetchQuests(this.$route.params.characterId);
      } catch (error) {
        const message = error.response?.data?.message || 'Không thể hoàn thành nhiệm vụ.';
        this.$toast?.error(message);
        console.error('Error completing quest:', error);
      }
    },
    async getUserQuest(questId) {
      try {
        const response = await axios.get('/api/character-quests/my-quests');
        const userQuests = response.data.data || [];
        return userQuests.find(uq => uq.character_quest_id === questId);
      } catch (error) {
        console.error('Error fetching user quests:', error);
        return null;
      }
    },
    getQuestTypeLabel(type) {
      const labels = {
        task: 'Nhiệm vụ',
        exercise: 'Luyện tập',
        collect: 'Thu thập',
        defeat: 'Tiêu diệt',
        explore: 'Khám phá',
        social: 'Xã hội',
      };
      return labels[type] || type;
    },
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('vi-VN');
    },
  },
};
</script>

