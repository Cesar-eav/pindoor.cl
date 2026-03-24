<template>
  <div class="mt-6">
    <div class="flex items-center justify-center w-full mb-4">
      <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
        <div class="flex flex-col items-center justify-center pt-5 pb-6">
          <svg class="w-8 h-8 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"/></svg>
          <p class="text-sm text-gray-500">Haz clic para subir fotos del local</p>
        </div>
        <input type="file" class="hidden" multiple accept="image/*" @change="handleFiles" :disabled="isUploading" />
      </label>
    </div>

    <div v-if="isUploading" class="mb-4">
        <div class="flex justify-between mb-1">
            <span class="text-xs font-bold text-pindoor-accent uppercase tracking-wider">Subiendo...</span>
            <span class="text-xs font-bold text-gray-600">{{ uploadProgress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            <div class="bg-pindoor-accent h-2 transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
        </div>
    </div>

    <draggable 
      v-model="images" 
      item-key="id"
      class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6"
    >
      <template #item="{ element, index }">
        <div class="relative group aspect-square bg-gray-100 rounded-xl overflow-hidden border-2" 
             :class="element.is_main ? 'border-pindoor-accent' : 'border-transparent'">
          
          <img :src="element.url" class="object-cover w-full h-full cursor-move" />

          <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center gap-1">
            <button type="button" @click="setMain(index)" class="text-[10px] bg-white text-black px-2 py-1 rounded font-bold uppercase">
              {{ element.is_main ? 'Principal ✓' : 'Hacer Principal' }}
            </button>
            <button type="button" @click="removeImage(index)" class="text-[10px] bg-red-500 text-white px-2 py-1 rounded font-bold uppercase">
              Eliminar
            </button>
          </div>
          
          <div v-if="element.is_main" class="absolute top-1 left-1 bg-pindoor-accent text-white text-[9px] px-2 py-0.5 rounded-full font-bold">
            PORTADA
          </div>
        </div>
      </template>
    </draggable>
  </div>
</template>

<script>
import draggable from 'vuedraggable';
import axios from 'axios';

export default {
    components: { draggable },
    data() {
        return {
            images: [],
            isUploading: false,
            uploadProgress: 0,
            endpoint: '/cliente/guardar-punto'
        };
    },
    mounted() {
        // 💡 Escuchamos un evento global desde Blade
        window.addEventListener('trigger-pindoor-submit', () => {
            if (this.images.length > 0 && !this.isUploading) {
                this.submitForm();
            } else if (this.images.length === 0) {
                alert('Debes subir al menos una imagen.');
            }
        });
    },
    methods: {
        handleFiles(event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.images.push({
                        id: Math.random(),
                        url: e.target.result,
                        file: file,
                        is_main: this.images.length === 0
                    });
                };
                reader.readAsDataURL(file);
            });
        },
        setMain(index) {
            this.images.forEach((img, i) => img.is_main = i === index);
        },
        removeImage(index) {
            this.images.splice(index, 1);
        },
        async submitForm() {
            this.isUploading = true;
            this.uploadProgress = 0;

            const formData = new FormData();
            const mainForm = document.querySelector('#main-form');
            const otherData = new FormData(mainForm);
            
            for (let [key, value] of otherData.entries()) {
                formData.append(key, value);
            }

            this.images.forEach((img, index) => {
                formData.append(`photos[${index}]`, img.file);
                formData.append(`metadata[${index}][is_main]`, img.is_main ? 1 : 0);
            });

            try {
                const response = await axios.post(this.endpoint, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                    onUploadProgress: (progressEvent) => {
                        this.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    }
                });

                if (response.data.success) {
                    window.location.href = response.data.url;
                }
            } catch (error) {
                alert("Error al subir los datos. Revisa el tamaño de las imágenes.");
            } finally {
                this.isUploading = false;
            }
        }
    }
};
</script>