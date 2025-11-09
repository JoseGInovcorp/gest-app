<script setup>
import { ref, computed } from "vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Button from "@/Components/ui/Button.vue";
import { Package, ArrowLeft, X } from "lucide-vue-next";

const props = defineProps({
    article: Object,
    opcoesIva: Array,
});

const form = useForm({
    referencia: props.article.referencia,
    nome: props.article.nome,
    descricao: props.article.descricao || "",
    preco: props.article.preco,
    iva_percentagem: props.article.iva_percentagem.toString(),
    observacoes: props.article.observacoes || "",
    estado: props.article.estado,
    foto: null,
    _method: "PATCH",
});

const previewUrl = ref(props.article.foto_url || null);
const fileInput = ref(null);

const isFormValid = computed(() => {
    return form.nome && form.preco > 0 && form.iva_percentagem && form.estado;
});

const precoComIva = computed(() => {
    const preco = parseFloat(form.preco) || 0;
    const iva = parseFloat(form.iva_percentagem) || 0;
    return preco * (1 + iva / 100);
});

const handleSubmit = () => {
    form.post(route("articles.update", props.article.id), {
        forceFormData: true,
    });
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.foto = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            previewUrl.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.foto = null;
    previewUrl.value = null;
    if (fileInput.value) {
        fileInput.value.value = "";
    }
};
</script>

<template>
    <Head title="Editar Artigo" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600/10 dark:bg-blue-400/10"
                        >
                            <Package
                                class="h-6 w-6 text-blue-600 dark:text-blue-400"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                Editar Artigo
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Atualizar dados do produto ou serviço
                            </p>
                        </div>
                    </div>
                    <Link :href="route('articles.index')">
                        <Button
                            variant="outline"
                            class="flex items-center space-x-2"
                        >
                            <ArrowLeft class="h-4 w-4" />
                            <span>Voltar à Lista</span>
                        </Button>
                    </Link>
                </div>
            </div>

            <div
                class="rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm"
            >
                <Form @submit="handleSubmit">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <FormField
                                label="Referência"
                                helper="Referência automática do artigo"
                                :required="true"
                            >
                                <Input
                                    v-model="form.referencia"
                                    type="text"
                                    readonly
                                    disabled
                                    class="bg-gray-50 dark:bg-gray-800"
                                />
                            </FormField>

                            <FormField
                                label="Nome"
                                helper="Nome do artigo"
                                :required="true"
                                :error="form.errors.nome"
                            >
                                <Input
                                    v-model="form.nome"
                                    type="text"
                                    placeholder="Nome do artigo"
                                />
                            </FormField>
                        </div>

                        <FormField
                            label="Descrição"
                            helper="Descrição detalhada do artigo"
                            :error="form.errors.descricao"
                        >
                            <Textarea
                                v-model="form.descricao"
                                rows="3"
                                placeholder="Descrição do artigo"
                            />
                        </FormField>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <FormField
                                label="Preço (€)"
                                helper="Preço base do artigo"
                                :required="true"
                                :error="form.errors.preco"
                            >
                                <Input
                                    v-model="form.preco"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                />
                            </FormField>

                            <FormField
                                label="IVA (%)"
                                helper="Taxa de IVA aplicável"
                                :required="true"
                                :error="form.errors.iva_percentagem"
                            >
                                <Select v-model="form.iva_percentagem">
                                    <option value="">Selecione...</option>
                                    <option
                                        v-for="opcao in opcoesIva"
                                        :key="opcao.value"
                                        :value="opcao.value"
                                    >
                                        {{ opcao.label }}
                                    </option>
                                </Select>
                            </FormField>

                            <FormField
                                label="Preço Final (com IVA)"
                                helper="Valor calculado automaticamente"
                            >
                                <div
                                    class="flex items-center h-10 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200"
                                >
                                    <span class="font-medium"
                                        >{{ precoComIva.toFixed(2) }}€</span
                                    >
                                </div>
                            </FormField>
                        </div>

                        <FormField
                            label="Foto"
                            helper="Imagem do artigo (JPEG, PNG, GIF - máx 2MB)"
                            :error="form.errors.foto"
                        >
                            <div class="space-y-4">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    @change="handleFileChange"
                                    accept="image/jpeg,image/png,image/jpg,image/gif"
                                    class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 focus:outline-none"
                                />

                                <div
                                    v-if="previewUrl"
                                    class="relative inline-block"
                                >
                                    <img
                                        :src="previewUrl"
                                        alt="Preview"
                                        class="h-32 w-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
                                    />
                                    <button
                                        @click="removeImage"
                                        type="button"
                                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1"
                                    >
                                        <X class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        </FormField>

                        <FormField
                            label="Observações"
                            helper="Notas adicionais sobre o artigo"
                            :error="form.errors.observacoes"
                        >
                            <Textarea
                                v-model="form.observacoes"
                                rows="3"
                                placeholder="Observações..."
                            />
                        </FormField>

                        <FormField
                            label="Estado"
                            helper="Estado do artigo no sistema"
                            :required="true"
                            :error="form.errors.estado"
                        >
                            <Select v-model="form.estado">
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </Select>
                        </FormField>

                        <div
                            class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800"
                        >
                            <Link :href="route('articles.index')">
                                <Button
                                    type="button"
                                    variant="outline"
                                    :disabled="form.processing"
                                >
                                    Cancelar
                                </Button>
                            </Link>
                            <Button
                                type="submit"
                                :disabled="!isFormValid || form.processing"
                            >
                                {{
                                    form.processing
                                        ? "A atualizar..."
                                        : "Atualizar Artigo"
                                }}
                            </Button>
                        </div>
                    </div>
                </Form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
