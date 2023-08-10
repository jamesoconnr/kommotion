<script setup>
import { Head, Link, useForm, } from '@inertiajs/vue3';
import { ref } from 'vue';
import SaveButton from '@/Components/SaveButton.vue'
import { onMounted } from 'vue';

const props = defineProps(['allNotes', 'auth', 'selectedNote']);

let sessionSelectedNote = useForm({
    id: null,
    content: null,
    name: null,
    created_at: null,
})

if (props.selectedNote === undefined){
} else {
    sessionSelectedNote = useForm({
        id: props.selectedNote.id,
        content: props.selectedNote.content,
        name: props.selectedNote.name,
        created_at: props.selectedNote.created_at,
    })
    const createdAtUTC = sessionSelectedNote.created_at
    const createdAt = (createdAtUTC.split('T')[0]).replace(/-/g, '/')
}

/* Display user name in nav bar */
const userName = props.auth.user.name
let apostrophe = '\'s'
if (userName.slice(-1) === 's'){
    apostrophe = '\''
}
/**/


const newNoteForm = useForm({
    name: '',
})


/* motion for komote */
onMounted(() => {
    let komoteElement = document.querySelector('#komoteElement')
    const komoteHeader = komoteElement.querySelector("header")

    const onDrag = ({movementX, movementY}) => {
        let getStyle = window.getComputedStyle(komoteElement)
        let leftVal = parseInt(getStyle.left)
        let topVal = parseInt(getStyle.top)
        komoteElement.style.left = `${leftVal + movementX}px`
        komoteElement.style.top = `${topVal + movementY}px`
    }
   
    komoteHeader.addEventListener('mousedown', () =>{
        komoteHeader.addEventListener('mousemove', onDrag)
    })
    document.addEventListener('mouseup', () =>{
        komoteHeader.removeEventListener('mousemove', onDrag)
    })
})
/**/

const autoExpandingTextarea = ref(null);
const resizeTextarea = () => {
    if (autoExpandingTextarea.value) {
        autoExpandingTextarea.value.style.height = 'auto';
        autoExpandingTextarea.value.style.height = autoExpandingTextarea.value.scrollHeight + 'px';
    }
}
const autoSaveKomote = ()=> {
    console.log('ok')
}

</script>

<template>
    <Head title="Dashboard" />
    <main class="min-h-[100svh] grid grid-cols-[15rem_1fr] bg-neutral-100 overflow-hidden">
        <div id="komoteElement" class="absolute w-56 min-h-[14rem] rounded-xl bg-amber-100 right-5 top-1/4 overflow-hidden">
            <header class="w-full bg-amber-200 rounded-t-xl font-bold p-2 text-center text-neutral-700 select-none cursor-move">komote</header>
            <form class="flex flex-col">
                <div class="h-full m-5">
                    <textarea name="komote" v-model="komoteContent" @input="resizeTextarea(); autoSaveKomote()" ref="autoExpandingTextarea" class="border-0 bg-transparent resize-none outline-none text font-extrabold text-neutral-600 remove-shadow w-full h-full" placeholder="Write anything..."></textarea>
                </div>
            </form>
        </div>
        <div class="bg-neutral-200 p-5 flex flex-col gap-5">
            <h2 class="font-bold text-4xl text-neutral-700">{{ userName }}{{ apostrophe }}<br>stuff</h2>
            <div class="bg-neutral-500 h-[1px]"></div>
            <ul class="flex flex-col-reverse gap-3 pl-5 [&>*]:font-bold  [&>*]:text-neutral-600">
                <Link :href="`/notes/${note.id}`" v-for="note in allNotes">{{ note.name }}</Link>
            </ul>
            <form class="mt-auto flex gap-4" @submit.prevent="sessionSelectedNote.put(route('notes.update'))">
                <input type="text" v-model="newNoteForm.name" class=" w-full">
                <button class="text-3xl select-none" >+</button>
            </form>
        </div>  
        <form v-if="sessionSelectedNote.id" class="p-10 flex flex-col" @submit.prevent="sessionSelectedNote.put(route('notes.update', sessionSelectedNote.id))">
            <div class="flex items-center gap-5">
                <input class="border-0 bg-transparent resize-none outline-none text-6xl font-extrabold text-neutral-600 remove-shadow w-2/3" v-model="sessionSelectedNote.name">
                <SaveButton />
                <span class="text-neutral-600 font-bold text-xl">{{ createdAt }}</span>
            </div>
            <div class="p-5 m-5 h-full">
                <textarea name='note' class="border-0 bg-transparent resize-none outline-none text-xl font-extrabold text-neutral-600 remove-shadow w-full h-full" v-model="sessionSelectedNote.content" placeholder="Write something..."></textarea>
            </div>
        </form>
        <div v-else class="h-full p-5 flex">
            <h1 class="font-bold text-5xl text-neutral-600 mt-auto">&lt;-Make your first note!</h1>
        </div>
    </main>
</template>
<style>
body{
    overflow: hidden;
}
.remove-shadow:focus{
    box-shadow: none !important;
}
</style>
