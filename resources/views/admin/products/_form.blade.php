@csrf

<div class="row g-4">
    <div class="col-md-6">
        <label for="name" class="form-label">Nama Produk</label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $product->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="Contoh: Headphone Bluetooth">
    </div>
    <div class="col-md-6">
        <label for="category_id" class="form-label">Kategori</label>
        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="slug" class="form-label">Slug</label>
        <input
            id="slug"
            type="text"
            name="slug"
            value="{{ old('slug', $product->slug) }}"
            class="form-control @error('slug') is-invalid @enderror"
            placeholder="Otomatis dari nama bila dikosongkan">
    </div>
    <div class="col-md-6">
        <label for="sku" class="form-label">SKU</label>
        <input
            id="sku"
            type="text"
            name="sku"
            value="{{ old('sku', $product->sku) }}"
            class="form-control @error('sku') is-invalid @enderror"
            placeholder="Contoh: ELEK-001">
    </div>
    <div class="col-md-6">
        <label for="price" class="form-label">Harga</label>
        <input
            id="price"
            type="number"
            step="0.01"
            min="0"
            name="price"
            value="{{ old('price', $product->price) }}"
            class="form-control @error('price') is-invalid @enderror">
    </div>
    <div class="col-md-6">
        <label for="compare_price" class="form-label">Harga Coret</label>
        <input
            id="compare_price"
            type="number"
            step="0.01"
            min="0"
            name="compare_price"
            value="{{ old('compare_price', $product->compare_price) }}"
            class="form-control @error('compare_price') is-invalid @enderror">
    </div>
    <div class="col-md-4">
        <label for="stock" class="form-label">Stok</label>
        <input
            id="stock"
            type="number"
            min="0"
            name="stock"
            value="{{ old('stock', $product->stock ?? 0) }}"
            class="form-control @error('stock') is-invalid @enderror">
    </div>
    <div class="col-md-4">
        <label for="weight" class="form-label">Berat (gram)</label>
        <input
            id="weight"
            type="number"
            step="0.01"
            min="0"
            name="weight"
            value="{{ old('weight', $product->weight) }}"
            class="form-control @error('weight') is-invalid @enderror">
    </div>
    <div class="col-md-4">
        <label for="cover_image" class="form-label">Path Gambar</label>
        <input
            id="cover_image"
            type="text"
            name="cover_image"
            value="{{ old('cover_image', $product->cover_image) }}"
            class="form-control @error('cover_image') is-invalid @enderror"
            placeholder="assets/img/elements/12.png">
    </div>
    <div class="col-12">
        <label for="excerpt" class="form-label">Excerpt</label>
        <textarea
            id="excerpt"
            name="excerpt"
            rows="2"
            class="form-control @error('excerpt') is-invalid @enderror"
            placeholder="Ringkasan singkat produk untuk katalog.">{{ old('excerpt', $product->excerpt) }}</textarea>
    </div>
    <div class="col-12">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="5"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="Deskripsi lengkap produk.">{{ old('description', $product->description) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="is_active"
                name="is_active"
                value="1"
                @checked(old('is_active', $product->is_active))>
            <label class="form-check-label" for="is_active">Produk aktif</label>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label d-block">Sorotan</label>
        <div class="form-check form-switch mt-2">
            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="featured"
                name="featured"
                value="1"
                @checked(old('featured', $product->featured))>
            <label class="form-check-label" for="featured">Tampilkan sebagai produk unggulan</label>
        </div>
    </div>
    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</div>
