import re
import os

# Definir as páginas e suas permissões
pages = [
    {"path": "Countries/Index.vue", "module": "countries"},
    {"path": "ContactFunctions/Index.vue", "module": "contact-functions"},
    {"path": "VatRates/Index.vue", "module": "vat-rates"},
    {"path": "Users/Index.vue", "module": "users"},
    {"path": "Roles/Index.vue", "module": "roles"},
]

base_path = r"c:\Inovcorp\gest-app\resources\js\Pages"

for page in pages:
    file_path = os.path.join(base_path, page["path"])
    module = page["module"]
    
    if not os.path.exists(file_path):
        print(f"❌ Arquivo não encontrado: {file_path}")
        continue
    
    with open(file_path, "r", encoding="utf-8") as f:
        content = f.read()
    
    # 1. Adicionar inject no import de vue
    if "inject" not in content:
        content = re.sub(
            r"from ['\"]vue['\"]",
            lambda m: m.group(0).replace('from "vue"', 'from "vue"').replace("'vue'", "'vue'").replace(
                'import { ref', 'import { ref, inject'
            ) if 'inject' not in m.group(0) else m.group(0),
            content
        )
        
        # Fallback: adicionar inject manualmente após import { ... } from "vue"
        content = re.sub(
            r'(import\s*\{)([^}]+)(\}\s*from\s*["\']vue["\'])',
            lambda m: f'{m.group(1)}{m.group(2)}, inject{m.group(3)}' if 'inject' not in m.group(2) else m.group(0),
            content
        )
    
    # 2. Adicionar hasPermission inject após Props ou após imports
    if "const hasPermission = inject" not in content:
        # Procurar por "// Props" e adicionar antes
        if "// Props" in content:
            content = re.sub(
                r"(// Props)",
                r'// Inject permission checker\nconst hasPermission = inject("hasPermission");\n\n\1',
                content
            )
        else:
            # Adicionar após defineProps
            content = re.sub(
                r"(const props = defineProps\(\{[^}]+\}\);)",
                r'\1\n\n// Inject permission checker\nconst hasPermission = inject("hasPermission");',
                content
            )
    
    # 3. Adicionar v-if no botão create
    # Encontrar padrões de botão criar
    create_patterns = [
        (rf'(<Link\s+:href="route\(\'{module}\.create\'\)">)', rf'<Link v-if="hasPermission(\'{module}.create\')" :href="route(\'{module}.create\')">'),
        (rf'(<Link\s+:href="route\(\'{module}\.create\'\)")', rf'<Link v-if="hasPermission(\'{module}.create\')" :href="route(\'{module}.create\')"'),
    ]
    
    for pattern, replacement in create_patterns:
        if re.search(pattern, content) and "v-if" not in re.search(pattern, content).group(0):
            content = re.sub(pattern, replacement, content)
    
    # 4. Adicionar v-if nos botões edit e delete dentro do loop
    # Padrão para editar
    edit_pattern = rf'(<Link\s+:href="route\(\'{module}\.edit\'[^>]*>)'
    if re.search(edit_pattern, content):
        matches = list(re.finditer(edit_pattern, content))
        for match in reversed(matches):  # Reverso para não afetar índices
            original = match.group(0)
            if "v-if" not in original:
                new_link = original.replace("<Link ", f'<Link v-if="hasPermission(\'{module}.update\')" ')
                content = content[:match.start()] + new_link + content[match.end():]
    
    # Padrão para eliminar (button com @click delete)
    delete_pattern = r'(<button\s+@click="delete[^"]*"[^>]*>)'
    if re.search(delete_pattern, content):
        matches = list(re.finditer(delete_pattern, content))
        for match in reversed(matches):
            original = match.group(0)
            if "v-if" not in original and "Trash" in content[match.end():match.end()+200]:
                new_button = original.replace("<button ", f'<button v-if="hasPermission(\'{module}.delete\')" ')
                content = content[:match.start()] + new_button + content[match.end():]
    
    # Salvar arquivo
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    
    print(f"✅ Atualizado: {page['path']}")

print("\n✨ Todas as páginas foram atualizadas!")
