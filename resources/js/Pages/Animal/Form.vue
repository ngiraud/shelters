<script setup lang="ts">
import {router} from '@inertiajs/vue3';
import {onMounted, ref} from 'vue'
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {Alert, AlertDescription, AlertTitle} from '@/components/ui/alert'
import {ExclamationTriangleIcon} from '@radix-icons/vue'
import {Button} from "@/Components/ui/button";
import {Label} from "@/Components/ui/label";
import {RadioGroup, RadioGroupItem} from "@/Components/ui/radio-group";

const species = ref([]);

const props = defineProps({
    animal: Object
});

let processing = ref(false);

const form = ref({
    name: props.animal?.name ?? '',
    species_id: props.animal?.species_id ?? '',
    description: props.animal?.description ?? '',
    birthdate: props.animal?.birthdate ?? '',
    gender: props.animal?.gender ?? '',
})

const errors = ref({
    errors: {},
    message: ''
})

const handleForm = async () => {
    if (processing.value) {
        return;
    }

    processing.value = true

    errors.value.errors = {}
    errors.value.message = ''

    try {
        const url = props.animal?.id
            ? route('api.animal.update', {animal: props.animal.id})
            : route('api.animal.store');

        const method = props.animal?.id ? 'put' : 'post';

        await axios.request({
            method: method,
            url: url,
            data: form.value
        });

        router.get(route('animal.index'))
    } catch (error) {
        errors.value.errors = error.response.data.errors;
        errors.value.message = error.response.data.message;
    } finally {
        processing.value = false;
    }
};

const fetchSpecies = async () => {
    try {
        const {data} = await axios.get(route('api.species.index'));

        species.value = data.data;
    } catch (error) {
        console.log(error)
    }
};

onMounted(() => {
    fetchSpecies();
})
</script>

<template>
    <form
            @submit.prevent="handleForm()"
            class="space-y-6"
    >
        <Alert v-if="errors.message" variant="destructive">
            <ExclamationTriangleIcon class="w-4 h-4"/>
            <AlertTitle>Error</AlertTitle>
            <AlertDescription>
                <p v-for="error in errors.errors" v-text="error[0]"></p>
            </AlertDescription>
        </Alert>

        <div>
            <InputLabel for="name" value="Name"/>

            <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    autofocus
            />
        </div>

        <div>
            <InputLabel for="species_id" value="Species"/>

            <select v-model="form.species_id"
                    id="species_id"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 mt-1 block w-full"
            >
                <option disabled selected>Select a species</option>
                <option v-for="sp in species" :value="sp.id" v-text="sp.name" :selected="sp.id === form.species_id"></option>
            </select>
        </div>

        <div>
            <InputLabel for="description" value="Description"/>

            <textarea v-model="form.description"
                      id="description"
                      class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 mt-1 block w-full"
            >
                            </textarea>
        </div>

        <div>
            <InputLabel for="birthdate" value="Birthdate"/>

            <TextInput
                    id="birthdate"
                    type="date"
                    class="mt-1 block w-full"
                    v-model="form.birthdate"
            />
        </div>

        <div class="flex gap-8">
            <InputLabel for="gender" value="Gender"/>

            <RadioGroup v-model="form.gender" class="mt-1">
                <div class="flex items-center space-x-2">
                    <RadioGroupItem id="male" value="male"/>
                    <Label for="male">Male</Label>
                </div>
                <div class="flex items-center space-x-2">
                    <RadioGroupItem id="female" value="female"/>
                    <Label for="female">Female</Label>
                </div>
            </RadioGroup>
        </div>

        <div class="flex items-center gap-4">
            <Button type="submit" :disabled="processing">Save</Button>
        </div>
    </form>
</template>
