<template>
    <div class="character-display">
        <!-- Character container with outfit -->
        <div class="character-container" :class="{ 'cultivating': isCultivating }">
            <!-- Background character (large, translucent) -->
            <div class="character-bg" v-if="backgroundCharacter">
                <div class="bg-character" :style="bgCharacterStyle"></div>
            </div>
            
            <!-- Main character (chibi style) -->
            <div class="character-main">
                <div class="character-sprite" :style="characterStyle">
                    <!-- Character image or icon/emoji -->
                    <div 
                        v-if="!outfit?.icon && !outfit?.sprite_url" 
                        class="character-default-img"
                    ></div>
                    <div v-else class="character-icon">{{ characterIcon }}</div>
                    
                    <!-- Cultivation aura -->
                    <div v-if="isCultivating" class="cultivation-aura">
                        <div class="aura-ring aura-ring-1"></div>
                        <div class="aura-ring aura-ring-2"></div>
                        <div class="aura-ring aura-ring-3"></div>
                    </div>
                </div>
            </div>
            
            <!-- Outfit change button -->
            <button 
                v-if="showOutfitButton"
                @click="$emit('change-outfit')"
                class="outfit-change-btn"
                title="Thay đổi trang phục"
            >
                <span class="btn-icon">👕</span>
            </button>
        </div>
        
        <!-- Character info -->
        <div v-if="showInfo" class="character-info">
            <div class="info-item" v-if="companion">
                <span class="info-label">Đạo lữ:</span>
                <span class="info-value">{{ companion || 'Chưa có' }}</span>
            </div>
            <div class="info-item" v-if="spiritBeast">
                <span class="info-label">Linh thú:</span>
                <span class="info-value">{{ spiritBeast || 'Chưa có' }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    outfit: {
        type: Object,
        default: null
    },
    isCultivating: {
        type: Boolean,
        default: false
    },
    showOutfitButton: {
        type: Boolean,
        default: true
    },
    showInfo: {
        type: Boolean,
        default: true
    },
    companion: {
        type: String,
        default: null
    },
    spiritBeast: {
        type: String,
        default: null
    },
    backgroundCharacter: {
        type: String,
        default: null
    }
})

const emit = defineEmits(['change-outfit'])

const characterIcon = computed(() => {
    if (props.outfit?.icon) {
        return props.outfit.icon
    }
    // Default character emoji based on cultivation state
    return props.isCultivating ? '🧘' : '👤'
})

const characterStyle = computed(() => {
    const style = {}
    
    if (props.outfit?.sprite_url) {
        style.backgroundImage = `url(${props.outfit.sprite_url})`
        style.backgroundSize = 'contain'
        style.backgroundRepeat = 'no-repeat'
        style.backgroundPosition = 'center'
    }
    
    // Apply outfit color if available
    if (props.outfit?.effects?.color) {
        style.filter = `hue-rotate(${props.outfit.effects.color}deg)`
    }
    
    return style
})

const bgCharacterStyle = computed(() => {
    if (props.backgroundCharacter) {
        return {
            backgroundImage: `url(${props.backgroundCharacter})`,
            backgroundSize: 'cover',
            backgroundPosition: 'center',
            opacity: 0.3
        }
    }
    return {}
})

// Using pre-processed image with transparent background
// No Canvas processing needed
</script>

<style scoped>
.character-display {
    position: relative;
    width: 100%;
    min-height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.character-container {
    position: relative;
    width: 100%;
    min-height: 300px;
    height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.character-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    pointer-events: none;
}

.bg-character {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0.2;
    filter: blur(2px);
}

.character-main {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
}

.character-sprite {
    position: relative;
    width: 200px;
    height: 200px;
    min-width: 200px;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s;
}

.character-container.cultivating .character-sprite {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.character-icon {
    font-size: 80px;
    filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.5));
    transition: transform 0.3s;
}

.character-default-img {
    position: relative;
    width: 200px;
    height: 200px;
    min-width: 200px;
    min-height: 200px;
    background-image: url('/images/nhanvatngoithien1-removebg-preview.png');
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;
    filter: drop-shadow(0 0 20px rgba(255, 215, 0, 0.5));
    transition: transform 0.3s;
    /* Ensure image is visible */
    display: block;
    visibility: visible;
    opacity: 1;
    z-index: 10;
    /* Image already has transparent background */
    background-color: transparent;
}

/* Ensure sprite container can handle transparent backgrounds */
.character-sprite {
    position: relative;
    width: 200px;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s;
}

.character-container.cultivating .character-default-img {
    animation: float 3s ease-in-out infinite;
}

.character-container.cultivating .character-icon {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.5));
    }
    50% {
        transform: scale(1.1);
        filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.8));
    }
}

/* Cultivation aura */
.cultivation-aura {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 150px;
    height: 150px;
    pointer-events: none;
}

.aura-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: 2px solid rgba(255, 215, 0, 0.6);
    border-radius: 50%;
    animation: auraRotate 4s linear infinite;
}

.aura-ring-1 {
    width: 100px;
    height: 100px;
    animation-duration: 3s;
}

.aura-ring-2 {
    width: 120px;
    height: 120px;
    animation-duration: 4s;
    animation-direction: reverse;
}

.aura-ring-3 {
    width: 140px;
    height: 140px;
    animation-duration: 5s;
}

@keyframes auraRotate {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
        opacity: 0.6;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) rotate(360deg);
        opacity: 0.6;
    }
}

/* Outfit change button */
.outfit-change-btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    z-index: 10;
}

.outfit-change-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: scale(1.1);
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
}

.btn-icon {
    font-size: 20px;
}

/* Character info */
.character-info {
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.info-item {
    display: flex;
    gap: 0.5rem;
}

.info-label {
    font-weight: 600;
}

.info-value {
    color: rgba(255, 255, 255, 0.8);
}

@media (max-width: 768px) {
    .character-sprite {
        width: 100px;
        height: 100px;
    }
    
    .character-icon {
        font-size: 60px;
    }
    
    .character-info {
        flex-direction: column;
        gap: 0.5rem;
        font-size: 0.75rem;
    }
}
</style>

