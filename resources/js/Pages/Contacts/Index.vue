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
            :contacts="contacts"
            :entities="entities"
            :filters="filters"
            :can-create="can.create"
            :can-view="can.view"
            :can-edit="can.edit"
            :can-delete="can.delete"
            @view="handleView"
            @edit="handleEdit"
            @delete="handleDelete"
            @create="handleCreate"
        />

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            :show="showDeleteDialog"
            type="danger"
            title="Eliminar Contacto"
            message="Tem certeza que pretende eliminar este contacto? Esta ação não pode ser desfeita."
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            :is-processing="processing"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ContactsDataTable from "@/Components/ui/ContactsDataTable.vue";
import ConfirmDialog from "@/Components/ConfirmDialog.vue";
import { Users } from "lucide-vue-next";

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
const showDeleteDialog = ref(false);
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
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    if (!contactToDelete.value) return;

    processing.value = true;

    router.delete(route("contacts.destroy", contactToDelete.value), {
        onFinish: () => {
            showDeleteDialog.value = false;
            contactToDelete.value = null;
            processing.value = false;
        },
    });
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    contactToDelete.value = null;
    processing.value = false;
};
</script>
