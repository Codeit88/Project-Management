<template>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Users</h1>

        <div class="mb-4">
            <button 
                @click="openModal('create')"
                class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
                Create New User
            </button>
        </div>

        <ul class="space-y-4">
            <li v-for="user in users" :key="user.id" class="bg-white p-4 rounded-md shadow-md flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold">{{ user.name }}</h2>
                    <p class="text-gray-600">{{ user.email }}</p>
                </div>
                <div class="space-x-2">
                    <button 
                        @click="openModal('edit', user.id)" 
                        class="bg-green-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-green-600 transition duration-300">
                        Edit
                    </button>
                    <button 
                        @click="deleteUser(user.id)" 
                        class="bg-red-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-red-600 transition duration-300">
                        Delete
                    </button>
                </div>
            </li>
        </ul>
        <button 
                @click="navigateToDashboard"
                class="bg-gray-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-gray-600 transition duration-300 mt-10">
                Back to Dashboard
        </button>

        <!-- Create User Modal -->
        <Modal :show="showModal === 'create'" title="Create User" @close="closeModal">
            <form @submit.prevent="submitCreateUser">
                <div class="mb-4 p-10">
                    <label for="name" class="block text-gray-700">User Name</label>
                    <input v-model="form.name" id="name" type="text" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4 p-10">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input v-model="form.email" id="email" type="email" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4 p-10">
                    <label for="password" class="block text-gray-700">Password</label>
                    <input v-model="form.password" id="password" type="password" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="flex justify-end m-5">
                    <button 
                        type="submit" 
                        class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
                        Save
                    </button>
                </div>
            </form>
        </Modal>

        <!-- Edit User Modal -->
        <Modal :show="showModal === 'edit'" title="Edit User" @close="closeModal">
            <form @submit.prevent="submitEditUser">
                <div class="mb-4 p-10">
                    <label for="name" class="block text-gray-700">User Name</label>
                    <input v-model="editForm.name" id="name" type="text" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4 p-10">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input v-model="editForm.email" id="email" type="email" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="flex justify-end m-5">
                    <button 
                        type="submit" 
                        class="bg-green-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-green-600 transition duration-300">
                        Save
                    </button>
                </div>
            </form>
        </Modal>

        <!-- Delete User Modal -->
        <Modal :show="showModal === 'delete'" title="Delete User" @close="closeModal" @submit="submitDeleteUser">
            <div class="mb-4">
                <p>Are you sure you want to delete this user?</p>
            </div>
        </Modal>

    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Modal from '@/Components/Modal.vue';

export default {
    components: { Modal },
    setup() {
        const users = ref([]);
        const showModal = ref(null);
        const form = ref({ name: '', email: '', password: '' ,role:'user'});
        const editForm = ref({ name: '', email: '' });
        const currentUserId = ref(null);

        const fetchUsers = async () => {
            try {
                const response = await axios.get('/v1/admin/user');
                users.value = response.data;
            } catch (error) {
                console.error("There was an error fetching the users:", error);
            }
        };

        const navigateToDashboard = () => {
            window.location.href = '/dashboard';
        };

        const openModal = (type, id = null) => {
            currentUserId.value = id;
            showModal.value = type;
            if (type === 'edit' && id !== null) {
                const user = users.value.find(u => u.id === id);
                if (user) {
                    editForm.value.name = user.name;
                    editForm.value.email = user.email;
                }
            }
        };

        const closeModal = () => {
            showModal.value = null;
            form.value = { name: '', email: '', password: '' };
            editForm.value = { name: '', email: '' };
        };

        const submitCreateUser = async () => {
            try {
                await axios.post('/v1/admin/user', form.value);
                fetchUsers();
                closeModal();
            } catch (error) {
                console.error("There was an error creating the user:", error);
            }
        };

        const submitEditUser = async () => {
            try {
                await axios.put(`/v1/admin/user/${currentUserId.value}`, editForm.value);
                fetchUsers();
                closeModal();
            } catch (error) {
                console.error("There was an error updating the user:", error);
            }
        };

        const deleteUser = async (id) => {
            try {
                await axios.delete(`/v1/admin/user/${id}`);
                fetchUsers();
            } catch (error) {
                console.error("There was an error deleting the user:", error);
            }
        };

        const submitDeleteUser = async () => {
            try {
                await axios.delete(`/v1/admin/user/${currentUserId.value}`);
                fetchUsers();
                closeModal();
            } catch (error) {
                console.error("There was an error deleting the user:", error);
            }
        };

        onMounted(() => {
            fetchUsers();
        });

        return { users, showModal, openModal, closeModal, submitCreateUser, submitEditUser, deleteUser, submitDeleteUser, form, editForm, navigateToDashboard };
    }
};
</script>

<style scoped>
.container {
    max-width: 1200px;
}

button {
    transition: background-color 0.3s, transform 0.3s;
}

button:hover {
    transform: scale(1.05);
}
</style>
