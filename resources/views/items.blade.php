<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel + Vue CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<div id="app" class="container py-5">
    <h2 class="mb-4">Item CRUD (Laravel + Vue.js)</h2>

    <!-- Add Form -->
    <div class="card mb-4">
        <div class="card-header">Add Item</div>
        <div class="card-body row g-2">
            <div class="col-md-4">
                <input v-model="newItem.name" class="form-control" placeholder="Name">
            </div>
            <div class="col-md-6">
                <input v-model="newItem.description" class="form-control" placeholder="Description">
            </div>
            <div class="col-md-2">
                <button @click="addItem" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input v-model="search" @input="getItems(1)" class="form-control" placeholder="Search...">
        </div>
    </div>

    <!-- Items Table -->
    <div class="card">
        <div class="card-header">Items</div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th style="width:180px">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in items.data" :key="item.id">
                    <td><input v-model="item.name" class="form-control"></td>
                    <td><input v-model="item.description" class="form-control"></td>
                    <td>
                        <button @click="updateItem(item)" class="btn btn-success btn-sm me-2">Update</button>
                        <button @click="confirmDelete(item.id)" class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>
                <tr v-if="items.data && items.data.length === 0">
                    <td colspan="3" class="text-center">No items found.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <nav v-if="items.last_page > 1" class="mt-3">
        <ul class="pagination">
            <li class="page-item" :class="{disabled: items.current_page === 1}">
                <a class="page-link" href="#" @click.prevent="getItems(items.current_page - 1)">Previous</a>
            </li>
            <li class="page-item" v-for="page in items.last_page" :key="page"
                :class="{active: items.current_page === page}">
                <a class="page-link" href="#" @click.prevent="getItems(page)">@{{ page }}</a>
            </li>
            <li class="page-item" :class="{disabled: items.current_page === items.last_page}">
                <a class="page-link" href="#" @click.prevent="getItems(items.current_page + 1)">Next</a>
            </li>
        </ul>
    </nav>
</div>

<script>
    new Vue({
        el: '#app',
        data: {
            items: { data: [] },
            newItem: { name: '', description: '' },
            search: ''
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems(page = 1) {
                fetch(`/items?page=${page}&search=${this.search}`)
                    .then(res => res.json())
                    .then(data => this.items = data);
            },
            addItem() {
                if (!this.newItem.name) {
                    Swal.fire("Required!", "Name field is required", "warning");
                    return;
                }
                fetch('/items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.newItem)
                })
                    .then(res => res.json())
                    .then(() => {
                        this.newItem = { name: '', description: '' };
                        this.getItems();
                        Swal.fire("Added!", "Item has been added.", "success");
                    });
            },
            updateItem(item) {
                fetch(`/items/${item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(item)
                })
                    .then(() => {
                        this.getItems(this.items.current_page);
                        Swal.fire("Updated!", "Item has been updated.", "success");
                    });
            },
            confirmDelete(id) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "This will delete the item permanently!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.deleteItem(id);
                    }
                });
            },
            deleteItem(id) {
                fetch(`/items/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(() => {
                        this.getItems(this.items.current_page);
                        Swal.fire("Deleted!", "Item has been deleted.", "success");
                    });
            }
        }
    });
</script>
</body>
</html>
