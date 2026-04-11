<div class="w-[95%] max-h-[98vh] sm:w-[80%] bg-white rounded-lg flex flex-col overflow-hidden shadow-2xl relative z-50">
    <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
        <div class="flex items-center gap-3">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-user-search-line text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900 uppercase tracking-tight">New Recruitment</h1>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Human Resources</p>
            </div>
        </div>
        <button id="closeNewModal" 
                @click="showModal = false; document.getElementById('myModal').innerHTML = '';" 
                class="ri-close-circle-fill text-3xl text-black hover:text-gray-700 transition-colors">
        </button>
    </div>

    <form id="newForm"
          autocomplete="off"
          enctype="multipart/form-data"
          class="p-6 bg-white flex flex-col flex-grow overflow-y-auto space-y-6"
          hx-post='?c=Recruitment&a=Save'
          hx-swap="none"
          hx-indicator="#loading">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            
            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Job Position</label>
                <select required name="profile_id"
                        class="tomselect w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                        hx-get="?c=Recruitment&a=getJobDetails"
                        hx-trigger="change"
                        hx-target="#job-details"
                        hx-swap="innerHTML">
                    <option value=""></option>
                    <?php foreach ($this->model->list('id,name,division_id', 'job_profiles', 'ORDER BY name ASC') as $r) { ?>
                        <option value="<?= $r->id ?>"><?= $r->name ?> || <?= $this->model->get('name', 'hr_db', "and id = $r->division_id")->name ?></option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Approver</label>
                <select required name="approver" class="tomselect w-full p-2 border border-gray-300 rounded-lg focus:outline-none text-sm">
                    <option value=""></option>
                    <?php foreach ($this->model->list('email,username', 'users', 'and area = 1 ORDER BY username ASC') as $r) { ?>
                        <option value="<?= $r->email ?>"><?= $r->username ?></option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* City</label>
                <select required name="city" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    <option value=""></option>
                    <?php foreach ($this->model->list('*', 'hr_db', " and kind = 'city'") as $r) { ?>
                        <option value="<?= $r->name ?>"><?= $r->name ?></option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Work Mode</label>
                <select required name="work_mode" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    <option value=""></option>
                    <option value="On-site">On-site</option>
                    <option value="Remote">Remote</option>
                    <option value="Hybrid">Hybrid</option>
                </select>
            </div>

            <div id="job-details" class="md:col-span-4 bg-gray-50 rounded-lg p-4 border border-dashed border-gray-300">
                <p class="text-xs text-gray-400 italic flex items-center gap-2">
                    <i class="ri-information-line"></i> Select a position to view the experience and education requirements.
                </p>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Quantity</label>
                <input type="number" min="1" step="1" name="qty" required 
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm">
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Contract</label>
                <select required name="contract" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    <option value=""></option>
                    <?php foreach ($this->model->list('*', 'hr_db', " and kind = 'contract'") as $r) { ?>
                        <option value="<?= $r->name ?>"><?= $r->name ?></option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Salary Range</label>
                <input required name="srange" placeholder="e.g. 2M - 3M"
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm">
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Start Date</label>
                <input type="date" onfocus="this.showPicker()" name="start_date" 
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm">
            </div>

            <div x-data="{ cause: '' }" class="md:col-span-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Cause</label>
                    <select x-model="cause" required name="cause"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                        <option value=""></option>
                        <?php foreach ($this->model->list('*', 'hr_db', " AND kind = 'cause'") as $r) { ?>
                            <option value="<?= $r->name ?>"><?= $r->name ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div x-show="cause == 'Remplazo' || cause == 'Remplazo por Maternidad / Incapacidad'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95">
                    <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Replaces</label>
                    <input name="replaces"
                           placeholder="Employee Name"
                           x-bind:required="cause == 'Remplazo' || cause == 'Remplazo por Maternidad / Incapacidad'"
                           class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm">
                </div>
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">Detailed Description</label>
            <textarea required name="others" rows="3" 
                      placeholder="Add specific details..."
                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm"></textarea>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="submit" class="bg-black text-white px-6 py-2 rounded-lg font-bold uppercase text-xs flex items-center gap-2 hover:bg-gray-800 transition shadow-md">
                <i class="ri-save-3-fill text-lg"></i> Save Recruitment
            </button>
        </div>
    </form>
</div>

<script>
// El script de inicialización se mantiene igual, ahora debería detectar los elementos correctamente
document.querySelectorAll('.tomselect').forEach(el => {
  if (el.tomselect) { el.tomselect.destroy(); }
  new TomSelect(el, {
    openOnFocus: true,
    maxOptions: null,
    render: {
        option: (data, escape) => `<div class="py-2 px-3 text-sm">${escape(data.text)}</div>`,
        item: (data, escape) => `<div class="text-sm">${escape(data.text)}</div>`
    }
  });
});
</script>