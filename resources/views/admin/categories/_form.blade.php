@csrf

<div class="row g-4">
    <div class="col-md-6">
        <label for="name" class="form-label">Nama Kategori</label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $category->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="Contoh: Elektronik">
    </div>
    <div class="col-md-6">
        <label for="slug" class="form-label">Slug</label>
        <input
            id="slug"
            type="text"
            name="slug"
            value="{{ old('slug', $category->slug) }}"
            class="form-control @error('slug') is-invalid @enderror"
            placeholder="Otomatis dari nama bila dikosongkan">
    </div>
    <div class="col-12">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="Ringkasan kategori untuk kebutuhan admin dan katalog.">{{ old('description', $category->description) }}</textarea>
    </div>
    <div class="col-md-4">
        <label for="sort_order" class="form-label">Urutan Tampil</label>
        <input
            id="sort_order"
            type="number"
            min="0"
            name="sort_order"
            value="{{ old('sort_order', $category->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror">
    </div>
    <div class="col-md-8">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="is_active"
                name="is_active"
                value="1"
                @checked(old('is_active', $category->is_active))>
            <label class="form-check-label" for="is_active">Kategori aktif dan muncul dalam pilihan katalog</label>
        </div>
    </div>
    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</div>
