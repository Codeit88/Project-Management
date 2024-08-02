<template>
    <div class="container mx-auto p-6">
      <h1 class="text-3xl font-bold mb-6">Projects</h1>
  
      <!-- List of Projects -->
      <div v-if="!selectedProject">
        <ul class="space-y-4">
          <li v-for="project in projects" :key="project.id" class="bg-white p-4 rounded-md shadow-md flex justify-between items-center">
            <div>
              <h2 class="text-xl font-semibold">{{ project.name }}</h2>
              <p class="text-gray-600">Assigned to: 
                <span v-if="project.assigned_user_id" class="font-semibold">{{ project.assigned_user_id }}</span>
                <span v-else class="italic">None</span>
              </p>
            </div>
            <button 
              @click="fetchProjectDetails(project.id)" 
              class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
              View Details
            </button>
          </li>
        </ul>
      </div>
      <button 
                @click="navigateToDashboard"
                class="bg-gray-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-gray-600 transition duration-300 mt-10">
                Back to Dashboard
            </button>
      <!-- Project Details -->
      <div v-if="selectedProject">
        <h2 class="text-2xl font-bold mb-4">{{ selectedProject.name }}</h2>
        <p class="text-gray-600 mb-4">Assigned to: 
          <span v-if="selectedProject.assigned_user_id" class="font-semibold">{{ selectedProject.assigned_user_id }}</span>
          <span v-else class="italic">None</span>
        </p>
        
        <h3 class="text-xl font-semibold mb-2">Users</h3>
        <ul class="space-y-2 mb-4">
          <li v-for="user in users" :key="user.id" class="bg-white p-2 rounded-md shadow-md">
            {{ user.name }} ({{ user.email }})
          </li>
        </ul>
        
        <button 
          @click="selectedProject = null" 
          class="bg-gray-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-gray-600 transition duration-300">
          Back to Projects
        </button>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  
  export default {
    setup() {
      const projects = ref([]);
      const selectedProject = ref(null);
      const users = ref([]);
  
      const fetchProjects = async () => {
        try {
          const response = await axios.get('/v1/project');
          projects.value = response.data;
        } catch (error) {
          console.error("There was an error fetching the projects:", error);
        }
      };
      const navigateToDashboard=()=>{
            window.location.href='/dashboard2';
        }
      const fetchProjectDetails = async (projectId) => {
        try {
          const [projectResponse, usersResponse] = await Promise.all([
            axios.get(`/v1/project/${projectId}`),
            axios.get(`/v1/users`)  // Fetch users list (you might need to adjust this endpoint based on your actual API)
          ]);
          selectedProject.value = projectResponse.data;
          users.value = usersResponse.data;
        } catch (error) {
          console.error("There was an error fetching the project details:", error);
        }
      };
  
      onMounted(() => {
        fetchProjects();
      });
  
      return {
        projects,
        selectedProject,
        users,
        fetchProjectDetails,
        navigateToDashboard
      };
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
  