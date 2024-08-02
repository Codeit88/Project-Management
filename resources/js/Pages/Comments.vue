<template>
    <div class="container mx-auto p-6">
      <h1 class="text-3xl font-bold mb-6">Comments Manager</h1>

      <!-- Button to Add New Comment -->
      <button @click="openModal('create')" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300 mb-6">
        Add New Comment
      </button>
  
      <!-- List Comments -->
      <ul class="space-y-4">
        <li v-for="comment in comments" :key="comment.id" class="bg-white p-4 rounded-md shadow-md flex justify-between items-center">
          <div>
            <p class="text-xl font-semibold">{{ comment.content }}</p>
            <p class="text-gray-600">Reply to: {{ comment.parent_id || 'None' }}</p>
            <p class="text-gray-600">Task ID: {{ comment.task_id }}</p>
          </div>
          <div class="space-x-2">
            <button @click="openModal('view', comment.id)" class="bg-gray-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-gray-600 transition duration-300">
              View
            </button>
            <button @click="openModal('edit', comment.id)" class="bg-green-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-green-600 transition duration-300">
              Edit
            </button>
            <button @click="deleteComment(comment.id)" class="bg-red-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-red-600 transition duration-300">
              Delete
            </button>
          </div>
        </li>
      </ul>
  
      <!-- Add Comment Modal -->
      <Modal :show="showModal === 'create'" title="Add Comment" @close="closeModal" @submit="submitCreateComment">
        <form @submit.prevent="submitCreateComment">
          <div class="mb-4 p-10">
            <label for="content" class="block text-gray-700">Comment Content</label>
            <textarea v-model="form.content" id="content" rows="4" class="mt-1 block w-full px-3 py-2 border rounded-md" required></textarea>
          </div>
          <div class="mb-4 p-10">
            <label for="parent_id" class="block text-gray-700">Reply To (optional)</label>
            <select v-model="form.parent_id" id="parent_id" class="mt-1 block w-full px-3 py-2 border rounded-md">
              <option v-for="comment in comments" :key="comment.id" :value="comment.id">
                {{ comment.content }}
              </option>
            </select>
          </div>
          <div class="mb-4 p-10">
            <label for="task_id" class="block text-gray-700">Select Task</label>
            <select v-model="form.task_id" id="task_id" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
              <option v-for="task in tasks" :key="task.id" :value="task.id">
                Task {{ task.id }}
              </option>
            </select>
          </div>
          <div class="flex justify-end m-5">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
              Save Comment
            </button>
          </div>
        </form>
      </Modal>
  
      <!-- View Comment Modal -->
      <Modal :show="showModal === 'view'" title="Comment Details" @close="closeModal">
        <div class="mb-4 p-10">
          <p><strong>Content:</strong> {{ commentDetails.content }}</p>
          <p><strong>Reply to:</strong> {{ commentDetails.parent_id || 'None' }}</p>
          <p><strong>Task ID:</strong> {{ commentDetails.task_id }}</p>
        </div>
      </Modal>
  
      <!-- Edit Comment Modal -->
      <Modal :show="showModal === 'edit'" title="Edit Comment" @close="closeModal" @submit="submitEditComment">
        <form @submit.prevent="submitEditComment">
          <div class="mb-4 p-10">
            <label for="content" class="block text-gray-700">Comment Content</label>
            <textarea v-model="editForm.content" id="content" rows="4" class="mt-1 block w-full px-3 py-2 border rounded-md" required></textarea>
          </div>
          <div class="mb-4 p-10">
            <label for="parent_id" class="block text-gray-700">Reply To (optional)</label>
            <select v-model="editForm.parent_id" id="parent_id" class="mt-1 block w-full px-3 py-2 border rounded-md">
              <option v-for="comment in comments" :key="comment.id" :value="comment.id">
                {{ comment.content }}
              </option>
            </select>
          </div>
          <div class="mb-4 p-10">
            <label for="task_id" class="block text-gray-700">Select Task</label>
            <select v-model="editForm.task_id" id="task_id" class="mt-1 block w-full px-3 py-2 border rounded-md" required>
              <option v-for="task in tasks" :key="task.id" :value="task.id">
                Task {{ task.id }}
              </option>
            </select>
          </div>
          <div class="flex justify-end m-5">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
              Save Changes
            </button>
          </div>
        </form>
      </Modal>
  
    </div>
  </template>
  
  <script>
  import { ref } from 'vue';
  import Modal from '@/Components/Modal.vue';
  
  export default {
    components: { Modal },
    props: ['projectId'],
    setup(props) {
      const tasks = ref([
        { id: 1, name: 'Task 1' },
        { id: 2, name: 'Task 2' },
        // Add more tasks as needed
      ]);

      const comments = ref([
        { id: 1, content: 'This is a comment', parent_id: null, task_id: 1 },
        { id: 2, content: 'This is a reply to comment 1', parent_id: 1, task_id: 1 },
      ]);
      const showModal = ref(null);
      const form = ref({ content: '', parent_id: null, task_id: null });
      const editForm = ref({ content: '', parent_id: null, task_id: null });
      const commentDetails = ref({});
      const currentCommentId = ref(null);
  
      const openModal = (type, id = null) => {
        currentCommentId.value = id;
        showModal.value = type;
        if (type === 'view' && id !== null) {
          const comment = comments.value.find(c => c.id === id);
          commentDetails.value = comment || {};
        } else if (type === 'edit' && id !== null) {
          const comment = comments.value.find(c => c.id === id);
          editForm.value = comment || { content: '', parent_id: null, task_id: null };
        }
      };
  
      const closeModal = () => {
        showModal.value = null;
        form.value = { content: '', parent_id: null, task_id: null };
        editForm.value = { content: '', parent_id: null, task_id: null };
      };
  
      const submitCreateComment = () => {
        const newComment = { ...form.value, id: comments.value.length + 1 };
        comments.value.push(newComment);
        closeModal();
      };
  
      const submitEditComment = () => {
        const index = comments.value.findIndex(c => c.id === currentCommentId.value);
        if (index !== -1) {
          comments.value[index] = { ...editForm.value, id: currentCommentId.value };
        }
        closeModal();
      };
  
      const deleteComment = (id) => {
        comments.value = comments.value.filter(c => c.id !== id);
      };
  
      return { 
        comments, showModal, form, editForm, commentDetails, tasks, openModal, closeModal, submitCreateComment, submitEditComment, deleteComment
      };
    }
  };
  </script>
  
  <style scoped>
  /* Add any additional styles you need here */
  </style>
