<div class="w-[95%] max-h-[98vh] sm:w-[50%] bg-white rounded-lg flex flex-col overflow-hidden shadow-2xl relative z-50">
    <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
        <div class="flex items-center gap-3">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-user-add-line text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900 uppercase tracking-tight">Add Candidate</h1>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Recruitment Process</p>
            </div>
        </div>
        <button id="closeNestedModal" @click="nestedModal = false; document.getElementById('nestedModal').innerHTML = '';" 
                class="ri-close-circle-fill text-3xl text-black hover:text-gray-700 transition-colors">
        </button>
    </div>

    <form autocomplete="off"
          id="formItem"
          x-data="{ candidateType: '', mode: '' }"
          hx-post="?c=Recruitment&a=SaveCandidate"
          hx-encoding="multipart/form-data"
          hx-indicator="#loading"
          class="p-6 bg-white flex flex-col flex-grow overflow-y-auto space-y-6">

        <input type="hidden" name="recruitment_id" value="<?= $id->id ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Type</label>
                <select required name="kind" x-model="candidateType"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    <option value=""></option>
                    <option value="Direct">Direct</option>
                    <option value="Temporary">Temporary</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Full Name</label>
                <input type="text" name="name" required placeholder="Enter full name"
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm">
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* ID Number (CC)</label>
                <input type="number" name="cc" required placeholder="00000000"
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm">
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Phone</label>
                <input type="text" name="phone" required placeholder="e.g. +57 300..."
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm">
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Email Address</label>
                <input type="email" name="email" required placeholder="candidate@email.com"
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm">
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Recruiter In Charge</label>
                <select required name="recruiter_id" 
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    <option value="">Select Recruiter...</option>
                    <?php foreach ($this->model->list('id, username', 'users', " AND active = true AND JSON_CONTAINS(permissions, '\"86\"') ORDER BY username ASC") as $r) { ?>
                        <option value="<?= $r->id ?>"><?= $r->username ?></option>
                    <?php } ?>
                </select>
            </div>

            <template x-if="candidateType === 'Direct'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:col-span-2">

                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Source</label>
                        <select required name="cv_source"
                                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                            <option value=""></option>
                            <option value="LinkedIn">LinkedIn</option>
                            <option value="Indeed">Indeed</option>
                            <option value="Referral">Referral</option>
                            <option value="Computrabajo">Computrabajo</option>
                            <option value="Web Page">Web Page</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Psychometrics</label>
                        <select required name="psychometrics"
                                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                            <option value=""></option>
                            <option value="CISD">CISD</option>
                            <option value="PF">PF</option>
                            <option value="Both">Both</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Interview Mode</label>
                        <select required name="appointment_mode" x-model="mode"
                                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                            <option value=""></option>
                            <option value="Virtual">Virtual (Microsoft Teams)</option>
                            <option value="Presencial">In-Person (On-site)</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Interview Appointment</label>
                        <input type="text" name="appointment" required
                               x-init="$nextTick(() => flatpickr($el, {
                                   enableTime: true,
                                   dateFormat: 'Y-m-d H:i',
                                   time_24hr: true,
                                   minuteIncrement: 5,
                                   minDate: 'today',
                                   static: false,
                                   monthSelectorType: 'static'
                               }))"
                               placeholder="YYYY-MM-DD HH:MM"
                               class="w-full p-4 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    </div>

                    <div x-show="mode === 'Virtual'" class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Microsoft Teams Link</label>
                        <input type="url" name="teams_link" :required="mode === 'Virtual'"
                            placeholder="https://teams.microsoft.com/l/meetup-join/..."
                            class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    </div>

                    <div x-show="mode === 'Presencial'" class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Headquarters</label>
                        <select :required="mode === 'Presencial'" name="appointment_location"
                                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                            <option value=""></option>
                            <option value="ESM1">ES METALS 1 (Ventana al Mundo)</option>
                            <option value="ESM2">ES METALS 2 (ES WINDOWS 4 Gate)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">Additional Instructions (Optional)</label>
                        <textarea name="additional_instructions" rows="2"
                                  placeholder="Example: Bring printed resume, ask for HR..."
                                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm"></textarea>
                    </div>

                </div>
            </template>

        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="submit"
                    class="bg-black text-white px-6 py-2 rounded-lg font-bold uppercase text-xs flex items-center gap-2 hover:bg-gray-800 transition shadow-md active:scale-95">
                <i class="ri-save-3-fill text-lg"></i> Save
            </button>
        </div>
    </form>
</div>