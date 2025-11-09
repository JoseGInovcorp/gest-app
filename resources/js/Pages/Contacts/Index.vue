<template>
    <Head title="Contactos" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <Users class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Contactos
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gerir contactos associados às entidades
                    </p>
                </div>
            </div>
        </div>

        <!-- Breadcrumbs -->
        <nav class="mb-6">
            <ol
                class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400"
            >
                <li>
                    <Link
                        :href="route('dashboard')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Dashboard
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Contactos</li>
            </ol>
        </nav>

        <!-- Data Table -->
        <ContactsDataTable
            :contacts="contacts.data"
            :can-create="can.create"
            :can-view="can.view"
            :can-edit="can.edit"
            :can-delete="can.delete"
            @edit="handleEdit"
            @delete="handleDelete"
            @create="handleCreate"
        />

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div
                        class="flex-shrink-0 w-10 h-10 mx-auto bg-red-100 rounded-full flex items-center justify-center dark:bg-red-900"
                    >
                        <Trash2
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                </div>
                <div class="text-center">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2"
                    >
                        Eliminar Contacto
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Tem certeza que pretende eliminar este contacto? Esta
                        ação não pode ser desfeita.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <Button
                            variant="outline"
                            @click="closeDeleteModal"
                            :disabled="processing"
                        >
                            Cancelar
                        </Button>
                        <Button
                            variant="destructive"
                            @click="confirmDelete"
                            :disabled="processing"
                        >
                            <Loader2
                                v-if="processing"
                                class="w-4 h-4 mr-2 animate-spin"
                            />
                            Eliminar
                        </Button>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ContactsDataTable from "@/Components/ContactsDataTableNew.vue";
import Modal from "@/Components/Modal.vue";
import Button from "@/Components/ui/Button.vue";
import { Users, Trash2, Loader2 } from "lucide-vue-next";

const props = defineProps({
    contacts: {
        type: Object,
        required: true,
    },
    entities: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    can: {
        type: Object,
        default: () => ({
            view: true,
            create: true,
            edit: true,
            delete: true,
        }),
    },
});

// Estado para modal de confirmação
const showDeleteModal = ref(false);
const contactToDelete = ref(null);
const processing = ref(false);

// Handlers
const handleView = (contactId) => {
    router.visit(route("contacts.show", contactId));
};

const handleEdit = (contactId) => {
    router.visit(route("contacts.edit", contactId));
};

const handleCreate = () => {
    router.visit(route("contacts.create"));
};

const handleDelete = (contactId) => {
    contactToDelete.value = contactId;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!contactToDelete.value) return;

    processing.value = true;

    router.delete(route("contacts.destroy", contactToDelete.value), {
        onSuccess: () => {
            closeDeleteModal();
        },
        onError: () => {
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    contactToDelete.value = null;
    processing.value = false;
};
</script>
