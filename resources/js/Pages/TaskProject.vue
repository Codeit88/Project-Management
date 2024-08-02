<template>
    <div class="container mx-auto p-6">
      <h1 class="text-3xl font-bold mb-6">Task Manager</h1>
  
      <!-- Create Task -->
      <div class="mb-4">
        <button @click="openModal('create')" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
          Create New Task
        </button>
      </div>
  
      <!-- List Tasks -->
      <ul class="space-y-4">
        <li v-for="task in tasks" :key="task.id" class="bg-white p-4 rounded-md shadow-md flex justify-between items-center">
          <div>
            <h2 class="text-xl font-semibold">{{ task.name }}</h2>
            <p class="text-gray-600">Parent Task ID: {{ task.parent_id || 'None' }}</p>
          </div>
          <div class="space-x-2">
            <button @click="openModal('edit', task.id)" class="bg-green-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-green-600 transition duration-300">
              Edit
            </button>
            <button @click="deleteTask(task.id)" class="bg-red-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-red-600 transition duration-300">
              Delete
            </button>
            <button @click="openModal('assign', task.id)" class="bg-yellow-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-yellow-600 transition duration-300">
              Assign
            </button>
          </div>
        </li>
      </ul>
  
      <!-- Create Task Modal -->
      <Modal :show="showModal === 'create'" title="Create Task" @close="closeModal" @submit="submitCreateTask">
        <form @submit.prevent="submitCreateTask">
          <div class="mb-4 p-10">
            <label for="name" class="block text-gray-700">Task Name</label>
            <input v-model="form.name" id="name" type="text" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
          </div>
          <div class="mb-4 p-10">
            <label for="parent_id" class="block text-gray-700">Parent Task (optional)</label>
            <select v-model="form.parent_id" id="parent_id" class="mt-1 block w-full px-3 py-2 border rounded-md">
            <option v-for="task in projects" :key="task.id" :value="task.id">
              {{ task.name }}
            </option>
          </select>
          </div>
          <div class="flex justify-end m-5">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
              Save Task
            </button>
          </div>
        </form>
      </Modal>
  
      <!-- View Task Modal -->
      <Modal :show="showModal === 'view'" title="Task Details" @close="closeModal">
        <div class="mb-4 p-10">
          <p><strong>Name:</strong> {{ taskDetails.name }}</p>
          <p><strong>Parent Task ID:</strong> {{ taskDetails.parent_id || 'None' }}</p>
        </div>
      </Modal>
  
      <!-- Edit Task Modal -->
      <Modal :show="showModal === 'edit'" title="Edit Task" @close="closeModal" @submit="submitEditTask">
        <form @submit.prevent="submitEditTask">
          <div class="mb-4 p-10">
            <label for="name" class="block text-gray-700">Task Name</label>
            <input v-model="editForm.name" id="name" type="text" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
          </div>
          <div class="mb-4 p-10">
            <label for="parent_id" class="block text-gray-700">Parent Task (optional)</label>
            <input v-model="editForm.parent_id" id="parent_id" type="number" class="mt-1 block w-full px-3 py-2 border rounded-md">
          </div>
          <div class="flex justify-end m-5">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
              Save Changes
            </button>
          </div>
        </form>
      </Modal>
  
      <!-- Assign Task Modal -->
      <Modal :show="showModal === 'assign'" title="Assign Task" @close="closeModal" @submit="submitAssignTask">
        <form @submit.prevent="submitAssignTask">
          <div class="mb-4 p-10">
            <label for="user" class="block text-gray-700">Assign to User</label>
            <select v-model="assignForm.user_id" id="user" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>
          <div class="flex justify-end m-5">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
              Assign Task
            </button>
          </div>
        </form>
      </Modal>
  
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  import Modal from '@/Components/Modal.vue';
  
  export default {
    components: { Modal },
    props: ['projectId'],
    setup(props) {
      const tasks = ref([]);
      const showModal = ref(null);
      const form = ref({ name: '', parent_id: null });
      const editForm = ref({ name: '', parent_id: null ,task:''});
      const assignForm = ref({ user_id: '' });
      const taskDetails = ref({});
      const currentTaskId = ref(null);
      const users = ref([]);      
      const projects = ref([]);
  

      const fetchProjects = async () => {
        try {
          const response = await axios.get('/v1/project');
          projects.value = response.data;
          console.log(projects.value);
          
        } catch (error) {
          console.error("There was an error fetching the projects:", error);
        }
      };


      const fetchTasks = async () => {
        try {
          const response = await axios.get(`/v1/project/${props.projectId}/task`);
          tasks.value = response.data;
        } catch (error) {
          console.error("There was an error fetching the tasks:", error);
        }
      };
  
      const fetchUsers = async () => {
        try {
          const response = await axios.get('/v1/admin/user');
          users.value = response.data;
        } catch (error) {
          console.error("There was an error fetching the users:", error);
        }
      };
  
      const openModal = async (type, id = null) => {
        currentTaskId.value = id;
        showModal.value = type;
        if (type === 'view' && id !== null) {
          try {
            const response = await axios.get(`/v1/project/${props.projectId}/task/${id}`);
            taskDetails.value = response.data;
          } catch (error) {
            console.error("There was an error fetching task details:", error);
          }
        } else if (type === 'edit' && id !== null) {
          try {
            const response = await axios.get(`/v1/project/${props.projectId}/task/${id}`);
            editForm.value = response.data;
          } catch (error) {
            console.error("There was an error fetching task details for editing:", error);
          }
        }
      };
  
      const closeModal = () => {
        showModal.value = null;
        form.value = { name: '', parent_id: null };
        editForm.value = { name: '', parent_id: null };
        assignForm.value = { user_id: '' };
      };
  
      const submitCreateTask = async () => {
        try {
          await axios.post(`/v1/project/addTask`, form.value);
          fetchTasks();
          closeModal();
        } catch (error) {
          console.error("There was an error creating the task:", error);
        }
      };
  
      const submitEditTask = async () => {
        try {
          await axios.put(`/v1/project/${props.projectId}/task/${currentTaskId.value}`, editForm.value);
          fetchTasks();
          closeModal();
        } catch (error) {
          console.error("There was an error updating the task:", error);
        }
      };
  
      const deleteTask = async (id) => {
        try {
          await axios.post(`/v1/project/delete`);
          fetchTasks();
        } catch (error) {
          console.error("There was an error deleting the task:", error);
        }
      };
  
      const submitAssignTask = async () => {
        try {
          await axios.post(`/v1/project/assign`, assignForm.value);
          fetchTasks();
          closeModal();
        } catch (error) {
          console.error("There was an error assigning the task:", error);
        }
      };
  
      onMounted(() => {
        fetchProjects();
        fetchTasks();
        fetchUsers();
      });
  
      return { 
        tasks, showModal, form, editForm, assignForm, taskDetails, users, openModal, closeModal, submitCreateTask, submitEditTask, deleteTask, submitAssignTask,fetchProjects,projects
      };
    }
  };
  </script>
  