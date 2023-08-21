
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

const props = defineProps(['auth', 'allNotes', 'selectedNote', 'userKomote', 'canvasImg', 'test']);

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
    canvas: null,
    previousCanvas: null
})

try {
    sessionSelectedNote = useForm({
        id: props.selectedNote.id,
        content: props.selectedNote.content,
        name: props.selectedNote.name,
        created_at: props.selectedNote.created_at,
        canvas: props.selectedNote.canvas,
        previousCanvas: props.canvasImg
    })
    console.log(props.test)
} catch (e){

}
const createdAtUTC = sessionSelectedNote.created_at
let createdAt = ''
try {
    createdAt = (createdAtUTC.split('T')[0]).replace(/-/g, '/')
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
let pngDataUrl = sessionSelectedNote.previousCanvas
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
    console.log(sessionSelectedNote)
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
    try {
        resizeTextarea()
    } catch (error) {
        
    }

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

const newNote = () => {
    newNoteForm.post(route('notes.store'))
}
// truly a very bad solution. other solutions worked for me but never in production
const firstNewNote = () => {
    newNoteForm.post(route('notes.store'))
    location.reload()
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
            <a href="/logout" class="font-bold text-neutral-600">logout</a>
            <div class="bg-neutral-500 h-[1px]"></div>
            <ul class="flex flex-col-reverse gap-3 pl-5 [&>*]:font-bold  [&>*]:text-neutral-600">
                <Link
                    :href="`/notes/${note.id}`"
                    v-for="note in allNotes"
                    :class="{'underline': sessionSelectedNote.id === note.id }"
                    >
                    {{ note.name }}</Link>
            </ul>
            <span @click="showHelp = !showHelp" class="font-bold text-neutral-700 mt-auto underline underline-offset-3 text-sm mb-0 select-none">how do i use kommotion?</span>
            <div v-if="showHelp" class="p-10 font-medium-neutral-600 absolute z-30 min-h-2/3 bg-neutral-300 rounded-xl top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-col gap-1 text-sm">
                <!-- <span class="p-1 text-lg font-extrabold text-red-500 ml-auto select-none hover:cursor-pointer border border-red-400 border-2 leading-none" @click="showHelp = !showHelp">X</span> -->
                <h3 class="underline font-bold">Making a note:</h3>
                    <p>Type your note name in the bottom left of the screen and click the + to the right of the name input. <span class="underline">If the first note you make on a new account doesn't appear, please refresh the page.</span></p>
                <h3 class="underline font-bold">Editing & saving a note:</h3>
                    <p>Both the title and note can be edited by clicking on their respective fields. The user can toggle drawing mode by clicking on the "drawing on/off" text at the top of the toolbar. The eye-slash icon toggles the canvas' visibility, but makes no change to its content. The trash icon clears the canvas.<br>A note's title content are all saved with click of the gray save button. At the time being there is <span class="underline">no autosave for notes.</span></p>
                <h3 class="underline font-bold">Editing & "saving" a Komote:</h3>
                    <p>The komote is edited by simply clicking and typing. Unlike notes, they are entirely auto-saved. The komote can be dragged anywhere in the window and the content is the same in all notes.</p>
                <h3 class="underline font-bold mt-auto">Upcoming Features:</h3>
                    <ul class="ml-5">
                        <li>-Deleting notes</li>
                        <li>-A better logo</li>
                        <li>-Better auto-save for komote that doesn't hit the rate limit</li>
                        <li>-More efficient storage of the canvas</li>
                        <li>-Lists, bold text, underlines, and italics within notes</li>
                    </ul>
                <h3 class="underline font-bold mt-auto">Contact the dev:</h3>
                    <a href="mailto:jamesdoconnor2@gmail.com">jamesdoconnor2@gmail.com</a>
                    <a href="https://github.com/jamesoconnr">github.com/jamesoconnr</a>
            </div>
            <form v-if="sessionSelectedNote.id" class="flex gap-4" @submit.prevent="newNote">
                <input type="text" v-model="newNoteForm.name" class=" w-full">
                <button class="text-3xl select-none" >+</button>
            </form>
            <form v-else class="flex gap-4" @submit="firstNewNote">
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
                            class="scrollbar border-1 border-neutral-200 rounded-xl bg-transparent resize-none outline-none text-xl font-bold text-neutral-600 remove-shadow w-full h-full p-5"
                            v-model="sessionSelectedNote.content"
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
                class="hover:cursor-pointer transition-transform absolute right-0 bottom-10 bg-neutral-200 p-3 rounded-xl flex flex-col gap-3 items-center z-20"
            >
                <div  @click="toggleDrawingMode" class="font-bold text-neutral-700 flex flex-col items-center text-xs">
                    <span>Drawing</span>
                    <span class="text-red-500" v-if="showCanvas">On</span>
                    <span class="" v-else>Off</span>
                </div>
                <div class="bg-neutral-500 h-[1px] w-2/3"></div>
                <FABrushIcon @click="setColor('#A3A3A3')" class="{transition-transform hover:scale-90 h-5 fill-neutral-600" :class="{'outline outline-offset-2 outline-2 outline-neutral-600 scale-90 rounded-xl': strokeColor != '#f5f5f5' }"/>
                <FAEraserIcon :class="{'outline outline-offset-2 outline-2 outline-neutral-600 scale-90 rounded-xl': strokeColor === '#f5f5f5' }" @click="setColor('#f5f5f5')" class="transition-transform hover:scale-90 h-5 fill-neutral-600" />
                <FAEyeSlash @click="toggleCanvasDisplay" :class="{'fill-red-500': !canvasDisplay}" class="transition-transform hover:scale-90 h-5 fill-neutral-600" />
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
        <div v-else class="h-full p-5 flex flex-col font-bold text-5xl text-neutral-600">
            <h2 class="mt-auto text-xl">Please refresh the page if the first note doesn't appear!</h2>
            <h1 class="">&lt;-Make your first note!</h1>
        </div>
    </main>
</template>
<style>
body{
    overflow: hidden;
}
.remove-shadow:focus{
    box-shadow: none !important;
    border-color: inherit;
}
.scrollbar{
    scrollbar-color: var(--bg-neutral-600);
}
</style>
