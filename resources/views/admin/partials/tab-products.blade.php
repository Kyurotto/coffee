{{-- ---- TAB: PRODUCTS ---- --}}
<div x-show="activeTab === 'products'" x-cloak class="space-y-6"
     x-data="{
        /* ── modal state ── */
        showModal: false,
        isEdit: false,
        saving: false,
        deleting: null,
        errors: {},
        successMsg: '',

        /* ── form ── */
        form: { id: null, name: '', description: '', price: '', category: 'milktea' },
        imageFile: null,
        imagePreview: null,

        /* ── open add ── */
        openAdd() {
            this.isEdit      = false;
            this.form        = { id: null, name: '', description: '', price: '', category: 'milktea' };
            this.imageFile   = null;
            this.imagePreview = null;
            this.errors      = {};
            this.successMsg  = '';
            this.showModal   = true;
        },

        /* ── open edit ── */
        openEdit(product) {
            this.isEdit      = true;
            this.form        = { id: product.id, name: product.name, description: product.description, price: product.price, category: product.category };
            this.imageFile   = null;
            this.imagePreview = product.image || null;
            this.errors      = {};
            this.successMsg  = '';
            this.showModal   = true;
        },

        /* ── image pick ── */
        pickImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.imageFile = file;
            const reader = new FileReader();
            reader.onload = e => this.imagePreview = e.target.result;
            reader.readAsDataURL(file);
        },

        /* ── save (add or edit) ── */
        async save() {
            this.saving = true;
            this.errors = {};
            this.successMsg = '';

            const fd = new FormData();
            fd.append('name', this.form.name);
            fd.append('description', this.form.description || '');
            fd.append('price', this.form.price);
            fd.append('category', this.form.category);
            if (this.imageFile) fd.append('image', this.imageFile);

            const url = this.isEdit
                ? `/products/${this.form.id}`
                : '/products';

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                const data = await res.json();
                if (!res.ok) {
                    this.errors = data.errors || {};
                } else {
                    const p = data.product;
                    if (this.isEdit) {
                        const idx = filteredProducts.findIndex(x => x.id == p.id);
                        /* update the parent Alpine scope's products array */
                        const allIdx = $root.__x.$data.filteredProducts.findIndex(x => x.id == p.id);
                        /* We update the source array instead */
                        const src = document.querySelector('[x-data]').__x.$data;
                        /* walk up to find the root with filteredProducts getter */
                    }
                    this.successMsg = this.isEdit ? 'Product updated!' : 'Product added!';
                    setTimeout(() => { window.location.reload(); }, 900);
                }
            } catch(e) {
                this.errors = { name: ['Something went wrong. Please try again.'] };
            }
            this.saving = false;
        },

        /* ── delete ── */
        async deleteProduct(id) {
            if (!confirm('Delete this product? This cannot be undone.')) return;
            this.deleting = id;
            try {
                await fetch(`/products/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                });
                window.location.reload();
            } catch(e) { alert('Delete failed. Please try again.'); }
            this.deleting = null;
        }
     }">

    {{-- Header row --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Product Catalog</h1>
            <p class="text-xs text-gray-500 mt-0.5">Manage all items in store</p>
        </div>
        <button @click="openAdd()"
            class="flex items-center space-x-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-500/25 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Add Product</span>
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" x-model="searchQuery" placeholder="Search products..."
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500 dark:text-white">
        </div>
        <div class="flex gap-2">
            <button @click="categoryFilter = 'all'" :class="categoryFilter==='all' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-950 text-gray-700 dark:text-gray-300'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition">All</button>
            <button @click="categoryFilter = 'milktea'" :class="categoryFilter==='milktea' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-950 text-gray-700 dark:text-gray-300'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition">Milk Tea</button>
            <button @click="categoryFilter = 'snack'" :class="categoryFilter==='snack' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-950 text-gray-700 dark:text-gray-300'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition">Snacks</button>
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="product in filteredProducts" :key="product.id">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                {{-- Product Image --}}
                <div class="relative h-44 w-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <template x-if="product.image">
                        <img :src="product.image" :alt="product.name"
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                    </template>
                    <template x-if="!product.image">
                        <div class="w-full h-full flex items-center justify-center"
                             :class="product.category === 'milktea' ? 'bg-gradient-to-br from-blue-100 to-indigo-200 dark:from-blue-900/40 dark:to-indigo-900/40' : 'bg-gradient-to-br from-amber-100 to-orange-200 dark:from-amber-900/40 dark:to-orange-900/40'">
                            <span class="text-4xl font-black opacity-30 text-gray-600 dark:text-gray-300" x-text="product.name.charAt(0)"></span>
                        </div>
                    </template>
                    {{-- Category badge --}}
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase backdrop-blur-sm bg-white/80 dark:bg-gray-900/80"
                              :class="product.category==='milktea' ? 'text-blue-700' : 'text-amber-700'"
                              x-text="product.category==='milktea' ? 'Milk Tea' : 'Snack'"></span>
                    </div>
                    {{-- NEW badge --}}
                    <template x-if="product.is_new">
                        <div class="absolute bottom-2 right-2">
                            <span class="relative inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-green-500 text-white shadow">
                                <span class="absolute inset-0 rounded-full animate-ping bg-green-400 opacity-60"></span>
                                <span class="relative">NEW</span>
                            </span>
                        </div>
                    </template>
                    {{-- Edit / Delete overlay buttons --}}
                    <div class="absolute top-2 left-2 flex space-x-1">
                        <button @click.stop="openEdit(product)"
                            class="w-7 h-7 rounded-lg bg-white/90 dark:bg-gray-900/90 text-gray-600 dark:text-gray-300 hover:text-amber-600 hover:bg-white dark:hover:bg-gray-800 flex items-center justify-center shadow transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click.stop="deleteProduct(product.id)"
                            :disabled="deleting === product.id"
                            class="w-7 h-7 rounded-lg bg-white/90 dark:bg-gray-900/90 text-gray-600 dark:text-gray-300 hover:text-red-600 hover:bg-white dark:hover:bg-gray-800 flex items-center justify-center shadow transition">
                            <template x-if="deleting !== product.id">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </template>
                            <template x-if="deleting === product.id">
                                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                        </button>
                    </div>
                </div>
                {{-- Product Info --}}
                <div class="p-4 flex flex-col flex-1 justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white" x-text="product.name"></h4>
                            <span class="text-sm font-bold text-amber-600 dark:text-amber-400 shrink-0 ml-2" x-text="'$' + parseFloat(product.price).toFixed(2)"></span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2" x-text="product.description"></p>
                    </div>
<div class="pt-3 border-t border-gray-100 dark:border-gray-800 mt-3"
                         x-data="{
                            inputVal: stocks[product.id] ?? stocks[String(product.id)] ?? 0,
                            saved: false,
                            update() {
                                const val = parseInt(this.inputVal);
                                if (isNaN(val) || val < 0) { this.inputVal = stocks[product.id] ?? stocks[String(product.id)] ?? 0; return; }
                                setStock(String(product.id), val);
                                this.saved = true;
                                setTimeout(() => this.saved = false, 1500);
                            }
                         }">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">Stock:</span>
                            <div class="flex items-center gap-1.5">
                                <input
                                    type="number"
                                    x-model.number="inputVal"
                                    min="0"
                                    step="1"
                                    @keydown.enter="update()"
                                    @click.stop
                                    class="w-16 px-2 py-1 text-xs text-center font-bold bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                    placeholder="0"
                                >
                                <button
                                    @click.stop="update()"
                                    :class="saved ? 'bg-green-500 text-white' : 'bg-amber-500 hover:bg-amber-600 text-white'"
                                    class="px-2 py-1 rounded-lg text-[10px] font-semibold transition-all active:scale-95 shrink-0">
                                    <span x-text="saved ? '✓' : 'Set'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Empty state --}}
        <template x-if="filteredProducts.length === 0">
            <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">No products found</p>
                <p class="text-xs text-gray-400 mt-1">Try a different search or add a new product.</p>
            </div>
        </template>
    </div>

    {{-- ── Add / Edit Modal (teleported to body) ── --}}
    <template x-teleport="body">
        <div x-show="showModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="showModal = false">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>

            {{-- Panel --}}
            <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col"
                 @click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-800 shrink-0">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white" x-text="isEdit ? 'Edit Product' : 'Add New Product'"></h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="isEdit ? 'Update product details' : 'Fill in the details for the new product'"></p>
                    </div>
                    <button type="button" @click="showModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg p-1 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Success banner --}}
                <div x-show="successMsg" x-cloak
                     class="mx-6 mt-4 flex items-center space-x-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="successMsg"></span>
                </div>

                {{-- Scrollable body --}}
                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

                    {{-- Image upload --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Product Image <span class="font-normal text-gray-400">(optional)</span></label>
                        <div class="relative flex items-center justify-center w-full h-36 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 overflow-hidden cursor-pointer hover:border-amber-400 transition group"
                             @click="$refs.imageInput.click()">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover rounded-2xl">
                            </template>
                            <template x-if="!imagePreview">
                                <div class="flex flex-col items-center space-y-2 text-gray-400 group-hover:text-amber-500 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs font-medium">Click to upload image</span>
                                    <span class="text-[10px]">PNG, JPG, WEBP up to 2MB</span>
                                </div>
                            </template>
                            <template x-if="imagePreview">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 flex items-center justify-center transition">
                                    <span class="opacity-0 group-hover:opacity-100 text-white text-xs font-semibold bg-black/50 px-3 py-1.5 rounded-lg transition">Change image</span>
                                </div>
                            </template>
                        </div>
                        <input type="file" x-ref="imageInput" accept="image/*" @change="pickImage($event)" class="hidden">
                        <p x-show="errors.image" x-text="errors.image && errors.image[0]" class="mt-1 text-xs text-red-500"></p>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Product Name <span class="text-red-400">*</span></label>
                        <input type="text" x-model="form.name" placeholder="e.g. Taro Milk Tea"
                            :class="errors.name ? 'border-red-400' : 'border-gray-200 dark:border-gray-700'"
                            class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                        <p x-show="errors.name" x-text="errors.name && errors.name[0]" class="mt-1 text-xs text-red-500"></p>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Description <span class="font-normal text-gray-400">(optional)</span></label>
                        <textarea x-model="form.description" rows="3" placeholder="Short description of the product..."
                            class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition resize-none"></textarea>
                        <p x-show="errors.description" x-text="errors.description && errors.description[0]" class="mt-1 text-xs text-red-500"></p>
                    </div>

                    {{-- Price & Category row --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Price ($) <span class="text-red-400">*</span></label>
                            <input type="number" x-model="form.price" placeholder="0.00" min="0.01" step="0.01"
                                :class="errors.price ? 'border-red-400' : 'border-gray-200 dark:border-gray-700'"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                            <p x-show="errors.price" x-text="errors.price && errors.price[0]" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Category <span class="text-red-400">*</span></label>
                            <select x-model="form.category"
                                :class="errors.category ? 'border-red-400' : 'border-gray-200 dark:border-gray-700'"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                                <option value="milktea">Milk Tea</option>
                                <option value="snack">Snack</option>
                            </select>
                            <p x-show="errors.category" x-text="errors.category && errors.category[0]" class="mt-1 text-xs text-red-500"></p>
                        </div>
                    </div>
                </div>

                {{-- Footer actions --}}
                <div class="flex items-center space-x-3 px-6 py-4 border-t border-gray-100 dark:border-gray-800 shrink-0">
                    <button type="button" @click="showModal = false"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="button" @click="save()" :disabled="saving"
                        :class="saving ? 'opacity-70 cursor-not-allowed' : ''"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl shadow-lg shadow-amber-500/25 transition-all flex items-center justify-center space-x-2">
                        <template x-if="!saving">
                            <span x-text="isEdit ? 'Save Changes' : 'Add Product'"></span>
                        </template>
                        <template x-if="saving">
                            <span class="flex items-center space-x-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span>Saving…</span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
