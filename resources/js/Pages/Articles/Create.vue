<script setup>
import { ref, computed } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Button from "@/Components/ui/Button.vue";
import { Package, ArrowLeft, X } from "lucide-vue-next";

const props = defineProps({
    proximaReferencia: String,
    opcoesIva: Array,
});

const form = useForm({
    referencia: props.proximaReferencia,
    nome: "",
    descricao: "",
    preco: "",
    iva_percentagem: "",
    observacoes: "",
    estado: "ativo",
    foto: null,
});

const previewUrl = ref(null);
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
    form.post(route("articles.store"), {
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
    <Head title="Criar Artigo" />

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
                                Criar Artigo
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Adicionar novo produto ou serviço
                            </p>
                        </div>
                    </div>
                    <Button
                        variant="outline"
                        @click="$inertia.visit(route('articles.index'))"
                        class="flex items-center space-x-2"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        <span>Voltar à Lista</span>
                    </Button>
                </div>
            </div>

            <div
                class="rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6"
            >
                <Form @submit.prevent="handleSubmit">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <FormField
                            id="referencia"
                            label="Referência"
                            description="Código gerado automaticamente"
                        >
                            <Input
                                v-model="form.referencia"
                                id="referencia"
                                type="text"
                                readonly
                                class="bg-gray-50 dark:bg-gray-800"
                                :placeholder="proximaReferencia"
                            />
                        </FormField>

                        <FormField
                            id="estado"
                            label="Estado"
                            required
                            :error="form.errors.estado"
                            description="Estado do artigo no sistema"
                        >
                            <Select v-model="form.estado" id="estado" required>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </Select>
                        </FormField>

                        <FormField
                            id="nome"
                            label="Nome"
                            required
                            :error="form.errors.nome"
                            description="Designação do artigo"
                            class="lg:col-span-2"
                        >
                            <Input
                                v-model="form.nome"
                                id="nome"
                                placeholder="Nome do produto/serviço"
                                required
                            />
                        </FormField>

                        <FormField
                            id="descricao"
                            label="Descrição"
                            :error="form.errors.descricao"
                            description="Descrição detalhada (opcional)"
                            class="lg:col-span-2"
                        >
                            <Textarea
                                v-model="form.descricao"
                                id="descricao"
                                placeholder="Descrição detalhada do artigo..."
                                rows="4"
                            />
                        </FormField>

                        <FormField
                            id="preco"
                            label="Preço"
                            required
                            :error="form.errors.preco"
                            description="Preço base sem IVA (€)"
                        >
                            <Input
                                v-model="form.preco"
                                id="preco"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                required
                            />
                        </FormField>

                        <FormField
                            id="iva_percentagem"
                            label="IVA"
                            required
                            :error="form.errors.iva_percentagem"
                            description="Taxa de IVA aplicável"
                        >
                            <Select
                                v-model="form.iva_percentagem"
                                id="iva_percentagem"
                                required
                            >
                                <option value="">Selecionar taxa IVA</option>
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
                            id="preco_com_iva"
                            label="Preço Final (com IVA)"
                            description="Valor calculado automaticamente"
                        >
                            <div
                                class="flex items-center h-10 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200"
                            >
                                <span class="font-medium"
                                    >{{ precoComIva.toFixed(2) }}€</span
                                >
                            </div>
                        </FormField>

                        <FormField
                            id="foto"
                            label="Foto"
                            :error="form.errors.foto"
                            description="Imagem do artigo (JPEG, PNG, JPG, GIF - máx. 2MB)"
                            class="lg:col-span-2"
                        >
                            <div class="space-y-4">
                                <Input
                                    type="file"
                                    accept="image/*"
                                    @change="handleFileChange"
                                    ref="fileInput"
                                    id="foto"
                                />
                                <div
                                    v-if="previewUrl"
                                    class="relative w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden"
                                >
                                    <img
                                        :src="previewUrl"
                                        alt="Preview"
                                        class="w-full h-full object-cover"
                                    />
                                    <button
                                        type="button"
                                        @click="removeImage"
                                        class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
                                    >
                                        <X class="h-3 w-3" />
                                    </button>
                                </div>
                            </div>
                        </FormField>

                        <FormField
                            id="observacoes"
                            label="Observações"
                            :error="form.errors.observacoes"
                            description="Notas internas (opcional)"
                            class="lg:col-span-2"
                        >
                            <Textarea
                                v-model="form.observacoes"
                                id="observacoes"
                                placeholder="Observações adicionais..."
                                rows="3"
                            />
                        </FormField>
                    </div>

                    <div class="mt-8 flex items-center justify-end space-x-3">
                        <Button
                            type="button"
                            variant="outline"
                            @click="router.visit(route('articles.index'))"
                            :disabled="form.processing"
                        >
                            Cancelar
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing || !isFormValid"
                        >
                            {{
                                form.processing
                                    ? "A guardar..."
                                    : "Guardar Artigo"
                            }}
                        </Button>
                    </div>

                    <div
                        v-if="!isFormValid"
                        class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-md"
                    >
                        <p class="text-sm text-amber-700 dark:text-amber-200">
                            ℹ️ Preencha os campos obrigatórios para continuar.
                        </p>
                    </div>
                </Form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
