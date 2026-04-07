<div class="mb-5">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-file-line text-xl"></i>
        <span>Contract</span>
    </h2>

    <div class="flex flex-col items-center justify-center py-8 text-center">
        <i class="ri-mail-send-line text-5xl text-blue-600"></i>

        <?php if (empty($id->contract_email)) { ?>
            <h2 class="text-xl font-semibold text-gray-800">Send Contract Email</h2>
            <button 
                hx-post="?c=Recruitment&a=ContractEmail"
                hx-include="[name='candidate_id']"
                hx-trigger="click"
                hx-target="this"
                hx-swap="outerHTML"
                hx-indicator="#loading"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 mt-4 rounded-lg shadow transition"
            >
                <i class="ri-send-plane-fill mr-1"></i> Send Email
            </button>

        <?php } else { ?>
            <h2 class="text-xl font-semibold text-gray-800">Contract Email Sent</h2>
            <p class="text-gray-600 mt-2">
                Sent on <span class="font-medium"><?= htmlspecialchars($id->contract_email) ?></span>
            </p>
            <button 
                hx-post="?c=Recruitment&a=ContractEmail"
                hx-include="[name='candidate_id']"
                hx-trigger="click"
                hx-target="this"
                hx-swap="outerHTML"
                hx-indicator="#loading"
                class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 mt-4 rounded-lg shadow transition"
            >
                <i class="ri-refresh-line mr-1"></i> Resend
            </button>
        <?php } ?>

        <input type="hidden" name="candidate_id" value="<?= htmlspecialchars($id->id) ?>">
    </div>
</div>