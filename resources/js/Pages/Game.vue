<template>
  <Head title="Memory Game" />
  <div class="p-4 max-w-7xl mx-auto text-center">
    <h1 class="text-2xl font-bold mb-4">🧠 Memory Game</h1>

    <!-- Messages -->
    <div
      v-if="message"
      class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 bg-white/90 shadow-lg rounded-xl px-6 py-3 mb-4 text-lg font-semibold text-blue-700 border border-blue-200 transition-all"
    >
      {{ message }}
    </div>
    <div class="flex justify-center game-container">
        <!-- Game field -->
        <div class="grid grid-cols-4 gap-2">
            <div
                v-for="(card, index) in cards"
                :key="index"
                class="bg-gray-200 rounded-xl aspect-square flex items-center justify-center text-3xl cursor-pointer select-none hover:bg-gray-300"
                @click="flipCard(index)"
            >
                {{ card.flipped || card.matched ? card.value : '❓' }}
            </div>
        </div>
        <!-- Game status -->
        <div class="ml-4 mt-4">
            <p v-if="isGameWon" class="text-green-600 font-bold text-lg">🏁 You won the game!</p>
            <p v-if="total_clicks">Total clicks: {{ total_clicks }}</p>
            
            <button
            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl"
            @click="startNewGame">
            🔁 Restart Game
            </button>
        </div>
        
        
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted} from 'vue'
import { Head } from '@inertiajs/vue3'

const rawValues = ['🍎', '🍌', '🍓', '🍇', '🍊', '🥝', '🍍', '🍉']
const values = [...rawValues, ...rawValues] // 16 карточек

function shuffle(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1))
    ;[array[i], array[j]] = [array[j], array[i]]
  }
  return array
}

const cards = ref([])
const message = ref('')
const total_clicks = ref(0)
const isGameWon = ref(false)

onMounted(() => {
  startNewGame()
})

function startNewGame() {
  cards.value = shuffle(
    values.map(v => ({
      value: v,
      flipped: false,
      matched: false,
    }))
  )

  total_clicks.value = 0
  message.value = ''
  isGameWon.value = false
  firstCardIndex = null
}

let firstCardIndex = null

function flipCard(index) {
  const card = cards.value[index]
  if (card.flipped || card.matched) return

  card.flipped = true
  total_clicks.value++

  if (firstCardIndex === null) {
    firstCardIndex = index
  } else {
    const firstCard = cards.value[firstCardIndex]
    if (firstCard.value === card.value) {
      firstCard.matched = true
      card.matched = true
      message.value = '✅ Match found!'
      checkWin()
    } else {
      message.value = '❌ Not a match...'
      setTimeout(() => {
        firstCard.flipped = false
        card.flipped = false
        message.value = ''
      }, 800)
    }
    firstCardIndex = null
  }
}

function checkWin() {
  if (cards.value.every(c => c.matched)) {
    isGameWon.value = true
    message.value = '🏁 You won the game!'
    saveGame()
  }
  else{
    setTimeout(() => {
      message.value = ''
    }, 2000)
  }
}

function saveGame() {
  axios.post('/game/save', {
    clicks: total_clicks.value
  }).then(response => {
    console.log('Game saved:', response.data)
  }).catch(error => {
    console.error('Save error:', error)
  })
}

</script>
