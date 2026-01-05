<template>
    <div class="cultivation-home-dark">
        <!-- Background character image -->
        <div class="background-character">
            <!-- Background character sẽ được render bằng CSS -->
        </div>

        <!-- Top Section: Player Info -->
        <div class="top-section">
            <div class="player-info">
                <div class="player-title">Người chơi</div>
                <div class="player-level">
                    Cảnh giới:
                    {{ cultivationRealm }}
                </div>
                <div class="player-resources">
                    <span class="resource-item">
                        <span class="resource-label">Linh Thạch:</span>
                        <span class="resource-value">{{
                            user?.currency || 0
                        }}</span>
                    </span>
                </div>
            </div>
            <button class="exit-btn" @click="handleExit" title="Thoát">
                <span class="exit-icon">✕</span>
            </button>
        </div>

        <!-- Top Horizontal Navigation Menu -->
        <div class="top-nav-menu">
            <div class="nav-menu-container">
                <div class="nav-menu-scroll">
                    <router-link
                        v-for="feature in features"
                        :key="feature.path"
                        :to="feature.path"
                        class="nav-menu-item"
                    >
                        <div class="nav-menu-icon">{{ feature.icon }}</div>
                        <div class="nav-menu-label">{{ feature.label }}</div>
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Center: Character Display -->
            <div class="center-character">
                <div class="character-wrapper">
                    <!-- Background character (large, translucent) -->
                    <div class="character-bg-large">
                        <!-- Background character sẽ được render bằng CSS -->
                    </div>

                    <!-- Main character (chibi) -->
                    <div class="character-main-display">
                        <CharacterDisplay
                            :outfit="currentOutfit"
                            :is-cultivating="isCultivating"
                            :show-outfit-button="false"
                            :show-info="false"
                        />
                    </div>

                    <!-- Companion & Spirit Beast Info -->
                    <div class="character-companions">
                        <div class="companion-item">
                            <span class="companion-label">Đạo lữ:</span>
                            <span class="companion-value">{{
                                companion || "[Chưa có]"
                            }}</span>
                        </div>
                        <div class="companion-item">
                            <span class="companion-label">Linh thú:</span>
                            <span class="companion-value">{{
                                spiritBeast || "[Chưa có]"
                            }}</span>
                        </div>
                    </div>

                    <!-- Character Stats -->
                    <div class="character-stats">
                        <div class="stat-item">
                            <span class="stat-label">Cảnh giới:</span>
                            <span class="stat-value">{{
                                cultivationRealm
                            }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Huyền lực:</span>
                            <span class="stat-value">{{
                                (user?.attack || 0) * 100 +
                                (user?.max_hp || 0) * 10
                            }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label stat-special"
                                >Huyết Mạch</span
                            >
                            <span class="stat-value">{{ "Không" }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Nguyên Lực:</span>
                            <div class="stat-progress">
                                <div
                                    class="progress-bar"
                                    :style="{
                                        width: `${nguyenLucPercentage}%`,
                                    }"
                                ></div>
                                <span class="progress-text"
                                    >{{ currentNguyenLuc }}/{{
                                        nextLevelExp
                                    }}</span
                                >
                            </div>
                            <!-- Breakthrough Button -->
                            <button
                                v-if="canBreakthrough"
                                @click="handleBreakthrough"
                                :disabled="breakthroughLoading"
                                class="breakthrough-btn"
                            >
                                <span class="breakthrough-icon">⚡</span>
                                <span class="breakthrough-text">Đột Phá</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cultivation Status Text -->
                    <div class="cultivation-status">
                        <div class="status-text-main">
                            Đang nhập định, vận chuyển huyền công theo chu
                            thiên...
                        </div>
                        <div class="status-quote">
                            Linh khí hội tụ, nguyên thần an định, thiên địa cảm
                            ứng, đạo vận luân hồi.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Quick Actions & Characters -->
            <div class="right-nav">
                <div class="quick-actions">
                    <router-link to="/tasks" class="action-btn">
                        <span class="action-icon">📋</span>
                        <span class="action-label">Nhiệm Vụ</span>
                    </router-link>
                    <router-link to="/battles" class="action-btn">
                        <span class="action-icon">⚔️</span>
                        <span class="action-label">Chiến Đấu</span>
                    </router-link>
                    <router-link to="/shop" class="action-btn">
                        <span class="action-icon">🛒</span>
                        <span class="action-label">Cửa Hàng</span>
                    </router-link>
                </div>

                <!-- Characters Section -->
                <div class="characters-section">
                    <h3 class="characters-title">Nhân Vật</h3>
                    <div v-if="charactersLoading" class="characters-loading">
                        <div class="loading-spinner"></div>
                    </div>
                    <div
                        v-else-if="characters.length === 0"
                        class="characters-empty"
                    >
                        <p>Chưa có nhân vật nào</p>
                        <router-link to="/stories" class="characters-link">
                            Xem cốt truyện →
                        </router-link>
                    </div>
                    <div v-else class="characters-list">
                        <div
                            v-for="rel in characters"
                            :key="rel.id"
                            class="character-item"
                            @click="viewCharacter(rel.character_id)"
                        >
                            <div class="character-avatar">
                                <span class="character-icon">{{
                                    rel.character?.avatar || "👤"
                                }}</span>
                            </div>
                            <div class="character-info">
                                <div class="character-name">
                                    {{ rel.character?.name || "Nhân vật" }}
                                </div>
                                <div class="character-relationship">
                                    <span
                                        :class="
                                            getRelationshipBadgeClass(
                                                rel.relationship_type
                                            )
                                        "
                                        class="relationship-badge"
                                    >
                                        {{
                                            getRelationshipLabel(
                                                rel.relationship_type
                                            )
                                        }}
                                    </span>
                                    <span class="relationship-value">{{
                                        rel.relationship_value
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom: Event Log -->
        <div class="event-log">
            <div class="event-tabs">
                <button
                    v-for="tab in eventTabs"
                    :key="tab.id"
                    @click="activeEventTab = tab.id"
                    class="event-tab"
                    :class="{ active: activeEventTab === tab.id }"
                >
                    {{ tab.label }} ({{ tab.count }})
                </button>
            </div>
            <div class="event-messages">
                <div
                    v-for="(event, index) in filteredEvents"
                    :key="index"
                    class="event-message"
                >
                    <span class="event-tag">[{{ eventTag }}]</span>
                    {{ event.message }}
                </div>
                <div v-if="filteredEvents.length === 0" class="event-empty">
                    Chưa có sự kiện nào
                </div>
            </div>
            <button class="scroll-to-bottom" @click="scrollToBottom">
                Về cuối
            </button>
        </div>

        <!-- Outfit Selection Modal -->
        <div
            v-if="showOutfitModal"
            class="outfit-modal-overlay"
            @click="showOutfitModal = false"
        >
            <div class="outfit-modal" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Thay Đổi Trang Phục</h3>
                    <button
                        @click="showOutfitModal = false"
                        class="modal-close"
                    >
                        ×
                    </button>
                </div>
                <div class="modal-content">
                    <!-- Unequip option -->
                    <div class="outfit-option" @click="equipOutfit(null)">
                        <div class="outfit-icon">👤</div>
                        <div class="outfit-info">
                            <div class="outfit-name">Trang phục mặc định</div>
                        </div>
                        <div
                            v-if="!currentOutfit"
                            class="outfit-badge equipped"
                        >
                            Đang mặc
                        </div>
                    </div>

                    <!-- User's outfits -->
                    <div
                        v-for="outfit in outfits"
                        :key="outfit.id"
                        class="outfit-option"
                        :class="{ equipped: currentOutfit?.id === outfit.id }"
                        @click="equipOutfit(outfit.id)"
                    >
                        <div class="outfit-icon">{{ outfit.icon || "👕" }}</div>
                        <div class="outfit-info">
                            <div class="outfit-name">{{ outfit.name }}</div>
                            <div class="outfit-category">
                                {{ outfit.category }}
                            </div>
                        </div>
                        <div
                            v-if="currentOutfit?.id === outfit.id"
                            class="outfit-badge equipped"
                        >
                            Đang mặc
                        </div>
                    </div>

                    <div v-if="outfits.length === 0" class="empty-outfits">
                        <p>Bạn chưa có trang phục nào.</p>
                        <router-link to="/shop" class="cultivation-link"
                            >Mua trang phục →</router-link
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import { useToast } from "vue-toastification";
import { useFormat } from "../../composables";
import { taskService } from "../../services";
import { getTodayDate } from "../../utils/helpers";
import ProfileCard from "../common/ProfileCard.vue";
import LoadingSpinner from "../common/LoadingSpinner.vue";
import EmptyState from "../common/EmptyState.vue";
import StatusBadge from "../common/StatusBadge.vue";
import CharacterDisplay from "../common/CharacterDisplay.vue";
import api from "../../api";

const toast = useToast();
const { formatDate } = useFormat();

const user = ref(null);
const tasks = ref([]);
const loadingTasks = ref(true);
const currentOutfit = ref(null);
const outfits = ref([]);
const showOutfitModal = ref(false);
const isCultivating = ref(true); // Default to cultivating
const companion = ref(null);
const spiritBeast = ref(null);
const characters = ref([]);
const charactersLoading = ref(false);
const activeEventTab = ref("all");
const eventTabs = ref([
    { id: "all", label: "Tất cả", count: 0 },
    { id: "world", label: "Thế Sự", count: 0 },
    { id: "cultivation", label: "Tu Vi", count: 0 },
    { id: "harvest", label: "Thu Hoạch", count: 0 },
    { id: "recovery", label: "Hồi Phục", count: 0 },
]);
const events = ref([
    {
        type: "recovery",
        message:
            "Thiên địa linh khí dung nhập vào thể phách, hồi phục 2 sinh lực.",
    },
    {
        type: "recovery",
        message:
            "Thiên địa linh khí dung nhập vào thể phách, hồi phục 2 sinh lực.",
    },
    {
        type: "recovery",
        message:
            "Thiên địa linh khí dung nhập vào thể phách, hồi phục 2 sinh lực.",
    },
]);

const allFeatures = [
    { path: "/character", label: "Bản Thân", icon: "👤" },
    { path: "/tasks", label: "Nhiệm Vụ", icon: "⭐" },
    { path: "/calendar", label: "Động Phủ", icon: "🏠" },
    { path: "/inventory", label: "Túi", icon: "🎒" },
    { path: "/daily-rewards", label: "Tu Luyện", icon: "🧘" },
    { path: "/shop", label: "Của Hàng", icon: "🛒" },
    { path: "/battles", label: "Chiến Đấu", icon: "🐉" },
    { path: "/companion", label: "Đạo Lữ", icon: "👥" },
    { path: "/equipment", label: "Pháp Bảo", icon: "⚔️" },
    { path: "/shop", label: "Luyện Đan", icon: "💊" },
    { path: "/equipment", label: "Luyện Khí", icon: "🔨" },
    { path: "/formation", label: "Trận Pháp", icon: "🌀" },
    { path: "/achievements", label: "Thành Tựu", icon: "🏆" },
    { path: "/personal-info", label: "Thông Tin", icon: "🎨" },
    { path: "/stories", label: "Tiên Duyên", icon: "✨" },
    { path: "/npcs", label: "Đạo Quán", icon: "🏛️" },
    { path: "/leaderboard", label: "Tu Hội", icon: "👥" },
    { path: "/admin", label: "Admin", icon: "⚙️", adminOnly: true },
];

const features = computed(() => {
    if (user.value?.role === "admin") {
        return allFeatures;
    }
    return allFeatures.filter((f) => !f.adminOnly);
});

// Watch user level and fetch level name when it changes
watch(
    () => user.value?.level,
    (newLevel) => {
        if (newLevel) {
            fetchLevelName(newLevel);
        }
    },
    { immediate: true }
);

const fetchUser = async () => {
    try {
        const res = await api.get("/me");
        // Ensure gamification data is preserved
        user.value = res.data;
    } catch (error) {
        console.error("Error fetching user:", error);
    }
};

const loadTasks = async () => {
    loadingTasks.value = true;
    try {
        const res = await taskService.list({
            filters: { status: "pending", task_date: getTodayDate() },
            sort_by: ["created_at"],
            sort_direction: ["desc"],
            page: 1,
            item_per_page: 4,
        });
        tasks.value = res?.data?.data || [];
    } catch (error) {
        console.error("Lỗi tải tasks:", error);
    } finally {
        loadingTasks.value = false;
    }
};

// ✅ Cập nhật trạng thái task
const updateTaskStatus = async (taskId, status) => {
    try {
        const res = await taskService.update(taskId, { status });
        if (status === "done") {
            toast.success("Đã hoàn thành task!");

            // Show streak info if available
            if (res?.data?.streak) {
                const streak = res.data.streak;
                if (streak.current_streak > 1) {
                    toast.info(`🔥 Chuỗi ngày: ${streak.current_streak} ngày!`);
                }
                if (streak.milestone_reward) {
                    toast.success(
                        `🎉 Đạt milestone ${streak.current_streak} ngày! +${streak.milestone_reward.xp} XP, +${streak.milestone_reward.currency} 💎`
                    );
                }
                if (
                    streak.streak_bonus_xp > 0 ||
                    streak.streak_bonus_currency > 0
                ) {
                    toast.info(
                        `✨ Bonus chuỗi: +${streak.streak_bonus_xp} XP, +${streak.streak_bonus_currency} 💎`
                    );
                }
            }

            // Show reward info
            if (res?.data?.reward) {
                const reward = res.data.reward;
                if (reward.level_result?.leveled_up) {
                    toast.success(
                        `🎉 Lên cấp ${reward.level_result.new_level}!`
                    );

                    // Show generated story notification
                    if (res?.data?.generated_story) {
                        const story = res.data.generated_story;
                        toast.info(
                            `📖 Cốt truyện mới đã được tạo: "${story.title}"!`,
                            {
                                timeout: 5000,
                            }
                        );
                    }
                }
            }
        } else {
            toast.success("Đã đánh dấu task thất bại!");
        }
        await loadTasks(); // Reload tasks
        await fetchUser(); // Reload user to update streak
    } catch (error) {
        console.error("Lỗi cập nhật task:", error);
        toast.error("Không thể cập nhật task. Vui lòng thử lại!");
    }
};

const fetchOutfit = async () => {
    try {
        if (user.value?.current_outfit_id) {
            const res = await api.get(`/outfits/`);
            const allOutfits = res.data.data || [];
            currentOutfit.value =
                allOutfits.find((o) => o.id === user.value.current_outfit_id) ||
                null;
        } else {
            currentOutfit.value = null;
        }
    } catch (error) {
        console.error("Error fetching outfit:", error);
    }
};

const fetchMyOutfits = async () => {
    try {
        const res = await api.get("/outfits/my");
        outfits.value = res.data.data || [];
    } catch (error) {
        console.error("Error fetching my outfits:", error);
    }
};

const fetchCharacters = async () => {
    try {
        charactersLoading.value = true;
        const res = await api.get("/characters/relationships");
        characters.value = res.data.data || [];
    } catch (error) {
        console.error("Error fetching characters:", error);
        characters.value = [];
    } finally {
        charactersLoading.value = false;
    }
};

const getRelationshipLabel = (type) => {
    const labels = {
        neutral: "Trung lập",
        ally: "Đồng minh",
        enemy: "Kẻ thù",
        rival: "Đối thủ",
        friend: "Bạn bè",
        master: "Sư phụ",
    };
    return labels[type] || type;
};

const getRelationshipBadgeClass = (type) => {
    const classes = {
        neutral: "badge-neutral",
        ally: "badge-ally",
        enemy: "badge-enemy",
        rival: "badge-rival",
        friend: "badge-friend",
        master: "badge-master",
    };
    return classes[type] || "badge-neutral";
};

const viewCharacter = (characterId) => {
    // Find the story ID from the character
    const relationship = characters.value.find(
        (rel) => rel.character_id === characterId
    );
    if (relationship?.character?.story_id) {
        window.location.href = `/characters/story/${relationship.character.story_id}`;
    }
};

const hpPercentage = computed(() => {
    if (!user.value) return 0;
    const hp = user.value.hp || 100;
    const maxHp = user.value.max_hp || 100;
    return maxHp > 0 ? Math.min(100, (hp / maxHp) * 100) : 0;
});

const formatNumber = (num) => {
    return new Intl.NumberFormat("vi-VN").format(num);
};

const nextLevelExp = computed(() => {
    // Priority 1: Get from gamification data if available
    if (user.value?.gamification?.next_level_exp) {
        return user.value.gamification.next_level_exp;
    }
    // Priority 2: If gamification data exists but next_level_exp is missing, calculate it
    // This shouldn't happen if API is correct, but just in case
    if (user.value?.level) {
        // Try to fetch from API if we don't have it
        // For now, use a reasonable default based on level
        // Default: 100 for level 1, then increase by 50-100 per level
        const level = user.value.level || 1;
        return 100 + (level - 1) * 50; // Level 1: 100, Level 2: 150, Level 3: 200, etc.
    }
    return 100; // Final fallback
});

const currentNguyenLuc = computed(() => {
    return user.value?.exp || 0;
});

const nguyenLucPercentage = computed(() => {
    const current = currentNguyenLuc.value;
    const needed = nextLevelExp.value;
    if (needed <= 0) return 0;
    return Math.min(100, (current / needed) * 100);
});

const canBreakthrough = computed(() => {
    // Check from gamification data if available
    if (user.value?.gamification?.can_breakthrough !== undefined) {
        return user.value.gamification.can_breakthrough;
    }
    // Fallback: check if current exp >= next level exp
    return currentNguyenLuc.value >= nextLevelExp.value;
});

// Calculate cultivation realm from level
// Cache for level names
const levelNameCache = ref({});
const cultivationRealm = ref("Phàm cảnh");

// Fetch level name from database
const fetchLevelName = async (level) => {
    if (!level || level <= 0) {
        cultivationRealm.value = "Phàm cảnh";
        return;
    }

    // Check cache first
    if (levelNameCache.value[level]) {
        cultivationRealm.value = levelNameCache.value[level];
        return;
    }

    try {
        const response = await api.get("/level-name", {
            params: { level },
        });
        if (response.data?.level_name) {
            levelNameCache.value[level] = response.data.level_name;
            cultivationRealm.value = response.data.level_name;
        } else {
            cultivationRealm.value = "Phàm cảnh";
        }
    } catch (error) {
        console.error("Error fetching level name:", error);
        cultivationRealm.value = "Phàm cảnh";
    }
};

// Watch user level and fetch level name
watch(
    () => user.value?.level,
    (newLevel) => {
        if (newLevel) {
            fetchLevelName(newLevel);
        }
    },
    { immediate: true }
);

const breakthroughLoading = ref(false);

const handleBreakthrough = async () => {
    if (breakthroughLoading.value || !canBreakthrough.value) return;

    breakthroughLoading.value = true;
    try {
        const response = await api.post("/breakthrough");
        if (response.data && response.data.user) {
            // Update user data
            user.value = { ...response.data.user };

            // Show success message
            if (response.data.breakthrough_result?.leveled_up) {
                toast.success(
                    `🎉 Đột phá thành công! Lên cấp ${response.data.breakthrough_result.new_level}!`
                );
            }
        }
    } catch (error) {
        console.error("Error performing breakthrough:", error);
        if (error.response?.data?.message) {
            toast.error(error.response.data.message);
        } else {
            toast.error("Không thể đột phá. Vui lòng thử lại!");
        }
    } finally {
        breakthroughLoading.value = false;
    }
};

const equipOutfit = async (outfitId) => {
    try {
        if (outfitId) {
            await api.post(`/outfits/${outfitId}/equip`);
        } else {
            await api.post("/outfits/unequip");
        }
        await fetchUser();
        await fetchOutfit();
        showOutfitModal.value = false;
        toast.success("Đã thay đổi trang phục!");
    } catch (error) {
        toast.error("Không thể thay đổi trang phục.");
        console.error("Error equipping outfit:", error);
    }
};

const filteredEvents = computed(() => {
    if (activeEventTab.value === "all") {
        return events.value;
    }
    return events.value.filter((e) => e.type === activeEventTab.value);
});

const eventTag = computed(() => {
    const tagMap = {
        recovery: "Hồi Phục",
        world: "Thế Sự",
        cultivation: "Tu Vi",
        harvest: "Thu Hoạch",
    };
    return tagMap[activeEventTab.value] || "Sự Kiện";
});

const scrollToBottom = () => {
    const eventMessages = document.querySelector(".event-messages");
    if (eventMessages) {
        eventMessages.scrollTop = eventMessages.scrollHeight;
    }
};

const handleExit = () => {
    // Handle exit action - can navigate to login or close app
    if (confirm("Bạn có muốn thoát không?")) {
        window.location.href = "/logout"; // or router.push('/login')
    }
};

let onlineExpInterval = null;

onMounted(() => {
    fetchUser();
    loadTasks();
    fetchOutfit();
    fetchMyOutfits();
    fetchCharacters();

    // Start online exp timer - add exp every 5 seconds
    onlineExpInterval = setInterval(async () => {
        try {
            const response = await api.post("/online-exp");
            if (response.data && response.data.user) {
                // Replace user data completely to ensure gamification data is included
                // This ensures Vue reactivity works correctly
                user.value = { ...response.data.user };
            }
        } catch (error) {
            console.error("Error adding online exp:", error);
            // Don't show error to user, just log it
        }
    }, 5000); // 5 seconds
});

onUnmounted(() => {
    if (onlineExpInterval) {
        clearInterval(onlineExpInterval);
        onlineExpInterval = null;
    }
});
</script>

<style scoped>
.cultivation-home-dark {
    position: relative;
    min-height: 100vh;
    background: #1a1a2e;
    color: #fff;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.background-character {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
    opacity: 0.15;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
}

.background-character::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url("/images/character-background.svg");
    background-size: cover;
    background-position: center;
    opacity: 0.2;
}

/* Top Section */
.top-section {
    position: relative;
    z-index: 10;
    background: rgba(22, 33, 62, 0.8);
    backdrop-filter: blur(10px);
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}

.player-info {
    flex: 1;
}

.player-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.5rem;
}

.player-level {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0.5rem;
}

.player-resources {
    display: flex;
    gap: 1.5rem;
    font-size: 0.85rem;
}

.resource-item {
    display: flex;
    gap: 0.5rem;
}

.resource-label {
    color: rgba(255, 255, 255, 0.7);
}

.resource-value {
    color: #4a9eff;
    font-weight: 600;
}

.exit-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    font-size: 1.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.exit-btn:hover {
    background: rgba(255, 77, 77, 0.3);
    border-color: rgba(255, 77, 77, 0.5);
    transform: scale(1.1);
}

.exit-icon {
    font-weight: bold;
    line-height: 1;
}

/* Top Horizontal Navigation Menu */
.top-nav-menu {
    position: relative;
    z-index: 10;
    background: rgba(22, 33, 62, 0.8);
    backdrop-filter: blur(10px);
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    padding: 0.75rem 1rem;
    width: 100%;
}

.nav-menu-container {
    position: relative;
    overflow: hidden;
    width: 100%;
}

.nav-menu-scroll {
    display: flex;
    flex-direction: row;
    gap: 0.5rem;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
    padding: 0.25rem 0;
    width: 100%;
}

.nav-menu-scroll::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.nav-menu-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 0.75rem;
    min-width: 70px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    transition: all 0.3s;
    cursor: pointer;
    flex-shrink: 0;
}

.nav-menu-item:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.nav-menu-icon {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

.nav-menu-label {
    font-size: 0.7rem;
    text-align: center;
    white-space: nowrap;
}

/* Main Content */
.main-content {
    position: relative;
    z-index: 5;
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 200px;
    gap: 1rem;
    padding: 1rem;
    overflow-y: auto;
}

/* Left Navigation */
.left-nav {
    background: rgba(22, 33, 62, 0.6);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.nav-section {
    margin-bottom: 1rem;
}

.nav-title {
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.nav-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.75rem 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    transition: all 0.3s;
    cursor: pointer;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.nav-icon {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

.nav-label {
    font-size: 0.7rem;
    text-align: center;
}

/* Center Character */
.center-character {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.character-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: 600px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.character-bg-large {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    opacity: 0.15;
    pointer-events: none;
    background-image: url("/images/character-background.svg");
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;
}

.character-main-display {
    position: relative;
    z-index: 2;
    margin-bottom: 1rem;
    /* Ensure dark background for blend mode to work */
    background: rgba(22, 33, 62, 0.3);
    border-radius: 12px;
    padding: 1rem;
}

.character-companions {
    position: relative;
    z-index: 2;
    display: flex;
    gap: 2rem;
    margin-bottom: 1rem;
    font-size: 0.85rem;
}

.companion-item {
    display: flex;
    gap: 0.5rem;
}

.companion-label {
    color: rgba(255, 255, 255, 0.7);
}

.companion-value {
    color: rgba(255, 255, 255, 0.9);
}

.character-stats {
    position: relative;
    z-index: 2;
    background: rgba(22, 33, 62, 0.6);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    width: 100%;
    max-width: 400px;
    margin-bottom: 1rem;
}

.stat-item {
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.stat-item:last-child {
    margin-bottom: 0;
}

.stat-label {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.8);
    min-width: 100px;
}

.stat-special {
    color: #ff4444;
    font-weight: 600;
}

.stat-value {
    font-size: 0.9rem;
    color: #fff;
    font-weight: 600;
}

.stat-progress {
    flex: 1;
    position: relative;
    height: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(90deg, #4a9eff, #9d4edd);
    transition: width 0.3s;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.75rem;
    color: #fff;
    font-weight: 600;
    z-index: 1;
}

.breakthrough-btn {
    margin-top: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
    border: none;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
    width: 100%;
    justify-content: center;
}

.breakthrough-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6);
    background: linear-gradient(135deg, #ff7b7b, #ff6b7f);
}

.breakthrough-btn:active:not(:disabled) {
    transform: translateY(0);
}

.breakthrough-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.breakthrough-icon {
    font-size: 1.2rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

.breakthrough-text {
    font-size: 0.9rem;
}

.cultivation-status {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 500px;
}

.status-text-main {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.75rem;
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.status-quote {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    font-style: italic;
    line-height: 1.6;
}

/* Right Navigation */
.right-nav {
    background: rgba(22, 33, 62, 0.6);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    height: fit-content;
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    transition: all 0.3s;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateX(5px);
}

.action-icon {
    font-size: 1.5rem;
}

.action-label {
    font-size: 0.9rem;
    font-weight: 500;
}

/* Characters Section */
.characters-section {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.characters-title {
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 0.75rem;
}

.characters-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}

.loading-spinner {
    width: 24px;
    height: 24px;
    border: 3px solid rgba(255, 255, 255, 0.2);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.characters-empty {
    text-align: center;
    padding: 1.5rem;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
}

.characters-link {
    display: inline-block;
    margin-top: 0.5rem;
    color: #4a9eff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.characters-link:hover {
    color: #3a8eef;
    text-decoration: underline;
}

.characters-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 400px;
    overflow-y: auto;
}

.character-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.character-item:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateX(3px);
}

.character-avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    flex-shrink: 0;
}

.character-icon {
    font-size: 1.5rem;
}

.character-info {
    flex: 1;
    min-width: 0;
}

.character-name {
    font-size: 0.85rem;
    font-weight: 500;
    color: #fff;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.character-relationship {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
}

.relationship-badge {
    padding: 0.15rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
}

.badge-neutral {
    background: rgba(128, 128, 128, 0.3);
    color: #ccc;
}

.badge-ally {
    background: rgba(74, 158, 255, 0.3);
    color: #4a9eff;
}

.badge-enemy {
    background: rgba(255, 77, 77, 0.3);
    color: #ff4d4d;
}

.badge-rival {
    background: rgba(255, 165, 0, 0.3);
    color: #ffa500;
}

.badge-friend {
    background: rgba(76, 175, 80, 0.3);
    color: #4caf50;
}

.badge-master {
    background: rgba(156, 39, 176, 0.3);
    color: #9c27b0;
}

.relationship-value {
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
}

/* Event Log */
.event-log {
    position: relative;
    z-index: 10;
    background: rgba(22, 33, 62, 0.8);
    backdrop-filter: blur(10px);
    border-top: 2px solid rgba(255, 255, 255, 0.1);
    padding: 1rem;
    max-height: 200px;
    display: flex;
    flex-direction: column;
}

.event-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
}

.event-tab {
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s;
}

.event-tab:hover {
    background: rgba(255, 255, 255, 0.1);
}

.event-tab.active {
    background: #4a9eff;
    border-color: #4a9eff;
    color: #fff;
    font-weight: 600;
}

.event-messages {
    flex: 1;
    overflow-y: auto;
    font-size: 0.85rem;
    color: #4a9eff;
    line-height: 1.6;
}

.event-message {
    margin-bottom: 0.5rem;
    padding: 0.25rem 0;
}

.event-tag {
    color: #4a9eff;
    font-weight: 600;
}

.event-empty {
    color: rgba(255, 255, 255, 0.5);
    text-align: center;
    padding: 1rem;
}

.scroll-to-bottom {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    padding: 0.5rem 1rem;
    background: #4a9eff;
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s;
}

.scroll-to-bottom:hover {
    background: #3a8eef;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 1024px) {
    .main-content {
        grid-template-columns: 200px 1fr 150px;
    }
}

@media (max-width: 768px) {
    .main-content {
        grid-template-columns: 1fr;
    }

    .right-nav {
        display: none;
    }

    .top-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .exit-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
    }

    .top-nav-menu {
        padding: 0.5rem 0.75rem;
    }

    .nav-menu-item {
        min-width: 60px;
        padding: 0.4rem 0.6rem;
    }

    .nav-menu-icon {
        font-size: 1.2rem;
    }

    .nav-menu-label {
        font-size: 0.65rem;
    }
}

/* Outfit Modal */
.outfit-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.outfit-modal {
    background: rgba(22, 33, 62, 0.95);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    animation: slideUp 0.3s;
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.modal-close {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.modal-content {
    padding: 1.5rem;
    overflow-y: auto;
    max-height: calc(80vh - 100px);
}

.outfit-option {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s;
}

.outfit-option:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateX(5px);
}

.outfit-option.equipped {
    background: rgba(74, 158, 255, 0.3);
    border-color: rgba(74, 158, 255, 0.5);
}

.outfit-icon {
    font-size: 3rem;
    filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
}

.outfit-info {
    flex: 1;
}

.outfit-name {
    font-weight: 600;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    margin-bottom: 0.25rem;
}

.outfit-category {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
    text-transform: capitalize;
}

.outfit-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.outfit-badge.equipped {
    background: rgba(74, 158, 255, 0.8);
    color: #fff;
}

.empty-outfits {
    text-align: center;
    padding: 2rem;
    color: rgba(255, 255, 255, 0.8);
}

.empty-outfits .cultivation-link {
    color: #4a9eff;
    text-decoration: none;
    font-weight: 600;
}

.empty-outfits .cultivation-link:hover {
    color: #3a8eef;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .main-content {
        grid-template-columns: 1fr;
    }

    .left-nav,
    .right-nav {
        display: none;
    }

    .top-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .cultivation-stage {
        text-align: left;
    }

    .outfit-modal {
        width: 95%;
    }
}
</style>
