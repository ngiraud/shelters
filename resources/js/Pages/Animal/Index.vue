<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, usePage} from '@inertiajs/vue3';
import {onMounted, ref, watch} from 'vue'
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow,} from '@/Components/ui/table'
import {Button} from '@/Components/ui/button'
import {Input} from '@/Components/ui/input'
import {Pagination, PaginationList, PaginationListItem,} from '@/Components/ui/pagination'
import {Popover, PopoverContent, PopoverTrigger,} from '@/Components/ui/popover'
import {RadioGroup, RadioGroupItem} from '@/Components/ui/radio-group'
import {Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList,} from '@/Components/ui/command'
import {DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger,} from '@/Components/ui/dropdown-menu'
import {Label} from '@/Components/ui/label'
import throttle from 'lodash/throttle';
import {cn} from '@/lib/utils';
import {CaretSortIcon, CheckIcon, DotsHorizontalIcon} from '@radix-icons/vue'

let animals = ref({
    data: [],
    links: {},
    meta: {
        links: []
    }
});

const species = ref([]);

const search = ref('');
const filteredGender = ref('');
const filteredSpecies = ref('')

const openedSpeciesFilter = ref(false)

let loading = ref(false);

const fetchAnimals = throttle(async (url = null) => {
    if (loading.value) {
        return;
    }

    if (url === null) {
        url = route('api.animal.index')
    }

    loading.value = true;

    try {
        const {data} = await axios.get(url);

        animals.value.data = data.data;
        animals.value.links = data.links;
        animals.value.meta = data.meta;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}, 250);

const fetchSpecies = async () => {
    try {
        const {data} = await axios.get(route('api.species.index'));

        species.value = data.data;
    } catch (error) {
        console.error(error);
    }
};

const handlePagination = (url = null) => {
    if (!url) {
        return;
    }

    fetchAnimals(url)
}

watch(search, (value) => {
    fetchAnimals(route('api.animal.index', {
        _query: {
            search: value,
            gender: filteredGender.value,
            species: filteredSpecies.value,
        }
    }));
})

watch(filteredGender, (value) => {
    fetchAnimals(route('api.animal.index', {
        _query: {
            search: search.value,
            gender: value,
            species: filteredSpecies.value,
        }
    }));
})

watch(filteredSpecies, (value) => {
    fetchAnimals(route('api.animal.index', {
        _query: {
            search: search.value,
            gender: filteredGender.value,
            species: value,
        }
    }));
})

onMounted(() => {
    fetchAnimals();
    fetchSpecies();
})
</script>

<template>
    <Head title="Animal List"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Animals in {{ usePage().props.auth.user.shelter.name }} shelter
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex gap-2">
                                <Input v-model="search" placeholder="Search..." class="max-w-xs"/>

                                <Popover v-model:open="openedSpeciesFilter">
                                    <PopoverTrigger as-child>
                                        <Button
                                                variant="outline"
                                                role="combobox"
                                                :aria-expanded="openedSpeciesFilter"
                                                class="w-[200px] justify-between"
                                        >
                                            {{
                                                filteredSpecies
                                                        ? species.find((sp) => sp.id === filteredSpecies)?.name
                                                        : "Filter species"
                                            }}
                                            <CaretSortIcon class="ml-2 h-4 w-4 shrink-0 opacity-50"/>
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[200px] p-0">
                                        <Command>
                                            <CommandInput class="h-9" placeholder="Search species..."/>
                                            <CommandEmpty>No species found.</CommandEmpty>
                                            <CommandList>
                                                <CommandGroup>
                                                    <CommandItem
                                                            v-for="sp in species"
                                                            :key="sp.id"
                                                            :value="sp.name"
                                                            @select="(ev) => {
                                                                if(sp.id === filteredSpecies) {
                                                                    filteredSpecies = ''
                                                                } else {
                                                                    filteredSpecies = sp.id
                                                                }
                                                                openedSpeciesFilter = false
                                                            }"
                                                    >
                                                        {{ sp.name }}
                                                        <CheckIcon
                                                                :class="cn(
                                                                  'ml-auto h-4 w-4',
                                                                  filteredSpecies === sp.id ? 'opacity-100' : 'opacity-0',
                                                                )"
                                                        />
                                                    </CommandItem>
                                                </CommandGroup>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>

                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button variant="outline">
                                            Filter gender
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent>
                                        <RadioGroup v-model="filteredGender" :default-value="filteredGender">
                                            <div class="flex items-center space-x-2">
                                                <RadioGroupItem id="all" value=""/>
                                                <Label for="all">All</Label>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <RadioGroupItem id="male" value="male"/>
                                                <Label for="male">Male</Label>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <RadioGroupItem id="female" value="female"/>
                                                <Label for="female">Female</Label>
                                            </div>
                                        </RadioGroup>
                                    </PopoverContent>
                                </Popover>
                            </div>

                            <div>
                                <Button as-child>
                                    <Link :href="route('animal.create')">Add</Link>
                                </Button>
                            </div>
                        </div>

                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Species</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead class="w-[100px]">Birthdate</TableHead>
                                    <TableHead class="w-[100px]">Gender</TableHead>
                                    <TableHead class="w-[50px]"></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="animals.data.length > 0" v-for="animal in animals.data">
                                    <TableCell v-text="animal.species.name"></TableCell>
                                    <TableCell class="font-medium" v-text="animal.name"></TableCell>
                                    <TableCell v-text="animal.birthdate_humans"></TableCell>
                                    <TableCell v-text="animal.gender_humans"></TableCell>
                                    <TableCell class="flex items-center justify-end w-[50px]">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger class="flex items-center justify-end">
                                                <DotsHorizontalIcon></DotsHorizontalIcon>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent>
                                                <DropdownMenuItem as-child>
                                                    <Link :href="route('animal.edit', {animal: animal.id})">Edit</Link>
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                                </TableRow>

                                <TableRow v-else>
                                    <TableCell colspan="5" class="h-10 text-center">
                                        No results
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <div class="flex justify-between items-center gap-5">
                            <p class="text-sm font-medium leading-none">
                                Showing {{ animals.meta.from }} to {{ animals.meta.to }} of {{ animals.meta.total }} animals
                            </p>

                            <Pagination>
                                <PaginationList class="flex items-center gap-1">
                                    <PaginationListItem v-for="(item, index) in animals.meta.links" :key="index" :value="item.value" as-child>
                                        <Button class="w-9 h-9 p-0" :variant="item.active ? 'default' : 'outline'" @click="handlePagination(item.url)">
                                            {{ item.label }}
                                        </Button>
                                    </PaginationListItem>
                                </PaginationList>
                            </Pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
