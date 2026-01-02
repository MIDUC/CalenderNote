<template>
  <div class="p-4">
    <div class="mb-4">
      <button
        @click="$router.back()"
        class="text-blue-600 hover:text-blue-800 mb-2"
      >
        ← Quay lại
      </button>
      <h1 class="text-2xl font-bold">{{ storyTitle || 'Nhân Vật' }}</h1>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>
    
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      {{ error }}
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="character in characters"
        :key="character.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
        @click="viewCharacter(character.id)"
      >
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xl font-semibold">{{ character.name }}</h2>
          <span
            :class="getRoleBadgeClass(character.role)"
            class="text-xs px-2 py-1 rounded"
          >
            {{ getRoleLabel(character.role) }}
          </span>
        </div>
        
        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ character.description }}</p>
        
        <div v-if="character.user_relationship" class="mt-3 pt-3 border-t">
          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500">Quan hệ:</span>
            <span
              :class="getRelationshipBadgeClass(character.user_relationship.relationship_type)"
              class="text-xs px-2 py-1 rounded"
            >
              {{ getRelationshipLabel(character.user_relationship.relationship_type) }}
            </span>
          </div>
          <div class="mt-1">
            <div class="flex items-center">
              <span class="text-xs text-gray-500 mr-2">Giá trị:</span>
              <div class="flex-1 bg-gray-200 rounded-full h-2">
                <div
                  :class="character.user_relationship.relationship_value >= 0 ? 'bg-green-500' : 'bg-red-500'"
                  class="h-2 rounded-full transition-all"
                  :style="{ width: Math.abs(character.user_relationship.relationship_value) + '%' }"
                ></div>
              </div>
              <span class="text-xs text-gray-600 ml-2">
                {{ character.user_relationship.relationship_value }}
              </span>
            </div>
          </div>
        </div>
        
        <div v-if="character.quests && character.quests.length > 0" class="mt-3 pt-3 border-t">
          <p class="text-xs text-gray-500">
            {{ character.quests.length }} nhiệm vụ có sẵn
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Characters',
  data() {
    return {
      characters: [],
      storyTitle: '',
      loading: true,
      error: null,
    };
  },
  mounted() {
    const storyId = this.$route.params.storyId;
    if (storyId) {
      this.fetchCharacters(storyId);
    }
  },
  methods: {
    async fetchCharacters(storyId) {
      try {
        this.loading = true;
        const response = await axios.get(`/api/characters/story/${storyId}`);
        this.characters = response.data.data || [];
        
        // Get story title
        if (this.characters.length > 0 && this.characters[0].story) {
          this.storyTitle = this.characters[0].story.title;
        }
      } catch (error) {
        this.error = 'Không thể tải danh sách nhân vật.';
        console.error('Error fetching characters:', error);
      } finally {
        this.loading = false;
      }
    },
    viewCharacter(characterId) {
      this.$router.push(`/character-quests/character/${characterId}`);
    },
    getRoleLabel(role) {
      const labels = {
        protagonist: 'Nhân vật chính',
        antagonist: 'Phản diện',
        supporting: 'Phụ',
        mentor: 'Sư phụ',
        love_interest: 'Tình yêu',
        villain: 'Kẻ thù',
        ally: 'Đồng minh',
        rival: 'Đối thủ',
      };
      return labels[role] || role;
    },
    getRoleBadgeClass(role) {
      const classes = {
        protagonist: 'bg-blue-100 text-blue-800',
        antagonist: 'bg-red-100 text-red-800',
        supporting: 'bg-gray-100 text-gray-800',
        mentor: 'bg-purple-100 text-purple-800',
        love_interest: 'bg-pink-100 text-pink-800',
        villain: 'bg-red-200 text-red-900',
        ally: 'bg-green-100 text-green-800',
        rival: 'bg-yellow-100 text-yellow-800',
      };
      return classes[role] || 'bg-gray-100 text-gray-800';
    },
    getRelationshipLabel(type) {
      const labels = {
        neutral: 'Trung lập',
        ally: 'Đồng minh',
        enemy: 'Kẻ thù',
        rival: 'Đối thủ',
        friend: 'Bạn bè',
        master: 'Sư phụ',
      };
      return labels[type] || type;
    },
    getRelationshipBadgeClass(type) {
      const classes = {
        neutral: 'bg-gray-100 text-gray-800',
        ally: 'bg-green-100 text-green-800',
        enemy: 'bg-red-100 text-red-800',
        rival: 'bg-yellow-100 text-yellow-800',
        friend: 'bg-blue-100 text-blue-800',
        master: 'bg-purple-100 text-purple-800',
      };
      return classes[type] || 'bg-gray-100 text-gray-800';
    },
  },
};
</script>

