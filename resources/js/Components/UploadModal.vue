<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Upload, X, File, AlertCircle } from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";
import Input from "@/Components/ui/Input.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Select from "@/Components/ui/Select.vue";

const props = defineProps({
    categories: Object,
    modules: Object,
});

const emit = defineEmits(["close"]);

// Form data
const form = ref({
    name: "",
    file: null,
    category: "",
    module: "",
    description: "",
    tags: [],
    expires_at: "",
});

const fileInput = ref(null);
const isDragging = ref(false);
const errors = ref({});
const uploading = ref(false);

// File selection
const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.file = file;
        if (!form.value.name) {
            form.value.name = file.name.split(".")[0];
        }
    }
};

// Drag and drop
const handleDragOver = (event) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const handleDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    if (file) {
        form.value.file = file;
        if (!form.value.name) {
            form.value.name = file.name.split(".")[0];
        }
    }
};

// Submit
const submit = () => {
    errors.value = {};

    // Validação
    if (!form.value.name) {
        errors.value.name = "Nome é obrigatório";
        return;
    }
    if (!form.value.file) {
        errors.value.file = "Ficheiro é obrigatório";
        return;
    }
    if (!form.value.category) {
        errors.value.category = "Categoria é obrigatória";
        return;
    }

    uploading.value = true;

    const formData = new FormData();
    formData.append("name", form.value.name);
    formData.append("file", form.value.file);
    formData.append("category", form.value.category);
    if (form.value.module) formData.append("module", form.value.module);
    if (form.value.description)
        formData.append("description", form.value.description);
    if (form.value.expires_at)
        formData.append("expires_at", form.value.expires_at);

    router.post(route("digital-archive.store"), formData, {
        onSuccess: () => {
            emit("close");
        },
        onError: (err) => {
            errors.value = err;
            uploading.value = false;
        },
        onFinish: () => {
            uploading.value = false;
        },
    });
};

const formatFileSize = (bytes) => {
    if (!bytes) return "0 B";
    const units = ["B", "KB", "MB", "GB"];
    const power = bytes > 0 ? Math.floor(Math.log(bytes) / Math.log(1024)) : 0;
    return (
        Math.round((bytes / Math.pow(1024, power)) * 100) / 100 +
        " " +
        units[power]
    );
};
</script>

<template>
    <!-- Overlay -->
    <div
        class="fixed inset-0 bg-gray-900/80 z-50 flex items-center justify-center p-4"
        @click.self="emit('close')"
    >
        <!-- Modal -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
        >
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            Carregar Documento
                        </h2>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                        >
                            Faça upload de um documento para o arquivo digital
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        @click="emit('close')"
                    >
                        <X class="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="p-6 space-y-4">
                <!-- Área de Upload -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Ficheiro *
                    </label>
                    <div
                        @dragover="handleDragOver"
                        @dragleave="handleDragLeave"
                        @drop="handleDrop"
                        @click="() => fileInput.click()"
                        :class="[
                            'border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors',
                            isDragging
                                ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                                : 'border-gray-300 dark:border-gray-700 hover:border-purple-400',
                        ]"
                    >
                        <Upload class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                        <p
                            class="text-sm text-gray-600 dark:text-gray-400 mb-2"
                        >
                            Clique para selecionar ou arraste o ficheiro
                        </p>
                        <p class="text-xs text-gray-500">
                            Máximo 10MB • PDF, DOC, XLS, JPG, PNG
                        </p>
                        <input
                            ref="fileInput"
                            type="file"
                            class="hidden"
                            @change="handleFileSelect"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                        />
                    </div>

                    <!-- Ficheiro selecionado -->
                    <div
                        v-if="form.file"
                        class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <File class="h-5 w-5 text-gray-500" />
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ form.file.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ formatFileSize(form.file.size) }}
                                </p>
                            </div>
                        </div>
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            @click="form.file = null"
                        >
                            <X class="h-4 w-4" />
                        </Button>
                    </div>

                    <p v-if="errors.file" class="mt-1 text-sm text-red-600">
                        {{ errors.file }}
                    </p>
                </div>

                <!-- Nome -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Nome do Documento *
                    </label>
                    <Input
                        v-model="form.name"
                        placeholder="Ex: Contrato Cliente XYZ"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                        {{ errors.name }}
                    </p>
                </div>

                <!-- Categoria e Módulo -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Categoria *
                        </label>
                        <Select v-model="form.category">
                            <option value="">Selecione...</option>
                            <option
                                v-for="(label, key) in categories"
                                :key="key"
                                :value="key"
                            >
                                {{ label }}
                            </option>
                        </Select>
                        <p
                            v-if="errors.category"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.category }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Módulo
                        </label>
                        <Select v-model="form.module">
                            <option value="">Selecione...</option>
                            <option
                                v-for="(label, key) in modules"
                                :key="key"
                                :value="key"
                            >
                                {{ label }}
                            </option>
                        </Select>
                    </div>
                </div>

                <!-- Descrição -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Descrição
                    </label>
                    <Textarea
                        v-model="form.description"
                        placeholder="Descrição opcional do documento..."
                        rows="3"
                    />
                </div>

                <!-- Data de Expiração -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Data de Expiração (opcional)
                    </label>
                    <Input v-model="form.expires_at" type="date" />
                </div>

                <!-- Ações -->
                <div class="flex justify-end gap-3 pt-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="emit('close')"
                        :disabled="uploading"
                    >
                        Cancelar
                    </Button>
                    <Button
                        type="submit"
                        :disabled="uploading"
                        class="bg-purple-600 hover:bg-purple-700"
                    >
                        <Upload v-if="!uploading" class="h-4 w-4 mr-2" />
                        {{ uploading ? "A carregar..." : "Carregar Documento" }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
