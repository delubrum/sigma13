<style>
    /* Lógica de Estrellas con CSS Puro */
    .rating-container:not(:checked) > input {
        position: absolute;
        clip: rect(0,0,0,0);
    }

    .rating-container > label {
        cursor: pointer;
        font-size: 2.25rem;
        color: #d1d5db;
        transition: all 0.2s;
    }

    /* Pintar estrellas seleccionadas y sus hermanas anteriores */
    .rating-container > input:checked ~ label,
    .rating-container:not(:checked) > label:hover,
    .rating-container:not(:checked) > label:hover ~ label {
        color: #facc15;
    }

    .rating-container > input:checked + label {
        transform: scale(1.1);
    }

    /* Mostrar texto descriptivo según el radio seleccionado */
    .rating-desc span { display: none; }
    #star5:checked ~ .rating-desc .desc-5,
    #star4:checked ~ .rating-desc .desc-4,
    #star3:checked ~ .rating-desc .desc-3,
    #star2:checked ~ .rating-desc .desc-2,
    #star1:checked ~ .rating-desc .desc-1 { 
        display: block; 
    }
</style>

<div class="w-[95%] sm:w-[30%] bg-white p-4 rounded-lg shadow-lg relative z-50">

    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-black"
        @click="nestedModal = false; document.getElementById('nestedModal').innerHTML='';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
            <i class="ri-star-smile-fill text-3xl text-yellow-500"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Rate Service</h1>
        <p class="text-sm text-gray-500">Your feedback helps us improve</p>
    </div>

    <form
        id="formRate"
        autocomplete="off"
        hx-post="?c=Maintenance&a=Update" 
        hx-indicator="#loading"
        hx-vals='{"id":<?= $id->id ?>,"field": "rating"}'
        class="space-y-6"
    >
        <input type="hidden" name="id" value="<?= $id->id ?>">

        <div class="bg-gray-50 py-6 rounded-xl border border-dashed border-gray-200">
            <label class="block text-sm font-semibold text-gray-600 mb-4 text-center uppercase tracking-wider">
                How would you rate the service?
            </label>
            
            <div class="rating-container flex flex-row-reverse flex-wrap justify-center gap-1">
                
                <?php for ($i = 5; $i >= 1; $i--) { ?>
                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                    <label for="star<?= $i ?>" class="group">
                        <i class="ri-star-fill"></i>
                    </label>
                <?php } ?>

                <div class="rating-desc w-full text-center mt-4 h-5">
                    <span class="desc-1 text-xs text-red-500 font-bold uppercase">Very poor</span>
                    <span class="desc-2 text-xs text-orange-500 font-bold uppercase">Poor</span>
                    <span class="desc-3 text-xs text-yellow-600 font-bold uppercase">Average</span>
                    <span class="desc-4 text-xs text-blue-500 font-bold uppercase">Good</span>
                    <span class="desc-5 text-xs text-green-500 font-bold uppercase">Excellent!</span>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">
                Notes / Feedback
            </label>
            <textarea
                name="notes"
                rows="3"
                placeholder="Tell us more about your experience..."
                class="w-full p-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-yellow-100 focus:border-yellow-400 focus:outline-none transition-all resize-none"
            ></textarea>
        </div>

        <button
            type="submit"
            class="w-full flex items-center justify-center gap-3 bg-gray-900 text-white hover:bg-black transition-all duration-300 py-4 rounded-xl font-bold text-base shadow-lg hover:shadow-xl active:scale-[0.98]"
        >
            <i class="ri-send-plane-2-fill"></i> Submit Rating
        </button>
    </form>
</div>