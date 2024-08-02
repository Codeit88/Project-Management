<!-- resources/js/Pages/Projects/Index.vue -->
<template>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Projects</h1>
        
        <div class="mb-4">
            <button 
                @click="openModal('create')"
                class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
                Create New Project
            </button>
        </div>

        <ul class="space-y-4">
            <li v-for="project in projects" :key="project.id" class="bg-white p-4 rounded-md shadow-md flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold">{{ project.name }}</h2>
                    <p class="text-gray-600">
                        Assigned to: 
                        <span v-if="project.assigned_user_id" class="font-semibold">{{ project.assigned_user_id }}</span>
                        <span v-else class="italic">None</span>
                    </p>
                </div>
                <div class="space-x-2">
                    <button 
                        @click="openModal('edit', project.id)" 
                        class="bg-green-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-green-600 transition duration-300">
                        Edit
                    </button>
                    <button 
                        @click="deleteProject(project.id)" 
                        class="bg-red-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-red-600 transition duration-300">
                        Delete
                    </button>
                    <button 
                        @click="openModal('assign', project.id)" 
                        class="bg-yellow-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-yellow-600 transition duration-300">
                        Assign
                    </button>
                </div>
            </li>
        </ul>
        <button 
                @click="navigateToDashboard"
                class="bg-gray-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-gray-600 transition duration-300 mt-10">
                Back to Dashboard
            </button>
        <!-- Create Project Modal -->
        <Modal :show="showModal === 'create'" title="Create Project" @close="closeModal" @submit="submitCreateProject">
            <form @submit.prevent="submitCreateProject">
                <div class="mb-4 p-10">
                    <label for="name" class="block text-gray-700 ">Project Name</label>
                    <input v-model="form.name" id="name" type="text" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
                </div>
                <!-- Add more fields if needed -->
            </form>
        </Modal>

        <!-- Edit Project Modal -->
        <Modal :show="showModal === 'edit'" title="Edit Project" @close="closeModal" @submit="submitEditProject">
            <form @submit.prevent="submitEditProject">
                <div class="mb-4 p-10">
                    <label for="name" class="block text-gray-700">Project Name</label>
                    <input v-model="editForm.name" id="name" type="text" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
                </div>
                <!-- Add more fields if needed -->
            </form>
        </Modal>

        <!-- Delete Project Modal -->
        <Modal :show="showModal === 'delete'" title="Delete Project" @close="closeModal" @submit="submitDeleteProject">
            <div class="mb-4">
            <p>Are you sure you want to delete this project?</p>
        </div>
        </Modal>

     <!-- Assign Project Modal -->
<Modal :show="showModal === 'assign'" title="Assign Project" @close="closeModal" @submit="submitAssignProject">
    <form @submit.prevent="submitAssignProject">
        <div class="mb-4 p-10">
            <label for="user" class="block text-gray-700">Assign to User</label>
            <select v-model="assignForm.user_id" id="user" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
                <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }}
                </option>
            </select>
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


    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Modal from '@/Components/Modal.vue';

export default {
    components: { Modal },
    setup() {

        const users = ref([]);
        const projects = ref([]);
        const showModal = ref(null);
        const form = ref({ name: '' });
        const editForm = ref({ name: '' });
        const assignForm = ref({ user_id: '' });
        const currentProjectId = ref(null);
        const router = useRouter();
         
        const fetchProjects = async () => {
            try {
                const response = await axios.get('/v1/admin/project');
                projects.value = response.data;
            } catch (error) {
                console.error("There was an error fetching the projects:", error);
            }
        };
        const navigateToDashboard=()=>{
            window.location.href='/dashboard';
        }
        const fetchUsers = async () => {
            try {
                const response = await axios.get('/v1/admin/user');
                users.value = response.data;
                console.log(users.value);
            } catch (error) {
                console.error("There was an error fetching the users:", error);
            }
        };


        const openModal = (type, id = null) => {
            currentProjectId.value = id;
            showModal.value = type;
            if (type === 'edit' && id !== null) {
                // Load the project details for editing
                const project = projects.value.find(p => p.id === id);
                if (project) {
                    editForm.value.name = project.name;
                }
            }
        };

        const closeModal = () => {
            showModal.value = null;
            form.value.name = '';
            editForm.value.name = '';
            assignForm.value.user_id = '';
        };
        const submitCreateProject = async () => {
            try {
                await axios.post('/v1/admin/project', form.value);
                fetchProjects();
                closeModal();
            } catch (error) {
                console.error("There was an error creating the project:", error);
            }
        };

        const submitEditProject = async () => {
            try {
                await axios.put(`/v1/admin/project/${currentProjectId.value}`, editForm.value);
                fetchProjects();
                closeModal();
            } catch (error) {
                console.error("There was an error updating the project:", error);
            }
        };


        const submitAssignProject = async () => {
            try {
                await axios.post(`/v1/admin/project/${currentProjectId.value}/assign`, assignForm.value);
                fetchProjects();
                closeModal();
            } catch (error) {
                console.error("There was an error assigning the project:", error);
            }
        };

        
        const deleteProject = async (id) => {
            try {
                await axios.delete(`/v1/admin/project/${id}`);
                fetchProjects();
            } catch (error) {
                console.error("There was an error deleting the project:", error);
            }
        };
        onMounted(() => {
            fetchProjects();
            fetchUsers();
        });

        return { projects,users, showModal, openModal, closeModal, submitCreateProject, submitEditProject, deleteProject, submitAssignProject, form, editForm, assignForm,navigateToDashboard };
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
