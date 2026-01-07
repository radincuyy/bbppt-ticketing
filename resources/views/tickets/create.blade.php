@extends('layouts.app')

@section('title', 'Buat Tiket Baru')
@section('header', 'Buat Tiket Baru')
@section('subheader', 'Ajukan permintaan layanan TI')

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-2">
                        Perihal Tiket <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('title') border-red-500 @enderror"
                           placeholder="Ringkasan singkat masalah atau permintaan Anda"
                           required>
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category & Priority -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-slate-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" 
                                id="category_id" 
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('category_id') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority_id" class="block text-sm font-medium text-slate-700 mb-2">
                            Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select name="priority_id" 
                                id="priority_id" 
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:border-blue-500 transition @error('priority_id') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Prioritas</option>
                            @foreach($priorities as $priority)
                            <option value="{{ $priority->id }}" {{ old('priority_id') == $priority->id ? 'selected' : '' }}>
                                {{ $priority->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('priority_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="6"
                              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('description') border-red-500 @enderror"
                              placeholder="Jelaskan secara detail masalah atau permintaan Anda. Sertakan informasi seperti:
- Langkah-langkah untuk mereproduksi masalah
- Pesan error yang muncul (jika ada)
- Perangkat atau software yang bermasalah"
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Attachments -->
                <div x-data="fileUpload()">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Lampiran (Opsional)
                    </label>
                    <div class="border-2 border-dashed rounded-xl p-8 text-center transition"
                         :class="isDragging ? 'border-blue-400 bg-blue-50' : 'border-slate-200 hover:border-blue-400'"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)">
                        <i class="ti ti-cloud-upload text-4xl text-slate-300 mb-4"></i>
                        <p class="text-slate-500 mb-2">Drag & drop file di sini atau</p>
                        <label class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 cursor-pointer transition">
                            <i class="ti ti-paperclip"></i>
                            Pilih File
                            <input type="file" name="attachments[]" multiple class="hidden" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx" @change="handleFileSelect($event)">
                        </label>
                        <p class="text-xs text-slate-400 mt-2">Maksimal 10MB per file. Format: JPG, PNG, PDF, DOC, XLS</p>
                    </div>
                    
                    <!-- File Preview List -->
                    <div x-show="files.length > 0" class="mt-4 space-y-2">
                        <p class="text-sm font-medium text-slate-700">File yang dipilih:</p>
                        <template x-for="(file, index) in files" :key="index">
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                                <!-- Icon based on file type -->
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                     :class="getFileIconClass(file.type)">
                                    <template x-if="file.type.startsWith('image/')">
                                        <img :src="file.preview" class="w-10 h-10 object-cover rounded-lg" alt="">
                                    </template>
                                    <template x-if="!file.type.startsWith('image/')">
                                        <i class="ti ti-file text-white text-xl"></i>
                                    </template>
                                </div>
                                
                                <!-- File info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-700 truncate" x-text="file.name"></p>
                                    <p class="text-xs text-slate-500" x-text="formatFileSize(file.size)"></p>
                                </div>
                                
                                <!-- Remove button -->
                                <button type="button" @click="removeFile(index)" class="p-1 text-slate-400 hover:text-red-500 transition">
                                    <i class="ti ti-x text-xl"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    
                    @error('attachments.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('tickets.index') }}" class="px-6 py-3 text-slate-600 font-medium hover:text-slate-800 transition inline-flex items-center gap-2">
                <i class="ti ti-arrow-left"></i>
                Kembali
            </a>
            <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 transform hover:scale-105 inline-flex items-center gap-2">
                <i class="ti ti-send"></i>
                Kirim Tiket
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function fileUpload() {
    return {
        files: [],
        isDragging: false,
        
        handleFileSelect(event) {
            this.addFiles(event.target.files);
        },
        
        handleDrop(event) {
            this.isDragging = false;
            this.addFiles(event.dataTransfer.files);
        },
        
        addFiles(fileList) {
            for (let file of fileList) {
                let fileData = {
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    preview: null,
                    file: file
                };
                
                // Create preview for images
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        fileData.preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
                
                this.files.push(fileData);
            }
        },
        
        removeFile(index) {
            this.files.splice(index, 1);
            // Update the file input
            this.updateFileInput();
        },
        
        updateFileInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f.file));
            document.querySelector('input[name="attachments[]"]').files = dt.files;
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        getFileIconClass(type) {
            if (type.startsWith('image/')) return 'bg-green-500';
            if (type.includes('pdf')) return 'bg-red-500';
            if (type.includes('word') || type.includes('document')) return 'bg-blue-500';
            if (type.includes('excel') || type.includes('spreadsheet')) return 'bg-green-600';
            return 'bg-slate-500';
        }
    }
}
</script>
@endpush
