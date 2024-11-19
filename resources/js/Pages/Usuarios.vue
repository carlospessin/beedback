<script setup>
import { ref, onMounted } from 'vue';
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import { useToast } from 'primevue/usetoast';

// Recebendo os usuários do Laravel
const props = defineProps({
    users: Array,
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

console.log(roles)


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
  return role ? role.label : 'Unknown';  // Se não encontrar, retorna 'Unknown'.
};


const hideDialog = () => {
  userDialog.value = false;
  submitted.value = false;
};
</script>

<template>
    <AppLayout title="Usuários">
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
                <div>
                    <label for="name" class="block font-bold mb-2">Nome</label>
                    <InputText 
                        id="name" 
                        v-model="newUser.name" 
                        required 
                        autofocus 
                        :class="{ 'p-invalid': submitted && !newUser.name }" 
                        placeholder="Digite o nome"
                    />
                    <small v-if="submitted && !newUser.name" class="text-red-500">Nome é obrigatório.</small>
                </div>
                <div>
                    <label for="email" class="block font-bold mb-2">Email</label>
                    <InputText 
                        id="email" 
                        v-model="newUser.email" 
                        required 
                        :class="{ 'p-invalid': submitted && !newUser.email }" 
                        placeholder="Digite o email"
                    />
                    <small v-if="submitted && !newUser.email" class="text-red-500">Email é obrigatório.</small>
                </div>
                <div>
                    <label for="role" class="block font-bold mb-2">Role</label>
                    <Dropdown 
                        id="role"
                        v-model="newUser.role" 
                        :options="roles" 
                        optionLabel="label" 
                        optionValue="value" 
                        placeholder="Selecione o role"
                        :class="{ 'p-invalid': submitted && !newUser.role }"
                        />
                    <small v-if="submitted && !newUser.role" class="text-red-500">Role é obrigatório.</small>
                </div>
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Salvar" icon="pi pi-check" @click="saveUser" />
            </template>
        </Dialog>
    </AppLayout>
</template>
