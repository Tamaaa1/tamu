<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Agenda Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus me-2"></i>Buat Agenda Baru
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('agenda.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="nama_agenda" class="form-label">Nama Agenda <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_agenda') is-invalid @enderror" 
                                       id="nama_agenda" name="nama_agenda" value="{{ old('nama_agenda') }}" required>
                                @error('nama_agenda')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_agenda" class="form-label">Tanggal Agenda <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_agenda') is-invalid @enderror" 
                                           id="tanggal_agenda" name="tanggal_agenda" value="{{ old('tanggal_agenda') }}" required>
                                    @error('tanggal_agenda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="dinas_id" class="form-label">Dinas <span class="text-danger">*</span></label>
                                    <select class="form-select @error('dinas_id') is-invalid @enderror" id="dinas_id" name="dinas_id" required>
                                        <option value="">Pilih Dinas</option>
                                        @foreach($dinas as $dinasItem)
                                            <option value="{{ $dinasItem->dinas_id }}" {{ old('dinas_id') == $dinasItem->dinas_id ? 'selected' : '' }}>
                                                {{ $dinasItem->nama_dinas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('dinas_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="link_acara" class="form-label">Link Acara <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    <input type="url" class="form-control @error('link_acara') is-invalid @enderror" 
                                           id="link_acara" name="link_acara" value="{{ old('link_acara') }}" 
                                           placeholder="https://meet.google.com/..." required>
                                </div>
                                <div class="form-text">Masukkan link meeting atau acara (Google Meet, Zoom, dll)</div>
                                @error('link_acara')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('agenda.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Agenda
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
