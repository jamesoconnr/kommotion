
<script setup>
import { Head, Link, useForm, } from '@inertiajs/vue3';
import { ref } from 'vue';
import SaveButton from '@/Components/SaveButton.vue'
import FABrushIcon from '@/Components/FABrushIcon.vue';
import FAEraserIcon from '@/Components/FAEraserIcon.vue'
import FAEyeSlash from '@/Components/FAEyeSlash.vue';
import FATrashIcon from '@/Components/FATrashIcon.vue'
import { onMounted } from 'vue';
import { faL } from '@fortawesome/free-solid-svg-icons';

const props = defineProps(['auth', 'allNotes', 'selectedNote', 'userKomote']);

const canvasRef = ref(null);
const context = ref(null);
let isDrawing = false;
const showCanvas = ref(false)
const canvasDisplay = ref(true)
const strokeColor = ref('#A3A3A3')
const strokeWidth = ref(3)
const note = ref(null)
const showHelp = ref(false)

let sessionSelectedNote = useForm({
    id: null,
    content: null,
    name: null,
    created_at: null,
    canvas: null
})

try {
    sessionSelectedNote = useForm({
        id: props.selectedNote.id,
        content: props.selectedNote.content,
        name: props.selectedNote.name,
        created_at: props.selectedNote.created_at,
        canvas: props.selectedNote.canvas
    })
} catch (e){

}
const createdAtUTC = sessionSelectedNote.created_at
try {
    const createdAt = (createdAtUTC.split('T')[0]).replace(/-/g, '/')
} catch (error) {
    
}

const komoteForm = useForm({
    content: '',
    id: null
})

try {
    komoteForm.content = props.userKomote.content
    komoteForm.id = props.userKomote.id
} catch(err){

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

const saveNote = () => {
    sessionSelectedNote.canvas = canvasRef.value.toDataURL()
    sessionSelectedNote.put(route('notes.update', sessionSelectedNote.id))
    /*sessionSelectedNote.put(route('notes.update', sessionSelectedNote.id))*/
}

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


/* Drawing on note */

let pngDataUrl = sessionSelectedNote.canvas // Replace with your PNG data URL
let img = new Image();
img.src = pngDataUrl;
img.onload = (() => {
    context.value.drawImage(img, 0, 0, canvasRef.value.width, canvasRef.value.height)
})

/*set color for drawing*/
const setColor = (color) => {
    if (strokeColor.value !== color){
        strokeColor.value = color
    } else {
        strokeColor.value = '#A3A3A3'
    }
}
/**/

/*set stroke size*/
const increaseSize = () => {
    if (strokeWidth.value <= 15) {
        strokeWidth.value = ++strokeWidth.value
    } 
}

const decreaseSize = () => {
    if (strokeWidth.value > 3) {
        strokeWidth.value = --strokeWidth.value
    }
}
/**/

const toggleCanvasDisplay = () => {
    canvasDisplay.value = !canvasDisplay.value
    if (canvasDisplay.value) {
        canvasRef.value.style = 'display:block'
    } else {
        canvasRef.value.style = 'display:none'
    }
}

onMounted(() => {
    let canvas = canvasRef.value;
    context.value = canvas.getContext('2d');
    context.value.lineCap = "round";

    canvas.width = canvas.offsetWidth
    canvas.height = canvas.offsetHeight

});

const clearCanvas = () => {
    if (confirm('Are you sure you want to completely clear the canvas') === true) {
        context.value.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height);
    } else {
        
    }
}

const toggleDrawingMode = () => {
    showCanvas.value = !showCanvas.value
}

const draw = (e) => {
    if (!isDrawing) return
    context.value.strokeStyle = strokeColor.value
    context.value.lineWidth = strokeWidth.value
    context.value.lineTo(e.offsetX, e.offsetY)
    context.value.stroke()
}

const startDrawing = () => {
    isDrawing = true
    context.value.beginPath()
}

const stopDrawing = () => {
    isDrawing = false
}

/**/

const handleKeybind = (event) => {
    if (event.key === 'l'){
    }
}

</script>

