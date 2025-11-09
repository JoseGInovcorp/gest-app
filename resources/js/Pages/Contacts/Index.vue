<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <Users class="h-8 w-8 text-green-600" />
                    <div>
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Contactos
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Gestão de contactos associados às entidades
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">
                        {{ contacts.total }}
                        {{ contacts.total === 1 ? "contacto" : "contactos" }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
                            >
                                <Home class="w-4 h-4 mr-2" />
                                Dashboard
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <ChevronRight
                                    class="w-4 h-4 text-gray-400 mx-1"
                                />
                                <span
                                    class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Contactos
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Data Table -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
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
                </div>
            </div>
        </div>

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
import { Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ContactsDataTable from "@/Components/ContactsDataTableNew.vue";
import Modal from "@/Components/Modal.vue";
import Button from "@/Components/ui/Button.vue";
import { Users, Home, ChevronRight, Trash2, Loader2 } from "lucide-vue-next";

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
