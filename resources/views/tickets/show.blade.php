@extends('layouts.app')

@section('title', $ticket->ticket_number)
@section('header', 'Detail Tiket')
@section('subheader', $ticket->ticket_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Ticket Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-sm font-mono text-blue-600">{{ $ticket->ticket_number }}</span>
                        <h2 class="text-xl font-bold text-slate-800 mt-1">{{ $ticket->title }}</h2>
                        <div class="flex flex-wrap items-center gap-4 mt-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-slate-500">Status:</span>
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $ticket->status->color }}20; color: {{ $ticket->status->color }}">
                                    {{ $ticket->status->name }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-slate-500">Prioritas:</span>
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $ticket->priority->color }}20; color: {{ $ticket->priority->color }}">
                                    {{ $ticket->priority->name }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-slate-500">Kategori:</span>
                                <span class="text-sm font-medium text-slate-700">{{ $ticket->category->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($ticket->needs_approval)
                    <div class="text-right">
                        @if($ticket->approval_status === 'pending')
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                            <i class="ti ti-clock"></i>
                            Menunggu Persetujuan
                        </span>
                        @elseif($ticket->approval_status === 'approved')
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                            <i class="ti ti-circle-check"></i>
                            Disetujui
                        </span>
                        @elseif($ticket->approval_status === 'rejected')
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                            <i class="ti ti-circle-x"></i>
                            Ditolak
                        </span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="p-6">
                <h3 class="text-sm font-medium text-slate-500 mb-3">Deskripsi</h3>
                <div class="prose prose-slate max-w-none">
                    {!! nl2br(e($ticket->description)) !!}
                </div>
                
                @if($ticket->attachments->count() > 0)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-medium text-slate-500 mb-3">Lampiran</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($ticket->attachments as $attachment)
                        <a href="{{ Storage::url($attachment->file_path) }}" 
                           target="_blank"
                           class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition">
                            @if($attachment->isImage())
                            <img src="{{ Storage::url($attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="w-10 h-10 object-cover rounded">
                            @else
                            <div class="w-10 h-10 rounded bg-slate-200 flex items-center justify-center">
                                <i class="ti ti-file text-xl text-slate-500"></i>
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-700 truncate">{{ $attachment->file_name }}</p>
                                <p class="text-xs text-slate-500">{{ $attachment->human_file_size }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($ticket->resolution_notes)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-medium text-slate-500 mb-3">Catatan Penyelesaian</h3>
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <p class="text-slate-700">{!! nl2br(e($ticket->resolution_notes)) !!}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Komentar & Aktivitas</h3>
            </div>
            
            <div class="divide-y divide-slate-100">
                @forelse($comments as $comment)
                <div class="p-6 {{ $comment->is_internal ? 'bg-amber-50' : '' }}">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-medium text-slate-800">{{ $comment->user->name }}</span>
                                <span class="text-xs text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                                @if($comment->is_internal)
                                <span class="px-2 py-0.5 text-xs bg-amber-100 text-amber-700 rounded-full">Internal</span>
                                @endif
                            </div>
                            <p class="text-slate-600">{!! nl2br(e($comment->content)) !!}</p>
                            
                            @if($comment->attachments->count() > 0)
                            <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($comment->attachments as $attachment)
                                <a href="{{ Storage::url($attachment->file_path) }}" 
                                   target="_blank"
                                   class="flex items-center gap-2 p-2 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                                    @if($attachment->isImage())
                                    <img src="{{ Storage::url($attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="w-10 h-10 object-cover rounded">
                                    @else
                                    <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0
                                        @if(str_contains($attachment->file_type, 'pdf')) bg-red-500
                                        @elseif(str_contains($attachment->file_type, 'word') || str_contains($attachment->file_type, 'document')) bg-blue-500
                                        @elseif(str_contains($attachment->file_type, 'excel') || str_contains($attachment->file_type, 'spreadsheet')) bg-green-600
                                        @else bg-slate-500
                                        @endif">
                                        <i class="ti ti-file text-white"></i>
                                    </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate">{{ $attachment->file_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $attachment->human_file_size }}</p>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <i class="ti ti-messages text-6xl text-slate-200 mb-3"></i>
                    <p class="text-slate-500">Belum ada komentar</p>
                </div>
                @endforelse
            </div>

            <!-- Add Comment Form -->
            @if($ticket->isOpen())
            <div class="p-6 border-t border-slate-100 bg-slate-50" x-data="commentFileUpload()">
                <form action="{{ route('comments.store', $ticket) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <textarea name="content" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                  placeholder="Tulis komentar atau balasan..."
                                  required></textarea>
                        
                        <!-- File Preview List -->
                        <div x-show="files.length > 0" class="space-y-2">
                            <p class="text-sm font-medium text-slate-700">File yang dipilih:</p>
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center gap-3 p-2 bg-white rounded-lg border border-slate-200">
                                    <div class="w-8 h-8 rounded flex items-center justify-center flex-shrink-0"
                                         :class="getFileIconClass(file.type)">
                                        <template x-if="file.type.startsWith('image/')">
                                            <img :src="file.preview" class="w-8 h-8 object-cover rounded" alt="">
                                        </template>
                                        <template x-if="!file.type.startsWith('image/')">
                                            <i class="ti ti-file text-white"></i>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate" x-text="file.name"></p>
                                        <p class="text-xs text-slate-500" x-text="formatFileSize(file.size)"></p>
                                    </div>
                                    <button type="button" @click="removeFile(index)" class="p-1 text-slate-400 hover:text-red-500 transition">
                                        <i class="ti ti-x text-lg"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 hover:bg-slate-50 cursor-pointer transition">
                                    <i class="ti ti-paperclip"></i>
                                    <span x-text="files.length > 0 ? files.length + ' file' : 'Lampiran'"></span>
                                    <input type="file" name="attachments[]" multiple class="hidden" @change="handleFileSelect($event)">
                                </label>
                                
                                @can('comments.create.internal')
                                <label class="flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="is_internal" value="1" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                    Catatan Internal
                                </label>
                                @endcan
                            </div>
                            
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition inline-flex items-center gap-2">
                                <i class="ti ti-send"></i>
                                Kirim
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Ticket Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Informasi Tiket</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase">Pemohon</span>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-semibold">
                            {{ strtoupper(substr($ticket->requester->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $ticket->requester->name }}</p>
                            <p class="text-xs text-slate-500">{{ $ticket->requester->email }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase">Ditugaskan Ke</span>
                    @if($ticket->assignedTo)
                    <div class="flex items-center gap-2 mt-1">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white text-xs font-semibold">
                            {{ strtoupper(substr($ticket->assignedTo->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $ticket->assignedTo->name }}</p>
                            <p class="text-xs text-slate-500">
                                @if($ticket->assignedTo->hasRole('Helpdesk'))
                                    Staff Helpdesk
                                @elseif($ticket->assignedTo->hasRole('Technician'))
                                    Staff Teknisi
                                @else
                                    {{ $ticket->assignedTo->getRoleNames()->first() ?? 'Staff' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-slate-500 mt-1 italic">Belum ditugaskan</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div>
                        <span class="text-xs font-medium text-slate-400 uppercase">Dibuat</span>
                        <p class="text-sm text-slate-700 mt-1">{{ $ticket->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-slate-400 uppercase">Terakhir Update</span>
                        <p class="text-sm text-slate-700 mt-1">{{ $ticket->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                @if($ticket->resolved_at)
                <div class="pt-4 border-t border-slate-100">
                    <span class="text-xs font-medium text-slate-400 uppercase">Diselesaikan</span>
                    <p class="text-sm text-green-600 mt-1">{{ $ticket->resolved_at->format('d M Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        @if($ticket->isOpen())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Aksi</h3>
            
            <div class="space-y-3">
                @can('tickets.assign')
                <!-- Assign Form -->
                <form action="{{ route('tickets.assign', $ticket) }}" method="POST" class="space-y-3">
                    @csrf
                    <label class="block text-sm font-medium text-slate-700">Tugaskan ke Staff</label>
                    <select name="assigned_to_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">Pilih Staff</option>
                        @if($helpdeskStaff->count() > 0)
                        <optgroup label="Staff Helpdesk">
                            @foreach($helpdeskStaff as $staff)
                            <option value="{{ $staff->id }}" {{ $ticket->assigned_to_id == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endif
                        @if($technicians->count() > 0)
                        <optgroup label="Staff Teknisi">
                            @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ $ticket->assigned_to_id == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endif
                    </select>
                    <p class="text-xs text-slate-500">Helpdesk handle dulu, jika tidak bisa baru ke Teknisi</p>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition inline-flex items-center justify-center gap-2">
                        <i class="ti ti-user-plus"></i>
                        Tugaskan
                    </button>
                </form>
                @endcan

                @can('tickets.update.all')
                <!-- Update Ticket Details -->
                <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="space-y-4 pt-3 border-t border-slate-100">
                    @csrf
                    @method('PUT')
                    
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $ticket->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Prioritas</label>
                        <select name="priority_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @foreach($priorities as $priority)
                            <option value="{{ $priority->id }}" {{ $ticket->priority_id == $priority->id ? 'selected' : '' }}>
                                {{ $priority->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $ticket->status_id == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full px-4 py-2 bg-slate-600 text-white font-medium rounded-lg hover:bg-slate-700 transition inline-flex items-center justify-center gap-2">
                        <i class="ti ti-device-floppy"></i>
                        Simpan Perubahan
                    </button>
                </form>
                @endcan

                <!-- Close Ticket -->
                @if(Auth::user()->can('tickets.close.all') || (Auth::user()->can('tickets.close.own') && $ticket->requester_id === Auth::id()))
                <form action="{{ route('tickets.close', $ticket) }}" method="POST" class="pt-3 border-t border-slate-100">
                    @csrf
                    @php
                        $isResolved = $ticket->status->slug === 'resolved';
                        $isStaff = Auth::user()->isStaff();
                        $canClose = $isStaff || $isResolved;
                    @endphp
                    
                    @if(!$canClose)
                    <p class="text-xs text-amber-600 mb-2">
                        <i class="ti ti-alert-circle"></i>
                        Tiket hanya bisa ditutup setelah berstatus "Resolved"
                    </p>
                    @endif
                    
                    <button type="submit" 
                            onclick="return confirm('Yakin ingin menutup tiket ini?')"
                            @if(!$canClose) disabled @endif
                            class="w-full px-4 py-2 {{ $canClose ? 'bg-red-600 hover:bg-red-700' : 'bg-slate-300 cursor-not-allowed' }} text-white font-medium rounded-lg transition inline-flex items-center justify-center gap-2">
                        <i class="ti ti-x"></i>
                        Tutup Tiket
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

        <!-- Back Button -->
        <a href="{{ route('tickets.index') }}" class="block text-center px-4 py-3 bg-slate-100 text-slate-600 font-medium rounded-xl hover:bg-slate-200 transition inline-flex items-center justify-center gap-2">
            <i class="ti ti-arrow-left"></i>
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function commentFileUpload() {
    return {
        files: [],
        
        handleFileSelect(event) {
            this.addFiles(event.target.files);
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