<template>
    <Head title="Dashboard" />
    <main class="min-h-[100svh] grid grid-cols-[15rem_1fr] bg-neutral-100 overflow-hidden">
        <div v-if="sessionSelectedNote.id" id="komoteElement" class="z-20 absolute w-56 min-h-[14rem] rounded-xl bg-amber-100 right-10 top-[15%] overflow-hidden">
            <header class="w-full bg-amber-200 rounded-t-xl font-bold p-2 text-center text-neutral-700 select-none cursor-move">komote</header>
            <form class="flex flex-col">
                <div class="h-full m-3">
                    <textarea name="komote" 
                        v-model="komoteForm.content"
                        @input="resizeTextarea(); komoteForm.put(route('komote.update', komoteForm.id))"
                        ref="autoExpandingTextarea"
                        class="border-0 bg-transparent resize-none outline-none text font-extrabold text-neutral-600 remove-shadow w-full h-full"
                        placeholder="Write anything..."
                    ></textarea>
                </div>
            </form>
        </div>
        <div class="bg-neutral-200 p-5 flex flex-col gap-5">
            <h2 class="font-bold text-4xl text-neutral-700">{{ userName }}{{ apostrophe }}<br>stuff</h2>
            <div class="bg-neutral-500 h-[1px]"></div>
            <ul class="flex flex-col-reverse gap-3 pl-5 [&>*]:font-bold  [&>*]:text-neutral-600">
                <Link :href="`/notes/${note.id}`" v-for="note in allNotes">{{ note.name }}</Link>
            </ul>
            <span @click="showHelp = !showHelp" class="font-bold text-neutral-700 mt-auto underline underline-offset-3 text-sm">how do i use kommotion?</span>
                <div v-if="showHelp" class="p-10 font-bold text-neutral-600 absolute z-30 h-2/3 bg-neutral-300 rounded-xl top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <h3 class="underline">Making a note:</h3>
                        <p>Type your note name in the bottom left of the screen and click the + to the right of the name input.</p>
                    <h3 class="underline">Editing & saving a note:</h3>
                        <p>Both the title and </p>
                    <h3 class="mb-2 underline">Keybinds:</h3>
                    <ul class="ml-3">
                        <li class="list-item">Create unordered list: Control +  l</li>
                    </ul>
                </div>
            <form class="flex gap-4" @submit.prevent="newNoteForm.post(route('notes.store'))">
                <input type="text" v-model="newNoteForm.name" class=" w-full">
                <button class="text-3xl select-none" >+</button>
            </form>
        </div>
        <div v-if="sessionSelectedNote.id" class="relative">
            <form class="p-10 flex flex-col h-full" @submit.prevent="saveNote">
                <div class="flex items-center gap-5">
                    <input class="border-0 bg-transparent resize-none outline-none text-6xl font-extrabold text-neutral-600 remove-shadow w-2/3"
                        v-model="sessionSelectedNote.name"
                    >
                    <SaveButton />
                    <span class="text-neutral-600 font-bold text-xl">{{ createdAt }}</span>
                </div>
                <div class="p-5 m-5 h-full relative">
                    <div class="z-10 absolute top-0 left-0 w-full h-full">
                        <div v-if="showCanvas"
                            @mousedown="startDrawing"
                            @mousemove="draw"
                            @mouseup="stopDrawing"
                            class="absolute top-0 left-0 bg-transparent w-full h-full"
                        ></div>
                        <textarea ref="note" name='note'
                            class="scrollbar border-0 bg-transparent resize-none outline-none text-xl font-extrabold text-neutral-600 remove-shadow w-full h-full"
                            v-html="sessionSelectedNote.content"
                            placeholder="Write something..."
                            @keydown.tab=""
                        ></textarea>
                    </div>
                    <canvas ref="canvasRef"
                        id="drawingCanvas"
                        class="absolute w-full h-full top-0 left-0"
                    ></canvas>
                </div>
            </form>
            <div 
                :class="{'bg-neutral-300 scale-95': showCanvas }"
                class="hover:cursor-pointer transition-transform absolute right-0 bottom-10 bg-neutral-200 p-3 rounded-xl flex flex-col gap-3 items-center"
            >
                <FABrushIcon @click="toggleDrawingMode" class="transition-transform hover:scale-90 h-5 fill-neutral-600"/>
                <div class="bg-neutral-500 h-[1px] w-2/3"></div>
                <FAEyeSlash @click="toggleCanvasDisplay" :class="{'fill-red-500': !canvasDisplay}" class="transition-transform hover:scale-90 h-5 fill-neutral-600" />
                <FAEraserIcon :class="{'outline outline-offset-2 outline-1 outline-neutral-600 scale-90 rounded-xl': strokeColor === '#f5f5f5' }" @click="setColor('#f5f5f5')" class="transition-transform hover:scale-90 h-5 fill-neutral-600" />
                <FATrashIcon @click="clearCanvas" class="transition-transform hover:scale-90 h-5 fill-neutral-600">f</FATrashIcon>
                <div class="bg-neutral-500 h-[1px] w-2/3"></div>
                <div :class="{'outline outline-offset-2 outline-1 outline-neutral-600 scale-90': strokeColor === '#525252' }" @click="setColor('#525252')" class="transition-all hover:scale-90 h-5 w-5 rounded-full bg-neutral-600"></div>
                <div :class="{'outline outline-offset-2 outline-1 outline-neutral-500 scale-90': strokeColor === '#737373' }" @click="setColor('#737373')" class="transition-all hover:scale-90 h-5 w-5 rounded-full bg-neutral-500"></div>
                <div :class="{'outline outline-offset-2 outline-1 outline-neutral-400 scale-90': strokeColor === '#A3A3A3' }" @click="setColor('#A3A3A3')" class="transition-all hover:scale-90 h-5 w-5 rounded-full bg-neutral-400"></div>
                <div :class="{'outline outline-offset-2 outline-1 outline-red-400 scale-90': strokeColor === '#EF4444' }" @click="setColor('#EF4444')" class="transition-all hover:scale-90 h-5 w-5 rounded-full bg-red-500"></div>
                <div :class="{'outline outline-offset-2 outline-1 outline-emerald-500 scale-90': strokeColor === '#10B981' }" @click="setColor('#10B981')" class="transition-all hover:scale-90 h-5 w-5 rounded-full bg-emerald-500"></div>
                <div class="bg-neutral-500 h-[1px] w-2/3"></div>
                <span @click="increaseSize" class="font-bold text-neutral-600 text-xl select-none">+</span>
                <div class="relative w-full">
                    <div class="absolute rounded-full outline outline-offset-1 outline-1 outline-neutral-600 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" :style="{ width: strokeWidth + 'px', height: strokeWidth + 'px', backgroundColor: strokeColor }" :class="{'bg-transparent': strokeColor === '#f5f5f5'}" ></div>
                </div>
                <span @click="decreaseSize" class="font-bold text-neutral-600 text-xl select-none">-</span>
            </div>
        </div>
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
.scrollbar{
    scrollbar-color: var(--bg-neutral-600);
}
</style>
