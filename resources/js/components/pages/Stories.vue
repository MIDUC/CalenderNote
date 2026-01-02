<template>
  <div class="p-4">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Cốt Truyện</h1>
      <button
        @click="generateStory"
        :disabled="generating"
        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition disabled:bg-gray-400"
      >
        {{ generating ? 'Đang tạo...' : '✨ Tạo Cốt Truyện Mới' }}
      </button>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>
    
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      {{ error }}
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="story in stories"
        :key="story.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
        @click="viewStory(story.id)"
      >
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xl font-semibold">{{ story.title }}</h2>
          <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
            Cấp {{ story.unlock_level }}
          </span>
        </div>
        
        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ story.description }}</p>
        
        <div class="flex items-center justify-between text-sm text-gray-500">
          <span>{{ story.genre }}</span>
          <span>{{ story.total_chapters }} chương</span>
        </div>
        
        <div v-if="story.characters && story.characters.length > 0" class="mt-3 pt-3 border-t">
          <p class="text-xs text-gray-500 mb-1">Nhân vật:</p>
          <div class="flex flex-wrap gap-1">
            <span
              v-for="char in story.characters.slice(0, 3)"
              :key="char.id"
              class="text-xs bg-gray-100 px-2 py-1 rounded"
            >
              {{ char.name }}
            </span>
            <span v-if="story.characters.length > 3" class="text-xs text-gray-400">
              +{{ story.characters.length - 3 }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useToast } from 'vue-toastification';

export default {
  name: 'Stories',
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      stories: [],
      loading: true,
      error: null,
      generating: false,
    };
  },
  mounted() {
    this.fetchStories();
  },
  methods: {
    async fetchStories() {
      try {
        this.loading = true;
        const response = await axios.get('/api/stories/list');
        this.stories = response.data.data || [];
      } catch (error) {
        this.error = 'Không thể tải danh sách cốt truyện.';
        console.error('Error fetching stories:', error);
      } finally {
        this.loading = false;
      }
    },
    viewStory(storyId) {
      this.$router.push(`/characters/story/${storyId}`);
    },
    async generateStory() {
      try {
        this.generating = true;
        const response = await axios.post('/api/story-generator/generate');
        this.toast.success('Đã tạo cốt truyện mới thành công!');
        await this.fetchStories();
      } catch (error) {
        const message = error.response?.data?.message || 'Không thể tạo cốt truyện.';
        this.toast.error(message);
        console.error('Error generating story:', error);
      } finally {
        this.generating = false;
      }
    },
  },
};
</script>

