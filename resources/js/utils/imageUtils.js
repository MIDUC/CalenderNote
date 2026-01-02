/**
 * Utility functions for image processing
 * Handles background removal and image manipulation
 */

/**
 * Remove white background from image using canvas
 * @param {string} imageUrl - URL of the image
 * @param {number} threshold - Threshold for white detection (0-255)
 * @returns {Promise<string>} - Data URL of processed image
 */
export async function removeWhiteBackground(imageUrl, threshold = 240) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = img.width;
            canvas.height = img.height;
            
            // Draw image to canvas
            ctx.drawImage(img, 0, 0);
            
            // Get image data
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            // Process each pixel
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                
                // Check if pixel is white (or near white)
                if (r > threshold && g > threshold && b > threshold) {
                    // Make transparent
                    data[i + 3] = 0;
                }
            }
            
            // Put processed data back
            ctx.putImageData(imageData, 0, 0);
            
            // Convert to data URL
            resolve(canvas.toDataURL('image/png'));
        };
        
        img.onerror = reject;
        img.src = imageUrl;
    });
}

/**
 * Apply CSS filter to remove white background
 * This is a CSS-only solution that works in real-time
 * @param {HTMLElement} element - Image element
 */
export function applyWhiteBackgroundRemoval(element) {
    if (!element) return;
    
    // Method 1: Use mix-blend-mode (works best on dark backgrounds)
    element.style.mixBlendMode = 'screen';
    
    // Method 2: Use CSS filters (alternative)
    // element.style.filter = 'contrast(1.2) brightness(1.1)';
    
    // Method 3: Use CSS mask (if image has transparency)
    // element.style.webkitMaskImage = `url(${element.src})`;
    // element.style.maskImage = `url(${element.src})`;
}

/**
 * Check if image has white background
 * @param {string} imageUrl - URL of the image
 * @returns {Promise<boolean>}
 */
export async function hasWhiteBackground(imageUrl) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            
            // Sample corners to check for white
            const corners = [
                [0, 0], // top-left
                [canvas.width - 1, 0], // top-right
                [0, canvas.height - 1], // bottom-left
                [canvas.width - 1, canvas.height - 1], // bottom-right
            ];
            
            let whiteCount = 0;
            for (const [x, y] of corners) {
                const pixel = ctx.getImageData(x, y, 1, 1).data;
                const r = pixel[0];
                const g = pixel[1];
                const b = pixel[2];
                
                if (r > 240 && g > 240 && b > 240) {
                    whiteCount++;
                }
            }
            
            resolve(whiteCount >= 2); // If 2+ corners are white, likely has white background
        };
        
        img.onerror = reject;
        img.src = imageUrl;
    });
}

