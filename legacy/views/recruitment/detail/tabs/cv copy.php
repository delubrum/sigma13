<div class="mb-5">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-user-add-line text-xl"></i>
        <span>CV</span>
    </h2>

    <form id="cvForm" class="space-y-6">

        <div class="border p-4 rounded-md space-y-4">
            <h3 class="font-semibold text-gray-700 mb-2">1. Personal Information</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="name">Name:</label>
                    <input id="name" name="name"
                        value="<?= $id->name ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"name"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="cc">CC:</label>
                    <input type="number" id="cc" name="cc" 
                        value="<?= $id->cc ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"cc"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="email">Email:</label>
                    <input id="email" name="email"
                        value="<?= $id->email ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"email"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="phone">Phone:</label>
                    <input id="phone" name="phone"
                        value="<?= $id->phone ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"phone"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="date_of_birth">Date of Birth:</label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                        value="<?= $id->date_of_birth ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"date_of_birth"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="place_of_birth">Place of Birth:</label>
                    <input type="text" id="place_of_birth" name="place_of_birth"
                        value="<?= $id->place_of_birth ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"place_of_birth"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="address">Address:</label>
                    <input type="text" id="address" name="address"
                        value="<?= $id->address ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"address"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="city">City:</label>
                    <input type="text" id="city" name="city"
                        value="<?= $id->city ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"city"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="marital_status">Marital Status:</label>
                    <input type="text" id="marital_status" name="marital_status"
                        value="<?= $id->marital_status ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"marital_status"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="shirt_size">Shirt Size:</label>
                    <input type="text" id="shirt_size" name="shirt_size"
                        value="<?= $id->shirt_size ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"shirt_size"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="pant_size">Pant Size:</label>
                    <input type="text" id="pant_size" name="pant_size"
                        value="<?= $id->pant_size ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"pant_size"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="shoe_size">Shoe Size:</label>
                    <input type="text" id="shoe_size" name="shoe_size"
                        value="<?= $id->shoe_size ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"shoe_size"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="health_insurance">Health Insurance:</label>
                    <input type="text" id="health_insurance" name="health_insurance"
                        value="<?= $id->health_insurance ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"health_insurance"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="pension_fund">Pension Fund:</label>
                    <input type="text" id="pension_fund" name="pension_fund"
                        value="<?= $id->pension_fund ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"pension_fund"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="severance">Severance:</label>
                    <input type="text" id="severance" name="severance"
                        value="<?= $id->severance ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"severance"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-600" for="how_found">How did your CV reach the company?</label>
                <input type="text" id="how_found" name="how_found"
                    value="<?= $id->how_found ?? '' ?>"
                    hx-post="?c=Recruitment&a=UpdateField"
                    hx-trigger="change delay:500ms"
                    hx-vals='{"id":<?= $id->id ?>,"field":"how_found"}'
                    hx-target="this"
                    hx-indicator="#loading"
                    class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="border p-4 rounded-md space-y-4">
            <h3 class="font-semibold text-gray-700 mb-2">2. Family Information</h3>

            <div>
                <label class="block mb-1 font-medium text-gray-600" for="family_members">List family members you live with:</label>
                <textarea id="family_members" name="family_members" rows="4"
                    hx-post="?c=Recruitment&a=UpdateField"
                    hx-trigger="change delay:500ms"
                    hx-vals='{"id":<?= $id->id ?>,"field":"family_members"}'
                    hx-target="this"
                    hx-indicator="#loading"
                    class="w-full border rounded px-3 py-2"><?= $id->family_members ?? '' ?></textarea>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-600">Do you have any family member linked to the company group?</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-1">
                        <input type="radio" name="family_linked" value="Yes"
                            <?= ($id->family_linked ?? '') === 'Yes' ? 'checked' : '' ?>
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"family_linked","value":"Yes"}'
                            hx-target="this"
                            hx-indicator="#loading">
                        Yes
                    </label>
                    <label class="flex items-center gap-1">
                        <input type="radio" name="family_linked" value="No"
                            <?= ($id->family_linked ?? '') === 'No' ? 'checked' : '' ?>
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"family_linked","value":"No"}'
                            hx-target="this"
                            hx-indicator="#loading">
                        No
                    </label>
                </div>
            </div>
        </div>

        <div class="border p-4 rounded-md space-y-4">
            <h3 class="font-semibold text-gray-700 mb-2">3. Academic Information</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1 text-left">Education Level</th>
                            <th class="border px-2 py-1 text-left">Degree</th>
                            <th class="border px-2 py-1 text-left">Graduation Year</th>
                            <th class="border px-2 py-1 text-left">School / Institution</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr>
                            <td class="border px-2 py-1">High School</td>
                            <td class="border px-2 py-1">
                                <input type="text" name="highschool_degree"
                                    value="<?= $id->highschool_degree ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"highschool_degree"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="highschool_year"
                                    value="<?= $id->highschool_year ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"highschool_year"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="highschool_school"
                                    value="<?= $id->highschool_school ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"highschool_school"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                        </tr>

                        <tr>
                            <td class="border px-2 py-1">Technical / Technological</td>
                            <td class="border px-2 py-1">
                                <input type="text" name="technical_degree"
                                    value="<?= $id->technical_degree ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"technical_degree"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="technical_year"
                                    value="<?= $id->technical_year ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"technical_year"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="technical_school"
                                    value="<?= $id->technical_school ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"technical_school"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                        </tr>

                        <tr>
                            <td class="border px-2 py-1">University</td>
                            <td class="border px-2 py-1">
                                <input type="text" name="university_degree"
                                    value="<?= $id->university_degree ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"university_degree"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="university_year"
                                    value="<?= $id->university_year ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"university_year"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="university_school"
                                    value="<?= $id->university_school ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"university_school"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                        </tr>

                        <tr>
                            <td class="border px-2 py-1">Postgraduate</td>
                            <td class="border px-2 py-1">
                                <input type="text" name="postgraduate_degree"
                                    value="<?= $id->postgraduate_degree ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"postgraduate_degree"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="postgraduate_year"
                                    value="<?= $id->postgraduate_year ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"postgraduate_year"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="postgraduate_school"
                                    value="<?= $id->postgraduate_school ?? '' ?>"
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change delay:500ms"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"postgraduate_school"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="w-full border rounded px-2 py-1">
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="border p-4 rounded-md space-y-4">
            <h3 class="font-semibold text-gray-700 mb-2">4. Work Experience</h3>

            <div class="border rounded-md p-3 space-y-3">
                <h4 class="font-medium text-gray-600 mb-2">Last Company</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="company1">Company Name</label>
                        <input type="text" id="company1" name="company1"
                            value="<?= $id->company1 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"company1"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="position1">Position</label>
                        <input type="text" id="position1" name="position1"
                            value="<?= $id->position1 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"position1"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="salary1">Salary</label>
                    <input type="text" id="salary1" name="salary1"
                        value="<?= $id->salary1 ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"salary1"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="start1">Start Date</label>
                        <input type="date" id="start1" name="start1"
                            value="<?= $id->start1 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"start1"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="end1">End Date</label>
                        <input type="date" id="end1" name="end1"
                            value="<?= $id->end1 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"end1"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="duties1">Duties</label>
                    <textarea id="duties1" name="duties1" rows="3"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"duties1"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2"><?= $id->duties1 ?? '' ?></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="manager1">Immediate Manager - Position</label>
                        <input type="text" id="manager1" name="manager1"
                            value="<?= $id->manager1 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"manager1"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="manager_phone1">Manager Phone</label>
                        <input type="tel" id="manager_phone1" name="manager_phone1"
                            value="<?= $id->manager_phone1 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"manager_phone1"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>

            <div class="border rounded-md p-3 space-y-3">
                <h4 class="font-medium text-gray-600 mb-2">Previous Company</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="company2">Company Name</label>
                        <input type="text" id="company2" name="company2"
                            value="<?= $id->company2 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"company2"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="position2">Position</label>
                        <input type="text" id="position2" name="position2"
                            value="<?= $id->position2 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"position2"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="salary2">Salary</label>
                    <input type="text" id="salary2" name="salary2"
                        value="<?= $id->salary2 ?? '' ?>"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"salary2"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="start2">Start Date</label>
                        <input type="date" id="start2" name="start2"
                            value="<?= $id->start2 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"start2"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="end2">End Date</label>
                        <input type="date" id="end2" name="end2"
                            value="<?= $id->end2 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"end2"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600" for="duties2">Duties</label>
                    <textarea id="duties2" name="duties2" rows="3"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"duties2"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2"><?= $id->duties2 ?? '' ?></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="manager2">Immediate Manager - Position</label>
                        <input type="text" id="manager2" name="manager2"
                            value="<?= $id->manager2 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"manager2"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-600" for="manager_phone2">Manager Phone</label>
                        <input type="tel" id="manager_phone2" name="manager_phone2"
                            value="<?= $id->manager_phone2 ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"manager_phone2"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>

                            <div>
                    <label class="block mb-1 font-medium text-gray-600" for="concept">Concept</label>
                    <textarea id="concept" name="concept" rows="3"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-trigger="change delay:500ms"
                        hx-vals='{"id":<?= $id->id ?>,"field":"concept"}'
                        hx-target="this"
                        hx-indicator="#loading"
                        class="w-full border rounded px-3 py-2"><?= $id->concept ?? '' ?></textarea>
                </div>
        </div>
    </form>
</div>