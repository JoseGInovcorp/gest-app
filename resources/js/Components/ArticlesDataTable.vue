<template>
    <div class="overflow-x-auto">
        <table
            class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
        >
            <thead
                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
            >
                <tr>
                    <th
                        scope="col"
                        class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                        @click="$emit('sort', 'referencia')"
                    >
                        <div class="flex items-center gap-2">
                            Referência
                            <ArrowUpDown class="h-3 w-3" />
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">Foto</th>
                    <th
                        scope="col"
                        class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                        @click="$emit('sort', 'nome')"
                    >
                        <div class="flex items-center gap-2">
                            Nome
                            <ArrowUpDown class="h-3 w-3" />
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">Descrição</th>
                    <th
                        scope="col"
                        class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                        @click="$emit('sort', 'preco')"
                    >
                        <div class="flex items-center gap-2">
                            Preço
                            <ArrowUpDown class="h-3 w-3" />
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">IVA</th>
                    <th
                        scope="col"
                        class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                        @click="$emit('sort', 'estado')"
                    >
                        <div class="flex items-center gap-2">
                            Estado
                            <ArrowUpDown class="h-3 w-3" />
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="article in articles.data"
                    :key="article.id"
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                    <!-- Referência -->
                    <td
                        class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                    >
                        {{ article.referencia }}
                    </td>

                    <!-- Foto -->
                    <td class="px-6 py-4">
                        <div
                            class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg overflow-hidden"
                        >
                            <img
                                v-if="article.foto"
                                :src="`/storage/${article.foto}`"
                                :alt="article.nome"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center"
                            >
                                <Package class="h-6 w-6 text-gray-400" />
                            </div>
                        </div>
                    </td>

                    <!-- Nome -->
                    <td
                        class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                    >
                        {{ article.nome }}
                    </td>

                    <!-- Descrição -->
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                        <div class="max-w-xs truncate">
                            {{ article.descricao || "-" }}
                        </div>
                    </td>

                    <!-- Preço -->
                    <td
                        class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                    >
                        {{ formatPrice(article.preco) }}
                    </td>

                    <!-- IVA -->
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                        {{ article.iva_percentagem }}%
                    </td>

                    <!-- Estado -->
                    <td class="px-6 py-4">
                        <span
                            :class="[
                                'px-2 py-1 text-xs font-medium rounded-full',
                                article.estado === 'ativo'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                            ]"
                        >
                            {{
                                article.estado === "ativo" ? "Ativo" : "Inativo"
                            }}
                        </span>
                    </td>

                    <!-- Ações -->
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center gap-2 justify-end">
                            <Link
                                :href="route('articles.show', article.id)"
                                class="p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                title="Ver detalhes"
                            >
                                <Eye class="h-4 w-4" />
                            </Link>
                            <Link
                                :href="route('articles.edit', article.id)"
                                class="p-2 text-gray-500 hover:text-yellow-600 dark:text-gray-400 dark:hover:text-yellow-400 transition-colors"
                                title="Editar"
                            >
                                <Edit class="h-4 w-4" />
                            </Link>
                            <button
                                @click="confirmDelete(article)"
                                class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
                                title="Eliminar"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Estado vazio -->
                <tr v-if="!articles.data || articles.data.length === 0">
                    <td
                        colspan="8"
                        class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
                    >
                        <div class="flex flex-col items-center gap-2">
                            <Package class="h-12 w-12 text-gray-300" />
                            <p>Nenhum artigo encontrado.</p>
                            <Link
                                :href="route('articles.create')"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                Criar primeiro artigo
                            </Link>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Paginação -->
        <div
            v-if="articles.data && articles.data.length > 0"
            class="px-6 py-4 border-t border-gray-200 dark:border-gray-700"
        >
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Mostrando {{ articles.from }} a {{ articles.to }} de
                    {{ articles.total }} artigos
                </div>

                <div class="flex gap-1">
                    <Link
                        v-for="link in articles.links"
                        :key="link.label"
                        :href="link.url"
                        preserve-state
                        :class="[
                            'px-3 py-2 text-sm border rounded transition-colors',
                            link.active
                                ? 'bg-blue-600 border-blue-600 text-white'
                                : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                            !link.url && 'opacity-50 cursor-not-allowed',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

// Icons
import { Package, ArrowUpDown, Eye, Edit, Trash2 } from "lucide-vue-next";

// Props
defineProps({
    articles: Object,
    sort: Object,
});

// Emits
defineEmits(["sort"]);

// Funções
const formatPrice = (price) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(price);
};

const confirmDelete = (article) => {
    if (
        confirm(`Tem certeza que deseja eliminar o artigo "${article.nome}"?`)
    ) {
        router.delete(route("articles.destroy", article.id), {
            onSuccess: () => {
                // Mensagem será exibida pelo flash message
            },
        });
    }
};
</script>
