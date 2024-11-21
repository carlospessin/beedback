<script setup>
import { ref, onMounted, defineProps } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import { useToast } from 'primevue/usetoast';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';


// Recebendo os usuários do Laravel
const props = defineProps({
    users: Array,
    role_id: Number,
});

const toast = useToast();
const userDialog = ref(false);
const newUser = ref({ name: '', email: '', role: '' });
const submitted = ref(false);
const roles = ref([]);

const openNewUserDialog = () => {
  newUser.value = { name: '', email: '', role: '' };
  submitted.value = false;
  userDialog.value = true;
};

const fetchRoles = async () => {
  try {
    const response = await axios.get('/api/roles');
    roles.value = response.data.map(item => ({ label: item.role, value: item.id }));
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Não foi possível carregar as roles.', life: 3000 });
  }
};

onMounted(fetchRoles);

const saveUser = () => {
  submitted.value = true;

  if (newUser.value.name.trim() && newUser.value.email.trim() && newUser.value.role) {
    // Aqui, você deve garantir que está adicionando a role_id e não apenas o nome da role.
    const roleId = newUser.value.role; // O role selecionado é o ID, não o nome.

    // Você precisa mapear a role selecionada para o ID correto
    props.users.push({
      ...newUser.value,
      role_id: roleId,  // Adiciona o role_id no lugar do campo role.
    });
    userDialog.value = false;
    toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Usuário adicionado!', life: 3000 });
  }
};

const getRoleNameById = (roleId) => {
  const role = roles.value.find(role => role.value === roleId);
  return role ? role.label : 'Unknown';  
};


const hideDialog = () => {
  userDialog.value = false;
  submitted.value = false;
};

const form = useForm({
    name: '',
    email: '',
    role: ''
});

const submit = () => {
    form.post('/register', {
      onSuccess: () => {
        userDialog.value = false;
        saveUser();
      },
      onError: () => {
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Erro ao adicionar usuário.', life: 3000 });
      },
    });
};

const deleteUser = async (userId) => {
  // Confirmação antes de deletar
  if (confirm('Tem certeza que deseja deletar este usuário?')) {
    try {
      const response = await axios.delete(`/users/${userId}`);
      
      if (response.status === 200) {
        // Remover usuário da lista local
        const index = props.users.findIndex(user => user.id === userId);
        if (index > -1) {
          props.users.splice(index, 1);
          toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Usuário excluído!', life: 3000 });
        }
      }
    } catch (error) {
      toast.add({ severity: 'error', summary: 'Erro', detail: 'Não foi possível excluir o usuário.', life: 3000 });
    }
  }
};

</script>

<template>
    <AppLayout title="Usuários" :role_id="props.role_id">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Usuários
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <Button label="Adicionar Novo Usuário" icon="pi pi-plus" @click="openNewUserDialog" />
                    </div>
                    
                    <DataTable 
                        :value="props.users" 
                        paginator 
                        :rows="5" 
                        :rowsPerPageOptions="[5, 10, 20, 50]" 
                        tableStyle="min-width: 50rem"
                        paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                        currentPageReportTemplate="{first} to {last} of {totalRecords}"
                    >
                        <template #paginatorstart>
                            <Button type="button" icon="pi pi-refresh" text />
                        </template>
                        <template #paginatorend>
                            <Button type="button" icon="pi pi-download" text />
                        </template>
                        <Column field="name" header="Name" style="width: 25%"></Column>
                        <Column field="email" header="Email" style="width: 25%"></Column>
                        <Column field="role_id" header="Role" sortable style="min-width: 16rem">
                            <template #body="slotProps">
                                <span>
                                {{ getRoleNameById(slotProps.data.role_id) }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Ações" style="min-width: 10rem">
                            <template #body="slotProps">
                                <Button 
                                    label="" 
                                    icon="pi pi-trash" 
                                    class="p-button-danger p-button-sm" 
                                    @click="deleteUser(slotProps.data.id)" 
                                />
                            </template>
                        </Column>

                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Modal de adicionar novo usuário -->
        <Dialog 
            v-model:visible="userDialog" 
            header="Adicionar Novo Usuário" 
            :modal="true" 
            :style="{ width: '450px' }"
            @hide="hideDialog"
        >
            <div class="flex flex-col gap-6">

                    <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div>
                        
                        <InputLabel for="email" value="Role" />
                        <Select 
                            v-model="form.role" 
                            :options="roles" 
                            optionLabel="label" 
                            placeholder="Selecione" 
                            class="w-full md:w-56" 
                        />
                        <InputError class="mt-2" :message="form.errors.roles" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />

                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Register
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Dialog>
    </AppLayout>
</template>
