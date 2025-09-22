<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRUD with Laravel + Vue.js</title>
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<div id="app" class="container py-4">
    <h2 class="mb-4">Item CRUD</h2>

    <!-- Add Form -->
    <div class="card p-3 mb-4 shadow-sm">
        <h5>Add New Item</h5>
        <div class="row g-2">
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

    <!-- Items Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th style="width:180px">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in items" :key="item.id">
                    <td><input v-model="item.name" class="form-control"></td>
                    <td><input v-model="item.description" class="form-control"></td>
                    <td>
                        <button @click="updateItem(item)" class="btn btn-success btn-sm me-2">Update</button>
                        <button @click="confirmDelete(item.id)" class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    new Vue({
        el: '#app',
        data: {
            items: [],
            newItem: { name: '', description: '' }
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems() {
                fetch('/items')
                    .then(res => res.json())
                    .then(data => this.items = data);
            },
            addItem() {
                if(!this.newItem.name){
                    Swal.fire('Error','Name is required','error');
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
                        Swal.fire('Success','Item added successfully','success');
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
                        this.getItems();
                        Swal.fire('Updated','Item updated successfully','success');
                    });
            },
            confirmDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
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
                        this.getItems();
                        Swal.fire('Deleted!','Item has been deleted.','success');
                    });
            }
        }
    });
</script>
</body>
</html>
